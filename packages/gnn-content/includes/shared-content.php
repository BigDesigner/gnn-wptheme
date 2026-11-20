<?php
/**
 * GNN Content independent API.
 *
 * @package GNN_Content
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; }
/**
 * Render published shared content with recursion protection.
 *
 * @param int $id Record ID.
 */
function gnn_shared_content( $id ) {
	if ( (int) $id <= 0 ) {
		return ''; }
	static $stack = array();
	$id           = gnn_translated_post_id( absint( $id ) );
	$record       = get_post( $id );
	if ( ! $record || ! in_array( $record->post_type, array( 'gnn_layout', 'elementor_library' ), true ) || 'publish' !== $record->post_status || $record->post_password || isset( $stack[ $id ] ) || count( $stack ) >= 8 ) {
		return '';
	}
	$stack[ $id ] = true;
	try {
		if ( 'builder' === get_post_meta( $id, '_elementor_edit_mode', true ) && class_exists( '\Elementor\Plugin' ) ) {
			ob_start();
			try {
				$content = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $id, true );
				return ob_get_contents() . $content;
			} finally {
				ob_end_clean(); }
		}
		return do_shortcode( do_blocks( $record->post_content ) );
	} finally {
		unset( $stack[ $id ] );
	}
}

/** Shared content shortcode can be used in native blocks or builder text.
 *
 * @param array $attrs Attributes.
 */
function gnn_shared_shortcode( $attrs ) {
	return gnn_shared_content( absint( $attrs['id'] ?? 0 ) );
}
/** Register the shared shortcode without replacing another owner. */
function gnn_content_register_shortcode() {
	if ( ! shortcode_exists( 'gnn_block' ) ) {
		add_shortcode( 'gnn_block', 'gnn_shared_shortcode' );
	}
}
add_action( 'init', 'gnn_content_register_shortcode', 9 );

/** Enable Elementor Free on shared regions without duplicating content. */
function gnn_shared_editor_support() {
	add_post_type_support( 'gnn_layout', 'elementor' );
	$types = get_option( 'elementor_cpt_support', array( 'page', 'post' ) );
	if ( is_array( $types ) && ! in_array( 'gnn_layout', $types, true ) ) {
		$types[] = 'gnn_layout';
		update_option( 'elementor_cpt_support', $types );
	}
}
add_action( 'init', 'gnn_shared_editor_support', 30 );
