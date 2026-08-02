<?php
/**
 * Elementor (Free + Pro) integration. Loaded only when Elementor is active —
 * zero overhead otherwise.
 *
 * Free: full editing of the content area; the theme's tokens are bridged so
 * Elementor widgets inherit the design (colors, fonts) out of the box.
 * Pro: header / footer / single / archive Theme Builder locations — build a
 * template there and it replaces the theme part; leave it empty and the
 * theme's own part renders.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register all core Theme Builder locations (header, footer, single, archive).
 *
 * @param object $manager Elementor locations manager.
 */
function gnn_register_elementor_locations( $manager ) {
	$manager->register_all_core_location();
}
add_action( 'elementor/theme/register_locations', 'gnn_register_elementor_locations' );

/**
 * First activation next to Elementor: align its defaults with the theme —
 * container width 1280, "inherit theme colors/fonts" (disable Elementor's
 * own kits) so widgets pick up the GNN tokens, and CSS printed inline
 * rather than to an external file (many hosts restrict writes to
 * wp-content/uploads/elementor/css/, which silently drops backgrounds/
 * icons/borders while widget content still renders — inline sidesteps
 * that class of hosting issue entirely). Only touches unset options, so
 * an intentional External File choice is never overridden.
 */
function gnn_elementor_defaults() {
	if ( ! get_option( 'elementor_container_width' ) ) {
		update_option( 'elementor_container_width', 1280 );
	}
	if ( false === get_option( 'elementor_disable_color_schemes', false ) ) {
		update_option( 'elementor_disable_color_schemes', 'yes' );
	}
	if ( false === get_option( 'elementor_disable_typography_schemes', false ) ) {
		update_option( 'elementor_disable_typography_schemes', 'yes' );
	}
	if ( ! get_option( 'elementor_css_print_method' ) ) {
		update_option( 'elementor_css_print_method', 'internal' );
	}
}
add_action( 'after_switch_theme', 'gnn_elementor_defaults' );
// Also runs on admin_init (not just theme activation) so sites already
// running an older copy of the theme get these defaults too, without
// needing to reactivate. Cheap: every check is a single guarded option
// read/write that becomes a no-op after the first successful run.
add_action( 'admin_init', 'gnn_elementor_defaults' );

/**
 * Add a "Hide Breadcrumb" toggle to Elementor's native Page Settings panel,
 * in its own small "GNN Theme" section. Breadcrumb has no Elementor
 * equivalent to sync with (unlike title), so this registers a new document
 * control instead; Elementor stores it in the same `_elementor_page_settings`
 * postmeta array, which gnn_hide_breadcrumb() (inc/page-meta.php) reads.
 *
 * @param \Elementor\Core\Base\Document $document Document being loaded.
 */
function gnn_elementor_register_breadcrumb_control( $document ) {
	if ( ! is_a( $document, '\Elementor\Core\DocumentTypes\PageBase' ) ) {
		return;
	}

	$document->start_controls_section(
		'gnn_page_settings_section',
		array(
			'label' => __( 'GNN Theme', 'gnn' ),
			'tab'   => \Elementor\Controls_Manager::TAB_SETTINGS,
		)
	);

	$document->add_control(
		'gnn_hide_breadcrumb',
		array(
			'label'   => __( 'Hide Breadcrumb', 'gnn' ),
			'type'    => \Elementor\Controls_Manager::SWITCHER,
			'default' => '',
		)
	);

	$document->end_controls_section();
}
add_action( 'elementor/documents/register_controls', 'gnn_elementor_register_breadcrumb_control' );

/**
 * Bridge the theme's CSS custom properties into Elementor content so
 * dark/light mode works inside Elementor-built sections too.
 */
function gnn_elementor_bridge_css() {
	$css = '.elementor-widget-container, .elementor-section, .e-con {' .
		'--e-global-color-primary: var(--accent);' .
		'--e-global-color-text: var(--fg);' .
		'}' .
		'.elementor a:not(.elementor-button):not(.gnn-btn) { color: var(--accent); }';
	wp_add_inline_style( 'gnn-main', $css );
}
add_action( 'wp_enqueue_scripts', 'gnn_elementor_bridge_css', 20 );
