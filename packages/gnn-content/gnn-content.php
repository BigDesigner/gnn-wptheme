<?php
/**
 * Plugin Name: GNN Content Continuity
 * Description: Keeps GNN slides, reusable layouts, portfolio projects, categories and gallery collections available when the presentation theme changes.
 * Version: 2.3.0
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

	foreach ( array( 'gnn_project' => __( 'Portfolio projects', 'gnn-content' ), 'gnn_album' => __( 'Gallery albums', 'gnn-content' ) ) as $type => $label ) {
		if ( ! $gnn && ! post_type_exists( $type ) ) {
			register_post_type( $type, array( 'label' => $label, 'public' => 'gnn_project' === $type, 'show_ui' => true, 'show_in_rest' => true, 'show_in_menu' => $gnn ? 'gnn-panel' : true, 'has_archive' => false, 'rewrite' => 'gnn_project' === $type ? array( 'slug' => 'projects', 'with_front' => false ) : false, 'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes', 'custom-fields' ), 'capability_type' => 'page', 'map_meta_cap' => true ) );
		}
		register_post_meta( $type, '_gnn_gallery_ids', array( 'type' => 'array', 'single' => true, 'default' => array(), 'show_in_rest' => array( 'schema' => array( 'type' => 'array', 'items' => array( 'type' => 'integer' ) ) ), 'sanitize_callback' => function ( $ids ) { return array_values( array_unique( array_filter( array_map( 'absint', array_slice( (array) $ids, 0, 100 ) ), 'wp_attachment_is_image' ) ) ); }, 'auth_callback' => function ( $allowed, $key, $id ) { return current_user_can( 'edit_post', $id ); } ) );
	}
	if ( ! $gnn && ! taxonomy_exists( 'gnn_project_category' ) ) {
		register_taxonomy( 'gnn_project_category', 'gnn_project', array( 'label' => __( 'Project categories', 'gnn-content' ), 'public' => true, 'show_ui' => true, 'show_in_rest' => true, 'hierarchical' => true, 'rewrite' => array( 'slug' => 'project-category', 'with_front' => false ) ) );
	}
	foreach ( array( '_gnn_project_location', '_gnn_project_year', '_gnn_project_client' ) as $key ) {
		register_post_meta( 'gnn_project', $key, array( 'type' => 'string', 'single' => true, 'show_in_rest' => true, 'sanitize_callback' => 'sanitize_text_field', 'auth_callback' => function ( $allowed, $key, $id ) { return current_user_can( 'edit_post', $id ); } ) );
	}

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
