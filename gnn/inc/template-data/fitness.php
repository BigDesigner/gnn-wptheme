<?php
/**
 * GNNfit ready-made site template data.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GNNfit: bold dark-mode template for gyms, training studios and coaching programmes.
 *
 * @return array Template configuration and sample data.
 */
function gnn_template_data_fitness() {
	return array(
		'slug'        => 'fitness',
		'label'       => __( 'GNNfit', 'gnn' ),
		'description' => __( 'Training programmes, class schedules, coaching profiles and membership plans for gyms and fitness studios.', 'gnn' ),
		'requires'    => array( 'elementor' ),
		'tokens'      => array(
			'accent'       => '#ff5a26',
			'accent_light' => '#c83e12',
			'accent_ink'   => '#0e1014',
			'radius'       => 10,
			'font'         => 'space',
			'heading_font' => 'space',
			'mode'         => 'dark',
			'light'        => array( '#f7f7f5', '#ffffff', '#eaeae5', '#111317', '#525660', '#dadad2' ),
			'dark'         => array( '#0e1014', '#161920', '#20242e', '#f5f6f8', '#9ea4b0', '#2b303c' ),
		),
		'header'      => array(
			'preset'    => 'transparent',
			'cta_label' => __( 'Join a session', 'gnn' ),
			'options'   => array(
				'topbar_enable'     => 0,
				'header_height'     => 74,
				'header_search_bar' => 0,
				'show_cart'         => 0,
				'show_search'       => 0,
			),
		),
		'pages'       => array(
			'home'       => array(
				'title'    => __( 'Home', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'video-placeholder',
						array(
							'eyebrow'      => __( 'Strength · Conditioning · Coaching', 'gnn' ),
							'title'        => __( 'Train with structure. Progress every week.', 'gnn' ),
							'text'         => __( 'Coach-led small group training, progressive strength blocks and open gym access built around measurable athletic development.', 'gnn' ),
							'button_label' => __( 'Explore programmes', 'gnn' ),
							'button_url'   => 'page:programs',
							'image'        => 'media:hero',
						),
					),
					array(
						'services',
						'rows',
						array(
							'eyebrow'      => __( 'Training disciplines', 'gnn' ),
							'title'        => __( 'Programmes structured for real progression', 'gnn' ),
							'text'         => __( 'Every track follows a planned training cycle so you build strength, endurance and mobility without guesswork.', 'gnn' ),
							'button_label' => __( 'View track', 'gnn' ),
							'button_url'   => 'page:programs',
							'items'        => array(
								array(
									'title' => __( 'Barbell & strength foundation', 'gnn' ),
									'text'  => __( 'Compound lifts, tempo control and progressive overload in small coach-supervised groups.', 'gnn' ),
								),
								array(
									'title' => __( 'Metabolic conditioning circuits', 'gnn' ),
									'text'  => __( 'High-output interval sessions combining ergometers, kettlebells, sleds and bodyweight work.', 'gnn' ),
								),
								array(
									'title' => __( 'Athletic mobility & recovery', 'gnn' ),
									'text'  => __( 'Joint preparation, end-range control and guided down-regulation to keep you training pain-free.', 'gnn' ),
								),
								array(
									'title' => __( 'Personal performance coaching', 'gnn' ),
									'text'  => __( 'Individual movement screening, custom programming and dedicated one-to-one floor sessions.', 'gnn' ),
								),
							),
						),
					),
					array(
						'about-split',
						'image-right',
						array(
							'eyebrow'      => __( 'The training floor', 'gnn' ),
							'title'        => __( 'Coached sessions, capped numbers, serious equipment', 'gnn' ),
							'text'         => __( 'We cap every class so your coach can watch your technique, adjust your working weights and keep your form dialled in.', 'gnn' ),
							'button_label' => __( 'Meet the studio', 'gnn' ),
							'button_url'   => 'page:about',
							'image'        => 'media:studio',
							'items'        => array(
								__( 'Dedicated lifting platforms, competition racks and calibrated plates', 'gnn' ),
								__( 'Capped group sizes with hands-on technique coaching every session', 'gnn' ),
								__( 'Morning, midday and evening timetable blocks across the week', 'gnn' ),
							),
						),
					),
					array(
						'pricing',
						'three',
						array(
							'eyebrow'      => __( 'Memberships', 'gnn' ),
							'title'        => __( 'Simple plans built around how often you train', 'gnn' ),
							'text'         => __( 'Replace the placeholder rates below with your own weekly, monthly or pass pricing.', 'gnn' ),
							'button_label' => __( 'Choose membership', 'gnn' ),
							'button_url'   => 'page:membership',
							'items'        => array(
								array(
									'title'    => __( 'Session pass', 'gnn' ),
									'text'     => __( 'Flexible entry for visitors and athletes supplementing another sport.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Single / Pack', 'gnn' ),
									'featured' => false,
									'features' => array(
										__( 'Access to any scheduled group session', 'gnn' ),
										__( 'Open floor warm-up window', 'gnn' ),
										__( 'Locker and shower facilities', 'gnn' ),
									),
								),
								array(
									'title'    => __( 'Unlimited studio', 'gnn' ),
									'text'     => __( 'Full access to all weekly strength, conditioning and mobility tracks.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Monthly / Annual', 'gnn' ),
									'featured' => true,
									'features' => array(
										__( 'Unlimited group training sessions', 'gnn' ),
										__( 'Daily open gym training hours', 'gnn' ),
										__( 'Quarterly movement and strength review', 'gnn' ),
									),
								),
								array(
									'title'    => __( 'Coaching + access', 'gnn' ),
									'text'     => __( 'Unlimited studio access paired with individual programming and 1-to-1 sessions.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Monthly / Annual', 'gnn' ),
									'featured' => false,
									'features' => array(
										__( 'Everything in Unlimited studio', 'gnn' ),
										__( 'Dedicated personal coach and custom block', 'gnn' ),
										__( 'Direct technique feedback and check-ins', 'gnn' ),
									),
								),
							),
						),
					),
					array(
						'gallery',
						'cols-3',
						array(
							'eyebrow' => __( 'Inside the studio', 'gnn' ),
							'title'   => __( 'Training zones and equipment', 'gnn' ),
							'text'    => __( 'A look across our lifting platforms, conditioning turf and mobility space.', 'gnn' ),
						),
					),
					array(
						'cta',
						'band',
						array(
							'eyebrow'      => __( 'Start training', 'gnn' ),
							'title'        => __( 'Ready to step onto the floor?', 'gnn' ),
							'text'         => __( 'Book an introductory session to tour the facility, meet a coach and find the right training track.', 'gnn' ),
							'button_label' => __( 'Join a session', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'programs'   => array(
				'title'    => __( 'Programmes', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'stacked-cta',
						array(
							'eyebrow'      => __( 'Training curriculum', 'gnn' ),
							'title'        => __( 'Structured blocks for strength, engine and mobility.', 'gnn' ),
							'text'         => __( 'Combine our core tracks across the week or follow a dedicated block matched to your current fitness level.', 'gnn' ),
							'button_label' => __( 'Book a trial session', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								__( 'Progressive multi-week training cycles', 'gnn' ),
								__( 'Scaled options for every experience level', 'gnn' ),
								__( 'Coach-guided warm-up and technique prep', 'gnn' ),
							),
						),
					),
					array(
						'services',
						'cards-4',
						array(
							'eyebrow'      => __( 'Weekly tracks', 'gnn' ),
							'title'        => __( 'Choose the sessions that match your goals', 'gnn' ),
							'text'         => __( 'Each session runs for sixty minutes with a structured warm-up, primary working block and cooldown.', 'gnn' ),
							'button_label' => __( 'Book this track', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title' => __( 'Strength block', 'gnn' ),
									'text'  => __( 'Focused lower, upper and full-body barbell and dumbbell progressions with tracked load targets.', 'gnn' ),
								),
								array(
									'title' => __( 'Engine & intervals', 'gnn' ),
									'text'  => __( 'Paced aerobic and anaerobic conditioning blocks using rowers, bikes, ski ergs and turf drills.', 'gnn' ),
								),
								array(
									'title' => __( 'Functional hybrid', 'gnn' ),
									'text'  => __( 'Combined strength-endurance circuits blending kettlebell complexes, carries and gymnastics foundations.', 'gnn' ),
								),
								array(
									'title' => __( 'Mobility & core', 'gnn' ),
									'text'  => __( 'Active range-of-motion work, trunk stability and decompression ideal for recovery days.', 'gnn' ),
								),
							),
						),
					),
					array(
						'process',
						'timeline',
						array(
							'eyebrow'      => __( 'Athlete onboarding', 'gnn' ),
							'title'        => __( 'How your first four weeks unfold', 'gnn' ),
							'text'         => __( 'We make sure every new member starts at a sustainable pace with clear technical foundations.', 'gnn' ),
							'button_label' => __( 'Start onboarding', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title' => __( 'Introductory movement screen', 'gnn' ),
									'text'  => __( 'A short session with a coach to review training history, injury considerations and baseline mobility.', 'gnn' ),
								),
								array(
									'title' => __( 'Track selection & weekly schedule', 'gnn' ),
									'text'  => __( 'We map out two to four weekly sessions that balance strength work, conditioning and recovery.', 'gnn' ),
								),
								array(
									'title' => __( 'Guided group or 1-to-1 sessions', 'gnn' ),
									'text'  => __( 'Learn primary lifting patterns and pacing benchmarks with direct coaching cues on the floor.', 'gnn' ),
								),
								array(
									'title' => __( 'Four-week progress check-in', 'gnn' ),
									'text'  => __( 'Review your training logs, adjust working loads and set goals for your next training block.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'split',
						array(
							'eyebrow'      => __( 'Personal guidance', 'gnn' ),
							'title'        => __( 'Not sure which programme fits you best?', 'gnn' ),
							'text'         => __( 'Tell us about your current routine and goals. A coach will recommend the right starting track.', 'gnn' ),
							'button_label' => __( 'Talk to a coach', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'membership' => array(
				'title'    => __( 'Membership', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Plans & passes', 'gnn' ),
							'title'        => __( 'Straightforward memberships with clear terms.', 'gnn' ),
							'text'         => __( 'Choose between flexible session packs, unlimited group training or personal coaching memberships.', 'gnn' ),
							'button_label' => __( 'Enquire now', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'pricing',
						'three',
						array(
							'eyebrow'      => __( 'Membership tiers', 'gnn' ),
							'title'        => __( 'Find the plan that matches your schedule', 'gnn' ),
							'text'         => __( 'Update these cards in Elementor with your studio rates, billing frequency and included perks.', 'gnn' ),
							'button_label' => __( 'Select plan', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title'    => __( 'Session pass', 'gnn' ),
									'text'     => __( 'Pay as you train with single drop-in entry or multi-session class packs.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Single / Pack', 'gnn' ),
									'featured' => false,
									'features' => array(
										__( 'Book any group class on the timetable', 'gnn' ),
										__( 'Full use of changing rooms and lockers', 'gnn' ),
										__( 'No recurring monthly commitment', 'gnn' ),
									),
								),
								array(
									'title'    => __( 'Unlimited studio', 'gnn' ),
									'text'     => __( 'Our core membership for consistent weekly training across all disciplines.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Monthly / Annual', 'gnn' ),
									'featured' => true,
									'features' => array(
										__( 'Unlimited coached group sessions', 'gnn' ),
										__( 'Open gym access outside peak class hours', 'gnn' ),
										__( 'Onboarding assessment and block reviews', 'gnn' ),
									),
								),
								array(
									'title'    => __( 'Coaching + access', 'gnn' ),
									'text'     => __( 'Individualised programming and private coaching alongside full studio access.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Monthly / Annual', 'gnn' ),
									'featured' => false,
									'features' => array(
										__( 'All Unlimited studio benefits included', 'gnn' ),
										__( 'Weekly 1-to-1 coaching sessions', 'gnn' ),
										__( 'Custom training block tailored to your goals', 'gnn' ),
									),
								),
							),
						),
					),
					array(
						'features',
						'grid-2x2',
						array(
							'eyebrow'      => __( 'Included with membership', 'gnn' ),
							'title'        => __( 'Everything you need for consistent training', 'gnn' ),
							'text'         => __( 'Every member gets access to well-maintained equipment, structured programming and a supportive coaching team.', 'gnn' ),
							'button_label' => __( 'Book a studio tour', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title' => __( 'Open gym training windows', 'gnn' ),
									'text'  => __( 'Dedicated floor time each day to work on accessory lifts, skill progressions or missed sessions.', 'gnn' ),
								),
								array(
									'title' => __( 'Small group class caps', 'gnn' ),
									'text'  => __( 'Every athlete has their own rack or station—never waiting in line for shared equipment.', 'gnn' ),
								),
								array(
									'title' => __( 'Showers & secure lockers', 'gnn' ),
									'text'  => __( 'Individual shower rooms, digital lockers and stretching space so you can train before work.', 'gnn' ),
								),
								array(
									'title' => __( 'Flexible pause options', 'gnn' ),
									'text'  => __( 'Pause your membership when travelling or recovering with simple notice rules.', 'gnn' ),
								),
							),
						),
					),
					array(
						'faq',
						'two-column',
						array(
							'eyebrow' => __( 'Membership FAQ', 'gnn' ),
							'title'   => __( 'Questions before you join', 'gnn' ),
							'text'    => __( 'Details on trial sessions, experience requirements and membership flexibility.', 'gnn' ),
							'items'   => array(
								array(
									'question' => __( 'Do I need prior lifting experience to join?', 'gnn' ),
									'answer'   => __( 'No. Every session includes scaled load and movement options, and new members begin with an introductory coach check-in.', 'gnn' ),
								),
								array(
									'question' => __( 'Can I try a session before choosing a membership?', 'gnn' ),
									'answer'   => __( 'Yes. Contact us to book a trial session or facility walkthrough with one of our coaches.', 'gnn' ),
								),
								array(
									'question' => __( 'How do I pause or change my membership tier?', 'gnn' ),
									'answer'   => __( 'Let our team know before your next billing date and we will update or pause your plan.', 'gnn' ),
								),
								array(
									'question' => __( 'What should I bring to my first session?', 'gnn' ),
									'answer'   => __( 'Bring flat-soled training shoes, a water bottle and comfortable sportswear. Lockers and showers are available on site.', 'gnn' ),
								),
							),
						),
					),
				),
			),
			'about'      => array(
				'title'    => __( 'Studio', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Our facility & coaches', 'gnn' ),
							'title'        => __( 'Built for focused work and long-term strength.', 'gnn' ),
							'text'         => __( 'Replace this overview with the story of your gym, your coaching philosophy and the layout of your training space.', 'gnn' ),
							'button_label' => __( 'Visit the studio', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'about-split',
						'image-left',
						array(
							'eyebrow'      => __( 'How we coach', 'gnn' ),
							'title'        => __( 'Technical precision in an energetic environment', 'gnn' ),
							'text'         => __( 'We believe group training should still feel personal. Our coaches teach the mechanics behind every movement so you train safely and confidently for years.', 'gnn' ),
							'button_label' => __( 'See our programmes', 'gnn' ),
							'button_url'   => 'page:programs',
							'image'        => 'media:studio',
							'items'        => array(
								__( 'Evidence-based strength and conditioning blocks', 'gnn' ),
								__( 'Clear movement standards before adding intensity', 'gnn' ),
								__( 'A welcoming floor for beginners and experienced lifters alike', 'gnn' ),
							),
						),
					),
					array(
						'team',
						'four',
						array(
							'eyebrow' => __( 'Coaching team', 'gnn' ),
							'title'   => __( 'Coaches on the floor', 'gnn' ),
							'text'    => __( 'Replace these placeholder cards with your coaches, certifications and specialties.', 'gnn' ),
							'image'   => 'media:coach',
							'items'   => array(
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Head coach & programming', 'gnn' ),
									'bio'  => __( 'Add a short biography covering strength coaching background and qualifications.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Strength & barbell specialist', 'gnn' ),
									'bio'  => __( 'Add a short biography covering compound lifting and athletic development.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Conditioning & endurance coach', 'gnn' ),
									'bio'  => __( 'Add a short biography covering metabolic conditioning and pacing strategy.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Mobility & recovery coach', 'gnn' ),
									'bio'  => __( 'Add a short biography covering joint health, movement screening and rehab support.', 'gnn' ),
								),
							),
						),
					),
					array(
						'values',
						'cards',
						array(
							'eyebrow' => __( 'Studio standards', 'gnn' ),
							'title'   => __( 'What defines every session', 'gnn' ),
							'text'    => __( 'Three principles that guide how we programme, coach and run the floor.', 'gnn' ),
							'items'   => array(
								array(
									'title' => __( 'Technique first', 'gnn' ),
									'text'  => __( 'Sound mechanics and controlled range of motion always come ahead of chasing numbers on the board.', 'gnn' ),
								),
								array(
									'title' => __( 'Structured progression', 'gnn' ),
									'text'  => __( 'We follow multi-week blocks rather than random daily workouts so you can track real improvements.', 'gnn' ),
								),
								array(
									'title' => __( 'Accountable community', 'gnn' ),
									'text'  => __( 'Train alongside focused teammates who respect the space, the equipment and the work.', 'gnn' ),
								),
							),
						),
					),
				),
			),
			'news'       => array(
				'title'    => __( 'Training notes', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Coaching journal', 'gnn' ),
							'title'        => __( 'Articles on strength, conditioning and recovery.', 'gnn' ),
							'text'         => __( 'Practical training guides, cycle breakdowns and studio announcements from our coaching team.', 'gnn' ),
							'button_label' => __( 'Explore programmes', 'gnn' ),
							'button_url'   => 'page:programs',
						),
					),
					array(
						'blog',
						'list',
						array(
							'eyebrow'      => __( 'Recent notes', 'gnn' ),
							'title'        => __( 'From the coaching desk', 'gnn' ),
							'text'         => __( 'Read our latest breakdowns on training splits, movement prep and recovery habits.', 'gnn' ),
							'button_label' => __( 'Book a session', 'gnn' ),
							'button_url'   => 'page:contact',
							'image'        => 'media:news-1',
						),
					),
					array(
						'newsletter',
						'centered',
						array(
							'eyebrow'      => __( 'Weekly dispatch', 'gnn' ),
							'title'        => __( 'Get new training block notes and timetable updates', 'gnn' ),
							'text'         => __( 'Short weekly notes on upcoming training cycles, workshops and studio hours.', 'gnn' ),
							'button_label' => __( 'Subscribe', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'contact'    => array(
				'title'    => __( 'Contact', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Visit the floor', 'gnn' ),
							'title'        => __( 'Book your first session or studio walkthrough.', 'gnn' ),
							'text'         => __( 'Drop us a message with your preferred training times and we will set up your introductory visit.', 'gnn' ),
							'button_label' => __( 'View memberships', 'gnn' ),
							'button_url'   => 'page:membership',
						),
					),
					array(
						'contact',
						'split',
						array(
							'eyebrow'    => __( 'Studio location & hours', 'gnn' ),
							'title'      => __( 'Get in touch with the team', 'gnn' ),
							'text'       => __( 'Use the contact cards below or send your trial session request through the form.', 'gnn' ),
							'form_title' => __( 'Book a trial session', 'gnn' ),
							'form_text'  => __( 'Add your booking or contact form here with the form plugin you use.', 'gnn' ),
						),
					),
					array(
						'cta',
						'band',
						array(
							'eyebrow'      => __( 'Weekly tracks', 'gnn' ),
							'title'        => __( 'Explore our training programmes before you visit', 'gnn' ),
							'text'         => __( 'See how our strength, conditioning and mobility sessions are structured across the week.', 'gnn' ),
							'button_label' => __( 'See programmes', 'gnn' ),
							'button_url'   => 'page:programs',
						),
					),
				),
			),
		),
		'menu'        => array( 'home', 'programs', 'membership', 'about', 'news', 'contact' ),
		'content'     => array(
			'projects' => array(
				array(
					'title'    => __( 'Barbell & strength block', 'gnn' ),
					'excerpt'  => __( 'Progressive compound lifting sessions focused on squat, hinge, press and pull mechanics with tracked loads.', 'gnn' ),
					'category' => __( 'Strength', 'gnn' ),
					'year'     => '2026',
					'media'    => 'prog-1',
				),
				array(
					'title'    => __( 'Metabolic conditioning circuit', 'gnn' ),
					'excerpt'  => __( 'Structured interval training combining ergometers, sled pushes and kettlebell work to build work capacity.', 'gnn' ),
					'category' => __( 'Conditioning', 'gnn' ),
					'year'     => '2026',
					'media'    => 'prog-2',
				),
				array(
					'title'    => __( 'Athletic mobility & core lab', 'gnn' ),
					'excerpt'  => __( 'Guided joint preparation, active flexibility and trunk stability work to support heavy training weeks.', 'gnn' ),
					'category' => __( 'Mobility', 'gnn' ),
					'year'     => '2026',
					'media'    => 'prog-3',
				),
				array(
					'title'    => __( 'Small-group functional pod', 'gnn' ),
					'excerpt'  => __( 'Capped coach-led pods where every athlete works from a dedicated rack with individualised load progressions.', 'gnn' ),
					'category' => __( 'Coaching', 'gnn' ),
					'year'     => '2025',
					'media'    => 'prog-4',
				),
				array(
					'title'    => __( '1-to-1 performance assessment', 'gnn' ),
					'excerpt'  => __( 'Individual movement screening, strength baseline testing and a custom weekly training roadmap.', 'gnn' ),
					'category' => __( 'Coaching', 'gnn' ),
					'year'     => '2025',
					'media'    => 'prog-5',
				),
			),
			'posts'    => array(
				array(
					'title'   => __( 'How to structure a balanced weekly training split', 'gnn' ),
					'excerpt' => __( 'Pair primary strength days with conditioning intervals and active recovery so you can progress week after week.', 'gnn' ),
					'media'   => 'news-1',
				),
				array(
					'title'   => __( 'Why movement quality comes before adding load', 'gnn' ),
					'excerpt' => __( 'Consistent tempo and full range of motion build stronger joints and more reliable long-term strength gains.', 'gnn' ),
					'media'   => 'news-2',
				),
				array(
					'title'   => __( 'Simple recovery habits between evening sessions', 'gnn' ),
					'excerpt' => __( 'Practical warm-down, hydration and sleep routines that help your nervous system reset after intense training.', 'gnn' ),
					'media'   => 'news-3',
				),
			),
		),
		'media'       => array(
			'hero'   => array(
				'alt'     => __( 'Dark athletic training studio with dramatic orange rim lighting and barbell racks', 'gnn' ),
				'license' => 'original',
				'brief'   => 'High-contrast dark fitness studio interior in deep carbon slate and steel with warm blaze-orange rim light across barbell racks and turf floor, aspect ratio 16:10.',
			),
			'studio' => array(
				'alt'     => __( 'Row of lifting platforms and calibrated weight plates in a modern gym', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Architectural perspective of black steel power racks, timber lifting platforms and subtle orange accent stripes in a clean dark gym, aspect ratio 4:3.',
			),
			'coach'  => array(
				'alt'     => __( 'Abstract athletic portrait backdrop with dark carbon and orange geometric light', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Abstract geometric portrait placeholder in deep carbon black, graphite and vibrant blaze-orange diagonal light, aspect ratio 1:1.',
			),
			'prog-1' => array(
				'alt'     => __( 'Loaded barbell and chalk bowl on a dark lifting platform', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Close-up composition of a steel barbell sleeve, matte black bumper plates and warm orange directional spotlight on a dark gym floor, aspect ratio 4:3.',
			),
			'prog-2' => array(
				'alt'     => __( 'Conditioning turf track with kettlebells and rowing ergometers', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Dynamic angle of a dark conditioning lane lined with cast-iron kettlebells and ergometers under crisp industrial lighting, aspect ratio 4:3.',
			),
			'prog-3' => array(
				'alt'     => __( 'Calm mobility and stretching zone with matte floor mats', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Minimal recovery studio corner with dark textured mats, wooden gymnastic rings and warm indirect amber wall glow, aspect ratio 4:3.',
			),
			'prog-4' => array(
				'alt'     => __( 'Small-group training pod stations arranged around a central rig', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Symmetrical view of four individual training stations around a matte black steel rig with orange number markers, aspect ratio 4:3.',
			),
			'prog-5' => array(
				'alt'     => __( 'Coaching clipboard, stopwatch and strength progression chart', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Still life of a black training logbook, stopwatch and calibrated fractional plates on a dark steel bench, aspect ratio 4:3.',
			),
			'news-1' => array(
				'alt'     => __( 'Organized dumbbell rack and training timer display', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Graphic side view of a matte black dumbbell rack with glowing orange LED interval clock in the background, aspect ratio 16:10.',
			),
			'news-2' => array(
				'alt'     => __( 'Barbell knurling detail under directional studio light', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Macro texture shot of brushed steel barbell knurling with dramatic dark shadows and warm orange highlight, aspect ratio 16:10.',
			),
			'news-3' => array(
				'alt'     => __( 'Foam rollers and recovery equipment in a quiet studio corner', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Evening studio atmosphere with recovery tools, kettlebells and soft warm floor lighting against dark graphite walls, aspect ratio 16:10.',
			),
		),
		'css'         => 'fitness',
	);
}
add_filter(
	'gnn_template_registry',
	function ( $templates ) {
		$templates['fitness'] = 'gnn_template_data_fitness';
		return $templates;
	}
);
