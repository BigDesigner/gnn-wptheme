<?php
/**
 * GNN Theme Customizer: accent color, default theme mode, hero slider.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitize the theme-mode choice.
 *
 * @param string $value Raw value.
 * @return string
 */
function gnn_sanitize_theme_mode( $value ) {
	return in_array( $value, array( 'dark', 'light' ), true ) ? $value : 'dark';
}

/**
 * Register Customizer settings and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function gnn_customize_register( $wp_customize ) {

	$wp_customize->add_panel(
		'gnn_theme_options',
		array(
			'title'    => __( 'GNN Theme Options', 'gnn' ),
			'priority' => 30,
		)
	);

	/* --- Brand ------------------------------------------------------- */
	$wp_customize->add_section(
		'gnn_brand',
		array(
			'title' => __( 'Brand', 'gnn' ),
			'panel' => 'gnn_theme_options',
		)
	);

	$wp_customize->add_setting(
		'gnn_accent_color',
		array(
			'default'           => '#34d399',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'gnn_accent_color',
			array(
				'label'       => __( 'Accent color', 'gnn' ),
				'description' => __( 'Suggested: #34d399, #22d3ee, #fb923c, #a78bfa', 'gnn' ),
				'section'     => 'gnn_brand',
			)
		)
	);

	$wp_customize->add_setting(
		'gnn_footer_legal',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'gnn_footer_legal',
		array(
			'label'   => __( 'Footer legal text', 'gnn' ),
			'section' => 'gnn_brand',
			'type'    => 'text',
		)
	);

	/* --- Theme mode --------------------------------------------------- */
	$wp_customize->add_section(
		'gnn_theme_mode',
		array(
			'title' => __( 'Theme Mode', 'gnn' ),
			'panel' => 'gnn_theme_options',
		)
	);

	$wp_customize->add_setting(
		'gnn_default_theme',
		array(
			'default'           => 'dark',
			'sanitize_callback' => 'gnn_sanitize_theme_mode',
		)
	);
	$wp_customize->add_control(
		'gnn_default_theme',
		array(
			'label'       => __( 'Default mode', 'gnn' ),
			'description' => __( 'Visitors can still toggle; their choice persists in the browser.', 'gnn' ),
			'section'     => 'gnn_theme_mode',
			'type'        => 'radio',
			'choices'     => array(
				'dark'  => __( 'Dark', 'gnn' ),
				'light' => __( 'Light', 'gnn' ),
			),
		)
	);

	// The hero slider is managed in its own screen (WP Admin → GNN Slider),
	// so it is intentionally not part of the Customizer.
}
add_action( 'customize_register', 'gnn_customize_register' );

/**
 * Customizer live-preview JS for postMessage settings.
 *
 * Mirrors gnn_brand_design_tokens(): the accent setting is the dark value
 * and the light default (accent_light overrides light), and --accent-ink /
 * --accent-text are re-derived per mode with the same rules. The result goes
 * into one <style> after the theme's own tokens instead of an inline style on
 * <html>, so the light palette and the mode toggle keep working.
 */
function gnn_customize_preview_js() {
	$config = array();
	foreach ( array( 'dark', 'light' ) as $mode ) {
		$background      = sanitize_hex_color( (string) gnn_option( 'color_bg_' . $mode ) );
		$config[ $mode ] = array(
			'accent' => 'light' === $mode ? (string) sanitize_hex_color( (string) gnn_option( 'accent_light' ) ) : '',
			'ink'    => (string) sanitize_hex_color( (string) gnn_option( 'accent_ink_' . $mode ) ),
			'bg'     => $background ? $background : ( 'light' === $mode ? '#fafafa' : '#0a0a0b' ),
		);
	}
	$script = <<<'JS'
(function (config) {
	function hex(value) {
		value = String(value || '').replace('#', '');
		if (value.length === 3) { value = value[0] + value[0] + value[1] + value[1] + value[2] + value[2]; }
		return /^[0-9a-f]{6}$/i.test(value) ? value.toLowerCase() : '';
	}
	function luminance(value) {
		var weights = [0.2126, 0.7152, 0.0722], sum = 0, i, channel;
		for (i = 0; i < 3; i++) {
			channel = parseInt(value.substr(i * 2, 2), 16) / 255;
			sum += weights[i] * (channel <= 0.04045 ? channel / 12.92 : Math.pow((channel + 0.055) / 1.055, 2.4));
		}
		return sum;
	}
	function contrast(a, b) {
		var x = luminance(a), y = luminance(b);
		return (Math.max(x, y) + 0.05) / (Math.min(x, y) + 0.05);
	}
	function readable(color, background) {
		var bg = luminance(background), to = bg < 0.179 ? 255 : 0, step, i, parts, candidate;
		for (step = 0; step <= 20; step++) {
			parts = [];
			for (i = 0; i < 3; i++) {
				var from = parseInt(color.substr(i * 2, 2), 16);
				parts.push(('0' + Math.round(from + (to - from) * step / 20).toString(16)).slice(-2));
			}
			candidate = parts.join('');
			if (contrast(candidate, background) >= 4.5) { return '#' + candidate; }
		}
		return to ? '#ffffff' : '#000000';
	}
	function tokens(accent, mode) {
		var own = hex(config[mode].accent), ink = hex(config[mode].ink), bg = hex(config[mode].bg);
		accent = own || accent;
		ink = ink || (contrast(accent, '0a0a0a') >= contrast(accent, 'ffffff') ? '0a0a0a' : 'ffffff');
		return '--accent:#' + accent + ';--accent-ink:#' + ink + ';--accent-text:' + readable(accent, bg) + ';';
	}
	wp.customize('gnn_accent_color', function (setting) {
		setting.bind(function (value) {
			var accent = hex(value) || '34d399', style = document.getElementById('gnn-accent-preview');
			if (!style) {
				style = document.createElement('style');
				style.id = 'gnn-accent-preview';
				document.head.appendChild(style);
			}
			style.textContent = ':root{' + tokens(accent, 'dark') + '}[data-theme="light"]{' + tokens(accent, 'light') + '}';
		});
	});
}(%s));
JS;
	wp_add_inline_script( 'customize-preview', sprintf( $script, wp_json_encode( $config ) ) );
}
add_action( 'customize_preview_init', 'gnn_customize_preview_js' );
