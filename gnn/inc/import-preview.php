<?php
/**
 * Bounded, user-scoped import preview and explicit target-reference selection.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

/**
 * Preview never mutates theme configuration.
 *
 * @param string $raw Untrusted JSON input.
 */
function gnn_render_import_preview( $raw ) {
	$result = gnn_decode_settings_import( $raw );
	if ( is_wp_error( $result ) && 'gnn_reference' !== $result->get_error_code() ) {
		wp_die(
			esc_html( $result->get_error_message() ),
			'',
			array(
				'response'  => 400,
				'back_link' => true,
			)
		); }
	$document = json_decode( $raw );
	$token    = wp_generate_uuid4();
	set_transient(
		'gnn_preview_' . $token,
		array(
			'user'     => get_current_user_id(),
			'raw'      => $raw,
			'revision' => gnn_configuration_revision(),
		),
		10 * MINUTE_IN_SECONDS
	);
	nocache_headers();
	echo '<!doctype html><html ';
	language_attributes();
	echo '><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>' . esc_html__( 'Review settings import', 'gnn' ) . '</title></head><body><main><h1>' . esc_html__( 'Review settings import', 'gnn' ) . '</h1><p>' . esc_html__( 'No settings have been changed. Review the affected keys and select references belonging to this site. Private values are not displayed.', 'gnn' ) . '</p><ul>';
	foreach ( array( 'options', 'theme_mods' ) as $group ) {
		foreach ( (array) ( $document->$group ?? array() ) as $key => $value ) {
			echo '<li>' . esc_html( $group . ': ' . $key ) . '</li>'; }
	}
	echo '</ul><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="gnn_confirm_import"><input type="hidden" name="token" value="' . esc_attr( $token ) . '">';
	wp_nonce_field( 'gnn_confirm_import' );
	$same_site     = isset( $document->source_site ) && is_string( $document->source_site ) && hash_equals( gnn_site_fingerprint(), $document->source_site );
	$source_values = array_replace( (array) ( $document->theme_mods ?? array() ), (array) ( $document->options ?? array() ) );
	foreach ( array_intersect_key( $source_values, array_flip( gnn_site_specific_keys() ) ) as $key => $id ) {
		$is_layout = false !== strpos( $key, '_template' ) || false !== strpos( $key, '_grid_layout' );
		$is_media  = false !== strpos( $key, 'logo_' ) || in_array( $key, array( 'error404_image', 'local_font' ), true );
		if ( $same_site || ( is_numeric( $id ) && (int) $id <= 0 ) || ( ! $is_layout && ! $is_media && ! in_array( $key, array( 'contact_page_id', 'hero_group' ), true ) ) ) {
			continue; }
		echo '<p><label for="map-' . esc_attr( $key ) . '">' . esc_html( $key ) . '</label><select id="map-' . esc_attr( $key ) . '" name="reference_map[' . esc_attr( $key ) . ']" required><option value="">' . esc_html__( 'Choose a reference on this site', 'gnn' ) . '</option>';
		if ( 'hero_group' === $key ) {
			foreach ( get_terms(
				array(
					'taxonomy'   => 'gnn_slide_group',
					'hide_empty' => false,
				)
			) as $term ) {
				if ( $term instanceof WP_Term ) {
					echo '<option value="' . esc_attr( $term->term_id ) . '">' . esc_html( $term->name ) . '</option>'; }
			}
		} else {
			foreach ( get_posts(
				array(
					'post_type'   => $is_media ? 'attachment' : ( $is_layout ? 'gnn_layout' : 'page' ),
					'post_status' => $is_media ? 'inherit' : 'publish',
					'numberposts' => 100,
					'orderby'     => 'title',
					'order'       => 'ASC',
				)
			) as $post ) {
				echo '<option value="' . esc_attr( $post->ID ) . '">' . esc_html( $post->post_title . ' (#' . $post->ID . ')' ) . '</option>'; }
		}
		echo '</select></p>';
	}
	echo '<p><label><input type="checkbox" name="confirmed" value="1" required> ' . esc_html__( 'Apply the reviewed changes and create a recovery snapshot.', 'gnn' ) . '</label></p><button type="submit">' . esc_html__( 'Confirm import', 'gnn' ) . '</button></form></main></body></html>';
	exit;
}

/** A preview can only be confirmed by its original authorized user. */
function gnn_confirm_import() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'gnn' ), '', array( 'response' => 403 ) ); }
	check_admin_referer( 'gnn_confirm_import' );
	$token = isset( $_POST['token'] ) && is_string( $_POST['token'] ) ? sanitize_text_field( wp_unslash( $_POST['token'] ) ) : '';
	$item  = get_transient( 'gnn_preview_' . $token );
	if ( ! is_array( $item ) || get_current_user_id() !== (int) $item['user'] || empty( $_POST['confirmed'] ) ) {
		wp_die( esc_html__( 'This preview expired or was not confirmed. Preview the file again.', 'gnn' ), '', array( 'response' => 400 ) ); }
	$document                = json_decode( $item['raw'] );
	$map                     = isset( $_POST['reference_map'] ) && is_array( $_POST['reference_map'] ) ? wp_unslash( $_POST['reference_map'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- typed allowlist below.
	$document->reference_map = new stdClass();
	foreach ( $map as $key => $value ) {
		if ( ! in_array( $key, gnn_site_specific_keys(), true ) || ! is_string( $value ) || ! ctype_digit( $value ) ) {
			wp_die( esc_html__( 'The reference mapping is invalid.', 'gnn' ), '', array( 'response' => 400 ) ); }
		$document->reference_map->$key = absint( $value );
	}
	if ( ! hash_equals( $item['revision'], gnn_configuration_revision() ) ) {
		wp_die( esc_html__( 'Settings changed after the preview. Preview the file again.', 'gnn' ), '', array( 'response' => 409 ) ); }
	$result = gnn_import_panel_settings( wp_json_encode( $document ), $item['revision'] );
	if ( is_wp_error( $result ) ) {
		wp_die(
			esc_html( $result->get_error_message() ),
			'',
			array(
				'response'  => 400,
				'back_link' => true,
			)
		); }
	delete_transient( 'gnn_preview_' . $token );
	gnn_panel_redirect( 'import_ok' );
}
add_action( 'admin_post_gnn_confirm_import', 'gnn_confirm_import' );
