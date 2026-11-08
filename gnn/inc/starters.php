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
	return array(
		'pro'          => array(
			'label'       => __( 'GNNpro', 'gnn' ),
			'accent'      => '#24415c',
			'header'      => 'classic',
			'radius'      => 2,
			'font'        => 'manrope',
			'heading'     => __( 'Expertise that moves you forward.', 'gnn' ),
			'description' => __( 'A complete business website for companies, teams and professional services.', 'gnn' ),
		),
		'commerce'     => array(
			'label'       => __( 'GNNcommerce', 'gnn' ),
			'accent'      => '#12644a',
			'header'      => 'classic',
			'radius'      => 8,
			'font'        => 'manrope',
			'heading'     => __( 'Considered products. Everyday possibilities.', 'gnn' ),
			'description' => __( 'A WooCommerce storefront with live products, categories, cart, checkout and customer account links.', 'gnn' ),
		),
		'design'       => array(
			'label'       => __( 'GNNdesign', 'gnn' ),
			'accent'      => '#7045aa',
			'header'      => 'minimal',
			'radius'      => 16,
			'font'        => 'space',
			'heading'     => __( 'Design. Code. Make an impact.', 'gnn' ),
			'description' => __( 'A personal portfolio for developers, designers and independent creative professionals.', 'gnn' ),
		),
		'architecture' => array(
			'label'        => __( 'GNNarchitecture', 'gnn' ),
			'accent'       => '#855c40',
			'header'       => 'minimal',
			'radius'       => 2,
			'font'         => 'manrope',
			'heading_font' => 'space',
			'heading'      => __( 'Space. Light. Life.', 'gnn' ),
			'description'  => __( 'Architecture and construction with a live two-column portfolio, gallery, studio and services.', 'gnn' ),
		),
	);
}

/**
 * Resolve former names without removing existing customer records or saved option values.
 *
 * @param string $name Profile key.
 */
function gnn_starter_profile_key( $name ) {
	$aliases = array(
		'general'      => 'pro',
		'professional' => 'pro',
		'original'     => 'pro',
		'industrial'   => 'commerce',
		'creative'     => 'design',
	);
	return $aliases[ $name ] ?? $name;
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
			'heading_size'        => 'architecture' === $name || 'design' === $name ? 100 : 80,
			'heading_mobile_size' => 42,
			'accent_light'        => $profile['accent'],
			'accent_ink_light'    => '#ffffff',
			'color_bg_light'      => '#fafafa',
			'color_bg2_light'     => '#f0f1f3',
			'color_bg3_light'     => '#e5e7eb',
			'color_fg_light'      => '#18212b',
			'color_fg2_light'     => '#515d69',
			'color_line_light'    => '#d4d9df',
		)
	);
	if ( 'architecture' === $name ) {
		$options = array_replace(
			$options,
			array(
				'color_bg_light'   => '#f4f1e9',
				'color_bg2_light'  => '#ebe6dc',
				'color_bg3_light'  => '#ded6c9',
				'color_fg_light'   => '#262b27',
				'color_fg2_light'  => '#62665f',
				'color_line_light' => '#d2cabb',
				'accent_light'     => '#855c40',
				'accent_ink_light' => '#ffffff',
				'hero_style'       => 'none',
				'container_width'  => 1440,
			)
		);
	}
	update_option( 'gnn_options', $options );
	set_theme_mod( 'gnn_default_theme', 'light' );
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
	if ( 'commerce' === $name && 'elementor' === $editor && ! class_exists( 'WooCommerce' ) ) {
		return new WP_Error( 'gnn_starter_woocommerce', __( 'Activate WooCommerce and complete store setup before importing GNNcommerce.', 'gnn' ) );
	}
	if ( 'commerce' === $name && 'elementor' === $editor ) {
		foreach ( array( 'shop', 'cart', 'checkout', 'myaccount' ) as $utility ) {
			$id = wc_get_page_id( $utility );
			if ( $id <= 0 || 'publish' !== get_post_status( $id ) ) {
				return new WP_Error( 'gnn_starter_woocommerce', __( 'Activate WooCommerce and complete store setup before importing GNNcommerce.', 'gnn' ) );
			}
		}
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
add_action( 'init', 'gnn_register_portable_patterns', 20 );
