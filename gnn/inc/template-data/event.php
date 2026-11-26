<?php
/**
 * GNNevent starter template data.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GNNevent: energetic conference, festival and summit template focused on schedules, speakers and tickets.
 *
 * @return array Template data definition.
 */
function gnn_template_data_event() {
	return array(
		'slug'        => 'event',
		'label'       => __( 'GNNevent', 'gnn' ),
		'description' => __( 'An energetic event and conference website built around schedules, speakers, sessions and ticket registration.', 'gnn' ),
		'requires'    => array( 'elementor' ),
		'tokens'      => array(
			'accent'       => '#ff5a36',
			'accent_light' => '#d9381e',
			'accent_ink'   => '#0e0c14',
			'radius'       => 16,
			'font'         => 'space',
			'heading_font' => 'space',
			'mode'         => 'dark',
			'light'        => array( '#faf8f5', '#f1ede6', '#e4dfd5', '#14111c', '#575266', '#d8d1c5' ),
			'dark'         => array( '#0e0c14', '#161320', '#211c2f', '#f7f5fa', '#a8a2b8', '#2d2640' ),
		),
		'header'      => array(
			'preset'    => 'centered',
			'cta_label' => __( 'Get tickets', 'gnn' ),
			'options'   => array(
				'header_structure'  => 'centered',
				'header_surface'    => 'glass',
				'header_shape'      => 'full',
				'header_width'      => 'contained',
				'header_behavior'   => 'sticky',
				'header_overlay'    => 0,
				'topbar_enable'     => 1,
				'header_height'     => 74,
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
						'image-bg',
						array(
							'eyebrow'      => __( 'Annual conference & workshops', 'gnn' ),
							'title'        => __( 'Two days of ideas, live sessions and practical workshops.', 'gnn' ),
							'text'         => __( 'Join practitioners, creators and technical leads for keynotes, roundtables and hands-on stage sessions.', 'gnn' ),
							'button_label' => __( 'Get tickets', 'gnn' ),
							'button_url'   => 'page:schedule',
							'image'        => 'media:hero',
						),
					),
					array(
						'team',
						'four',
						array(
							'eyebrow' => __( 'Keynote lineup', 'gnn' ),
							'title'   => __( 'Featured speakers on stage', 'gnn' ),
							'text'    => __( 'Replace these cards with your confirmed speakers, their session topics and roles.', 'gnn' ),
							'image'   => 'media:speaker',
							'items'   => array(
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Opening keynote · Systems & scale', 'gnn' ),
									'bio'  => __( 'Add speaker biography and session summary here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Main stage · Product architecture', 'gnn' ),
									'bio'  => __( 'Add speaker biography and session summary here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Workshop lead · Creative workflows', 'gnn' ),
									'bio'  => __( 'Add speaker biography and session summary here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Panel moderator · Future of craft', 'gnn' ),
									'bio'  => __( 'Add speaker biography and session summary here.', 'gnn' ),
								),
							),
						),
					),
					array(
						'process',
						'timeline',
						array(
							'eyebrow'      => __( 'Programme overview', 'gnn' ),
							'title'        => __( 'How the conference unfolds', 'gnn' ),
							'text'         => __( 'A structured flow of keynotes, breakout tracks and evening networking across the venue.', 'gnn' ),
							'button_label' => __( 'View full schedule', 'gnn' ),
							'button_url'   => 'page:schedule',
							'items'        => array(
								array(
									__( 'Morning · Registration & opening keynote', 'gnn' ),
									__( 'Check in, collect your badge and join the main auditorium for the opening address.', 'gnn' ),
								),
								array(
									__( 'Midday · Parallel track sessions', 'gnn' ),
									__( 'Choose between technical deep-dives, case study talks and interactive Q&A rooms.', 'gnn' ),
								),
								array(
									__( 'Afternoon · Hands-on workshops', 'gnn' ),
									__( 'Small-group practical labs led by visiting practitioners and stage speakers.', 'gnn' ),
								),
								array(
									__( 'Evening · Roundtable & social reception', 'gnn' ),
									__( 'Unwind with fellow attendees, speakers and partners in the main courtyard.', 'gnn' ),
								),
							),
						),
					),
					array(
						'services',
						'lined',
						array(
							'eyebrow'      => __( 'Stage tracks', 'gnn' ),
							'title'        => __( 'Three focused tracks across two stages', 'gnn' ),
							'text'         => __( 'Switch freely between rooms or follow a single track from start to finish.', 'gnn' ),
							'button_label' => __( 'Explore sessions', 'gnn' ),
							'button_url'   => 'page:sessions',
							'items'        => array(
								array(
									__( 'Main stage keynotes', 'gnn' ),
									__( 'Big-picture talks on strategy, design direction and emerging practices.', 'gnn' ),
								),
								array(
									__( 'Applied case studies', 'gnn' ),
									__( 'Behind-the-scenes breakdowns of real projects, lessons learned and trade-offs.', 'gnn' ),
								),
								array(
									__( 'Workshop labs', 'gnn' ),
									__( 'Guided ninety-minute sessions where participants work through practical exercises.', 'gnn' ),
								),
							),
						),
					),
					array(
						'pricing',
						'three',
						array(
							'eyebrow'      => __( 'Passes & registration', 'gnn' ),
							'title'        => __( 'Choose your conference pass', 'gnn' ),
							'text'         => __( 'Replace the placeholder prices below with your early-bird, standard and workshop ticket tiers.', 'gnn' ),
							'button_label' => __( 'Register pass', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title'    => __( 'Day pass', 'gnn' ),
									'text'     => __( 'Single-day access to all main stage keynotes and exhibition areas.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Single day', 'gnn' ),
									'featured' => false,
									'features' => array(
										__( 'Main stage and track access', 'gnn' ),
										__( 'Printed programme guide', 'gnn' ),
										__( 'Coffee and lunch service', 'gnn' ),
									),
								),
								array(
									'title'    => __( 'Full conference pass', 'gnn' ),
									'text'     => __( 'Complete two-day access including workshops, recordings and evening reception.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'All days', 'gnn' ),
									'featured' => true,
									'features' => array(
										__( 'All keynotes and breakout rooms', 'gnn' ),
										__( 'Workshop seat reservation', 'gnn' ),
										__( 'Evening reception & talk recordings', 'gnn' ),
									),
								),
								array(
									'title'    => __( 'Group pass', 'gnn' ),
									'text'     => __( 'Bundled registration for teams attending the conference together.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Per team', 'gnn' ),
									'featured' => false,
									'features' => array(
										__( 'Everything in Full pass', 'gnn' ),
										__( 'Reserved team seating block', 'gnn' ),
										__( 'Single consolidated invoice', 'gnn' ),
									),
								),
							),
						),
					),
					array(
						'logos',
						'strip',
						array(
							'eyebrow' => __( 'Event partners & sponsors', 'gnn' ),
							'items'   => array(
								__( 'Your logo', 'gnn' ),
								__( 'Your logo', 'gnn' ),
								__( 'Your logo', 'gnn' ),
								__( 'Your logo', 'gnn' ),
								__( 'Your logo', 'gnn' ),
								__( 'Your logo', 'gnn' ),
							),
						),
					),
					array(
						'cta',
						'band',
						array(
							'eyebrow'      => __( 'Limited venue capacity', 'gnn' ),
							'title'        => __( 'Secure your badge for the upcoming edition', 'gnn' ),
							'text'         => __( 'Register your pass or contact our team for group bookings and sponsorship opportunities.', 'gnn' ),
							'button_label' => __( 'Reserve tickets', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'schedule' => array(
				'title'    => __( 'Schedule', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'left-large',
						array(
							'eyebrow'      => __( 'Conference timetable', 'gnn' ),
							'title'        => __( 'Two days of talks, labs and roundtables.', 'gnn' ),
							'text'         => __( 'Plan your itinerary across the main auditorium, breakout rooms and workshop studios.', 'gnn' ),
							'button_label' => __( 'Reserve a pass', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'process',
						'split-timeline',
						array(
							'eyebrow'      => __( 'Day 01 · Foundations', 'gnn' ),
							'title'        => __( 'Opening keynotes and core tracks', 'gnn' ),
							'text'         => __( 'The first day focuses on foundational themes, live demonstrations and peer discussions.', 'gnn' ),
							'button_label' => __( 'See speakers', 'gnn' ),
							'button_url'   => 'page:speakers',
							'items'        => array(
								array(
									__( '09:00 · Doors open & badge pickup', 'gnn' ),
									__( 'Morning coffee in the main foyer and welcome pack collection.', 'gnn' ),
								),
								array(
									__( '10:00 · Opening keynote address', 'gnn' ),
									__( 'Plenary session in the main hall setting the theme for the edition.', 'gnn' ),
								),
								array(
									__( '13:30 · Track sessions & case studies', 'gnn' ),
									__( 'Parallel talks across Stage A and Stage B with moderated Q&A.', 'gnn' ),
								),
								array(
									__( '17:30 · Welcome reception', 'gnn' ),
									__( 'Informal drinks and conversation in the gallery courtyard.', 'gnn' ),
								),
							),
						),
					),
					array(
						'services',
						'rows',
						array(
							'eyebrow'      => __( 'Day 02 · Applied labs', 'gnn' ),
							'title'        => __( 'Workshops, panels and closing session', 'gnn' ),
							'text'         => __( 'The second day dives into hands-on workshops, specialist roundtables and the closing keynote.', 'gnn' ),
							'button_label' => __( 'Session archive', 'gnn' ),
							'button_url'   => 'page:sessions',
							'items'        => array(
								array(
									__( '10:00 · Morning workshop blocks', 'gnn' ),
									__( 'Ninety-minute interactive labs with limited seating and practical takeaways.', 'gnn' ),
								),
								array(
									__( '14:00 · Industry roundtable panels', 'gnn' ),
									__( 'Moderated debates bringing together multiple perspectives on shared challenges.', 'gnn' ),
								),
								array(
									__( '16:30 · Closing keynote & wrap-up', 'gnn' ),
									__( 'Final plenary reflection and announcements for the next edition.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'split',
						array(
							'eyebrow'      => __( 'Pass registration', 'gnn' ),
							'title'        => __( 'Workshop seats are assigned on registration', 'gnn' ),
							'text'         => __( 'Book your pass early to select your preferred afternoon workshop track.', 'gnn' ),
							'button_label' => __( 'Get tickets', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'speakers' => array(
				'title'    => __( 'Speakers', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'centered',
						array(
							'eyebrow'      => __( 'Speaker roster', 'gnn' ),
							'title'        => __( 'Voices on stage this edition.', 'gnn' ),
							'text'         => __( 'Meet the keynote speakers, workshop leaders and panellists sharing their work.', 'gnn' ),
							'button_label' => __( 'Apply to speak', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'team',
						'four',
						array(
							'eyebrow' => __( 'Main stage', 'gnn' ),
							'title'   => __( 'Keynote and plenary speakers', 'gnn' ),
							'text'    => __( 'Replace these placeholders with your confirmed keynote speakers and session titles.', 'gnn' ),
							'image'   => 'media:speaker',
							'items'   => array(
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Opening keynote · Systems & scale', 'gnn' ),
									'bio'  => __( 'Add speaker biography and session summary here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Keynote · Design leadership', 'gnn' ),
									'bio'  => __( 'Add speaker biography and session summary here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Keynote · Open standards', 'gnn' ),
									'bio'  => __( 'Add speaker biography and session summary here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Closing keynote · Future craft', 'gnn' ),
									'bio'  => __( 'Add speaker biography and session summary here.', 'gnn' ),
								),
							),
						),
					),
					array(
						'team',
						'three',
						array(
							'eyebrow' => __( 'Workshop hosts', 'gnn' ),
							'title'   => __( 'Studio leads and moderators', 'gnn' ),
							'text'    => __( 'Practitioners guiding the hands-on afternoon labs and Q&A rooms.', 'gnn' ),
							'image'   => 'media:workshop',
							'items'   => array(
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Lab host · Prototyping systems', 'gnn' ),
									'bio'  => __( 'Add speaker biography and workshop scope here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Lab host · Accessible interfaces', 'gnn' ),
									'bio'  => __( 'Add speaker biography and workshop scope here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Roundtable moderator · Research', 'gnn' ),
									'bio'  => __( 'Add speaker biography and panel topic here.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'band',
						array(
							'eyebrow'      => __( 'Call for proposals', 'gnn' ),
							'title'        => __( 'Interested in speaking or hosting a workshop?', 'gnn' ),
							'text'         => __( 'Send us your talk abstract and topic outline for upcoming editions.', 'gnn' ),
							'button_label' => __( 'Submit proposal', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'sessions' => array(
				'title'    => __( 'Sessions', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Sessions & highlights', 'gnn' ),
							'title'        => __( 'Explore talks, tracks and past editions.', 'gnn' ),
							'text'         => __( 'Browse session topics by track or look back at photography from previous gatherings.', 'gnn' ),
							'button_label' => __( 'View schedule', 'gnn' ),
							'button_url'   => 'page:schedule',
						),
					),
					array(
						'portfolio',
						'filtered',
						array(
							'eyebrow' => __( 'Session catalogue', 'gnn' ),
							'title'   => __( 'Featured talks and workshops', 'gnn' ),
							'text'    => __( 'Filter sessions by track to preview what each stage covers.', 'gnn' ),
						),
					),
					array(
						'gallery',
						'cols-3',
						array(
							'eyebrow' => __( 'Atmosphere', 'gnn' ),
							'title'   => __( 'Moments from the venue', 'gnn' ),
							'text'    => __( 'Stage sessions, workshop rooms and community conversations.', 'gnn' ),
						),
					),
					array(
						'cta',
						'split',
						array(
							'eyebrow'      => __( 'Join us live', 'gnn' ),
							'title'        => __( 'Experience every session in person', 'gnn' ),
							'text'         => __( 'All full conference passes include access to session slides and post-event recordings.', 'gnn' ),
							'button_label' => __( 'Register now', 'gnn' ),
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
							'eyebrow'      => __( 'About the event', 'gnn' ),
							'title'        => __( 'Built for honest conversation and practical craft.', 'gnn' ),
							'text'         => __( 'Replace this summary with the story behind your conference, the venue details and travel information.', 'gnn' ),
							'button_label' => __( 'Contact organisers', 'gnn' ),
							'button_url'   => 'page:contact',
							'image'        => 'media:venue',
						),
					),
					array(
						'features',
						'grid-2x2',
						array(
							'eyebrow'      => __( 'Venue & experience', 'gnn' ),
							'title'        => __( 'What to expect on site', 'gnn' ),
							'text'         => __( 'Everything arranged so attendees can focus on learning and connecting.', 'gnn' ),
							'button_label' => '',
							'items'        => array(
								array(
									__( 'Accessible auditorium', 'gnn' ),
									__( 'Step-free access, clear acoustics and live captioning on the main stage.', 'gnn' ),
								),
								array(
									__( 'Breakout studios', 'gnn' ),
									__( 'Dedicated rooms equipped for hands-on workshops and small-group discussion.', 'gnn' ),
								),
								array(
									__( 'Catering & lounge', 'gnn' ),
									__( 'All-day coffee, lunch service and quiet tables for catching up on work.', 'gnn' ),
								),
								array(
									__( 'Central location', 'gnn' ),
									__( 'Easy public transport links and nearby accommodation options.', 'gnn' ),
								),
							),
						),
					),
					array(
						'values',
						'minimal',
						array(
							'eyebrow' => __( 'Code of conduct', 'gnn' ),
							'title'   => __( 'A welcoming, respectful community', 'gnn' ),
							'text'    => __( 'All attendees, speakers and partners agree to uphold three shared standards.', 'gnn' ),
							'items'   => array(
								array(
									__( 'Substance over pitches', 'gnn' ),
									__( 'Talks focus on real methods, honest lessons and actionable insights.', 'gnn' ),
								),
								array(
									__( 'Inclusive participation', 'gnn' ),
									__( 'Every participant is treated with respect across all stages and social spaces.', 'gnn' ),
								),
								array(
									__( 'Open exchange', 'gnn' ),
									__( 'Questions, dialogue and generous knowledge-sharing are encouraged.', 'gnn' ),
								),
							),
						),
					),
					array(
						'logos',
						'grid',
						array(
							'eyebrow' => __( 'Supporters', 'gnn' ),
							'title'   => __( 'Sponsors and community partners', 'gnn' ),
							'text'    => __( 'Replace these boxes with the organisations supporting your event.', 'gnn' ),
						),
					),
					array(
						'faq',
						'two-column',
						array(
							'eyebrow' => __( 'Attendee info', 'gnn' ),
							'title'   => __( 'Practical questions answered', 'gnn' ),
							'text'    => __( 'Details on tickets, badge transfers, accessibility and venue entry.', 'gnn' ),
							'items'   => array(
								array(
									'question' => __( 'Can I transfer my ticket to a colleague?', 'gnn' ),
									'answer'   => __( 'Yes. Contact the registration desk prior to the event to update the attendee name on your badge.', 'gnn' ),
								),
								array(
									'question' => __( 'Are sessions recorded for pass holders?', 'gnn' ),
									'answer'   => __( 'Main stage talks are recorded and shared with full-pass holders after the event concludes.', 'gnn' ),
								),
								array(
									'question' => __( 'What accessibility support is available at the venue?', 'gnn' ),
									'answer'   => __( 'The venue provides step-free access, reserved seating and accessible restrooms. Let us know any requirements when registering.', 'gnn' ),
								),
								array(
									'question' => __( 'How do sponsorship enquiries work?', 'gnn' ),
									'answer'   => __( 'Reach out through the contact page to request the partner prospectus and exhibition floor plan.', 'gnn' ),
								),
							),
						),
					),
				),
			),
			'news'     => array(
				'title'    => __( 'News', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Event bulletin', 'gnn' ),
							'title'        => __( 'Lineup announcements and attendee guides.', 'gnn' ),
							'text'         => __( 'Follow speaker reveals, schedule updates and travel tips leading up to doors opening.', 'gnn' ),
							'button_label' => __( 'Subscribe', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'blog',
						'grid',
						array(
							'eyebrow' => __( 'Latest updates', 'gnn' ),
							'title'   => __( 'News from the organisers', 'gnn' ),
							'text'    => __( 'Announcements, speaker interviews and preparation notes.', 'gnn' ),
							'image'   => 'media:news-1',
						),
					),
					array(
						'newsletter',
						'centered',
						array(
							'eyebrow'      => __( 'Stay updated', 'gnn' ),
							'title'        => __( 'Never miss a speaker announcement', 'gnn' ),
							'text'         => __( 'Get early-bird ticket alerts and timetable releases straight to your inbox.', 'gnn' ),
							'button_label' => __( 'Join mailing list', 'gnn' ),
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
							'eyebrow'      => __( 'Registration & support', 'gnn' ),
							'title'        => __( 'Tickets, group bookings and partner enquiries.', 'gnn' ),
							'text'         => __( 'Reach the organising team for ticket registration, press accreditation or sponsorship details.', 'gnn' ),
							'button_label' => __( 'View schedule', 'gnn' ),
							'button_url'   => 'page:schedule',
						),
					),
					array(
						'contact',
						'cards',
						array(
							'eyebrow' => __( 'Organising desk', 'gnn' ),
							'title'   => __( 'How to reach our team', 'gnn' ),
							'text'    => __( 'Use the contact details below or embed your ticket registration and enquiry form.', 'gnn' ),
						),
					),
					array(
						'cta',
						'email',
						array(
							'eyebrow' => __( 'Direct enquiries', 'gnn' ),
							'title'   => __( 'Questions about sponsorship or press passes?', 'gnn' ),
							'text'    => __( 'Write to our event coordination desk and we will reply promptly.', 'gnn' ),
						),
					),
				),
			),
		),
		'menu'        => array( 'home', 'schedule', 'speakers', 'sessions', 'about', 'news', 'contact' ),
		'content'     => array(
			'projects' => array(
				array(
					'title'    => __( 'Opening Keynote: Designing Resilient Systems', 'gnn' ),
					'excerpt'  => __( 'A plenary talk on how teams build adaptable architectures and sustainable workflows under change.', 'gnn' ),
					'category' => __( 'Keynote', 'gnn' ),
					'year'     => '2026',
					'media'    => 'session-1',
				),
				array(
					'title'    => __( 'Stage Track: From Prototype to Production', 'gnn' ),
					'excerpt'  => __( 'Practical lessons on bridging the gap between early creative concepts and reliable delivery.', 'gnn' ),
					'category' => __( 'Main Stage', 'gnn' ),
					'year'     => '2026',
					'media'    => 'session-2',
				),
				array(
					'title'    => __( 'Interactive Lab: Collaborative Design Critiques', 'gnn' ),
					'excerpt'  => __( 'A hands-on workshop exploring structured critique methods that elevate team output.', 'gnn' ),
					'category' => __( 'Workshop', 'gnn' ),
					'year'     => '2026',
					'media'    => 'session-3',
				),
				array(
					'title'    => __( 'Case Study: Scaling Community-Led Events', 'gnn' ),
					'excerpt'  => __( 'An inside look at programme curation, speaker coaching and accessible stage production.', 'gnn' ),
					'category' => __( 'Case Study', 'gnn' ),
					'year'     => '2026',
					'media'    => 'session-4',
				),
				array(
					'title'    => __( 'Closing Panel: Craft, Tools and Next Directions', 'gnn' ),
					'excerpt'  => __( 'Four practitioners compare notes on emerging tools, team culture and what comes next.', 'gnn' ),
					'category' => __( 'Panel', 'gnn' ),
					'year'     => '2026',
					'media'    => 'session-5',
				),
			),
			'posts'    => array(
				array(
					'title'   => __( 'First wave of keynote speakers and workshop hosts announced', 'gnn' ),
					'excerpt' => __( 'Meet the practitioners joining the main stage and studio rooms for our upcoming edition.', 'gnn' ),
					'media'   => 'news-1',
				),
				array(
					'title'   => __( 'How to plan your two-day conference schedule', 'gnn' ),
					'excerpt' => __( 'Tips on balancing main-stage talks, hands-on workshops and informal roundtable discussions.', 'gnn' ),
					'media'   => 'news-2',
				),
				array(
					'title'   => __( 'Venue guide: getting there, accessibility and local tips', 'gnn' ),
					'excerpt' => __( 'Everything you need to know about check-in times, transport links and on-site facilities.', 'gnn' ),
					'media'   => 'news-3',
				),
			),
		),
		'media'       => array(
			'hero'      => array(
				'alt'     => __( 'Conference auditorium stage illuminated with warm coral and violet spotlights', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Deep obsidian violet (#0e0c14) and electric coral-orange (#ff5a36) stage lighting; wide architectural auditorium with geometric stage backdrop and dramatic light beams, ratio 16:9',
			),
			'speaker'   => array(
				'alt'     => __( 'Stylised stage speaker portrait silhouette with coral rim lighting', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Abstract editorial speaker portrait with high-contrast dark violet background and warm coral-orange geometric rim light, ratio 1:1',
			),
			'workshop'  => array(
				'alt'     => __( 'Interactive workshop studio table with lighting accents', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Collaborative workshop studio scene in deep slate violet and warm coral accent lighting, ratio 1:1',
			),
			'venue'     => array(
				'alt'     => __( 'Contemporary cultural centre foyer and conference hall entrance', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Modern exhibition and conference venue architecture at dusk with warm glowing glass atrium and dark stone facade, ratio 4:3',
			),
			'session-1' => array(
				'alt'     => __( 'Main stage keynote backdrop with abstract light waves', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Abstract stage visual with glowing coral-orange and violet light arcs over dark obsidian grid, ratio 16:10',
			),
			'session-2' => array(
				'alt'     => __( 'Stage screen displaying modular product architecture diagrams', 'gnn' ),
				'license' => 'original',
				'brief'   => 'High-contrast presentation screen composition with geometric wireframes in coral, amber and off-white on dark violet, ratio 16:10',
			),
			'session-3' => array(
				'alt'     => __( 'Breakout studio room arranged for hands-on group exercises', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Warm-lit studio table with abstract cards, markers and geometric diagrams in coral and dark slate palette, ratio 16:10',
			),
			'session-4' => array(
				'alt'     => __( 'Acoustic auditorium ceiling and stage production lighting rig', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Architectural upward angle of stage trusses and warm coral spotlight cones against dark ceiling, ratio 16:10',
			),
			'session-5' => array(
				'alt'     => __( 'Roundtable stage arrangement with four armchairs and warm uplighting', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Minimalist panel stage set with four modern chairs and subtle coral-orange stage floor glow, ratio 16:10',
			),
			'news-1'    => array(
				'alt'     => __( 'Printed conference badges and lanyard passes on dark surface', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Editorial flat-lay of bold typographic event passes and coral lanyards on dark obsidian table, ratio 16:10',
			),
			'news-2'    => array(
				'alt'     => __( 'Timetable board showing stage tracks and session blocks', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Graphic conference schedule grid with coral-orange time markers and crisp geometric typography on dark background, ratio 16:10',
			),
			'news-3'    => array(
				'alt'     => __( 'Courtyard lounge area prepared for evening reception', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Evening venue courtyard with warm string lights, dark architectural columns and coral accent glow, ratio 16:10',
			),
		),
		'css'         => 'event',
	);
}
add_filter(
	'gnn_template_registry',
	function ( $templates ) {
		$templates['event'] = 'gnn_template_data_event';
		return $templates;
	}
);
