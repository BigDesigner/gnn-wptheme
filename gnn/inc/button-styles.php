<?php
/**
 * GNN Button Styles — 6 fixed, admin-defined button variants
 * (GNN Panel → Button Styles).
 *
 * Each configured slot becomes a `.gnn-btn-style-{n}` class an editor can
 * type into Gutenberg's or Elementor's "Additional CSS Class(es)" field,
 * layered on top of the theme's own `.gnn-btn` (pill shape, weight,
 * padding) — only the colors an admin actually sets are ever emitted, so
 * unused slots produce zero CSS.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fixed button style slots.
 *
 * @return int[] 1..6.
 */
function gnn_button_style_slots() {
	return range( 1, 6 );
}

/**
 * The CSS class a configured slot generates.
 *
 * @param int $slot Slot number (1-6).
 * @return string
 */
function gnn_button_style_class( $slot ) {
	return 'gnn-btn-style-' . (int) $slot;
}

/**
 * Inline CSS for every slot that has a label set. Colors left empty for a
 * configured slot simply inherit `.gnn-btn`'s own accent-based defaults.
 */
function gnn_button_styles_inline_css() {
	$css = '';
	foreach ( gnn_button_style_slots() as $slot ) {
		$label = trim( (string) gnn_option( "btn_style_{$slot}_label" ) );
		if ( '' === $label ) {
			continue;
		}
		$bg     = trim( (string) gnn_option( "btn_style_{$slot}_bg" ) );
		$text   = trim( (string) gnn_option( "btn_style_{$slot}_text" ) );
		$border = trim( (string) gnn_option( "btn_style_{$slot}_border" ) );

		$rule = '';
		if ( '' !== $bg ) {
			$rule .= 'background:' . esc_html( $bg ) . ';';
		}
		if ( '' !== $text ) {
			$rule .= 'color:' . esc_html( $text ) . ';';
		}
		if ( '' !== $border ) {
			$rule .= 'border:1px solid ' . esc_html( $border ) . ';';
		}
		if ( '' !== $rule ) {
			$css .= '.' . gnn_button_style_class( $slot ) . '{' . $rule . '}';
		}
	}
	if ( '' !== $css ) {
		wp_add_inline_style( 'gnn-main', $css );
	}
}
add_action( 'wp_enqueue_scripts', 'gnn_button_styles_inline_css', 15 );
