<?php
/**
 * Real WordPress section-library contract checks. Run in separate Woo on/off requests.
 *
 * @package GNN
 */
$checks = array();
$check = static function ( $name, $pass, $detail = '' ) use ( &$checks ) {
	$checks[] = array( 'name' => $name, 'pass' => (bool) $pass, 'detail' => $detail );
};
$check( 'Sections registry loads independently of Elementor', function_exists( 'gnn_sections' ) );
$notices = array();
set_error_handler(
	static function ( $severity, $message, $file, $line ) use ( &$notices ) {
		$notices[] = "$message ($file:$line)";
		return true;
	}
);
try {
	$sections = gnn_sections();
	$check( 'Section definitions are available with or without Elementor', ! empty( $sections ) );
	$check( 'Registration hook fires once', 1 === did_action( 'gnn_sections_register' ) );
	$check( 'Unknown section returns empty array', array() === gnn_section_build( 'missing-section' ) );
	$check( 'Registry resolves arbitrary callable labels', get_bloginfo() === gnn_sections_resolve_label( 'get_bloginfo' ) );
	$check( 'Registry resolves deferred closure labels', 'Deferred label' === gnn_sections_resolve_label( static function () { return 'Deferred label'; } ) );
	$shop_ids = array( 'product-categories', 'featured-products', 'promo-banners', 'usp-strip' );
	$shop_on = class_exists( 'WooCommerce' );
	$check( 'Woo sections registered only with WooCommerce', $shop_on ? 4 === count( array_intersect( $shop_ids, array_keys( $sections ) ) ) : array() === array_intersect( $shop_ids, array_keys( $sections ) ) );
	foreach ( $shop_ids as $id ) {
		if ( ! $shop_on ) {
			$check( "$id silently unavailable without Woo", array() === gnn_section_build( $id ) );
		}
	}
	foreach ( $sections as $id => $section ) {
		foreach ( $section['variants'] as $variant => $label ) {
			$before = count( $notices );
			$data = gnn_section_build( $id, array( 'variant' => $variant ) );
			$ids = array();
			$errors = array();
			$walk = static function ( $elements ) use ( &$walk, &$ids, &$errors ) {
				foreach ( $elements as $element ) {
					if ( ! is_array( $element ) || ! isset( $element['id'], $element['elType'], $element['settings'], $element['elements'] ) || ! is_string( $element['id'] ) || ! is_array( $element['settings'] ) || ! is_array( $element['elements'] ) || ! in_array( $element['elType'], array( 'container', 'widget' ), true ) ) {
						$errors[] = 'Invalid element shape';
						continue;
					}
					if ( '' === $element['id'] || isset( $ids[ $element['id'] ] ) ) {
						$errors[] = 'Duplicate or empty ID';
					}
					$ids[ $element['id'] ] = true;
					if ( 'widget' === $element['elType'] && empty( $element['widgetType'] ) ) {
						$errors[] = 'Missing widget type';
					}
					$walk( $element['elements'] );
				}
			};
			if ( is_array( $data ) && ! empty( $data ) ) {
				$walk( $data );
			} else {
				$errors[] = 'Empty build';
			}
			$classes = $data[0]['settings']['css_classes'] ?? '';
			$valid = empty( $errors ) && count( $notices ) === $before && false !== wp_json_encode( $data ) && false !== strpos( $classes, 'gnn-sec-' . $id ) && false !== strpos( $classes, 'gnn-sec--' . $variant );
			$check( "$id/$variant: valid unique Elementor tree, JSON, scope, no notices", $valid, implode( '; ', array_merge( $errors, array_slice( $notices, $before ) ) ) );
		}
	}
	$check( 'Section build and registration emit no PHP notices', empty( $notices ), implode( '; ', $notices ) );
} catch ( Throwable $error ) {
	$check( 'Section integration completed', false, $error->getMessage() );
} finally {
	restore_error_handler();
}
echo 'GNN_QA_RESULT:' . wp_json_encode( $checks );
