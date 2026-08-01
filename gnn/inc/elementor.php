<?php
/**
 * Elementor (Free + Pro) integration. Loaded only when Elementor is active —
 * zero overhead otherwise.
 *
 * Free: full editing of the content area; the theme's tokens are bridged so
 * Elementor widgets inherit the design (colors, fonts) out of the box.
 * Pro: header / footer / single / archive Theme Builder locations — build a
 * template there and it replaces the theme part; leave it empty and the
 * theme's own part renders.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register all core Theme Builder locations (header, footer, single, archive).
 *
 * @param object $manager Elementor locations manager.
 */
function gnn_register_elementor_locations( $manager ) {
	$manager->register_all_core_location();
}
add_action( 'elementor/theme/register_locations', 'gnn_register_elementor_locations' );

/**
 * First activation next to Elementor: align its defaults with the theme —
 * container width 1280 and "inherit theme colors/fonts" (disable Elementor's
 * own kits) so widgets pick up the GNN tokens. Only touches unset options.
 */
function gnn_elementor_defaults() {
	if ( ! get_option( 'elementor_container_width' ) ) {
		update_option( 'elementor_container_width', 1280 );
	}
	if ( false === get_option( 'elementor_disable_color_schemes', false ) ) {
		update_option( 'elementor_disable_color_schemes', 'yes' );
	}
	if ( false === get_option( 'elementor_disable_typography_schemes', false ) ) {
		update_option( 'elementor_disable_typography_schemes', 'yes' );
	}
}
add_action( 'after_switch_theme', 'gnn_elementor_defaults' );

/**
 * Bridge the theme's CSS custom properties into Elementor content so
 * dark/light mode works inside Elementor-built sections too.
 */
function gnn_elementor_bridge_css() {
	$css = '.elementor-widget-container, .elementor-section, .e-con {' .
		'--e-global-color-primary: var(--accent);' .
		'--e-global-color-text: var(--fg);' .
		'}' .
		'.elementor a:not(.elementor-button):not(.gnn-btn) { color: var(--accent); }';
	wp_add_inline_style( 'gnn-main', $css );
}
add_action( 'wp_enqueue_scripts', 'gnn_elementor_bridge_css', 20 );
