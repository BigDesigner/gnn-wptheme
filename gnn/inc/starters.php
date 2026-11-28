<?php
/**
 * Original, native-block starter profiles. Existing pages are never overwritten.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

/** Small, distinct profiles rather than copied commercial demo sites. */
function gnn_starter_profiles() {
	$profiles = array(
		'architecture' => array(
			'label'        => __( 'GNNarchitecture', 'gnn' ),
			'accent'       => '#855c40',
			'header'       => 'minimal',
			'radius'       => 2,
			'font'         => 'manrope',
			'heading_font' => 'space',
			'options'      => array(
				'header_structure' => 'classic',
				'header_surface'   => 'solid',
				'header_shape'     => 'full',
				'header_width'     => 'wide',
				'header_behavior'  => 'sticky',
				'header_overlay'   => 0,
				'header_height'    => 84,
			),
			'heading'      => __( 'Space. Light. Life.', 'gnn' ),
			'preview'      => 'assets/images/demo/architecture/image-18.jpg',
			'description'  => __( 'Architecture and construction with a live two-column portfolio, gallery, studio and services.', 'gnn' ),
			'featured'     => false,
			'order'        => 230,
		),
	);
	if ( function_exists( 'gnn_demo_registry' ) ) {
		foreach ( gnn_demo_registry() as $key => $demo ) {
			if ( isset( $profiles[ $key ] ) ) {
				continue;
			}
			$profiles[ $key ] = array_merge(
				$demo['profile'],
				array(
					'label'       => $demo['label'],
					'description' => $demo['description'],
					'featured'    => $demo['featured'],
					'order'       => $demo['order'],
					'preview'     => $demo['preview'],
				)
			);
		}
	}
	uksort(
		$profiles,
		static function ( $a, $b ) use ( $profiles ) {
			$order_a = (int) ( $profiles[ $a ]['order'] ?? 100 );
			$order_b = (int) ( $profiles[ $b ]['order'] ?? 100 );
			if ( $order_a !== $order_b ) {
				return $order_a <=> $order_b;
			}
			return strcmp( (string) $a, (string) $b );
		}
	);
	return apply_filters( 'gnn_starter_profiles', $profiles );
}

/**
 * Resolve a shipped design key. Older and unrecognised values use the base theme.
 *
 * @param string $name Stored profile key.
 */
function gnn_starter_profile_key( $name ) {
	return 'architecture' === $name || gnn_dp_has( $name ) ? $name : '';
}

/**
 * Presets alter design only; customer identity, code, media and user content survive.
 *
 * @param string $name Profile identifier.
 * @param bool   $reset Reset presentation defaults before applying the new profile.
 */
function gnn_apply_design_preset( $name, $reset = false ) {
	$name     = gnn_starter_profile_key( $name );
	$profiles = gnn_starter_profiles();
	if ( ! current_user_can( 'manage_options' ) || ! isset( $profiles[ $name ] ) ) {
		return new WP_Error( 'gnn_preset', __( 'This profile is not available.', 'gnn' ) ); }
	$started = gnn_configuration_begin( 'preset', gnn_configuration_form_revision() );
	if ( is_wp_error( $started ) ) {
		return $started; }
	$profile = $profiles[ $name ];
	$options = wp_parse_args( (array) get_option( 'gnn_options', array() ), gnn_option_defaults() );
	if ( $reset ) {
		$defaults = gnn_option_defaults();
		foreach ( gnn_design_reset_keys() as $key ) {
			$options[ $key ] = $defaults[ $key ];
		}
	}
	$light   = $profile['light'] ?? array( '#fafafa', '#f0f1f3', '#e5e7eb', '#18212b', '#515d69', '#d4d9df' );
	$dark    = $profile['dark'] ?? null;
	$options = array_replace(
		$options,
		array(
			'site_segment'        => $name,
			'header_preset'       => $profile['header'],
			'footer_preset'       => 'balanced',
			'design_radius'       => $profile['radius'],
			'button_radius'       => $profile['radius'],
			'body_font'           => $profile['font'],
			'heading_font'        => $profile['heading_font'] ?? $profile['font'],
			'hero_style'          => 'none',
			'container_width'     => 1440,
			'heading_size'        => 'architecture' === $name ? 100 : 80,
			'heading_mobile_size' => 42,
			'accent_light'        => $profile['accent_light'] ?? $profile['accent'],
			'accent_ink_light'    => '#ffffff',
			'color_bg_light'      => $light[0],
			'color_bg2_light'     => $light[1],
			'color_bg3_light'     => $light[2],
			'color_fg_light'      => $light[3],
			'color_fg2_light'     => $light[4],
			'color_line_light'    => $light[5],
		)
	);
	if ( $dark ) {
		$options = array_replace(
			$options,
			array(
				'accent_ink_dark' => $profile['accent_ink'] ?? '#06150f',
				'color_bg_dark'   => $dark[0],
				'color_bg2_dark'  => $dark[1],
				'color_bg3_dark'  => $dark[2],
				'color_fg_dark'   => $dark[3],
				'color_fg2_dark'  => $dark[4],
				'color_line_dark' => $dark[5],
			)
		);
	}
	if ( ! empty( $profile['accent_ink'] ) ) {
		$options['accent_ink_light'] = '#ffffff';
	}
	// Header-related design options start from schema defaults, so switching designs leaves no leftovers.
	$defaults          = gnn_option_defaults();
	$header_reset_keys = array_unique(
		array_merge(
			array(
				'header_preset',
				'header_layout',
				'header_menu_align',
				'header_height',
				'sticky_header',
				'show_search',
				'show_toggle',
				'show_cart',
				'header_search_bar',
				'header_cta_show',
				'header_cta_label',
				'header_cta_url',
				'header_contact',
				'header_language',
				'header_bg_dark',
				'header_bg_light',
				'logo_overlay',
				'topbar_enable',
				'topbar_text',
				'topbar_bg',
				'topbar_text_color',
				'topbar_mobile',
				'mobile_dock',
				'mobile_actions',
				'brand_dot',
			),
			function_exists( 'gnn_header_schema' ) ? array_keys( gnn_header_schema() ) : array()
		)
	);
	$header_defaults   = array( 'portfolio_columns' => 2 );
	foreach ( $header_reset_keys as $key ) {
		if ( array_key_exists( $key, $defaults ) ) {
			$header_defaults[ $key ] = $defaults[ $key ];
		}
	}
	if ( ! empty( $profile['header'] ) ) {
		$header_defaults['header_preset'] = $profile['header'];
	}
	$options = array_replace(
		$options,
		$header_defaults,
		$profile['options'] ?? array()
	);
	update_option( 'gnn_options', $options );
	set_theme_mod( 'gnn_default_theme', $profile['mode'] ?? 'light' );
	set_theme_mod( 'gnn_accent_color', $profile['accent'] );
	return true;
}

/**
 * Portable native paragraphs/headings, not opaque HTML-only sections.
 *
 * @param string $heading Section heading.
 * @param string $text Paragraph text.
 * @param string $url Button URL.
 * @param string $label Control label.
 */
function gnn_starter_content( $heading, $text, $url = '', $label = '' ) {
	$blocks  = array(
		array(
			'blockName'    => 'core/heading',
			'attrs'        => array( 'level' => 2 ),
			'innerBlocks'  => array(),
			'innerHTML'    => '<h2 class="wp-block-heading">' . esc_html( $heading ) . '</h2>',
			'innerContent' => array( '<h2 class="wp-block-heading">' . esc_html( $heading ) . '</h2>' ),
		),
		array(
			'blockName'    => 'core/paragraph',
			'attrs'        => array(),
			'innerBlocks'  => array(),
			'innerHTML'    => '<p>' . esc_html( $text ) . '</p>',
			'innerContent' => array( '<p>' . esc_html( $text ) . '</p>' ),
		),
	);
	$content = serialize_blocks( $blocks );
	if ( $url && $label ) {
		$content .= '<!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></div><!-- /wp:button --></div><!-- /wp:buttons -->'; }
	return $content;
}

/**
 * Idempotent starter installation; menu, homepage and existing slugs are retained.
 *
 * @param string $name Profile identifier.
 * @param string $editor Gutenberg or Elementor edition.
 */
function gnn_install_starter( $name, $editor = 'gutenberg' ) {
	$name     = gnn_starter_profile_key( $name );
	$profiles = gnn_starter_profiles();
	if ( ! current_user_can( 'manage_options' ) || ! isset( $profiles[ $name ] ) ) {
		return new WP_Error( 'gnn_starter', __( 'Invalid starter profile.', 'gnn' ) ); }
	if ( ! in_array( $editor, array( 'gutenberg', 'elementor' ), true ) ) {
		return new WP_Error( 'gnn_starter_editor', __( 'Choose Gutenberg or Elementor.', 'gnn' ) );
	}
	if ( 'elementor' === $editor && ! gnn_starter_elementor_available() ) {
		return new WP_Error( 'gnn_starter_elementor', __( 'Activate Elementor 3.6 or newer before installing this edition.', 'gnn' ) );
	}
	$started = gnn_configuration_begin( 'starter', gnn_configuration_form_revision() );
	if ( is_wp_error( $started ) ) {
		return $started; }
	if ( 'architecture' === $name && 'gutenberg' === $editor ) {
		return gnn_install_architecture_starter(); }
	return gnn_install_profile_pages( $name, $editor );
}

/** Register editable alternatives without changing previously inserted legacy sections. */
function gnn_register_portable_patterns() {
	if ( ! function_exists( 'register_block_pattern' ) ) {
		return; }
	foreach ( array(
		'services'       => __( 'Services overview', 'gnn' ),
		'team'           => __( 'Team introduction', 'gnn' ),
		'case-study'     => __( 'Project overview', 'gnn' ),
		'contact'        => __( 'Contact information', 'gnn' ),
		'call-to-action' => __( 'Call to action', 'gnn' ),
		'trust'          => __( 'Experience and credentials', 'gnn' ),
	) as $slug => $label ) {
		register_block_pattern(
			'gnn/portable-' . $slug,
			array(
				'title'      => $label,
				'categories' => array( 'gnn' ),
				'content'    => gnn_starter_content( $label, __( 'Add your own verified information here.', 'gnn' ) ),
			)
		);
	}
}
gnn_add_block_patterns( 'gnn_register_portable_patterns', 20 );
