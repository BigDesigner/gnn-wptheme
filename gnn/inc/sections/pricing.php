<?php
/**
 * Pricing, FAQ, team, logo strip and testimonial sections.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers pricing, FAQ, team, logos and testimonial sections.
 *
 * @return void
 */
function gnn_sections_register_pricing() {
	if ( ! function_exists( 'gnn_section_register' ) ) {
		return;
	}

	gnn_section_register(
		'pricing',
		array(
			'label'    => __( 'Pricing plans', 'gnn' ),
			'category' => 'pricing',
			'variants' => array(
				'three' => __( 'Three plans', 'gnn' ),
				'two'   => __( 'Two plans', 'gnn' ),
			),
			'build'    => 'gnn_sec_pricing_build',
		)
	);

	gnn_section_register(
		'faq',
		array(
			'label'    => __( 'Frequently asked questions', 'gnn' ),
			'category' => 'faq',
			'variants' => array(
				'single'     => __( 'Single column', 'gnn' ),
				'two-column' => __( 'Two columns', 'gnn' ),
			),
			'build'    => 'gnn_sec_faq_build',
		)
	);

	gnn_section_register(
		'team',
		array(
			'label'    => __( 'Team members', 'gnn' ),
			'category' => 'team',
			'variants' => array(
				'four'  => __( 'Four columns', 'gnn' ),
				'three' => __( 'Three columns', 'gnn' ),
			),
			'build'    => 'gnn_sec_team_build',
		)
	);

	gnn_section_register(
		'logos',
		array(
			'label'    => __( 'Logo strip', 'gnn' ),
			'category' => 'logos',
			'variants' => array(
				'strip' => __( 'Logo row', 'gnn' ),
				'grid'  => __( 'Logo grid', 'gnn' ),
			),
			'build'    => 'gnn_sec_logos_build',
		)
	);

	gnn_section_register(
		'testimonial',
		array(
			'label'    => __( 'Customer quotes', 'gnn' ),
			'category' => 'pricing',
			'variants' => array(
				'cards'    => __( 'Quote cards', 'gnn' ),
				'featured' => __( 'Featured quote', 'gnn' ),
			),
			'build'    => 'gnn_sec_testimonial_build',
		)
	);
}
add_action( 'gnn_sections_register', 'gnn_sections_register_pricing' );

/**
 * Builds the pricing section elements.
 *
 * @param array $a Section arguments and overrides.
 * @return array
 */
function gnn_sec_pricing_build( $a = array() ) {
	$a            = is_array( $a ) ? $a : array();
	$variant      = isset( $a['variant'] ) && in_array( $a['variant'], array( 'three', 'two' ), true ) ? $a['variant'] : 'three';
	$eyebrow      = isset( $a['eyebrow'] ) ? (string) $a['eyebrow'] : __( 'Pricing', 'gnn' );
	$title        = isset( $a['title'] ) ? (string) $a['title'] : __( 'Clear plans for every stage', 'gnn' );
	$text         = isset( $a['text'] ) ? (string) $a['text'] : __( 'Replace the placeholder rates below with your own monthly or annual pricing.', 'gnn' );
	$button_label = isset( $a['button_label'] ) ? (string) $a['button_label'] : __( 'Choose plan', 'gnn' );
	$button_url   = isset( $a['button_url'] ) ? (string) $a['button_url'] : home_url( '/' );

	$default_three = array(
		array(
			'title'    => __( 'Starter', 'gnn' ),
			'text'     => __( 'Essential scope for small projects and initial setups.', 'gnn' ),
			'price'    => __( 'Add your price here.', 'gnn' ),
			'period'   => __( 'Monthly / Annual', 'gnn' ),
			'featured' => false,
			'features' => array(
				__( 'Core setup and configuration', 'gnn' ),
				__( 'Standard documentation', 'gnn' ),
				__( 'Email support', 'gnn' ),
			),
		),
		array(
			'title'    => __( 'Professional', 'gnn' ),
			'text'     => __( 'Complete delivery for growing teams and active operations.', 'gnn' ),
			'price'    => __( 'Add your price here.', 'gnn' ),
			'period'   => __( 'Monthly / Annual', 'gnn' ),
			'featured' => true,
			'features' => array(
				__( 'Everything in Starter', 'gnn' ),
				__( 'Custom workflow setup', 'gnn' ),
				__( 'Scheduled review sessions', 'gnn' ),
			),
		),
		array(
			'title'    => __( 'Custom', 'gnn' ),
			'text'     => __( 'Tailored scope, dedicated coordination and ongoing advisory.', 'gnn' ),
			'price'    => __( 'Add your price here.', 'gnn' ),
			'period'   => __( 'Monthly / Annual', 'gnn' ),
			'featured' => false,
			'features' => array(
				__( 'Everything in Professional', 'gnn' ),
				__( 'Dedicated account lead', 'gnn' ),
				__( 'Priority response times', 'gnn' ),
			),
		),
	);

	$default_two = array(
		$default_three[0],
		$default_three[1],
	);

	$items = isset( $a['items'] ) && is_array( $a['items'] ) && ! empty( $a['items'] )
		? array_values( $a['items'] )
		: ( 'two' === $variant ? $default_two : $default_three );

	$cards = array();
	foreach ( $items as $index => $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}
		$plan_title    = isset( $item['title'] ) ? (string) $item['title'] : ( isset( $item[0] ) ? (string) $item[0] : __( 'Plan', 'gnn' ) );
		$plan_text     = isset( $item['text'] ) ? (string) $item['text'] : ( isset( $item['description'] ) ? (string) $item['description'] : ( isset( $item[1] ) ? (string) $item[1] : '' ) );
		$plan_price    = isset( $item['price'] ) ? (string) $item['price'] : ( isset( $item[2] ) ? (string) $item[2] : __( 'Add your price here.', 'gnn' ) );
		$plan_period   = isset( $item['period'] ) ? (string) $item['period'] : __( 'Monthly / Annual', 'gnn' );
		$plan_btn_text = isset( $item['button_label'] ) ? (string) $item['button_label'] : $button_label;
		$plan_btn_url  = isset( $item['button_url'] ) ? (string) $item['button_url'] : $button_url;
		$is_featured   = isset( $item['featured'] ) ? (bool) $item['featured'] : ( 1 === $index );
		$features      = isset( $item['features'] ) && is_array( $item['features'] ) ? $item['features'] : array();

		$tick_widgets = array();
		foreach ( $features as $feature ) {
			if ( is_string( $feature ) && '' !== $feature ) {
				$tick_widgets[] = gnn_st_p( $feature );
			}
		}

		$card_class = 'gnn-card gnn-sec-plan' . ( $is_featured ? ' is-featured' : '' );
		$btn_class  = $is_featured ? 'gnn-btn-main' : 'gnn-btn-ghost';

		$cards[] = gnn_st_c(
			$card_class,
			array(
				$is_featured ? gnn_st_p( __( 'Recommended', 'gnn' ), 'gnn-eyebrow gnn-sec-plan-badge' ) : null,
				gnn_st_h( $plan_title, 3 ),
				'' !== $plan_text ? gnn_st_p( $plan_text, 'gnn-sec-plan-desc' ) : null,
				gnn_st_p( $plan_price, 'gnn-price' ),
				'' !== $plan_period ? gnn_st_p( $plan_period, 'gnn-sec-plan-period' ) : null,
				! empty( $tick_widgets ) ? gnn_st_c( 'gnn-ticks gnn-sec-plan-ticks', $tick_widgets, true ) : null,
				gnn_st_b( $plan_btn_text, $plan_btn_url, $btn_class ),
			),
			true
		);
	}

	$billing_bar = gnn_st_c(
		'gnn-chips gnn-sec-billing',
		array(
			gnn_st_p( __( 'Monthly', 'gnn' ), 'gnn-chip' ),
			gnn_st_p( __( 'Annual', 'gnn' ), 'gnn-chip' ),
		),
		true
	);

	$cols_class = 'two' === $variant ? 'gnn-cards gnn-cols-2' : 'gnn-cards gnn-cols-3';
	$elements   = array(
		gnn_st_band(
			'gnn-sec',
			'pricing',
			array(
				gnn_st_head( $eyebrow, $title, $text ),
				$billing_bar,
				gnn_st_c( $cols_class, $cards, true ),
			)
		),
	);

	$elements[0]['settings']['css_classes'] .= ' gnn-sec--' . $variant;
	return $elements;
}

/**
 * Builds the FAQ section elements using accessible details and summary markup.
 *
 * @param array $a Section arguments and overrides.
 * @return array
 */
function gnn_sec_faq_build( $a = array() ) {
	$a       = is_array( $a ) ? $a : array();
	$variant = isset( $a['variant'] ) && in_array( $a['variant'], array( 'single', 'two-column' ), true ) ? $a['variant'] : 'single';
	$eyebrow = isset( $a['eyebrow'] ) ? (string) $a['eyebrow'] : __( 'FAQ', 'gnn' );
	$title   = isset( $a['title'] ) ? (string) $a['title'] : __( 'Frequently asked questions', 'gnn' );
	$text    = isset( $a['text'] ) ? (string) $a['text'] : __( 'Common questions about scope, timelines and how we work.', 'gnn' );

	$default_items = array(
		array(
			'question' => __( 'How do we get started?', 'gnn' ),
			'answer'   => __( 'Share your goals and timeline through the contact page. We review your request and suggest the next step.', 'gnn' ),
		),
		array(
			'question' => __( 'Can the scope be adjusted later?', 'gnn' ),
			'answer'   => __( 'Yes. We review progress at each milestone and agree on any changes before moving forward.', 'gnn' ),
		),
		array(
			'question' => __( 'What is included in a standard engagement?', 'gnn' ),
			'answer'   => __( 'Every engagement includes initial planning, regular updates and clear handover documentation.', 'gnn' ),
		),
		array(
			'question' => __( 'How does ongoing support work?', 'gnn' ),
			'answer'   => __( 'After completion, you can choose scheduled check-ins or a monthly support arrangement.', 'gnn' ),
		),
	);

	$items = isset( $a['items'] ) && is_array( $a['items'] ) && ! empty( $a['items'] )
		? array_values( $a['items'] )
		: $default_items;

	$faq_widgets = array();
	foreach ( $items as $index => $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}
		$question = isset( $item['question'] ) ? (string) $item['question'] : ( isset( $item['title'] ) ? (string) $item['title'] : ( isset( $item[0] ) ? (string) $item[0] : '' ) );
		$answer   = isset( $item['answer'] ) ? (string) $item['answer'] : ( isset( $item['text'] ) ? (string) $item['text'] : ( isset( $item[1] ) ? (string) $item[1] : '' ) );
		if ( '' === $question ) {
			continue;
		}

		$open_attr = 0 === $index ? ' open' : '';
		$html      = '<details class="gnn-sec-details"' . $open_attr . '>'
			. '<summary class="gnn-sec-summary">' . esc_html( $question ) . '</summary>'
			. '<div class="gnn-sec-answer"><p>' . esc_html( $answer ) . '</p></div>'
			. '</details>';

		$faq_widgets[] = gnn_st_html( $html, 'gnn-sec-faq-item' );
	}

	$list_class = 'two-column' === $variant ? 'gnn-cards gnn-cols-2 gnn-sec-faq-list' : 'gnn-cards gnn-sec-faq-list';
	$elements   = array(
		gnn_st_band(
			'gnn-sec',
			'faq',
			array(
				gnn_st_head( $eyebrow, $title, $text ),
				gnn_st_c( $list_class, $faq_widgets, true ),
			)
		),
	);

	$elements[0]['settings']['css_classes'] .= ' gnn-sec--' . $variant;
	return $elements;
}

/**
 * Builds the team section elements with placeholder member profiles.
 *
 * @param array $a Section arguments and overrides.
 * @return array
 */
function gnn_sec_team_build( $a = array() ) {
	$a             = is_array( $a ) ? $a : array();
	$variant       = isset( $a['variant'] ) && in_array( $a['variant'], array( 'four', 'three' ), true ) ? $a['variant'] : 'four';
	$eyebrow       = isset( $a['eyebrow'] ) ? (string) $a['eyebrow'] : __( 'Team', 'gnn' );
	$title         = isset( $a['title'] ) ? (string) $a['title'] : __( 'People you will work with', 'gnn' );
	$text          = isset( $a['text'] ) ? (string) $a['text'] : __( 'Replace these placeholder cards with your team members and their roles.', 'gnn' );
	$default_image = isset( $a['image'] ) && '' !== (string) $a['image']
		? (string) $a['image']
		: get_theme_file_uri( 'assets/img/placeholder-cover.svg' );

	$default_four = array(
		array(
			'name' => __( 'Name Surname', 'gnn' ),
			'role' => __( 'Managing partner', 'gnn' ),
			'bio'  => __( 'Add a short biography or role summary here.', 'gnn' ),
		),
		array(
			'name' => __( 'Name Surname', 'gnn' ),
			'role' => __( 'Head of strategy', 'gnn' ),
			'bio'  => __( 'Add a short biography or role summary here.', 'gnn' ),
		),
		array(
			'name' => __( 'Name Surname', 'gnn' ),
			'role' => __( 'Head of delivery', 'gnn' ),
			'bio'  => __( 'Add a short biography or role summary here.', 'gnn' ),
		),
		array(
			'name' => __( 'Name Surname', 'gnn' ),
			'role' => __( 'Client director', 'gnn' ),
			'bio'  => __( 'Add a short biography or role summary here.', 'gnn' ),
		),
	);

	$default_three = array(
		$default_four[0],
		$default_four[1],
		$default_four[2],
	);

	$items = isset( $a['items'] ) && is_array( $a['items'] ) && ! empty( $a['items'] )
		? array_values( $a['items'] )
		: ( 'three' === $variant ? $default_three : $default_four );

	$cards = array();
	foreach ( $items as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}
		$member_name = isset( $item['name'] ) ? (string) $item['name'] : ( isset( $item['title'] ) ? (string) $item['title'] : ( isset( $item[0] ) ? (string) $item[0] : __( 'Name Surname', 'gnn' ) ) );
		$member_role = isset( $item['role'] ) ? (string) $item['role'] : ( isset( $item[1] ) ? (string) $item[1] : __( 'Team member', 'gnn' ) );
		$member_bio  = isset( $item['bio'] ) ? (string) $item['bio'] : ( isset( $item['text'] ) ? (string) $item['text'] : ( isset( $item[2] ) ? (string) $item[2] : '' ) );
		$member_img  = isset( $item['image'] ) && '' !== (string) $item['image'] ? (string) $item['image'] : $default_image;

		$image_widget = gnn_st_w(
			'image',
			array(
				'image'      => array(
					'url' => esc_url_raw( $member_img ),
					'id'  => '',
					'alt' => $member_name,
				),
				'image_size' => 'full',
				'link_to'    => 'none',
			)
		);

		$cards[] = gnn_st_c(
			'gnn-card gnn-person gnn-sec-member',
			array(
				gnn_st_c( 'gnn-visual gnn-sec-member-photo', array( $image_widget ), true ),
				gnn_st_h( $member_name, 3, 'gnn-name' ),
				gnn_st_p( $member_role, 'gnn-sec-member-role' ),
				'' !== $member_bio ? gnn_st_p( $member_bio, 'gnn-sec-member-bio' ) : null,
			),
			true
		);
	}

	$cols_class = 'three' === $variant ? 'gnn-cards gnn-cols-3' : 'gnn-cards gnn-cols-4';
	$elements   = array(
		gnn_st_band(
			'gnn-sec',
			'team',
			array(
				gnn_st_head( $eyebrow, $title, $text ),
				gnn_st_c( $cols_class, $cards, true ),
			)
		),
	);

	$elements[0]['settings']['css_classes'] .= ' gnn-sec--' . $variant;
	return $elements;
}

/**
 * Builds the logo strip section elements using neutral placeholder boxes.
 *
 * @param array $a Section arguments and overrides.
 * @return array
 */
function gnn_sec_logos_build( $a = array() ) {
	$a       = is_array( $a ) ? $a : array();
	$variant = isset( $a['variant'] ) && in_array( $a['variant'], array( 'strip', 'grid' ), true ) ? $a['variant'] : 'strip';
	$eyebrow = isset( $a['eyebrow'] ) ? (string) $a['eyebrow'] : __( 'Partners and clients', 'gnn' );
	$title   = isset( $a['title'] ) ? (string) $a['title'] : __( 'Organisations we work with', 'gnn' );
	$text    = isset( $a['text'] ) ? (string) $a['text'] : __( 'Replace these boxes with your own partner or client logos.', 'gnn' );

	$default_items = array(
		__( 'Your logo', 'gnn' ),
		__( 'Your logo', 'gnn' ),
		__( 'Your logo', 'gnn' ),
		__( 'Your logo', 'gnn' ),
		__( 'Your logo', 'gnn' ),
		__( 'Your logo', 'gnn' ),
	);

	$items = isset( $a['items'] ) && is_array( $a['items'] ) && ! empty( $a['items'] )
		? array_values( $a['items'] )
		: $default_items;

	$boxes = array();
	foreach ( $items as $item ) {
		$label = __( 'Your logo', 'gnn' );
		if ( is_string( $item ) && '' !== $item ) {
			$label = $item;
		} elseif ( is_array( $item ) && isset( $item['label'] ) && '' !== (string) $item['label'] ) {
			$label = (string) $item['label'];
		} elseif ( is_array( $item ) && isset( $item['title'] ) && '' !== (string) $item['title'] ) {
			$label = (string) $item['title'];
		}

		$boxes[] = gnn_st_c(
			'gnn-card gnn-sec-logo-box',
			array(
				gnn_st_p( $label, 'gnn-sec-logo-label' ),
			),
			true
		);
	}

	$children = 'grid' === $variant
		? array(
			gnn_st_head( $eyebrow, $title, $text ),
			gnn_st_c( 'gnn-cards gnn-cols-3 gnn-sec-logo-grid', $boxes, true ),
		)
		: array(
			gnn_st_p( $eyebrow, 'gnn-eyebrow' ),
			gnn_st_c( 'gnn-cards gnn-sec-logo-strip', $boxes, true ),
		);

	$elements = array(
		gnn_st_band( 'gnn-sec', 'logos', $children ),
	);

	$elements[0]['settings']['css_classes'] .= ' gnn-sec--' . $variant;
	return $elements;
}

/**
 * Builds the testimonial section elements with explicit quote placeholders.
 *
 * @param array $a Section arguments and overrides.
 * @return array
 */
function gnn_sec_testimonial_build( $a = array() ) {
	$a       = is_array( $a ) ? $a : array();
	$variant = isset( $a['variant'] ) && in_array( $a['variant'], array( 'cards', 'featured' ), true ) ? $a['variant'] : 'cards';
	$eyebrow = isset( $a['eyebrow'] ) ? (string) $a['eyebrow'] : __( 'Testimonials', 'gnn' );
	$title   = isset( $a['title'] ) ? (string) $a['title'] : __( 'What clients say', 'gnn' );
	$text    = isset( $a['text'] ) ? (string) $a['text'] : __( 'Replace these placeholders with verified feedback from your customers.', 'gnn' );

	$default_items = array(
		array(
			'quote'  => __( 'Add a customer quote here.', 'gnn' ),
			'author' => __( 'Name Surname', 'gnn' ),
			'role'   => __( 'Role or company', 'gnn' ),
		),
		array(
			'quote'  => __( 'Add a customer quote here.', 'gnn' ),
			'author' => __( 'Name Surname', 'gnn' ),
			'role'   => __( 'Role or company', 'gnn' ),
		),
		array(
			'quote'  => __( 'Add a customer quote here.', 'gnn' ),
			'author' => __( 'Name Surname', 'gnn' ),
			'role'   => __( 'Role or company', 'gnn' ),
		),
	);

	$items = isset( $a['items'] ) && is_array( $a['items'] ) && ! empty( $a['items'] )
		? array_values( $a['items'] )
		: $default_items;

	if ( 'featured' === $variant ) {
		$first  = isset( $items[0] ) && is_array( $items[0] ) ? $items[0] : $default_items[0];
		$quote  = isset( $first['quote'] ) ? (string) $first['quote'] : ( isset( $first['text'] ) ? (string) $first['text'] : ( isset( $first[0] ) ? (string) $first[0] : __( 'Add a customer quote here.', 'gnn' ) ) );
		$author = isset( $first['author'] ) ? (string) $first['author'] : ( isset( $first['name'] ) ? (string) $first['name'] : ( isset( $first[1] ) ? (string) $first[1] : __( 'Name Surname', 'gnn' ) ) );
		$role   = isset( $first['role'] ) ? (string) $first['role'] : ( isset( $first[2] ) ? (string) $first[2] : __( 'Role or company', 'gnn' ) );

		$featured_card = gnn_st_c(
			'gnn-card gnn-sec-quote gnn-sec-quote--featured',
			array(
				gnn_st_p( $eyebrow, 'gnn-eyebrow' ),
				gnn_st_p( $quote, 'gnn-sec-quote-text' ),
				gnn_st_h( $author, 3, 'gnn-name' ),
				gnn_st_p( $role, 'gnn-sec-quote-role' ),
			),
			true
		);

		$elements                                = array(
			gnn_st_band( 'gnn-sec', 'testimonial', array( $featured_card ) ),
		);
		$elements[0]['settings']['css_classes'] .= ' gnn-sec--' . $variant;
		return $elements;
	}

	$cards = array();
	foreach ( $items as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}
		$quote  = isset( $item['quote'] ) ? (string) $item['quote'] : ( isset( $item['text'] ) ? (string) $item['text'] : ( isset( $item[0] ) ? (string) $item[0] : __( 'Add a customer quote here.', 'gnn' ) ) );
		$author = isset( $item['author'] ) ? (string) $item['author'] : ( isset( $item['name'] ) ? (string) $item['name'] : ( isset( $item[1] ) ? (string) $item[1] : __( 'Name Surname', 'gnn' ) ) );
		$role   = isset( $item['role'] ) ? (string) $item['role'] : ( isset( $item[2] ) ? (string) $item[2] : __( 'Role or company', 'gnn' ) );

		$cards[] = gnn_st_c(
			'gnn-card gnn-sec-quote',
			array(
				gnn_st_p( $quote, 'gnn-sec-quote-text' ),
				gnn_st_h( $author, 3, 'gnn-name' ),
				gnn_st_p( $role, 'gnn-sec-quote-role' ),
			),
			true
		);
	}

	$elements = array(
		gnn_st_band(
			'gnn-sec',
			'testimonial',
			array(
				gnn_st_head( $eyebrow, $title, $text ),
				gnn_st_c( 'gnn-cards gnn-cols-3', $cards, true ),
			)
		),
	);

	$elements[0]['settings']['css_classes'] .= ' gnn-sec--' . $variant;
	return $elements;
}
