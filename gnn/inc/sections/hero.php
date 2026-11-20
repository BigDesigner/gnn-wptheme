<?php
/**
 * Hero section definition and 8 reusable layout variants.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the hero section and its 8 variants.
 *
 * @return void
 */
function gnn_sections_register_hero() {
	gnn_section_register(
		'hero',
		array(
			'label'    => __( 'Hero', 'gnn' ),
			'category' => 'hero',
			'variants' => array(
				'split'             => __( 'Split', 'gnn' ),
				'centered'          => __( 'Centered', 'gnn' ),
				'left-large'        => __( 'Large heading', 'gnn' ),
				'image-bg'          => __( 'Background image', 'gnn' ),
				'minimal'           => __( 'Minimal', 'gnn' ),
				'stacked-cta'       => __( 'Stacked actions', 'gnn' ),
				'video-placeholder' => __( 'Video placeholder', 'gnn' ),
				'cards'             => __( 'Hero with cards', 'gnn' ),
			),
			'build'    => 'gnn_sec_hero_build',
		)
	);
}
add_action( 'gnn_sections_register', 'gnn_sections_register_hero' );

/**
 * Normalize plain-text note items for the stacked-cta hero variant.
 *
 * @param array $items Custom note items passed to the builder.
 * @return array
 */
function gnn_sec_hero_note_items( $items ) {
	$defaults = array(
		__( 'Clear scope and timeline', 'gnn' ),
		__( 'Direct communication', 'gnn' ),
		__( 'Editable in Elementor', 'gnn' ),
	);
	if ( ! is_array( $items ) || empty( $items ) ) {
		$items = $defaults;
	}
	$widgets = array();
	foreach ( $items as $item ) {
		$text = '';
		if ( is_string( $item ) || is_numeric( $item ) ) {
			$text = (string) $item;
		} elseif ( is_array( $item ) ) {
			$text = isset( $item['title'] ) ? (string) $item['title'] : ( isset( $item[0] ) ? (string) $item[0] : '' );
		}
		if ( '' !== trim( $text ) ) {
			$widgets[] = gnn_st_p( $text );
		}
	}
	return $widgets;
}

/**
 * Normalize card pairs for the cards hero variant.
 *
 * @param array $items Custom card items passed to the builder.
 * @return array
 */
function gnn_sec_hero_card_items( $items ) {
	$defaults = array(
		array( __( 'First highlight', 'gnn' ), __( 'Summarize a key service, product area or priority in one short sentence.', 'gnn' ) ),
		array( __( 'Second highlight', 'gnn' ), __( 'Add supporting context that helps visitors choose where to go next.', 'gnn' ) ),
		array( __( 'Third highlight', 'gnn' ), __( 'Keep each card concise so the three columns stay balanced on every screen.', 'gnn' ) ),
	);
	if ( ! is_array( $items ) || empty( $items ) ) {
		return $defaults;
	}
	$cards = array();
	foreach ( array_slice( $items, 0, 6 ) as $item ) {
		if ( is_array( $item ) ) {
			$title   = isset( $item['title'] ) ? (string) $item['title'] : ( isset( $item[0] ) ? (string) $item[0] : __( 'Highlight', 'gnn' ) );
			$text    = isset( $item['text'] ) ? (string) $item['text'] : ( isset( $item[1] ) ? (string) $item[1] : __( 'Add supporting details here.', 'gnn' ) );
			$cards[] = array( $title, $text );
		} elseif ( is_string( $item ) && '' !== trim( $item ) ) {
			$cards[] = array( $item, __( 'Add supporting details here.', 'gnn' ) );
		}
	}
	return ! empty( $cards ) ? $cards : $defaults;
}

/**
 * Build Elementor elements for the hero section.
 *
 * @param array $a Optional overrides: title, eyebrow, text, button_label, button_url, image, items, variant.
 * @return array
 */
function gnn_sec_hero_build( $a = array() ) {
	$a               = is_array( $a ) ? $a : array();
	$valid_variants  = array( 'split', 'centered', 'left-large', 'image-bg', 'minimal', 'stacked-cta', 'video-placeholder', 'cards' );
	$variant         = isset( $a['variant'] ) ? sanitize_key( (string) $a['variant'] ) : 'split';
	$variant         = in_array( $variant, $valid_variants, true ) ? $variant : 'split';
	$eyebrow         = isset( $a['eyebrow'] ) ? (string) $a['eyebrow'] : __( 'Overview', 'gnn' );
	$title           = isset( $a['title'] ) ? (string) $a['title'] : __( 'Clear structure for your next page.', 'gnn' );
	$text            = isset( $a['text'] ) ? (string) $a['text'] : __( 'Replace this introduction with a short summary of what you offer and who it helps.', 'gnn' );
	$button_label    = isset( $a['button_label'] ) ? (string) $a['button_label'] : __( 'Get started', 'gnn' );
	$button_url      = isset( $a['button_url'] ) && '' !== (string) $a['button_url'] ? (string) $a['button_url'] : home_url( '/' );
	$secondary_label = isset( $a['secondary_label'] ) && '' !== (string) $a['secondary_label'] ? (string) $a['secondary_label'] : __( 'Learn more', 'gnn' );
	$secondary_url   = isset( $a['secondary_url'] ) && '' !== (string) $a['secondary_url'] ? (string) $a['secondary_url'] : $button_url;
	$image_url       = isset( $a['image'] ) && '' !== (string) $a['image'] ? (string) $a['image'] : get_theme_file_uri( 'assets/img/placeholder-cover.svg' );
	$image_alt       = isset( $a['image_alt'] ) && '' !== (string) $a['image_alt'] ? (string) $a['image_alt'] : $title;
	$items           = isset( $a['items'] ) && is_array( $a['items'] ) ? $a['items'] : array();

	$image_widget = gnn_st_w(
		'image',
		array(
			'image'      => array(
				'url' => $image_url,
				'id'  => '',
				'alt' => $image_alt,
			),
			'image_size' => 'full',
			'link_to'    => 'none',
		)
	);

	$single_action = gnn_st_c(
		'gnn-actions',
		array(
			'' !== $button_label ? gnn_st_b( $button_label, $button_url, 'gnn-btn-main' ) : null,
		),
		true
	);

	$dual_actions = gnn_st_c(
		'gnn-actions',
		array(
			'' !== $button_label ? gnn_st_b( $button_label, $button_url, 'gnn-btn-main' ) : null,
			'' !== $secondary_label ? gnn_st_b( $secondary_label, $secondary_url, 'gnn-btn-ghost' ) : null,
		),
		true
	);

	$copy_standard = gnn_st_c(
		'gnn-copy',
		array(
			'' !== $eyebrow ? gnn_st_p( $eyebrow, 'gnn-eyebrow' ) : null,
			gnn_st_h( $title, 1 ),
			'' !== $text ? gnn_st_p( $text, 'gnn-lead' ) : null,
			$dual_actions,
		),
		true
	);

	switch ( $variant ) {
		case 'centered':
			$children = array(
				gnn_st_c(
					'gnn-copy gnn-sec-hero__center',
					array(
						'' !== $eyebrow ? gnn_st_p( $eyebrow, 'gnn-eyebrow' ) : null,
						gnn_st_h( $title, 1 ),
						'' !== $text ? gnn_st_p( $text, 'gnn-lead' ) : null,
						$dual_actions,
					),
					true
				),
			);
			break;

		case 'left-large':
			$children = array(
				gnn_st_c(
					'gnn-copy gnn-sec-hero__large',
					array(
						'' !== $eyebrow ? gnn_st_p( $eyebrow, 'gnn-eyebrow' ) : null,
						gnn_st_h( $title, 1 ),
						gnn_st_c(
							'gnn-sec-hero__foot',
							array(
								'' !== $text ? gnn_st_p( $text, 'gnn-lead' ) : null,
								$single_action,
							),
							true
						),
					),
					true
				),
			);
			break;

		case 'image-bg':
			$children = array(
				gnn_st_c(
					'gnn-sec-hero__stage',
					array(
						gnn_st_c( 'gnn-visual gnn-sec-hero__bg', array( $image_widget ), true ),
						gnn_st_c(
							'gnn-copy gnn-sec-hero__overlay',
							array(
								'' !== $eyebrow ? gnn_st_p( $eyebrow, 'gnn-eyebrow' ) : null,
								gnn_st_h( $title, 1 ),
								'' !== $text ? gnn_st_p( $text, 'gnn-lead' ) : null,
								$single_action,
							),
							true
						),
					),
					true
				),
			);
			break;

		case 'minimal':
			$children = array(
				gnn_st_c(
					'gnn-copy gnn-sec-hero__minimal',
					array(
						'' !== $eyebrow ? gnn_st_p( $eyebrow, 'gnn-eyebrow' ) : null,
						gnn_st_h( $title, 1 ),
						'' !== $text ? gnn_st_p( $text, 'gnn-lead' ) : null,
						$single_action,
					),
					true
				),
			);
			break;

		case 'stacked-cta':
			$children = array(
				gnn_st_c(
					'gnn-copy gnn-sec-hero__stack',
					array(
						'' !== $eyebrow ? gnn_st_p( $eyebrow, 'gnn-eyebrow' ) : null,
						gnn_st_h( $title, 1 ),
						'' !== $text ? gnn_st_p( $text, 'gnn-lead' ) : null,
						$dual_actions,
						gnn_st_c( 'gnn-ticks gnn-sec-hero__notes', gnn_sec_hero_note_items( $items ), true ),
					),
					true
				),
			);
			break;

		case 'video-placeholder':
			$children = array(
				gnn_st_c(
					'gnn-split',
					array(
						$copy_standard,
						gnn_st_c(
							'gnn-visual gnn-sec-hero__video',
							array(
								$image_widget,
								gnn_st_p( __( 'Watch overview', 'gnn' ), 'gnn-sec-hero__play' ),
							),
							true
						),
					),
					true
				),
			);
			break;

		case 'cards':
			$children = array(
				gnn_st_c(
					'gnn-split gnn-sec-hero__top',
					array(
						$copy_standard,
						gnn_st_c( 'gnn-visual', array( $image_widget ), true ),
					),
					true
				),
				gnn_st_cards( gnn_sec_hero_card_items( $items ), 'gnn-cols-3 gnn-sec-hero__cards' ),
			);
			break;

		case 'split':
		default:
			$children = array(
				gnn_st_c(
					'gnn-split',
					array(
						$copy_standard,
						gnn_st_c( 'gnn-visual', array( $image_widget ), true ),
					),
					true
				),
			);
			break;
	}

	$band                             = gnn_st_band( 'gnn-sec', 'hero', $children );
	$band['settings']['css_classes'] .= ' gnn-sec--' . $variant;
	return array( $band );
}
