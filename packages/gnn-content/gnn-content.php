<?php
/**
 * Plugin Name: GNN Content
 * Description: Portfolio, collections, slides and reusable content.
 * Version: 2.15.1
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License: GPL-2.0-or-later
 * Text Domain: gnn-content
 * Domain Path: /languages
 *
 * @package GNN_Content
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( 'GNN_CONTENT_API_VERSION', '1.0.0' );
define( 'GNN_CONTENT_VERSION', '2.15.1' );
define( 'GNN_CONTENT_PATH', plugin_dir_path( __FILE__ ) );
define( 'GNN_CONTENT_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
require_once GNN_CONTENT_PATH . 'includes/lifecycle.php';
/** Load only after the active theme has declared its API support. */
function gnn_content_bootstrap() {
	if ( 'gnn' === get_template() && ( ! defined( 'GNN_CONTENT_THEME_API_VERSION' ) || version_compare( GNN_CONTENT_THEME_API_VERSION, GNN_CONTENT_API_VERSION, '<' ) ) ) {
		return;
	}
	foreach ( array( 'api', 'modules', 'slides', 'layout-access', 'shared-content', 'shared-library', 'block-roles', 'roles', 'tracking', 'settings' ) as $module ) {
		require_once GNN_CONTENT_PATH . 'includes/' . $module . '.php';
	}
}
add_action( 'after_setup_theme', 'gnn_content_bootstrap', 20 );
/** Backward compatible registration entry point. */
function gnn_content_register_types() {
	if ( function_exists( 'gnn_register_content_modules' ) ) {
		if ( ! post_type_exists( 'gnn_project' ) || ! post_type_exists( 'gnn_product' ) ) {
			gnn_register_content_modules(); }
		gnn_register_slide_cpt();
		gnn_register_slide_meta();
		gnn_register_layout_content();
	}
}
/**
 * Preserve the legacy effective-capabilities callback.
 *
 * @param array $allcaps Capabilities.
 */
function gnn_content_capabilities( $allcaps ) {
	if ( function_exists( 'gnn_layout_capabilities' ) ) {
		return gnn_layout_capabilities( $allcaps );
	}
	return $allcaps;
}

/** Load plugin translations after WordPress locale initialization. */
function gnn_content_load_textdomain() {
	load_plugin_textdomain( 'gnn-content', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'gnn_content_load_textdomain', 0 );
