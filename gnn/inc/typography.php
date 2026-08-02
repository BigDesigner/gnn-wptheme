<?php
/**
 * GNN Typography — optional per-element Google Fonts (GNN Panel → Typography).
 *
 * Off by default: the theme keeps its self-hosted Space Grotesk / Manrope
 * tokens (--font-heading / --font-body) with zero extra requests. When
 * enabled, only the fonts actually assigned to a heading level or body text
 * are requested, merged into a single Google Fonts stylesheet call.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Typography roles the panel can assign a Google Font to.
 *
 * @return string[] role => label.
 */
function gnn_typography_roles() {
	return array(
		'h1' => __( 'H1', 'gnn' ),
		'h2' => __( 'H2', 'gnn' ),
		'h3' => __( 'H3', 'gnn' ),
		'h4' => __( 'H4', 'gnn' ),
		'h5' => __( 'H5', 'gnn' ),
		'h6' => __( 'H6', 'gnn' ),
		'p'  => __( 'Paragraph (body text)', 'gnn' ),
	);
}

/**
 * Static weights the Google Fonts css2 endpoint accepts per family.
 *
 * @return string[]
 */
function gnn_typography_weights() {
	return array( '300', '400', '500', '600', '700', '800', '900' );
}

/**
 * Collect { family => { weight => true } } for every role with a Google
 * Font configured, when the feature is enabled. Multiple roles sharing the
 * same family are merged into one entry so the font is only requested once.
 *
 * @return array
 */
function gnn_typography_google_fonts() {
	if ( ! gnn_option( 'typography_google_enable' ) ) {
		return array();
	}
	$fonts = array();
	foreach ( array_keys( gnn_typography_roles() ) as $role ) {
		$family = trim( (string) gnn_option( "typo_{$role}_family" ) );
		if ( '' === $family ) {
			continue;
		}
		$weight = (string) gnn_option( "typo_{$role}_weight" );
		$weight = in_array( $weight, gnn_typography_weights(), true ) ? $weight : '400';
		if ( ! isset( $fonts[ $family ] ) ) {
			$fonts[ $family ] = array();
		}
		$fonts[ $family ][ $weight ] = true;
	}
	return $fonts;
}

/**
 * Build one combined Google Fonts css2 URL for every configured family and
 * weight (a single request, never one per font).
 *
 * @return string Empty string when nothing is configured.
 */
function gnn_typography_google_fonts_url() {
	$fonts = gnn_typography_google_fonts();
	if ( empty( $fonts ) ) {
		return '';
	}
	$parts = array();
	foreach ( $fonts as $family => $weights ) {
		// Google's css2 endpoint uses a literal "+" for spaces in family names.
		$parts[] = 'family=' . str_replace( ' ', '+', $family ) . ':wght@' . implode( ';', array_keys( $weights ) );
	}
	return 'https://fonts.googleapis.com/css2?' . implode( '&', $parts ) . '&display=swap';
}

/**
 * Enqueue the combined Google Fonts stylesheet (only when configured).
 */
function gnn_typography_assets() {
	$url = gnn_typography_google_fonts_url();
	if ( '' === $url ) {
		return;
	}
	wp_enqueue_style( 'gnn-typography-fonts', esc_url_raw( $url ), array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- external CDN URL; a ?ver= must not be appended.
}
add_action( 'wp_enqueue_scripts', 'gnn_typography_assets' );

/**
 * Inline CSS custom properties for configured roles. Any role left empty
 * falls back to the theme's own --font-heading / --font-body, so leaving
 * everything at its default costs nothing and changes nothing.
 */
function gnn_typography_inline_css() {
	if ( ! gnn_option( 'typography_google_enable' ) ) {
		return;
	}
	$css = '';
	foreach ( array_keys( gnn_typography_roles() ) as $role ) {
		$family = trim( (string) gnn_option( "typo_{$role}_family" ) );
		if ( '' === $family ) {
			continue;
		}
		$weight   = (string) gnn_option( "typo_{$role}_weight" );
		$weight   = in_array( $weight, gnn_typography_weights(), true ) ? $weight : '400';
		$fallback = 'p' === $role ? 'var(--font-body)' : 'var(--font-heading)';
		$css     .= '--font-' . $role . ":'" . esc_html( $family ) . "'," . $fallback . ';';
		$css     .= '--font-' . $role . '-weight:' . $weight . ';';
	}
	if ( '' !== $css ) {
		wp_add_inline_style( 'gnn-main', ':root{' . $css . '}' );
	}
}
add_action( 'wp_enqueue_scripts', 'gnn_typography_inline_css', 15 );
