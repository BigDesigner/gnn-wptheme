<?php
/**
 * GNNwedding ready-made site template data.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GNNwedding: elegant, story and gallery-focused template for weddings and celebrations.
 *
 * @return array Template data definition.
 */
function gnn_template_data_wedding() {
	return array(
		'slug'        => 'wedding',
		'label'       => __( 'GNNwedding', 'gnn' ),
		'description' => __( 'An elegant, editorial website for wedding planners, event designers and celebration venues with galleries and packages.', 'gnn' ),
		'requires'    => array( 'elementor' ),
		'tokens'      => array(
			'accent'       => '#b88657',
			'accent_light' => '#926338',
			'accent_ink'   => '#ffffff',
			'radius'       => 12,
			'font'         => 'manrope',
			'heading_font' => 'space',
			'mode'         => 'light',
			'light'        => array( '#fcfaf7', '#f7f3ec', '#ece4d8', '#26201b', '#6e6256', '#dfd5c7' ),
			'dark'         => array( '#181512', '#211c18', '#2c2621', '#f7f4ee', '#b2a698', '#383029' ),
		),
		'header'      => array(
			'preset'    => 'transparent',
			'cta_label' => __( 'Plan your day', 'gnn' ),
			'options'   => array(
				'topbar_enable'     => 0,
				'header_height'     => 76,
				'header_search_bar' => 0,
				'show_cart'         => 0,
				'show_search'       => 0,
			),
		),
		'pages'       => array(
			'home'     => array(
				'title'    => __( 'Home', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'centered',
						array(
							'eyebrow'         => __( 'Weddings & bespoke celebrations', 'gnn' ),
							'title'           => __( 'Crafting unforgettable wedding celebrations.', 'gnn' ),
							'text'            => __( 'Bespoke planning, intentional design and seamless coordination so you can experience every moment with calm confidence.', 'gnn' ),
							'button_label'    => __( 'Plan your day', 'gnn' ),
							'button_url'      => 'page:contact',
							'secondary_label' => __( 'Explore services', 'gnn' ),
							'secondary_url'   => 'page:services',
						),
					),
					array(
						'about-split',
						'image-left',
						array(
							'eyebrow'      => __( 'Our philosophy', 'gnn' ),
							'title'        => __( 'Thoughtful design tailored to your story', 'gnn' ),
							'text'         => __( 'We collaborate closely with couples to shape celebrations that feel authentic, unhurried and beautifully personal from first welcome to final farewell.', 'gnn' ),
							'button_label' => __( 'About our studio', 'gnn' ),
							'button_url'   => 'page:about',
							'image'        => 'media:about',
							'items'        => array(
								__( 'Personalised creative direction and floral styling', 'gnn' ),
								__( 'Curated vendor matching and transparent management', 'gnn' ),
								__( 'Calm, discreet on-site coordination on your wedding day', 'gnn' ),
							),
						),
					),
					array(
						'services',
						'cards-3',
						array(
							'eyebrow'      => __( 'Offerings', 'gnn' ),
							'title'        => __( 'Bespoke planning and design services', 'gnn' ),
							'text'         => __( 'Choose full-scope production or focused coordination for your celebration.', 'gnn' ),
							'button_label' => __( 'View all services', 'gnn' ),
							'button_url'   => 'page:services',
							'items'        => array(
								array(
									'title' => __( 'Full planning & production', 'gnn' ),
									'text'  => __( 'End-to-end guidance from venue scouting and design conception to complete wedding day management.', 'gnn' ),
								),
								array(
									'title' => __( 'Design & aesthetic styling', 'gnn' ),
									'text'  => __( 'Cohesive visual direction covering colour palettes, tablescapes, florals, stationery and ambient lighting.', 'gnn' ),
								),
								array(
									'title' => __( 'Month-of coordination', 'gnn' ),
									'text'  => __( 'Detailed timeline finalisation, supplier logistics handover and seamless day-of direction.', 'gnn' ),
								),
							),
						),
					),
					array(
						'gallery',
						'cols-3',
						array(
							'eyebrow' => __( 'Portfolio', 'gnn' ),
							'title'   => __( 'Moments, decor and atmospheres', 'gnn' ),
							'text'    => __( 'A visual collection of recent ceremonies, floral details and celebrations.', 'gnn' ),
						),
					),
					array(
						'pricing',
						'three',
						array(
							'eyebrow'      => __( 'Packages', 'gnn' ),
							'title'        => __( 'Curated celebration packages', 'gnn' ),
							'text'         => __( 'Replace the placeholder figures below with your planning and coordination investment.', 'gnn' ),
							'button_label' => __( 'Inquire about package', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title'    => __( 'Intimate celebration', 'gnn' ),
									'text'     => __( 'Designed for gatherings of up to fifty guests with refined personal details.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Per celebration', 'gnn' ),
									'featured' => false,
									'features' => array(
										__( 'Venue styling and table design concept', 'gnn' ),
										__( 'Curated vendor recommendations', 'gnn' ),
										__( 'Full wedding day coordination', 'gnn' ),
									),
								),
								array(
									'title'    => __( 'Signature wedding', 'gnn' ),
									'text'     => __( 'Comprehensive planning, design and on-site production for your main celebration.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Per celebration', 'gnn' ),
									'featured' => true,
									'features' => array(
										__( 'Full creative direction and 3D floor plans', 'gnn' ),
										__( 'Budget tracking and supplier contract review', 'gnn' ),
										__( 'Two dedicated coordinators on the wedding day', 'gnn' ),
									),
								),
								array(
									'title'    => __( 'Destination weekend', 'gnn' ),
									'text'     => __( 'Multi-day coordination for destination weddings, welcome gatherings and farewell brunches.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Per celebration', 'gnn' ),
									'featured' => false,
									'features' => array(
										__( 'Multi-event timeline management and guest logistics', 'gnn' ),
										__( 'Local vendor curation and rehearsal dinner styling', 'gnn' ),
										__( 'Full weekend lead planner attendance', 'gnn' ),
									),
								),
							),
						),
					),
					array(
						'testimonial',
						'featured',
						array(
							'eyebrow' => __( 'Kind words', 'gnn' ),
							'title'   => __( 'Stories from our couples', 'gnn' ),
							'text'    => __( 'Replace this placeholder with verified feedback from celebrated couples.', 'gnn' ),
							'items'   => array(
								array(
									'quote'  => __( 'Add a customer quote here.', 'gnn' ),
									'author' => __( 'Name Surname', 'gnn' ),
									'role'   => __( 'Married couple', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'split',
						array(
							'eyebrow'      => __( 'Let’s connect', 'gnn' ),
							'title'        => __( 'Ready to begin planning your wedding?', 'gnn' ),
							'text'         => __( 'Inquire about availability for your date and schedule an introductory conversation.', 'gnn' ),
							'button_label' => __( 'Inquire date', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'services' => array(
				'title'    => __( 'Services', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'What we do', 'gnn' ),
							'title'        => __( 'Planning and design tailored to your vision.', 'gnn' ),
							'text'         => __( 'Explore our coordination and styling services for celebrations of any scale.', 'gnn' ),
							'button_label' => __( 'Inquire date', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'services',
						'rows',
						array(
							'eyebrow'      => __( 'Detailed services', 'gnn' ),
							'title'        => __( 'Guidance at every stage of the planning journey', 'gnn' ),
							'text'         => __( 'Select the level of involvement that best supports your timeline and schedule.', 'gnn' ),
							'button_label' => __( 'Discuss this service', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title' => __( 'Full wedding production', 'gnn' ),
									'text'  => __( 'Complete management from initial venue search, budgeting and contract negotiation to wedding day execution.', 'gnn' ),
								),
								array(
									'title' => __( 'Design concept & styling', 'gnn' ),
									'text'  => __( 'Visual direction, moodboards, custom linen and tableware selection, floral styling and ambient design.', 'gnn' ),
								),
								array(
									'title' => __( 'Month-of coordination', 'gnn' ),
									'text'  => __( 'Six weeks of intensive coordination, timeline creation, vendor briefings and on-the-day management.', 'gnn' ),
								),
								array(
									'title' => __( 'Private events & parties', 'gnn' ),
									'text'  => __( 'Rehearsal dinners, welcome cocktails, post-wedding brunches and anniversary celebrations.', 'gnn' ),
								),
							),
						),
					),
					array(
						'process',
						'horizontal',
						array(
							'eyebrow' => __( 'The journey', 'gnn' ),
							'title'   => __( 'How we work together', 'gnn' ),
							'text'    => __( 'Four clear steps from our first consultation to your send-off.', 'gnn' ),
							'items'   => array(
								array(
									'title' => __( 'Consult', 'gnn' ),
									'text'  => __( 'We meet to discuss your date, vision, priorities and planning expectations.', 'gnn' ),
								),
								array(
									'title' => __( 'Design', 'gnn' ),
									'text'  => __( 'We build moodboards, choose palettes and match the right creative partners.', 'gnn' ),
								),
								array(
									'title' => __( 'Coordinate', 'gnn' ),
									'text'  => __( 'We handle logistics, manage supplier timelines and review every layout detail.', 'gnn' ),
								),
								array(
									'title' => __( 'Celebrate', 'gnn' ),
									'text'  => __( 'You immerse yourselves in your wedding day while our team directs the flow.', 'gnn' ),
								),
							),
						),
					),
					array(
						'pricing',
						'three',
						array(
							'eyebrow'      => __( 'Investment', 'gnn' ),
							'title'        => __( 'Wedding planning packages', 'gnn' ),
							'text'         => __( 'Transparent packages adapted to your guest count and location.', 'gnn' ),
							'button_label' => __( 'Select package', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'cta',
						'band',
						array(
							'eyebrow'      => __( 'Custom requests', 'gnn' ),
							'title'        => __( 'Looking for custom planning or partial styling?', 'gnn' ),
							'text'         => __( 'Share your celebration details and we will create a tailored proposal.', 'gnn' ),
							'button_label' => __( 'Get in touch', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'gallery'  => array(
				'title'    => __( 'Gallery', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Visual portfolio', 'gnn' ),
							'title'        => __( 'Celebrations, details and atmospheres.', 'gnn' ),
							'text'         => __( 'Explore ceremonies, intimate dinners and reception moments curated by our team.', 'gnn' ),
							'button_label' => __( 'Plan your day', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'gallery',
						'cols-3',
						array(
							'eyebrow' => __( 'Curated highlights', 'gnn' ),
							'title'   => __( 'Tablescapes, florals and ceremonies', 'gnn' ),
							'text'    => __( 'Photographs from recent weddings across historic villas, vineyards and gardens.', 'gnn' ),
						),
					),
					array(
						'portfolio',
						'filtered',
						array(
							'eyebrow' => __( 'Selected weddings', 'gnn' ),
							'title'   => __( 'Explore real celebrations', 'gnn' ),
							'text'    => __( 'Filter past weddings by celebration format and venue style.', 'gnn' ),
						),
					),
					array(
						'cta',
						'band',
						array(
							'eyebrow'      => __( 'Your celebration', 'gnn' ),
							'title'        => __( 'Let’s design a wedding as unique as your story', 'gnn' ),
							'text'         => __( 'Reach out to check availability for your preferred season and venue.', 'gnn' ),
							'button_label' => __( 'Inquire date', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'about'    => array(
				'title'    => __( 'About', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'split',
						array(
							'eyebrow'      => __( 'About the studio', 'gnn' ),
							'title'        => __( 'A calm and thoughtful approach to wedding planning.', 'gnn' ),
							'text'         => __( 'Tell visitors about your studio, your background in event design and your commitment to authentic celebrations.', 'gnn' ),
							'button_label' => __( 'Explore services', 'gnn' ),
							'button_url'   => 'page:services',
							'image'        => 'media:about',
						),
					),
					array(
						'values',
						'cards',
						array(
							'eyebrow' => __( 'Studio values', 'gnn' ),
							'title'   => __( 'What guides every celebration we craft', 'gnn' ),
							'text'    => __( 'Three guiding principles that shape our creative collaboration.', 'gnn' ),
							'items'   => array(
								array(
									'title' => __( 'Intentional beauty', 'gnn' ),
									'text'  => __( 'Every floral choice, paper texture and lighting cue serves the overall mood without excess.', 'gnn' ),
								),
								array(
									'title' => __( 'Unshakable calm', 'gnn' ),
									'text'  => __( 'We handle logistics behind the scenes so you and your families remain relaxed throughout.', 'gnn' ),
								),
								array(
									'title' => __( 'Guest hospitality', 'gnn' ),
									'text'  => __( 'We design celebrations from your guests’ perspective, ensuring warmth and effortless comfort.', 'gnn' ),
								),
							),
						),
					),
					array(
						'team',
						'three',
						array(
							'eyebrow' => __( 'The team', 'gnn' ),
							'title'   => __( 'Meet your planners and designers', 'gnn' ),
							'text'    => __( 'Replace these profile cards with your lead planners, floral designers and production coordinators.', 'gnn' ),
							'items'   => array(
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Creative director & lead planner', 'gnn' ),
									'bio'  => __( 'Add a short biography or design background here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Event designer & floral stylist', 'gnn' ),
									'bio'  => __( 'Add a short biography or design background here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Logistics & production manager', 'gnn' ),
									'bio'  => __( 'Add a short biography or design background here.', 'gnn' ),
								),
							),
						),
					),
					array(
						'process',
						'split-timeline',
						array(
							'eyebrow'      => __( 'Our approach', 'gnn' ),
							'title'        => __( 'A partnership rooted in transparency and care', 'gnn' ),
							'text'         => __( 'From your engagement to the final dance, we stand by your side with clear timelines and reliable guidance.', 'gnn' ),
							'button_label' => __( 'Get in touch', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title' => __( 'Listening deeply', 'gnn' ),
									'text'  => __( 'Understanding what matters most to you, your cultural traditions and your guests.', 'gnn' ),
								),
								array(
									'title' => __( 'Curating partners', 'gnn' ),
									'text'  => __( 'Selecting photographers, caterers and musicians whose values align with your vision.', 'gnn' ),
								),
								array(
									'title' => __( 'Refining details', 'gnn' ),
									'text'  => __( 'Perfecting menu tastings, floral mockups, transit schedules and weather contingencies.', 'gnn' ),
								),
								array(
									'title' => __( 'Seamless execution', 'gnn' ),
									'text'  => __( 'Managing the day so you can immerse yourself in every memory and embrace.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'split',
						array(
							'eyebrow'      => __( 'Start the conversation', 'gnn' ),
							'title'        => __( 'We would be honored to plan your wedding.', 'gnn' ),
							'text'         => __( 'Tell us about your date, estimated guest count and destination ideas.', 'gnn' ),
							'button_label' => __( 'Inquire date', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'blog'     => array(
				'title'    => __( 'Journal', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Wedding journal', 'gnn' ),
							'title'        => __( 'Inspiration, guides and real celebrations.', 'gnn' ),
							'text'         => __( 'Practical advice on timelines, styling insights and behind-the-scenes stories.', 'gnn' ),
							'button_label' => __( 'View services', 'gnn' ),
							'button_url'   => 'page:services',
						),
					),
					array(
						'blog',
						'grid',
						array(
							'eyebrow' => __( 'Latest articles', 'gnn' ),
							'title'   => __( 'From our wedding notebook', 'gnn' ),
							'text'    => __( 'Read recent articles on intentional wedding design and guest hospitality.', 'gnn' ),
						),
					),
					array(
						'newsletter',
						'centered',
						array(
							'eyebrow'      => __( 'Wedding newsletter', 'gnn' ),
							'title'        => __( 'Receive seasonal wedding guides', 'gnn' ),
							'text'         => __( 'Occasional planning checklists, venue spotlights and styling advice delivered to your inbox.', 'gnn' ),
							'button_label' => __( 'Subscribe', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'contact'  => array(
				'title'    => __( 'Contact', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Inquire with us', 'gnn' ),
							'title'        => __( 'Tell us about your wedding celebration.', 'gnn' ),
							'text'         => __( 'Share your planned date, venue ideas and guest count. We reply within one business day.', 'gnn' ),
							'button_label' => __( 'Explore services', 'gnn' ),
							'button_url'   => 'page:services',
						),
					),
					array(
						'contact',
						'split',
						array(
							'eyebrow'    => __( 'Get in touch', 'gnn' ),
							'title'      => __( 'Wedding date & consultation inquiries', 'gnn' ),
							'text'       => __( 'We accept a limited number of weddings each season to ensure dedicated focus on every couple.', 'gnn' ),
							'form_title' => __( 'Wedding inquiry form', 'gnn' ),
							'form_text'  => __( 'Add your wedding inquiry or contact form here with the form plugin you use.', 'gnn' ),
						),
					),
					array(
						'faq',
						'two-column',
						array(
							'eyebrow' => __( 'Common questions', 'gnn' ),
							'title'   => __( 'Frequently asked wedding questions', 'gnn' ),
							'text'    => __( 'Helpful details about our booking process, retainer and travel availability.', 'gnn' ),
							'items'   => array(
								array(
									'question' => __( 'How far in advance should we secure our date?', 'gnn' ),
									'answer'   => __( 'Couples typically reserve our planning services nine to eighteen months prior to their wedding date.', 'gnn' ),
								),
								array(
									'question' => __( 'Do you travel for destination weddings?', 'gnn' ),
									'answer'   => __( 'Yes. We frequently produce celebrations across coastal venues, historic estates and private countryside residences.', 'gnn' ),
								),
								array(
									'question' => __( 'Can you recommend and coordinate local vendors?', 'gnn' ),
									'answer'   => __( 'Yes. We curate and manage all vendor contracts, from florists and caterers to musicians and photographers.', 'gnn' ),
								),
								array(
									'question' => __( 'How many celebrations do you take per weekend?', 'gnn' ),
									'answer'   => __( 'We commit to only one wedding per weekend to guarantee our lead planner is present on your day.', 'gnn' ),
								),
							),
						),
					),
				),
			),
		),
		'menu'        => array( 'home', 'services', 'gallery', 'about', 'blog', 'contact' ),
		'content'     => array(
			'projects' => array(
				array(
					'title'    => __( 'Tuscan villa garden celebration', 'gnn' ),
					'excerpt'  => __( 'An intimate three-day weekend celebration featuring candlelit courtyard dining and olive branch florals.', 'gnn' ),
					'category' => __( 'Destination wedding', 'gnn' ),
					'year'     => '2026',
					'media'    => 'celebration-1',
				),
				array(
					'title'    => __( 'Coastal cliffside terrace ceremony', 'gnn' ),
					'excerpt'  => __( 'Minimalist white floral installations and sea views paired with sunset dinner music.', 'gnn' ),
					'category' => __( 'Full planning', 'gnn' ),
					'year'     => '2026',
					'media'    => 'celebration-2',
				),
				array(
					'title'    => __( 'Historic conservatory reception', 'gnn' ),
					'excerpt'  => __( 'Glasshouse botanical styling with suspended greenery, vintage crystal and acoustic quartet.', 'gnn' ),
					'category' => __( 'Design & styling', 'gnn' ),
					'year'     => '2025',
					'media'    => 'celebration-3',
				),
				array(
					'title'    => __( 'Country manor autumn gathering', 'gnn' ),
					'excerpt'  => __( 'Warm terracotta hues, open fireplaces and seasonal local fare for eighty close family and friends.', 'gnn' ),
					'category' => __( 'Intimate celebration', 'gnn' ),
					'year'     => '2025',
					'media'    => 'celebration-4',
				),
				array(
					'title'    => __( 'Lakeside private estate wedding', 'gnn' ),
					'excerpt'  => __( 'Sailcloth tent reception on open lawns with custom wooden bars and wildflower arrangements.', 'gnn' ),
					'category' => __( 'Full planning', 'gnn' ),
					'year'     => '2025',
					'media'    => 'celebration-5',
				),
			),
			'posts'    => array(
				array(
					'title'   => __( 'How to build an unhurried wedding timeline', 'gnn' ),
					'excerpt' => __( 'Strategic buffer times, sunset light considerations and tips for savoring every hour with your guests.', 'gnn' ),
					'media'   => 'news-1',
				),
				array(
					'title'   => __( 'Choosing seasonal florals that endure through the evening', 'gnn' ),
					'excerpt' => __( 'Insights on heat tolerance, local botanical sourcing and sustainable floral foam alternatives.', 'gnn' ),
					'media'   => 'news-2',
				),
				array(
					'title'   => __( 'Creating a meaningful tablescape for intimate dinners', 'gnn' ),
					'excerpt' => __( 'How intentional stationery, textured stoneware and warm candlelight foster memorable conversation.', 'gnn' ),
					'media'   => 'news-3',
				),
			),
		),
		'media'       => array(
			'hero'          => array(
				'alt'     => __( 'Romantic wedding reception table with delicate florals and soft candlelight', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Romantic wedding dining setup with soft champagne linen, ivory candles, delicate white and blush florals; warm neutral palette; editorial wedding photography, 16:9 aspect ratio',
			),
			'about'         => array(
				'alt'     => __( 'Wedding planner styling floral centerpieces and stationery details', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Hands arranging fine letterpress wedding stationery, silk ribbons and fresh garden roses on a textured limestone table; soft natural daylight, 4:3 aspect ratio',
			),
			'ceremony'      => array(
				'alt'     => __( 'Outdoor wedding ceremony arch adorned with garden florals', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Elegant outdoor ceremony arch with garden roses and greenery overlooking a peaceful meadow at golden hour; warm champagne and cream tones, 4:3 aspect ratio',
			),
			'details'       => array(
				'alt'     => __( 'Fine letterpress wedding invitation suite with wax seal and silk ribbon', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Flatlay of handmade cotton paper wedding invitations with calligraphy, gold wax seal and delicate botanicals; warm beige and gold accents, 4:3 aspect ratio',
			),
			'table'         => array(
				'alt'     => __( 'Intimate candlelit wedding reception banquet table', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Overhead perspective of a long banquet table set with taper candles, ribbed glassware, ceramic plates and linen napkins; warm ambient lighting, 4:3 aspect ratio',
			),
			'cake'          => array(
				'alt'     => __( 'Minimalist multi-tier wedding cake with organic sugar flowers', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Contemporary two-tier wedding cake with textured white frosting and delicate dried floral accents on a marble pedestal; clean elegant styling, 4:3 aspect ratio',
			),
			'celebration-1' => array(
				'alt'     => __( 'Historic villa courtyard wedding dinner under festoon lights', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Courtyard banquet under warm fairy lights with stone archways in the background; warm terracotta and champagne tones, 16:10 aspect ratio',
			),
			'celebration-2' => array(
				'alt'     => __( 'Coastal cliffside wedding ceremony terrace', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Breathtaking seaside wedding terrace with white chairs and floral aisle leading toward the ocean horizon; bright natural daylight, 16:10 aspect ratio',
			),
			'celebration-3' => array(
				'alt'     => __( 'Greenhouse conservatory reception with hanging botanical installations', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Glass greenhouse reception space filled with lush hanging greenery, warm pendant lighting and wooden tables, 16:10 aspect ratio',
			),
			'celebration-4' => array(
				'alt'     => __( 'Autumn countryside manor wedding table setting', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Autumnal wedding table with amber glassware, velvet ribbons and soft dried floral arrangements, 16:10 aspect ratio',
			),
			'celebration-5' => array(
				'alt'     => __( 'Sailcloth tent wedding reception on private estate lawn', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Glowing white sailcloth marquee tent on an open estate lawn at twilight with lantern-lit pathways, 16:10 aspect ratio',
			),
			'news-1'        => array(
				'alt'     => __( 'Couple walking through countryside gardens at dusk', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Married couple walking peacefully hand-in-hand through garden pathway at golden hour; soft romantic lighting and warm champagne tones, 16:9 aspect ratio',
			),
			'news-2'        => array(
				'alt'     => __( 'Fresh seasonal wedding flowers in ceramic vessels', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Arrangement of garden roses, ranunculus and sweet peas in handmade ceramic urns on a wooden workbench, 16:9 aspect ratio',
			),
			'news-3'        => array(
				'alt'     => __( 'Artisan table place setting with calligraphed name card', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Close-up of a tailored wedding table setting with linen napkin, gold cutlery and calligraphed name tag on handmade paper, 16:9 aspect ratio',
			),
		),
		'css'         => 'wedding',
	);
}

add_filter(
	'gnn_template_registry',
	function ( $templates ) {
		$templates['wedding'] = 'gnn_template_data_wedding';
		return $templates;
	}
);
