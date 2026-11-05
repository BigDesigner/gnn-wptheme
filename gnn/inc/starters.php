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
		'general'      => array(
			'label'   => __( 'General services', 'gnn' ),
			'accent'  => '#2563eb',
			'header'  => 'classic',
			'radius'  => 10,
			'font'    => 'manrope',
			'heading' => __( 'Clear solutions. Reliable service.', 'gnn' ),
		),
		'professional' => array(
			'label'   => __( 'Professional services', 'gnn' ),
			'accent'  => '#24415c',
			'header'  => 'minimal',
			'radius'  => 2,
			'font'    => 'system',
			'heading' => __( 'Experience you can rely on.', 'gnn' ),
		),
		'industrial'   => array(
			'label'   => __( 'Industrial and B2B', 'gnn' ),
			'accent'  => '#b45309',
			'header'  => 'stacked',
			'radius'  => 4,
			'font'    => 'manrope',
			'heading' => __( 'Built for demanding projects.', 'gnn' ),
		),
		'creative'     => array(
			'label'   => __( 'Creative agency', 'gnn' ),
			'accent'  => '#7c3aed',
			'header'  => 'transparent',
			'radius'  => 24,
			'font'    => 'space',
			'heading' => __( 'Ideas shaped into meaningful experiences.', 'gnn' ),
		),
	);
}

/**
 * Presets alter design only; customer identity, code, media and user content survive.
 *
 * @param string $name Profile identifier.
 */
function gnn_apply_design_preset( $name ) {
	$profiles = gnn_starter_profiles();
	if ( ! current_user_can( 'manage_options' ) || ! isset( $profiles[ $name ] ) ) {
		return new WP_Error( 'gnn_preset', __( 'This profile is not available.', 'gnn' ) ); }
	$started = gnn_configuration_begin( 'preset', gnn_configuration_form_revision() );
	if ( is_wp_error( $started ) ) {
		return $started; }
	$profile = $profiles[ $name ];
	$options = (array) get_option( 'gnn_options', array() );
	$options = array_replace(
		$options,
		array(
			'site_segment'          => $name,
			'header_preset'         => $profile['header'],
			'footer_preset'         => 'balanced',
			'design_radius'         => $profile['radius'],
			'button_radius'         => $profile['radius'],
			'body_font'             => $profile['font'],
			'heading_font'          => $profile['font'],
			'compatibility_mode'    => 1,
			'google_material_icons' => 0,
		)
	);
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
 */
function gnn_install_starter( $name ) {
	$profiles = gnn_starter_profiles();
	if ( ! current_user_can( 'manage_options' ) || ! isset( $profiles[ $name ] ) ) {
		return new WP_Error( 'gnn_starter', __( 'Invalid starter profile.', 'gnn' ) ); }
	$started = gnn_configuration_begin( 'starter', gnn_configuration_form_revision() );
	if ( is_wp_error( $started ) ) {
		return $started; }
	$pages = array(
		'contact'  => __( 'Contact', 'gnn' ),
		'services' => __( 'Services', 'gnn' ),
		'about'    => __( 'About', 'gnn' ),
		'home'     => __( 'Home', 'gnn' ),
		'news'     => __( 'News', 'gnn' ),
	);
	$ids   = array();
	foreach ( $pages as $slug => $title ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			$ids[ $slug ] = $existing->ID;
			continue; }
		$contact = ! empty( $ids['contact'] ) ? get_permalink( $ids['contact'] ) : '';
		$heading = 'home' === $slug ? $profiles[ $name ]['heading'] : $title;
		$text    = 'contact' === $slug ? __( 'Add your public phone, email, location and a tested contact form before publishing.', 'gnn' ) : __( 'Replace this introductory content with your own services, expertise and project information.', 'gnn' );
		$id      = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_name'    => $slug,
				'post_title'   => $title,
				'post_status'  => 'draft',
				'post_content' => gnn_starter_content( $heading, $text, 'contact' === $slug ? '' : $contact, __( 'Contact us', 'gnn' ) ),
			),
			true
		);
		if ( is_wp_error( $id ) ) {
			return $id; }
		update_post_meta( $id, '_gnn_starter', $name . ':' . $slug );
		$ids[ $slug ] = $id;
	}
	// Drafts are intentionally not assigned as the live front page or published automatically.
	return $ids;
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
