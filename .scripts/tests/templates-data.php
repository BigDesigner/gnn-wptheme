<?php
/**
 * Real WordPress checks for data-driven template registry, validator, compiler and installer.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wp_set_current_user( 1 );

$checks = array();
$check  = static function ( $name, $pass, $detail = '' ) use ( &$checks ) {
	$checks[] = array(
		'name'   => $name,
		'pass'   => (bool) $pass,
		'detail' => $detail,
	);
};

$notices = array();
set_error_handler(
	static function ( $severity, $message, $file, $line ) use ( &$notices ) {
		$notices[] = "$message ($file:$line)";
		return true;
	}
);

try {
	$expected_slugs = array( 'restaurant', 'clinic', 'education', 'event', 'realestate', 'fitness', 'saas', 'nonprofit' );
	$templates      = gnn_templates();

	$check( 'Template loader registers all 8 bundled templates', empty( array_diff( $expected_slugs, array_keys( $templates ) ) ), implode( ', ', array_keys( $templates ) ) );
	$check( 'Single template lookup returns data and null for unknown', null !== gnn_template( 'restaurant' ) && null === gnn_template( 'missing-template' ) );

	// Validator: valid template passes, malformed templates are rejected and omitted cleanly.
	$valid_sample = gnn_template( 'restaurant' );
	$check( 'Validator accepts valid restaurant template', true === gnn_template_validate( $valid_sample, 'restaurant' ) );

	$broken_missing = $valid_sample;
	unset( $broken_missing['tokens'] );
	$check( 'Validator rejects missing required key', is_wp_error( gnn_template_validate( $broken_missing ) ) );

	$broken_color                     = $valid_sample;
	$broken_color['tokens']['accent'] = 'not-a-hex';
	$check( 'Validator rejects invalid hex color', is_wp_error( gnn_template_validate( $broken_color ) ) );

	$broken_font                   = $valid_sample;
	$broken_font['tokens']['font'] = 'roboto';
	$check( 'Validator rejects unsupported font', is_wp_error( gnn_template_validate( $broken_font ) ) );

	$broken_header                     = $valid_sample;
	$broken_header['header']['preset'] = 'unknown-preset';
	$check( 'Validator rejects invalid header preset', is_wp_error( gnn_template_validate( $broken_header ) ) );

	$broken_section                            = $valid_sample;
	$broken_section['pages']['home']['sections'][] = array( 'missing-section', 'split', array() );
	$check( 'Validator rejects unregistered section ID', is_wp_error( gnn_template_validate( $broken_section ) ) );

	$broken_variant                            = $valid_sample;
	$broken_variant['pages']['home']['sections'][0][1] = 'missing-variant';
	$check( 'Validator rejects unregistered section variant', is_wp_error( gnn_template_validate( $broken_variant ) ) );

	$broken_menu           = $valid_sample;
	$broken_menu['menu'][] = 'nonexistent-page';
	$check( 'Validator rejects menu entry missing from pages', is_wp_error( gnn_template_validate( $broken_menu ) ) );

	$broken_filter = static function ( $registry ) use ( $broken_font ) {
		$broken_font['slug']        = 'broken-qa';
		$registry['broken-qa']      = static function () use ( $broken_font ) {
			return $broken_font;
		};
		return $registry;
	};
	add_filter( 'gnn_template_registry', $broken_filter );
	$filtered_templates = gnn_templates();
	remove_filter( 'gnn_template_registry', $broken_filter );
	$check( 'Invalid template excluded from gnn_templates() without notices', ! isset( $filtered_templates['broken-qa'] ) && isset( $filtered_templates['restaurant'] ) );

	// Existing 4 profiles output in gnn_starter_profiles() remains identical.
	$with_filter = gnn_starter_profiles();
	remove_filter( 'gnn_starter_profiles', 'gnn_template_register_starter_profiles' );
	$without_filter = gnn_starter_profiles();
	add_filter( 'gnn_starter_profiles', 'gnn_template_register_starter_profiles' );
	$core_unchanged = 4 === count( $without_filter );
	foreach ( array( 'pro', 'commerce', 'design', 'architecture' ) as $core_slug ) {
		if ( ! isset( $with_filter[ $core_slug ], $without_filter[ $core_slug ] ) || $with_filter[ $core_slug ] !== $without_filter[ $core_slug ] ) {
			$core_unchanged = false;
		}
	}
	$check( 'Existing 4 starter profiles output unchanged', $core_unchanged );
	$check( 'Starter profiles include new templates', isset( $with_filter['restaurant'], $with_filter['saas'] ) );

	// Compiler: produces valid Elementor tree and resolves media: / page: references.
	$compiled = gnn_template_compile_page(
		$valid_sample,
		'home',
		array(
			'home'    => 1,
			'contact' => 2,
			'menu'    => 3,
			'about'   => 4,
		),
		array(
			'hero'    => 'https://example.invalid/hero.jpg',
			'kitchen' => 'https://example.invalid/kitchen.jpg',
		)
	);
	$compiled_json = wp_json_encode( $compiled );
	$check( 'Compiler builds Elementor elements with resolved media/page tokens', ! empty( $compiled ) && false !== $compiled_json && false === strpos( $compiled_json, 'media:' ) && false === strpos( $compiled_json, 'page:' ) );

	// Requires check blocks missing plugin dependencies cleanly.
	$req_filter = static function ( $registry ) use ( $valid_sample ) {
		$custom             = $valid_sample;
		$custom['slug']     = 'woo-qa';
		$custom['requires'] = class_exists( 'WooCommerce' ) ? array( 'elementor', 'missing-plugin' ) : array( 'elementor', 'woocommerce' );
		$registry['woo-qa'] = static function () use ( $custom ) {
			return $custom;
		};
		return $registry;
	};
	add_filter( 'gnn_template_registry', $req_filter );
	$req_tpl = gnn_template( 'woo-qa' );
	$req_err = $req_tpl ? gnn_tpl_import( 'woo-qa', gnn_configuration_revision() ) : new WP_Error( 'missing', 'missing' );
	remove_filter( 'gnn_template_registry', $req_filter );
	$check( 'Template import rejects unmet plugin requirements gracefully', is_array( $req_tpl ) && ! gnn_template_requires_met( $req_tpl ) && is_wp_error( $req_err ) );

	// Full import with Elementor active for 2 templates (light + dark).
	$check( 'Elementor active for full template import checks', gnn_starter_elementor_available() );
	foreach ( array( 'restaurant', 'saas' ) as $slug ) {
		delete_option( 'gnn_demo_' . $slug . '_complete' );
		$tpl = gnn_template( $slug );
		$res = gnn_tpl_import( $slug, gnn_configuration_revision() );
		$ok  = is_array( $res ) && ! empty( $res['pages']['home'] ) && ! empty( $res['menu'] );
		$check( "$slug: full import succeeds", $ok, is_wp_error( $res ) ? $res->get_error_message() : '' );
		if ( ! $ok ) {
			continue;
		}
		$home_id   = (int) $res['home'];
		$elem_data = (string) get_post_meta( $home_id, '_elementor_data', true );
		$check( "$slug: home assigned as front page with starter meta and Elementor data", 'page' === get_option( 'show_on_front' ) && (int) get_option( 'page_on_front' ) === $home_id && ( $slug . ':elementor:home' ) === get_post_meta( $home_id, '_gnn_starter', true ) && false !== strpos( $elem_data, 'gnn-sec' ) );
		$check( "$slug: preset options and body class applied", $slug === gnn_option( 'site_segment' ) && $tpl['header']['preset'] === gnn_option( 'header_preset' ) && $tpl['tokens']['font'] === gnn_option( 'body_font' ) && $tpl['tokens']['accent'] === get_theme_mod( 'gnn_accent_color' ) && in_array( 'gnn-site-' . $slug, apply_filters( 'body_class', array() ), true ) );
		$check( "$slug: nav menu populated and assigned", ! empty( wp_get_nav_menu_items( $res['menu'] ) ) && (int) ( get_theme_mod( 'nav_menu_locations' )['primary'] ?? 0 ) === (int) $res['menu'] );

		// Missing images must not stop installation and should report "eksik görsel" + use placeholder SVG.
		$missing_reported = ! empty( $res['missing_media'] ) && in_array( 'eksik görsel', (array) ( $res['notices'] ?? array() ), true ) && false !== strpos( $elem_data, 'placeholder-cover.svg' );
		$check( "$slug: missing media falls back to placeholder-cover.svg and reports eksik görsel", $missing_reported || empty( $res['missing_media'] ) );

		// Idempotency and edited-record preservation.
		$repeat = gnn_tpl_import( $slug, gnn_configuration_revision() );
		$check( "$slug: repeat import returns stored completion record", $repeat === $res );

		$about_id = (int) ( $res['pages']['about'] ?? 0 );
		if ( $about_id ) {
			wp_update_post(
				array(
					'ID'         => $about_id,
					'post_title' => 'Customer Edited About',
				)
			);
			delete_option( 'gnn_demo_' . $slug . '_complete' );
			$reimport = gnn_tpl_import( $slug, gnn_configuration_revision() );
			$check( "$slug: re-import preserves user-edited page", ! is_wp_error( $reimport ) && 'Customer Edited About' === get_the_title( $about_id ) );
		}
	}

	$check( 'Template data suite emits no PHP notices', empty( $notices ), implode( '; ', $notices ) );
} catch ( Throwable $error ) {
	$check( 'Template data suite completed', false, $error->getMessage() );
} finally {
	restore_error_handler();
}

echo 'GNN_QA_RESULT:' . wp_json_encode( $checks );
