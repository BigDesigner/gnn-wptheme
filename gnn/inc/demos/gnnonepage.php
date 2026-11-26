<?php
/**
 * GNNonepage: flagship single-page product launch demo.
 *
 * Calm home device family ("Olen Series 01") presented on one long,
 * anchor-linked landing page built with native Elementor Free containers
 * and widgets.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'gnn_register_demos', 'gnn_gnnonepage_register' );

/**
 * Register the GNNonepage demo definition.
 *
 * @return void
 */
function gnn_gnnonepage_register() {
	gnn_demo_register(
		'gnnonepage',
		array(
			'label'       => __( 'GNNonepage', 'gnn' ),
			'description' => __( 'A single-page website for a product, launch or personal brand.', 'gnn' ),
			'featured'    => true,
			'order'       => 40,
			'profile'     => gnn_gnnonepage_profile(),
			'pages'       => 'gnn_gnnonepage_pages',
			'page'        => 'gnn_gnnonepage_page',
			'data'        => 'gnn_gnnonepage_data',
			'css'         => 'demos/gnnonepage.css',
			'js'          => 'demos/gnnonepage.js',
			'preview'     => 'assets/images/demo/gnnonepage/preview.jpg',
			'anchors'     => array(
				'overview' => __( 'Overview', 'gnn' ),
				'features' => __( 'Features', 'gnn' ),
				'gallery'  => __( 'Gallery', 'gnn' ),
				'story'    => __( 'Story', 'gnn' ),
				'pricing'  => __( 'Pricing', 'gnn' ),
				'faq'      => __( 'FAQ', 'gnn' ),
				'contact'  => __( 'Contact', 'gnn' ),
			),
		)
	);
}

/**
 * Design preset and theme options for GNNonepage.
 *
 * @return array
 */
function gnn_gnnonepage_profile() {
	return array(
		'label'        => __( 'GNNonepage', 'gnn' ),
		'accent'       => '#7393ff',
		'accent_light' => '#2546d6',
		'accent_ink'   => '#070d1f',
		'header'       => 'floating',
		'radius'       => 24,
		'font'         => 'manrope',
		'heading_font' => 'space',
		'mode'         => 'light',
		'light'        => array( '#ffffff', '#f5f7fb', '#e9eef8', '#0d152b', '#4b5670', '#dce3f0' ),
		'dark'         => array( '#070d1f', '#0d1630', '#162347', '#f2f5fc', '#a3b1cf', '#1f2f57' ),
		'options'      => array(
			'header_structure' => 'classic',
			'header_surface'   => 'glass',
			'header_shape'     => 'floating',
			'header_width'     => 'contained',
			'header_behavior'  => 'sticky',
			'header_height'    => 60,
			'header_cta_label' => __( 'Pre-order', 'gnn' ),
			'header_cta_url'   => home_url( '/#pricing' ),
			'show_cart'        => 0,
			'show_search'      => 0,
			'topbar_enable'    => 0,
			'smooth_scroll'    => 1,
			'scroll_anim'      => 1,
			'button_radius'    => 999,
		),
		'heading'      => __( 'Calm devices for quieter rooms.', 'gnn' ),
		'description'  => __( 'A single-page website for a product, launch or personal brand.', 'gnn' ),
	);
}

/**
 * Page keys and titles (single-page demo: home only).
 *
 * @return array
 */
function gnn_gnnonepage_pages() {
	return array(
		'home' => __( 'Home', 'gnn' ),
	);
}

/**
 * Bundled demo images and empty sample collections.
 *
 * @return array
 */
function gnn_gnnonepage_data() {
	return array(
		'images'   => array(
			'hero',
			'feature-1',
			'feature-2',
			'feature-3',
			'showcase',
			'gallery-1',
			'gallery-2',
			'gallery-3',
			'gallery-4',
			'about',
		),
		'projects' => array(),
		'articles' => array(),
		'slides'   => array(),
		'products' => array(),
	);
}

/**
 * Full-width GNNonepage band with optional anchor ID on the outer container.
 *
 * @param string $name      Section suffix class.
 * @param array  $children  Child Elementor elements.
 * @param string $anchor_id Optional HTML id for anchor navigation.
 * @return array
 */
function gnn_gnnonepage_band( $name, $children, $anchor_id = '' ) {
	$band = gnn_st_band( 'gnn-gnnonepage', $name, $children );
	if ( '' !== $anchor_id ) {
		$band['settings']['_element_id'] = $anchor_id;
	}
	return $band;
}

/**
 * Build elements for a requested page key.
 *
 * @param string $page_key Page key.
 * @param array  $page_ids Installed page IDs.
 * @param array  $data     Created sample records.
 * @param mixed  $group    Optional slide group ID.
 * @return array
 */
function gnn_gnnonepage_page( $page_key, $page_ids = array(), $data = array(), $group = 0 ) {
	if ( 'home' !== $page_key ) {
		return array();
	}
	return gnn_gnnonepage_home( $page_ids );
}

/**
 * Build the GNNonepage single-page landing layout.
 *
 * Sections in order:
 * 1. overview (hero)
 * 2. press (text-only proof strip)
 * 3. features (3-up macro trio)
 * 4. details (3 alternating sticky-copy rows reusing gallery images)
 * 5. showcase (full-bleed lifestyle band with scrim)
 * 6. gallery (4-up product & packaging grid)
 * 7. story (maker story with designer portrait and 3 stats)
 * 8. pricing (3 tiers, middle highlighted)
 * 9. faq (5-item native accordion)
 * 10. contact (details + contact area)
 * 11. closing (final launch line)
 *
 * @param array $page_ids Installed page IDs.
 * @return array
 */
function gnn_gnnonepage_home( $page_ids ) {
	$s = 'gnn-gnnonepage';

	// 1. Overview (Hero).
	$hero = gnn_gnnonepage_band(
		'hero',
		array(
			gnn_st_c(
				'gnn-gnnonepage-hero-copy',
				array(
					gnn_st_p( __( 'Olen Series 01 · Pre-orders open', 'gnn' ), 'gnn-gnnonepage-pill' ),
					gnn_st_h( __( 'Calm devices for quieter rooms.', 'gnn' ), 1 ),
					gnn_st_p( __( 'A coordinated family of home objects—humidifier, desk speaker, kettle and ambient hub—shaped to sit naturally in your living space without screens, status lights or visual noise.', 'gnn' ), 'gnn-lead' ),
					gnn_st_c(
						'gnn-actions gnn-gnnonepage-hero-actions',
						array(
							gnn_st_b( __( 'Pre-order Series 01', 'gnn' ), '#pricing', 'gnn-btn-main' ),
							gnn_st_b( __( 'Explore the family', 'gnn' ), '#features', 'gnn-btn-ghost' ),
						),
						true
					),
				),
				true
			),
			gnn_st_c(
				'gnn-gnnonepage-hero-stage',
				array(
					gnn_st_c(
						'gnn-visual gnn-gnnonepage-hero-media',
						array(
							gnn_st_img( 'gnnonepage', 'hero', __( 'Olen Series 01 ambient hub resting against a soft ultramarine-to-white gradient', 'gnn' ) ),
						),
						true
					),
				),
				true
			),
		),
		'overview'
	);

	// 2. Press / Proof Strip (text-only invented publications).
	$quotes_data = array(
		array(
			__( '“Quietly solved the visual clutter on our nightstand.”', 'gnn' ),
			__( 'Monograph Daily', 'gnn' ),
		),
		array(
			__( '“Tactile hardware that refuses to beg for attention.”', 'gnn' ),
			__( 'Object & Room', 'gnn' ),
		),
		array(
			__( '“Three everyday appliances that finally share one silhouette.”', 'gnn' ),
			__( 'Form Notes', 'gnn' ),
		),
		array(
			__( '“No blinking LEDs, no subscriptions, no plastic foam in the box.”', 'gnn' ),
			__( 'Domestic Review', 'gnn' ),
		),
	);
	$quotes      = array();
	foreach ( $quotes_data as $item ) {
		$quotes[] = gnn_st_c(
			'gnn-gnnonepage-quote',
			array(
				gnn_st_p( $item[0], 'gnn-gnnonepage-quote-text' ),
				gnn_st_p( $item[1], 'gnn-gnnonepage-quote-source' ),
			),
			true
		);
	}
	$press = gnn_gnnonepage_band(
		'press',
		array(
			gnn_st_p( __( 'Early impressions', 'gnn' ), 'gnn-eyebrow' ),
			gnn_st_c( 'gnn-gnnonepage-quotes', $quotes, true ),
		)
	);

	// 3. Features (3 macro cards).
	$features_data = array(
		array(
			'feature-1',
			__( 'Close-up of a rounded matte ceramic-coated device edge', 'gnn' ),
			__( '01 · Surface', 'gnn' ),
			__( 'Soft matte ceramic coat', 'gnn' ),
			__( 'Every shell is bead-blasted and sealed in a low-sheen mineral finish that diffuses daylight and resists fingerprints.', 'gnn' ),
		),
		array(
			'feature-2',
			__( 'Compact white Olen device resting comfortably in a hand', 'gnn' ),
			__( '02 · Touch', 'gnn' ),
			__( 'One-thumb tactile control', 'gnn' ),
			__( 'A weighted physical ring replaces menus and apps. Turn for intensity, press to pause, and feel each detent without looking.', 'gnn' ),
		),
		array(
			'feature-3',
			__( 'White Olen device placed on a shelf beside a blue ceramic vase', 'gnn' ),
			__( '03 · Presence', 'gnn' ),
			__( 'Made to live in plain sight', 'gnn' ),
			__( 'Balanced proportions and a single recessed cord let each object sit beside books and ceramics rather than hiding behind furniture.', 'gnn' ),
		),
	);
	$feature_cards = array();
	foreach ( $features_data as $feat ) {
		$feature_cards[] = gnn_st_c(
			'gnn-card gnn-gnnonepage-feature-card',
			array(
				gnn_st_c(
					'gnn-visual gnn-gnnonepage-feature-media',
					array(
						gnn_st_img( 'gnnonepage', $feat[0], $feat[1] ),
					),
					true
				),
				gnn_st_c(
					'gnn-gnnonepage-feature-body',
					array(
						gnn_st_p( $feat[2], 'gnn-eyebrow' ),
						gnn_st_h( $feat[3], 3 ),
						gnn_st_p( $feat[4] ),
					),
					true
				),
			),
			true
		);
	}
	$features = gnn_gnnonepage_band(
		'features',
		array(
			gnn_st_head(
				__( 'Craft & Form', 'gnn' ),
				__( 'Built to stay out of the way.', 'gnn' ),
				__( 'Three shared details connect every object in Series 01, from the bedside to the kitchen counter.', 'gnn' ),
				'gnn-gnnonepage-head-center'
			),
			gnn_st_c( 'gnn-cards gnn-cols-3 gnn-gnnonepage-feature-grid', $feature_cards, true ),
		),
		'features'
	);

	// 4. Detail rows (3 alternating sticky-copy rows reusing gallery-1..3).
	$details_data = array(
		array(
			'gallery-1',
			__( 'Olen Mist bedside humidifier on a wooden nightstand', 'gnn' ),
			__( '01 · Bedroom', 'gnn' ),
			__( 'Olen Mist — Whisper-quiet bedside humidity', 'gnn' ),
			__( 'A 2.2-litre top-fill vessel runs through the night at 24 dB with zero glowing indicators. Lift the lid to refill from a carafe and rinse the stainless basin in seconds.', 'gnn' ),
			array( __( '24 dB night mode', 'gnn' ), __( '14 h runtime', 'gnn' ), __( 'Top-fill basin', 'gnn' ) ),
			false,
		),
		array(
			'gallery-2',
			__( 'Olen Tone desk speaker beside a workspace', 'gnn' ),
			__( '02 · Workspace', 'gnn' ),
			__( 'Olen Tone — Focused near-field sound', 'gnn' ),
			__( 'Tuned for low-volume listening at a desk or reading chair. A milled acoustic grille replaces fabric mesh, pairing over USB-C or local Wi-Fi with zero cloud account required.', 'gnn' ),
			array( __( 'Near-field tuned', 'gnn' ), __( 'USB-C & Wi-Fi', 'gnn' ), __( 'Physical volume ring', 'gnn' ) ),
			true,
		),
		array(
			'gallery-3',
			__( 'Olen Pour precision kettle on a kitchen counter', 'gnn' ),
			__( '03 · Kitchen', 'gnn' ),
			__( 'Olen Pour — Measured heat, balanced pour', 'gnn' ),
			__( 'Hold any temperature between 60 °C and 100 °C in one-degree steps. The insulated matte exterior stays cool to the touch while the internal spout guides a steady stream.', 'gnn' ),
			array( __( '1 °C precision', 'gnn' ), __( '0.9 L capacity', 'gnn' ), __( 'Cool-touch wall', 'gnn' ) ),
			false,
		),
	);
	$detail_rows  = array();
	foreach ( $details_data as $row ) {
		$specs = array();
		foreach ( $row[5] as $spec ) {
			$specs[] = gnn_st_p( $spec, 'gnn-chip' );
		}
		$detail_rows[] = gnn_st_c(
			'gnn-split gnn-gnnonepage-detail-row' . ( $row[6] ? ' gnn-gnnonepage-detail-row--flip' : '' ),
			array(
				gnn_st_c(
					'gnn-copy gnn-gnnonepage-detail-copy',
					array(
						gnn_st_p( $row[2], 'gnn-eyebrow' ),
						gnn_st_h( $row[3], 3 ),
						gnn_st_p( $row[4] ),
						gnn_st_c( 'gnn-chips gnn-gnnonepage-specs', $specs, true ),
					),
					true
				),
				gnn_st_c(
					'gnn-visual gnn-gnnonepage-detail-media',
					array(
						gnn_st_img( 'gnnonepage', $row[0], $row[1] ),
					),
					true
				),
			),
			true
		);
	}
	$details = gnn_gnnonepage_band(
		'details',
		array(
			gnn_st_head(
				__( 'The Family', 'gnn' ),
				__( 'One design language across three rooms.', 'gnn' ),
				__( 'Each appliance works on its own, or links to the Olen Hub for shared morning and evening routines.', 'gnn' )
			),
			gnn_st_c( 'gnn-gnnonepage-detail-list', $detail_rows, true ),
		)
	);

	// 5. Showcase (Full-bleed lifestyle image with overlay text and contrast scrim).
	$showcase = gnn_st_c(
		$s . ' ' . $s . '-showcase',
		array(
			gnn_st_c(
				'gnn-visual gnn-gnnonepage-showcase-bg',
				array(
					gnn_st_img( 'gnnonepage', 'showcase', __( 'Person relaxing on a sofa in a calm living room with a tablet on the side table', 'gnn' ) ),
				),
				true
			),
			gnn_st_c(
				'gnn-wrap gnn-gnnonepage-showcase-content',
				array(
					gnn_st_c(
						'gnn-gnnonepage-showcase-copy',
						array(
							gnn_st_p( __( 'Evening mode', 'gnn' ), 'gnn-eyebrow' ),
							gnn_st_h( __( 'Technology that settles into the room.', 'gnn' ), 2 ),
							gnn_st_p( __( 'No chimes at midnight, no forced firmware pop-ups and no subscriptions. Set a single quiet schedule once and let the room take care of itself.', 'gnn' ), 'gnn-lead' ),
							gnn_st_b( __( 'Compare pre-order sets', 'gnn' ), '#pricing', 'gnn-btn-main' ),
						),
						true
					),
				),
				true
			),
		)
	);

	// 6. Gallery (4-up grid using gallery-1..4).
	$gallery_data = array(
		array(
			'gallery-1',
			__( 'Olen Mist humidifier on a bedside table', 'gnn' ),
			__( '01 / Olen Mist — Bedside', 'gnn' ),
		),
		array(
			'gallery-2',
			__( 'Olen Tone speaker on a minimal desk', 'gnn' ),
			__( '02 / Olen Tone — Workspace', 'gnn' ),
		),
		array(
			'gallery-3',
			__( 'Olen Pour kettle on a light kitchen counter', 'gnn' ),
			__( '03 / Olen Pour — Kitchen', 'gnn' ),
		),
		array(
			'gallery-4',
			__( 'Open plastic-free molded-pulp packaging box for Olen Series 01', 'gnn' ),
			__( '04 / Packaging — Molded pulp', 'gnn' ),
		),
	);
	$gallery_items = array();
	foreach ( $gallery_data as $item ) {
		$gallery_items[] = gnn_st_c(
			'gnn-gnnonepage-gallery-item',
			array(
				gnn_st_c(
					'gnn-visual gnn-gnnonepage-gallery-media',
					array(
						gnn_st_img( 'gnnonepage', $item[0], $item[1] ),
					),
					true
				),
				gnn_st_p( $item[2], 'gnn-gnnonepage-caption' ),
			),
			true
		);
	}
	$gallery = gnn_gnnonepage_band(
		'gallery',
		array(
			gnn_st_head(
				__( 'Gallery', 'gnn' ),
				__( 'In the rooms they were made for.', 'gnn' ),
				__( 'From bedside and desk to the molded-pulp box they arrive in.', 'gnn' )
			),
			gnn_st_c( 'gnn-cards gnn-cols-4 gnn-gnnonepage-gallery-grid', $gallery_items, true ),
		),
		'gallery'
	);

	// 7. Story (Maker story with designer portrait + 3 stats).
	$stats_data = array(
		array( __( '18 mo', 'gnn' ), __( 'Tested in 24 apartments before tooling', 'gnn' ) ),
		array( __( '42', 'gnn' ), __( 'Hand-carved clay form studies', 'gnn' ) ),
		array( __( '5 yr', 'gnn' ), __( 'Modular parts and repair guarantee', 'gnn' ) ),
	);
	$stats      = array();
	foreach ( $stats_data as $stat ) {
		$stats[] = gnn_st_c(
			'gnn-gnnonepage-stat',
			array(
				gnn_st_p( $stat[0], 'gnn-gnnonepage-stat-value' ),
				gnn_st_p( $stat[1], 'gnn-gnnonepage-stat-label' ),
			),
			true
		);
	}
	$story = gnn_gnnonepage_band(
		'story',
		array(
			gnn_st_c(
				'gnn-split gnn-gnnonepage-story-split',
				array(
					gnn_st_c(
						'gnn-visual gnn-gnnonepage-story-media',
						array(
							gnn_st_img( 'gnnonepage', 'about', __( 'Industrial designer holding a hand-shaped clay prototype in the studio', 'gnn' ) ),
						),
						true
					),
					gnn_st_c(
						'gnn-copy gnn-gnnonepage-story-copy',
						array(
							gnn_st_p( __( 'Maker story', 'gnn' ), 'gnn-eyebrow' ),
							gnn_st_h( __( 'Shaped in clay before a circuit was drawn.', 'gnn' ), 2 ),
							gnn_st_p( __( 'Olen started in a small industrial design studio tired of appliances that looked like server racks. We spent a year carving full-scale clay forms to get the weight, curve and finger feel right before choosing internal components.', 'gnn' ) ),
							gnn_st_p( __( 'Every Series 01 enclosure opens with standard hex screws, carries replaceable gaskets and runs local firmware that continues working even if you never connect it to the internet.', 'gnn' ) ),
							gnn_st_c( 'gnn-gnnonepage-stats', $stats, true ),
						),
						true
					),
				),
				true
			),
		),
		'story'
	);

	// 8. Pricing (3 plans, middle highlighted).
	$plans_data = array(
		array(
			'',
			__( 'Single Object', 'gnn' ),
			__( '$180', 'gnn' ),
			__( 'Start with one room—choose Mist, Tone or Pour.', 'gnn' ),
			array(
				__( 'One Series 01 device of your choice', 'gnn' ),
				__( 'Braided linen power cord', 'gnn' ),
				__( '5-year modular repair guarantee', 'gnn' ),
			),
			__( 'Reserve Single', 'gnn' ),
			false,
		),
		array(
			__( 'Most chosen', 'gnn' ),
			__( 'Room Pair', 'gnn' ),
			__( '$320', 'gnn' ),
			__( 'Two devices paired together for bedroom and workspace.', 'gnn' ),
			array(
				__( 'Any two Series 01 devices', 'gnn' ),
				__( 'Includes Olen Hub ambient dock', 'gnn' ),
				__( 'Priority Autumn 2026 dispatch', 'gnn' ),
				__( '5-year modular repair guarantee', 'gnn' ),
			),
			__( 'Reserve Room Pair', 'gnn' ),
			true,
		),
		array(
			'',
			__( 'Complete Family', 'gnn' ),
			__( '$490', 'gnn' ),
			__( 'The full Series 01 collection for every room.', 'gnn' ),
			array(
				__( 'Mist, Tone, Pour and Olen Hub', 'gnn' ),
				__( 'Spare gasket and filter pack', 'gnn' ),
				__( 'First-batch numbered edition', 'gnn' ),
				__( '5-year modular repair guarantee', 'gnn' ),
			),
			__( 'Reserve Complete', 'gnn' ),
			false,
		),
	);
	$plan_cards = array();
	foreach ( $plans_data as $plan ) {
		$perks = array();
		foreach ( $plan[4] as $perk ) {
			$perks[] = gnn_st_p( $perk );
		}
		$plan_cards[] = gnn_st_c(
			'gnn-card gnn-gnnonepage-plan' . ( $plan[6] ? ' is-featured' : '' ),
			array(
				'' !== $plan[0] ? gnn_st_p( $plan[0], 'gnn-gnnonepage-plan-badge' ) : null,
				gnn_st_h( $plan[1], 3 ),
				gnn_st_p( $plan[2], 'gnn-gnnonepage-plan-price' ),
				gnn_st_p( $plan[3], 'gnn-gnnonepage-plan-desc' ),
				gnn_st_c( 'gnn-ticks gnn-gnnonepage-plan-perks', $perks, true ),
				gnn_st_b( $plan[5], '#contact', $plan[6] ? 'gnn-btn-main' : 'gnn-btn-ghost' ),
			),
			true
		);
	}
	$pricing = gnn_gnnonepage_band(
		'pricing',
		array(
			gnn_st_head(
				__( 'Pre-order', 'gnn' ),
				__( 'Choose your starting set.', 'gnn' ),
				__( 'Refundable at any time before dispatch. No subscriptions or hidden software fees.', 'gnn' ),
				'gnn-gnnonepage-head-center'
			),
			gnn_st_c( 'gnn-cards gnn-cols-3 gnn-gnnonepage-pricing-grid', $plan_cards, true ),
		),
		'pricing'
	);

	// 9. FAQ (5-item native Elementor Free accordion).
	$faq_items = array(
		array(
			'_id'         => 'opfaq1',
			'tab_title'   => __( 'When does Series 01 ship?', 'gnn' ),
			'tab_content' => '<p>' . esc_html__( 'First-batch reservations ship in Autumn 2026 in order of reservation. You receive an address confirmation email four weeks before dispatch and can cancel for a full refund at any time beforehand.', 'gnn' ) . '</p>',
		),
		array(
			'_id'         => 'opfaq2',
			'tab_title'   => __( 'Do the devices require an app or subscription?', 'gnn' ),
			'tab_content' => '<p>' . esc_html__( 'No. Every device operates completely offline with its physical controls. Connecting to the Olen Hub adds optional local scheduling over your home Wi-Fi with no account or monthly fee.', 'gnn' ) . '</p>',
		),
		array(
			'_id'         => 'opfaq3',
			'tab_title'   => __( 'What voltage and plug types are supported?', 'gnn' ),
			'tab_content' => '<p>' . esc_html__( 'Mist, Tone and the Hub ship with universal 100–240 V USB-C adapters and regional blades. Pour is built in dedicated 120 V (North America) and 230 V (Europe, UK and Australia) editions selected at checkout.', 'gnn' ) . '</p>',
		),
		array(
			'_id'         => 'opfaq4',
			'tab_title'   => __( 'How does the 5-year repair guarantee work?', 'gnn' ),
			'tab_content' => '<p>' . esc_html__( 'All enclosures fasten with standard hex screws instead of glue. We stock replacement pumps, seals, acoustic drivers and power boards for at least five years, with step-by-step repair guides or studio servicing.', 'gnn' ) . '</p>',
		),
		array(
			'_id'         => 'opfaq5',
			'tab_title'   => __( 'Can I change which devices are in my bundle later?', 'gnn' ),
			'tab_content' => '<p>' . esc_html__( 'Yes. You can swap models, change voltage region or upgrade from a Single Object to a Room Pair any time before your batch locks for final assembly.', 'gnn' ) . '</p>',
		),
	);
	$faq       = gnn_gnnonepage_band(
		'faq',
		array(
			gnn_st_head(
				__( 'Questions', 'gnn' ),
				__( 'Details before you order.', 'gnn' ),
				'',
				'gnn-gnnonepage-head-center'
			),
			gnn_st_w(
				'accordion',
				array(
					'tabs'           => $faq_items,
					'title_html_tag' => 'h3',
					'icon_align'     => 'right',
				),
				'gnn-gnnonepage-accordion'
			),
		),
		'faq'
	);

	// 10. Contact (same approach as gnn/inc/templates/pro.php).
	$contact = gnn_gnnonepage_band(
		'contact',
		array(
			gnn_st_c(
				'gnn-split gnn-gnnonepage-contact-split',
				array(
					gnn_st_c(
						'gnn-copy',
						array(
							gnn_st_p( __( 'Contact', 'gnn' ), 'gnn-eyebrow' ),
							gnn_st_h( __( 'Pre-order, studio or press enquiries.', 'gnn' ), 2 ),
							gnn_st_p( __( 'Write to our team about reservations, architectural specifications or press samples. We reply within one working day.', 'gnn' ) ),
							gnn_st_contact(),
						),
						true
					),
					gnn_st_c(
						'gnn-card gnn-note gnn-gnnonepage-contact-card',
						array(
							gnn_st_h( __( 'Reserve or send a note', 'gnn' ), 3 ),
							gnn_st_p( __( 'Add your reservation or contact form here with the form plugin you use.', 'gnn' ) ),
						),
						true
					),
				),
				true
			),
		),
		'contact'
	);

	// 11. Closing line.
	$closing = gnn_gnnonepage_band(
		'closing',
		array(
			gnn_st_c(
				'gnn-gnnonepage-closing-inner',
				array(
					gnn_st_p( __( 'Olen Series 01 — shipping Autumn 2026.', 'gnn' ), 'gnn-gnnonepage-closing-line' ),
					gnn_st_b( __( 'Back to pre-order', 'gnn' ), '#pricing', 'gnn-btn-main' ),
				),
				true
			),
		)
	);

	return array(
		$hero,
		$press,
		$features,
		$details,
		$showcase,
		$gallery,
		$story,
		$pricing,
		$faq,
		$contact,
		$closing,
	);
}
