<?php
/**
 * Header settings, legacy defaults and per-page precedence.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Header fields shared by the native settings and import validators. */
function gnn_header_schema() {
	$inherit = array( '' => __( 'Use existing setting', 'gnn' ) );
	return array(
		'header_structure'         => array( __( 'Structure', 'gnn' ), 'select', '', 'header', $inherit + array( 'classic' => __( 'Classic', 'gnn' ), 'centered' => __( 'Centered', 'gnn' ), 'stacked' => __( 'Stacked', 'gnn' ), 'shop' => __( 'Shop', 'gnn' ) ) ),
		'header_surface'           => array( __( 'Surface', 'gnn' ), 'select', '', 'header', $inherit + array( 'solid' => __( 'Solid', 'gnn' ), 'glass' => __( 'Glass', 'gnn' ), 'transparent' => __( 'Transparent', 'gnn' ) ) ),
		'header_shape'             => array( __( 'Shape', 'gnn' ), 'select', '', 'header', $inherit + array( 'full' => __( 'Full', 'gnn' ), 'floating' => __( 'Floating', 'gnn' ) ) ),
		'header_width'             => array( __( 'Width', 'gnn' ), 'select', '', 'header', $inherit + array( 'contained' => __( 'Contained', 'gnn' ), 'wide' => __( 'Wide', 'gnn' ) ) ),
		'header_behavior'          => array( __( 'Scroll behavior', 'gnn' ), 'select', '', 'header', $inherit + array( 'static' => __( 'Static', 'gnn' ), 'sticky' => __( 'Sticky', 'gnn' ), 'hide' => __( 'Hide on scroll', 'gnn' ), 'shrink' => __( 'Shrink on scroll', 'gnn' ) ) ),
		'header_overlay'           => array( __( 'Overlay first section', 'gnn' ), 'bool', '', 'header' ),
		'logo_overlay'             => array( __( 'Overlay logo', 'gnn' ), 'media', 0, 'header' ),
		'header_mobile_breakpoint' => array( __( 'Mobile breakpoint', 'gnn' ), 'number', 900, 'header', 600, 1280 ),
		'header_overflow'          => array( __( 'Menu overflow', 'gnn' ), 'select', 'more', 'header', array( 'more' => __( 'More menu', 'gnn' ), 'collapse' => __( 'Mobile menu', 'gnn' ) ) ),
		'header_search_mode'       => array( __( 'Search mode', 'gnn' ), 'select', '', 'header', $inherit + array( 'link' => __( 'Link', 'gnn' ), 'overlay' => __( 'Overlay', 'gnn' ), 'field' => __( 'Field', 'gnn' ) ) ),
		'header_search_scope'      => array( __( 'Search scope', 'gnn' ), 'select', 'all', 'header', array( 'all' => __( 'Entire site', 'gnn' ), 'products' => __( 'Products', 'gnn' ) ) ),
		'header_elements'          => array( __( 'Header elements', 'gnn' ), 'header_elements', '', 'header' ),
		'header_text_dark'         => array( __( 'Header text (dark)', 'gnn' ), 'hex', '', 'header' ),
		'header_text_light'        => array( __( 'Header text (light)', 'gnn' ), 'hex', '', 'header' ),
		'header_accent_dark'       => array( __( 'Header accent (dark)', 'gnn' ), 'hex', '', 'header' ),
		'header_accent_light'      => array( __( 'Header accent (light)', 'gnn' ), 'hex', '', 'header' ),
		'topbar_mobile'            => array( __( 'Top bar on mobile', 'gnn' ), 'bool', 1, 'header' ),
	);
}

/** Default order and visibility for sites without an explicit list. */
function gnn_header_default_elements() {
	return array_map(
		static function ( $id ) {
			return array( 'id' => $id, 'desktop' => true, 'mobile' => true );
		},
		array( 'contact', 'language', 'cta', 'search', 'theme', 'cart' )
	);
}

/**
 * Decode an exact JSON list. Empty input inherits; an empty list hides all.
 *
 * @return array|null|WP_Error
 */
function gnn_parse_header_elements( $raw ) {
	if ( '' === $raw ) {
		return null;
	}
	$items = is_string( $raw ) && strlen( $raw ) <= 4096 ? json_decode( $raw, false, 4 ) : null;
	$valid = array_column( gnn_header_default_elements(), 'id' );
	$out   = array();
	if ( is_array( $items ) && count( $items ) <= count( $valid ) ) {
		foreach ( $items as $item ) {
			if ( ! is_object( $item ) || 3 !== count( get_object_vars( $item ) )
				|| ! isset( $item->id, $item->desktop, $item->mobile )
				|| ! in_array( $item->id, $valid, true ) || isset( $out[ $item->id ] )
				|| ! is_bool( $item->desktop ) || ! is_bool( $item->mobile ) ) {
				return new WP_Error( 'gnn_header_elements', __( 'Invalid header elements.', 'gnn' ) );
			}
			$out[ $item->id ] = array( 'id' => $item->id, 'desktop' => $item->desktop, 'mobile' => $item->mobile );
		}
		return array_values( $out );
	}
	return new WP_Error( 'gnn_header_elements', __( 'Invalid header elements.', 'gnn' ) );
}

/** Return the first non-empty translated public contact value. */
function gnn_header_contact_details() {
	$details = array();
	foreach ( array( 'phone', 'email' ) as $type ) {
		$value = trim( (string) gnn_translated_option( 'topbar_' . $type ) );
		$details[ $type ] = '' === $value ? trim( (string) gnn_translated_option( 'company_' . $type ) ) : $value;
	}
	return $details;
}

/**
 * Resolve the request header. A page preset supplies its own legacy baseline,
 * and explicit per-page surface and overlay choices take final precedence.
 *
 * @return array Effective option values plus derived rendering settings.
 */
function gnn_header_config() {
	$schema = gnn_header_schema();
	$id     = is_singular() ? get_queried_object_id() : 0;
	$meta   = $id ? (array) get_post_meta( $id, '_gnn_layout', true ) : array();
	$presets = array( 'legacy', 'classic', 'centered', 'stacked', 'transparent', 'minimal', 'shop', 'floating' );
	$page_preset = in_array( $meta['header_preset'] ?? null, $presets, true );
	$preset = $page_preset ? $meta['header_preset'] : gnn_option( 'header_preset' );
	$preset = in_array( $preset, $presets, true ) ? $preset : 'legacy';
	$config = array( 'preset' => $preset );

	foreach ( $schema as $key => $field ) {
		$value = gnn_option( $key );
		if ( 'select' === $field[1] && ( ! is_string( $value ) || ! array_key_exists( $value, $field[4] ) ) ) {
			$value = $field[2];
		}
		$config[ $key ] = $value;
	}

	$legacy_structure = 'centered' === gnn_option( 'header_layout' ) ? 'centered' : 'classic';
	$structure = in_array( $preset, array( 'classic', 'centered', 'stacked', 'shop' ), true ) ? $preset : 'classic';
	$baseline = array(
		'header_structure' => 'legacy' === $preset ? $legacy_structure : $structure,
		'header_surface'   => 'transparent' === $preset ? 'transparent' : ( 'minimal' === $preset ? 'solid' : 'glass' ),
		'header_shape'     => 'floating' === $preset ? 'floating' : 'full',
		'header_width'     => 'minimal' === $preset ? 'wide' : 'contained',
	);
	foreach ( $baseline as $key => $value ) {
		if ( $page_preset || '' === $config[ $key ] ) {
			$config[ $key ] = $value;
		}
	}
	$surface = $meta['header_surface'] ?? '';
	if ( is_string( $surface ) && '' !== $surface && isset( $schema['header_surface'][4][ $surface ] ) ) {
		$config['header_surface'] = $surface;
	}
	$overlay = $config['header_overlay'];
	if ( isset( $meta['header_overlay'] ) && in_array( $meta['header_overlay'], array( 0, 1, '0', '1', false, true ), true ) ) {
		$overlay = $meta['header_overlay'];
	}
	$config['overlay_inherited'] = ! in_array( $overlay, array( 0, 1, '0', '1', false, true ), true );
	$config['header_overlay'] = $config['overlay_inherited'] ? 'transparent' === $preset : (bool) $overlay;

	$config['header_behavior'] = $config['header_behavior'] ?: ( gnn_option( 'sticky_header' ) ? 'sticky' : 'static' );
	$legacy_field = '' === $config['header_search_mode'] && gnn_option( 'header_search_bar' );
	$config['header_search_mode'] = $config['header_search_mode'] ?: ( $legacy_field ? 'field' : 'link' );
	$config['header_search_scope'] = class_exists( 'WooCommerce' ) ? $config['header_search_scope'] : 'all';
	$config['header_height'] = gnn_header_dimension( gnn_option( 'header_height' ), 64, 48, 160 );
	$config['header_mobile_breakpoint'] = gnn_header_dimension( $config['header_mobile_breakpoint'], 900, 600, 1280 );
	$config['topbar_mobile'] = in_array( $config['topbar_mobile'], array( 0, '0', false ), true ) ? false : true;
	$config['logo_overlay'] = is_numeric( $config['logo_overlay'] ) ? max( 0, (int) $config['logo_overlay'] ) : 0;
	$config['header_template'] = (int) gnn_layout_value( 'header_template' );
	$config['two_rows'] = in_array( $config['header_structure'], array( 'stacked', 'shop' ), true );
	$align = gnn_option( 'header_menu_align' );
	$config['header_menu_align'] = in_array( $align, array( 'left', 'center', 'right' ), true ) ? $align : 'right';
	$config['enabled'] = array(
		'contact'  => (bool) gnn_option( 'header_contact' ),
		'language' => (bool) gnn_option( 'header_language' ),
		'cta'      => (bool) gnn_option( 'header_cta_show' ) && gnn_option( 'header_cta_label' ) && gnn_option( 'header_cta_url' ),
		'search'   => $legacy_field || gnn_option( 'show_search' ),
		'theme'    => (bool) gnn_option( 'show_toggle' ),
		'cart'     => (bool) gnn_option( 'show_cart' ) && function_exists( 'wc_get_cart_url' ),
	);
	$elements = gnn_parse_header_elements( $config['header_elements'] );
	$config['header_elements'] = is_array( $elements ) ? $elements : gnn_header_default_elements();
	$config['contact'] = gnn_header_contact_details();
	$config['topbar_text'] = trim( (string) gnn_translated_option( 'topbar_text' ) );
	$config['topbar'] = gnn_option( 'topbar_enable' ) && ( '' !== $config['topbar_text'] || array_filter( $config['contact'] ) );
	foreach ( array( 'light', 'dark' ) as $mode ) {
		foreach ( array( 'bg', 'text', 'accent' ) as $part ) {
			$key = 'header_' . $part . '_' . $mode;
			$raw = gnn_option( $key );
			$config[ $key ] = is_string( $raw ) ? (string) sanitize_hex_color( $raw ) : '';
		}
	}
	return $config;
}

/** Normalize dimensions read from older imports and option records. */
function gnn_header_dimension( $value, $default, $min, $max ) {
	return is_numeric( $value ) ? max( $min, min( $max, (int) $value ) ) : $default;
}
