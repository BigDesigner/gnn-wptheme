<?php
/**
 * Plugin Name: GNN Content Continuity
 * Description: Keeps existing GNN slides and reusable layouts available when the presentation theme changes.
 * Version: 2.0.2
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License: GPL-2.0-or-later
 * Text Domain: gnn-content
 *
 * @package GNN_Content
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Register existing keys, never copy or delete customer records. */
function gnn_content_register_types() {
	$gnn = 'gnn' === get_template();
	if ( ! post_type_exists( 'gnn_slide' ) ) {
		register_post_type( 'gnn_slide', array( 'label' => __( 'Slides', 'gnn-content' ), 'public' => false, 'show_ui' => true, 'show_in_rest' => true, 'show_in_menu' => $gnn ? 'gnn-panel' : true, 'supports' => array( 'title', 'thumbnail', 'page-attributes' ), 'capability_type' => 'page' ) );
	}
	if ( ! post_type_exists( 'gnn_layout' ) ) {
		register_post_type( 'gnn_layout', array( 'label' => __( 'Reusable layouts', 'gnn-content' ), 'public' => false, 'show_ui' => true, 'show_in_rest' => true, 'show_in_menu' => $gnn ? 'gnn-panel' : true, 'supports' => array( 'title', 'editor', 'revisions' ), 'capability_type' => array( 'gnn_layout', 'gnn_layouts' ), 'map_meta_cap' => true ) );
	}
	if ( ! taxonomy_exists( 'gnn_slide_group' ) ) { register_taxonomy( 'gnn_slide_group', 'gnn_slide', array( 'label' => __( 'Slider groups', 'gnn-content' ), 'public' => false, 'show_ui' => true, 'show_in_rest' => true, 'hierarchical' => true ) ); }
	foreach ( array( '_gnn_slide_kicker', '_gnn_slide_text', '_gnn_slide_cta_label', '_gnn_slide_cta_url', '_gnn_slide_fit' ) as $key ) {
		register_post_meta( 'gnn_slide', $key, array( 'type' => 'string', 'single' => true, 'show_in_rest' => true, 'sanitize_callback' => '_gnn_slide_cta_url' === $key ? 'esc_url_raw' : 'sanitize_textarea_field', 'auth_callback' => function ( $allowed, $key, $id ) { return current_user_can( 'edit_post', $id ); } ) );
	}
}
add_action( 'init', 'gnn_content_register_types', 8 );

/** Preserve the same role boundaries after a theme switch. */
function gnn_content_capabilities( $allcaps ) {
 if ( ! empty( $allcaps['manage_options'] ) || ! empty( $allcaps['gnn_manage_design'] ) ) {
  foreach ( array( 'edit_gnn_layouts', 'edit_others_gnn_layouts', 'edit_published_gnn_layouts', 'edit_private_gnn_layouts', 'publish_gnn_layouts', 'read_private_gnn_layouts', 'delete_gnn_layouts', 'delete_others_gnn_layouts', 'delete_published_gnn_layouts', 'delete_private_gnn_layouts' ) as $cap ) { $allcaps[ $cap ] = true; }
 }
 return $allcaps;
}
add_filter( 'user_has_cap', 'gnn_content_capabilities' );
