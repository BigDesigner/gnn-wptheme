<?php
/**
 * Independent adapters.
 *
 * @package GNN_Content
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; }
/**
 * Read preserved options.
 *
 * @param string $key Setting.
 */
function gnn_option( $key ) {
	$defaults = array(
		'portfolio_per_page' => 12,
		'portfolio_columns'  => 3,
		'gallery_columns'    => 3,
		'shop_columns'       => 3,
		'slider_interval'    => 6,
		'analytics_delivery' => 'consent',
	);
	$options  = get_option( 'gnn_options', array() );
	return is_array( $options ) && array_key_exists( $key, $options ) ? $options[ $key ] : ( $defaults[ $key ] ?? '' );
}
/**
 * Translate a record.
 *
 * @param int $id Record.
 */
function gnn_translated_post_id( $id ) {
	if ( function_exists( 'pll_get_post' ) ) {
		$translated = absint( pll_get_post( $id ) );
		return $translated ? $translated : $id; }
	return absint( apply_filters( 'wpml_object_id', $id, get_post_type( $id ), true ) );
}
/**
 * Render fixed local template.
 *
 * @param array $args Settings.
 */
function gnn_content_slider_template( $args ) {
	$args = is_array( $args ) ? $args : array();
	include GNN_CONTENT_PATH . 'templates/hero-slider.php';
}
/**
 * Register ready widgets.
 *
 * @param object $manager Manager.
 */
function gnn_content_widgets( $manager ) {
	if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
		return; }
	require_once GNN_CONTENT_PATH . 'includes/elementor/class-gnn-elementor-module.php';
	foreach ( array( 'portfolio', 'catalog', 'gallery', 'products', 'slider', 'block' ) as $name ) {
		$class = 'GNN_Elementor_' . ucfirst( $name );
		if ( ! class_exists( $class ) ) {
			require_once GNN_CONTENT_PATH . 'includes/elementor/class-gnn-elementor-' . $name . '.php'; }
		if ( ! isset( $manager->get_widget_types()[ 'gnn-' . $name ] ) ) {
			$manager->register( new $class() ); }
	}
}
add_action( 'elementor/widgets/register', 'gnn_content_widgets' );
/**
 * Extend the multilingual model.
 *
 * @param array $types Types.
 */
function gnn_content_language_types( $types ) {
	foreach ( array( 'gnn_project', 'gnn_product', 'gnn_album', 'gnn_slide', 'gnn_layout' ) as $type ) {
		$types[ $type ] = $type; }
	return $types;
}
add_filter( 'pll_get_post_types', 'gnn_content_language_types' );
