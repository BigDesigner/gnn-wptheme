<?php
/**
 * Non-destructive lifecycle.
 *
 * @package GNN_Content
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; }
/** Mark pending rewrite. */
function gnn_content_activate_site() {
	update_option( 'gnn_content_rewrite_pending', 1, false ); }
/**
 * Network-aware activation.
 *
 * @param bool $network Network.
 */
function gnn_content_activate( $network = false ) {
	if ( is_multisite() && $network ) {
		foreach ( get_sites(
			array(
				'fields' => 'ids',
				'number' => 0,
			)
		) as $site_id ) {
			switch_to_blog( $site_id );
			gnn_content_activate_site();
			restore_current_blog(); }
		return;
	}
	gnn_content_activate_site();
}
register_activation_hook( GNN_CONTENT_PATH . 'gnn-content.php', 'gnn_content_activate' );
/** One flush after registrations or theme changes. */
function gnn_content_rewrite() {
	$schema = GNN_CONTENT_API_VERSION . ':' . get_template();
	if ( get_option( 'gnn_content_api_schema' ) !== $schema || get_option( 'gnn_content_rewrite_pending' ) ) {
		flush_rewrite_rules( false );
		update_option( 'gnn_content_api_schema', $schema, false );
		delete_option( 'gnn_content_rewrite_pending' );
	}
}
add_action( 'init', 'gnn_content_rewrite', 99 );
/** Remove definitions only; preserve content and roles. */
function gnn_content_deactivate_site() {
	if ( 'gnn' !== get_template() ) {
		foreach ( array( 'gnn_project_category', 'gnn_product_category', 'gnn_slide_group' ) as $taxonomy ) {
			if ( taxonomy_exists( $taxonomy ) ) {
					unregister_taxonomy( $taxonomy ); }
		}
		foreach ( array( 'gnn_project', 'gnn_product', 'gnn_album', 'gnn_slide', 'gnn_layout' ) as $type ) {
			if ( post_type_exists( $type ) ) {
					unregister_post_type( $type ); }
		}
	}
	flush_rewrite_rules( false );
	gnn_content_activate_site();
}
/**
 * Network-aware deactivation.
 *
 * @param bool $network Network.
 */
function gnn_content_deactivate( $network = false ) {
	if ( is_multisite() && $network ) {
		foreach ( get_sites(
			array(
				'fields' => 'ids',
				'number' => 0,
			)
		) as $site_id ) {
			switch_to_blog( $site_id );
			gnn_content_deactivate_site();
			restore_current_blog(); }
		return;
	}
	gnn_content_deactivate_site();
}
register_deactivation_hook( GNN_CONTENT_PATH . 'gnn-content.php', 'gnn_content_deactivate' );

/** Mark newly initialized network sites only when this plugin is network-active.
 *
 * @param WP_Site $site New site.
 */
function gnn_content_initialize_site( $site ) {
	$active = get_site_option( 'active_sitewide_plugins', array() );
	if ( ! isset( $active['gnn-content/gnn-content.php'] ) ) {
		return; }
	switch_to_blog( $site->blog_id );
	gnn_content_activate_site();
	restore_current_blog();
}
add_action( 'wp_initialize_site', 'gnn_content_initialize_site', 200 );
