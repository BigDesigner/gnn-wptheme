<?php
/**
 * Native Free Elementor widgets for centrally managed content.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add four small query widgets, without installing any saved-template database rows.
 *
 * @param object $manager Native widget manager.
 */
function gnn_register_elementor_modules( $manager ) {
	require_once get_template_directory() . '/inc/class-gnn-elementor-module.php';
	foreach ( array( 'portfolio', 'gallery', 'products', 'slider' ) as $module ) {
		require_once get_template_directory() . '/inc/class-gnn-elementor-' . $module . '.php';
		$class = 'GNN_Elementor_' . ucfirst( $module );
		$manager->register( new $class() );
	}
}
