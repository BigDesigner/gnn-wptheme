<?php
/**
 * Explicit package download, upload and confirmation controls.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Check both configuration and authored-content permissions. */
function gnn_package_permission() {
	return current_user_can( 'manage_options' ) && current_user_can( 'unfiltered_html' ) && current_user_can( 'upload_files' ) && ! is_multisite();
}

/** Explicit download; no persistent public archive is created. */
function gnn_package_download() {
	check_admin_referer( 'gnn_package_export' );
	if ( ! gnn_package_permission() ) {
		wp_die( esc_html__( 'Permission denied.', 'gnn' ), '', array( 'response' => 403 ) );
	}
	$file = gnn_package_export();
	if ( is_wp_error( $file ) ) {
		wp_die( esc_html( $file->get_error_message() ) );
	}
	nocache_headers();
	header( 'Content-Type: application/zip' );
	header( 'Content-Disposition: attachment; filename="gnn-demo-' . gmdate( 'Y-m-d' ) . '.zip"' );
	header( 'Content-Length: ' . filesize( $file ) );
	$stream = fopen( $file, 'rb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- streamed private temporary download.
	if ( $stream ) {
		fpassthru( $stream ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- verified binary ZIP download, not HTML.
		fclose( $stream ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- close streamed download.
	}
	wp_delete_file( $file );
	exit;
}
add_action( 'admin_post_gnn_package_export', 'gnn_package_download' );

/** Validate the uploaded archive and redirect to its one deletion confirmation. */
function gnn_package_upload() {
	check_admin_referer( 'gnn_package_upload' );
	if ( ! gnn_package_permission() || get_option( 'gnn_wipe_job' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'gnn' ), '', array( 'response' => 403 ) );
	}
	if ( ! isset( $_FILES['package']['tmp_name'], $_FILES['package']['error'] ) || ! is_string( $_FILES['package']['tmp_name'] ) || UPLOAD_ERR_OK !== $_FILES['package']['error'] || ! is_uploaded_file( $_FILES['package']['tmp_name'] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- verified PHP upload temporary path, never a user destination.
		wp_die( esc_html__( 'The file could not be uploaded. Check the upload limit and retry.', 'gnn' ) );
	}
	$source = $_FILES['package']['tmp_name']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- checked is_uploaded_file immediately above.
	$doc    = gnn_package_validate( $source );
	if ( is_wp_error( $doc ) ) {
		wp_die( esc_html( $doc->get_error_message() ) );
	}
	$file = gnn_package_temp();
	if ( is_wp_error( $file ) || ! move_uploaded_file( $source, $file ) ) {
		if ( is_string( $file ) ) {
			wp_delete_file( $file );
		}
		wp_die( esc_html__( 'The file could not be uploaded. Check the upload limit and retry.', 'gnn' ) );
	}
	chmod( $file, 0600 ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_chmod -- private temporary archive.
	$token = 'package-' . str_replace( '-', '', wp_generate_uuid4() );
	add_option(
		'gnn_' . $token,
		array(
			'owner'   => get_current_user_id(),
			'site'    => home_url( '/' ),
			'file'    => $file,
			'hash'    => hash_file( 'sha256', $file ),
			'title'   => sanitize_text_field( $doc['title'] ),
			'created' => time(),
		),
		'',
		false
	);
	wp_schedule_single_event( time() + DAY_IN_SECONDS, 'gnn_package_expire', array( $token ) );
	wp_safe_redirect( admin_url( 'admin.php?page=gnn-workspace&path=ready&package=' . $token ) );
	exit;
}
add_action( 'admin_post_gnn_package_upload', 'gnn_package_upload' );

/**
 * Remove unclaimed packages; an active installation always retains its archive.
 *
 * @param string $token Staging identifier.
 */
function gnn_package_expire( $token ) {
	if ( ! preg_match( '/^package-[a-f0-9]{32}$/D', $token ) ) {
		return;
	}
	$job  = get_option( 'gnn_wipe_job' );
	$lock = (int) get_option( 'gnn_wipe_batch_lock' );
	if ( ( is_array( $job ) && ( $job['profile'] ?? '' ) === $token ) || $lock > time() - 120 ) {
		wp_schedule_single_event( time() + DAY_IN_SECONDS, 'gnn_package_expire', array( $token ) );
		return;
	}
	$stage = get_option( 'gnn_' . $token );
	if ( is_array( $stage ) ) {
		wp_delete_file( $stage['file'] );
		delete_option( 'gnn_' . $token );
	}
}
add_action( 'gnn_package_expire', 'gnn_package_expire' );

/** A small, independent upload area; uploaded demos do not join the catalog. */
function gnn_package_upload_screen() {
	if ( ! gnn_package_permission() ) {
		return;
	}
	$token = isset( $_GET['package'] ) && is_string( $_GET['package'] ) ? sanitize_key( wp_unslash( $_GET['package'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only owned package selection.
	$stage = preg_match( '/^package-[a-f0-9]{32}$/D', $token ) ? get_option( 'gnn_' . $token ) : false;
	echo '<section class="gnn-settings-card gnn-card-content"><h2>' . esc_html__( 'Load demo package', 'gnn' ) . '</h2>';
	if ( ! class_exists( 'ZipArchive' ) ) {
		echo '<p>' . esc_html__( 'Enable the PHP ZIP extension to use demo packages.', 'gnn' ) . '</p></section>';
		return;
	}
	if ( is_array( $stage ) && get_current_user_id() === $stage['owner'] && hash_equals( $stage['site'], home_url( '/' ) ) ) {
		echo '<p>' . esc_html( $stage['title'] ) . '</p><button type="button" class="button button-primary gnn-demo-open" data-profile="' . esc_attr( $token ) . '" data-label="' . esc_attr( $stage['title'] ) . '">' . esc_html__( 'Install demo site', 'gnn' ) . '</button>';
	} else {
		echo '<form action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" method="post" enctype="multipart/form-data"><input type="hidden" name="action" value="gnn_package_upload">';
		wp_nonce_field( 'gnn_package_upload' );
		echo '<p><label>' . esc_html__( 'Demo package', 'gnn' ) . ' <input type="file" name="package" accept=".zip,application/zip" required></label></p>';
		/* translators: %s: maximum upload size. */
		echo '<p>' . esc_html( sprintf( __( 'ZIP · up to %s', 'gnn' ), size_format( min( wp_max_upload_size(), 256 * MB_IN_BYTES ) ) ) ) . '</p>';
		submit_button( __( 'Upload', 'gnn' ), 'secondary' );
		echo '</form>';
	}
	echo '</section>';
}

/** Export action alongside configuration maintenance. */
function gnn_package_export_screen() {
	if ( ! gnn_package_permission() ) {
		return;
	}
	if ( ! class_exists( 'ZipArchive' ) ) {
		echo '<section class="gnn-settings-card gnn-card-content"><h2>' . esc_html__( 'Export demo package', 'gnn' ) . '</h2><p>' . esc_html__( 'Enable the PHP ZIP extension to use demo packages.', 'gnn' ) . '</p></section>';
		return;
	}
	echo '<section class="gnn-settings-card gnn-card-content"><h2>' . esc_html__( 'Export demo package', 'gnn' ) . '</h2><p>' . esc_html__( 'Includes site content, media, menus and appearance. Accounts, comments, orders and integration keys are excluded.', 'gnn' ) . '</p><form action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" method="post"><input type="hidden" name="action" value="gnn_package_export">';
	wp_nonce_field( 'gnn_package_export' );
	submit_button( __( 'Download demo package', 'gnn' ), 'secondary' );
	echo '</form></section>';
}
