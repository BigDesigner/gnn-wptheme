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
 */
function gnn_customize_preview_js() {
	wp_add_inline_script(
		'customize-preview',
		"wp.customize('gnn_accent_color',function(v){v.bind(function(c){document.documentElement.style.setProperty('--accent',c);});});"
	);
}
add_action( 'customize_preview_init', 'gnn_customize_preview_js' );
