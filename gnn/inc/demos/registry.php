<?php
/**
 * Self-registering ready-made site (demo) registry.
 *
 * Adding a new demo requires only dropping one PHP file into `gnn/inc/demos/`
 * (plus its own CSS, optional JS, and images under `gnn/assets/images/demo/<key>/`).
 * The file registers from a callback on the `gnn_register_demos` action, never at include time,
 * so translated labels are resolved after the theme text domain is loaded:
 *
 *     add_action( 'gnn_register_demos', 'gnn_<key>_register' );
 *     function gnn_<key>_register() { gnn_demo_register( '<key>', array( ... ) ); }
 *
 * The action runs once, the first time the registry is read.
 *
 * Public API:
 * - `gnn_demo_register( string $key, array $args ): void`
 * - `gnn_demo_registry(): array` — `key => normalised args`, sorted by `order`
 *   ascending then `key` ascending, filtered through `gnn_demo_registry`.
 * - `gnn_demo_get( string $key ): ?array` — single normalised demo entry or `null`.
 *
 * `$args` contract (missing optional keys are normalised to the defaults below):
 * - `label`       (string, translated, required)   — Display name, e.g. `__( 'GNNpro', 'gnn' )`.
 * - `description` (string, translated, required)   — One-sentence summary.
 * - `featured`    (bool, default `false`)          — Whether highlighted in the gallery.
 * - `order`       (int, default `100`)             — Sort order (classic designs use 200-230).
 * - `profile`     (array, required)                — Design preset array matching `gnn_st_profile_pro()`
 *                                                    (accent, accent_light, header, radius, font,
 *                                                    heading_font, mode, light[6], dark[6], options[],
 *                                                    heading, description).
 * - `pages`       (callable, required)             — Returns `array( page_key => translated title )`
 *                                                    in menu order; must contain `home`.
 * - `page`        (callable, required)             — `function( string $page_key, array $page_ids, array $data, $group ): array`
 *                                                    returning Elementor elements built with `gnn/inc/templates/common.php` helpers.
 * - `data`        (callable, default empty arrays) — Returns `array( 'images' => [], 'projects' => [], 'articles' => [], 'slides' => [], 'products' => [] )`.
 * - `css`         (string, default `''`)           — Stylesheet path relative to `gnn/assets/css/`, e.g. `demos/gnnpro.css`.
 * - `js`          (string, default `''`)           — Script path relative to `gnn/assets/js/`, e.g. `demos/gnnagency.js`.
 * - `preview`     (string, default `''`)           — Theme-relative gallery preview image path, e.g. `assets/images/demo/gnnpro/preview.jpg`.
 * - `anchors`     (array, default `array()`)       — One-page menu anchors `array( section_id => translated label )`.
 *                                                    When non-empty, primary menu links use `home_url( '/#section_id' )`.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default data provider for demos that do not define custom sample records.
 *
 * @return array
 */
function gnn_demo_empty_data(): array {
	return array(
		'images'   => array(),
		'projects' => array(),
		'articles' => array(),
		'slides'   => array(),
		'products' => array(),
	);
}

/**
 * Internal static store for registered demo definitions.
 *
 * @param string|null $key  Sanitised demo key when storing.
 * @param array|null  $args Normalised demo definition when storing.
 * @return array
 */
function gnn_demo_store( ?string $key = null, ?array $args = null ): array {
	static $demos = array();
	if ( null !== $key && '' !== $key && null !== $args ) {
		$demos[ $key ] = $args;
	}
	return $demos;
}

/**
 * Register a ready-made site (demo) definition.
 *
 * @param string $key  Unique demo key (e.g. `gnnpro`).
 * @param array  $args Demo definition matching the registry contract.
 * @return void
 */
function gnn_demo_register( string $key, array $args ): void {
	$key = sanitize_key( $key );
	if ( '' === $key ) {
		return;
	}
	if (
		empty( $args['label'] ) || ! is_string( $args['label'] ) ||
		empty( $args['description'] ) || ! is_string( $args['description'] ) ||
		empty( $args['profile'] ) || ! is_array( $args['profile'] ) ||
		empty( $args['pages'] ) || ! is_callable( $args['pages'] ) ||
		empty( $args['page'] ) || ! is_callable( $args['page'] )
	) {
		return;
	}
	$normalised = array(
		'label'       => $args['label'],
		'description' => $args['description'],
		'featured'    => isset( $args['featured'] ) ? (bool) $args['featured'] : false,
		'order'       => isset( $args['order'] ) ? (int) $args['order'] : 100,
		'profile'     => $args['profile'],
		'pages'       => $args['pages'],
		'page'        => $args['page'],
		'data'        => isset( $args['data'] ) && is_callable( $args['data'] ) ? $args['data'] : 'gnn_demo_empty_data',
		'css'         => isset( $args['css'] ) && is_string( $args['css'] ) ? ltrim( $args['css'], '/' ) : '',
		'js'          => isset( $args['js'] ) && is_string( $args['js'] ) ? ltrim( $args['js'], '/' ) : '',
		'preview'     => isset( $args['preview'] ) && is_string( $args['preview'] ) ? ltrim( $args['preview'], '/' ) : '',
		'anchors'     => isset( $args['anchors'] ) && is_array( $args['anchors'] ) ? $args['anchors'] : array(),
	);
	gnn_demo_store( $key, $normalised );
}

/**
 * Return all registered demos, sorted by `order` then key, passed through `gnn_demo_registry`.
 *
 * @return array
 */
function gnn_demo_registry(): array {
	static $loaded = false;
	if ( ! $loaded ) {
		$loaded = true;
		do_action( 'gnn_register_demos' );
	}
	$demos = gnn_demo_store();
	uksort(
		$demos,
		static function ( $a, $b ) use ( $demos ) {
			$order_a = (int) ( $demos[ $a ]['order'] ?? 100 );
			$order_b = (int) ( $demos[ $b ]['order'] ?? 100 );
			if ( $order_a !== $order_b ) {
				return $order_a <=> $order_b;
			}
			return strcmp( (string) $a, (string) $b );
		}
	);
	$filtered = apply_filters( 'gnn_demo_registry', $demos );
	return is_array( $filtered ) ? $filtered : array();
}

/**
 * Retrieve a single registered demo definition by key.
 *
 * @param string $key Demo key.
 * @return array|null
 */
function gnn_demo_get( string $key ): ?array {
	$key = sanitize_key( $key );
	if ( '' === $key ) {
		return null;
	}
	$demos = gnn_demo_registry();
	return isset( $demos[ $key ] ) && is_array( $demos[ $key ] ) ? $demos[ $key ] : null;
}
