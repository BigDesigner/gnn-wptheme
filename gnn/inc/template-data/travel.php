<?php
/**
 * GNNtravel ready-made site template data.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GNNtravel: vibrant travel and expedition template focused on routes, tour packages and departures.
 *
 * @return array Template data definition.
 */
function gnn_template_data_travel() {
	return array(
		'slug'        => 'travel',
		'label'       => __( 'GNNtravel', 'gnn' ),
		'description' => __( 'A vibrant travel website for tour operators, expedition guides and small group adventures.', 'gnn' ),
		'requires'    => array( 'elementor' ),
		'tokens'      => array(
			'accent'       => '#0284c7',
			'accent_light' => '#0369a1',
			'accent_ink'   => '#ffffff',
			'radius'       => 12,
			'font'         => 'manrope',
			'heading_font' => 'space',
			'mode'         => 'light',
			'light'        => array( '#f8fafc', '#f1f5f9', '#e2e8f0', '#0f172a', '#475569', '#cbd5e1' ),
			'dark'         => array( '#0b1120', '#111c33', '#1a2948', '#f8fafc', '#94a3b8', '#243556' ),
		),
		'header'      => array(
			'preset'    => 'classic',
			'cta_label' => __( 'Book a trip', 'gnn' ),
			'options'   => array(
				'header_structure'  => 'classic',
				'header_surface'    => 'glass',
				'header_shape'      => 'full',
				'header_width'      => 'contained',
				'header_behavior'   => 'sticky',
				'header_overlay'    => 0,
				'topbar_enable'     => 1,
				'header_height'     => 76,
				'header_search_bar' => 0,
				'show_cart'         => 0,
				'show_search'       => 1,
			),
		),
		'pages'       => array(
			'home'     => array(
				'title'    => __( 'Home', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'video-placeholder',
						array(
							'eyebrow'         => __( 'Guided small group journeys', 'gnn' ),
							'title'           => __( 'Expeditions planned for the open trail.', 'gnn' ),
							'text'            => __( 'Carefully routed trekking, coastal journeys and alpine expeditions led by certified local guides.', 'gnn' ),
							'button_label'    => __( 'Explore routes', 'gnn' ),
							'button_url'      => 'page:tours',
							'secondary_label' => __( 'Custom journey', 'gnn' ),
							'secondary_url'   => 'page:contact',
							'image'           => 'media:hero',
						),
					),
					array(
						'portfolio',
						'filtered',
						array(
							'eyebrow'      => __( 'Featured routes', 'gnn' ),
							'title'        => __( 'Curated departures for this season', 'gnn' ),
							'text'         => __( 'Filter by landscape type to discover upcoming trails, difficulty grades and trip lengths.', 'gnn' ),
							'button_label' => __( 'All expeditions', 'gnn' ),
							'button_url'   => 'page:tours',
						),
					),
					array(
						'services',
						'rows',
						array(
							'eyebrow'      => __( 'Expedition disciplines', 'gnn' ),
							'title'        => __( 'How we travel across diverse terrain', 'gnn' ),
							'text'         => __( 'Every route is designed around small group dynamics and low-impact outdoor ethics.', 'gnn' ),
							'button_label' => __( 'View tour packages', 'gnn' ),
							'button_url'   => 'page:packages',
							'items'        => array(
								array(
									'title' => __( 'Alpine trekking & ridge walks', 'gnn' ),
									'text'  => __( 'High-elevation footpaths, mountain pass crossings and wilderness camp overnights.', 'gnn' ),
								),
								array(
									'title' => __( 'Coastal & island kayak trails', 'gnn' ),
									'text'  => __( 'Sea kayaking along secluded coves, tidal estuaries and protected marine waterways.', 'gnn' ),
								),
								array(
									'title' => __( 'Canyon crossings & desert trails', 'gnn' ),
									'text'  => __( 'Stargazing expeditions, canyon gorges and supported desert camp logistics.', 'gnn' ),
								),
								array(
									'title' => __( 'Highland cultural routes', 'gnn' ),
									'text'  => __( 'Village-to-village trails connecting rural heritage communities and seasonal pastures.', 'gnn' ),
								),
							),
						),
					),
					array(
						'features',
						'split-image',
						array(
							'eyebrow'      => __( 'The journey standard', 'gnn' ),
							'title'        => __( 'Complete field support on every departure', 'gnn' ),
							'text'         => __( 'We handle logistics, safety protocols and camp gear so you can focus entirely on the trail.', 'gnn' ),
							'button_label' => __( 'Read guiding ethos', 'gnn' ),
							'button_url'   => 'page:about',
							'image'        => 'media:about',
							'items'        => array(
								array(
									'title' => __( 'Certified wilderness guides', 'gnn' ),
									'text'  => __( 'Every expedition is led by guides certified in wilderness first response and terrain safety.', 'gnn' ),
								),
								array(
									'title' => __( 'Small group limit', 'gnn' ),
									'text'  => __( 'Groups stay capped between eight and twelve travellers to protect quiet trails.', 'gnn' ),
								),
								array(
									'title' => __( 'All camp gear provided', 'gnn' ),
									'text'  => __( 'Four-season tents, sleeping pads, satellite communication and camp kitchen equipment.', 'gnn' ),
								),
								array(
									'title' => __( 'Leave No Trace certified', 'gnn' ),
									'text'  => __( 'Committed to zero campsite footprint and active trail conservation partnerships.', 'gnn' ),
								),
							),
						),
					),
					array(
						'pricing',
						'three',
						array(
							'eyebrow'      => __( 'Travel packages', 'gnn' ),
							'title'        => __( 'Transparent tiers for every journey style', 'gnn' ),
							'text'         => __( 'Replace the price placeholders below with your actual per-traveller expedition rates.', 'gnn' ),
							'button_label' => __( 'Book this package', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title'    => __( 'Guided Explorer', 'gnn' ),
									'text'     => __( 'Weekend routes and shorter regional treks with shared camp accommodation.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Per traveller', 'gnn' ),
									'featured' => false,
									'features' => array(
										__( 'Route briefing and certified trail lead', 'gnn' ),
										__( 'Shared expedition campsite and meals', 'gnn' ),
										__( 'National park entry and trail permits', 'gnn' ),
									),
								),
								array(
									'title'    => __( 'Wilderness Expedition', 'gnn' ),
									'text'     => __( 'Multi-day alpine traverse with dedicated support crew and camp chef.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Per traveller', 'gnn' ),
									'featured' => true,
									'features' => array(
										__( 'Full porter support for camp gear', 'gnn' ),
										__( 'All meals and trail nutrition included', 'gnn' ),
										__( 'Emergency satellite beacon tracking', 'gnn' ),
									),
								),
								array(
									'title'    => __( 'Private Custom Tour', 'gnn' ),
									'text'     => __( 'Bespoke itinerary tailored to your private group, dates and pace.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Per group', 'gnn' ),
									'featured' => false,
									'features' => array(
										__( 'Private guide and dedicated vehicles', 'gnn' ),
										__( 'Customised route and difficulty tier', 'gnn' ),
										__( 'Flexible rest days and lodge upgrades', 'gnn' ),
									),
								),
							),
						),
					),
					array(
						'testimonial',
						'cards',
						array(
							'eyebrow' => __( 'Field feedback', 'gnn' ),
							'title'   => __( 'Notes from the trail', 'gnn' ),
							'text'    => __( 'Replace these placeholder cards with verified feedback from past travellers.', 'gnn' ),
							'items'   => array(
								array(
									'quote'  => __( 'Add a customer quote here.', 'gnn' ),
									'author' => __( 'Name Surname', 'gnn' ),
									'role'   => __( 'Highland Trekker', 'gnn' ),
								),
								array(
									'quote'  => __( 'Add a customer quote here.', 'gnn' ),
									'author' => __( 'Name Surname', 'gnn' ),
									'role'   => __( 'Coastal Explorer', 'gnn' ),
								),
								array(
									'quote'  => __( 'Add a customer quote here.', 'gnn' ),
									'author' => __( 'Name Surname', 'gnn' ),
									'role'   => __( 'Alpine Traverse Guest', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'split',
						array(
							'eyebrow'      => __( 'Next departure', 'gnn' ),
							'title'        => __( 'Ready to step onto your next trail?', 'gnn' ),
							'text'         => __( 'Tell us which landscapes inspire you and our expedition team will recommend the right route.', 'gnn' ),
							'button_label' => __( 'Plan your trip', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'tours'    => array(
				'title'    => __( 'Tours', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Expedition routes', 'gnn' ),
							'title'        => __( 'Scheduled departures and wilderness trails.', 'gnn' ),
							'text'         => __( 'Browse upcoming small group itineraries by region, physical grade and trip duration.', 'gnn' ),
							'button_label' => __( 'View packages', 'gnn' ),
							'button_url'   => 'page:packages',
						),
					),
					array(
						'portfolio',
						'filtered',
						array(
							'eyebrow'      => __( 'Route catalogue', 'gnn' ),
							'title'        => __( 'Active itineraries for upcoming seasons', 'gnn' ),
							'text'         => __( 'Select a category to filter expeditions by difficulty and terrain.', 'gnn' ),
							'button_label' => __( 'Enquire about dates', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'process',
						'horizontal',
						array(
							'eyebrow' => __( 'Booking process', 'gnn' ),
							'title'   => __( 'Four steps from inquiry to the trailhead', 'gnn' ),
							'text'    => __( 'Simple preparation milestones to ensure your expedition runs seamlessly.', 'gnn' ),
							'items'   => array(
								array(
									'title' => __( 'Select route', 'gnn' ),
									'text'  => __( 'Choose a departure date and match your physical fitness to the route tier.', 'gnn' ),
								),
								array(
									'title' => __( 'Pre-trip review', 'gnn' ),
									'text'  => __( 'Receive our comprehensive gear checklist, medical form and travel guidelines.', 'gnn' ),
								),
								array(
									'title' => __( 'Briefing', 'gnn' ),
									'text'  => __( 'Meet your guide team at the gateway hotel for pack checks and route updates.', 'gnn' ),
								),
								array(
									'title' => __( 'Trailhead', 'gnn' ),
									'text'  => __( 'Embark onto the trail with full support, navigation and wilderness safety in place.', 'gnn' ),
								),
							),
						),
					),
					array(
						'faq',
						'single',
						array(
							'eyebrow' => __( 'Trail guidance', 'gnn' ),
							'title'   => __( 'Frequently asked route questions', 'gnn' ),
							'text'    => __( 'Common questions regarding packing, fitness grades and mountain weather.', 'gnn' ),
							'items'   => array(
								array(
									'question' => __( 'What physical fitness level is required?', 'gnn' ),
									'answer'   => __( 'Each tour lists an explicit fitness grade from moderate day walks to strenuous mountain passes. We advise six to eight weeks of cardiovascular prep for alpine routes.', 'gnn' ),
								),
								array(
									'question' => __( 'What happens in severe weather conditions?', 'gnn' ),
									'answer'   => __( 'Our lead guides carry satellite communication and maintain daily forecast check-ins. If conditions become unsafe, contingency routes and alternative shelters are deployed immediately.', 'gnn' ),
								),
								array(
									'question' => __( 'Can solo travellers join group departures?', 'gnn' ),
									'answer'   => __( 'Yes. More than half of our travellers join as individuals. Solo travellers share spacious twin tents or can request a single tent supplement when available.', 'gnn' ),
								),
								array(
									'question' => __( 'Are permits and park passes included?', 'gnn' ),
									'answer'   => __( 'All national park access fees, wilderness conservation permits and campsite booking reservations are fully covered in the tour package price.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'band',
						array(
							'eyebrow'      => __( 'Custom groups', 'gnn' ),
							'title'        => __( 'Planning a private expedition with friends or family?', 'gnn' ),
							'text'         => __( 'We organise dedicated private departures with tailored pacing and private lodge bookings.', 'gnn' ),
							'button_label' => __( 'Enquire about private tours', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'packages' => array(
				'title'    => __( 'Packages', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'centered',
						array(
							'eyebrow'      => __( 'Tour tiers', 'gnn' ),
							'title'        => __( 'Expedition packages with all logistics covered.', 'gnn' ),
							'text'         => __( 'Select the expedition tier that best matches your preferred accommodation, support style and duration.', 'gnn' ),
							'button_label' => __( 'Compare routes', 'gnn' ),
							'button_url'   => 'page:tours',
						),
					),
					array(
						'pricing',
						'three',
						array(
							'eyebrow'      => __( 'Pricing tiers', 'gnn' ),
							'title'        => __( 'Choose your expedition format', 'gnn' ),
							'text'         => __( 'Replace the rate placeholders below with your current tour prices.', 'gnn' ),
							'button_label' => __( 'Reserve this tier', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'services',
						'cards-4',
						array(
							'eyebrow'      => __( 'Package inclusions', 'gnn' ),
							'title'        => __( 'What is covered on every guided journey', 'gnn' ),
							'text'         => __( 'No hidden fees or unexpected charges once you arrive at the trailhead.', 'gnn' ),
							'button_label' => '',
							'items'        => array(
								array(
									'title' => __( 'Trail transport', 'gnn' ),
									'text'  => __( 'Private overland transfers between gateway towns and remote trailheads.', 'gnn' ),
								),
								array(
									'title' => __( 'Camp hospitality', 'gnn' ),
									'text'  => __( 'Hearty trail breakfasts, packed lunch provisions and hot three-course evening meals.', 'gnn' ),
								),
								array(
									'title' => __( 'Medical & safety kit', 'gnn' ),
									'text'  => __( 'Satellite phone, emergency oxygen on high-altitude routes and wilderness trauma kits.', 'gnn' ),
								),
								array(
									'title' => __( 'Carbon offset', 'gnn' ),
									'text'  => __( 'Ground travel emissions are calculated and offset through local reforestation programs.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'split',
						array(
							'eyebrow'      => __( 'Need advice?', 'gnn' ),
							'title'        => __( 'Speak with an expedition specialist.', 'gnn' ),
							'text'         => __( 'Get in touch to confirm gear requirements, travel insurance and pre-trip preparation.', 'gnn' ),
							'button_label' => __( 'Get in touch', 'gnn' ),
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
							'eyebrow'      => __( 'Our foundation', 'gnn' ),
							'title'        => __( 'Guiding grounded in mountain knowledge and respect.', 'gnn' ),
							'text'         => __( 'Introduce your guiding company, history on the trail and your commitment to low-impact expedition travel.', 'gnn' ),
							'button_label' => __( 'View routes', 'gnn' ),
							'button_url'   => 'page:tours',
							'image'        => 'media:about',
						),
					),
					array(
						'about-split',
						'image-left',
						array(
							'eyebrow'      => __( 'The guiding ethos', 'gnn' ),
							'title'        => __( 'Small groups. Unhurried trails. Lasting respect.', 'gnn' ),
							'text'         => __( 'We believe that travel into fragile wilderness areas demands patient pacing, knowledgeable local partnerships and leaving places cleaner than we found them.', 'gnn' ),
							'button_label' => __( 'Contact our team', 'gnn' ),
							'button_url'   => 'page:contact',
							'image'        => 'media:route-1',
							'items'        => array(
								__( 'Locally registered guides with deep native landscape fluency', 'gnn' ),
								__( 'Direct economic support for remote mountain communities', 'gnn' ),
								__( 'Rigorous zero single-use plastic policy across all camps', 'gnn' ),
							),
						),
					),
					array(
						'values',
						'cards',
						array(
							'eyebrow' => __( 'Guiding principles', 'gnn' ),
							'title'   => __( 'Standards that govern every expedition', 'gnn' ),
							'text'    => __( 'Consistent rules that keep our groups safe and landscape trails healthy.', 'gnn' ),
							'items'   => array(
								array(
									'title' => __( 'Safety first', 'gnn' ),
									'text'  => __( 'Every route decision prioritises group safety, weather caution and conservative risk margins.', 'gnn' ),
								),
								array(
									'title' => __( 'Local stewardship', 'gnn' ),
									'text'  => __( 'We work alongside local trail keepers, porters and pastoralists with fair compensation.', 'gnn' ),
								),
								array(
									'title' => __( 'Trail quietness', 'gnn' ),
									'text'  => __( 'We respect the quiet of wilderness spaces by keeping group sizes small and unobtrusive.', 'gnn' ),
								),
							),
						),
					),
					array(
						'team',
						'four',
						array(
							'eyebrow' => __( 'Expedition leads', 'gnn' ),
							'title'   => __( 'Meet the guides on the trail', 'gnn' ),
							'text'    => __( 'Replace these placeholder cards with your lead mountain guides and operations team.', 'gnn' ),
							'image'   => 'media:about',
							'items'   => array(
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Head mountain guide', 'gnn' ),
									'bio'  => __( 'Add a short biography or mountaineering certification background here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Expedition operations lead', 'gnn' ),
									'bio'  => __( 'Add a short biography or wilderness logistics background here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Wilderness safety director', 'gnn' ),
									'bio'  => __( 'Add a short biography or search-and-rescue background here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Coastal & kayak lead', 'gnn' ),
									'bio'  => __( 'Add a short biography or marine guiding background here.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'band',
						array(
							'eyebrow'      => __( 'Join the team', 'gnn' ),
							'title'        => __( 'Looking to guide with our expedition crew?', 'gnn' ),
							'text'         => __( 'We look for certified wilderness leaders with passionate regional terrain knowledge.', 'gnn' ),
							'button_label' => __( 'Guide enquiries', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'journal'  => array(
				'title'    => __( 'Journal', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Field notes', 'gnn' ),
							'title'        => __( 'Trail dispatches, gear guides and expedition stories.', 'gnn' ),
							'text'         => __( 'Practical guidance from our guides on mountain packing, route planning and seasonal weather.', 'gnn' ),
							'button_label' => __( 'View all departures', 'gnn' ),
							'button_url'   => 'page:tours',
						),
					),
					array(
						'blog',
						'grid',
						array(
							'eyebrow'      => __( 'Recent dispatches', 'gnn' ),
							'title'        => __( 'Stories from recent expeditions', 'gnn' ),
							'text'         => __( 'Read recent updates and notes written directly from our camps and trails.', 'gnn' ),
							'button_label' => __( 'All articles', 'gnn' ),
							'image'        => 'media:news-1',
						),
					),
					array(
						'newsletter',
						'centered',
						array(
							'eyebrow'      => __( 'Departure alerts', 'gnn' ),
							'title'        => __( 'Get new season route announcements first', 'gnn' ),
							'text'         => __( 'We notify our journal subscribers when new dates open before public release. No spam.', 'gnn' ),
							'button_label' => __( 'Subscribe to trail notes', 'gnn' ),
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
							'eyebrow'      => __( 'Expedition desk', 'gnn' ),
							'title'        => __( 'Let’s talk about your next departure.', 'gnn' ),
							'text'         => __( 'Reach out directly to verify route availability, custom dates or gear specifications.', 'gnn' ),
							'button_label' => __( 'Browse routes', 'gnn' ),
							'button_url'   => 'page:tours',
						),
					),
					array(
						'contact',
						'cards',
						array(
							'eyebrow'    => __( 'Reach our team', 'gnn' ),
							'title'      => __( 'Gateway office & field base details', 'gnn' ),
							'text'       => __( 'Our expedition coordinators are available on weekdays for route planning consultations.', 'gnn' ),
							'form_title' => __( 'Send an expedition enquiry', 'gnn' ),
							'form_text'  => __( 'Add your tour booking or enquiry form here with the form plugin you use.', 'gnn' ),
						),
					),
					array(
						'faq',
						'two-column',
						array(
							'eyebrow' => __( 'Booking policies', 'gnn' ),
							'title'   => __( 'Common questions before you book', 'gnn' ),
							'text'    => __( 'Key information about payment milestones, transfers and gear hire.', 'gnn' ),
							'items'   => array(
								array(
									'question' => __( 'What deposit is needed to secure a departure?', 'gnn' ),
									'answer'   => __( 'A twenty percent deposit reserves your place on any scheduled departure. The remaining balance is due sixty days before the tour start date.', 'gnn' ),
								),
								array(
									'question' => __( 'Can I hire tents and sleeping equipment locally?', 'gnn' ),
									'answer'   => __( 'Yes. High-quality four-season tents, sleeping bags rated to sub-zero temperatures and trekking poles can be rented at our basecamp office.', 'gnn' ),
								),
								array(
									'question' => __( 'What is your cancellation and refund policy?', 'gnn' ),
									'answer'   => __( 'Cancellations made more than sixty days prior to departure receive a full refund minus an administrative booking fee. Travel insurance covering cancellation is mandatory.', 'gnn' ),
								),
								array(
									'question' => __( 'Do you arrange airport transfers?', 'gnn' ),
									'answer'   => __( 'All scheduled itineraries include group transfer shuttles between the regional airport and our starting gateway lodge on day one and the final day.', 'gnn' ),
								),
							),
						),
					),
				),
			),
		),
		'menu'        => array( 'home', 'tours', 'packages', 'about', 'journal', 'contact' ),
		'content'     => array(
			'projects' => array(
				array(
					'title'    => __( 'High Alpine Traverse', 'gnn' ),
					'excerpt'  => __( 'Seven days traversing high mountain passes, glacier moraines and wilderness huts.', 'gnn' ),
					'category' => __( 'Mountain Trek', 'gnn' ),
					'year'     => '2026',
					'media'    => 'route-1',
				),
				array(
					'title'    => __( 'Fjord & Coast Kayak Trail', 'gnn' ),
					'excerpt'  => __( 'Five days of supported sea kayaking through quiet coastal fjords and island coves.', 'gnn' ),
					'category' => __( 'Coastal Waterway', 'gnn' ),
					'year'     => '2026',
					'media'    => 'route-2',
				),
				array(
					'title'    => __( 'Ancient Silk Oasis Route', 'gnn' ),
					'excerpt'  => __( 'Historical caravan footpaths, canyon gorges and high desert camp stargazing.', 'gnn' ),
					'category' => __( 'Cultural Trail', 'gnn' ),
					'year'     => '2026',
					'media'    => 'route-3',
				),
				array(
					'title'    => __( 'Highlands Wilderness Trek', 'gnn' ),
					'excerpt'  => __( 'Remote river valleys, peat ridges and wild camping under expansive northern skies.', 'gnn' ),
					'category' => __( 'Wilderness', 'gnn' ),
					'year'     => '2025',
					'media'    => 'route-4',
				),
				array(
					'title'    => __( 'Glacier Valley Basecamp', 'gnn' ),
					'excerpt'  => __( 'A four-day introductory alpine skills camp and day hikes beneath towering ice fields.', 'gnn' ),
					'category' => __( 'Alpine Basecamp', 'gnn' ),
					'year'     => '2025',
					'media'    => 'route-5',
				),
				array(
					'title'    => __( 'Desert Canyons & Star Trail', 'gnn' ),
					'excerpt'  => __( 'Deep sandstone labyrinth hikes and supported desert camps far from artificial light.', 'gnn' ),
					'category' => __( 'Desert Canyon', 'gnn' ),
					'year'     => '2025',
					'media'    => 'route-6',
				),
			),
			'posts'    => array(
				array(
					'title'   => __( 'Essential gear checklist for multi-day wilderness treks', 'gnn' ),
					'excerpt' => __( 'How our guide team balances weight, weather protection and warm layers for backcountry trips.', 'gnn' ),
					'media'   => 'news-1',
				),
				array(
					'title'   => __( 'How we design low-impact routes in fragile alpine valleys', 'gnn' ),
					'excerpt' => __( 'Practical trail stewardship, Leave No Trace campsite habits and local community cooperation.', 'gnn' ),
					'media'   => 'news-2',
				),
				array(
					'title'   => __( 'Understanding fitness grades before choosing your expedition', 'gnn' ),
					'excerpt' => __( 'A realistic breakdown of trail elevation gain, pack weight and daily walking hours.', 'gnn' ),
					'media'   => 'news-3',
				),
			),
		),
		'media'       => array(
			'hero'    => array(
				'alt'     => __( 'Dramatic mountain ridge trail with backpackers under clear blue sky', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Expedition hikers walking along a high alpine mountain ridge in morning sunlight; azure sky, stone grey and warm gold accents; wide outdoor adventure composition, aspect ratio 16:9',
			),
			'about'   => array(
				'alt'     => __( 'Trekking guides reviewing a topographical route map on a wooden table', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Outdoor mountain guides with compass and paper contour map planning an expedition route; warm wood, slate and expedition azure tones, aspect ratio 4:3',
			),
			'route-1' => array(
				'alt'     => __( 'Rugged alpine pass crossing above a morning sea of clouds', 'gnn' ),
				'license' => 'original',
				'brief'   => 'High mountain pass crossing with rocky path and cloud inversion below; dramatic mountain landscape photography, azure and slate palette, aspect ratio 4:3',
			),
			'route-2' => array(
				'alt'     => __( 'Sea kayakers paddling through a calm coastal fjord beside steep cliffs', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Sea kayaks gliding over clear fjord water with towering granite cliffs reflecting in the surface; deep marine blue and granite tones, aspect ratio 4:3',
			),
			'route-3' => array(
				'alt'     => __( 'Winding stone trail leading through a mountain heritage settlement', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Ancient cobblestone mountain footpath leading into an alpine valley village in golden afternoon light; warm ochre and stone grey, aspect ratio 4:3',
			),
			'route-4' => array(
				'alt'     => __( 'Expedition hikers crossing a remote river valley in wide highlands', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Wide highland wilderness valley with braided mountain river and small group of backpackers; moss green, slate grey and cold blue stream, aspect ratio 4:3',
			),
			'route-5' => array(
				'alt'     => __( 'Four-season expedition tents pitched on moraine near a glacier', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Yellow and blue expedition tents pitched on rocky mountain moraine with glacier ice wall in background; alpine mountaineering still life, aspect ratio 4:3',
			),
			'route-6' => array(
				'alt'     => __( 'Deep sandstone canyon glowing with warm light at sunset', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Narrow sandstone canyon with backpackers walking through glowing red and gold evening light; desert adventure composition, aspect ratio 4:3',
			),
			'news-1'  => array(
				'alt'     => __( 'Backpack, leather hiking boots and compass laid out on wooden bench', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Carefully organised trekking equipment: rucksack, boots, water bottle and trail map on rustic timber; outdoor gear preparation, aspect ratio 16:9',
			),
			'news-2'  => array(
				'alt'     => __( 'Pristine mountain stream flowing through undisturbed alpine meadow', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Clear alpine stream winding through green mountain meadows with distant jagged peaks; conservation and Leave No Trace concept, aspect ratio 16:9',
			),
			'news-3'  => array(
				'alt'     => __( 'Hiker standing on rocky summit looking out across mountain ranges', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Backpacker with trekking poles on mountain summit ridge gazing across layered blue mountain peaks; sense of scale and accomplishment, aspect ratio 16:9',
			),
		),
		'css'         => 'travel',
	);
}
add_filter(
	'gnn_template_registry',
	function ( $templates ) {
		$templates['travel'] = 'gnn_template_data_travel';
		return $templates;
	}
);
