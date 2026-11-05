<?php
/**
 * GNN client extension point. Parent assets already load this stylesheet last.
 *
 * @package GNN_Child
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Preserve shared branding/menu choices on first child-theme activation. */
function gnn_child_preserve_branding() {
	$parent = (array) get_option( 'theme_mods_gnn', array() );
	$current = (array) get_theme_mods();
	foreach ( array( 'gnn_accent_color', 'gnn_default_theme', 'gnn_footer_legal', 'logo_light', 'logo_dark', 'logo_light_2x', 'logo_dark_2x', 'nav_menu_locations' ) as $key ) {
		if ( ! array_key_exists( $key, $current ) && array_key_exists( $key, $parent ) ) { set_theme_mod( $key, $parent[ $key ] ); }
	}
}
add_action( 'after_switch_theme', 'gnn_child_preserve_branding' );
