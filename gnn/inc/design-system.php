<?php
/**
 * One design contract for frontend, block editor and builder content.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

/**
 * Resolve a local font stack without requesting a third-party stylesheet.
 *
 * @param string $role Typography role.
 */
function gnn_local_font_stack( $role ) {
	$fonts = array(
		'manrope' => "'Manrope',Arial,sans-serif",
		'space'   => "'Space Grotesk',Arial,sans-serif",
		'system'  => 'system-ui,-apple-system,sans-serif',
		'custom'  => "'GNN Local',system-ui,sans-serif",
	);
	return $fonts[ gnn_option( $role . '_font' ) ] ?? $fonts['system'];
}

/**
 * Relative sRGB luminance used for readable derived brand colors.
 *
 * @param string $hex Valid hexadecimal color.
 * @return float
 */
function gnn_color_luminance( $hex ) {
	$hex = ltrim( $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	$sum = 0;
	foreach ( array( 0.2126, 0.7152, 0.0722 ) as $i => $weight ) {
		$channel = hexdec( substr( $hex, $i * 2, 2 ) ) / 255;
		$sum    += $weight * ( $channel <= 0.04045 ? $channel / 12.92 : pow( ( $channel + 0.055 ) / 1.055, 2.4 ) );
	}
	return $sum;
}

/**
 * Derive link text with at least 4.5:1 contrast against its actual surface.
 *
 * @param string $color Valid brand color.
 * @param string $background Valid surface color.
 * @return string
 */
function gnn_readable_brand_color( $color, $background ) {
	$bg     = gnn_color_luminance( $background );
	$target = $bg < 0.179 ? '#ffffff' : '#000000';
	$start  = ltrim( $color, '#' );
	if ( 3 === strlen( $start ) ) {
		$start = $start[0] . $start[0] . $start[1] . $start[1] . $start[2] . $start[2];
	}
	for ( $step = 0; $step <= 20; $step++ ) {
		$parts = array();
		for ( $i = 0; $i < 3; $i++ ) {
			$from    = hexdec( substr( $start, $i * 2, 2 ) );
			$to      = '#ffffff' === $target ? 255 : 0;
			$parts[] = (int) round( $from + ( $to - $from ) * $step / 20 );
		}
		$candidate = vsprintf( '#%02x%02x%02x', $parts );
		$luminance = gnn_color_luminance( $candidate );
		if ( ( max( $bg, $luminance ) + 0.05 ) / ( min( $bg, $luminance ) + 0.05 ) >= 4.5 ) {
			return $candidate;
		}
	}
	return $target;
}

/**
 * Preserve explicit ink choices; derive readable defaults for any brand palette.
 *
 * @param string $mode Dark or light palette.
 * @return string
 */
function gnn_brand_design_tokens( $mode ) {
	$accent     = sanitize_hex_color( get_theme_mod( 'gnn_accent_color', '#34d399' ) );
	$light      = sanitize_hex_color( (string) gnn_option( 'accent_light' ) );
	$accent     = 'light' === $mode && $light ? $light : $accent;
	$accent     = $accent ? $accent : '#34d399';
	$background = sanitize_hex_color( (string) gnn_option( 'color_bg_' . $mode ) );
	$background = $background ? $background : ( 'light' === $mode ? '#fafafa' : '#0a0a0b' );
	$ink        = sanitize_hex_color( (string) gnn_option( 'accent_ink_' . $mode ) );
	if ( ! $ink ) {
		$ink = gnn_color_luminance( $accent ) > 0.179 ? '#000000' : '#ffffff';
	}
	return '--accent:' . $accent . ';--accent-ink:' . $ink . ';--accent-text:' . gnn_readable_brand_color( $accent, $background ) . ';';
}

/**
 * The same variables are emitted into both editing and viewing contexts.
 *
 * @param string $scope Export scope or CSS selector.
 */
function gnn_design_css( $scope = ':root' ) {
	$css = '--font-body:' . gnn_local_font_stack( 'body' ) . ';--font-heading:' . gnn_local_font_stack( 'heading' ) . ';';
	foreach ( array(
		'container_width'     => 'container',
		'full_width'          => 'full-width',
		'header_height'       => 'header-height',
		'body_size'           => 'body-size',
		'heading_size'        => 'heading-size',
		'heading_mobile_size' => 'heading-mobile-size',
		'design_radius'       => 'radius',
		'button_radius'       => 'button-radius',
		'button_padding'      => 'button-padding',
		'design_spacing'      => 'section-space',
	) as $key => $name ) {
		$css .= '--gnn-' . $name . ':' . (int) gnn_option( $key ) . 'px;';
	}
	$css    .= '--gnn-body-leading:' . ( (int) gnn_option( 'body_line_height' ) / 100 ) . ';';
	$css    .= '--gnn-heading-leading:' . ( (int) gnn_option( 'heading_line_height' ) / 100 ) . ';';
	$css    .= '--gnn-heading-tracking:' . ( (int) gnn_option( 'heading_tracking' ) / 100 ) . 'em;';
	$shadows = array(
		'none'   => 'none',
		'soft'   => '0 8px 30px rgb(0 0 0 / 8%)',
		'strong' => '0 16px 48px rgb(0 0 0 / 18%)',
	);
	$css    .= '--gnn-shadow:' . ( $shadows[ gnn_option( 'design_shadow' ) ] ?? 'none' ) . ';';
	$css    .= '--gnn-blog-columns:' . (int) gnn_option( 'blog_columns' ) . ';--gnn-footer-columns:' . (int) gnn_option( 'footer_columns' ) . ';';
	$out     = $scope . '{' . $css . '}';
	$id      = (int) gnn_option( 'local_font' );
	if ( $id && 'font/woff2' === get_post_mime_type( $id ) ) {
		$url = wp_get_attachment_url( $id );
		if ( $url ) {
			$out .= '@font-face{font-family:"GNN Local";font-display:swap;src:url(' . wp_json_encode( esc_url_raw( $url ) ) . ') format("woff2");}'; }
	}
	return $out;
}

/** Native and custom headers share the configured geometry. */
function gnn_enqueue_design_system() {
	wp_enqueue_style( 'gnn-design-system', get_template_directory_uri() . '/assets/css/design-system.css', array( 'gnn-main' ), GNN_VERSION );
	wp_add_inline_style( 'gnn-design-system', gnn_design_css() . ':root{' . gnn_brand_design_tokens( 'dark' ) . '}[data-theme="light"]{' . gnn_brand_design_tokens( 'light' ) . '}' );
	wp_enqueue_script( 'gnn-layout-geometry', get_template_directory_uri() . '/assets/js/layout-geometry.js', array(), GNN_VERSION, true );
	$styles = wp_styles();
	if ( isset( $styles->registered['gnn-style'] ) ) {
		$styles->registered['gnn-style']->deps = array( 'gnn-design-system' ); }
}
add_action( 'wp_enqueue_scripts', 'gnn_enqueue_design_system', 25 );

/** Editor styles receive the same colors and fonts, not frozen demo values. */
function gnn_editor_design_system() {
	wp_enqueue_style( 'gnn-design-editor', get_template_directory_uri() . '/assets/css/design-system.css', array(), GNN_VERSION );
	$mode     = get_theme_mod( 'gnn_default_theme', 'dark' );
	$defaults = 'light' === $mode ? array(
		'bg'   => '#fafafa',
		'bg2'  => '#ffffff',
		'bg3'  => '#efeff1',
		'fg'   => '#101013',
		'fg2'  => '#5c5c64',
		'line' => '#e5e5e9',
	) : array(
		'bg'   => '#0a0a0b',
		'bg2'  => '#121214',
		'bg3'  => '#1b1b1f',
		'fg'   => '#f5f5f4',
		'fg2'  => '#9d9da4',
		'line' => '#232328',
	);
	$vars     = '';
	foreach ( $defaults as $key => $fallback ) {
		$color = sanitize_hex_color( (string) gnn_option( 'color_' . $key . '_' . $mode ) );
		$vars .= '--' . $key . ':' . ( $color ? $color : $fallback ) . ';'; }
	$vars .= gnn_brand_design_tokens( $mode );
	wp_add_inline_style( 'gnn-design-editor', gnn_design_css( '.editor-styles-wrapper' ) . '.editor-styles-wrapper{' . $vars . '}' );
}
add_action( 'enqueue_block_editor_assets', 'gnn_editor_design_system' );

/**
 * Keep the editor palette and width synchronized while retaining custom colors.
 *
 * @param WP_Theme_JSON_Data $theme_json Current editor configuration.
 */
function gnn_sync_design_theme_json( $theme_json ) {
	$data                                 = $theme_json->get_data();
	$map                                  = array(
		'ink'        => 'color_bg_dark',
		'surface'    => 'color_bg2_dark',
		'paper'      => 'color_bg_light',
		'foreground' => 'color_fg_dark',
		'muted'      => 'color_fg2_dark',
	);
	$palette                              = function ( $entries ) use ( &$palette, $map ) {
		foreach ( $entries as $i => $entry ) {
			if ( ! is_array( $entry ) ) {
				continue;
			}
			if ( isset( $entry['slug'], $map[ $entry['slug'] ] ) ) {
				$color = sanitize_hex_color( (string) gnn_option( $map[ $entry['slug'] ] ) );
				if ( $color ) {
					$entry['color'] = $color;
				}
			} else {
				$entry = $palette( $entry );
			}
			$entries[ $i ] = $entry;
		}
		return $entries;
	};
	$data['settings']['color']['palette'] = $palette( $data['settings']['color']['palette'] ?? array() );
	$data['settings']['layout']           = array(
		'contentSize' => (int) gnn_option( 'container_width' ) . 'px',
		'wideSize'    => (int) gnn_option( 'full_width' ) . 'px',
	);
	$data['settings']['typography']['fontFamilies'] = array(
		array(
			'slug'       => 'body',
			'name'       => __( 'Body', 'gnn' ),
			'fontFamily' => gnn_local_font_stack( 'body' ),
		),
		array(
			'slug'       => 'heading',
			'name'       => __( 'Heading', 'gnn' ),
			'fontFamily' => gnn_local_font_stack( 'heading' ),
		),
	);
	return $theme_json->update_with( $data );
}
add_filter( 'wp_theme_json_data_theme', 'gnn_sync_design_theme_json', 30 );

/**
 * Informative layout classes do not encode client identity.
 *
 * @param array $classes Current body classes.
 */
function gnn_platform_body_classes( $classes ) {
	$classes[] = 'gnn-header-' . sanitize_html_class( gnn_layout_value( 'header_preset' ) );
	$classes[] = 'gnn-footer-' . sanitize_html_class( gnn_option( 'footer_preset' ) );
	$classes[] = 'gnn-hero-style-' . sanitize_html_class( gnn_layout_value( 'hero_style' ) );
	return $classes;
}
add_filter( 'body_class', 'gnn_platform_body_classes' );

/** Make semantic button variants discoverable in the block editor. */
function gnn_register_button_variants() {
	$styles = array(
		'primary'   => __( 'Primary', 'gnn' ),
		'secondary' => __( 'Secondary', 'gnn' ),
		'outline'   => __( 'Outline', 'gnn' ),
		'text'      => __( 'Text', 'gnn' ),
	);
	foreach ( $styles as $name => $label ) {
		register_block_style(
			'core/button',
			array(
				'name'  => 'gnn-' . $name,
				'label' => $label,
			)
		);
	}
}
add_action( 'init', 'gnn_register_button_variants' );
