<?php
/**
 * Call-to-action, contact and newsletter sections for the Elementor Free section library.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve the default contact destination URL.
 *
 * @return string
 */
function gnn_sec_default_contact_url() {
	if ( function_exists( 'gnn_contact_url' ) ) {
		return (string) gnn_contact_url();
	}
	return home_url( '/contact/' );
}

/**
 * Resolve the saved public company email or a neutral placeholder address.
 *
 * @return string
 */
function gnn_sec_company_email() {
	$email = function_exists( 'gnn_option' ) ? sanitize_email( (string) gnn_option( 'company_email' ) ) : '';
	return '' !== $email ? $email : 'hello@example.com';
}

/**
 * Build the Call to Action (`cta`) section.
 *
 * Variants: `band`, `split`, `email`.
 *
 * @param array $a Section arguments and overrides.
 * @return array
 */
function gnn_sec_build_cta( $a = array() ) {
	$raw_variant = isset( $a['variant'] ) && is_string( $a['variant'] ) ? sanitize_key( $a['variant'] ) : 'band';
	if ( 'big-email' === $raw_variant || 'bigmail' === $raw_variant ) {
		$raw_variant = 'email';
	}
	$variant = in_array( $raw_variant, array( 'band', 'split', 'email' ), true ) ? $raw_variant : 'band';

	$eyebrow = isset( $a['eyebrow'] ) ? (string) $a['eyebrow'] : __( 'Next step', 'gnn' );
	$title   = ! empty( $a['title'] ) ? (string) $a['title'] : __( 'Ready to talk it through?', 'gnn' );
	$text    = isset( $a['text'] ) ? (string) $a['text'] : __( 'Tell us what you are working on and we will suggest a practical next step.', 'gnn' );

	if ( 'email' === $variant ) {
		$email        = gnn_sec_company_email();
		$button_label = ! empty( $a['button_label'] ) ? (string) $a['button_label'] : $email;
		$button_url   = ! empty( $a['button_url'] ) ? (string) $a['button_url'] : 'mailto:' . $email;
		$children     = array(
			'' !== $eyebrow ? gnn_st_p( $eyebrow, 'gnn-eyebrow' ) : null,
			gnn_st_h( $title, 2 ),
			'' !== $text ? gnn_st_p( $text, 'gnn-lead' ) : null,
			gnn_st_b( $button_label, $button_url, 'gnn-bigmail' ),
		);
	} elseif ( 'split' === $variant ) {
		$button_label = ! empty( $a['button_label'] ) ? (string) $a['button_label'] : __( 'Contact us', 'gnn' );
		$button_url   = ! empty( $a['button_url'] ) ? (string) $a['button_url'] : gnn_sec_default_contact_url();
		$children     = array(
			gnn_st_c(
				'gnn-split',
				array(
					gnn_st_c(
						'gnn-copy',
						array(
							'' !== $eyebrow ? gnn_st_p( $eyebrow, 'gnn-eyebrow' ) : null,
							gnn_st_h( $title, 2 ),
							'' !== $text ? gnn_st_p( $text, 'gnn-lead' ) : null,
						),
						true
					),
					gnn_st_c(
						'gnn-actions',
						array(
							gnn_st_b( $button_label, $button_url, 'gnn-btn-main' ),
						),
						true
					),
				),
				true
			),
		);
	} else {
		$button_label = ! empty( $a['button_label'] ) ? (string) $a['button_label'] : __( 'Contact us', 'gnn' );
		$button_url   = ! empty( $a['button_url'] ) ? (string) $a['button_url'] : gnn_sec_default_contact_url();
		$children     = array(
			'' !== $eyebrow ? gnn_st_p( $eyebrow, 'gnn-eyebrow' ) : null,
			gnn_st_h( $title, 2 ),
			'' !== $text ? gnn_st_p( $text, 'gnn-lead' ) : null,
			gnn_st_c(
				'gnn-actions',
				array(
					gnn_st_b( $button_label, $button_url, 'gnn-btn-main' ),
				),
				true
			),
		);
	}

	$elements                                = array( gnn_st_band( 'gnn-sec', 'cta', $children ) );
	$elements[0]['settings']['css_classes'] .= ' gnn-sec--' . $variant;
	return $elements;
}

/**
 * Build the contact detail cards from saved company options or placeholders.
 *
 * @param array $a Section arguments and overrides.
 * @return array
 */
function gnn_sec_contact_info_cards( $a = array() ) {
	if ( ! empty( $a['items'] ) && is_array( $a['items'] ) ) {
		$custom = array();
		foreach ( $a['items'] as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$card_title = isset( $item['title'] ) ? (string) $item['title'] : ( isset( $item[0] ) ? (string) $item[0] : '' );
			$card_value = isset( $item['text'] ) ? (string) $item['text'] : ( isset( $item[1] ) ? (string) $item[1] : '' );
			$card_url   = isset( $item['url'] ) ? (string) $item['url'] : ( isset( $item[2] ) ? (string) $item[2] : '' );
			if ( '' === $card_title && '' === $card_value ) {
				continue;
			}
			$custom[] = gnn_st_c(
				'gnn-card gnn-contact-card',
				array(
					'' !== $card_title ? gnn_st_h( $card_title, 3 ) : null,
					'' !== $card_url ? gnn_st_b( $card_value, $card_url, 'gnn-link' ) : gnn_st_p( $card_value ),
				),
				true
			);
		}
		if ( ! empty( $custom ) ) {
			return $custom;
		}
	}

	$email = gnn_sec_company_email();
	$phone = function_exists( 'gnn_option' ) ? trim( (string) gnn_option( 'company_phone' ) ) : '';
	$addr  = function_exists( 'gnn_option' ) ? trim( (string) gnn_option( 'company_address' ) ) : '';
	$hours = function_exists( 'gnn_option' ) ? trim( (string) gnn_option( 'company_hours' ) ) : '';

	if ( '' !== $phone && preg_match( '/^\+?[0-9\s().-]{5,30}$/', $phone ) ) {
		$phone_el = gnn_st_b( $phone, 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ), 'gnn-link' );
	} else {
		$phone_el = gnn_st_p( '' !== $phone ? $phone : __( 'Add your phone number here.', 'gnn' ) );
	}

	$cards = array(
		gnn_st_c(
			'gnn-card gnn-contact-card',
			array(
				gnn_st_h( __( 'Email', 'gnn' ), 3 ),
				gnn_st_b( $email, 'mailto:' . $email, 'gnn-link' ),
			),
			true
		),
		gnn_st_c(
			'gnn-card gnn-contact-card',
			array(
				gnn_st_h( __( 'Phone', 'gnn' ), 3 ),
				$phone_el,
			),
			true
		),
		gnn_st_c(
			'gnn-card gnn-contact-card',
			array(
				gnn_st_h( __( 'Address', 'gnn' ), 3 ),
				gnn_st_p( '' !== $addr ? $addr : __( 'Add your address here.', 'gnn' ) ),
				'' !== $hours ? gnn_st_p( $hours, 'gnn-contact-hours' ) : null,
			),
			true
		),
	);

	return $cards;
}

/**
 * Build the Contact (`contact`) section.
 *
 * Variants: `split`, `cards`.
 *
 * @param array $a Section arguments and overrides.
 * @return array
 */
function gnn_sec_build_contact( $a = array() ) {
	$raw_variant = isset( $a['variant'] ) && is_string( $a['variant'] ) ? sanitize_key( $a['variant'] ) : 'split';
	$variant     = in_array( $raw_variant, array( 'split', 'cards' ), true ) ? $raw_variant : 'split';

	$eyebrow    = isset( $a['eyebrow'] ) ? (string) $a['eyebrow'] : __( 'Contact', 'gnn' );
	$title      = ! empty( $a['title'] ) ? (string) $a['title'] : __( 'Get in touch', 'gnn' );
	$text       = isset( $a['text'] ) ? (string) $a['text'] : __( 'Reach us directly using the details below or send a message through your contact form.', 'gnn' );
	$form_title = ! empty( $a['form_title'] ) ? (string) $a['form_title'] : __( 'Send a message', 'gnn' );
	$form_text  = ! empty( $a['form_text'] ) ? (string) $a['form_text'] : __( 'Add your contact form here with the form plugin you use.', 'gnn' );

	$info_cards = gnn_sec_contact_info_cards( $a );
	$form_box   = gnn_st_c(
		'gnn-card gnn-note gnn-form-placeholder',
		array(
			gnn_st_h( $form_title, 3 ),
			gnn_st_p( $form_text ),
		),
		true
	);

	if ( 'cards' === $variant ) {
		$children = array(
			gnn_st_head( $eyebrow, $title, $text ),
			gnn_st_c( 'gnn-cards gnn-cols-3 gnn-contact-cards', $info_cards, true ),
			$form_box,
		);
	} else {
		$children = array(
			gnn_st_c(
				'gnn-split',
				array(
					gnn_st_c(
						'gnn-copy',
						array(
							gnn_st_head( $eyebrow, $title, $text ),
							gnn_st_c( 'gnn-cards gnn-contact-cards', $info_cards, true ),
						),
						true
					),
					$form_box,
				),
				true
			),
		);
	}

	$elements                                = array( gnn_st_band( 'gnn-sec', 'contact', $children ) );
	$elements[0]['settings']['css_classes'] .= ' gnn-sec--' . $variant;
	return $elements;
}

/**
 * Build the Newsletter (`newsletter`) section.
 *
 * Variants: `split`, `centered`.
 *
 * @param array $a Section arguments and overrides.
 * @return array
 */
function gnn_sec_build_newsletter( $a = array() ) {
	$raw_variant = isset( $a['variant'] ) && is_string( $a['variant'] ) ? sanitize_key( $a['variant'] ) : 'split';
	if ( 'inline' === $raw_variant ) {
		$raw_variant = 'split';
	}
	$variant = in_array( $raw_variant, array( 'split', 'centered' ), true ) ? $raw_variant : 'split';

	$eyebrow      = isset( $a['eyebrow'] ) ? (string) $a['eyebrow'] : __( 'Newsletter', 'gnn' );
	$title        = ! empty( $a['title'] ) ? (string) $a['title'] : __( 'Updates worth reading', 'gnn' );
	$text         = isset( $a['text'] ) ? (string) $a['text'] : __( 'Occasional notes, new releases and practical guides. No spam.', 'gnn' );
	$button_label = ! empty( $a['button_label'] ) ? (string) $a['button_label'] : __( 'Subscribe', 'gnn' );
	$button_url   = ! empty( $a['button_url'] ) ? (string) $a['button_url'] : gnn_sec_default_contact_url();
	$placeholder  = ! empty( $a['form_text'] ) ? (string) $a['form_text'] : __( 'Add your newsletter form shortcode or block here with the form plugin you use.', 'gnn' );

	$form_box = gnn_st_c(
		'gnn-card gnn-newsletter-box gnn-form-placeholder',
		array(
			gnn_st_p( $placeholder, 'gnn-newsletter-note' ),
			gnn_st_c(
				'gnn-actions',
				array(
					gnn_st_b( $button_label, $button_url, 'gnn-btn-main' ),
				),
				true
			),
		),
		true
	);

	if ( 'centered' === $variant ) {
		$children = array(
			gnn_st_head( $eyebrow, $title, $text ),
			$form_box,
		);
	} else {
		$children = array(
			gnn_st_c(
				'gnn-split',
				array(
					gnn_st_c(
						'gnn-copy',
						array(
							'' !== $eyebrow ? gnn_st_p( $eyebrow, 'gnn-eyebrow' ) : null,
							gnn_st_h( $title, 2 ),
							'' !== $text ? gnn_st_p( $text, 'gnn-lead' ) : null,
						),
						true
					),
					$form_box,
				),
				true
			),
		);
	}

	$elements                                = array( gnn_st_band( 'gnn-sec', 'newsletter', $children ) );
	$elements[0]['settings']['css_classes'] .= ' gnn-sec--' . $variant;
	return $elements;
}

/**
 * Register CTA, contact and newsletter sections.
 *
 * @return void
 */
function gnn_sections_register_cta() {
	if ( ! function_exists( 'gnn_section_register' ) ) {
		return;
	}

	gnn_section_register(
		'cta',
		array(
			'label'    => __( 'Call to action', 'gnn' ),
			'category' => 'cta',
			'variants' => array(
				'band'  => __( 'Band', 'gnn' ),
				'split' => __( 'Split', 'gnn' ),
				'email' => __( 'Large email link', 'gnn' ),
			),
			'build'    => 'gnn_sec_build_cta',
		)
	);

	gnn_section_register(
		'contact',
		array(
			'label'    => __( 'Contact', 'gnn' ),
			'category' => 'contact',
			'variants' => array(
				'split' => __( 'Split with form', 'gnn' ),
				'cards' => __( 'Cards above form', 'gnn' ),
			),
			'build'    => 'gnn_sec_build_contact',
		)
	);

	gnn_section_register(
		'newsletter',
		array(
			'label'    => __( 'Newsletter', 'gnn' ),
			'category' => 'cta',
			'variants' => array(
				'split'    => __( 'Split', 'gnn' ),
				'centered' => __( 'Centered', 'gnn' ),
			),
			'build'    => 'gnn_sec_build_newsletter',
		)
	);
}
add_action( 'gnn_sections_register', 'gnn_sections_register_cta' );
