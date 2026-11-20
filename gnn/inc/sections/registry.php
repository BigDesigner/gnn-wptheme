<?php
/**
 * Reusable Elementor section registry and conditional stylesheet loader.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Internal store for registered section definitions.
 *
 * @param string|null $id   Section identifier when storing a definition.
 * @param array|null  $args Section definition array when storing.
 * @return array Registered section definitions keyed by section ID.
 */
function gnn_sections_store( $id = null, $args = null ) {
	static $sections = array();
	if ( null !== $id && '' !== $id && is_array( $args ) ) {
		$sections[ $id ] = $args;
	}
	return $sections;
}

/**
 * Resolve a section or variant label registered as a string or lazy callable.
 *
 * @param callable|string $label Translated string or callable returning one.
 * @return string
 */
function gnn_sections_resolve_label( $label ) {
	if ( is_callable( $label ) ) {
		return (string) call_user_func( $label );
	}
	return is_scalar( $label ) ? (string) $label : '';
}

/**
 * Register a section definition in the central section library.
 *
 * @param string $id   Unique section identifier.
 * @param array  $args Section arguments: label, category, variants, build.
 * @return void
 */
function gnn_section_register( $id, $args ) {
	$id = sanitize_key( (string) $id );
	if ( '' === $id || ! is_array( $args ) || empty( $args['build'] ) || ! is_callable( $args['build'] ) ) {
		return;
	}
	$variants = array();
	if ( isset( $args['variants'] ) && is_array( $args['variants'] ) ) {
		foreach ( $args['variants'] as $slug => $vlabel ) {
			$slug = sanitize_key( (string) $slug );
			if ( '' !== $slug ) {
				$variants[ $slug ] = $vlabel;
			}
		}
	}
	gnn_sections_store(
		$id,
		array(
			'id'       => $id,
			'label'    => $args['label'] ?? $id,
			'category' => isset( $args['category'] ) ? sanitize_key( (string) $args['category'] ) : $id,
			'variants' => $variants,
			'build'    => $args['build'],
		)
	);
}

/**
 * Return all registered section definitions, firing the registration hook on first call.
 *
 * @return array
 */
function gnn_sections() {
	static $booted = false;
	if ( ! $booted ) {
		$booted = true;
		do_action( 'gnn_sections_register' );
	}
	$sections = gnn_sections_store();
	foreach ( $sections as $id => $section ) {
		$sections[ $id ]['label'] = gnn_sections_resolve_label( $section['label'] );
		if ( ! empty( $section['variants'] ) && is_array( $section['variants'] ) ) {
			foreach ( $section['variants'] as $slug => $vlabel ) {
				$sections[ $id ]['variants'][ $slug ] = gnn_sections_resolve_label( $vlabel );
			}
		}
	}
	return $sections;
}

/**
 * Build Elementor container and widget elements for a registered section.
 *
 * @param string $id Section identifier.
 * @param array  $a  Optional section overrides (title, eyebrow, text, button_label, button_url, image, items, variant).
 * @return array
 */
function gnn_section_build( $id, $a = array() ) {
	$id       = sanitize_key( (string) $id );
	$sections = gnn_sections();
	if ( '' === $id || ! isset( $sections[ $id ]['build'] ) || ! is_callable( $sections[ $id ]['build'] ) ) {
		return array();
	}
	$args     = is_array( $a ) ? $a : array();
	$elements = call_user_func( $sections[ $id ]['build'], $args );
	if ( ! is_array( $elements ) || empty( $elements ) ) {
		return array();
	}
	if ( isset( $elements['elType'] ) ) {
		$elements = array( $elements );
	}
	$variants = isset( $sections[ $id ]['variants'] ) && is_array( $sections[ $id ]['variants'] ) ? array_keys( $sections[ $id ]['variants'] ) : array();
	$variant  = isset( $args['variant'] ) ? sanitize_key( (string) $args['variant'] ) : '';
	if ( ( '' === $variant || ( ! empty( $variants ) && ! in_array( $variant, $variants, true ) ) ) && ! empty( $variants ) ) {
		$variant = (string) $variants[0];
	}
	if ( '' !== $variant && isset( $elements[0]['settings']['css_classes'] ) && is_string( $elements[0]['settings']['css_classes'] ) ) {
		$variant_class = 'gnn-sec--' . $variant;
		if ( false === strpos( $elements[0]['settings']['css_classes'], 'gnn-sec--' ) ) {
			$elements[0]['settings']['css_classes'] .= ' ' . $variant_class;
		}
	}
	return $elements;
}

/**
 * Check whether a post's Elementor data or content references `gnn-sec`.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function gnn_sections_post_has_sec( $post_id ) {
	$post_id = absint( $post_id );
	if ( ! $post_id ) {
		return false;
	}
	$data = (string) get_post_meta( $post_id, '_elementor_data', true );
	if ( '' !== $data && false !== strpos( $data, 'gnn-sec' ) ) {
		return true;
	}
	$post = get_post( $post_id );
	return $post instanceof WP_Post && false !== strpos( (string) $post->post_content, 'gnn-sec' );
}

/**
 * Enqueue section stylesheets when the current page contains `gnn-sec` in Elementor data.
 *
 * @return void
 */
function gnn_sections_enqueue() {
	$post_id = get_queried_object_id();
	if ( ! $post_id ) {
		$post_id = (int) get_the_ID();
	}
	if ( ! $post_id || ! gnn_sections_post_has_sec( $post_id ) ) {
		return;
	}
	$dir   = get_theme_file_path( 'assets/css/sections' );
	$files = glob( $dir . '/*.css' );
	if ( ! is_array( $files ) || empty( $files ) ) {
		return;
	}
	sort( $files );
	$ver      = defined( 'GNN_VERSION' ) ? GNN_VERSION : '1.0.0';
	$has_base = is_file( $dir . '/base.css' );
	if ( $has_base ) {
		wp_enqueue_style(
			'gnn-sec-base',
			get_theme_file_uri( 'assets/css/sections/base.css' ),
			array( 'gnn-design-system' ),
			$ver
		);
	}
	foreach ( $files as $file ) {
		$name = basename( $file, '.css' );
		if ( 'base' === $name ) {
			continue;
		}
		$deps = $has_base ? array( 'gnn-design-system', 'gnn-sec-base' ) : array( 'gnn-design-system' );
		wp_enqueue_style(
			'gnn-sec-' . sanitize_key( $name ),
			get_theme_file_uri( 'assets/css/sections/' . basename( $file ) ),
			$deps,
			$ver
		);
	}
}
add_action( 'wp_enqueue_scripts', 'gnn_sections_enqueue', 20 );

/**
 * Load all section definition files in this directory except the registry itself.
 *
 * @return void
 */
function gnn_sections_load() {
	$files = glob( __DIR__ . '/*.php' );
	if ( ! is_array( $files ) ) {
		return;
	}
	sort( $files );
	foreach ( $files as $file ) {
		if ( 'registry.php' !== basename( $file ) ) {
			require_once $file;
		}
	}
}
gnn_sections_load();
