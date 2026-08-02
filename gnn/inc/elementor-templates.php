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

	// Home page template — matches the theme's own dark tokens exactly
	// (see :root in main.css) so it blends seamlessly, plus fixed accent
	// companions, one per GNN sub-brand, for visual identity.
	$bg     = '#0a0a0b';
	$bg2    = '#121214';
	$line   = '#232328';
	$cyan   = '#22d3ee';
	$purple = '#a78bfa';
	$amber  = '#fb923c';
	$blue   = '#60a5fa';

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

		'home-page-gnn'               => array(
			'title'    => 'GNN Ana Sayfa',
			'sections' => array(

				// 1. Hero: kicker + headline + copy + dual CTA (left), ecosystem "code" art panel (right).
				gnn_el_section(
					array(
						gnn_el_column(
							array(
								gnn_el_heading(
									'GNN ENTERPRISE ECOSYSTEM',
									array(
										'header_size' => 'h6',
										'title_color' => $accent,
										'typography_typography' => 'custom',
										'typography_font_size' => array(
											'unit' => 'px',
											'size' => 13,
										),
										'typography_font_weight' => '700',
										'typography_letter_spacing' => array(
											'unit' => 'px',
											'size' => 1.5,
										),
									)
								),
								gnn_el_heading(
									'Kritik Altyapılar ve Dijital Varlıklar İçin Mutlak Mühendislik.',
									array(
										'header_size' => 'h1',
										'title_color' => $fg,
										'typography_typography' => 'custom',
										'typography_font_size' => array(
											'unit' => 'px',
											'size' => 46,
										),
										'typography_font_weight' => '800',
										'typography_line_height' => array(
											'unit' => 'em',
											'size' => 1.18,
										),
									)
								),
								gnn_el_text(
									'<p>Siber savunma, zafiyet analitiği, kurumsal kimlik tasarımı, özel yazılım geliştirme ve bütüncül IT danışmanlığını kapsayan uçtan uca kurumsal teknoloji ekosistemi.</p>',
									array( 'text_color' => $muted )
								),
								gnn_el_spacer( 8 ),
								gnn_el_widget(
									'button',
									array(
										'text'          => 'Ekosistemi Keşfedin →',
										'link'          => array( 'url' => '#ecosystem' ),
										'button_type'   => 'success',
										'size'          => 'md',
										'border_radius' => array(
											'unit'     => 'px',
											'top'      => '999',
											'right'    => '999',
											'bottom'   => '999',
											'left'     => '999',
											'isLinked' => true,
										),
										'_margin'       => array(
											'unit'     => 'px',
											'top'      => '0',
											'right'    => '12',
											'bottom'   => '0',
											'left'     => '0',
											'isLinked' => false,
										),
									)
								),
								gnn_el_widget(
									'button',
									array(
										'text'          => 'İletişime Geçin',
										'link'          => array( 'url' => '#contact' ),
										'button_type'   => 'outline',
										'border_color'  => $line,
										'button_text_color' => $fg,
										'size'          => 'md',
										'border_radius' => array(
											'unit'     => 'px',
											'top'      => '999',
											'right'    => '999',
											'bottom'   => '999',
											'left'     => '999',
											'isLinked' => true,
										),
									)
								),
							),
							50,
							array( 'content_position' => 'center' )
						),
						gnn_el_column(
							array(
								gnn_el_text(
									'<div style="background:' . $bg2 . ';border:1px solid ' . $line . ';padding:28px;border-radius:20px;font-family:Consolas,Menlo,monospace;font-size:14px;line-height:2;color:' . $muted . ';box-shadow:0 20px 60px -20px rgba(0,0,0,.6);">' .
										'<span style="color:' . $purple . ';">const</span> gnnEcosystem = {<br>' .
										'&nbsp;&nbsp;creative: <span style="color:' . $purple . ';">\'Kimlik &amp; Arayüz\'</span>,<br>' .
										'&nbsp;&nbsp;cyber: <span style="color:' . $accent . ';">\'Zero-Trust Savunma\'</span>,<br>' .
										'&nbsp;&nbsp;logix: <span style="color:' . $cyan . ';">\'SIEM &amp; Zafiyet Analizi\'</span>,<br>' .
										'&nbsp;&nbsp;labs: <span style="color:' . $amber . ';">\'Özel Yazılım\'</span>,<br>' .
										'&nbsp;&nbsp;advisory: <span style="color:' . $blue . ';">\'IT Stratejisi\'</span><br>' .
										'};</div>'
								),
							),
							50,
							array( 'content_position' => 'center' )
						),
					),
					array(
						'background_background' => 'classic',
						'layout'                => 'boxed',
						'gap'                   => 'wide',
						'background_color'      => $bg,
						'padding'               => array(
							'unit'     => 'px',
							'top'      => '110',
							'right'    => '32',
							'bottom'   => '72',
							'left'     => '32',
							'isLinked' => false,
						),
					)
				),

				// 2. Stat strip.
				gnn_el_section(
					array(
						gnn_el_column(
							array(
								gnn_el_heading(
									'7/24',
									array(
										'align'       => 'center',
										'title_color' => $accent,
										'typography_typography' => 'custom',
										'typography_font_size' => array(
											'unit' => 'px',
											'size' => 34,
										),
										'typography_font_weight' => '800',
									)
								),
								gnn_el_text( '<p style="text-align:center">SOC İzleme</p>', array( 'text_color' => $muted ) ),
							),
							25
						),
						gnn_el_column(
							array(
								gnn_el_heading(
									'50+',
									array(
										'align'       => 'center',
										'title_color' => $cyan,
										'typography_typography' => 'custom',
										'typography_font_size' => array(
											'unit' => 'px',
											'size' => 34,
										),
										'typography_font_weight' => '800',
									)
								),
								gnn_el_text( '<p style="text-align:center">Kurumsal Proje</p>', array( 'text_color' => $muted ) ),
							),
							25
						),
						gnn_el_column(
							array(
								gnn_el_heading(
									'%99.9',
									array(
										'align'       => 'center',
										'title_color' => $purple,
										'typography_typography' => 'custom',
										'typography_font_size' => array(
											'unit' => 'px',
											'size' => 34,
										),
										'typography_font_weight' => '800',
									)
								),
								gnn_el_text( '<p style="text-align:center">Uptime Garantisi</p>', array( 'text_color' => $muted ) ),
							),
							25
						),
						gnn_el_column(
							array(
								gnn_el_heading(
									'10+',
									array(
										'align'       => 'center',
										'title_color' => $accent,
										'typography_typography' => 'custom',
										'typography_font_size' => array(
											'unit' => 'px',
											'size' => 34,
										),
										'typography_font_weight' => '800',
									)
								),
								gnn_el_text( '<p style="text-align:center">Yıl Deneyim</p>', array( 'text_color' => $muted ) ),
							),
							25
						),
					),
					array(
						'background_background' => 'classic',
						'layout'                => 'boxed',
						'gap'                   => 'wide',
						'background_color'      => $bg2,
						'padding'               => array(
							'unit'     => 'px',
							'top'      => '36',
							'right'    => '32',
							'bottom'   => '36',
							'left'     => '32',
							'isLinked' => false,
						),
					)
				),

				// 3. GNNcreative — zigzag #1: copy left, design-system mockup right.
				gnn_el_section(
					array(
						gnn_el_column(
							array(
								gnn_el_text( '<span class="gnn-subbrand-tag"><span class="gnn-brand-prefix">GNN</span><span class="gnn-brand-dot"></span><span class="gnn-brand-suffix">creative</span></span>' ),
								gnn_el_spacer( 20 ),
								gnn_el_heading(
									'Kurumsal Kimlikten Dijital Deneyime, Tek Hassas Çizgi.',
									array(
										'title_color' => $fg,
										'typography_typography' => 'custom',
										'typography_font_size' => array(
											'unit' => 'px',
											'size' => 30,
										),
										'typography_font_weight' => '700',
									)
								),
								gnn_el_text(
									'<p>Marka kimliğinizi sıfırdan kurguluyor; logo, kurumsal doküman ve fuar/outdoor materyallerinden, milisaniyeler içinde yüklenen yüksek dönüşümlü web platformlarına kadar her temas noktasını aynı hassasiyetle tasarlıyoruz.</p>',
									array( 'text_color' => $muted )
								),
								gnn_el_text( '<p><a href="#contact" style="color:' . $purple . ';font-weight:700;text-decoration:none;">Tasarım Sürecini İnceleyin →</a></p>' ),
							),
							50,
							array( 'content_position' => 'center' )
						),
						gnn_el_column(
							array(
								gnn_el_text(
									'<div style="background:' . $bg . ';border:1px solid ' . $line . ';border-radius:20px;padding:28px;box-shadow:0 20px 60px -20px rgba(0,0,0,.6);">' .
										'<div style="display:flex;gap:6px;margin-bottom:22px;">' .
											'<span style="width:10px;height:10px;border-radius:50%;background:#ff5f57;display:inline-block;"></span>' .
											'<span style="width:10px;height:10px;border-radius:50%;background:#febc2e;display:inline-block;"></span>' .
											'<span style="width:10px;height:10px;border-radius:50%;background:#28c840;display:inline-block;"></span>' .
										'</div>' .
										'<div style="font-family:Consolas,Menlo,monospace;font-size:40px;font-weight:800;color:' . $fg . ';margin-bottom:20px;">Aa</div>' .
										'<div style="display:flex;gap:10px;margin-bottom:22px;">' .
											'<span style="width:28px;height:28px;border-radius:8px;background:' . $purple . ';display:inline-block;"></span>' .
											'<span style="width:28px;height:28px;border-radius:8px;background:' . $accent . ';display:inline-block;"></span>' .
											'<span style="width:28px;height:28px;border-radius:8px;background:' . $cyan . ';display:inline-block;"></span>' .
											'<span style="width:28px;height:28px;border-radius:8px;background:' . $fg . ';display:inline-block;"></span>' .
										'</div>' .
										'<div style="height:10px;background:' . $line . ';border-radius:6px;margin-bottom:8px;"></div>' .
										'<div style="height:10px;width:70%;background:' . $line . ';border-radius:6px;"></div>' .
									'</div>'
								),
							),
							50,
							array( 'content_position' => 'center' )
						),
					),
					array(
						'background_background' => 'classic',
						'layout'                => 'boxed',
						'gap'                   => 'wide',
						'background_color'      => $bg2,
						'padding'               => array(
							'unit'     => 'px',
							'top'      => '96',
							'right'    => '32',
							'bottom'   => '96',
							'left'     => '32',
							'isLinked' => false,
						),
					)
				),

				// 4. GNNcyber — zigzag #2: SIEM/EDR terminal mockup left, copy right.
				gnn_el_section(
					array(
						gnn_el_column(
							array(
								gnn_el_text(
									'<div style="background:' . $bg2 . ';border:1px solid ' . $line . ';border-radius:20px;padding:28px;font-family:Consolas,Menlo,monospace;font-size:13px;line-height:2;color:' . $muted . ';box-shadow:0 20px 60px -20px rgba(0,0,0,.6);">' .
										'<div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">' .
											'<span style="width:8px;height:8px;border-radius:50%;background:' . $accent . ';display:inline-block;"></span>' .
											'<span style="color:' . $accent . ';font-weight:700;">Sistem Güvende</span>' .
										'</div>' .
										'<div>[19:42:11] <span style="color:' . $cyan . ';">EDR</span> :: endpoint-042 ... <span style="color:' . $accent . ';">OK</span></div>' .
										'<div>[19:42:13] <span style="color:' . $cyan . ';">SIEM</span> :: korelasyon ... 3 olay</div>' .
										'<div>[19:42:15] <span style="color:' . $cyan . ';">FIREWALL</span> :: kural senkronu ... <span style="color:' . $accent . ';">OK</span></div>' .
										'<div>[19:42:18] <span style="color:' . $cyan . ';">XDR</span> :: tehdit avı ... temiz</div>' .
									'</div>'
								),
							),
							50,
							array( 'content_position' => 'center' )
						),
						gnn_el_column(
							array(
								gnn_el_text( '<span class="gnn-subbrand-tag"><span class="gnn-brand-prefix">GNN</span><span class="gnn-brand-dot"></span><span class="gnn-brand-suffix">cyber</span></span>' ),
								gnn_el_spacer( 20 ),
								gnn_el_heading(
									'Sıfır Güven Mimarisiyle Kesintisiz Siber Savunma.',
									array(
										'title_color' => $fg,
										'typography_typography' => 'custom',
										'typography_font_size' => array(
											'unit' => 'px',
											'size' => 30,
										),
										'typography_font_weight' => '700',
									)
								),
								gnn_el_text(
									'<p>Kurumsal ağınızı EDR, SIEM, XDR ve yeni nesil güvenlik duvarı entegrasyonlarıyla uçtan uca koruyoruz. Sızma testlerinden Active Directory sıkılaştırmasına, gerçek zamanlı tehdit avcılığından yetkisiz erişim denetimine kadar savunmanız hiç durmuyor.</p>',
									array( 'text_color' => $muted )
								),
								gnn_el_text( '<p><a href="#contact" style="color:' . $accent . ';font-weight:700;text-decoration:none;">Güvenlik Mimarisini Görün →</a></p>' ),
							),
							50,
							array( 'content_position' => 'center' )
						),
					),
					array(
						'background_background' => 'classic',
						'layout'                => 'boxed',
						'gap'                   => 'wide',
						'background_color'      => $bg,
						'padding'               => array(
							'unit'     => 'px',
							'top'      => '96',
							'right'    => '32',
							'bottom'   => '96',
							'left'     => '32',
							'isLinked' => false,
						),
					)
				),

				// 5. GNNlogix — zigzag #3: copy left, vulnerability report mockup right.
				gnn_el_section(
					array(
						gnn_el_column(
							array(
								gnn_el_text( '<span class="gnn-subbrand-tag"><span class="gnn-brand-prefix">GNN</span><span class="gnn-brand-dot"></span><span class="gnn-brand-suffix">logix</span></span>' ),
								gnn_el_spacer( 20 ),
								gnn_el_heading(
									'Riski Görünür Kılan Zafiyet İstihbaratı.',
									array(
										'title_color' => $fg,
										'typography_typography' => 'custom',
										'typography_font_size' => array(
											'unit' => 'px',
											'size' => 30,
										),
										'typography_font_weight' => '700',
									)
								),
								gnn_el_text(
									'<p>Kendi geliştirdiğimiz tarama araçları ve Wazuh SIEM mimarisiyle altyapınızı gerçek zamanlı izliyor; teknik bulguları yönetim kurulunun okuyabileceği net, aksiyon alınabilir raporlara dönüştürüyoruz.</p>',
									array( 'text_color' => $muted )
								),
								gnn_el_text( '<p><a href="#contact" style="color:' . $cyan . ';font-weight:700;text-decoration:none;">Örnek Raporu İnceleyin →</a></p>' ),
							),
							50,
							array( 'content_position' => 'center' )
						),
						gnn_el_column(
							array(
								gnn_el_text(
									'<div style="background:' . $bg . ';border:1px solid ' . $line . ';border-radius:20px;padding:28px;box-shadow:0 20px 60px -20px rgba(0,0,0,.6);">' .
										'<div style="font-family:Consolas,Menlo,monospace;font-size:12px;letter-spacing:.08em;text-transform:uppercase;color:' . $muted . ';margin-bottom:18px;">Wazuh SIEM · Zafiyet Taraması</div>' .
										'<div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid ' . $line . ';"><span style="color:' . $fg . ';font-size:14px;">Kritik</span><span style="background:#ef4444;color:#fff;font-size:12px;font-weight:800;padding:3px 10px;border-radius:999px;">0</span></div>' .
										'<div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid ' . $line . ';"><span style="color:' . $fg . ';font-size:14px;">Yüksek</span><span style="background:' . $amber . ';color:#111;font-size:12px;font-weight:800;padding:3px 10px;border-radius:999px;">2</span></div>' .
										'<div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid ' . $line . ';"><span style="color:' . $fg . ';font-size:14px;">Orta</span><span style="background:#eab308;color:#111;font-size:12px;font-weight:800;padding:3px 10px;border-radius:999px;">5</span></div>' .
										'<div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;"><span style="color:' . $fg . ';font-size:14px;">Düşük</span><span style="background:' . $line . ';color:' . $muted . ';font-size:12px;font-weight:800;padding:3px 10px;border-radius:999px;">11</span></div>' .
									'</div>'
								),
							),
							50,
							array( 'content_position' => 'center' )
						),
					),
					array(
						'background_background' => 'classic',
						'layout'                => 'boxed',
						'gap'                   => 'wide',
						'background_color'      => $bg2,
						'padding'               => array(
							'unit'     => 'px',
							'top'      => '96',
							'right'    => '32',
							'bottom'   => '96',
							'left'     => '32',
							'isLinked' => false,
						),
					)
				),

				// 6. GNNlabs & GNNadvisory — compressed 2-card grid (breaks the zigzag rhythm).
				gnn_el_section(
					array(
						gnn_el_column(
							array(
								gnn_el_text(
									'<div style="background:' . $bg2 . ';border:1px solid ' . $line . ';border-radius:16px;padding:32px;height:100%;box-sizing:border-box;">' .
										'<span class="gnn-subbrand-tag"><span class="gnn-brand-prefix">GNN</span><span class="gnn-brand-dot"></span><span class="gnn-brand-suffix">labs</span></span>' .
										'<h3 style="color:' . $fg . ';font-size:20px;margin:18px 0 10px;">Web, Mobil ve Özel Yazılım Mühendisliği</h3>' .
										'<p style="color:' . $muted . ';font-size:15px;line-height:1.7;margin:0;">İş süreçlerinize özel CRM\'ler, yüksek performanslı mobil uygulamalar ve ölçeklenebilir platformlar — fikirden dağıtıma tüm mühendislik döngüsü tek çatı altında.</p>' .
									'</div>'
								),
							),
							50
						),
						gnn_el_column(
							array(
								gnn_el_text(
									'<div style="background:' . $bg2 . ';border:1px solid ' . $line . ';border-radius:16px;padding:32px;height:100%;box-sizing:border-box;">' .
										'<span class="gnn-subbrand-tag"><span class="gnn-brand-prefix">GNN</span><span class="gnn-brand-dot"></span><span class="gnn-brand-suffix">advisory</span></span>' .
										'<h3 style="color:' . $fg . ';font-size:20px;margin:18px 0 10px;">Stratejik IT ve Teknoloji Danışmanlığı</h3>' .
										'<p style="color:' . $muted . ';font-size:15px;line-height:1.7;margin:0;">Altyapınızın tam bir röntgenini çekiyoruz: sunucu ve yedekleme stratejisinden ERP seçimine, KVKK/GDPR uyumluluğuna kadar eksiksiz bir teknoloji yol haritası çıkarıyoruz.</p>' .
									'</div>'
								),
							),
							50
						),
					),
					array(
						'background_background' => 'classic',
						'layout'                => 'boxed',
						'gap'                   => 'wide',
						'background_color'      => $bg,
						'padding'               => array(
							'unit'     => 'px',
							'top'      => '16',
							'right'    => '32',
							'bottom'   => '96',
							'left'     => '32',
							'isLinked' => false,
						),
					)
				),

				// 7. Final CTA.
				gnn_el_section(
					array(
						gnn_el_column(
							array(
								gnn_el_heading(
									'Sıradaki Büyük Projenizi GNN Ekosistemiyle Şekillendirin',
									array(
										'align'       => 'center',
										'title_color' => $fg,
										'typography_typography' => 'custom',
										'typography_font_size' => array(
											'unit' => 'px',
											'size' => 30,
										),
									)
								),
								gnn_el_text( '<p style="text-align:center">Tasarımdan güvenliğe, yazılımdan stratejiye — tek ekip, tek sorumluluk.</p>', array( 'text_color' => $muted ) ),
								gnn_el_spacer( 24 ),
								gnn_el_widget(
									'button',
									array(
										'text'          => 'Ücretsiz Danışmanlık Alın →',
										'link'          => array( 'url' => '#contact' ),
										'align'         => 'center',
										'button_type'   => 'success',
										'border_radius' => array(
											'unit'     => 'px',
											'top'      => '999',
											'right'    => '999',
											'bottom'   => '999',
											'left'     => '999',
											'isLinked' => true,
										),
									)
								),
							),
							100
						),
					),
					array(
						'background_background' => 'classic',
						'layout'                => 'boxed',
						'gap'                   => 'wide',
						'background_color'      => $bg2,
						'padding'               => array(
							'unit'     => 'px',
							'top'      => '72',
							'right'    => '40',
							'bottom'   => '72',
							'left'     => '40',
							'isLinked' => false,
						),
					)
				),
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
		// Most templates are one section ('section'); multi-section page
		// templates provide 'sections' (already a list) instead.
		$sections = isset( $tpl['sections'] ) ? $tpl['sections'] : array( $tpl['section'] );
		update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( $sections ) ) );
		update_post_meta( $post_id, '_gnn_elementor_template', $slug );
		wp_set_object_terms( $post_id, 'section', 'elementor_library_type' );
	}

	update_option( 'gnn_elementor_templates_installed', GNN_VERSION );
}

/**
 * Install (or heal) the Elementor template library. Hooked to admin_init
 * (runs on every wp-admin request) rather than elementor/loaded: in some
 * environments (e.g. automated/headless installs) elementor/loaded fires
 * without a full admin request ever completing, so it can be missed.
 *
 * Re-runs when either:
 * - The stored "installed" version doesn't match GNN_VERSION (a theme
 *   update may have added/changed templates), or
 * - No GNN-authored templates actually exist any more (e.g. someone
 *   deleted them from Templates → Saved Templates) — checking the version
 *   flag alone isn't enough here, since the flag survives post deletion.
 */
function gnn_maybe_install_elementor_templates() {
	if ( ! post_type_exists( 'elementor_library' ) ) {
		return;
	}

	$version_synced = get_option( 'gnn_elementor_templates_installed' ) === GNN_VERSION;
	$has_templates  = ! empty(
		get_posts(
			array(
				'post_type'      => 'elementor_library',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- admin-only, LIMIT 1, indexed meta key.
					array( 'key' => '_gnn_elementor_template' ),
				),
				'fields'         => 'ids',
			)
		)
	);

	if ( ! $version_synced || ! $has_templates ) {
		gnn_install_elementor_templates();
	}
}
add_action( 'admin_init', 'gnn_maybe_install_elementor_templates' );
