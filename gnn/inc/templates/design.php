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
function gnn_st_profile_design() {
	return array(
		'label'        => __( 'GNNdesign', 'gnn' ),
		'accent'       => '#b8ff3c',
		'accent_light' => '#5c8a00',
		'accent_ink'   => '#0d0d10',
		'header'       => 'floating',
		'radius'       => 28,
		'font'         => 'space',
		'mode'         => 'dark',
		'light'        => array( '#f6f6f2', '#ffffff', '#ecece6', '#0d0d10', '#55555e', '#dcdcd4' ),
		'dark'         => array( '#0d0d10', '#16161b', '#1f1f26', '#f4f4f0', '#a3a3ad', '#2a2a33' ),
		'options'      => array(
			'header_cta_label'  => __( 'Start a project', 'gnn' ),
			'portfolio_columns' => 2,
			'show_cart'         => 0,
		),
		'heading'      => __( 'Design. Code. Make an impact.', 'gnn' ),
		'description'  => __( 'A personal portfolio for developers, designers and independent creative professionals.', 'gnn' ),
	);
}

/** Page keys and titles. */
function gnn_st_pages_design() {
	return array(
		'home'     => __( 'Home', 'gnn' ),
		'work'     => __( 'Work', 'gnn' ),
		'services' => __( 'Services', 'gnn' ),
		'about'    => __( 'About', 'gnn' ),
		'news'     => __( 'Notes', 'gnn' ),
		'contact'  => __( 'Contact', 'gnn' ),
	);
}

/** Bundled demo data. */
function gnn_dp_data_design() {
	return array(
		'images'   => array( 'work-1', 'work-2', 'work-3', 'work-4', 'work-5', 'work-6', 'about', 'news-1', 'news-2', 'news-3' ),
		'projects' => array(
			'work-1' => array( __( 'Atlas brand identity', 'gnn' ), __( 'A confident identity for a travel start-up: logo, colour, type and guidelines.', 'gnn' ), __( 'Brand', 'gnn' ), '2026' ),
			'work-2' => array( __( 'Pulse mobile app', 'gnn' ), __( 'A habit-tracking app designed and prototyped from first sketch to store release.', 'gnn' ), __( 'App', 'gnn' ), '2026' ),
			'work-3' => array( __( 'Orbit marketing site', 'gnn' ), __( 'A fast, accessible website with motion that supports the story instead of hiding it.', 'gnn' ), __( 'Web', 'gnn' ), '2025' ),
			'work-4' => array( __( 'Lumen design system', 'gnn' ), __( 'Tokens, components and documentation shared by design and engineering teams.', 'gnn' ), __( 'Web', 'gnn' ), '2025' ),
			'work-5' => array( __( 'Fern packaging', 'gnn' ), __( 'Packaging and label system for a small-batch tea producer.', 'gnn' ), __( 'Brand', 'gnn' ), '2024' ),
			'work-6' => array( __( 'Kite analytics dashboard', 'gnn' ), __( 'A calm dashboard that turns busy data into clear next actions.', 'gnn' ), __( 'App', 'gnn' ), '2024' ),
		),
		'articles' => array(
			array( __( 'Why I prototype before I polish', 'gnn' ), __( 'A rough prototype answers questions a polished mock-up cannot. Test the idea first, then refine the details.', 'gnn' ) ),
			array( __( 'A small design system that scales', 'gnn' ), __( 'Start with colour, type and spacing tokens. Add components only when you have used them twice.', 'gnn' ) ),
			array( __( 'Notes on accessible motion', 'gnn' ), __( 'Motion should guide attention, respect reduced-motion settings and never block the task at hand.', 'gnn' ) ),
		),
		'slides'   => array(),
		'products' => array(),
	);
}

/* ---------------------------------------------------------------------------
 * GNNdesign - independent designers and developers
 * ------------------------------------------------------------------------- */

function gnn_st_design_home( $ids ) {
	$s       = 'gnn-dz';
	$contact = gnn_st_link( $ids, 'contact' );
	$tags    = array();
	foreach ( array( __( 'Brand identity', 'gnn' ), __( 'Interface design', 'gnn' ), __( 'Web development', 'gnn' ), __( 'Motion', 'gnn' ), __( 'Design systems', 'gnn' ), __( 'Prototyping', 'gnn' ), __( 'Front-end', 'gnn' ), __( 'Strategy', 'gnn' ) ) as $tag ) {
		$tags[] = gnn_st_p( $tag, 'gnn-chip' );
	}
	$marquee = '<div class="gnn-marquee" aria-hidden="true"><div class="gnn-marquee__track">' . str_repeat( '<span>' . esc_html__( 'Design', 'gnn' ) . '</span><span>' . esc_html__( 'Code', 'gnn' ) . '</span><span>' . esc_html__( 'Motion', 'gnn' ) . '</span><span>' . esc_html__( 'Ideas', 'gnn' ) . '</span>', 6 ) . '</div></div>';
	return array(
		gnn_st_c(
			$s . ' ' . $s . '-hero',
			array(
				gnn_st_c(
					'gnn-wrap',
					array(
						gnn_st_p( __( 'Available for new projects', 'gnn' ), 'gnn-status' ),
						gnn_st_h( __( 'I design and build things people enjoy using.', 'gnn' ), 1 ),
						gnn_st_c( 'gnn-herofoot', array( gnn_st_p( __( 'Independent designer and developer working with startups, studios and brands.', 'gnn' ), 'gnn-lead' ), gnn_st_c( 'gnn-actions', array( gnn_st_b( __( 'See my work', 'gnn' ), gnn_st_link( $ids, 'work' ), 'gnn-btn-main' ), gnn_st_b( __( 'Start a project', 'gnn' ), $contact, 'gnn-btn-ghost' ) ), true ) ), true ),
					),
					true
				),
				gnn_st_html( $marquee, 'gnn-marquee-wrap' ),
			)
		),
		gnn_st_band(
			$s,
			'work',
			array(
				gnn_st_c( 'gnn-headrow', array( gnn_st_head( __( 'Selected work', 'gnn' ), __( 'Recent projects', 'gnn' ) ), gnn_st_b( __( 'All work', 'gnn' ), gnn_st_link( $ids, 'work' ), 'gnn-link' ) ), true ),
				gnn_st_mod(
					'portfolio',
					array(
						'columns' => 2,
						'limit'   => 4,
						'filters' => '',
					)
				),
			)
		),
		gnn_st_band(
			$s,
			'services',
			array(
				gnn_st_head( __( 'Services', 'gnn' ), __( 'How I can help', 'gnn' ) ),
				gnn_st_c(
					'gnn-list',
					array(
						gnn_st_c( 'gnn-listrow', array( gnn_st_p( '01', 'gnn-num' ), gnn_st_h( __( 'Design', 'gnn' ), 3 ), gnn_st_p( __( 'Brand identity, interface and product design.', 'gnn' ) ) ), true ),
						gnn_st_c( 'gnn-listrow', array( gnn_st_p( '02', 'gnn-num' ), gnn_st_h( __( 'Development', 'gnn' ), 3 ), gnn_st_p( __( 'Fast, accessible websites and web apps.', 'gnn' ) ) ), true ),
						gnn_st_c( 'gnn-listrow', array( gnn_st_p( '03', 'gnn-num' ), gnn_st_h( __( 'Collaboration', 'gnn' ), 3 ), gnn_st_p( __( 'Workshops, reviews and long-term partnerships.', 'gnn' ) ) ), true ),
					),
					true
				),
				gnn_st_c( 'gnn-chips', $tags, true ),
			)
		),
		gnn_st_band(
			$s,
			'about',
			array(
				gnn_st_c(
					'gnn-split',
					array(
						gnn_st_c( 'gnn-visual', array( gnn_st_img( 'design', 'about', __( 'Abstract portrait', 'gnn' ) ) ), true ),
						gnn_st_c( 'gnn-copy', array( gnn_st_p( __( 'About', 'gnn' ), 'gnn-eyebrow' ), gnn_st_h( __( 'Curious, careful and direct.', 'gnn' ), 2 ), gnn_st_p( __( 'Write a short introduction about your background, your way of working and the kind of projects you enjoy.', 'gnn' ) ), gnn_st_b( __( 'More about me', 'gnn' ), gnn_st_link( $ids, 'about' ), 'gnn-btn-ghost' ) ), true ),
					),
					true
				),
			)
		),
		gnn_st_band(
			$s,
			'cta',
			array( gnn_st_h( __( 'Have an idea? Let’s make it real.', 'gnn' ), 2 ), gnn_st_b( ( sanitize_email( (string) gnn_option( 'company_email' ) ) ? sanitize_email( (string) gnn_option( 'company_email' ) ) : 'hello@example.com' ), 'mailto:' . ( sanitize_email( (string) gnn_option( 'company_email' ) ) ? sanitize_email( (string) gnn_option( 'company_email' ) ) : 'hello@example.com' ), 'gnn-bigmail' ) )
		),
	);
}

function gnn_st_design_work( $ids ) {
	$s = 'gnn-dz';
	return array(
		gnn_st_page_head( $s, __( 'Work', 'gnn' ), __( 'Selected projects', 'gnn' ), __( 'Filter by discipline to explore the work.', 'gnn' ) ),
		gnn_st_band(
			$s,
			'work',
			array(
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
		gnn_st_cta( $s, __( 'Like what you see?', 'gnn' ), __( 'Start a project', 'gnn' ), gnn_st_link( $ids, 'contact' ) ),
	);
}

function gnn_st_design_services( $ids ) {
	$s     = 'gnn-dz';
	$plans = array();
	foreach ( array(
		array( __( 'Sprint', 'gnn' ), __( 'A focused week to explore a problem and test a first solution.', 'gnn' ) ),
		array( __( 'Project', 'gnn' ), __( 'Design and build a complete website or product from brief to launch.', 'gnn' ) ),
		array( __( 'Retainer', 'gnn' ), __( 'Ongoing design and development support, month by month.', 'gnn' ) ),
	) as $index => $plan ) {
		$plans[] = gnn_st_c( 'gnn-card' . ( 1 === $index ? ' is-featured' : '' ), array( gnn_st_h( $plan[0], 3 ), gnn_st_p( $plan[1] ), gnn_st_p( __( 'Add your rate or scope here.', 'gnn' ), 'gnn-price' ), gnn_st_b( __( 'Ask about this', 'gnn' ), gnn_st_link( $ids, 'contact' ), 'gnn-btn-main' ) ), true );
	}
	return array(
		gnn_st_page_head( $s, __( 'Services', 'gnn' ), __( 'Ways to work together.', 'gnn' ), __( 'Pick the format that fits the size of your project.', 'gnn' ) ),
		gnn_st_band( $s, 'plans', array( gnn_st_c( 'gnn-cards gnn-cols-3', $plans, true ) ) ),
		gnn_st_band( $s, 'process', array( gnn_st_head( __( 'Process', 'gnn' ), __( 'From brief to launch', 'gnn' ) ), gnn_st_cards( array( array( __( 'Discover', 'gnn' ), __( 'Goals, users and constraints.', 'gnn' ) ), array( __( 'Design', 'gnn' ), __( 'Concepts, prototypes and feedback.', 'gnn' ) ), array( __( 'Build', 'gnn' ), __( 'Clean code and careful details.', 'gnn' ) ), array( __( 'Launch', 'gnn' ), __( 'Release, measure and improve.', 'gnn' ) ) ), 'gnn-cols-4 gnn-steps' ) ) ),
		gnn_st_cta( $s, __( 'Tell me about your project', 'gnn' ), __( 'Get in touch', 'gnn' ), gnn_st_link( $ids, 'contact' ) ),
	);
}

function gnn_st_design_about( $ids ) {
	$s = 'gnn-dz';
	return array(
		gnn_st_page_head( $s, __( 'About', 'gnn' ), __( 'Hello, I’m your designer and developer.', 'gnn' ), __( 'Introduce yourself in a few honest sentences.', 'gnn' ) ),
		gnn_st_band( $s, 'about', array( gnn_st_c( 'gnn-split', array( gnn_st_c( 'gnn-visual', array( gnn_st_img( 'design', 'about', __( 'Abstract portrait', 'gnn' ) ) ), true ), gnn_st_c( 'gnn-copy', array( gnn_st_h( __( 'Background', 'gnn' ), 2 ), gnn_st_p( __( 'Share your experience, the tools you use and what you care about in your work.', 'gnn' ) ), gnn_st_h( __( 'Beyond work', 'gnn' ), 3 ), gnn_st_p( __( 'Add a few personal details that help clients connect with you.', 'gnn' ) ) ), true ) ), true ) ) ),
		gnn_st_cta( $s, __( 'Let’s work together', 'gnn' ), __( 'Get in touch', 'gnn' ), gnn_st_link( $ids, 'contact' ) ),
	);
}

function gnn_st_design_news( $ids ) {
	$s    = 'gnn-dz';
	$blog = (int) get_option( 'page_for_posts' );
	return array(
		gnn_st_page_head( $s, __( 'Notes', 'gnn' ), __( 'Thoughts on design and code.', 'gnn' ), __( 'Short articles, process notes and tools.', 'gnn' ) ),
		gnn_st_band( $s, 'news', array( $blog && 'publish' === get_post_status( $blog ) ? gnn_st_b( __( 'Read the latest articles', 'gnn' ), get_permalink( $blog ), 'gnn-btn-main' ) : gnn_st_p( __( 'Publish articles from Posts.', 'gnn' ) ) ) ),
	);
}

function gnn_st_design_contact( $ids ) {
	$s = 'gnn-dz';
	return array(
		gnn_st_page_head( $s, __( 'Contact', 'gnn' ), __( 'Say hello.', 'gnn' ), __( 'Tell me about your idea, timeline and budget.', 'gnn' ) ),
		gnn_st_band( $s, 'contact', array( gnn_st_c( 'gnn-split', array( gnn_st_c( 'gnn-copy', array( gnn_st_p( __( 'Available for new projects', 'gnn' ), 'gnn-status' ), gnn_st_contact() ), true ), gnn_st_c( 'gnn-card gnn-note', array( gnn_st_h( __( 'Project brief', 'gnn' ), 3 ), gnn_st_p( __( 'Add your contact form here with the form plugin you use.', 'gnn' ) ) ), true ) ), true ) ) ),
	);
}
