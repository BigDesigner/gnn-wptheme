<?php
/**
 * GNNrestaurant ready-made site template data.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GNNrestaurant: warm, visual-first template for restaurants, cafes and hospitality venues.
 *
 * @return array Template data definition.
 */
function gnn_template_data_restaurant() {
	return array(
		'slug'        => 'restaurant',
		'label'       => __( 'GNNrestaurant', 'gnn' ),
		'description' => __( 'A warm website for restaurants, cafes and hospitality venues with seasonal menus, gallery and table reservations.', 'gnn' ),
		'requires'    => array( 'elementor' ),
		'tokens'      => array(
			'accent'       => '#c85a32',
			'accent_light' => '#9e3f1d',
			'accent_ink'   => '#ffffff',
			'radius'       => 16,
			'font'         => 'manrope',
			'heading_font' => 'space',
			'mode'         => 'light',
			'light'        => array( '#fdf8f3', '#f5ece2', '#eadccf', '#231710', '#695346', '#dfd0c0' ),
			'dark'         => array( '#16100c', '#211813', '#2d211a', '#f7efe8', '#b8a596', '#3a2c23' ),
		),
		'header'      => array(
			'preset'    => 'centered',
			'cta_label' => __( 'Book a table', 'gnn' ),
			'options'   => array(
				'topbar_enable'     => 1,
				'header_height'     => 80,
				'header_search_bar' => 0,
				'show_cart'         => 0,
				'show_search'       => 0,
			),
		),
		'pages'       => array(
			'home'    => array(
				'title'    => __( 'Home', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'image-bg',
						array(
							'eyebrow'      => __( 'Seasonal kitchen & bakery', 'gnn' ),
							'title'        => __( 'Honest cooking around an open table.', 'gnn' ),
							'text'         => __( 'Fresh market ingredients, wood-fired plates and unhurried hospitality from morning coffee to evening service.', 'gnn' ),
							'button_label' => __( 'Book a table', 'gnn' ),
							'button_url'   => 'page:contact',
							'image'        => 'media:hero',
						),
					),
					array(
						'services',
						'rows',
						array(
							'eyebrow'      => __( 'From the kitchen', 'gnn' ),
							'title'        => __( 'Daily menus shaped by the season', 'gnn' ),
							'text'         => __( 'Each service follows what arrives fresh in the kitchen.', 'gnn' ),
							'button_label' => __( 'View full menu', 'gnn' ),
							'button_url'   => 'page:menu',
							'items'        => array(
								array(
									'title' => __( 'Morning bakery & espresso', 'gnn' ),
									'text'  => __( 'Naturally leavened loaves, warm pastries and single-origin espresso served from early morning.', 'gnn' ),
								),
								array(
									'title' => __( 'Midday market plates', 'gnn' ),
									'text'  => __( 'Light seasonal salads, daily soup, flatbreads and quick plates for lunch at the counter or terrace.', 'gnn' ),
								),
								array(
									'title' => __( 'Evening hearth & grill', 'gnn' ),
									'text'  => __( 'Shared starters, wood-roasted mains and handmade pasta cooked to order for evening dining.', 'gnn' ),
								),
								array(
									'title' => __( 'Desserts & cellar', 'gnn' ),
									'text'  => __( 'House tarts, seasonal fruit desserts and a curated list of regional beverages and infusions.', 'gnn' ),
								),
							),
						),
					),
					array(
						'about-split',
						'image-right',
						array(
							'eyebrow'      => __( 'Our philosophy', 'gnn' ),
							'title'        => __( 'Simple ingredients prepared with patience', 'gnn' ),
							'text'         => __( 'We work directly with local growers, millers and small producers so every dish speaks for itself without unnecessary complication.', 'gnn' ),
							'button_label' => __( 'Read our story', 'gnn' ),
							'button_url'   => 'page:about',
							'image'        => 'media:kitchen',
							'items'        => array(
								__( 'Daily preparation from whole seasonal produce', 'gnn' ),
								__( 'Naturally fermented doughs and house stocks', 'gnn' ),
								__( 'Menus adapted for dietary needs on request', 'gnn' ),
							),
						),
					),
					array(
						'gallery',
						'cols-3',
						array(
							'eyebrow' => __( 'At the table', 'gnn' ),
							'title'   => __( 'Plates, atmosphere and detail', 'gnn' ),
							'text'    => __( 'A look inside the dining room, the open kitchen and seasonal dishes.', 'gnn' ),
						),
					),
					array(
						'testimonial',
						'featured',
						array(
							'eyebrow' => __( 'Guest notes', 'gnn' ),
							'title'   => __( 'Words from our table', 'gnn' ),
							'text'    => __( 'Replace this placeholder with verified feedback from your guests.', 'gnn' ),
							'items'   => array(
								array(
									'quote'  => __( 'Add a customer quote here.', 'gnn' ),
									'author' => __( 'Name Surname', 'gnn' ),
									'role'   => __( 'Dining guest', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'split',
						array(
							'eyebrow'      => __( 'Reservations', 'gnn' ),
							'title'        => __( 'Join us for lunch, dinner or a private gathering.', 'gnn' ),
							'text'         => __( 'Book a table online or get in touch with our team for group dining requests.', 'gnn' ),
							'button_label' => __( 'Book a table', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'menu'    => array(
				'title'    => __( 'Menu', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Menu', 'gnn' ),
							'title'        => __( 'Seasonal dishes cooked to order.', 'gnn' ),
							'text'         => __( 'Replace the sample dishes below with your current starters, main courses, desserts and beverages.', 'gnn' ),
							'button_label' => __( 'Book a table', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'services',
						'rows',
						array(
							'eyebrow'      => __( 'First course', 'gnn' ),
							'title'        => __( 'Starters & shared plates', 'gnn' ),
							'text'         => __( 'Designed for sharing across the table before the main course.', 'gnn' ),
							'button_label' => '',
							'items'        => array(
								array(
									'title' => __( 'Wood-fired sourdough & cultured butter', 'gnn' ),
									'text'  => __( 'House-baked crust with sea salt, olive oil and seasonal herb spread.', 'gnn' ),
								),
								array(
									'title' => __( 'Charred garden vegetables & labneh', 'gnn' ),
									'text'  => __( 'Roasted roots, toasted seeds, citrus oil and crisp flatbread.', 'gnn' ),
								),
								array(
									'title' => __( 'Cured seasonal fish & fennel salad', 'gnn' ),
									'text'  => __( 'Shaved fennel, citrus segments, capers and cold-pressed olive oil.', 'gnn' ),
								),
								array(
									'title' => __( 'Handmade broth & herb dumplings', 'gnn' ),
									'text'  => __( 'Slow-simmered vegetable and aromatic broth finished with fresh greens.', 'gnn' ),
								),
							),
						),
					),
					array(
						'features',
						'split-image',
						array(
							'eyebrow'      => __( 'Main table', 'gnn' ),
							'title'        => __( 'Mains from the hearth and oven', 'gnn' ),
							'text'         => __( 'Balanced plates paired with seasonal sides and house sauces.', 'gnn' ),
							'button_label' => __( 'Ask about allergies', 'gnn' ),
							'button_url'   => 'page:contact',
							'image'        => 'media:dish-1',
							'items'        => array(
								array(
									'title' => __( 'Handmade tagliolini & wild mushrooms', 'gnn' ),
									'text'  => __( 'Fresh pasta tossed with roasted mushrooms, thyme and aged cheese.', 'gnn' ),
								),
								array(
									'title' => __( 'Pan-roasted catch of the day', 'gnn' ),
									'text'  => __( 'Served with braised greens, saffron potatoes and lemon emulsion.', 'gnn' ),
								),
								array(
									'title' => __( 'Slow-braised shoulder & root purée', 'gnn' ),
									'text'  => __( 'Cooked over gentle heat with rosemary jus and charred shallots.', 'gnn' ),
								),
								array(
									'title' => __( 'Roasted cauliflower & spiced grains', 'gnn' ),
									'text'  => __( 'Caramelised florets over herbed grains, tahini dressing and pomegranate.', 'gnn' ),
								),
							),
						),
					),
					array(
						'pricing',
						'three',
						array(
							'eyebrow'      => __( 'Set menus', 'gnn' ),
							'title'        => __( 'Tasting and group dining menus', 'gnn' ),
							'text'         => __( 'Replace the price placeholders below with your per-guest menu rates.', 'gnn' ),
							'button_label' => __( 'Reserve this menu', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title'    => __( 'Midday table', 'gnn' ),
									'text'     => __( 'A two-course seasonal lunch with filter coffee or tea.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Per guest', 'gnn' ),
									'featured' => false,
									'features' => array(
										__( 'Choice of starter and main plate', 'gnn' ),
										__( 'House sourdough and table water', 'gnn' ),
										__( 'Available on weekdays at lunch', 'gnn' ),
									),
								),
								array(
									'title'    => __( 'Chef tasting menu', 'gnn' ),
									'text'     => __( 'Five seasonal courses chosen by the kitchen for the whole table.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Per guest', 'gnn' ),
									'featured' => true,
									'features' => array(
										__( 'Amuse-bouche and shared starters', 'gnn' ),
										__( 'Two main courses and seasonal dessert', 'gnn' ),
										__( 'Optional beverage pairing available', 'gnn' ),
									),
								),
								array(
									'title'    => __( 'Private dining menu', 'gnn' ),
									'text'     => __( 'Custom family-style service for celebrations and group bookings.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Per guest', 'gnn' ),
									'featured' => false,
									'features' => array(
										__( 'Dedicated table or private room', 'gnn' ),
										__( 'Tailored menu for dietary preferences', 'gnn' ),
										__( 'Welcome drink on arrival', 'gnn' ),
									),
								),
							),
						),
					),
					array(
						'cta',
						'band',
						array(
							'eyebrow'      => __( 'Dietary notes', 'gnn' ),
							'title'        => __( 'Have an allergy or special request?', 'gnn' ),
							'text'         => __( 'Let us know when booking so the kitchen can prepare for your visit.', 'gnn' ),
							'button_label' => __( 'Contact the kitchen', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'about'   => array(
				'title'    => __( 'Our story', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'split',
						array(
							'eyebrow'      => __( 'Our story', 'gnn' ),
							'title'        => __( 'Built around good produce and warm service.', 'gnn' ),
							'text'         => __( 'Introduce your restaurant, the neighbourhood you serve and the culinary traditions behind your menu.', 'gnn' ),
							'button_label' => __( 'Explore the menu', 'gnn' ),
							'button_url'   => 'page:menu',
							'image'        => 'media:about',
						),
					),
					array(
						'values',
						'cards',
						array(
							'eyebrow' => __( 'Kitchen principles', 'gnn' ),
							'title'   => __( 'What guides every service', 'gnn' ),
							'text'    => __( 'Consistent habits in the kitchen and dining room.', 'gnn' ),
							'items'   => array(
								array(
									'title' => __( 'Seasonal sourcing', 'gnn' ),
									'text'  => __( 'We write our menu around ingredients at their peak rather than fixed year-round lists.', 'gnn' ),
								),
								array(
									'title' => __( 'Scratch cooking', 'gnn' ),
									'text'  => __( 'Bread, stocks, sauces and pastries are prepared in-house each morning.', 'gnn' ),
								),
								array(
									'title' => __( 'Relaxed hospitality', 'gnn' ),
									'text'  => __( 'Attentive, knowledgeable table service that lets guests feel at home.', 'gnn' ),
								),
							),
						),
					),
					array(
						'team',
						'three',
						array(
							'eyebrow' => __( 'The people', 'gnn' ),
							'title'   => __( 'Meet the kitchen and dining room team', 'gnn' ),
							'text'    => __( 'Replace these cards with your chefs, bakers and hospitality leads.', 'gnn' ),
							'image'   => 'media:kitchen',
							'items'   => array(
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Head chef', 'gnn' ),
									'bio'  => __( 'Add a short biography or culinary background here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Head baker & pastry chef', 'gnn' ),
									'bio'  => __( 'Add a short biography or culinary background here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Restaurant manager', 'gnn' ),
									'bio'  => __( 'Add a short biography or culinary background here.', 'gnn' ),
								),
							),
						),
					),
					array(
						'process',
						'horizontal',
						array(
							'eyebrow' => __( 'Daily rhythm', 'gnn' ),
							'title'   => __( 'How a day runs in our kitchen', 'gnn' ),
							'text'    => __( 'From early market deliveries to the last evening table.', 'gnn' ),
							'items'   => array(
								array(
									'title' => __( 'Harvest', 'gnn' ),
									'text'  => __( 'Morning deliveries arrive from local farms and fisheries.', 'gnn' ),
								),
								array(
									'title' => __( 'Prep & bake', 'gnn' ),
									'text'  => __( 'Doughs are baked, stocks simmered and vegetables trimmed.', 'gnn' ),
								),
								array(
									'title' => __( 'Service', 'gnn' ),
									'text'  => __( 'Dishes are cooked to order over the hearth and pass.', 'gnn' ),
								),
								array(
									'title' => __( 'Gather', 'gnn' ),
									'text'  => __( 'Guests share food, conversation and unhurried time at the table.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'split',
						array(
							'eyebrow'      => __( 'Visit us', 'gnn' ),
							'title'        => __( 'We would love to welcome you to the table.', 'gnn' ),
							'text'         => __( 'Reserve a table for tonight or plan an upcoming celebration.', 'gnn' ),
							'button_label' => __( 'Book a table', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'gallery' => array(
				'title'    => __( 'Gallery', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'centered',
						array(
							'eyebrow'      => __( 'Gallery', 'gnn' ),
							'title'        => __( 'Inside the dining room and kitchen.', 'gnn' ),
							'text'         => __( 'Seasonal plates, private suppers and the spaces where we gather.', 'gnn' ),
							'button_label' => __( 'Book a table', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'gallery',
						'cols-3',
						array(
							'eyebrow' => __( 'Visuals', 'gnn' ),
							'title'   => __( 'Dishes and atmosphere', 'gnn' ),
							'text'    => __( 'Browse photographs from recent menus and dining room moments.', 'gnn' ),
						),
					),
					array(
						'portfolio',
						'cols-2',
						array(
							'eyebrow'      => __( 'Highlights', 'gnn' ),
							'title'        => __( 'Seasonal menus & private events', 'gnn' ),
							'text'         => __( 'Explore past supper clubs, seasonal table series and culinary highlights.', 'gnn' ),
							'button_label' => __( 'Enquire about events', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'cta',
						'band',
						array(
							'eyebrow'      => __( 'Private dining', 'gnn' ),
							'title'        => __( 'Planning a dinner or celebration?', 'gnn' ),
							'text'         => __( 'Ask about our private table and custom group menus.', 'gnn' ),
							'button_label' => __( 'Get in touch', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'blog'    => array(
				'title'    => __( 'Journal', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Kitchen journal', 'gnn' ),
							'title'        => __( 'Notes on ingredients, seasons and events.', 'gnn' ),
							'text'         => __( 'Stories from our suppliers, new menu releases and upcoming tastings.', 'gnn' ),
							'button_label' => __( 'View the menu', 'gnn' ),
							'button_url'   => 'page:menu',
						),
					),
					array(
						'blog',
						'grid',
						array(
							'eyebrow'      => __( 'Latest stories', 'gnn' ),
							'title'        => __( 'From the kitchen notebook', 'gnn' ),
							'text'         => __( 'Recent updates, seasonal recipes and event announcements.', 'gnn' ),
							'button_label' => __( 'All articles', 'gnn' ),
							'image'        => 'media:news-1',
						),
					),
					array(
						'newsletter',
						'centered',
						array(
							'eyebrow'      => __( 'Supper club list', 'gnn' ),
							'title'        => __( 'Hear about new menus and tasting nights', 'gnn' ),
							'text'         => __( 'Occasional updates when the seasonal menu changes or special dinners open for booking.', 'gnn' ),
							'button_label' => __( 'Join the list', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'contact' => array(
				'title'    => __( 'Reservations', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Reservations & contact', 'gnn' ),
							'title'        => __( 'Reserve a table or ask a question.', 'gnn' ),
							'text'         => __( 'Use your booking form or reach our dining room directly by phone or email.', 'gnn' ),
							'button_label' => __( 'View the menu', 'gnn' ),
							'button_url'   => 'page:menu',
						),
					),
					array(
						'contact',
						'split',
						array(
							'eyebrow'    => __( 'Find us', 'gnn' ),
							'title'      => __( 'Hours, location and table bookings', 'gnn' ),
							'text'       => __( 'Walk-ins are welcome at the counter whenever seats are free. For groups, please book ahead.', 'gnn' ),
							'form_title' => __( 'Table reservation request', 'gnn' ),
							'form_text'  => __( 'Add your reservation or contact form here with the form plugin you use.', 'gnn' ),
						),
					),
					array(
						'faq',
						'two-column',
						array(
							'eyebrow' => __( 'Before you visit', 'gnn' ),
							'title'   => __( 'Common reservation questions', 'gnn' ),
							'text'    => __( 'Helpful details for planning your meal with us.', 'gnn' ),
							'items'   => array(
								array(
									'question' => __( 'How far in advance should we book?', 'gnn' ),
									'answer'   => __( 'Reservations open thirty days ahead. Weekend evenings fill quickly, while weekday lunches often have same-day tables.', 'gnn' ),
								),
								array(
									'question' => __( 'Can you accommodate dietary requirements?', 'gnn' ),
									'answer'   => __( 'Yes. Note any allergies or dietary preferences when booking so our kitchen can prepare suitable dishes.', 'gnn' ),
								),
								array(
									'question' => __( 'Do you host larger groups or private dinners?', 'gnn' ),
									'answer'   => __( 'Groups of eight or more can book our shared set menu. Contact us directly to arrange a private table.', 'gnn' ),
								),
								array(
									'question' => __( 'What if our plans change?', 'gnn' ),
									'answer'   => __( 'Please let us know at least twenty-four hours before your booking if you need to adjust guest numbers or cancel.', 'gnn' ),
								),
							),
						),
					),
				),
			),
		),
		'menu'        => array( 'home', 'menu', 'about', 'gallery', 'blog', 'contact' ),
		'content'     => array(
			'projects' => array(
				array(
					'title'    => __( 'Autumn harvest table', 'gnn' ),
					'excerpt'  => __( 'Root vegetables, roasted squash and wood-fired grains prepared for the autumn evening menu.', 'gnn' ),
					'category' => __( 'Seasonal menu', 'gnn' ),
					'year'     => '2026',
					'media'    => 'dish-1',
				),
				array(
					'title'    => __( 'Morning sourdough & pastry bake', 'gnn' ),
					'excerpt'  => __( 'Country loaves, laminated pastries and seasonal fruit tarts fresh from the early bake.', 'gnn' ),
					'category' => __( 'Bakery', 'gnn' ),
					'year'     => '2026',
					'media'    => 'dish-2',
				),
				array(
					'title'    => __( 'Regional cellar pairing dinner', 'gnn' ),
					'excerpt'  => __( 'A five-course collaborative supper pairing hearth dishes with small-batch regional producers.', 'gnn' ),
					'category' => __( 'Events', 'gnn' ),
					'year'     => '2026',
					'media'    => 'dish-3',
				),
				array(
					'title'    => __( 'Courtyard long-table supper', 'gnn' ),
					'excerpt'  => __( 'Family-style platters served outdoors under warm lantern light for private gatherings.', 'gnn' ),
					'category' => __( 'Private dining', 'gnn' ),
					'year'     => '2025',
					'media'    => 'dining',
				),
				array(
					'title'    => __( 'Open kitchen counter service', 'gnn' ),
					'excerpt'  => __( 'Counter seating overlooking the hearth where guests watch each course come together.', 'gnn' ),
					'category' => __( 'Dining room', 'gnn' ),
					'year'     => '2025',
					'media'    => 'kitchen',
				),
			),
			'posts'    => array(
				array(
					'title'   => __( 'What comes into the kitchen this season', 'gnn' ),
					'excerpt' => __( 'A look at the fresh produce, herbs and grains shaping our newest lunch and dinner plates.', 'gnn' ),
					'media'   => 'news-1',
				),
				array(
					'title'   => __( 'How we build a balanced tasting menu', 'gnn' ),
					'excerpt' => __( 'From crisp opening bites to slow-cooked mains, pacing and contrast guide every course we serve.', 'gnn' ),
					'media'   => 'news-2',
				),
				array(
					'title'   => __( 'Planning a relaxed private dinner for guests', 'gnn' ),
					'excerpt' => __( 'Simple choices on shared platters, seating and welcome drinks make group celebrations effortless.', 'gnn' ),
					'media'   => 'news-3',
				),
			),
		),
		'media'       => array(
			'hero'    => array(
				'alt'     => __( 'Warm restaurant dining table with seasonal dishes and soft evening lighting', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Warm candlelit restaurant table with rustic ceramic plates, bread and seasonal dishes; terracotta, cream and espresso palette; editorial hospitality composition, aspect ratio 16:9',
			),
			'about'   => array(
				'alt'     => __( 'Inviting dining room interior with wooden tables and natural light', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Cozy bistro and restaurant interior with oak tables, linen napkins and warm plaster walls; parchment and terracotta tones; natural daylight composition, aspect ratio 4:3',
			),
			'kitchen' => array(
				'alt'     => __( 'Chefs preparing fresh seasonal ingredients at the kitchen pass', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Open restaurant kitchen with wood hearth, copper pans and fresh herbs on a wooden prep table; warm amber and dark espresso palette; culinary craft composition, aspect ratio 4:3',
			),
			'dining'  => array(
				'alt'     => __( 'Long wooden table set for a private supper gathering', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Courtyard long table set with stoneware plates, glassware and warm string lighting at dusk; terracotta and warm cream palette; hospitality event composition, aspect ratio 4:3',
			),
			'dish-1'  => array(
				'alt'     => __( 'Seasonal main course plated on handmade stoneware', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Overhead close-up of a seasonal roasted dish on handmade ceramic stoneware with herbs and olive oil gloss; warm saffron and terracotta accents, aspect ratio 4:3',
			),
			'dish-2'  => array(
				'alt'     => __( 'Freshly baked sourdough loaf and morning pastries on a wooden board', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Artisan sourdough crust and golden pastries on a dark wood board with linen cloth; warm golden-brown and cream tones; bakery still life, aspect ratio 4:3',
			),
			'dish-3'  => array(
				'alt'     => __( 'Shared small plates and glasses arranged on a restaurant table', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Tabletop arrangement of small plates, citrus, olives and stemmed glasses in soft evening light; warm terracotta, amber and linen palette, aspect ratio 4:3',
			),
			'news-1'  => array(
				'alt'     => __( 'Fresh seasonal harvest vegetables and herbs in kitchen crates', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Freshly harvested root vegetables, greens and culinary herbs on a butcher block table; warm earthy terracotta and sage tones, aspect ratio 16:9',
			),
			'news-2'  => array(
				'alt'     => __( 'Chef plating a tasting course with sauce and herbs', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Close-up of a ceramic plate being finished with sauce on a warm kitchen pass; amber lighting and dark espresso background, aspect ratio 16:9',
			),
			'news-3'  => array(
				'alt'     => __( 'Table setting with linen napkins and menus for a group dinner', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Minimal printed menu card on a folded linen napkin beside ceramic tableware and candle glow; warm cream and terracotta palette, aspect ratio 16:9',
			),
		),
		'css'         => 'restaurant',
	);
}
add_filter(
	'gnn_template_registry',
	function ( $templates ) {
		$templates['restaurant'] = 'gnn_template_data_restaurant';
		return $templates;
	}
);
