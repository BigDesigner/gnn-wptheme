<?php
/**
 * Explicit design reset and reviewable cleanup of unchanged imported drafts.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Presentation settings only: identity, customer code, integrations and operational switches survive.
 *
 * @return string[]
 */
function gnn_design_reset_keys() {
	$preserve = array( 'header_cta_label', 'header_cta_url', 'topbar_text', 'topbar_email', 'topbar_phone', 'footer_tagline', 'footer_copyright', 'footer_logo_light', 'footer_logo_dark', 'contact_page_id' );
	$keys     = array();
	foreach ( gnn_option_defaults() as $key => $default ) {
		if ( ! in_array( $key, $preserve, true ) && preg_match( '/^(show_toggle$|remember_mode$|smooth_scroll$|scroll_top$|scroll_anim$|preloader$|loading_screen$|portfolio_|gallery_|module_gap$|project_detail_width$|color_|accent|design_|button_|btn_style_|typo_|typography_|header_|footer_|topbar_|page_|post_|blog_|shop_|slider_|hero_|body_(font|size|line_height)$|heading_(font|size|mobile_size|line_height|tracking)$|container_width$|boxed_width$|full_width$|sidebar_|content_.*padding$|title_overlay_|sticky_header$|show_search$|show_cart$|logo_max_height)/', $key ) ) {
			$keys[] = $key;
		}
	}
	return $keys;
}

/**
 * Compare the entire imported draft and metadata, not merely its title.
 * Unknown legacy baselines are intentionally not adopted as safe to delete.
 *
 * @param int $id Draft ID.
 */
function gnn_starter_fingerprint( $id ) {
	$post = get_post( $id );
	if ( ! $post ) {
		return '';
	}
	$meta = get_post_meta( $id );
	foreach ( array_keys( $meta ) as $key ) {
		if ( in_array( $key, array( '_gnn_starter_baseline', '_edit_lock', '_edit_last', '_elementor_css', '_elementor_element_cache', '_elementor_page_assets', '_gnn_starter_pending' ), true ) ) {
			unset( $meta[ $key ] );
		}
	}
	ksort( $meta );
	return hash( 'sha256', wp_json_encode( array( $post->post_title, $post->post_content, $post->post_excerpt, $post->post_status, $post->post_name, $post->post_parent, $post->menu_order, $post->post_password, $meta ) ) );
}

/**
 * Content assigned to live site navigation or as a parent is never a cleanup candidate.
 *
 * @param int $id Page ID.
 */
function gnn_starter_is_referenced( $id ) {
	if ( in_array( (int) $id, array( (int) get_option( 'page_on_front' ), (int) get_option( 'page_for_posts' ), (int) gnn_option( 'contact_page_id' ) ), true ) ) {
		return true;
	}
	if ( get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'private', 'draft', 'pending', 'future' ),
			'post_parent'    => $id,
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	) ) {
		return true;
	}
	$types      = array_diff( array_unique( array_merge( get_post_types( array( 'public' => true ) ), array( 'gnn_layout', 'elementor_library' ) ) ), array( 'attachment' ) );
	$references = new WP_Query(
		array(
			'post_type'      => $types,
			'post_status'    => array( 'publish', 'private', 'draft', 'pending', 'future' ),
			'posts_per_page' => 100,
			'post__not_in'   => array( $id ),
		)
	);
	if ( $references->found_posts > 100 ) {
		// Large existing content inventories require manual review rather than partial proof of safety.
		return true;
	}
	$permalink = get_permalink( $id );
	foreach ( $references->posts as $record ) {
		$baseline = get_post_meta( $record->ID, '_gnn_starter_baseline', true );
		if ( 'draft' === $record->post_status && $baseline && hash_equals( $baseline, gnn_starter_fingerprint( $record->ID ) ) ) {
			continue;
		}
		$text = $record->post_content . (string) get_post_meta( $record->ID, '_elementor_data', true );
		$text = str_replace( '\\/', '/', html_entity_decode( $text, ENT_QUOTES, 'UTF-8' ) );
		if ( preg_match( '/[?&](?:page_id|p)=' . absint( $id ) . '(?![0-9])/', $text ) || ( $permalink && false !== strpos( $text, $permalink ) ) ) {
			return true;
		}
	}
	$menus = wp_get_nav_menus();
	foreach ( $menus as $menu ) {
		foreach ( (array) wp_get_nav_menu_items( $menu->term_id ) as $item ) {
			if ( (int) $item->object_id === (int) $id && 'post_type' === $item->type ) {
				return true;
			}
		}
	}
	return false;
}

/** A bounded inventory; changing/reviewing it never performs a write. */
function gnn_starter_cleanup_inventory() {
	$posts = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'draft', 'publish', 'private', 'pending', 'future', 'trash' ),
			// phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page -- bounded administrator review only.
			'posts_per_page' => 200,
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- explicit administrator inventory.
			'meta_key'       => '_gnn_starter',
		)
	); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- explicit admin review; bounded.
	$rows = array();
	foreach ( $posts as $post ) {
		$baseline = get_post_meta( $post->ID, '_gnn_starter_baseline', true );
		$safe     = 'draft' === $post->post_status && $baseline && hash_equals( $baseline, gnn_starter_fingerprint( $post->ID ) ) && ! gnn_starter_is_referenced( $post->ID );
		$rows[]   = array(
			'id'          => $post->ID,
			'title'       => $post->post_title,
			'profile'     => get_post_meta( $post->ID, '_gnn_starter', true ),
			'safe'        => (bool) $safe,
			'fingerprint' => gnn_starter_fingerprint( $post->ID ),
		);
	}
	return $rows;
}

/**
 * Reviewed, unchanged draft pages only. Never deletes central projects, albums or media.
 *
 * @param array $selection Map of reviewed ID => fingerprint.
 * @param bool  $permanent Explicit irreversible deletion request.
 * @return true|WP_Error
 */
function gnn_starter_cleanup( $selection, $permanent = false ) {
	if ( ! current_user_can( 'manage_options' ) || ! is_array( $selection ) || count( $selection ) > 200 || ! $selection ) {
		return new WP_Error( 'gnn_cleanup', __( 'Select reviewed, unchanged demo drafts first.', 'gnn' ) );
	}
	$inventory = array_column( gnn_starter_cleanup_inventory(), null, 'id' );
	foreach ( $selection as $id => $fingerprint ) {
		if ( ! isset( $inventory[ $id ] ) || ! $inventory[ $id ]['safe'] || ! is_string( $fingerprint ) || ! hash_equals( $inventory[ $id ]['fingerprint'], $fingerprint ) || ! current_user_can( 'delete_post', $id ) ) {
			return new WP_Error( 'gnn_cleanup_changed', __( 'The cleanup selection changed or contains protected content. Review the latest list.', 'gnn' ) );
		}
	}
	$started = gnn_configuration_begin( 'starter-cleanup', gnn_configuration_form_revision() );
	if ( is_wp_error( $started ) ) {
		return $started;
	}
	foreach ( $selection as $id => $fingerprint ) {
		if ( ! hash_equals( gnn_starter_fingerprint( $id ), $fingerprint ) || gnn_starter_is_referenced( $id ) ) {
			return new WP_Error( 'gnn_cleanup_changed', __( 'The cleanup selection changed or contains protected content. Review the latest list.', 'gnn' ) );
		}
		$result = $permanent ? wp_delete_post( $id, true ) : wp_trash_post( $id );
		if ( ! $result ) {
			return new WP_Error( 'gnn_cleanup_failed', __( 'A draft could not be removed. Review the remaining list before retrying.', 'gnn' ) );
		}
	}
	return true;
}

/** Review first, then explicit destructive confirmation. Settings reset is a separate control. */
function gnn_render_starter_maintenance() {
	echo '<section class="gnn-settings-card"><h2>' . esc_html__( 'Design replacement and cleanup', 'gnn' ) . '</h2><div class="gnn-workspace-section"><p>' . esc_html__( 'Reset presentation settings when applying a new profile. Identity, integrations, customer code and central content are preserved. A configuration snapshot is created first.', 'gnn' ) . '</p><p>' . esc_html__( 'Cleanup only offers unchanged imported draft pages. Published, edited, referenced and older unverified records are protected. Projects, albums, slides and media are never deleted here. Remove child drafts first to unlock an unchanged parent on the next review.', 'gnn' ) . '</p><details><summary>' . esc_html__( 'Review imported demo drafts', 'gnn' ) . '</summary><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="gnn_workspace"><input type="hidden" name="operation" value="starter_cleanup">';
	wp_nonce_field( 'gnn_workspace' );
	gnn_configuration_revision_field();
	echo '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'Select', 'gnn' ) . '</th><th>' . esc_html__( 'Page', 'gnn' ) . '</th><th>' . esc_html__( 'Status', 'gnn' ) . '</th></tr></thead><tbody>';
	foreach ( gnn_starter_cleanup_inventory() as $row ) {
		echo '<tr><td>';
		if ( $row['safe'] ) {
			echo '<input type="checkbox" name="cleanup[' . absint( $row['id'] ) . ']" value="' . esc_attr( $row['fingerprint'] ) . '" aria-label="' . esc_attr( $row['title'] ) . '">';
		}
		echo '</td><td>' . esc_html( $row['title'] . ' / ' . $row['profile'] ) . '</td><td>' . esc_html( $row['safe'] ? __( 'Unchanged demo draft', 'gnn' ) : __( 'Protected: edited, referenced, published or unverified', 'gnn' ) ) . '</td></tr>';
	}
	echo '</tbody></table><p><label><input type="checkbox" name="permanent" value="1"> ' . esc_html__( 'Permanently delete selected demo drafts and their metadata instead of moving to Trash. This cannot be undone; a settings snapshot does not back up pages.', 'gnn' ) . '</label></p><p><label><input type="checkbox" name="confirmed" value="1" required> ' . esc_html__( 'I reviewed the selected records and deletion method.', 'gnn' ) . '</label></p>';
	submit_button( __( 'Clean selected demo drafts', 'gnn' ), 'secondary' );
	echo '</form></details><p><a href="' . esc_url( admin_url( 'admin.php?page=gnn-workspace#gnn-workspace-operations' ) ) . '">' . esc_html__( 'Visitor maintenance mode is controlled separately under Maintenance and compatibility.', 'gnn' ) . '</a></p></div></section>';
}
