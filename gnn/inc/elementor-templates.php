<?php
/**
 * Elementor Free "Saved Templates" library — the same designed sections as
 * the Gutenberg patterns (inc/patterns.php), rebuilt with native Elementor
 * section/column/widget JSON so Elementor-only users get them too.
 *
 * Nothing here touches the Gutenberg patterns; both libraries coexist.
 * Templates are created once (idempotent, tracked by an option) as
 * `elementor_library` posts, which Elementor's own "Templates → Saved
 * Templates → Insert Template" panel lists automatically — no Pro required.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generate an Elementor-style element id (7 hex chars).
 *
 * @return string
 */
function gnn_el_id() {
	return substr( md5( uniqid( (string) wp_rand(), true ) ), 0, 7 );
}

/**
 * Build a section element.
 *
 * @param array $columns  Column elements (from gnn_el_column()).
 * @param array $settings Extra section settings (background, padding, …).
 * @return array
 */
function gnn_el_section( $columns, $settings = array() ) {
	return array(
		'id'       => gnn_el_id(),
		'elType'   => 'section',
		'settings' => array_merge(
			array(
				'layout' => 'full_width',
				'gap'    => 'no',
			),
			$settings
		),
		'elements' => $columns,
	);
}

/**
 * Build a column element.
 *
 * @param array $widgets Widget elements.
 * @param int   $size    Column width percentage (default 100).
 * @param array $settings Extra column settings.
 * @return array
 */
function gnn_el_column( $widgets, $size = 100, $settings = array() ) {
	return array(
		'id'       => gnn_el_id(),
		'elType'   => 'column',
		'settings' => array_merge( array( '_column_size' => $size ), $settings ),
		'elements' => $widgets,
	);
}

/**
 * Build a widget element.
 *
 * @param string $type     Elementor widgetType (heading, text-editor, button, …).
 * @param array  $settings Widget settings.
 * @return array
 */
function gnn_el_widget( $type, $settings = array() ) {
	return array(
		'id'         => gnn_el_id(),
		'elType'     => 'widget',
		'widgetType' => $type,
		'settings'   => $settings,
	);
}

/**
 * Shorthand: heading widget.
 *
 * @param string $text  Heading text.
 * @param array  $extra Extra settings (align, size, color, header_size…).
 * @return array
 */
function gnn_el_heading( $text, $extra = array() ) {
	return gnn_el_widget(
		'heading',
		array_merge(
			array(
				'title'       => $text,
				'header_size' => 'h2',
			),
			$extra
		)
	);
}

/**
 * Shorthand: text-editor widget (paragraph).
 *
 * @param string $html  Inner HTML.
 * @param array  $extra Extra settings.
 * @return array
 */
function gnn_el_text( $html, $extra = array() ) {
	return gnn_el_widget( 'text-editor', array_merge( array( 'editor' => $html ), $extra ) );
}

/**
 * Shorthand: button widget.
 *
 * @param string $text  Button label.
 * @param string $url   Button URL.
 * @param array  $extra Extra settings.
 * @return array
 */
function gnn_el_button( $text, $url = '#', $extra = array() ) {
	return gnn_el_widget(
		'button',
		array_merge(
			array(
				'text' => $text,
				'link' => array( 'url' => $url ),
			),
			$extra
		)
	);
}

/**
 * Shorthand: spacer widget.
 *
 * @param int $px Height in pixels.
 * @return array
 */
function gnn_el_spacer( $px = 40 ) {
	return gnn_el_widget(
		'spacer',
		array(
			'space' => array(
				'unit' => 'px',
				'size' => $px,
			),
		)
	);
}

/**
 * Shorthand: image widget.
 *
 * @param string $url   Image URL.
 * @param array  $extra Extra settings.
 * @return array
 */
function gnn_el_image( $url, $extra = array() ) {
	return gnn_el_widget(
		'image',
		array_merge(
			array(
				'image' => array(
					'url' => $url,
					'id'  => 0,
				),
			),
			$extra
		)
	);
}

/**
 * The full library: slug => [ title, section() ]. Colors reference the
 * theme's current accent (read once at build time) so a freshly generated
 * library matches the site's brand color; Elementor content itself is
 * static markup, so later accent changes need re-generating (Advanced tab).
 *
 * @return array
 */
function gnn_elementor_library() {
	$accent = get_theme_mod( 'gnn_accent_color', '#34d399' );
	$ink    = '#062416';
	$fg     = '#f5f5f4';
	$muted  = '#9d9da4';
	$ph     = get_theme_file_uri( 'assets/img/placeholder-cover.svg' );

	$section_pad = array(
		'padding' => array(
			'unit'     => 'px',
			'top'      => '80',
			'right'    => '32',
			'bottom'   => '80',
			'left'     => '32',
			'isLinked' => false,
		),
	);

	return array(

		'cover-poster-left'           => array(
			'title'   => __( 'Cover: poster left, text right', 'gnn' ),
			'section' => gnn_el_section(
				array(
					gnn_el_column(
						array(
							gnn_el_heading(
								__( 'Sample title', 'gnn' ),
								array(
									'header_size'          => 'h1',
									'title_color'          => $fg,
									'typography_typography' => 'custom',
									'typography_font_size' => array(
										'unit' => 'px',
										'size' => 64,
									),
								)
							),
						),
						50
					),
					gnn_el_column(
						array(
							gnn_el_text( '<p>' . __( 'Add your introductory copy here. Describe the feature, product or story you want to highlight in a couple of short sentences.', 'gnn' ) . '</p>', array( 'text_color' => $muted ) ),
							gnn_el_button(
								__( 'Learn more', 'gnn' ),
								'#',
								array(
									'background_color'  => $accent,
									'button_text_color' => $ink,
								)
							),
						),
						50
					),
				),
				array_merge(
					$section_pad,
					array(
						'background_background' => 'classic',
						'background_color'      => '#36220c',
					)
				)
			),
		),

		'cover-two-tone-image'        => array(
			'title'   => __( 'Two-tone background, centered image', 'gnn' ),
			'section' => gnn_el_section(
				array(
					gnn_el_column(
						array(
							gnn_el_image( $ph, array( 'align' => 'center' ) ),
							gnn_el_spacer( 32 ),
							gnn_el_heading(
								__( 'Etcetera', 'gnn' ),
								array(
									'align'       => 'center',
									'title_color' => '#fff',
								)
							),
						),
						100
					),
				),
				array_merge(
					$section_pad,
					array(
						'background_background'     => 'gradient',
						'background_color'          => '#234a14',
						'background_color_b'        => '#e18974',
						'background_gradient_angle' => array(
							'unit' => 'deg',
							'size' => 90,
						),
					)
				)
			),
		),

		'heading-paragraph-image'     => array(
			'title'   => __( 'Heading & paragraph with image', 'gnn' ),
			'section' => gnn_el_section(
				array(
					gnn_el_column( array( gnn_el_image( $ph ) ), 50 ),
					gnn_el_column(
						array(
							gnn_el_heading( __( 'About the event', 'gnn' ) ),
							gnn_el_text( '<p>' . __( 'Held over a weekend, the event is structured around a series of exhibitions, workshops, and panel discussions. Replace this copy with your own.', 'gnn' ) . '</p>' ),
						),
						50
					),
				),
				$section_pad
			),
		),

		'faqs'                        => array(
			'title'   => __( 'FAQs', 'gnn' ),
			'section' => gnn_el_section(
				array(
					gnn_el_column(
						array(
							gnn_el_heading( __( 'Frequently Asked Questions', 'gnn' ) ),
							gnn_el_spacer( 24 ),
							gnn_el_widget(
								'accordion',
								array(
									'tabs' => array(
										array(
											'_id'         => gnn_el_id(),
											'tab_title'   => __( 'Question one goes here?', 'gnn' ),
											'tab_content' => __( 'Answer the question here. Replace this placeholder text with your own content.', 'gnn' ),
										),
										array(
											'_id'         => gnn_el_id(),
											'tab_title'   => __( 'Question two goes here?', 'gnn' ),
											'tab_content' => __( 'Answer the question here. Replace this placeholder text with your own content.', 'gnn' ),
										),
										array(
											'_id'         => gnn_el_id(),
											'tab_title'   => __( 'Question three goes here?', 'gnn' ),
											'tab_content' => __( 'Answer the question here. Replace this placeholder text with your own content.', 'gnn' ),
										),
										array(
											'_id'         => gnn_el_id(),
											'tab_title'   => __( 'Question four goes here?', 'gnn' ),
											'tab_content' => __( 'Answer the question here. Replace this placeholder text with your own content.', 'gnn' ),
										),
									),
								)
							),
						),
						100
					),
				),
				$section_pad
			),
		),

		'contact-social'              => array(
			'title'   => __( 'Contact with social links', 'gnn' ),
			'section' => gnn_el_section(
				array(
					gnn_el_column(
						array(
							gnn_el_heading(
								__( 'Got questions? Feel free to reach out.', 'gnn' ),
								array(
									'align'       => 'center',
									'header_size' => 'h2',
								)
							),
							gnn_el_spacer( 32 ),
							gnn_el_widget(
								'social-icons',
								array(
									'social_icon_list' => array(
										array(
											'_id'         => gnn_el_id(),
											'social_icon' => array(
												'value'   => 'fab fa-x-twitter',
												'library' => 'fa-brands',
											),
											'link'        => array( 'url' => '#' ),
										),
										array(
											'_id'         => gnn_el_id(),
											'social_icon' => array(
												'value'   => 'fab fa-facebook',
												'library' => 'fa-brands',
											),
											'link'        => array( 'url' => '#' ),
										),
										array(
											'_id'         => gnn_el_id(),
											'social_icon' => array(
												'value'   => 'fab fa-instagram',
												'library' => 'fa-brands',
											),
											'link'        => array( 'url' => '#' ),
										),
										array(
											'_id'         => gnn_el_id(),
											'social_icon' => array(
												'value'   => 'fab fa-linkedin',
												'library' => 'fa-brands',
											),
											'link'        => array( 'url' => '#' ),
										),
									),
									'align'            => 'center',
								)
							),
						),
						100
					),
				),
				$section_pad
			),
		),

		'dark-banner-heading'         => array(
			'title'   => __( 'Dark banner, top-left heading', 'gnn' ),
			'section' => gnn_el_section(
				array(
					gnn_el_column(
						array(
							gnn_el_heading(
								__( 'hello!', 'gnn' ),
								array(
									'title_color'          => '#d8a557',
									'typography_typography' => 'custom',
									'typography_font_size' => array(
										'unit' => 'px',
										'size' => 96,
									),
									'typography_font_style' => 'italic',
									'typography_font_weight' => '900',
								)
							),
						),
						100
					),
				),
				array_merge(
					$section_pad,
					array(
						'background_background' => 'classic',
						'background_color'      => '#141414',
						'background_image'      => array(
							'url' => $ph,
							'id'  => 0,
						),
						'background_position'   => 'top center',
						'background_size'       => 'cover',
						'min_height'            => array(
							'unit' => 'vh',
							'size' => 50,
						),
						'height'                => 'min-height',
					)
				)
			),
		),

		'bold-heading-button'         => array(
			'title'   => __( 'Bold heading, paragraph & button', 'gnn' ),
			'section' => gnn_el_section(
				array(
					gnn_el_column(
						array(
							gnn_el_heading(
								__( 'Big bold headline', 'gnn' ),
								array(
									'title_color'          => '#000',
									'typography_typography' => 'custom',
									'typography_font_size' => array(
										'unit' => 'px',
										'size' => 80,
									),
									'typography_font_weight' => '700',
								)
							),
							gnn_el_heading(
								__( '— second line', 'gnn' ),
								array(
									'title_color'          => '#fff',
									'typography_typography' => 'custom',
									'typography_font_size' => array(
										'unit' => 'px',
										'size' => 80,
									),
									'typography_font_weight' => '700',
								)
							),
						),
						50
					),
					gnn_el_column(
						array(
							gnn_el_text( '<p>' . __( 'Add a short supporting paragraph here to describe the offer, product or story. Replace with your own copy.', 'gnn' ) . '</p>' ),
							gnn_el_button(
								__( 'Learn more', 'gnn' ),
								'#',
								array(
									'background_color'  => '#000',
									'button_text_color' => '#fff',
								)
							),
						),
						50
					),
				),
				array_merge(
					$section_pad,
					array(
						'background_background' => 'classic',
						'background_color'      => '#d1362a',
					)
				)
			),
		),

		'headline-links-gradient'     => array(
			'title'   => __( 'Headline with links, gradient background', 'gnn' ),
			'section' => gnn_el_section(
				array(
					gnn_el_column(
						array(
							gnn_el_heading(
								__( 'BRAND.', 'gnn' ),
								array(
									'title_color'          => '#fff',
									'typography_typography' => 'custom',
									'typography_font_size' => array(
										'unit' => 'px',
										'size' => 96,
									),
									'typography_font_weight' => '700',
								)
							),
						),
						60
					),
					gnn_el_column(
						array(
							gnn_el_text( '<p>' . __( 'A new collection', 'gnn' ) . '</p>', array( 'text_color' => '#fff' ) ),
							gnn_el_text( '<p>' . __( 'Learn More →', 'gnn' ) . '</p>', array( 'text_color' => '#fff' ) ),
						),
						40
					),
				),
				array_merge(
					$section_pad,
					array(
						'background_background'     => 'gradient',
						'background_color'          => '#000',
						'background_color_b'        => '#53507b',
						'background_gradient_angle' => array(
							'unit' => 'deg',
							'size' => 180,
						),
					)
				)
			),
		),

		'cover-heading-button-left'   => array(
			'title'   => __( 'Cover: heading & button (left)', 'gnn' ),
			'section' => gnn_el_section(
				array(
					gnn_el_column(
						array(
							gnn_el_heading(
								__( 'A bold headline goes here', 'gnn' ),
								array(
									'title_color'          => '#fff',
									'typography_typography' => 'custom',
									'typography_font_size' => array(
										'unit' => 'px',
										'size' => 64,
									),
									'typography_font_weight' => '700',
								)
							),
							gnn_el_spacer( 32 ),
							gnn_el_button(
								__( 'Explore', 'gnn' ),
								'#',
								array(
									'button_type'       => 'outline',
									'border_color'      => '#fff',
									'button_text_color' => '#fff',
								)
							),
						),
						100
					),
				),
				array_merge(
					$section_pad,
					array(
						'background_background' => 'classic',
						'background_color'      => '#00000030',
						'background_image'      => array(
							'url' => $ph,
							'id'  => 0,
						),
						'background_size'       => 'cover',
						'min_height'            => array(
							'unit' => 'vh',
							'size' => 66,
						),
						'height'                => 'min-height',
					)
				)
			),
		),

		'cover-heading-button-center' => array(
			'title'   => __( 'Cover: heading & button (centered)', 'gnn' ),
			'section' => gnn_el_section(
				array(
					gnn_el_column(
						array(
							gnn_el_heading(
								__( 'A centered bold headline goes here', 'gnn' ),
								array(
									'align'                => 'center',
									'title_color'          => '#fff',
									'typography_typography' => 'custom',
									'typography_font_size' => array(
										'unit' => 'px',
										'size' => 64,
									),
									'typography_font_weight' => '700',
								)
							),
							gnn_el_spacer( 32 ),
							gnn_el_button(
								__( 'Explore', 'gnn' ),
								'#',
								array(
									'align'             => 'center',
									'button_type'       => 'outline',
									'border_color'      => '#fff',
									'button_text_color' => '#fff',
								)
							),
						),
						100
					),
				),
				array_merge(
					$section_pad,
					array(
						'background_background' => 'classic',
						'background_color'      => '#00000030',
						'background_image'      => array(
							'url' => $ph,
							'id'  => 0,
						),
						'background_size'       => 'cover',
						'min_height'            => array(
							'unit' => 'vh',
							'size' => 66,
						),
						'height'                => 'min-height',
					)
				)
			),
		),

		'media-text-left'             => array(
			'title'   => __( 'Media & text (image left)', 'gnn' ),
			'section' => gnn_el_section(
				array(
					gnn_el_column( array( gnn_el_image( $ph ) ), 50 ),
					gnn_el_column(
						array(
							gnn_el_heading( __( 'Open spaces', 'gnn' ), array( 'align' => 'center' ) ),
							gnn_el_text( '<p style="text-align:center"><a href="#">' . __( 'Case study ↗', 'gnn' ) . '</a></p>' ),
						),
						50,
						array( 'content_position' => 'center' )
					),
				),
				array( 'gap' => 'no' )
			),
		),

		'media-text-right'            => array(
			'title'   => __( 'Media & text (image right)', 'gnn' ),
			'section' => gnn_el_section(
				array(
					gnn_el_column(
						array(
							gnn_el_heading( __( 'Open spaces', 'gnn' ), array( 'align' => 'center' ) ),
							gnn_el_text( '<p style="text-align:center"><a href="#">' . __( 'Case study ↗', 'gnn' ) . '</a></p>' ),
						),
						50,
						array( 'content_position' => 'center' )
					),
					gnn_el_column( array( gnn_el_image( $ph ) ), 50 ),
				),
				array( 'gap' => 'no' )
			),
		),

		'cover-large-header-left'     => array(
			'title'   => __( 'Large header over cover (left text)', 'gnn' ),
			'section' => gnn_el_section(
				array(
					gnn_el_column(
						array(
							gnn_el_heading(
								__( 'Headline.', 'gnn' ),
								array(
									'title_color'          => '#ffe074',
									'typography_typography' => 'custom',
									'typography_font_size' => array(
										'unit' => 'px',
										'size' => 64,
									),
								)
							),
							gnn_el_spacer( 48 ),
							gnn_el_text( '<p><em>' . __( 'Add a short, evocative introduction here. Replace this placeholder with your own copy describing the section, product or story.', 'gnn' ) . '</em></p>', array( 'text_color' => '#ffe074' ) ),
						),
						55
					),
					gnn_el_column( array(), 45 ),
				),
				array_merge(
					$section_pad,
					array(
						'background_background' => 'classic',
						'background_image'      => array(
							'url' => $ph,
							'id'  => 0,
						),
						'background_size'       => 'cover',
						'min_height'            => array(
							'unit' => 'px',
							'size' => 800,
						),
						'height'                => 'min-height',
					)
				)
			),
		),

		'gallery-two-images'          => array(
			'title'   => __( 'Two images side by side', 'gnn' ),
			'section' => gnn_el_section(
				array(
					gnn_el_column( array( gnn_el_image( $ph ) ), 50 ),
					gnn_el_column( array( gnn_el_image( $ph ) ), 50 ),
				)
			),
		),

	);
}

/**
 * Create (or re-create) the Elementor local templates. Runs once per theme
 * activation and once whenever "Rebuild Elementor templates" is used from
 * the panel's Advanced tab (e.g. after changing the accent color).
 */
function gnn_install_elementor_templates() {
	if ( ! post_type_exists( 'elementor_library' ) ) {
		return;
	}

	// Remove any previously generated set first, so re-runs don't duplicate.
	$existing = get_posts(
		array(
			'post_type'      => 'elementor_library',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- rare, admin-triggered, small result set (theme templates only).
				array( 'key' => '_gnn_elementor_template' ),
			),
			'fields'         => 'ids',
		)
	);
	foreach ( $existing as $post_id ) {
		wp_delete_post( $post_id, true );
	}

	foreach ( gnn_elementor_library() as $slug => $tpl ) {
		$post_id = wp_insert_post(
			array(
				'post_type'   => 'elementor_library',
				'post_title'  => $tpl['title'],
				'post_status' => 'publish',
			)
		);
		if ( ! $post_id || is_wp_error( $post_id ) ) {
			continue;
		}
		update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
		update_post_meta( $post_id, '_elementor_template_type', 'section' );
		update_post_meta( $post_id, '_elementor_version', '3.0.0' );
		update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( array( $tpl['section'] ) ) ) );
		update_post_meta( $post_id, '_gnn_elementor_template', $slug );
		wp_set_object_terms( $post_id, 'section', 'elementor_library_type' );
	}

	update_option( 'gnn_elementor_templates_installed', GNN_VERSION );
}

/**
 * Install once, the first time Elementor is active. Hooked to admin_init
 * (runs on every wp-admin request) rather than elementor/loaded: in some
 * environments (e.g. automated/headless installs) elementor/loaded fires
 * without a full admin request ever completing, so it can be missed. The
 * option flag makes this a no-op after the first successful run.
 */
function gnn_maybe_install_elementor_templates() {
	if ( post_type_exists( 'elementor_library' ) && ! get_option( 'gnn_elementor_templates_installed' ) ) {
		gnn_install_elementor_templates();
	}
}
add_action( 'admin_init', 'gnn_maybe_install_elementor_templates' );
