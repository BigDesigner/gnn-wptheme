<?php
/**
 * GNNarchitecture: editorial Elementor Free page set with a monograph design language.
 * Sections are native containers and widgets; sample photographs are the bundled architecture set.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Page keys and titles for new installs. Existing page records keep their own titles. */
function gnn_st_pages_architecture() {
	return array(
		'home'      => __( 'Home', 'gnn' ),
		'studio'    => __( 'Studio', 'gnn' ),
		'services'  => __( 'Services', 'gnn' ),
		'portfolio' => __( 'Projects', 'gnn' ),
		'gallery'   => __( 'Gallery', 'gnn' ),
		'contact'   => __( 'Contact', 'gnn' ),
	);
}

/**
 * Records created by the complete demo, shared with the generators while one install runs.
 *
 * @param array|null $set Data to store, or null to read.
 * @return array
 */
function gnn_st_arch_ctx( $set = null ) {
	static $ctx = array();
	if ( null !== $set ) {
		$ctx = $set;
	}
	return $ctx;
}

/**
 * Bundled photograph.
 *
 * @param int    $number Bundled image number.
 * @param string $alt Alternative text.
 * @param string $class Extra CSS classes.
 */
function gnn_st_arch_img( $number, $alt, $class = '' ) {
	return gnn_st_img( 'architecture', 'image-' . sprintf( '%02d', $number ), $alt, $class );
}

/**
 * Opening block of an inner page.
 *
 * @param string $label Visible label.
 * @param string $title Heading text.
 * @param string $lead Introductory text.
 */
function gnn_st_arch_head( $label, $title, $lead ) {
	return gnn_st_band( 'gnn-ar', 'pagehead', array( gnn_st_p( $label, 'gnn-eyebrow' ), gnn_st_h( $title, 1 ), gnn_st_p( $lead, 'gnn-lead' ) ) );
}

/**
 * Dark closing band.
 *
 * @param array $ids Installed page IDs.
 */
function gnn_st_arch_cta( $ids ) {
	return gnn_st_band( 'gnn-ar', 'cta', array( gnn_st_h( __( 'Let’s talk about your site.', 'gnn' ), 2 ), gnn_st_p( __( 'Every project begins with a conversation.', 'gnn' ), 'gnn-lead' ), gnn_st_b( __( 'Start a project', 'gnn' ), gnn_st_link( $ids, 'contact' ), 'gnn-btn-main' ) ) );
}

/**
 * Numbered rows.
 *
 * @param array $items Section items.
 */
function gnn_st_arch_rows( $items ) {
	$rows = array();
	foreach ( $items as $index => $item ) {
		$rows[] = gnn_st_c( 'gnn-ar-row', array( gnn_st_p( sprintf( '%02d', $index + 1 ), 'gnn-num' ), gnn_st_h( $item[0], 3 ), gnn_st_p( $item[1] ) ), true );
	}
	return gnn_st_c( 'gnn-ar-rows', $rows, true );
}

/** Three most recent journal entries, or neutral placeholders before any exist. */
function gnn_st_arch_journal() {
	$ctx   = gnn_st_arch_ctx();
	$cards = array();
	foreach ( array_slice( (array) ( $ctx['posts'] ?? array() ), 0, 3 ) as $post_id ) {
		$image   = get_the_post_thumbnail_url( $post_id, 'large' );
		$cards[] = gnn_st_c(
			'gnn-ar-story',
			array(
				$image ? gnn_st_w(
					'image',
					array(
						'image'      => array(
							'url' => $image,
							'id'  => (int) get_post_thumbnail_id( $post_id ),
							'alt' => get_the_title( $post_id ),
						),
						'image_size' => 'large',
						'link_to'    => 'none',
					)
				) : null,
				gnn_st_p( get_the_date( '', $post_id ), 'gnn-eyebrow' ),
				gnn_st_h( get_the_title( $post_id ), 3 ),
				gnn_st_b( __( 'Read story', 'gnn' ), get_permalink( $post_id ), 'gnn-link' ),
			),
			true
		);
	}
	if ( ! $cards ) {
		foreach ( array( 25, 26, 28 ) as $number ) {
			$cards[] = gnn_st_c( 'gnn-ar-story', array( gnn_st_arch_img( $number, __( 'Architecture studio: drawings, materials and places', 'gnn' ) ), gnn_st_h( __( 'Add your first story', 'gnn' ), 3 ), gnn_st_p( __( 'Publish articles from Posts.', 'gnn' ) ) ), true );
		}
	}
	return gnn_st_c( 'gnn-ar-stories', $cards, true );
}

/**
 * Build the architecture home page.
 *
 * @param array $ids Installed page IDs.
 */
function gnn_st_architecture_home( $ids ) {
	$s     = 'gnn-ar';
	$ctx   = gnn_st_arch_ctx();
	$group = (int) ( $ctx['group'] ?? 0 );
	$hero  = $group
		? gnn_st_mod(
			'slider',
			array(
				'category' => $group,
				'limit'    => 3,
			),
			'gnn-ar-slider'
		)
		: gnn_st_c( 'gnn-ar-slider gnn-ar-static', array( gnn_st_arch_img( 18, __( 'Residential building with planted terraces', 'gnn' ) ), gnn_st_c( 'gnn-ar-static-copy', array( gnn_st_p( __( 'Architecture / Selected projects', 'gnn' ), 'gnn-eyebrow' ), gnn_st_h( __( 'Space. Light. Life.', 'gnn' ), 1 ) ), true ) ) );
	return array(
		$hero,
		gnn_st_band(
			$s,
			'manifesto',
			array(
				gnn_st_p( '01 / ' . __( 'Studio', 'gnn' ), 'gnn-eyebrow' ),
				gnn_st_c(
					'gnn-ar-statement',
					array(
						gnn_st_h( __( 'We design places that feel natural to live in.', 'gnn' ), 2 ),
						gnn_st_p( __( 'Architecture, interiors and landscape, shaped by light, material and the way people use space.', 'gnn' ), 'gnn-lead' ),
						gnn_st_b( __( 'Meet the studio', 'gnn' ), gnn_st_link( $ids, 'studio' ), 'gnn-link' ),
					),
					true
				),
			)
		),
		gnn_st_band(
			$s,
			'work',
			array(
				gnn_st_c( 'gnn-headrow', array( gnn_st_c( 'gnn-head', array( gnn_st_p( '02 / ' . __( 'Selected work', 'gnn' ), 'gnn-eyebrow' ), gnn_st_h( __( 'Recent projects', 'gnn' ), 2 ) ), true ), gnn_st_b( __( 'All projects', 'gnn' ), gnn_st_link( $ids, 'portfolio' ), 'gnn-link' ) ), true ),
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
			'disciplines',
			array(
				gnn_st_p( '03 / ' . __( 'Disciplines', 'gnn' ), 'gnn-eyebrow' ),
				gnn_st_cards(
					array(
						array( __( 'Architecture', 'gnn' ), __( 'From the first sketch to the finished building.', 'gnn' ) ),
						array( __( 'Interiors', 'gnn' ), __( 'Rooms planned around daily use, light and materials.', 'gnn' ) ),
						array( __( 'Landscape', 'gnn' ), __( 'Gardens and courtyards that extend the home outdoors.', 'gnn' ) ),
					),
					'gnn-cols-3'
				),
			)
		),
		gnn_st_band(
			$s,
			'approach',
			array(
				gnn_st_c(
					'gnn-split',
					array(
						gnn_st_c( 'gnn-visual', array( gnn_st_arch_img( 24, __( 'Studio drawings and material samples', 'gnn' ) ) ), true ),
						gnn_st_c(
							'gnn-copy',
							array(
								gnn_st_p( '04 / ' . __( 'Approach', 'gnn' ), 'gnn-eyebrow' ),
								gnn_st_h( __( 'From the first sketch to the final detail.', 'gnn' ), 2 ),
								gnn_st_arch_rows(
									array(
										array( __( 'Listen', 'gnn' ), __( 'Understand the brief and the site.', 'gnn' ) ),
										array( __( 'Test', 'gnn' ), __( 'Explore options with drawings and models.', 'gnn' ) ),
										array( __( 'Detail', 'gnn' ), __( 'Resolve materials and junctions.', 'gnn' ) ),
										array( __( 'Follow', 'gnn' ), __( 'Stay close through construction.', 'gnn' ) ),
									)
								),
							),
							true
						),
					),
					true
				),
			)
		),
		gnn_st_band( $s, 'journal', array( gnn_st_c( 'gnn-headrow', array( gnn_st_c( 'gnn-head', array( gnn_st_p( '05 / ' . __( 'Journal', 'gnn' ), 'gnn-eyebrow' ), gnn_st_h( __( 'From the studio', 'gnn' ), 2 ) ), true ) ), true ), gnn_st_arch_journal() ) ),
		gnn_st_arch_cta( $ids ),
	);
}

/**
 * Build the architecture studio page.
 *
 * @param array $ids Installed page IDs.
 */
function gnn_st_architecture_studio( $ids ) {
	return array(
		gnn_st_arch_head( __( 'Studio', 'gnn' ), __( 'Curiosity. Clarity. Care.', 'gnn' ), __( 'A collaborative studio interested in how places can support everyday life.', 'gnn' ) ),
		gnn_st_band(
			'gnn-ar',
			'about',
			array(
				gnn_st_c(
					'gnn-split',
					array(
						gnn_st_c( 'gnn-visual', array( gnn_st_arch_img( 24, __( 'Studio drawings and material samples', 'gnn' ) ) ), true ),
						gnn_st_c( 'gnn-copy', array( gnn_st_h( __( 'A shared design process.', 'gnn' ), 2 ), gnn_st_p( __( 'Architects, interior designers and project coordinators work together from the first conversation. Each project grows through close dialogue with its users and a careful reading of its surroundings.', 'gnn' ) ) ), true ),
					),
					true
				),
			)
		),
		gnn_st_band(
			'gnn-ar',
			'disciplines',
			array(
				gnn_st_p( __( 'How we work', 'gnn' ), 'gnn-eyebrow' ),
				gnn_st_cards(
					array(
						array( __( 'Listen', 'gnn' ), __( 'Understand the brief and the site.', 'gnn' ) ),
						array( __( 'Test', 'gnn' ), __( 'Explore options with drawings and models.', 'gnn' ) ),
						array( __( 'Follow', 'gnn' ), __( 'Stay close through construction.', 'gnn' ) ),
					),
					'gnn-cols-3'
				),
			)
		),
		gnn_st_arch_cta( $ids ),
	);
}

/**
 * Build the architecture services page.
 *
 * @param array $ids Installed page IDs.
 */
function gnn_st_architecture_services( $ids ) {
	return array(
		gnn_st_arch_head( __( 'Services', 'gnn' ), __( 'Support from idea to handover.', 'gnn' ), __( 'Choose a single service or work with the studio from the first sketch to the keys.', 'gnn' ) ),
		gnn_st_band(
			'gnn-ar',
			'services',
			array(
				gnn_st_arch_rows(
					array(
						array( __( 'Architecture', 'gnn' ), __( 'From the first sketch to the finished building.', 'gnn' ) ),
						array( __( 'Interiors', 'gnn' ), __( 'Rooms planned around daily use, light and materials.', 'gnn' ) ),
						array( __( 'Landscape', 'gnn' ), __( 'Gardens and courtyards that extend the home outdoors.', 'gnn' ) ),
						array( __( 'Project coordination', 'gnn' ), __( 'Specialists and builders brought together under one plan.', 'gnn' ) ),
					)
				),
			)
		),
		gnn_st_arch_cta( $ids ),
	);
}

/**
 * Build the architecture portfolio page.
 *
 * @param array $ids Installed page IDs.
 */
function gnn_st_architecture_portfolio( $ids ) {
	return array(
		gnn_st_arch_head( __( 'Projects', 'gnn' ), __( 'A collection of places.', 'gnn' ), __( 'Residential architecture, interiors and shared spaces. Explore the projects by category.', 'gnn' ) ),
		gnn_st_band(
			'gnn-ar',
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
		gnn_st_arch_cta( $ids ),
	);
}

/**
 * Build the architecture gallery page.
 *
 * @param array $ids Installed page IDs.
 */
function gnn_st_architecture_gallery( $ids ) {
	$ctx      = gnn_st_arch_ctx();
	$settings = array(
		'columns' => 3,
		'limit'   => 30,
	);
	if ( ! empty( $ctx['albums'][0] ) ) {
		$settings['source'] = 'album';
		$settings['record'] = (int) $ctx['albums'][0];
	}
	return array(
		gnn_st_arch_head( __( 'Gallery', 'gnn' ), __( 'In the details.', 'gnn' ), __( 'Materials, rooms and landscape from our projects and studio.', 'gnn' ) ),
		gnn_st_band( 'gnn-ar', 'gallery', array( gnn_st_mod( 'gallery', $settings ) ) ),
		gnn_st_arch_cta( $ids ),
	);
}

/**
 * Build the architecture contact page.
 *
 * @param array $ids Installed page IDs.
 */
function gnn_st_architecture_contact( $ids ) {
	return array(
		gnn_st_arch_head( __( 'Contact', 'gnn' ), __( 'Let’s talk about your project.', 'gnn' ), __( 'Every project begins with a conversation.', 'gnn' ) ),
		gnn_st_band(
			'gnn-ar',
			'contact',
			array(
				gnn_st_c(
					'gnn-split',
					array(
						gnn_st_c( 'gnn-copy', array( gnn_st_h( __( 'Project enquiries', 'gnn' ), 2 ), gnn_st_p( __( 'Tell us about the place, the people who will use it and what you would like to create.', 'gnn' ) ), gnn_st_contact() ), true ),
						gnn_st_c( 'gnn-visual', array( gnn_st_arch_img( 23, __( 'Architecture studio: drawings, materials and places', 'gnn' ) ) ), true ),
					),
					true
				),
			)
		),
	);
}

/**
 * Page elements for the complete demo, where created records are known.
 *
 * @param string $key Page key.
 * @param array  $pages Page IDs.
 * @param array  $data Created records (posts, albums).
 * @param int    $group Slide group ID.
 * @return array
 */
function gnn_st_architecture_page( $key, $pages, $data, $group ) {
	$previous = gnn_st_arch_ctx();
	gnn_st_arch_ctx(
		array(
			'posts'  => $data['posts'] ?? array(),
			'albums' => $data['albums'] ?? array(),
			'group'  => $group,
		)
	);
	$elements = gnn_st_page( 'architecture', $key, $pages );
	gnn_st_arch_ctx( $previous );
	return $elements;
}
