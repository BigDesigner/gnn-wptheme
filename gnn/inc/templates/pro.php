<?php
/**
 * Ready-made site template functions.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Starter profile settings. */
function gnn_st_profile_pro() {
	return array(
		'label'        => __( 'GNNpro', 'gnn' ),
		'accent'       => '#2f7fb8',
		'accent_light' => '#1f4e79',
		'header'       => 'classic',
		'radius'       => 4,
		'font'         => 'manrope',
		'mode'         => 'light',
		'light'        => array( '#ffffff', '#f4f6f9', '#e6ebf1', '#12263a', '#4f5f70', '#dbe2ea' ),
		'dark'         => array( '#0c1621', '#121f2d', '#1a2a3b', '#eef3f8', '#9fb0c2', '#233447' ),
		'options'      => array(
			'header_height'     => 78,
			'topbar_enable'     => 1,
			'topbar_text'       => __( 'Advisory and delivery for growing organisations', 'gnn' ),
			'header_cta_label'  => __( 'Book a consultation', 'gnn' ),
			'show_cart'         => 0,
			'portfolio_columns' => 3,
		),
		'heading'      => __( 'Expertise that moves you forward.', 'gnn' ),
		'description'  => __( 'A complete business website for companies, teams and professional services.', 'gnn' ),
	);
}

/** Page keys and titles. */
function gnn_st_pages_pro() {
	return array(
		'home'     => __( 'Home', 'gnn' ),
		'about'    => __( 'About', 'gnn' ),
		'services' => __( 'Services', 'gnn' ),
		'cases'    => __( 'Case studies', 'gnn' ),
		'news'     => __( 'News', 'gnn' ),
		'contact'  => __( 'Contact', 'gnn' ),
	);
}

/** Bundled demo data. */
function gnn_dp_data_pro() {
	return array(
		'images'   => array( 'hero', 'about', 'case-1', 'case-2', 'case-3', 'news-1', 'news-2', 'news-3' ),
		'projects' => array(
			'case-1' => array( __( 'Operating model redesign', 'gnn' ), __( 'A regional finance group simplified approvals and reporting across five departments.', 'gnn' ), __( 'Finance', 'gnn' ), '2025' ),
			'case-2' => array( __( 'Patient flow programme', 'gnn' ), __( 'A hospital network shortened waiting times by redesigning how referrals move between teams.', 'gnn' ), __( 'Health', 'gnn' ), '2025' ),
			'case-3' => array( __( 'Platform migration roadmap', 'gnn' ), __( 'A technology company planned a staged move to a new platform without interrupting customers.', 'gnn' ), __( 'Technology', 'gnn' ), '2026' ),
		),
		'articles' => array(
			array( __( 'Making a decision framework people actually use', 'gnn' ), __( 'A good framework is short, visible and owned by the people who use it. Start with the three questions every decision must answer.', 'gnn' ) ),
			array( __( 'How to run a change programme without losing momentum', 'gnn' ), __( 'Momentum comes from small, visible wins. Plan the first ninety days around results your team can see.', 'gnn' ) ),
			array( __( 'What to ask before hiring an advisor', 'gnn' ), __( 'Ask about scope, who does the work and how success will be measured. Clear answers save time and money later.', 'gnn' ) ),
		),
		'slides'   => array(),
		'products' => array(),
	);
}

/* GNNpro - corporate and professional services. */

/**
 * Build the pro home page.
 *
 * @param array $ids Installed page IDs.
 */
function gnn_st_pro_home( $ids ) {
	$s       = 'gnn-pro';
	$contact = gnn_st_link( $ids, 'contact' );
	$sectors = array();
	foreach ( array( __( 'Finance', 'gnn' ), __( 'Health', 'gnn' ), __( 'Technology', 'gnn' ), __( 'Public sector', 'gnn' ), __( 'Industry', 'gnn' ), __( 'Education', 'gnn' ) ) as $sector ) {
		$sectors[] = gnn_st_p( $sector, 'gnn-chip' );
	}
	return array(
		gnn_st_c(
			$s . ' ' . $s . '-hero',
			array(
				gnn_st_c(
					'gnn-wrap gnn-split',
					array(
						gnn_st_c(
							'gnn-copy',
							array(
								gnn_st_p( __( 'Advisory and delivery', 'gnn' ), 'gnn-eyebrow' ),
								gnn_st_h( __( 'Clear advice. Steady delivery.', 'gnn' ), 1 ),
								gnn_st_p( __( 'We help organisations decide with confidence and carry the work through, from first assessment to lasting results.', 'gnn' ), 'gnn-lead' ),
								gnn_st_c( 'gnn-actions', array( gnn_st_b( __( 'Book a consultation', 'gnn' ), $contact, 'gnn-btn-main' ), gnn_st_b( __( 'Our services', 'gnn' ), gnn_st_link( $ids, 'services' ), 'gnn-btn-ghost' ) ), true ),
								gnn_st_c( 'gnn-ticks', array( gnn_st_p( __( 'Independent view', 'gnn' ) ), gnn_st_p( __( 'Plain-language reports', 'gnn' ) ), gnn_st_p( __( 'One accountable team', 'gnn' ) ) ), true ),
							),
							true
						),
						gnn_st_c( 'gnn-visual', array( gnn_st_img( 'pro', 'hero', __( 'Abstract city towers at dusk', 'gnn' ) ), gnn_st_p( __( 'Strategy · Delivery · Support', 'gnn' ), 'gnn-float' ) ), true ),
					),
					true
				),
			)
		),
		gnn_st_band( $s, 'sectors', array( gnn_st_p( __( 'Sectors we work in', 'gnn' ), 'gnn-eyebrow' ), gnn_st_c( 'gnn-chips', $sectors, true ) ) ),
		gnn_st_band(
			$s,
			'services',
			array(
				gnn_st_head( __( 'What we do', 'gnn' ), __( 'Services built around your goals', 'gnn' ), __( 'Choose a single engagement or a longer partnership.', 'gnn' ) ),
				gnn_st_cards(
					array(
						array( __( 'Strategy', 'gnn' ), __( 'Assess where you are, define where you are going and agree the route.', 'gnn' ) ),
						array( __( 'Operations', 'gnn' ), __( 'Improve how work flows through your teams, systems and suppliers.', 'gnn' ) ),
						array( __( 'Transformation', 'gnn' ), __( 'Plan and lead change programmes with clear milestones and owners.', 'gnn' ) ),
					),
					'gnn-cols-3',
					gnn_st_link( $ids, 'services' ),
					__( 'Learn more', 'gnn' )
				),
			)
		),
		gnn_st_band(
			$s,
			'about',
			array(
				gnn_st_c(
					'gnn-split gnn-split--flip',
					array(
						gnn_st_c( 'gnn-visual', array( gnn_st_img( 'pro', 'about', __( 'Abstract concentric arcs', 'gnn' ) ) ), true ),
						gnn_st_c(
							'gnn-copy',
							array(
								gnn_st_p( __( 'Why teams choose us', 'gnn' ), 'gnn-eyebrow' ),
								gnn_st_h( __( 'Senior people on every engagement', 'gnn' ), 2 ),
								gnn_st_p( __( 'You work with the experts you meet at the start. We listen first, explain the options and agree the scope before any work begins.', 'gnn' ) ),
								gnn_st_c( 'gnn-ticks', array( gnn_st_p( __( 'Fixed scope and clear fees', 'gnn' ) ), gnn_st_p( __( 'Regular progress reviews', 'gnn' ) ), gnn_st_p( __( 'Handover your team can run', 'gnn' ) ) ), true ),
								gnn_st_b( __( 'About the firm', 'gnn' ), gnn_st_link( $ids, 'about' ), 'gnn-btn-ghost' ),
							),
							true
						),
					),
					true
				),
			)
		),
		gnn_st_band(
			$s,
			'process',
			array(
				gnn_st_head( __( 'How it works', 'gnn' ), __( 'A simple path from question to result', 'gnn' ) ),
				gnn_st_cards(
					array(
						array( __( 'Listen', 'gnn' ), __( 'We learn your goals, limits and what success looks like.', 'gnn' ) ),
						array( __( 'Plan', 'gnn' ), __( 'We propose options, a timeline and the people involved.', 'gnn' ) ),
						array( __( 'Deliver', 'gnn' ), __( 'We do the work alongside your team and report as we go.', 'gnn' ) ),
						array( __( 'Review', 'gnn' ), __( 'We measure the outcome and agree what comes next.', 'gnn' ) ),
					),
					'gnn-cols-4 gnn-steps'
				),
			)
		),
		gnn_st_band(
			$s,
			'cases',
			array(
				gnn_st_head( __( 'Selected work', 'gnn' ), __( 'Recent case studies', 'gnn' ) ),
				gnn_st_mod(
					'portfolio',
					array(
						'columns' => 3,
						'limit'   => 3,
						'filters' => '',
					)
				),
				gnn_st_b( __( 'All case studies', 'gnn' ), gnn_st_link( $ids, 'cases' ), 'gnn-btn-ghost' ),
			)
		),
		gnn_st_cta( $s, __( 'Ready to talk it through?', 'gnn' ), __( 'Contact us', 'gnn' ), $contact, __( 'Tell us what you are working on. We reply within a working day.', 'gnn' ) ),
	);
}

/**
 * Build the pro about page.
 *
 * @param array $ids Installed page IDs.
 */
function gnn_st_pro_about( $ids ) {
	$s    = 'gnn-pro';
	$team = array();
	foreach ( array( __( 'Managing partner', 'gnn' ), __( 'Head of strategy', 'gnn' ), __( 'Head of delivery', 'gnn' ), __( 'Client director', 'gnn' ) ) as $role ) {
		$team[] = gnn_st_c( 'gnn-card gnn-person', array( gnn_st_p( __( 'Name Surname', 'gnn' ), 'gnn-name' ), gnn_st_p( $role ) ), true );
	}
	return array(
		gnn_st_page_head( $s, __( 'About', 'gnn' ), __( 'An independent firm with a practical approach.', 'gnn' ), __( 'Replace this text with your history, your people and the way you work.', 'gnn' ) ),
		gnn_st_band( $s, 'about', array( gnn_st_c( 'gnn-split', array( gnn_st_c( 'gnn-visual', array( gnn_st_img( 'pro', 'about', __( 'Abstract concentric arcs', 'gnn' ) ) ), true ), gnn_st_c( 'gnn-copy', array( gnn_st_h( __( 'Our story', 'gnn' ), 2 ), gnn_st_p( __( 'Tell visitors why the firm exists, who it serves and what makes its approach different.', 'gnn' ) ), gnn_st_p( __( 'Add milestones, certifications and the values that guide your decisions.', 'gnn' ) ) ), true ) ), true ) ) ),
		gnn_st_band(
			$s,
			'values',
			array(
				gnn_st_head( __( 'Our principles', 'gnn' ), __( 'What guides our work', 'gnn' ) ),
				gnn_st_cards(
					array(
						array( __( 'Independence', 'gnn' ), __( 'Advice that serves your interests only.', 'gnn' ) ),
						array( __( 'Clarity', 'gnn' ), __( 'Plain language and honest options.', 'gnn' ) ),
						array( __( 'Accountability', 'gnn' ), __( 'We stay with the work until it delivers.', 'gnn' ) ),
					),
					'gnn-cols-3'
				),
			)
		),
		gnn_st_band( $s, 'team', array( gnn_st_head( __( 'The team', 'gnn' ), __( 'People you will work with', 'gnn' ) ), gnn_st_c( 'gnn-cards gnn-cols-4', $team, true ) ) ),
		gnn_st_cta( $s, __( 'Work with us', 'gnn' ), __( 'Contact us', 'gnn' ), gnn_st_link( $ids, 'contact' ) ),
	);
}

/**
 * Build the pro services page.
 *
 * @param array $ids Installed page IDs.
 */
function gnn_st_pro_services( $ids ) {
	$s    = 'gnn-pro';
	$rows = array();
	foreach ( array(
		array( __( 'Strategy', 'gnn' ), __( 'Market and organisation review, options appraisal and a roadmap your leadership can sign off.', 'gnn' ) ),
		array( __( 'Operations', 'gnn' ), __( 'Process design, performance measures and supplier management that remove friction.', 'gnn' ) ),
		array( __( 'Transformation', 'gnn' ), __( 'Programme setup, change planning and delivery support from kick-off to handover.', 'gnn' ) ),
	) as $index => $row ) {
		$rows[] = gnn_st_c( 'gnn-row', array( gnn_st_p( sprintf( '%02d', $index + 1 ), 'gnn-num' ), gnn_st_h( $row[0], 3 ), gnn_st_p( $row[1] ), gnn_st_b( __( 'Discuss this service', 'gnn' ), gnn_st_link( $ids, 'contact' ), 'gnn-link' ) ), true );
	}
	return array(
		gnn_st_page_head( $s, __( 'Services', 'gnn' ), __( 'Support for every stage of the work.', 'gnn' ), __( 'Describe each service, who it helps and what clients receive.', 'gnn' ) ),
		gnn_st_band( $s, 'rows', array( gnn_st_c( 'gnn-rows', $rows, true ) ) ),
		gnn_st_band( $s, 'process', array( gnn_st_head( '', __( 'How an engagement runs', 'gnn' ) ), gnn_st_cards( array( array( __( 'Listen', 'gnn' ), __( 'Goals, limits and success measures.', 'gnn' ) ), array( __( 'Plan', 'gnn' ), __( 'Options, timeline and team.', 'gnn' ) ), array( __( 'Deliver', 'gnn' ), __( 'Joint work with regular reporting.', 'gnn' ) ), array( __( 'Review', 'gnn' ), __( 'Outcome and next steps.', 'gnn' ) ) ), 'gnn-cols-4 gnn-steps' ) ) ),
		gnn_st_cta( $s, __( 'Not sure where to start?', 'gnn' ), __( 'Book a consultation', 'gnn' ), gnn_st_link( $ids, 'contact' ) ),
	);
}

/**
 * Build the pro cases page.
 *
 * @param array $ids Installed page IDs.
 */
function gnn_st_pro_cases( $ids ) {
	$s = 'gnn-pro';
	return array(
		gnn_st_page_head( $s, __( 'Case studies', 'gnn' ), __( 'Work we are proud of.', 'gnn' ), __( 'Filter by sector to see how similar organisations achieved their goals.', 'gnn' ) ),
		gnn_st_band(
			$s,
			'cases',
			array(
				gnn_st_mod(
					'portfolio',
					array(
						'columns' => 3,
						'limit'   => 12,
						'filters' => 'yes',
					)
				),
			)
		),
		gnn_st_cta( $s, __( 'Have a similar challenge?', 'gnn' ), __( 'Contact us', 'gnn' ), gnn_st_link( $ids, 'contact' ) ),
	);
}

/**
 * Build the pro news page.
 *
 * @param array $ids Installed page IDs.
 */
function gnn_st_pro_news( $ids ) {
	$s    = 'gnn-pro';
	$blog = (int) get_option( 'page_for_posts' );
	return array(
		gnn_st_page_head( $s, __( 'News', 'gnn' ), __( 'Insights and announcements.', 'gnn' ), __( 'Articles, guidance and company news.', 'gnn' ) ),
		gnn_st_band( $s, 'news', array( gnn_st_p( __( 'Publish articles from Posts and link this page to your blog in Settings.', 'gnn' ) ), $blog && 'publish' === get_post_status( $blog ) ? gnn_st_b( __( 'Read the latest articles', 'gnn' ), get_permalink( $blog ), 'gnn-btn-main' ) : null ) ),
		gnn_st_cta( $s, __( 'Stay in touch', 'gnn' ), __( 'Contact us', 'gnn' ), gnn_st_link( $ids, 'contact' ) ),
	);
}

/**
 * Build the pro contact page.
 *
 * @param array $ids Installed page IDs.
 */
function gnn_st_pro_contact( $ids ) {
	$s = 'gnn-pro';
	return array(
		gnn_st_page_head( $s, __( 'Contact', 'gnn' ), __( 'Let’s talk about your project.', 'gnn' ), __( 'Tell us what you need and we will suggest the next step.', 'gnn' ) ),
		gnn_st_band( $s, 'contact', array( gnn_st_c( 'gnn-split', array( gnn_st_c( 'gnn-copy', array( gnn_st_h( __( 'Get in touch', 'gnn' ), 2 ), gnn_st_contact() ), true ), gnn_st_c( 'gnn-card gnn-note', array( gnn_st_h( __( 'Send a message', 'gnn' ), 3 ), gnn_st_p( __( 'Add your contact form here with the form plugin you use.', 'gnn' ) ) ), true ) ), true ) ) ),
	);
}
