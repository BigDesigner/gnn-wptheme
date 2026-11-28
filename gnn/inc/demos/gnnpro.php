<?php
/**
 * GNNpro flagship consulting demo.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Register the demo and its live posts adapter. */
function gnn_gnnpro_register(): void {
	$description = __( 'A corporate website for consulting firms, professional services and growing companies.', 'gnn' );
	gnn_demo_register(
		'gnnpro',
		array(
			'label'       => __( 'GNNpro', 'gnn' ),
			'description' => $description,
			'featured'    => true,
			'order'       => 20,
			'profile'     => array(
				'label'        => __( 'GNNpro', 'gnn' ),
				'accent'       => '#80b9ec',
				'accent_light' => '#285f98',
				'accent_ink'   => '#0b1929',
				'header'       => 'classic',
				'radius'       => 3,
				'font'         => 'manrope',
				'heading_font' => 'space',
				'mode'         => 'light',
				'light'        => array( '#ffffff', '#f1f5f9', '#e6eef6', '#10243b', '#40556d', '#cedae6' ),
				'dark'         => array( '#0b1420', '#111f30', '#182a40', '#f0f5fa', '#b8c9dc', '#34485c' ),
				'options'      => array(
					'header_structure'  => 'classic',
					'header_surface'    => 'solid',
					'header_shape'      => 'full',
					'header_width'      => 'contained',
					'header_behavior'   => 'sticky',
					'header_overlay'    => 0,
					'header_height'     => 76,
					'topbar_enable'     => 1,
					'topbar_text'       => __( 'Independent strategy and operations advisory', 'gnn' ),
					'header_cta_label'  => __( 'Book a consultation', 'gnn' ),
					'show_search'       => 0,
					'show_cart'         => 0,
					'portfolio_columns' => 3,
				),
				'heading'      => __( 'Make the hard decisions count.', 'gnn' ),
				'description'  => $description,
			),
			'pages'       => 'gnn_gnnpro_pages',
			'page'        => 'gnn_gnnpro_page',
			'data'        => 'gnn_gnnpro_data',
			'css'         => 'demos/gnnpro.css',
			'preview'     => 'assets/images/demo/gnnpro/preview.jpg',
		)
	);
	add_shortcode( 'gnn_gnnpro_posts', 'gnn_gnnpro_render_posts' );
	add_action( 'add_attachment', 'gnn_gnnpro_seed_image_alt' );
}
add_action( 'gnn_register_demos', 'gnn_gnnpro_register' );

/** Page titles in primary menu order. */
function gnn_gnnpro_pages(): array {
	return array(
		'home'     => __( 'Home', 'gnn' ),
		'about'    => __( 'About', 'gnn' ),
		'services' => __( 'Services', 'gnn' ),
		'cases'    => __( 'Case studies', 'gnn' ),
		'careers'  => __( 'Careers', 'gnn' ),
		'news'     => __( 'Insights', 'gnn' ),
		'contact'  => __( 'Contact', 'gnn' ),
	);
}

/** Descriptions for both bundled widgets and imported featured images. */
function gnn_gnnpro_image_descriptions(): array {
	return array(
		'hero'     => __( 'People crossing a glass atrium in an office tower', 'gnn' ),
		'about'    => __( 'The leadership team meeting around a conference table', 'gnn' ),
		'case-1'   => __( 'Finance operations room with rows of workstations', 'gnn' ),
		'case-2'   => __( 'Hospital corridor with treatment rooms on either side', 'gnn' ),
		'case-3'   => __( 'Server racks lining a data centre aisle', 'gnn' ),
		'case-4'   => __( 'Robotic equipment on a manufacturing line', 'gnn' ),
		'leader-1' => __( 'Elias Varen wearing a grey suit', 'gnn' ),
		'leader-2' => __( 'Nolan Merrow wearing a navy sweater', 'gnn' ),
		'facade'   => __( 'Repeating bays across a glass office facade', 'gnn' ),
		'desk'     => __( 'Laptop and working materials on an office desk', 'gnn' ),
		'news-1'   => __( 'An audience facing a conference stage', 'gnn' ),
		'news-2'   => __( 'Two advisers reviewing a tablet together', 'gnn' ),
		'news-3'   => __( 'Business district seen from above at dusk', 'gnn' ),
		'careers'  => __( 'Colleagues in discussion around a shared table', 'gnn' ),
	);
}

/**
 * Replace the importer's filename-derived alt text for new demo attachments.
 *
 * @param int $id Attachment ID.
 */
function gnn_gnnpro_seed_image_alt( int $id ): void {
	$marker = get_post_meta( $id, '_gnn_demo', true );
	$prefix = 'gnnpro:image-';
	if ( ! is_string( $marker ) || 0 !== strpos( $marker, $prefix ) ) {
		return;
	}

	$name         = substr( $marker, strlen( $prefix ) );
	$descriptions = gnn_gnnpro_image_descriptions();
	if ( isset( $descriptions[ $name ] ) ) {
		update_post_meta( $id, '_wp_attachment_image_alt', $descriptions[ $name ] );
	}
}

/** Sample records in the shared demo importer format. */
function gnn_gnnpro_data(): array {
	return array(
		'images'   => array_keys( gnn_gnnpro_image_descriptions() ),
		'projects' => array(
			'case-1' => array(
				__( 'A faster close for a regional lender', 'gnn' ),
				__( '5 days saved on each month-end close after a regional lender consolidated reconciliations across six business units.', 'gnn' ),
				__( 'Financial services', 'gnn' ),
				'2026',
			),
			'case-2' => array(
				__( 'A shorter route to specialist care', 'gnn' ),
				__( '32% less time from referral to triage after a hospital network introduced a single intake queue across four sites.', 'gnn' ),
				__( 'Healthcare', 'gnn' ),
				'2026',
			),
			'case-3' => array(
				__( 'A staged data centre migration', 'gnn' ),
				__( '48 workloads moved without unplanned downtime by sequencing a platform migration around customer usage and testing rollback criteria.', 'gnn' ),
				__( 'Technology', 'gnn' ),
				'2025',
			),
			'case-4' => array(
				__( 'More output from an existing line', 'gnn' ),
				__( '18% more output on a packaging line after shift leads tackled changeover delays and standardised line checks.', 'gnn' ),
				__( 'Manufacturing', 'gnn' ),
				'2025',
			),
		),
		'articles' => array(
			array(
				__( 'What a decision log can tell your board', 'gnn' ),
				__( 'Record who made each decision, what evidence they had and when they will revisit it. After a quarter, look for decisions that repeatedly stall at the same level. That pattern tells you where to clarify authority or bring better evidence into the room.', 'gnn' ),
			),
			array(
				__( 'Set a baseline your teams can agree on', 'gnn' ),
				__( 'Start with one unit of work and define exactly when the clock starts and stops. Sample a few real cases with the people who run the process. Comparing their readings before you set a target will surface local exceptions that an average can hide.', 'gnn' ),
			),
			array(
				__( 'Planning the first 90 days of change', 'gnn' ),
				__( 'Pick one group that can try the new way of working in a fortnight. Give it a clear success measure and name the person who can remove obstacles. Use the results to decide what to expand, what to adjust and what to stop before committing more capacity.', 'gnn' ),
			),
		),
		'products' => array(),
	);
}

/**
 * Render the core Latest Posts module in a Free Shortcode widget.
 *
 * @param array|string $attributes Shortcode attributes.
 * @return string
 */
function gnn_gnnpro_render_posts( $attributes ): string {
	$attributes = shortcode_atts( array( 'limit' => 3 ), $attributes, 'gnn_gnnpro_posts' );
	return render_block(
		array(
			'blockName'    => 'core/latest-posts',
			'attrs'        => array(
				'postsToShow'           => max( 1, min( 12, absint( $attributes['limit'] ) ) ),
				'postLayout'            => 'grid',
				'columns'               => 3,
				'displayPostDate'       => true,
				'displayFeaturedImage'  => true,
				'featuredImageSizeSlug' => 'large',
			),
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		)
	);
}

/**
 * Build a page with native Elementor containers and widgets.
 *
 * @param string $key Page key.
 * @param array  $ids Installed page IDs.
 * @param array  $data Created demo records.
 * @return array
 */
function gnn_gnnpro_page( string $key, array $ids, array $data ): array {
	switch ( $key ) {
		case 'home':
			return gnn_gnnpro_home( $ids, $data );
		case 'about':
			return gnn_gnnpro_about( $ids );
		case 'services':
			return gnn_gnnpro_services( $ids );
		case 'cases':
			return gnn_gnnpro_cases( $ids );
		case 'careers':
			return gnn_gnnpro_careers( $ids );
		case 'news':
			return gnn_gnnpro_news( $ids );
		case 'contact':
			return gnn_gnnpro_contact();
	}
	return array();
}

/** Add a named photo to an editable image widget. */
function gnn_gnnpro_image( string $name, string $class = '' ): array {
	$descriptions = gnn_gnnpro_image_descriptions();
	return gnn_st_img( 'gnnpro', $name, $descriptions[ $name ], 'gnn-gnnpro-image ' . $class );
}

/** Add a demo band with a centred wrapper. */
function gnn_gnnpro_band( string $name, array $children ): array {
	return gnn_st_band( 'gnn-gnnpro', $name, $children );
}

/** Build an inner page opening with a page-specific class. */
function gnn_gnnpro_pagehead( string $key, string $title, string $lead ): array {
	$labels = gnn_gnnpro_pages();
	$head   = gnn_st_page_head( 'gnn-gnnpro', $labels[ $key ], $title, $lead );
	$head['settings']['css_classes'] .= ' gnn-gnnpro-pagehead-' . $key;
	return $head;
}

/** Closing call to action for advisory pages. */
function gnn_gnnpro_cta( array $ids, string $title, string $text ): array {
	return gnn_st_cta( 'gnn-gnnpro', $title, __( 'Book a consultation', 'gnn' ), gnn_st_link( $ids, 'contact' ), $text );
}

/** Native heading and text cards with optional first-column metadata. */
function gnn_gnnpro_cards( array $items, string $class ): array {
	$cards = array();
	foreach ( $items as $item ) {
		$cards[] = gnn_st_c(
			'gnn-gnnpro-card',
			array(
				isset( $item[2] ) ? gnn_st_p( $item[2], 'gnn-gnnpro-card-meta' ) : null,
				gnn_st_h( $item[0], 3 ),
				gnn_st_p( $item[1] ),
			),
			true
		);
	}
	return gnn_st_c( 'gnn-gnnpro-grid ' . $class, $cards, true );
}

/** Six disciplines used for the service overview and full service rows. */
function gnn_gnnpro_disciplines(): array {
	return array(
		array(
			__( 'Strategy and growth', 'gnn' ),
			__( 'Decide where to invest, what to prioritise and how to measure progress.', 'gnn' ),
			'case-1',
			__( 'We test growth options against customer demand, operating capacity and financial constraints. You get an investment case, a sequenced roadmap and the assumptions to revisit each quarter.', 'gnn' ),
			__( 'Investment choices and a three-year roadmap', 'gnn' ),
		),
		array(
			__( 'Operating model design', 'gnn' ),
			__( 'Clarify who owns key decisions and how work moves between teams.', 'gnn' ),
			'case-2',
			__( 'We follow real work from request to handover, identify where it waits and map decisions to the teams that can make them. We pilot the revised model before rolling it out.', 'gnn' ),
			__( 'Decision rights and a tested operating model', 'gnn' ),
		),
		array(
			__( 'Data and analytics', 'gnn' ),
			__( 'Build a shared set of measures leaders and delivery teams can use.', 'gnn' ),
			'case-3',
			__( 'We trace measures back to their source, resolve conflicting definitions and design reporting around decisions people need to make. Teams leave with clear ownership for every metric.', 'gnn' ),
			__( 'Metric definitions and a reporting prototype', 'gnn' ),
		),
		array(
			__( 'Performance improvement', 'gnn' ),
			__( 'Shorten cycle times and free capacity in the processes you already run.', 'gnn' ),
			'case-4',
			__( 'We measure delays, handoffs and rework with frontline teams. Together we test changes on a single line or service, then estimate the capacity released before expanding the work.', 'gnn' ),
			__( 'A performance baseline and a tested improvement plan', 'gnn' ),
		),
		array(
			__( 'Transformation delivery', 'gnn' ),
			__( 'Turn a programme mandate into decisions, milestones and accountable work.', 'gnn' ),
			'desk',
			__( 'We help sponsors sequence work, unblock dependencies and set up regular decisions on scope and risk. Each workstream has a named owner and a clear measure of completion.', 'gnn' ),
			__( 'A delivery plan and a governance rhythm', 'gnn' ),
		),
		array(
			__( 'Change and capability', 'gnn' ),
			__( 'Help managers and teams adopt new routines and sustain them after handover.', 'gnn' ),
			'careers',
			__( 'We work with managers to define the behaviours that need to change, build practice into real work and measure adoption. Internal leads take over the tools and review cadence.', 'gnn' ),
			__( 'A manager toolkit and an adoption plan', 'gnn' ),
		),
	);
}

/** Leadership portraits and the firm's operating philosophy. */
function gnn_gnnpro_leadership(): array {
	$leaders = array(
		array( 'leader-1', __( 'Elias Varen', 'gnn' ), __( 'Managing partner', 'gnn' ), __( 'Leads strategy and operating model work for complex service organisations.', 'gnn' ) ),
		array( 'leader-2', __( 'Nolan Merrow', 'gnn' ), __( 'Partner, transformation', 'gnn' ), __( 'Oversees delivery programmes and helps teams build the skills to run them.', 'gnn' ) ),
	);
	$people  = array();
	foreach ( $leaders as $leader ) {
		$people[] = gnn_st_c(
			'gnn-gnnpro-person',
			array(
				gnn_gnnpro_image( $leader[0], 'gnn-gnnpro-portrait' ),
				gnn_st_h( $leader[1], 3 ),
				gnn_st_p( $leader[2], 'gnn-gnnpro-card-meta' ),
				gnn_st_p( $leader[3] ),
			),
			true
		);
	}

	return gnn_gnnpro_band(
		'leadership',
		array(
			gnn_st_head( __( 'Leadership', 'gnn' ), __( 'Meet the people who lead the work.', 'gnn' ) ),
			gnn_st_c(
				'gnn-gnnpro-leadership-layout',
				array(
					gnn_st_c( 'gnn-gnnpro-people', $people, true ),
					gnn_st_c(
						'gnn-gnnpro-statement',
						array(
							gnn_gnnpro_image( 'facade', 'gnn-gnnpro-facade' ),
							gnn_st_h( __( 'A partner stays with every engagement.', 'gnn' ), 3 ),
							gnn_st_p( __( 'We share the evidence behind each decision and leave your team with the tools to keep improving after handover.', 'gnn' ) ),
						),
						true
					),
				),
				true
			),
		)
	);
}

/** Show the current posts through a Free Elementor Shortcode widget. */
function gnn_gnnpro_posts( int $limit ): array {
	return gnn_st_w( 'shortcode', array( 'shortcode' => sprintf( '[gnn_gnnpro_posts limit="%d"]', $limit ) ), 'gnn-gnnpro-posts' );
}

/** Build the Home page. */
function gnn_gnnpro_home( array $ids, array $data ): array {
	$contact = gnn_st_link( $ids, 'contact' );
	$sectors = array();
	foreach ( array( __( 'Financial services', 'gnn' ), __( 'Healthcare', 'gnn' ), __( 'Technology', 'gnn' ), __( 'Manufacturing', 'gnn' ) ) as $sector ) {
		$sectors[] = gnn_st_p( $sector, 'gnn-gnnpro-sector' );
	}

	$services = array();
	foreach ( gnn_gnnpro_disciplines() as $service ) {
		$services[] = array_slice( $service, 0, 2 );
	}

	$stats = array();
	foreach ( array(
		array( __( '120+', 'gnn' ), __( 'Engagements completed', 'gnn' ) ),
		array( __( '14', 'gnn' ), __( 'Markets served', 'gnn' ) ),
		array( __( '32', 'gnn' ), __( 'Specialist advisers', 'gnn' ) ),
		array( __( '78%', 'gnn' ), __( 'Repeat client engagements', 'gnn' ) ),
	) as $stat ) {
		$stats[] = gnn_st_c( 'gnn-gnnpro-stat', array( gnn_st_p( $stat[0], 'gnn-gnnpro-figure' ), gnn_st_p( $stat[1] ) ), true );
	}

	return array(
		gnn_gnnpro_band(
			'hero',
			array(
				gnn_st_c(
					'gnn-copy gnn-gnnpro-hero-copy',
					array(
						gnn_st_p( __( 'Strategy and operations consulting', 'gnn' ), 'gnn-eyebrow' ),
						gnn_st_h( __( 'Make the hard decisions count.', 'gnn' ), 1 ),
						gnn_st_p( __( 'We help leadership teams choose where to invest, improve how they operate and measure what changes.', 'gnn' ), 'gnn-lead' ),
						gnn_st_c(
							'gnn-actions',
							array(
								gnn_st_b( __( 'Book a consultation', 'gnn' ), $contact, 'gnn-btn-main' ),
								gnn_st_b( __( 'Explore our services', 'gnn' ), gnn_st_link( $ids, 'services' ), 'gnn-btn-ghost' ),
							),
							true
						),
					),
					true
				),
				gnn_st_c(
					'gnn-gnnpro-hero-visual',
					array(
						gnn_gnnpro_image( 'hero', 'gnn-gnnpro-hero-photo' ),
						gnn_st_c(
							'gnn-gnnpro-chip',
							array(
								gnn_st_p( __( '32%', 'gnn' ), 'gnn-gnnpro-figure' ),
								gnn_st_p( __( 'Shorter referral-to-triage time across four hospitals', 'gnn' ) ),
							),
							true
						),
					),
					true
				),
			)
		),
		gnn_gnnpro_band( 'sectors', array_merge( array( gnn_st_h( __( 'Sectors we serve', 'gnn' ), 2 ) ), $sectors ) ),
		gnn_gnnpro_band(
			'services',
			array(
				gnn_sec_content_header( __( 'Services', 'gnn' ), __( 'From board decisions to frontline results.', 'gnn' ), '', __( 'View all services', 'gnn' ), gnn_st_link( $ids, 'services' ) ),
				gnn_gnnpro_cards( $services, 'gnn-gnnpro-grid-three' ),
			)
		),
		gnn_gnnpro_band(
			'stats',
			array(
				gnn_st_h( __( 'Our work in numbers', 'gnn' ), 2 ),
				gnn_st_c( 'gnn-gnnpro-stats-grid', $stats, true ),
			)
		),
		gnn_gnnpro_band(
			'featured',
			array(
				gnn_sec_content_header( __( 'Selected work', 'gnn' ), __( 'What changed for our clients.', 'gnn' ), '', __( 'View all case studies', 'gnn' ), gnn_st_link( $ids, 'cases' ) ),
				gnn_st_mod(
					'portfolio',
					array(
						'columns'     => 3,
						'limit'       => 3,
						'filters'     => '',
						'exclude_ids' => isset( $data['projects']['case-4'] ) ? (string) $data['projects']['case-4'] : '',
					)
				),
			)
		),
		gnn_gnnpro_band(
			'process',
			array(
				gnn_st_head( __( 'How we work', 'gnn' ), __( 'Four steps, with a decision at each.', 'gnn' ) ),
				gnn_st_cards(
					array(
						array( __( 'Diagnose', 'gnn' ), __( 'Agree the question, map the work and establish a baseline.', 'gnn' ) ),
						array( __( 'Choose', 'gnn' ), __( 'Test the options and agree which trade-offs are worth making.', 'gnn' ) ),
						array( __( 'Deliver', 'gnn' ), __( 'Run the work with your teams and review progress against the baseline.', 'gnn' ) ),
						array( __( 'Transfer', 'gnn' ), __( 'Confirm the results and hand over ownership, tools and routines.', 'gnn' ) ),
					),
					'gnn-cols-4 gnn-gnnpro-steps'
				),
			)
		),
		gnn_gnnpro_leadership(),
		gnn_gnnpro_band(
			'insights',
			array(
				gnn_sec_content_header( __( 'Insights', 'gnn' ), __( 'Notes from the work.', 'gnn' ), '', __( 'Read all insights', 'gnn' ), gnn_st_link( $ids, 'news' ) ),
				gnn_gnnpro_posts( 3 ),
			)
		),
		gnn_gnnpro_cta( $ids, __( 'What are you working through?', 'gnn' ), __( 'Tell us what your team needs to decide or deliver. We can help you frame the first step.', 'gnn' ) ),
	);
}

/** Build the About page. */
function gnn_gnnpro_about( array $ids ): array {
	return array(
		gnn_gnnpro_pagehead( 'about', __( 'Independent advice. Shared responsibility.', 'gnn' ), __( 'We are a partner-led firm built for the work between choosing a direction and making it happen.', 'gnn' ) ),
		gnn_gnnpro_band(
			'story',
			array(
				gnn_st_c(
					'gnn-gnnpro-split',
					array(
						gnn_gnnpro_image( 'about' ),
						gnn_st_c(
							'gnn-copy',
							array(
								gnn_st_h( __( 'We started with a problem we knew first-hand.', 'gnn' ), 2 ),
								gnn_st_p( __( 'Our founders had led operations and transformation teams. They saw decisions lose momentum when the people who designed a change stepped away before it reached the teams doing the work.', 'gnn' ) ),
								gnn_st_p( __( 'In 2016 they formed a firm where the same team can build the evidence, make the case and deliver the work alongside the client.', 'gnn' ) ),
							),
							true
						),
					),
					true
				),
			)
		),
		gnn_gnnpro_band(
			'values',
			array(
				gnn_st_head( '', __( 'What we hold ourselves to.', 'gnn' ) ),
				gnn_gnnpro_cards(
					array(
						array( __( 'Independent judgement', 'gnn' ), __( 'We disclose conflicts and show the trade-offs behind our recommendations.', 'gnn' ) ),
						array( __( 'Evidence in the open', 'gnn' ), __( 'We share the baseline, assumptions and source data with the teams who use them.', 'gnn' ) ),
						array( __( 'Direct communication', 'gnn' ), __( 'We name difficult decisions early and explain the options in plain language.', 'gnn' ) ),
						array( __( 'Ownership through handover', 'gnn' ), __( 'We agree who will run the work and test the handover with them before we leave.', 'gnn' ) ),
					),
					'gnn-gnnpro-grid-four'
				),
			)
		),
		gnn_gnnpro_band(
			'timeline',
			array(
				gnn_st_head( __( 'Firm history', 'gnn' ), __( 'A decade of working alongside clients.', 'gnn' ) ),
				gnn_gnnpro_cards(
					array(
						array( __( 'The firm opens', 'gnn' ), __( 'The founding partners start an independent practice in London.', 'gnn' ), __( '2016', 'gnn' ) ),
						array( __( 'Healthcare practice', 'gnn' ), __( 'The firm takes on its first multi-site patient flow programme.', 'gnn' ), __( '2019', 'gnn' ) ),
						array( __( 'Data practice', 'gnn' ), __( 'A dedicated analytics team makes measurement part of every delivery plan.', 'gnn' ), __( '2022', 'gnn' ) ),
						array( __( 'A second office', 'gnn' ), __( 'The Manchester team opens a base for clients across northern England.', 'gnn' ), __( '2025', 'gnn' ) ),
					),
					'gnn-gnnpro-grid-four'
				),
			)
		),
		gnn_gnnpro_leadership(),
		gnn_gnnpro_cta( $ids, __( 'Meet the team behind your project.', 'gnn' ), __( 'Discuss your priorities with a partner who would be involved in the work.', 'gnn' ) ),
	);
}

/** Build the Services page and the editable FAQ. */
function gnn_gnnpro_services( array $ids ): array {
	$rows = array();
	foreach ( gnn_gnnpro_disciplines() as $index => $service ) {
		$rows[] = gnn_st_c(
			'gnn-gnnpro-split gnn-gnnpro-service-row',
			array(
				gnn_gnnpro_image( $service[2] ),
				gnn_st_c(
					'gnn-copy',
					array(
						gnn_st_p( sprintf( '%02d', $index + 1 ), 'gnn-gnnpro-card-meta' ),
						gnn_st_h( $service[0], 2 ),
						gnn_st_p( $service[3] ),
						gnn_st_p( $service[4], 'gnn-gnnpro-deliverable' ),
					),
					true
				),
			),
			true
		);
	}

	$tabs = array();
	foreach ( array(
		array( __( 'How do you scope the work?', 'gnn' ), __( 'We start with a conversation about your decision, deadline and constraints. We then send a written scope with the deliverables, team, fees and review dates.', 'gnn' ) ),
		array( __( 'What do we need to share at the start?', 'gnn' ), __( 'Bring the background you already have and name the people closest to the work. We agree any further data requests, access and handling requirements before analysis begins.', 'gnn' ) ),
		array( __( 'Can you work with our internal programme team?', 'gnn' ), __( 'Yes. We agree roles with your programme lead and use your reporting cadence where possible. Your team keeps ownership of priorities, decisions and delivery.', 'gnn' ) ),
		array( __( 'How are fees structured?', 'gnn' ), __( 'Assessments and defined projects use a fixed scope and fee. For ongoing advisory work, we agree a monthly allocation and review it with you before changing it.', 'gnn' ) ),
		array( __( 'How will we know whether the work succeeded?', 'gnn' ), __( 'We agree the baseline and the measures of success before delivery. At handover we review the results with your team and name who will continue tracking them.', 'gnn' ) ),
	) as $pair ) {
		$tabs[] = array(
			'_id'         => gnn_st_id(),
			'tab_title'   => esc_html( $pair[0] ),
			'tab_content' => '<p>' . esc_html( $pair[1] ) . '</p>',
		);
	}

	return array(
		gnn_gnnpro_pagehead( 'services', __( 'Advice built for decisions and delivery.', 'gnn' ), __( 'Bring us a strategic choice, a process that keeps slowing down, or a programme that needs direction.', 'gnn' ) ),
		gnn_gnnpro_band( 'detail', $rows ),
		gnn_gnnpro_band(
			'models',
			array(
				gnn_st_head( __( 'Engagement models', 'gnn' ), __( 'Choose the support your team needs.', 'gnn' ) ),
				gnn_gnnpro_cards(
					array(
						array( __( 'Focused assessment', 'gnn' ), __( 'Use a two-to-four-week review to establish a baseline and decide where to act.', 'gnn' ) ),
						array( __( 'Joint delivery team', 'gnn' ), __( 'Plan and deliver a defined programme together, with milestones and a joint handover.', 'gnn' ) ),
						array( __( 'Ongoing advisory', 'gnn' ), __( 'Reserve regular time with a partner to test decisions and review outcomes.', 'gnn' ) ),
					),
					'gnn-gnnpro-grid-three'
				),
			)
		),
		gnn_gnnpro_band(
			'faq',
			array(
				gnn_st_head( '', __( 'Questions before we start', 'gnn' ) ),
				gnn_st_w(
					'toggle',
					array(
						'tabs'                 => $tabs,
						'title_html_tag'       => 'h3',
						'selected_icon'        => array( 'value' => '', 'library' => '' ),
						'selected_active_icon' => array( 'value' => '', 'library' => '' ),
					),
					'gnn-gnnpro-toggle'
				),
			)
		),
		gnn_gnnpro_cta( $ids, __( 'Scope the work with us.', 'gnn' ), __( 'Share what is changing and when you need to decide. We can recommend a starting point.', 'gnn' ) ),
	);
}

/** Build the filterable Case studies page. */
function gnn_gnnpro_cases( array $ids ): array {
	return array(
		gnn_gnnpro_pagehead( 'cases', __( 'What changed, and how we measured it.', 'gnn' ), __( 'See how clients in four sectors improved the way they operate, from shortening a financial close to moving critical workloads.', 'gnn' ) ),
		gnn_gnnpro_band(
			'cases',
			array(
				gnn_st_h( __( 'Explore the projects', 'gnn' ), 2 ),
				gnn_st_mod(
					'portfolio',
					array(
						'columns' => 2,
						'limit'   => 12,
						'filters' => 'yes',
					)
				),
			)
		),
		gnn_gnnpro_cta( $ids, __( 'Working on a similar challenge?', 'gnn' ), __( 'We can help you define the baseline and identify where a first change could make a measurable difference.', 'gnn' ) ),
	);
}

/** Build the Careers page. */
function gnn_gnnpro_careers( array $ids ): array {
	$contact = gnn_st_link( $ids, 'contact' );
	$roles   = array();
	foreach ( array(
		array( __( 'Senior consultant', 'gnn' ), __( 'Strategy', 'gnn' ), __( 'London, hybrid', 'gnn' ) ),
		array( __( 'Operations consultant', 'gnn' ), __( 'Operations', 'gnn' ), __( 'Manchester, hybrid', 'gnn' ) ),
		array( __( 'Analytics lead', 'gnn' ), __( 'Data and analytics', 'gnn' ), __( 'London, hybrid', 'gnn' ) ),
		array( __( 'Programme analyst', 'gnn' ), __( 'Transformation', 'gnn' ), __( 'Manchester, hybrid', 'gnn' ) ),
	) as $role ) {
		$roles[] = gnn_st_c(
			'gnn-gnnpro-role',
			array(
				gnn_st_h( $role[0], 3 ),
				gnn_st_p( $role[1] ),
				gnn_st_p( $role[2] ),
				/* translators: %s: job title. */
				gnn_st_b( sprintf( __( 'Enquire about %s', 'gnn' ), $role[0] ), $contact, 'gnn-link' ),
			),
			true
		);
	}

	return array(
		gnn_gnnpro_pagehead( 'careers', __( 'Do work you can see through.', 'gnn' ), __( 'Join advisers who stay close to clients, share what they learn and take responsibility for what changes.', 'gnn' ) ),
		gnn_gnnpro_band(
			'culture',
			array(
				gnn_st_c(
					'gnn-gnnpro-split',
					array(
						gnn_gnnpro_image( 'careers' ),
						gnn_st_c(
							'gnn-copy',
							array(
								gnn_st_h( __( 'Small teams, shared ownership.', 'gnn' ), 2 ),
								gnn_st_p( __( 'Teams mix consultants, analysts and delivery leads. Everyone gets time with the client and sees how the work is used.', 'gnn' ) ),
								gnn_st_p( __( 'We review cases together, discuss what we would do differently and make time for people to practise a new skill on their next project.', 'gnn' ) ),
							),
							true
						),
					),
					true
				),
			)
		),
		gnn_gnnpro_band(
			'benefits',
			array(
				gnn_st_head( '', __( 'How we support your work.', 'gnn' ) ),
				gnn_gnnpro_cards(
					array(
						array( __( 'Hybrid working', 'gnn' ), __( 'Plan office, client and home days with your team around the work you need to do together.', 'gnn' ) ),
						array( __( 'Learning budget', 'gnn' ), __( 'Use an annual allowance and protected time for training and qualifications.', 'gnn' ) ),
						array( __( 'Named mentor', 'gnn' ), __( 'Meet regularly with a mentor outside your project team to discuss your development.', 'gnn' ) ),
						array( __( 'Annual leave', 'gnn' ), __( 'Take 28 days of paid leave, plus local public holidays.', 'gnn' ) ),
						array( __( 'Family leave', 'gnn' ), __( 'Use enhanced parental leave and agree a gradual return with your manager.', 'gnn' ) ),
						array( __( 'Pension and wellbeing', 'gnn' ), __( 'Receive employer pension contributions and access to confidential wellbeing support.', 'gnn' ) ),
					),
					'gnn-gnnpro-grid-three'
				),
			)
		),
		gnn_gnnpro_band(
			'roles',
			array(
				gnn_st_head( '', __( 'Open roles', 'gnn' ) ),
				gnn_st_c( 'gnn-gnnpro-role-list', $roles, true ),
			)
		),
		gnn_gnnpro_band(
			'careers-cta',
			array(
				gnn_st_h( __( 'Interested in working with us?', 'gnn' ), 2 ),
				gnn_st_p( __( 'Tell us what kind of work you would like to do and where you are based.', 'gnn' ), 'gnn-lead' ),
				gnn_st_b( __( 'Contact our team', 'gnn' ), $contact, 'gnn-btn-main' ),
			)
		),
	);
}

/** Build the Insights page. */
function gnn_gnnpro_news( array $ids ): array {
	return array(
		gnn_gnnpro_pagehead( 'news', __( 'Field notes on better decisions.', 'gnn' ), __( 'Practical ways to set a baseline, organise change and make progress visible to the people doing the work.', 'gnn' ) ),
		gnn_gnnpro_band( 'news', array( gnn_st_h( __( 'Latest articles', 'gnn' ), 2 ), gnn_gnnpro_posts( 12 ) ) ),
		gnn_gnnpro_cta( $ids, __( 'Put an idea to work.', 'gnn' ), __( 'Tell us where you want to apply it and what could get in the way.', 'gnn' ) ),
	);
}

/** Build Contact using the theme's contact details and a form placement area. */
function gnn_gnnpro_contact(): array {
	$email = sanitize_email( (string) gnn_option( 'company_email' ) );

	return array(
		gnn_gnnpro_pagehead( 'contact', __( 'Tell us what you need to move forward.', 'gnn' ), __( 'For project enquiries, share the decision or delivery problem, who is involved and any timing constraints.', 'gnn' ) ),
		gnn_gnnpro_band(
			'contact',
			array(
				gnn_st_c(
					'gnn-gnnpro-contact-layout',
					array(
						gnn_st_c(
							'gnn-copy',
							array(
								gnn_st_h( __( 'Get in touch', 'gnn' ), 2 ),
								gnn_st_p( __( 'Available Monday to Friday, 09:00 to 17:30 UK time.', 'gnn' ) ),
								gnn_st_contact(),
							),
							true
						),
						gnn_st_c(
							'gnn-copy gnn-gnnpro-form',
							array(
								gnn_st_h( __( 'Send a message', 'gnn' ), 2 ),
								gnn_st_p( __( 'Add your contact form here with the form plugin you use.', 'gnn' ) ),
							),
							true
						),
					),
					true
				),
				gnn_st_head( '', __( 'Our offices', 'gnn' ) ),
				gnn_gnnpro_cards(
					array(
						array( __( 'London', 'gnn' ), __( 'Our main office supports clients in London and southern England. Meetings by appointment.', 'gnn' ) ),
						array( __( 'Manchester', 'gnn' ), __( 'Our northern office supports clients across the region. Meetings by appointment.', 'gnn' ) ),
					),
					'gnn-gnnpro-grid-two'
				),
			)
		),
		gnn_gnnpro_band(
			'map',
			array(
				gnn_st_c(
					'gnn-copy',
					array(
						gnn_st_h( __( 'Plan an office visit.', 'gnn' ), 2 ),
						gnn_st_p( __( 'Email our team to arrange a visit and get directions for your appointment.', 'gnn' ) ),
						gnn_st_b( __( 'Email our team', 'gnn' ), 'mailto:' . ( $email ? $email : 'hello@example.com' ), 'gnn-btn-main' ),
					),
					true
				),
				gnn_st_c(
					'gnn-gnnpro-map-graphic',
					array(
						gnn_st_p( __( 'Manchester', 'gnn' ), 'gnn-gnnpro-map-pin gnn-gnnpro-map-north' ),
						gnn_st_p( __( 'London', 'gnn' ), 'gnn-gnnpro-map-pin gnn-gnnpro-map-south' ),
					),
					true
				),
			)
		),
	);
}
