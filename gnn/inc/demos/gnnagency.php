<?php
/**
 * GNNagency creative studio demo.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'gnn_register_demos', 'gnn_gnnagency_register' );

/** Register the agency site and its demo-specific integrations. */
function gnn_gnnagency_register() {
	$label       = __( 'GNNagency', 'gnn' );
	$description = __( 'A bold portfolio website for creative studios, agencies and freelancers.', 'gnn' );
	gnn_demo_register(
		'gnnagency',
		array(
			'label'       => $label,
			'description' => $description,
			'featured'    => true,
			'order'       => 30,
			'profile'     => array(
				'label'        => $label,
				'description'  => $description,
				'heading'      => __( 'Design in motion.', 'gnn' ),
				'accent'       => '#c6f432',
				'accent_light' => '#536c13',
				'accent_ink'   => '#11140a',
				'header'       => 'minimal',
				'radius'       => 0,
				'font'         => 'manrope',
				'heading_font' => 'space',
				'mode'         => 'dark',
				'light'        => array( '#f5f4ee', '#eae9e1', '#dfded5', '#191a17', '#50524a', '#c6c6ba' ),
				'dark'         => array( '#111210', '#191b17', '#242620', '#f5f4ee', '#b9bcb0', '#3a3d33' ),
				'options'      => array(
					'header_height' => 80,
					'topbar_enable' => 0,
					'sticky_header' => 0,
					'show_search'   => 0,
					'show_cart'     => 0,
					'show_toggle'   => 1,
				),
			),
			'pages'       => 'gnn_gnnagency_pages',
			'page'        => 'gnn_gnnagency_page',
			'data'        => 'gnn_gnnagency_data',
			'css'         => 'demos/gnnagency.css',
			'preview'     => 'assets/images/demo/gnnagency/preview.jpg',
		)
	);
	add_shortcode( 'gnnagency_journal', 'gnn_gnnagency_journal_posts' );
	add_action( 'add_attachment', 'gnn_gnnagency_seed_alt' );
	add_action( 'wp_after_insert_post', 'gnn_gnnagency_seed_journal_image', 10, 2 );
}

/** Primary navigation in page order. */
function gnn_gnnagency_pages() {
	return array(
		'home'     => __( 'Home', 'gnn' ),
		'work'     => __( 'Work', 'gnn' ),
		'services' => __( 'Services', 'gnn' ),
		'studio'   => __( 'Studio', 'gnn' ),
		'journal'  => __( 'Journal', 'gnn' ),
		'contact'  => __( 'Contact', 'gnn' ),
	);
}

/** Descriptions for the bundled assets, keyed by filename stem. */
function gnn_gnnagency_images() {
	return array(
		'hero'      => '',
		'work-1'    => __( 'Smartphone displaying the cycling app interface', 'gnn' ),
		'work-2'    => __( 'Print pieces from the Silt Assembly identity laid side by side', 'gnn' ),
		'work-3'    => __( 'Illuminated Night Circuit campaign billboard after dark', 'gnn' ),
		'work-4'    => __( 'Laptop displaying the Open Form exhibition website', 'gnn' ),
		'work-5'    => __( 'Coral red packaging boxes arranged in a stack', 'gnn' ),
		'work-6'    => __( 'Liquid chrome forms from the Viscous Matter motion study', 'gnn' ),
		'studio-1'  => __( 'Designers at work in the studio after dark', 'gnn' ),
		'studio-2'  => __( 'Studio wall covered with references and colour studies', 'gnn' ),
		'journal-1' => __( 'A designer sketching ideas on a tablet', 'gnn' ),
		'journal-2' => __( 'A cloud of lime ink dispersing in water', 'gnn' ),
		'journal-3' => __( 'Workshop participants arranging sticky notes on a wall', 'gnn' ),
	);
}

/** Seed data in the shared demo importer format. */
function gnn_gnnagency_data() {
	return array(
		'images'   => array_keys( gnn_gnnagency_images() ),
		'projects' => array(
			'work-1' => array( __( 'Switchback Index', 'gnn' ), __( 'An interface for city cyclists that keeps route changes and road closures legible at a glance.', 'gnn' ), __( 'Digital product', 'gnn' ), '2026' ),
			'work-2' => array( __( 'Silt Assembly', 'gnn' ), __( 'A modular identity and print system for a programme of exhibitions about the materials beneath our feet.', 'gnn' ), __( 'Branding', 'gnn' ), '2026' ),
			'work-3' => array( __( 'Night Circuit', 'gnn' ), __( 'A street campaign that uses floodlit typography to guide audiences to late-night screenings.', 'gnn' ), __( 'Campaign', 'gnn' ), '2025' ),
			'work-4' => array( __( 'Open Form Archive', 'gnn' ), __( 'An exhibition website that lets visitors trace twelve installations through sketches, film and installation notes.', 'gnn' ), __( 'Digital product', 'gnn' ), '2025' ),
			'work-5' => array( __( 'Ember Parcel', 'gnn' ), __( 'Coral cartons and large batch codes turn small-run pantry goods into a family that is easy to spot on the shelf.', 'gnn' ), __( 'Packaging', 'gnn' ), '2025' ),
			'work-6' => array( __( 'Viscous Matter', 'gnn' ), __( 'A series of liquid chrome loops gives an experimental sound programme a distinct visual rhythm.', 'gnn' ), __( 'Motion', 'gnn' ), '2024' ),
		),
		'articles' => array(
			array( __( 'Sketching past the obvious', 'gnn' ), __( 'Our first sketches usually repeat an assumption from the brief. Drawing six variations before opening the design file helps us see which parts of the idea are worth keeping.', 'gnn' ) ),
			array( __( 'One colour, five materials', 'gnn' ), __( 'The same lime behaved differently on coated paper, uncoated stock, fabric, glass and screen. We kept a physical sample beside every proof and adjusted each surface on its own terms.', 'gnn' ) ),
			array( __( 'What we leave on the workshop wall', 'gnn' ), __( 'We sort workshop notes into decisions, open questions and ideas to test. A photo of the wall gives everyone a record of what was agreed and what still needs a decision.', 'gnn' ) ),
		),
		'slides'   => array(),
		'products' => array(),
	);
}

/**
 * Describe newly imported media.
 *
 * @param int $id New attachment ID.
 */
function gnn_gnnagency_seed_alt( $id ) {
	$marker = (string) get_post_meta( $id, '_gnn_demo', true );
	if ( 0 !== strpos( $marker, 'gnnagency:image-' ) ) {
		return;
	}
	$images = gnn_gnnagency_images();
	$name   = substr( $marker, strlen( 'gnnagency:image-' ) );
	if ( isset( $images[ $name ] ) && ucfirst( str_replace( '-', ' ', $name ) ) === get_post_meta( $id, '_wp_attachment_image_alt', true ) ) {
		update_post_meta( $id, '_wp_attachment_image_alt', $images[ $name ] );
	}
}

/**
 * Match journal images to the shared importer's zero-indexed article records.
 *
 * @param int     $id   Saved record ID.
 * @param WP_Post $post Saved record.
 */
function gnn_gnnagency_seed_journal_image( $id, $post ) {
	if ( 'post' !== $post->post_type || 'publish' !== $post->post_status || '1' !== get_post_meta( $id, '_gnn_demo_pending', true ) ) {
		return;
	}
	$marker = (string) get_post_meta( $id, '_gnn_demo', true );
	if ( ! preg_match( '/^gnnagency:journal-([0-2])$/D', $marker, $match ) || has_post_thumbnail( $id ) ) {
		return;
	}
	$image_ids = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_gnn_demo',
			'meta_value'     => 'gnnagency:image-journal-' . ( (int) $match[1] + 1 ),
		)
	);
	if ( $image_ids ) {
		set_post_thumbnail( $id, $image_ids[0] );
	}
}

/** Render the live journal through WordPress's native Latest Posts block. */
function gnn_gnnagency_journal_posts( $atts ) {
	$atts = shortcode_atts( array( 'limit' => 3 ), $atts, 'gnnagency_journal' );
	return render_block(
		array(
			'blockName'    => 'core/latest-posts',
			'attrs'        => array(
				'postsToShow'             => max( 1, min( 12, absint( $atts['limit'] ) ) ),
				'displayFeaturedImage'    => true,
				'featuredImageSizeSlug'   => 'large',
				'displayPostDate'         => true,
				'displayPostContent'      => true,
				'displayPostContentRadio' => 'excerpt',
				'excerptLength'           => 24,
			),
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		)
	);
}

/** Use imported media when present so Elementor and WordPress can use attachment metadata. */
function gnn_gnnagency_image( $name, $media, $class = '' ) {
	$images = gnn_gnnagency_images();
	$widget = gnn_st_img( 'gnnagency', $name, $images[ $name ], $class );
	$id     = (int) ( $media[ $name ] ?? 0 );
	$url    = $id ? wp_get_attachment_image_url( $id, 'full' ) : false;
	if ( $url ) {
		$widget['settings']['image']['id']  = $id;
		$widget['settings']['image']['url'] = $url;
		$widget['settings']['image_size']   = 'hero' === $name ? 'full' : 'large';
	}
	return $widget;
}

/** Common section shell. */
function gnn_gnnagency_band( $name, $children ) {
	return gnn_st_band( 'gnn-gnnagency', $name, $children );
}

/** Intro title and lead for an inner page. */
function gnn_gnnagency_page_head( $key, $title, $lead ) {
	$pages = gnn_gnnagency_pages();
	$head  = gnn_st_page_head( 'gnn-gnnagency', $pages[ $key ], $title, $lead );
	$head['settings']['css_classes'] .= ' gnn-gnnagency-pagehead-' . $key;
	return $head;
}

/** Use the configured studio inbox, with a reserved sample address for clean installs. */
function gnn_gnnagency_email() {
	$email = sanitize_email( (string) gnn_option( 'company_email' ) );
	return $email ? $email : 'hello@gnnagency.example';
}

/** An editable mail link used by the closing band and the Contact page. */
function gnn_gnnagency_mail_link() {
	$email = gnn_gnnagency_email();
	return gnn_st_b( $email, 'mailto:' . $email, 'gnn-gnnagency-mail' );
}

/** Shared closing invitation with both email and project enquiry links. */
function gnn_gnnagency_cta( $ids ) {
	return gnn_gnnagency_band(
		'cta',
		array(
			gnn_st_p( __( 'New projects', 'gnn' ), 'gnn-eyebrow' ),
			gnn_st_h( __( 'Let’s talk.', 'gnn' ), 2 ),
			gnn_gnnagency_mail_link(),
			gnn_st_b( __( 'Plan a project', 'gnn' ), gnn_st_link( $ids, 'contact' ), 'gnn-link' ),
		)
	);
}

/** Capability copy shared by the home list and the Services detail blocks. */
function gnn_gnnagency_capabilities() {
	return array(
		array(
			__( 'Strategy', 'gnn' ),
			__( 'Find the idea that holds the work together.', 'gnn' ),
			array( __( 'Audience research', 'gnn' ), __( 'Brand positioning', 'gnn' ), __( 'Naming and messaging', 'gnn' ) ),
		),
		array(
			__( 'Brand identity', 'gnn' ),
			__( 'Make that idea recognisable across print, packaging and screen.', 'gnn' ),
			array( __( 'Visual identity', 'gnn' ), __( 'Type and colour systems', 'gnn' ), __( 'Brand guidelines', 'gnn' ) ),
		),
		array(
			__( 'Digital design', 'gnn' ),
			__( 'Give people a clear path through websites and products.', 'gnn' ),
			array( __( 'Information architecture', 'gnn' ), __( 'Interactive prototypes', 'gnn' ), __( 'Component libraries', 'gnn' ) ),
		),
		array(
			__( 'Motion and campaigns', 'gnn' ),
			__( 'Give launches a visual rhythm across formats.', 'gnn' ),
			array( __( 'Art direction', 'gnn' ), __( 'Motion toolkits', 'gnn' ), __( 'Launch assets', 'gnn' ) ),
		),
	);
}

/** A group of editorial rows, with optional sequence numbers. */
function gnn_gnnagency_rows( $items, $numbered = false ) {
	$rows = array();
	foreach ( $items as $index => $item ) {
		$rows[] = gnn_st_c(
			'gnn-gnnagency-row',
			array(
				$numbered ? gnn_st_p( sprintf( '%02d', $index + 1 ), 'gnn-gnnagency-index' ) : null,
				gnn_st_h( $item[0], 3 ),
				gnn_st_p( $item[1] ),
			),
			true
		);
	}
	return gnn_st_c( 'gnn-gnnagency-rows' . ( $numbered ? ' gnn-gnnagency-rows-numbered' : '' ), $rows, true );
}

/** Four phases used on Home and Services. */
function gnn_gnnagency_process( $eyebrow ) {
	return gnn_gnnagency_band(
		'process',
		array(
			gnn_st_head( $eyebrow, __( 'From brief to launch.', 'gnn' ) ),
			gnn_st_cards(
				array(
					array( __( 'Discover', 'gnn' ), __( 'Meet the team, review the existing work and agree what needs to change.', 'gnn' ) ),
					array( __( 'Define', 'gnn' ), __( 'Set the creative direction and agree how we will judge the work.', 'gnn' ) ),
					array( __( 'Design', 'gnn' ), __( 'Test the idea in actual layouts, prototypes and production samples.', 'gnn' ) ),
					array( __( 'Deliver', 'gnn' ), __( 'Prepare the assets, test the rollout and walk your team through the handover.', 'gnn' ) ),
				),
				'gnn-cols-4 gnn-gnnagency-steps'
			),
		)
	);
}

/** Curated project cards with native widgets and links to the imported records. */
function gnn_gnnagency_selected_work( $ids, $data ) {
	$projects = gnn_gnnagency_data()['projects'];
	$cards    = array();
	foreach ( $projects as $name => $project ) {
		$id  = (int) ( $data['projects'][ $name ] ?? 0 );
		$url = $id && 'publish' === get_post_status( $id ) ? get_permalink( $id ) : false;
		$cards[] = gnn_st_c(
			'gnn-gnnagency-project',
			array(
				gnn_gnnagency_image( $name, $data['media'] ?? array(), 'gnn-gnnagency-project-image' ),
				gnn_st_c(
					'gnn-gnnagency-project-caption',
					array(
						gnn_st_p( $project[2] . ' / ' . $project[3], 'gnn-eyebrow' ),
						gnn_st_h( $project[0], 3 ),
						$url ? gnn_st_b( __( 'View project', 'gnn' ), $url, 'gnn-link' ) : null,
					),
					true
				),
			),
			true
		);
	}
	return gnn_gnnagency_band(
		'selected',
		array(
			gnn_st_c(
				'gnn-headrow',
				array(
					gnn_st_head( '01 / ' . __( 'Portfolio', 'gnn' ), __( 'Selected work.', 'gnn' ) ),
					gnn_st_b( __( 'All projects', 'gnn' ), gnn_st_link( $ids, 'work' ), 'gnn-link' ),
				),
				true
			),
			gnn_st_c( 'gnn-gnnagency-projects', $cards, true ),
		)
	);
}

/** Home composition. */
function gnn_gnnagency_home( $ids, $data ) {
	return array(
		gnn_st_c(
			'gnn-gnnagency gnn-gnnagency-hero',
			array(
				gnn_gnnagency_image( 'hero', $data['media'] ?? array(), 'gnn-gnnagency-hero-image' ),
				gnn_st_c(
					'gnn-wrap gnn-gnnagency-hero-copy',
					array(
						gnn_st_p( __( 'Independent creative studio', 'gnn' ), 'gnn-eyebrow' ),
						gnn_st_h( __( 'Design in motion.', 'gnn' ), 1 ),
						gnn_st_c(
							'gnn-gnnagency-hero-note',
							array(
								gnn_st_p( __( 'We build identities, digital experiences and campaigns with a point of view.', 'gnn' ), 'gnn-lead' ),
								gnn_st_b( __( 'Explore our work', 'gnn' ), gnn_st_link( $ids, 'work' ), 'gnn-btn-main' ),
							),
							true
						),
					),
					true
				),
			)
		),
		gnn_gnnagency_band( 'marquee', array( gnn_st_p( __( 'Strategy ✳ Identity ✳ Digital ✳ Campaigns ✳ Motion', 'gnn' ) ) ) ),
		gnn_gnnagency_selected_work( $ids, $data ),
		gnn_gnnagency_band(
			'capabilities',
			array(
				gnn_st_head( '02 / ' . __( 'Capabilities', 'gnn' ), __( 'What we do.', 'gnn' ) ),
				gnn_gnnagency_rows( gnn_gnnagency_capabilities(), true ),
				gnn_st_b( __( 'Explore our services', 'gnn' ), gnn_st_link( $ids, 'services' ), 'gnn-link' ),
			)
		),
		gnn_gnnagency_process( '03 / ' . __( 'Process', 'gnn' ) ),
		gnn_gnnagency_band(
			'studio-band',
			array(
				gnn_gnnagency_image( 'studio-1', $data['media'] ?? array(), 'gnn-gnnagency-studio-image' ),
				gnn_st_c(
					'gnn-copy',
					array(
						gnn_st_head( '04 / ' . __( 'Studio', 'gnn' ), __( 'Six people. One room.', 'gnn' ), __( 'Designers, strategists and a producer work side by side from the first workshop to the final proof.', 'gnn' ) ),
						gnn_st_b( __( 'Meet the studio', 'gnn' ), gnn_st_link( $ids, 'studio' ), 'gnn-link' ),
					),
					true
				),
			)
		),
		gnn_gnnagency_band(
			'recognition',
			array(
				gnn_st_head( '05 / ' . __( 'Recognition', 'gnn' ), __( 'On the record.', 'gnn' ) ),
				gnn_gnnagency_rows(
					array(
						array( __( 'Design annual', 'gnn' ), __( 'Shortlist, 2025', 'gnn' ) ),
						array( __( 'Independent print review', 'gnn' ), __( 'Packaging selection, 2025', 'gnn' ) ),
						array( __( 'Screen and type showcase', 'gnn' ), __( 'Motion selection, 2024', 'gnn' ) ),
					)
				),
			)
		),
		gnn_gnnagency_band(
			'journal',
			array(
				gnn_st_c(
					'gnn-headrow',
					array(
						gnn_st_head( '06 / ' . __( 'Journal', 'gnn' ), __( 'Studio notes.', 'gnn' ) ),
						gnn_st_b( __( 'Read the journal', 'gnn' ), gnn_st_link( $ids, 'journal' ), 'gnn-link' ),
					),
					true
				),
				gnn_st_w( 'shortcode', array( 'shortcode' => '[gnnagency_journal]' ) ),
			)
		),
		gnn_gnnagency_cta( $ids ),
	);
}

/** Work archive with the theme's filterable live project collection. */
function gnn_gnnagency_work( $ids ) {
	return array(
		gnn_gnnagency_page_head( 'work', __( 'Work out in the world.', 'gnn' ), __( 'Identities, digital products and campaigns made to be used, seen and remembered.', 'gnn' ) ),
		gnn_gnnagency_band(
			'work',
			array(
				gnn_st_h( __( 'Project archive', 'gnn' ), 2, 'gnn-gnnagency-accessible' ),
				gnn_st_mod( 'portfolio', array( 'columns' => 2, 'limit' => 12, 'filters' => 'yes' ) ),
			)
		),
		gnn_gnnagency_cta( $ids ),
	);
}

/** Services, deliverables and practical questions. */
function gnn_gnnagency_services( $ids ) {
	$blocks = array();
	foreach ( gnn_gnnagency_capabilities() as $index => $capability ) {
		$list = '';
		foreach ( $capability[2] as $deliverable ) {
			$list .= '<li>' . esc_html( $deliverable ) . '</li>';
		}
		$blocks[] = gnn_st_c(
			'gnn-gnnagency-service',
			array(
				gnn_st_p( sprintf( '%02d', $index + 1 ), 'gnn-gnnagency-index' ),
				gnn_st_h( $capability[0], 3 ),
				gnn_st_c(
					'gnn-copy',
					array(
						gnn_st_p( $capability[1] ),
						gnn_st_w( 'text-editor', array( 'editor' => '<ul>' . $list . '</ul>' ) ),
					),
					true
				),
			),
			true
		);
	}
	return array(
		gnn_gnnagency_page_head( 'services', __( 'From idea to form.', 'gnn' ), __( 'Work with us on a single launch or build a design system your team can use for years.', 'gnn' ) ),
		gnn_gnnagency_band( 'services', array_merge( array( gnn_st_head( '', __( 'Capabilities', 'gnn' ) ) ), $blocks ) ),
		gnn_gnnagency_process( '02 / ' . __( 'Engagement', 'gnn' ) ),
		gnn_gnnagency_band(
			'faq',
			array(
				gnn_st_head( '03 / ' . __( 'Before we start', 'gnn' ), __( 'Common questions.', 'gnn' ) ),
				gnn_gnnagency_rows(
					array(
						array( __( 'How long does a project take?', 'gnn' ), __( 'An identity usually takes six to eight weeks. A combined identity and website typically takes twelve, depending on content and review time.', 'gnn' ) ),
						array( __( 'Can you work with our existing brand?', 'gnn' ), __( 'Yes. Bring the guidelines and a few examples of where they work well. We will agree which parts of the system to keep and which to extend.', 'gnn' ) ),
						array( __( 'Who builds the website?', 'gnn' ), __( 'We can hand prototypes and components to your developers or bring in a production partner and stay involved through testing.', 'gnn' ) ),
						array( __( 'What will we receive at handover?', 'gnn' ), __( 'Editable source files, exported assets and usage notes. We also run a walkthrough with the people who will maintain the work.', 'gnn' ) ),
					)
				),
			)
		),
		gnn_gnnagency_cta( $ids ),
	);
}

/** Studio manifesto, images, values and team. */
function gnn_gnnagency_studio( $ids, $data ) {
	$media = $data['media'] ?? array();
	return array(
		gnn_gnnagency_page_head( 'studio', __( 'Better in the room.', 'gnn' ), __( 'An independent studio bringing strategy, design and production to the same table.', 'gnn' ) ),
		gnn_gnnagency_band(
			'manifesto',
			array(
				gnn_st_head( '01 / ' . __( 'Manifesto', 'gnn' ), __( 'An idea earns its place when people can use it.', 'gnn' ) ),
				gnn_st_p( __( 'We take the work off the moodboard early. We put it on a screen, in someone’s hand or up on a wall, then look at what holds up.', 'gnn' ), 'gnn-lead' ),
			)
		),
		gnn_gnnagency_band(
			'studio-pair',
			array(
				gnn_gnnagency_image( 'studio-1', $media, 'gnn-gnnagency-studio-image' ),
				gnn_gnnagency_image( 'studio-2', $media, 'gnn-gnnagency-studio-image' ),
			)
		),
		gnn_gnnagency_band(
			'values',
			array(
				gnn_st_head( '02 / ' . __( 'Values', 'gnn' ), __( 'How we work together.', 'gnn' ) ),
				gnn_st_cards(
					array(
						array( __( 'Show the work early', 'gnn' ), __( 'A rough prototype leaves room for the people using it to change its direction.', 'gnn' ) ),
						array( __( 'Make room for dissent', 'gnn' ), __( 'We ask who disagrees before a decision hardens into a brief.', 'gnn' ) ),
						array( __( 'Design for handover', 'gnn' ), __( 'Build assets your team can edit under a real deadline.', 'gnn' ) ),
					),
					'gnn-cols-3'
				),
			)
		),
		gnn_gnnagency_band(
			'team',
			array(
				gnn_st_head( '03 / ' . __( 'Team', 'gnn' ), __( 'Around the table.', 'gnn' ) ),
				gnn_gnnagency_rows(
					array(
						array( __( 'Mara Bellfold', 'gnn' ), __( 'Creative director', 'gnn' ) ),
						array( __( 'Ivo Fenmere', 'gnn' ), __( 'Brand strategist', 'gnn' ) ),
						array( __( 'Lena Ashquill', 'gnn' ), __( 'Design director', 'gnn' ) ),
						array( __( 'Tari Vellstrand', 'gnn' ), __( 'Digital designer', 'gnn' ) ),
						array( __( 'Noor Flintmere', 'gnn' ), __( 'Motion designer', 'gnn' ) ),
						array( __( 'Remy Wrenfield', 'gnn' ), __( 'Studio producer', 'gnn' ) ),
					)
				),
			)
		),
		gnn_gnnagency_cta( $ids ),
	);
}

/** Live index of articles. */
function gnn_gnnagency_journal() {
	return array(
		gnn_gnnagency_page_head( 'journal', __( 'Notes in the margins.', 'gnn' ), __( 'Experiments, materials and decisions from the studio floor.', 'gnn' ) ),
		gnn_gnnagency_band( 'journal', array( gnn_st_w( 'shortcode', array( 'shortcode' => '[gnnagency_journal limit="12"]' ) ) ) ),
	);
}

/** Contact details and an email-led enquiry area or an editor-provided form. */
function gnn_gnnagency_contact() {
	$details = gnn_st_contact();
	// The email is already displayed above. Show only configured additional details.
	$details['elements'] = array_values(
		array_filter(
			array_slice( $details['elements'], 1 ),
			static function ( $widget ) {
				return '<p>' . esc_html( __( 'Add your address here.', 'gnn' ) ) . '</p>' !== ( $widget['settings']['editor'] ?? '' );
			}
		)
	);
	return array(
		gnn_gnnagency_page_head( 'contact', __( 'Tell us what’s next.', 'gnn' ), __( 'For commissions, collaborations and questions about our work.', 'gnn' ) ),
		gnn_gnnagency_band( 'contact-mail', array( gnn_gnnagency_mail_link() ) ),
		gnn_gnnagency_band(
			'contact',
			array(
				gnn_st_c(
					'gnn-copy',
					array(
						gnn_st_h( __( 'Studio details', 'gnn' ), 2 ),
						gnn_st_p( __( 'We review enquiries on weekdays.', 'gnn' ) ),
						$details,
					),
					true
				),
				gnn_st_c(
					'gnn-copy gnn-gnnagency-enquiry',
					array(
						gnn_st_h( __( 'Project enquiries', 'gnn' ), 2 ),
						gnn_st_p( __( 'Tell us what you’re making, when you need it and the budget range you have in mind. Share a brief if you have one.', 'gnn' ) ),
						gnn_st_b( __( 'Email your brief', 'gnn' ), 'mailto:' . gnn_gnnagency_email(), 'gnn-link' ),
					),
					true
				),
			)
		),
	);
}

/**
 * Dispatch the registered page callback.
 *
 * @param string $key  Page key.
 * @param array  $ids  Installed page IDs.
 * @param array  $data Imported records and media.
 */
function gnn_gnnagency_page( $key, $ids, $data ) {
	if ( ! isset( gnn_gnnagency_pages()[ $key ] ) ) {
		return array();
	}
	return call_user_func( 'gnn_gnnagency_' . $key, $ids, $data );
}
