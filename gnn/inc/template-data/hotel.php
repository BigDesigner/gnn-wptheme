<?php
/**
 * GNNstay ready-made site template data.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GNNstay: inviting website for boutique hotels, inns and countryside retreats.
 *
 * @return array Template configuration array.
 */
function gnn_template_data_hotel() {
	return array(
		'slug'        => 'hotel',
		'label'       => __( 'GNNstay', 'gnn' ),
		'description' => __( 'An inviting website for boutique hotels, countryside retreats, historic inns and lodges with room tiers, amenities and direct booking.', 'gnn' ),
		'requires'    => array( 'elementor' ),
		'tokens'      => array(
			'accent'       => '#486b53',
			'accent_light' => '#314b39',
			'accent_ink'   => '#ffffff',
			'radius'       => 12,
			'font'         => 'manrope',
			'heading_font' => 'space',
			'mode'         => 'light',
			'light'        => array( '#faf8f4', '#f2eee6', '#e4ded2', '#1c1b18', '#5e5950', '#dcd5c7' ),
			'dark'         => array( '#141513', '#1c1e1a', '#262923', '#f6f4ee', '#a7a398', '#2e332a' ),
		),
		'header'      => array(
			'preset'    => 'classic',
			'cta_label' => __( 'Book your stay', 'gnn' ),
			'options'   => array(
				'topbar_enable'     => 1,
				'header_height'     => 82,
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
						'centered',
						array(
							'eyebrow'         => __( 'Boutique hotel & country retreat', 'gnn' ),
							'title'           => __( 'Quiet rooms, natural light and unhurried hospitality.', 'gnn' ),
							'text'            => __( 'Twelve private rooms, a seasonal garden kitchen and restful grounds in the rolling countryside.', 'gnn' ),
							'button_label'    => __( 'Book your stay', 'gnn' ),
							'button_url'      => 'page:contact',
							'secondary_label' => __( 'Explore rooms', 'gnn' ),
							'secondary_url'   => 'page:rooms',
							'image'           => 'media:hero',
						),
					),
					array(
						'services',
						'cards-3',
						array(
							'eyebrow'      => __( 'Accommodation', 'gnn' ),
							'title'        => __( 'Rooms crafted for rest and quiet', 'gnn' ),
							'text'         => __( 'Every room is individually finished with natural linen bedding, stone bathrooms and garden views.', 'gnn' ),
							'button_label' => __( 'View all rooms', 'gnn' ),
							'button_url'   => 'page:rooms',
							'items'        => array(
								array(
									'title' => __( 'Garden Terrace Room', 'gnn' ),
									'text'  => __( 'Ground-floor retreat opening onto private limestone patio and planted lavender beds.', 'gnn' ),
								),
								array(
									'title' => __( 'Courtyard Suite', 'gnn' ),
									'text'  => __( 'Generous two-room suite with freestanding bath, oak beams and afternoon sunlight.', 'gnn' ),
								),
								array(
									'title' => __( 'Orchard Loft Studio', 'gnn' ),
									'text'  => __( 'Top-floor light-filled studio with vaulted ceiling and panoramic orchard outlook.', 'gnn' ),
								),
							),
						),
					),
					array(
						'about-split',
						'image-right',
						array(
							'eyebrow'      => __( 'The estate', 'gnn' ),
							'title'        => __( 'A restored sanctuary surrounded by centuries-old gardens', 'gnn' ),
							'text'         => __( 'Originally built as an agricultural homestead, the estate has been thoughtfully restored into a quiet haven where days flow without rush.', 'gnn' ),
							'button_label' => __( 'Our story', 'gnn' ),
							'button_url'   => 'page:about',
							'image'        => 'media:grounds',
							'items'        => array(
								__( 'Twelve individually styled guest rooms and suites', 'gnn' ),
								__( 'Heated limestone plunge pool and cedar sauna', 'gnn' ),
								__( 'Complimentary afternoon tea and house-baked sourdough', 'gnn' ),
							),
						),
					),
					array(
						'features',
						'grid-2x2',
						array(
							'eyebrow'      => __( 'Guest amenities', 'gnn' ),
							'title'        => __( 'Thoughtful details for a comfortable stay', 'gnn' ),
							'text'         => __( 'Everything you need to unwind, work quietly or explore the surrounding trails.', 'gnn' ),
							'button_label' => __( 'Explore amenities', 'gnn' ),
							'button_url'   => 'page:about',
							'items'        => array(
								array(
									'title' => __( 'Farmhouse Breakfast', 'gnn' ),
									'text'  => __( 'Freshly baked pastries, local organic eggs, orchard fruits and artisanal coffee served daily.', 'gnn' ),
								),
								array(
									'title' => __( 'Garden Pool & Sauna', 'gnn' ),
									'text'  => __( 'A secluded outdoor limestone pool and cedarwood sauna nestled between olive groves.', 'gnn' ),
								),
								array(
									'title' => __( 'Country Bicycles & Maps', 'gnn' ),
									'text'  => __( 'Complimentary touring bikes, helmet kits and curated trail maps for countryside rides.', 'gnn' ),
								),
								array(
									'title' => __( 'Quiet Library & Hearth', 'gnn' ),
									'text'  => __( 'A wood-panelled reading room with an open fire, art monographs and curated board games.', 'gnn' ),
								),
							),
						),
					),
					array(
						'gallery',
						'cols-3',
						array(
							'eyebrow' => __( 'Atmosphere', 'gnn' ),
							'title'   => __( 'Scenes from around the hotel', 'gnn' ),
							'text'    => __( 'A glimpse into the quiet spaces, morning light and seasonal textures waiting for you.', 'gnn' ),
						),
					),
					array(
						'faq',
						'single',
						array(
							'eyebrow' => __( 'Stay details', 'gnn' ),
							'title'   => __( 'Practical information for your visit', 'gnn' ),
							'text'    => __( 'Key details on check-in, parking, dining reservations and travel connections.', 'gnn' ),
							'items'   => array(
								array(
									'question' => __( 'What are the check-in and check-out times?', 'gnn' ),
									'answer'   => __( 'Check-in is from 15:00 and check-out is by 11:00. Early arrival or late departure can be arranged subject to availability.', 'gnn' ),
								),
								array(
									'question' => __( 'Is breakfast included with room bookings?', 'gnn' ),
									'answer'   => __( 'Yes. Our seasonal farmhouse breakfast is served every morning in the garden dining room and is included in all room rates.', 'gnn' ),
								),
								array(
									'question' => __( 'Is parking available on site?', 'gnn' ),
									'answer'   => __( 'Complimentary private parking is available on the estate grounds with electric vehicle charging points for guests.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'split',
						array(
							'eyebrow'      => __( 'Plan your getaway', 'gnn' ),
							'title'        => __( 'Reserve your room or suite today', 'gnn' ),
							'text'         => __( 'Book directly for complimentary room upgrades when available, flexible cancellation and best seasonal rates.', 'gnn' ),
							'button_label' => __( 'Check availability', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'rooms'   => array(
				'title'    => __( 'Rooms', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Accommodation', 'gnn' ),
							'title'        => __( 'Rooms and suites designed for deep rest.', 'gnn' ),
							'text'         => __( 'Explore our collection of garden rooms, courtyard suites and family cottages.', 'gnn' ),
							'button_label' => __( 'Reserve a room', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'services',
						'rows',
						array(
							'eyebrow'      => __( 'Room collection', 'gnn' ),
							'title'        => __( 'Find the space that fits your journey', 'gnn' ),
							'text'         => __( 'All rooms feature king-size beds, organic cotton linens, rainfall showers and quiet garden outlooks.', 'gnn' ),
							'button_label' => __( 'Check dates', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title' => __( 'Garden Terrace Room', 'gnn' ),
									'text'  => __( '28 sqm ground-floor room featuring private terrace, limestone shower and queen-size bed.', 'gnn' ),
								),
								array(
									'title' => __( 'Courtyard Suite', 'gnn' ),
									'text'  => __( '45 sqm spacious suite with freestanding bathtub, lounge corner and direct courtyard access.', 'gnn' ),
								),
								array(
									'title' => __( 'Orchard Loft Studio', 'gnn' ),
									'text'  => __( '38 sqm top-floor haven with high beamed ceilings, dedicated writing desk and valley view.', 'gnn' ),
								),
								array(
									'title' => __( 'Estate Cottage', 'gnn' ),
									'text'  => __( '65 sqm standalone two-bedroom cottage with private garden, open hearth and full kitchen.', 'gnn' ),
								),
							),
						),
					),
					array(
						'features',
						'split-head',
						array(
							'eyebrow'      => __( 'In-room comforts', 'gnn' ),
							'title'        => __( 'Every detail considered for your comfort', 'gnn' ),
							'text'         => __( 'We select natural materials and sustainable amenities to create a restorative environment.', 'gnn' ),
							'button_label' => __( 'Contact hosts', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title' => __( 'Organic Bedding', 'gnn' ),
									'text'  => __( 'Hypoallergenic mattresses with natural washed linen and down duvets.', 'gnn' ),
								),
								array(
									'title' => __( 'Botanical Toiletries', 'gnn' ),
									'text'  => __( 'Handcrafted herbal soaps, shampoos and body oils made from estate flora.', 'gnn' ),
								),
								array(
									'title' => __( 'Espresso & Herbal Tea', 'gnn' ),
									'text'  => __( 'In-room pour-over coffee, fresh local mint and chamomile teas replenished daily.', 'gnn' ),
								),
								array(
									'title' => __( 'High-Speed Wi-Fi', 'gnn' ),
									'text'  => __( 'Discreet high-speed connectivity throughout all rooms and garden work areas.', 'gnn' ),
								),
							),
						),
					),
					array(
						'pricing',
						'three',
						array(
							'eyebrow'      => __( 'Seasonal rates', 'gnn' ),
							'title'        => __( 'Transparent pricing for every room tier', 'gnn' ),
							'text'         => __( 'Rates include organic farmhouse breakfast, pool access and afternoon tea.', 'gnn' ),
							'button_label' => __( 'Reserve now', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title'    => __( 'Standard Room', 'gnn' ),
									'text'     => __( 'Ideal for solo travellers and couples on weekend retreats.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Nightly rate', 'gnn' ),
									'featured' => false,
									'features' => array(
										__( 'King bed and private terrace', 'gnn' ),
										__( 'Farmhouse breakfast included', 'gnn' ),
										__( 'Garden pool and sauna access', 'gnn' ),
									),
								),
								array(
									'title'    => __( 'Terrace Suite', 'gnn' ),
									'text'     => __( 'Generous living room and outdoor patio with panoramic sunset view.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Nightly rate', 'gnn' ),
									'featured' => true,
									'features' => array(
										__( 'Everything in Standard Room', 'gnn' ),
										__( 'Freestanding soaking bathtub', 'gnn' ),
										__( 'Complimentary evening aperitif', 'gnn' ),
									),
								),
								array(
									'title'    => __( 'Estate Cottage', 'gnn' ),
									'text'     => __( 'Standalone historic cottage for families or extended stays.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Nightly rate', 'gnn' ),
									'featured' => false,
									'features' => array(
										__( 'Two private bedrooms and hearth', 'gnn' ),
										__( 'Private garden and dining area', 'gnn' ),
										__( 'Full kitchen and pantry setup', 'gnn' ),
									),
								),
							),
						),
					),
					array(
						'cta',
						'band',
						array(
							'eyebrow'      => __( 'Reservations', 'gnn' ),
							'title'        => __( 'Ready to plan your countryside stay?', 'gnn' ),
							'text'         => __( 'Contact our front desk directly for group bookings, whole-estate buyouts or special requests.', 'gnn' ),
							'button_label' => __( 'Book your room', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'dining'  => array(
				'title'    => __( 'Dining', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Seasonal kitchen', 'gnn' ),
							'title'        => __( 'Honest food inspired by our kitchen gardens.', 'gnn' ),
							'text'         => __( 'Simple, flavourful meals prepared with ingredients harvested from local farms and our own orchard.', 'gnn' ),
							'button_label' => __( 'Reserve a table', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'about-split',
						'image-left',
						array(
							'eyebrow'      => __( 'Farm to table', 'gnn' ),
							'title'        => __( 'Daily menus shaped by what is fresh and in season', 'gnn' ),
							'text'         => __( 'Our kitchen operates with an open hearth, focusing on sourdough baking, roasted heritage vegetables, regional cheeses and cold-pressed olive oils.', 'gnn' ),
							'button_label' => __( 'Dinner inquiries', 'gnn' ),
							'button_url'   => 'page:contact',
							'image'        => 'media:kitchen',
							'items'        => array(
								__( 'Farmhouse breakfast included for all resident guests', 'gnn' ),
								__( 'Shared family-style dinner served five evenings a week', 'gnn' ),
								__( 'Curated selection of regional natural and organic wines', 'gnn' ),
							),
						),
					),
					array(
						'values',
						'cards',
						array(
							'eyebrow' => __( 'Kitchen philosophy', 'gnn' ),
							'title'   => __( 'How we source and prepare our food', 'gnn' ),
							'text'    => __( 'Respect for local terroir, minimal food waste and slow, patient preparation.', 'gnn' ),
							'items'   => array(
								array(
									'title' => __( 'Local Producers', 'gnn' ),
									'text'  => __( 'We partner with nearby dairy farms, beekeepers and organic grain millers.', 'gnn' ),
								),
								array(
									'title' => __( 'Zero Waste Prep', 'gnn' ),
									'text'  => __( 'Composting kitchen trimmings to enrich our estate herb and fruit gardens.', 'gnn' ),
								),
								array(
									'title' => __( 'Artisanal Cellar', 'gnn' ),
									'text'  => __( 'Low-intervention wines, estate cider and house-infused botanical shrubs.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'split',
						array(
							'eyebrow'      => __( 'Dinner reservations', 'gnn' ),
							'title'        => __( 'Join us for dinner at the farmhouse table', 'gnn' ),
							'text'         => __( 'Resident guests and visitors can reserve seating in advance for our evening dinner service.', 'gnn' ),
							'button_label' => __( 'Reserve table', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'about'   => array(
				'title'    => __( 'About', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'About the estate', 'gnn' ),
							'title'        => __( 'A family-run retreat dedicated to calm hospitality.', 'gnn' ),
							'text'         => __( 'Learn about the history of the house, our architectural restoration and the team welcoming you.', 'gnn' ),
							'button_label' => __( 'Explore grounds', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'about-split',
						'numbered',
						array(
							'eyebrow'      => __( 'The history', 'gnn' ),
							'title'        => __( 'Preserving stone walls, timber trusses and garden heritage', 'gnn' ),
							'text'         => __( 'Over three years of gentle conservation, we worked with regional craftsmen to restore lime plasters, local terracotta tiles and aged oak joinery.', 'gnn' ),
							'button_label' => __( 'View suites', 'gnn' ),
							'button_url'   => 'page:rooms',
							'image'        => 'media:grounds',
							'items'        => array(
								__( 'Historical farm buildings dating back to the late nineteenth century', 'gnn' ),
								__( 'Restored using natural lime mortar, reclaimed stone and sustainable timber', 'gnn' ),
								__( 'Rainwater collection and solar thermal heating across all guest suites', 'gnn' ),
							),
						),
					),
					array(
						'features',
						'split-image',
						array(
							'eyebrow'      => __( 'Estate grounds', 'gnn' ),
							'title'        => __( 'Ten acres of meadows, olive trees and winding paths', 'gnn' ),
							'text'         => __( 'Take a morning stroll to the herb gardens or read in the shade of the old pergola.', 'gnn' ),
							'button_label' => __( 'Plan visit', 'gnn' ),
							'button_url'   => 'page:contact',
							'image'        => 'media:amenity-pool',
							'items'        => array(
								array(
									'title' => __( 'Olive Groves', 'gnn' ),
									'text'  => __( 'Two hundred mature olive trees yielding our annual cold-pressed estate oil.', 'gnn' ),
								),
								array(
									'title' => __( 'Wildflower Meadows', 'gnn' ),
									'text'  => __( 'Planted paths supporting native pollinators, songbirds and quiet morning walks.', 'gnn' ),
								),
								array(
									'title' => __( 'Kitchen Garden', 'gnn' ),
									'text'  => __( 'Raised beds growing heirloom tomatoes, herbs and greens for the dining room.', 'gnn' ),
								),
								array(
									'title' => __( 'Sunset Terrace', 'gnn' ),
									'text'  => __( 'A westward-facing stone loggia ideal for early evening aperitifs and reading.', 'gnn' ),
								),
							),
						),
					),
					array(
						'team',
						'three',
						array(
							'eyebrow' => __( 'Our team', 'gnn' ),
							'title'   => __( 'People who make you feel at home', 'gnn' ),
							'text'    => __( 'Replace these profiles with your hotel founders, head chef and front-of-house hosts.', 'gnn' ),
							'image'   => 'media:grounds',
							'items'   => array(
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Estate Director', 'gnn' ),
									'bio'  => __( 'Add a short biography covering hospitality background and estate vision.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Head Chef & Kitchen Lead', 'gnn' ),
									'bio'  => __( 'Add a short biography covering farm-to-table culinary background.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Guest Experience Manager', 'gnn' ),
									'bio'  => __( 'Add a short biography covering reservations and guest excursions.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'split',
						array(
							'eyebrow'      => __( 'Experience it yourself', 'gnn' ),
							'title'        => __( 'We look forward to welcoming you to the estate', 'gnn' ),
							'text'         => __( 'Whether for a weekend pause or a longer stay, we are here to ensure your time is restful.', 'gnn' ),
							'button_label' => __( 'Book your stay', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'journal' => array(
				'title'    => __( 'Journal', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Estate journal', 'gnn' ),
							'title'        => __( 'Notes on country living, seasonal recipes and quiet travel.', 'gnn' ),
							'text'         => __( 'Stories from our kitchen garden, walking trail recommendations and seasonal highlights.', 'gnn' ),
							'button_label' => __( 'Explore rooms', 'gnn' ),
							'button_url'   => 'page:rooms',
						),
					),
					array(
						'blog',
						'grid',
						array(
							'eyebrow'      => __( 'Recent entries', 'gnn' ),
							'title'        => __( 'From the garden and kitchen', 'gnn' ),
							'text'         => __( 'Read about our daily routines, local producers and surrounding landscapes.', 'gnn' ),
							'button_label' => __( 'Contact reception', 'gnn' ),
							'button_url'   => 'page:contact',
							'image'        => 'media:news-1',
						),
					),
					array(
						'newsletter',
						'centered',
						array(
							'eyebrow'      => __( 'Seasonal dispatches', 'gnn' ),
							'title'        => __( 'Receive quarterly notes and early booking invitations', 'gnn' ),
							'text'         => __( 'We send occasional letters with seasonal recipes and advance notice for retreat weekends.', 'gnn' ),
							'button_label' => __( 'Subscribe', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'contact' => array(
				'title'    => __( 'Contact', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Reservations & inquiries', 'gnn' ),
							'title'        => __( 'Plan your arrival and reserve your room.', 'gnn' ),
							'text'         => __( 'Reach our front desk directly for date inquiries, private buyouts or special dietary needs.', 'gnn' ),
							'button_label' => __( 'View room rates', 'gnn' ),
							'button_url'   => 'page:rooms',
						),
					),
					array(
						'contact',
						'cards',
						array(
							'eyebrow'    => __( 'Contact information', 'gnn' ),
							'title'      => __( 'Direct communication with our team', 'gnn' ),
							'text'       => __( 'Our reception is available daily from 08:00 to 22:00 to answer questions and assist with bookings.', 'gnn' ),
							'form_title' => __( 'Send a booking inquiry', 'gnn' ),
							'form_text'  => __( 'Add your contact form here with the form plugin you use.', 'gnn' ),
						),
					),
					array(
						'faq',
						'two-column',
						array(
							'eyebrow' => __( 'Common inquiries', 'gnn' ),
							'title'   => __( 'Arrival, transportation and stay policies', 'gnn' ),
							'text'    => __( 'Helpful answers to assist you before setting off on your journey.', 'gnn' ),
							'items'   => array(
								array(
									'question' => __( 'What is your cancellation policy?', 'gnn' ),
									'answer'   => __( 'Cancellations received up to 7 days prior to arrival are refunded in full. Within 7 days, the first night deposit is retained.', 'gnn' ),
								),
								array(
									'question' => __( 'Are children and pets welcome?', 'gnn' ),
									'answer'   => __( 'Children of all ages are welcome in our Cottage and Suites. Well-behaved dogs are accommodated in designated ground-floor Garden Rooms.', 'gnn' ),
								),
								array(
									'question' => __( 'How do we reach the hotel from the airport?', 'gnn' ),
									'answer'   => __( 'The estate is an hour drive from the nearest international airport. Private transfers can be arranged on request.', 'gnn' ),
								),
								array(
									'question' => __( 'Can we book the entire hotel for private gatherings?', 'gnn' ),
									'answer'   => __( 'Yes. Whole-estate buyout bookings are available for weddings, family reunions and creative retreats during selected seasons.', 'gnn' ),
								),
							),
						),
					),
				),
			),
		),
		'menu'        => array( 'home', 'rooms', 'dining', 'about', 'journal', 'contact' ),
		'content'     => array(
			'projects' => array(
				array(
					'title'    => __( 'Garden Terrace Room', 'gnn' ),
					'excerpt'  => __( 'Ground-floor guest room featuring private stone patio, king-size bed and rainfall limestone shower.', 'gnn' ),
					'category' => __( 'Rooms', 'gnn' ),
					'year'     => '2026',
					'media'    => 'room-deluxe',
				),
				array(
					'title'    => __( 'Courtyard Suite', 'gnn' ),
					'excerpt'  => __( 'Two-room suite with freestanding soaking tub, wood-burning hearth and peaceful courtyard view.', 'gnn' ),
					'category' => __( 'Suites', 'gnn' ),
					'year'     => '2026',
					'media'    => 'room-suite',
				),
				array(
					'title'    => __( 'Orchard Loft Studio', 'gnn' ),
					'excerpt'  => __( 'Upper-level quiet studio with vaulted ceilings, writing bureau and panoramic countryside horizon.', 'gnn' ),
					'category' => __( 'Rooms', 'gnn' ),
					'year'     => '2025',
					'media'    => 'room-studio',
				),
				array(
					'title'    => __( 'The Estate Cottage', 'gnn' ),
					'excerpt'  => __( 'Standalone historic two-bedroom stone cottage with private kitchen, lawn garden and outdoor dining loggia.', 'gnn' ),
					'category' => __( 'Cottages', 'gnn' ),
					'year'     => '2025',
					'media'    => 'grounds',
				),
			),
			'posts'    => array(
				array(
					'title'   => __( 'A seasonal guide to the surrounding valleys and trails', 'gnn' ),
					'excerpt' => __( 'Our favourite half-day walking loops through ancient olive groves, stone chapels and scenic ridgelines.', 'gnn' ),
					'media'   => 'news-1',
				),
				array(
					'title'   => __( 'Notes from the morning bake: heritage grains and sourdough', 'gnn' ),
					'excerpt' => __( 'How our kitchen sources stoneground local flours and ferments daily loaves for the farmhouse table.', 'gnn' ),
					'media'   => 'news-2',
				),
				array(
					'title'   => __( 'Creating quiet spaces: natural materials and restorative light', 'gnn' ),
					'excerpt' => __( 'The architectural choices behind our restoration: lime washes, local oak joinery and unhurried interiors.', 'gnn' ),
					'media'   => 'news-3',
				),
			),
		),
		'media'       => array(
			'hero'          => array(
				'alt'     => __( 'Restored stone boutique hotel courtyard in warm late-afternoon sunlight', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Warm stone architectural hotel exterior with arched loggia, olive trees and clay planters in soft golden hour light, aspect ratio 16:9.',
			),
			'room-deluxe'   => array(
				'alt'     => __( 'Serene bedroom interior with washed linen bedding and garden terrace view', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Bright boutique hotel bedroom with warm lime-wash walls, linen duvet, oak nightstand and French doors opening to garden, aspect ratio 4:3.',
			),
			'room-suite'    => array(
				'alt'     => __( 'Courtyard suite living area with stone fireplace and freestanding bath', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Spacious hotel suite interior with timber ceiling beams, freestanding bathtub, wool rug and natural afternoon light, aspect ratio 4:3.',
			),
			'room-studio'   => array(
				'alt'     => __( 'Loft studio with writing desk overlooking country orchards', 'gnn' ),
				'license' => 'original',
				'brief'   => 'High-ceiling loft bedroom with writing desk, low linen platform bed and morning light through casement window, aspect ratio 4:3.',
			),
			'grounds'       => array(
				'alt'     => __( 'Hotel garden terrace with shaded pergola and stone pathway', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Aged stone courtyard with timber dining pergola, flowering jasmine and gravel path winding through olive trees, aspect ratio 16:10.',
			),
			'kitchen'       => array(
				'alt'     => __( 'Farmhouse breakfast table with sourdough bread, fresh fruit and herbal tea', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Warm rustic dining table laid with ceramic bowls, artisanal sourdough loaf, figs and fresh butter in soft daylight, aspect ratio 4:3.',
			),
			'amenity-pool'  => array(
				'alt'     => __( 'Limestone plunge pool surrounded by lavender and olive trees', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Quiet garden plunge pool lined with pale limestone, shaded by olive trees with two canvas sun loungers, aspect ratio 16:10.',
			),
			'news-1'        => array(
				'alt'     => __( 'Winding walking path through quiet countryside meadows and cypress trees', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Atmospheric country landscape with dirt trail, golden grassy meadows and solitary cypress trees in morning mist, aspect ratio 16:10.',
			),
			'news-2'        => array(
				'alt'     => __( 'Freshly baked sourdough bread and flour dusted on wooden bakery bench', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Close-up of golden crusty sourdough boule on linen cloth with antique bread knife and wooden peel, aspect ratio 16:10.',
			),
			'news-3'        => array(
				'alt'     => __( 'Architectural detail of lime plaster wall and oak window shutter', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Tactile architectural detail of sunlit lime-plastered wall, warm oak shutter and cast-iron latch, aspect ratio 16:10.',
			),
		),
		'css'         => 'hotel',
	);
}
add_filter(
	'gnn_template_registry',
	function ( $templates ) {
		$templates['hotel'] = 'gnn_template_data_hotel';
		return $templates;
	}
);
