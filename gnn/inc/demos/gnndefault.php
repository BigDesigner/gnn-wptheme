<?php
/**
 * GNNdefault: flagship all-purpose business website demo.
 * Warm neutrals, calm editorial rhythm, native Elementor Free containers and widgets.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'gnn_register_demos', 'gnn_gnndefault_register' );

/**
 * Register the GNNdefault demo definition.
 *
 * @return void
 */
function gnn_gnndefault_register() {
	gnn_demo_register(
		'gnndefault',
		array(
			'label'       => __( 'GNNdefault', 'gnn' ),
			'description' => __( 'A versatile business website that suits almost any company or studio.', 'gnn' ),
			'featured'    => true,
			'order'       => 10,
			'profile'     => gnn_gnndefault_profile(),
			'pages'       => 'gnn_gnndefault_pages',
			'page'        => 'gnn_gnndefault_page',
			'data'        => 'gnn_gnndefault_data',
			'css'         => 'demos/gnndefault.css',
			'js'          => '',
			'preview'     => 'assets/images/demo/gnndefault/preview.jpg',
		)
	);
}

/**
 * Design preset tokens and panel defaults for GNNdefault.
 *
 * @return array
 */
function gnn_gnndefault_profile() {
	return array(
		'label'        => __( 'GNNdefault', 'gnn' ),
		'accent'       => '#9db497',
		'accent_light' => '#4a5d47',
		'accent_ink'   => '#151914',
		'header'       => 'classic',
		'radius'       => 14,
		'font'         => 'manrope',
		'heading_font' => 'space',
		'mode'         => 'light',
		'light'        => array( '#f7f4ef', '#ffffff', '#ede8df', '#1d1c19', '#59554d', '#ddd6ca' ),
		'dark'         => array( '#171614', '#201e1b', '#2a2723', '#f3efe8', '#b5afa4', '#34302b' ),
		'options'      => array(
			'header_structure'  => 'classic',
			'header_surface'    => 'glass',
			'header_shape'      => 'full',
			'header_width'      => 'contained',
			'header_behavior'   => 'sticky',
			'header_overlay'    => 0,
			'header_height'     => 76,
			'topbar_enable'     => 0,
			'header_cta_label'  => __( 'Get in touch', 'gnn' ),
			'show_cart'         => 0,
			'portfolio_columns' => 3,
			'button_radius'     => 999,
			'body_size'         => 17,
			'body_line_height'  => 175,
			'container_width'   => 1240,
		),
		'heading'      => __( 'Thoughtful work for growing businesses.', 'gnn' ),
		'description'  => __( 'A versatile business website that suits almost any company or studio.', 'gnn' ),
	);
}

/**
 * Page keys and titles in menu order.
 *
 * @return array
 */
function gnn_gnndefault_pages() {
	return array(
		'home'     => __( 'Home', 'gnn' ),
		'about'    => __( 'About', 'gnn' ),
		'services' => __( 'Services', 'gnn' ),
		'work'     => __( 'Work', 'gnn' ),
		'news'     => __( 'News', 'gnn' ),
		'contact'  => __( 'Contact', 'gnn' ),
	);
}

/**
 * Bundled sample media, projects and articles.
 *
 * @return array
 */
function gnn_gnndefault_data() {
	return array(
		'images'   => array(
			'hero',
			'about',
			'service-1',
			'service-2',
			'service-3',
			'work-1',
			'work-2',
			'work-3',
			'news-1',
			'news-2',
			'news-3',
		),
		'projects' => array(
			'work-1' => array(
				__( 'Stoneware apothecary range', 'gnn' ),
				__( 'Packaging, product photography direction and launch site for a small-batch botanical line.', 'gnn' ),
				__( 'Brand', 'gnn' ),
				'2026',
			),
			'work-2' => array(
				__( 'Cedar & Linen flagship store', 'gnn' ),
				__( 'Spatial identity, signage and e-commerce storefront for an independent homewares shop.', 'gnn' ),
				__( 'Retail', 'gnn' ),
				'2025',
			),
			'work-3' => array(
				__( 'Courtyard coffee house', 'gnn' ),
				__( 'Visual identity, menu system and local website for a neighbourhood roastery and cafe.', 'gnn' ),
				__( 'Hospitality', 'gnn' ),
				'2025',
			),
		),
		'articles' => array(
			array(
				__( 'Starting a project with a one-page brief', 'gnn' ),
				__( 'A single page covering goals, audience and deadlines aligns everyone faster than a long slide deck.', 'gnn' ),
			),
			array(
				__( 'Four numbers worth reviewing each quarter', 'gnn' ),
				__( 'Track enquiry quality, response time, repeat clients and margin rather than chasing vanity traffic.', 'gnn' ),
			),
			array(
				__( 'How to keep a project handover calm and clear', 'gnn' ),
				__( 'Document decisions as you go, record a short walkthrough and leave files your team can edit easily.', 'gnn' ),
			),
		),
		'slides'   => array(),
		'products' => array(),
	);
}

/**
 * Build Elementor elements for one GNNdefault page.
 *
 * @param string $page_key Page key.
 * @param array  $page_ids Installed page IDs.
 * @param array  $data     Created sample records.
 * @param mixed  $group    Optional slide group ID.
 * @return array
 */
function gnn_gnndefault_page( $page_key, $page_ids, $data = array(), $group = 0 ) {
	unset( $group );
	switch ( $page_key ) {
		case 'home':
			return gnn_gnndefault_page_home( $page_ids, $data );
		case 'about':
			return gnn_gnndefault_page_about( $page_ids );
		case 'services':
			return gnn_gnndefault_page_services( $page_ids );
		case 'work':
			return gnn_gnndefault_page_work( $page_ids );
		case 'news':
			return gnn_gnndefault_page_news( $page_ids, $data );
		case 'contact':
			return gnn_gnndefault_page_contact();
		default:
			return array();
	}
}

/**
 * Editorial quote band with inline SVG quote mark.
 *
 * @param string $quote       Testimonial text.
 * @param string $attribution Role-based attribution.
 * @return array
 */
function gnn_gnndefault_quote_band( $quote, $attribution ) {
	$mark = '<svg class="gnn-gnndefault-quote-mark" width="36" height="28" viewBox="0 0 36 28" fill="none" aria-hidden="true"><path d="M14.4 0C6.4 3.2 1.6 9.8 1.6 18.2 1.6 24 5.2 28 10.2 28c4.4 0 7.8-3.4 7.8-7.6 0-4.2-3.1-7.3-7.1-7.4.6-4.4 3.1-7.8 6.9-10L14.4 0Zm18 0c-8 3.2-12.8 9.8-12.8 18.2 0 5.8 3.6 9.8 8.6 9.8 4.4 0 7.8-3.4 7.8-7.6 0-4.2-3.1-7.3-7.1-7.4.6-4.4 3.1-7.8 6.9-10L32.4 0Z" fill="currentColor"/></svg>';
	return gnn_st_band(
		'gnn-gnndefault',
		'quote',
		array(
			gnn_st_c(
				'gnn-gnndefault-quote-inner',
				array(
					gnn_st_html( $mark, 'gnn-gnndefault-quote-icon' ),
					gnn_st_p( $quote, 'gnn-gnndefault-quote-text' ),
					gnn_st_p( $attribution, 'gnn-gnndefault-quote-attr' ),
				),
				true
			),
		)
	);
}

/**
 * Warm closing call-to-action band.
 *
 * @param array  $ids   Installed page IDs.
 * @param string $title Heading text.
 * @param string $lead  Supporting sentence.
 * @param string $label Button label.
 * @return array
 */
function gnn_gnndefault_cta_band( $ids, $title, $lead, $label = '' ) {
	$button = '' !== $label ? $label : __( 'Get in touch', 'gnn' );
	return gnn_st_band(
		'gnn-gnndefault',
		'cta',
		array(
			gnn_st_c(
				'gnn-gnndefault-cta-inner',
				array(
					gnn_st_p( __( 'Start a conversation', 'gnn' ), 'gnn-eyebrow' ),
					gnn_st_h( $title, 2 ),
					gnn_st_p( $lead, 'gnn-lead' ),
					gnn_st_c(
						'gnn-actions',
						array(
							gnn_st_b( $button, gnn_st_link( $ids, 'contact' ), 'gnn-btn-main' ),
						),
						true
					),
				),
				true
			),
		)
	);
}

/**
 * Three-column news grid populated from created demo posts, with bundled fallback.
 *
 * @param array $data Created sample records.
 * @return array
 */
function gnn_gnndefault_news_grid( $data ) {
	$cards = array();
	$posts = array_slice( (array) ( $data['posts'] ?? array() ), 0, 3 );
	foreach ( $posts as $post_id ) {
		$post_id = absint( $post_id );
		if ( ! $post_id ) {
			continue;
		}
		$image_url = get_the_post_thumbnail_url( $post_id, 'large' );
		$excerpt   = get_the_excerpt( $post_id );
		$cards[]   = gnn_st_c(
			'gnn-gnndefault-post',
			array(
				$image_url ? gnn_st_c(
					'gnn-visual gnn-gnndefault-post-media',
					array(
						gnn_st_w(
							'image',
							array(
								'image'      => array(
									'url' => $image_url,
									'id'  => (int) get_post_thumbnail_id( $post_id ),
									'alt' => get_the_title( $post_id ),
								),
								'image_size' => 'large',
								'link_to'    => 'custom',
								'link'       => array( 'url' => get_permalink( $post_id ) ),
							)
						),
					),
					true
				) : null,
				gnn_st_c(
					'gnn-gnndefault-post-body',
					array(
						gnn_st_p( get_the_date( '', $post_id ), 'gnn-eyebrow' ),
						gnn_st_h( get_the_title( $post_id ), 3 ),
						$excerpt ? gnn_st_p( $excerpt ) : null,
						gnn_st_b( __( 'Read article', 'gnn' ), get_permalink( $post_id ), 'gnn-link' ),
					),
					true
				),
			),
			true
		);
	}
	if ( ! $cards ) {
		$alts     = array(
			__( 'Open notebook with a handwritten project plan', 'gnn' ),
			__( 'Tablet displaying simple quarterly charts on a desk', 'gnn' ),
			__( 'Two colleagues shaking hands after a meeting', 'gnn' ),
		);
		$articles = gnn_gnndefault_data()['articles'];
		foreach ( $articles as $index => $article ) {
			$img_name = 'news-' . ( $index + 1 );
			$cards[]  = gnn_st_c(
				'gnn-gnndefault-post',
				array(
					gnn_st_c(
						'gnn-visual gnn-gnndefault-post-media',
						array( gnn_st_img( 'gnndefault', $img_name, $alts[ $index ] ?? $article[0] ) ),
						true
					),
					gnn_st_c(
						'gnn-gnndefault-post-body',
						array(
							gnn_st_p( __( 'Journal', 'gnn' ), 'gnn-eyebrow' ),
							gnn_st_h( $article[0], 3 ),
							gnn_st_p( $article[1] ),
						),
						true
					),
				),
				true
			);
		}
	}
	return gnn_st_c( 'gnn-cards gnn-cols-3 gnn-gnndefault-posts', $cards, true );
}

/**
 * Build the Home page.
 *
 * @param array $ids  Installed page IDs.
 * @param array $data Created sample records.
 * @return array
 */
function gnn_gnndefault_page_home( $ids, $data ) {
	$s       = 'gnn-gnndefault';
	$contact = gnn_st_link( $ids, 'contact' );
	$services = array(
		array(
			'service-1',
			__( 'Hands arranging print layouts and colour swatches on a table', 'gnn' ),
			__( 'Brand & Identity', 'gnn' ),
			__( 'Clear visual systems, messaging and guidelines that make your business easy to recognise.', 'gnn' ),
		),
		array(
			'service-2',
			__( 'Laptop, coffee cup and notebook arranged on a clean desk', 'gnn' ),
			__( 'Digital & Web', 'gnn' ),
			__( 'Fast, readable websites designed around your customers and simple for your team to update.', 'gnn' ),
		),
		array(
			'service-3',
			__( 'Advisor presenting a project plan at a studio whiteboard', 'gnn' ),
			__( 'Advisory & Workshops', 'gnn' ),
			__( 'Practical planning sessions to sharpen positioning, map priorities and guide key launches.', 'gnn' ),
		),
	);
	$service_cards = array();
	foreach ( $services as $service ) {
		$service_cards[] = gnn_st_c(
			'gnn-card gnn-gnndefault-service-card',
			array(
				gnn_st_c( 'gnn-visual gnn-gnndefault-service-media', array( gnn_st_img( 'gnndefault', $service[0], $service[1] ) ), true ),
				gnn_st_c(
					'gnn-gnndefault-service-body',
					array(
						gnn_st_h( $service[2], 3 ),
						gnn_st_p( $service[3] ),
						gnn_st_b( __( 'Explore service', 'gnn' ), gnn_st_link( $ids, 'services' ), 'gnn-link' ),
					),
					true
				),
			),
			true
		);
	}

	return array(
		// 1. Hero split.
		gnn_st_c(
			$s . ' ' . $s . '-hero',
			array(
				gnn_st_c(
					'gnn-wrap gnn-split',
					array(
						gnn_st_c(
							'gnn-copy',
							array(
								gnn_st_p( __( 'Independent studio & consultancy', 'gnn' ), 'gnn-eyebrow' ),
								gnn_st_h( __( 'Calm, considered work for businesses that value craft.', 'gnn' ), 1 ),
								gnn_st_p( __( 'We partner with founders, studios and local companies to shape clear brands, websites and everyday customer experiences.', 'gnn' ), 'gnn-lead' ),
								gnn_st_c(
									'gnn-actions',
									array(
										gnn_st_b( __( 'Get in touch', 'gnn' ), $contact, 'gnn-btn-main' ),
										gnn_st_b( __( 'Selected work', 'gnn' ), gnn_st_link( $ids, 'work' ), 'gnn-btn-ghost' ),
									),
									true
								),
							),
							true
						),
						gnn_st_c(
							'gnn-visual gnn-gnndefault-hero-media',
							array( gnn_st_img( 'gnndefault', 'hero', __( 'Bright studio workspace with a solid oak table and natural light', 'gnn' ) ) ),
							true
						),
					),
					true
				),
			)
		),
		// 2. What we do trio.
		gnn_st_band(
			$s,
			'services',
			array(
				gnn_st_c(
					'gnn-headrow',
					array(
						gnn_st_head( __( 'What we do', 'gnn' ), __( 'Three areas where we help most', 'gnn' ), __( 'Focused engagements with clear scope, steady communication and practical outcomes.', 'gnn' ) ),
						gnn_st_b( __( 'All services', 'gnn' ), gnn_st_link( $ids, 'services' ), 'gnn-link' ),
					),
					true
				),
				gnn_st_c( 'gnn-cards gnn-cols-3 gnn-gnndefault-service-grid', $service_cards, true ),
			)
		),
		// 3. Large intro statement.
		gnn_st_band(
			$s,
			'statement',
			array(
				gnn_st_c(
					'gnn-gnndefault-statement-inner',
					array(
						gnn_st_p( __( 'Our approach', 'gnn' ), 'gnn-eyebrow' ),
						gnn_st_h( __( 'Good work is rarely loud. It is clear, well-proportioned and built to stay useful long after launch day.', 'gnn' ), 2 ),
						gnn_st_p( __( 'Whether you are refreshing an established company or introducing something new, we keep the process grounded in real customer needs.', 'gnn' ), 'gnn-lead' ),
						gnn_st_b( __( 'More about us', 'gnn' ), gnn_st_link( $ids, 'about' ), 'gnn-link' ),
					),
					true
				),
			)
		),
		// 4. Alternating image/text feature rows (2).
		gnn_st_band(
			$s,
			'features',
			array(
				gnn_st_c(
					'gnn-gnndefault-feature-rows',
					array(
						gnn_st_c(
							'gnn-split gnn-gnndefault-feature',
							array(
								gnn_st_c( 'gnn-visual gnn-gnndefault-feature-media', array( gnn_st_img( 'gnndefault', 'work-1', __( 'Stoneware product bottle displayed on a natural stone plinth', 'gnn' ) ) ), true ),
								gnn_st_c(
									'gnn-copy',
									array(
										gnn_st_p( __( 'Clarity first', 'gnn' ), 'gnn-eyebrow' ),
										gnn_st_h( __( 'Designed around the details customers notice', 'gnn' ), 2 ),
										gnn_st_p( __( 'From packaging hierarchy to page layout, we strip away clutter so your product and message carry the room on their own.', 'gnn' ) ),
										gnn_st_b( __( 'See how we work', 'gnn' ), gnn_st_link( $ids, 'services' ), 'gnn-link' ),
									),
									true
								),
							),
							true
						),
						gnn_st_c(
							'gnn-split gnn-gnndefault-feature gnn-gnndefault-feature--reverse',
							array(
								gnn_st_c( 'gnn-visual gnn-gnndefault-feature-media', array( gnn_st_img( 'gnndefault', 'work-2', __( 'Calm boutique interior with warm wooden shelving', 'gnn' ) ) ), true ),
								gnn_st_c(
									'gnn-copy',
									array(
										gnn_st_p( __( 'Built to last', 'gnn' ), 'gnn-eyebrow' ),
										gnn_st_h( __( 'Consistent across physical spaces and screens', 'gnn' ), 2 ),
										gnn_st_p( __( 'We align print, interiors and digital touchpoints into one coherent language that your team can maintain without friction.', 'gnn' ) ),
										gnn_st_b( __( 'View recent projects', 'gnn' ), gnn_st_link( $ids, 'work' ), 'gnn-link' ),
									),
									true
								),
							),
							true
						),
					),
					true
				),
			)
		),
		// 5. Selected work (3, from projects).
		gnn_st_band(
			$s,
			'work',
			array(
				gnn_st_c(
					'gnn-headrow',
					array(
						gnn_st_head( __( 'Selected work', 'gnn' ), __( 'Recent projects', 'gnn' ) ),
						gnn_st_b( __( 'All projects', 'gnn' ), gnn_st_link( $ids, 'work' ), 'gnn-link' ),
					),
					true
				),
				gnn_st_mod(
					'portfolio',
					array(
						'columns' => 3,
						'limit'   => 3,
						'filters' => '',
					)
				),
			)
		),
		// 6. Testimonial band.
		gnn_gnndefault_quote_band(
			__( '“They listened carefully, asked the right questions early and delivered a site our whole team enjoys updating. Enquiries improved within the first month.”', 'gnn' ),
			__( 'Founder, design studio', 'gnn' )
		),
		// 7. How we work (3 steps).
		gnn_st_band(
			$s,
			'process',
			array(
				gnn_st_head( __( 'How we work', 'gnn' ), __( 'Three steady stages from brief to launch', 'gnn' ), __( 'Clear checkpoints keep timelines realistic and decisions straightforward.', 'gnn' ) ),
				gnn_st_cards(
					array(
						array( __( 'Listen & scope', 'gnn' ), __( 'We review your goals, audience and constraints, then agree a clear one-page plan and fixed fee.', 'gnn' ) ),
						array( __( 'Design & refine', 'gnn' ), __( 'We share early layouts and working prototypes so feedback happens while changes are quick.', 'gnn' ) ),
						array( __( 'Launch & handover', 'gnn' ), __( 'We publish, test across devices and leave concise notes so your team can run things confidently.', 'gnn' ) ),
					),
					'gnn-cols-3 gnn-gnndefault-steps'
				),
			)
		),
		// 8. Latest news (3 posts).
		gnn_st_band(
			$s,
			'news',
			array(
				gnn_st_c(
					'gnn-headrow',
					array(
						gnn_st_head( __( 'Latest news', 'gnn' ), __( 'Notes from the studio', 'gnn' ) ),
						gnn_st_b( __( 'All news', 'gnn' ), gnn_st_link( $ids, 'news' ), 'gnn-link' ),
					),
					true
				),
				gnn_gnndefault_news_grid( $data ),
			)
		),
		// 9. Warm closing CTA.
		gnn_gnndefault_cta_band(
			$ids,
			__( 'Have a project in mind?', 'gnn' ),
			__( 'Share a short outline of what you are planning. We reply within one working day.', 'gnn' )
		),
	);
}

/**
 * Build the About page.
 *
 * @param array $ids Installed page IDs.
 * @return array
 */
function gnn_gnndefault_page_about( $ids ) {
	$s     = 'gnn-gnndefault';
	$stats = array(
		array( '12', __( 'Years working with independent businesses', 'gnn' ) ),
		array( '84', __( 'Completed brand and web projects', 'gnn' ) ),
		array( '92%', __( 'Clients who return for a second stage', 'gnn' ) ),
	);
	$stat_cards = array();
	foreach ( $stats as $stat ) {
		$stat_cards[] = gnn_st_c(
			'gnn-gnndefault-stat',
			array(
				gnn_st_p( $stat[0], 'gnn-gnndefault-stat-num' ),
				gnn_st_p( $stat[1], 'gnn-gnndefault-stat-label' ),
			),
			true
		);
	}

	return array(
		// 1. Page header.
		gnn_st_page_head(
			$s,
			__( 'About', 'gnn' ),
			__( 'A small, senior team focused on steady craft.', 'gnn' ),
			__( 'We work closely with owners and internal teams who want direct communication and lasting results.', 'gnn' )
		),
		// 2. Story split (about image).
		gnn_st_band(
			$s,
			'story',
			array(
				gnn_st_c(
					'gnn-split',
					array(
						gnn_st_c( 'gnn-visual gnn-gnndefault-story-media', array( gnn_st_img( 'gnndefault', 'about', __( 'Three colleagues reviewing sketches together at a studio table', 'gnn' ) ) ), true ),
						gnn_st_c(
							'gnn-copy',
							array(
								gnn_st_p( __( 'Our story', 'gnn' ), 'gnn-eyebrow' ),
								gnn_st_h( __( 'Built for long-term partnerships, not quick turnover', 'gnn' ), 2 ),
								gnn_st_p( __( 'We started with a simple idea: small and medium businesses deserve the same care in typography, structure and strategy as much larger organisations, without layers of account management.', 'gnn' ) ),
								gnn_st_p( __( 'Every project is led by the people who attend your first call. We keep our roster intentionally small so each client receives calm, thorough attention.', 'gnn' ) ),
							),
							true
						),
					),
					true
				),
			)
		),
		// 3. Numbers (3 stats).
		gnn_st_band(
			$s,
			'stats',
			array(
				gnn_st_c( 'gnn-cards gnn-cols-3 gnn-gnndefault-stats-grid', $stat_cards, true ),
			)
		),
		// 4. Values (3).
		gnn_st_band(
			$s,
			'values',
			array(
				gnn_st_head( __( 'What guides us', 'gnn' ), __( 'Three principles we hold to', 'gnn' ) ),
				gnn_st_cards(
					array(
						array( __( 'Plain speaking', 'gnn' ), __( 'We explain trade-offs clearly and avoid jargon so decisions stay easy to make.', 'gnn' ) ),
						array( __( 'Measured pace', 'gnn' ), __( 'Realistic schedules produce better work than rushed sprints and late surprises.', 'gnn' ) ),
						array( __( 'Self-sufficiency', 'gnn' ), __( 'We build systems your team can edit and grow without calling us for every small change.', 'gnn' ) ),
					),
					'gnn-cols-3 gnn-gnndefault-values-grid'
				),
			)
		),
		// 5. Testimonial.
		gnn_gnndefault_quote_band(
			__( '“Working with them felt like adding two calm, experienced colleagues to our own team. Everything arrived on schedule and ready to use.”', 'gnn' ),
			__( 'Operations director, independent retail group', 'gnn' )
		),
		// 6. CTA.
		gnn_gnndefault_cta_band(
			$ids,
			__( 'Let’s see if we are a good fit.', 'gnn' ),
			__( 'Tell us a little about your company and what you would like to improve next.', 'gnn' )
		),
	);
}

/**
 * Build the Services page.
 *
 * @param array $ids Installed page IDs.
 * @return array
 */
function gnn_gnndefault_page_services( $ids ) {
	$s       = 'gnn-gnndefault';
	$contact = gnn_st_link( $ids, 'contact' );

	$blocks_data = array(
		array(
			'01',
			'service-1',
			__( 'Hands arranging print layouts and colour swatches on a table', 'gnn' ),
			__( 'Brand & Identity', 'gnn' ),
			__( 'A cohesive visual and verbal foundation that gives your company a calm, recognisable presence across print and digital.', 'gnn' ),
			array(
				__( 'Positioning summary and tone of voice notes', 'gnn' ),
				__( 'Logo, colour palette and typography system', 'gnn' ),
				__( 'Practical brand guide and everyday templates', 'gnn' ),
			),
			false,
		),
		array(
			'02',
			'service-2',
			__( 'Laptop, coffee cup and notebook arranged on a clean desk', 'gnn' ),
			__( 'Digital & Web', 'gnn' ),
			__( 'Clear, accessible websites structured around how visitors actually read, compare options and get in touch.', 'gnn' ),
			array(
				__( 'Page architecture and editorial copy support', 'gnn' ),
				__( 'Responsive design in light and dark modes', 'gnn' ),
				__( 'Editable templates and team handover walkthrough', 'gnn' ),
			),
			true,
		),
		array(
			'03',
			'service-3',
			__( 'Advisor presenting a project plan at a studio whiteboard', 'gnn' ),
			__( 'Advisory & Workshops', 'gnn' ),
			__( 'Focused sessions for founders and teams who need an outside perspective before committing to a larger build.', 'gnn' ),
			array(
				__( 'Website and brand clarity review', 'gnn' ),
				__( 'Half-day roadmap and prioritisation workshop', 'gnn' ),
				__( 'Written action plan with realistic milestones', 'gnn' ),
			),
			false,
		),
	);

	$service_blocks = array();
	foreach ( $blocks_data as $block ) {
		$bullets = array();
		foreach ( $block[5] as $bullet ) {
			$bullets[] = gnn_st_p( $bullet, 'gnn-gnndefault-bullet' );
		}
		$service_blocks[] = gnn_st_c(
			'gnn-split gnn-gnndefault-feature' . ( $block[6] ? ' gnn-gnndefault-feature--reverse' : '' ),
			array(
				gnn_st_c( 'gnn-visual gnn-gnndefault-feature-media', array( gnn_st_img( 'gnndefault', $block[1], $block[2] ) ), true ),
				gnn_st_c(
					'gnn-copy',
					array(
						gnn_st_p( $block[0], 'gnn-num' ),
						gnn_st_h( $block[3], 2 ),
						gnn_st_p( $block[4] ),
						gnn_st_c( 'gnn-gnndefault-checklist', $bullets, true ),
					),
					true
				),
			),
			true
		);
	}

	$plans_data = array(
		array(
			__( 'Advisory sprint', 'gnn' ),
			__( 'From 1,200', 'gnn' ),
			__( 'A focused two-week review with a workshop and clear written roadmap.', 'gnn' ),
			array(
				__( 'Discovery call & audit', 'gnn' ),
				__( 'Half-day working session', 'gnn' ),
				__( 'Prioritised action plan', 'gnn' ),
			),
			false,
		),
		array(
			__( 'Complete website', 'gnn' ),
			__( 'From 3,800', 'gnn' ),
			__( 'Full structure, visual design, build and handover for a multi-page site.', 'gnn' ),
			array(
				__( 'Content & layout architecture', 'gnn' ),
				__( 'Custom responsive design', 'gnn' ),
				__( 'Launch & team training', 'gnn' ),
			),
			true,
		),
		array(
			__( 'Ongoing partner', 'gnn' ),
			__( 'From 1,600 / month', 'gnn' ),
			__( 'Reserved monthly time for design updates, new pages and steady improvements.', 'gnn' ),
			array(
				__( 'Monthly planning check-in', 'gnn' ),
				__( 'Design & content updates', 'gnn' ),
				__( 'Quarterly review notes', 'gnn' ),
			),
			false,
		),
	);

	$plan_cards = array();
	foreach ( $plans_data as $plan ) {
		$items = array();
		foreach ( $plan[3] as $item ) {
			$items[] = gnn_st_p( $item, 'gnn-gnndefault-bullet' );
		}
		$plan_cards[] = gnn_st_c(
			'gnn-card gnn-gnndefault-plan' . ( $plan[4] ? ' is-featured' : '' ),
			array(
				gnn_st_h( $plan[0], 3 ),
				gnn_st_p( $plan[1], 'gnn-gnndefault-price' ),
				gnn_st_p( $plan[2] ),
				gnn_st_c( 'gnn-gnndefault-checklist', $items, true ),
				gnn_st_b( __( 'Ask about this', 'gnn' ), $contact, $plan[4] ? 'gnn-btn-main' : 'gnn-btn-ghost' ),
			),
			true
		);
	}

	$faqs_data = array(
		array(
			__( 'How long does a typical website project take?', 'gnn' ),
			__( 'Most multi-page sites take between four and eight weeks from kick-off to launch, depending on how ready the copy and photography are.', 'gnn' ),
		),
		array(
			__( 'Can we update text and images ourselves afterwards?', 'gnn' ),
			__( 'Yes. Every page is built with standard editable blocks and widgets, and we include a short handover guide so your team feels comfortable making changes.', 'gnn' ),
		),
		array(
			__( 'Do you work with clients outside your local area?', 'gnn' ),
			__( 'Yes. Most of our engagements run smoothly over video calls and shared review links, with in-person workshops when helpful.', 'gnn' ),
		),
		array(
			__( 'How are fees structured?', 'gnn' ),
			__( 'Projects are quoted as a fixed fee split across clear milestones, agreed in writing before work starts.', 'gnn' ),
		),
	);

	$faq_cards = array();
	foreach ( $faqs_data as $faq ) {
		$faq_cards[] = gnn_st_c(
			'gnn-gnndefault-faq-item',
			array(
				gnn_st_h( $faq[0], 3 ),
				gnn_st_p( $faq[1] ),
			),
			true
		);
	}

	return array(
		// 1. Page header.
		gnn_st_page_head(
			$s,
			__( 'Services', 'gnn' ),
			__( 'Practical support for every stage of your business.', 'gnn' ),
			__( 'Choose a single focused engagement or combine brand, web and ongoing advisory.', 'gnn' )
		),
		// 2. Service blocks (3, alternating with service images).
		gnn_st_band(
			$s,
			'serviceblocks',
			array(
				gnn_st_c( 'gnn-gnndefault-feature-rows', $service_blocks, true ),
			)
		),
		// 3. Pricing teaser (3 simple plans).
		gnn_st_band(
			$s,
			'pricing',
			array(
				gnn_st_head( __( 'Guide pricing', 'gnn' ), __( 'Simple ways to get started', 'gnn' ), __( 'Final quotes are tailored to your scope and agreed upfront.', 'gnn' ) ),
				gnn_st_c( 'gnn-cards gnn-cols-3 gnn-gnndefault-plans', $plan_cards, true ),
			)
		),
		// 4. FAQ (4).
		gnn_st_band(
			$s,
			'faq',
			array(
				gnn_st_head( __( 'Common questions', 'gnn' ), __( 'Good to know before we begin', 'gnn' ) ),
				gnn_st_c( 'gnn-cards gnn-cols-2 gnn-gnndefault-faq-grid', $faq_cards, true ),
			)
		),
		// 5. CTA.
		gnn_gnndefault_cta_band(
			$ids,
			__( 'Not sure which option fits best?', 'gnn' ),
			__( 'Send a short note about your timeline and goals and we will suggest a sensible starting point.', 'gnn' )
		),
	);
}

/**
 * Build the Work page.
 *
 * @param array $ids Installed page IDs.
 * @return array
 */
function gnn_gnndefault_page_work( $ids ) {
	$s = 'gnn-gnndefault';
	return array(
		// 1. Page header.
		gnn_st_page_head(
			$s,
			__( 'Work', 'gnn' ),
			__( 'Selected projects', 'gnn' ),
			__( 'A small collection of recent brand, retail and hospitality collaborations.', 'gnn' )
		),
		// 2. Projects module.
		gnn_st_band(
			$s,
			'work',
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
		// 3. CTA.
		gnn_gnndefault_cta_band(
			$ids,
			__( 'Planning something similar?', 'gnn' ),
			__( 'We would be glad to hear what you are building and how we might help.', 'gnn' )
		),
	);
}

/**
 * Build the News page.
 *
 * @param array $ids  Installed page IDs.
 * @param array $data Created sample records.
 * @return array
 */
function gnn_gnndefault_page_news( $ids, $data ) {
	$s    = 'gnn-gnndefault';
	$blog = (int) get_option( 'page_for_posts' );
	return array(
		// 1. Page header.
		gnn_st_page_head(
			$s,
			__( 'News', 'gnn' ),
			__( 'Notes, updates and practical guides.', 'gnn' ),
			__( 'Short articles on planning, design and running a clear business website.', 'gnn' )
		),
		// 2. Posts list.
		gnn_st_band(
			$s,
			'news',
			array(
				gnn_gnndefault_news_grid( $data ),
				$blog && 'publish' === get_post_status( $blog ) ? gnn_st_c(
					'gnn-actions gnn-gnndefault-news-more',
					array( gnn_st_b( __( 'Browse full archive', 'gnn' ), get_permalink( $blog ), 'gnn-btn-ghost' ) ),
					true
				) : null,
			)
		),
		// 3. CTA.
		gnn_gnndefault_cta_band(
			$ids,
			__( 'Want to talk through an idea?', 'gnn' ),
			__( 'Reach out any time with a question or a brief.', 'gnn' )
		),
	);
}

/**
 * Build the Contact page.
 *
 * @return array
 */
function gnn_gnndefault_page_contact() {
	$s     = 'gnn-gnndefault';
	$hours = array(
		array( __( 'Monday – Thursday', 'gnn' ), '09:00 – 18:00' ),
		array( __( 'Friday', 'gnn' ), '09:00 – 16:00' ),
		array( __( 'Saturday – Sunday', 'gnn' ), __( 'Closed', 'gnn' ) ),
	);
	$hour_rows = array();
	foreach ( $hours as $row ) {
		$hour_rows[] = gnn_st_c(
			'gnn-gnndefault-hour-row',
			array(
				gnn_st_p( $row[0], 'gnn-gnndefault-hour-day' ),
				gnn_st_p( $row[1], 'gnn-gnndefault-hour-time' ),
			),
			true
		);
	}

	return array(
		// 1. Page header.
		gnn_st_page_head(
			$s,
			__( 'Contact', 'gnn' ),
			__( 'Get in touch.', 'gnn' ),
			__( 'Tell us what you are working on and we will reply within one working day.', 'gnn' )
		),
		// 2. Contact details, contact area & opening hours.
		gnn_st_band(
			$s,
			'contact',
			array(
				gnn_st_c(
					'gnn-split gnn-gnndefault-contact-split',
					array(
						gnn_st_c(
							'gnn-copy gnn-gnndefault-contact-info',
							array(
								gnn_st_h( __( 'Studio details', 'gnn' ), 2 ),
								gnn_st_p( __( 'Write or call directly. If you already have a timeline or budget range in mind, feel free to include it.', 'gnn' ) ),
								gnn_st_contact(),
								gnn_st_c(
									'gnn-gnndefault-hours',
									array(
										gnn_st_h( __( 'Opening hours', 'gnn' ), 3 ),
										gnn_st_c( 'gnn-gnndefault-hours-list', $hour_rows, true ),
									),
									true
								),
							),
							true
						),
						gnn_st_c(
							'gnn-card gnn-note gnn-gnndefault-form-card',
							array(
								gnn_st_h( __( 'Send a message', 'gnn' ), 3 ),
								gnn_st_p( __( 'Add your contact form here with the form plugin you use.', 'gnn' ) ),
							),
							true
						),
					),
					true
				),
			)
		),
	);
}
