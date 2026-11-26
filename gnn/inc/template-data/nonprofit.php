<?php
/**
 * GNNcause template data definition.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GNNcause: Association and foundation template focused on mission, community stories and donations.
 *
 * @return array Template configuration array.
 */
function gnn_template_data_nonprofit() {
	return array(
		'slug'        => 'nonprofit',
		'label'       => __( 'GNNcause', 'gnn' ),
		'description' => __( 'A warm community foundation and nonprofit website built around stories, programmes and contributions.', 'gnn' ),
		'requires'    => array( 'elementor' ),
		'tokens'      => array(
			'accent'       => '#d95d39',
			'accent_light' => '#b84322',
			'accent_ink'   => '#ffffff',
			'radius'       => 10,
			'font'         => 'manrope',
			'heading_font' => 'manrope',
			'mode'         => 'light',
			'light'        => array( '#fbf8f3', '#f3ede2', '#e7decb', '#1f241f', '#555c54', '#dfd5c3' ),
			'dark'         => array( '#141814', '#1c221c', '#262e26', '#f4f1ea', '#a8b0a6', '#2f382f' ),
		),
		'header'      => array(
			'preset'    => 'stacked',
			'cta_label' => __( 'Donate now', 'gnn' ),
			'options'   => array(
				'header_structure'  => 'stacked',
				'header_surface'    => 'solid',
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
						'cards',
						array(
							'eyebrow'      => __( 'Community foundation', 'gnn' ),
							'title'        => __( 'Practical support led by the communities we serve.', 'gnn' ),
							'text'         => __( 'We partner with local educators, families and volunteers to fund lasting programmes in education, food access and neighbourhood resilience.', 'gnn' ),
							'button_label' => __( 'Donate now', 'gnn' ),
							'button_url'   => 'page:contact',
							'image'        => 'media:hero',
							'items'        => array(
								array(
									__( 'Fund a programme', 'gnn' ),
									__( 'Direct your contribution to community classrooms, local food pantries or clean water repairs.', 'gnn' ),
								),
								array(
									__( 'Volunteer locally', 'gnn' ),
									__( 'Share your time and practical skills alongside neighbourhood coordinators.', 'gnn' ),
								),
								array(
									__( 'Partner with us', 'gnn' ),
									__( 'Collaborate on community grants, equipment donations and regional outreach.', 'gnn' ),
								),
							),
						),
					),
					array(
						'about-split',
						'image-left',
						array(
							'eyebrow'      => __( 'Our mission', 'gnn' ),
							'title'        => __( 'Change lasts when local people lead the work', 'gnn' ),
							'text'         => __( 'Every initiative begins by listening to residents, teachers and local coordinators who understand what their neighbourhood needs most.', 'gnn' ),
							'button_label' => __( 'Read our story', 'gnn' ),
							'button_url'   => 'page:about',
							'image'        => 'media:mission',
							'items'        => array(
								__( 'Direct funding to neighbourhood-run programmes', 'gnn' ),
								__( 'Open annual reporting on every grant and project', 'gnn' ),
								__( 'Long-term partnerships instead of one-off handouts', 'gnn' ),
							),
						),
					),
					array(
						'services',
						'cards-3',
						array(
							'eyebrow'      => __( 'Where we work', 'gnn' ),
							'title'        => __( 'Three focus areas for steady impact', 'gnn' ),
							'text'         => __( 'Explore the programmes your contributions keep running throughout the year.', 'gnn' ),
							'button_label' => __( 'View programmes', 'gnn' ),
							'button_url'   => 'page:programs',
							'items'        => array(
								array(
									__( 'Learning & youth', 'gnn' ),
									__( 'After-school reading rooms, mentorship circles and workshop supplies for local schools.', 'gnn' ),
								),
								array(
									__( 'Food & water access', 'gnn' ),
									__( 'Community gardens, shared kitchens and safe water infrastructure maintained by local teams.', 'gnn' ),
								),
								array(
									__( 'Local resilience', 'gnn' ),
									__( 'Emergency preparedness, tool libraries and small grants for neighbourhood repairs.', 'gnn' ),
								),
							),
						),
					),
					array(
						'portfolio',
						'cols-3',
						array(
							'eyebrow'      => __( 'In the field', 'gnn' ),
							'title'        => __( 'Active community initiatives', 'gnn' ),
							'text'         => __( 'Recent projects shaped and run with our regional partners.', 'gnn' ),
							'button_label' => __( 'All initiatives', 'gnn' ),
							'button_url'   => 'page:projects',
						),
					),
					array(
						'process',
						'split-timeline',
						array(
							'eyebrow'      => __( 'Stewardship', 'gnn' ),
							'title'        => __( 'How every contribution moves from gift to action', 'gnn' ),
							'text'         => __( 'We keep our grant process clear and accountable at every step.', 'gnn' ),
							'button_label' => __( 'Our accountability', 'gnn' ),
							'button_url'   => 'page:about',
							'items'        => array(
								array(
									__( 'Community proposal', 'gnn' ),
									__( 'Local groups identify a concrete need and outline a practical plan.', 'gnn' ),
								),
								array(
									__( 'Direct allocation', 'gnn' ),
									__( 'Funds and materials are transferred directly to the programme coordinators.', 'gnn' ),
								),
								array(
									__( 'Field accompaniment', 'gnn' ),
									__( 'Our team provides logistics, training and steady support during delivery.', 'gnn' ),
								),
								array(
									__( 'Open reporting', 'gnn' ),
									__( 'Outcomes and lessons are shared publicly with supporters and residents.', 'gnn' ),
								),
							),
						),
					),
					array(
						'testimonial',
						'featured',
						array(
							'eyebrow' => __( 'Community voice', 'gnn' ),
							'items'   => array(
								array(
									'quote'  => __( 'Add a customer quote here.', 'gnn' ),
									'author' => __( 'Name Surname', 'gnn' ),
									'role'   => __( 'Role or company', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'band',
						array(
							'eyebrow'      => __( 'Take part', 'gnn' ),
							'title'        => __( 'Help a community project take root today.', 'gnn' ),
							'text'         => __( 'Whether you give monthly, volunteer your skills or sponsor a classroom, your support goes straight to the work.', 'gnn' ),
							'button_label' => __( 'Make a contribution', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'programs' => array(
				'title'    => __( 'Programmes', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'left-large',
						array(
							'eyebrow'      => __( 'Our programmes', 'gnn' ),
							'title'        => __( 'Steady programmes built for the long run.', 'gnn' ),
							'text'         => __( 'Describe how your core programmes operate, who they serve and how communities participate in shaping them.', 'gnn' ),
							'button_label' => __( 'Support a programme', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'services',
						'rows',
						array(
							'eyebrow'      => __( 'Focus areas', 'gnn' ),
							'title'        => __( 'What we fund and coordinate', 'gnn' ),
							'text'         => __( 'Each programme is co-designed with local educators, health workers and volunteer leads.', 'gnn' ),
							'button_label' => __( 'Get involved', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									__( 'Neighbourhood learning hubs', 'gnn' ),
									__( 'Safe, well-equipped spaces where students access books, tutoring and creative workshops after school.', 'gnn' ),
								),
								array(
									__( 'Community kitchens & growers', 'gnn' ),
									__( 'Support for local growers, seed libraries and shared kitchens preparing nutritious meals.', 'gnn' ),
								),
								array(
									__( 'Clean water & repair crews', 'gnn' ),
									__( 'Training and spare-parts funding so local technicians can keep water points working year-round.', 'gnn' ),
								),
								array(
									__( 'Youth leadership fellowships', 'gnn' ),
									__( 'Stipends and mentoring for young organisers leading projects in their own towns.', 'gnn' ),
								),
							),
						),
					),
					array(
						'features',
						'split-head',
						array(
							'eyebrow'      => __( 'How we work', 'gnn' ),
							'title'        => __( 'Designed around dignity and local ownership', 'gnn' ),
							'text'         => __( 'We measure success by whether a community can sustain and guide the programme independently.', 'gnn' ),
							'button_label' => __( 'See field projects', 'gnn' ),
							'button_url'   => 'page:projects',
							'items'        => array(
								array(
									__( 'Local hiring', 'gnn' ),
									__( 'Coordinators and facilitators are recruited from the communities where programmes run.', 'gnn' ),
								),
								array(
									__( 'Multi-year commitments', 'gnn' ),
									__( 'We stay with partner groups across multiple cycles so programmes can mature.', 'gnn' ),
								),
								array(
									__( 'Shared learning', 'gnn' ),
									__( 'Regional coordinators meet regularly to exchange practical tools and lessons.', 'gnn' ),
								),
								array(
									__( 'Clear budgets', 'gnn' ),
									__( 'Every programme budget is reviewed openly with both community boards and donors.', 'gnn' ),
								),
							),
						),
					),
					array(
						'faq',
						'single',
						array(
							'eyebrow' => __( 'Programme FAQ', 'gnn' ),
							'title'   => __( 'Questions about contributions and volunteering', 'gnn' ),
							'text'    => __( 'How to direct your gift, apply for a community grant or join a local team.', 'gnn' ),
							'items'   => array(
								array(
									'question' => __( 'Can I dedicate my contribution to a specific programme?', 'gnn' ),
									'answer'   => __( 'Yes. Let us know which focus area you wish to support when you get in touch or complete your donation form.', 'gnn' ),
								),
								array(
									'question' => __( 'How can a local group apply for partnership?', 'gnn' ),
									'answer'   => __( 'Community organisations can send a short outline of their initiative through our contact page during any cycle.', 'gnn' ),
								),
								array(
									'question' => __( 'Do you accept in-kind equipment or book donations?', 'gnn' ),
									'answer'   => __( 'We accept materials that match active requests from our field coordinators. Contact us for the current needs list.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'split',
						array(
							'eyebrow'      => __( 'Partner with us', 'gnn' ),
							'title'        => __( 'Bring a programme to your community', 'gnn' ),
							'text'         => __( 'Reach out to discuss local partnerships, school sponsorships or volunteer days.', 'gnn' ),
							'button_label' => __( 'Contact coordinators', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'projects' => array(
				'title'    => __( 'Impact', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Impact & projects', 'gnn' ),
							'title'        => __( 'Community initiatives in action.', 'gnn' ),
							'text'         => __( 'Browse active and completed projects across education, food access and local infrastructure.', 'gnn' ),
							'button_label' => __( 'Support this work', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'portfolio',
						'filtered',
						array(
							'eyebrow' => __( 'Field directory', 'gnn' ),
							'title'   => __( 'Explore initiatives by focus area', 'gnn' ),
							'text'    => __( 'Filter projects to see how local teams put community grants to work.', 'gnn' ),
						),
					),
					array(
						'values',
						'minimal',
						array(
							'eyebrow' => __( 'Transparency', 'gnn' ),
							'title'   => __( 'How we evaluate and report our work', 'gnn' ),
							'text'    => __( 'Clear standards that keep us accountable to both residents and supporters.', 'gnn' ),
							'items'   => array(
								array(
									__( 'Community feedback first', 'gnn' ),
									__( 'Residents and participants review every project before we call it complete.', 'gnn' ),
								),
								array(
									__( 'Open financial summaries', 'gnn' ),
									__( 'Publish annual breakdowns showing how funds are distributed across programmes.', 'gnn' ),
								),
								array(
									__( 'Honest lessons', 'gnn' ),
									__( 'We document what did not work alongside what succeeded so future projects improve.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'band',
						array(
							'eyebrow'      => __( 'Next initiative', 'gnn' ),
							'title'        => __( 'Help launch our next neighbourhood project.', 'gnn' ),
							'text'         => __( 'Your contribution funds tools, training and local coordination from day one.', 'gnn' ),
							'button_label' => __( 'Donate now', 'gnn' ),
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
							'eyebrow'      => __( 'About the foundation', 'gnn' ),
							'title'        => __( 'Built on neighbour-to-neighbour trust.', 'gnn' ),
							'text'         => __( 'Share the history of your organisation, the communities you serve and the people who guide your mission.', 'gnn' ),
							'button_label' => __( 'Meet our team', 'gnn' ),
							'button_url'   => 'page:contact',
							'image'        => 'media:about',
						),
					),
					array(
						'values',
						'split',
						array(
							'eyebrow' => __( 'Our principles', 'gnn' ),
							'title'   => __( 'What guides every decision we make', 'gnn' ),
							'text'    => __( 'Three commitments shared by our board, staff and field volunteers.', 'gnn' ),
							'items'   => array(
								array(
									__( 'Proximity', 'gnn' ),
									__( 'Decisions are made as close to the work and the community as possible.', 'gnn' ),
								),
								array(
									__( 'Stewardship', 'gnn' ),
									__( 'Every contribution is treated as a public trust with clear accounting.', 'gnn' ),
								),
								array(
									__( 'Continuity', 'gnn' ),
									__( 'We invest in durable local capacity rather than short-term visibility.', 'gnn' ),
								),
							),
						),
					),
					array(
						'team',
						'three',
						array(
							'eyebrow' => __( 'People', 'gnn' ),
							'title'   => __( 'Coordinators and trustees', 'gnn' ),
							'text'    => __( 'Replace these placeholder cards with your directors, coordinators and board members.', 'gnn' ),
							'items'   => array(
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Executive director', 'gnn' ),
									'bio'  => __( 'Add a short biography or role summary here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Programmes lead', 'gnn' ),
									'bio'  => __( 'Add a short biography or role summary here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Community partnerships', 'gnn' ),
									'bio'  => __( 'Add a short biography or role summary here.', 'gnn' ),
								),
							),
						),
					),
					array(
						'logos',
						'grid',
						array(
							'eyebrow' => __( 'Supporters & allies', 'gnn' ),
							'title'   => __( 'Organisations we collaborate with', 'gnn' ),
							'text'    => __( 'Replace these boxes with logos of partner associations, schools and grantmakers.', 'gnn' ),
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
						'split',
						array(
							'eyebrow'      => __( 'Volunteer', 'gnn' ),
							'title'        => __( 'Lend your skills to a local project', 'gnn' ),
							'text'         => __( 'We welcome teachers, builders, organisers and neighbours ready to help.', 'gnn' ),
							'button_label' => __( 'Join as a volunteer', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'news'     => array(
				'title'    => __( 'Stories', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Field notes', 'gnn' ),
							'title'        => __( 'Stories and reports from the community.', 'gnn' ),
							'text'         => __( 'Updates from our programme coordinators, volunteer spotlights and annual stewardship notes.', 'gnn' ),
							'button_label' => __( 'Donate now', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'blog',
						'list',
						array(
							'eyebrow' => __( 'Latest dispatches', 'gnn' ),
							'title'   => __( 'Recent stories', 'gnn' ),
							'text'    => __( 'Read updates directly from our neighbourhood hubs and partner teams.', 'gnn' ),
						),
					),
					array(
						'newsletter',
						'centered',
						array(
							'eyebrow'      => __( 'Stay connected', 'gnn' ),
							'title'        => __( 'Receive monthly field stories', 'gnn' ),
							'text'         => __( 'A thoughtful monthly letter with project updates and upcoming volunteer days.', 'gnn' ),
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
							'eyebrow'      => __( 'Get in touch', 'gnn' ),
							'title'        => __( 'Donate, volunteer or start a conversation.', 'gnn' ),
							'text'         => __( 'Reach our team for donation details, volunteer registration or community grant enquiries.', 'gnn' ),
							'button_label' => __( 'Explore programmes', 'gnn' ),
							'button_url'   => 'page:programs',
						),
					),
					array(
						'contact',
						'cards',
						array(
							'eyebrow' => __( 'Reach us', 'gnn' ),
							'title'   => __( 'Foundation office and channels', 'gnn' ),
							'text'    => __( 'Connect your donation or contact form plugin below, or reach us directly.', 'gnn' ),
						),
					),
					array(
						'cta',
						'email',
						array(
							'eyebrow' => __( 'Direct enquiries', 'gnn' ),
							'title'   => __( 'Write to our partnerships coordinator', 'gnn' ),
							'text'    => __( 'For institutional grants, school partnerships or press requests.', 'gnn' ),
						),
					),
				),
			),
		),
		'menu'        => array( 'home', 'programs', 'projects', 'about', 'news', 'contact' ),
		'content'     => array(
			'projects' => array(
				array(
					'title'    => __( 'Neighbourhood reading room', 'gnn' ),
					'excerpt'  => __( 'Equipped a shared library and after-school study space with local teachers and parent volunteers.', 'gnn' ),
					'category' => __( 'Education', 'gnn' ),
					'year'     => '2026',
					'media'    => 'cause-1',
				),
				array(
					'title'    => __( 'Community harvest kitchen', 'gnn' ),
					'excerpt'  => __( 'Connected urban growers with a shared kitchen to prepare fresh meals and preserve seasonal produce.', 'gnn' ),
					'category' => __( 'Food access', 'gnn' ),
					'year'     => '2026',
					'media'    => 'cause-2',
				),
				array(
					'title'    => __( 'Clean water maintenance cooperative', 'gnn' ),
					'excerpt'  => __( 'Trained regional technicians and stocked a shared parts depot to keep rural water points running.', 'gnn' ),
					'category' => __( 'Water', 'gnn' ),
					'year'     => '2025',
					'media'    => 'cause-3',
				),
				array(
					'title'    => __( 'Neighbourhood tool & repair library', 'gnn' ),
					'excerpt'  => __( 'Established a community-run lending library of repair tools and weekend workshops for residents.', 'gnn' ),
					'category' => __( 'Resilience', 'gnn' ),
					'year'     => '2025',
					'media'    => 'cause-4',
				),
			),
			'posts'    => array(
				array(
					'title'   => __( 'What we learned from a year of community-led grants', 'gnn' ),
					'excerpt' => __( 'When neighbourhood committees shape the budget, projects move faster and stay maintained long after launch.', 'gnn' ),
					'media'   => 'news-1',
				),
				array(
					'title'   => __( 'Inside the after-school reading rooms', 'gnn' ),
					'excerpt' => __( 'How volunteer tutors and local librarians built a welcoming routine for young readers.', 'gnn' ),
					'media'   => 'news-2',
				),
				array(
					'title'   => __( 'How to prepare for your first volunteer day', 'gnn' ),
					'excerpt' => __( 'A simple guide to our orientation sessions, field safety and how we match skills to local needs.', 'gnn' ),
					'media'   => 'news-3',
				),
			),
		),
		'media'       => array(
			'hero'    => array(
				'alt'     => __( 'Warm illustration of community garden and neighbourhood gathering space', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Warm editorial illustration of a sunlit community courtyard with planters, shared tables and warm terracotta (#d95d39), sand (#f3ede2) and deep olive (#1f241f) tones, 16:10 aspect ratio',
			),
			'mission' => array(
				'alt'     => __( 'Hands planting seedlings and sharing books at a community table', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Organic tactile composition of open notebooks, botanical leaves and terracotta pottery on warm parchment (#fbf8f3), 4:3 aspect ratio',
			),
			'about'   => array(
				'alt'     => __( 'Community workshop space with natural daylight and wooden tables', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Warm architectural interior of a community workshop room with timber shelves, soft daylight and earthy terracotta and sage accents, 4:3 aspect ratio',
			),
			'cause-1' => array(
				'alt'     => __( 'Neighbourhood reading room shelves and study tables', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Cozy reading room corner with wooden bookshelves, warm lamp light and terracotta colour accents, 16:10 aspect ratio',
			),
			'cause-2' => array(
				'alt'     => __( 'Fresh harvest baskets and shared community kitchen table', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Overhead composition of woven baskets, leafy greens and ceramic bowls on a warm linen and sand surface, 16:10 aspect ratio',
			),
			'cause-3' => array(
				'alt'     => __( 'Clean water fountain and stone courtyard basin', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Minimal sunlit stone water basin with clear ripples surrounded by terracotta tiles and olive foliage, 16:10 aspect ratio',
			),
			'cause-4' => array(
				'alt'     => __( 'Organized workbench in a community tool library', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Neatly arranged hand tools on a warm timber pegboard with soft natural light and ochre highlights, 16:10 aspect ratio',
			),
			'news-1'  => array(
				'alt'     => __( 'Abstract circles representing community cooperation', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Overlapping organic paper-cut circles in terracotta (#d95d39), warm oat (#f3ede2) and forest charcoal (#1f241f), 16:10 aspect ratio',
			),
			'news-2'  => array(
				'alt'     => __( 'Stack of open books in warm morning light', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Still life of open books and a ceramic cup on a sunlit wooden desk in warm cream and terracotta hues, 16:10 aspect ratio',
			),
			'news-3'  => array(
				'alt'     => __( 'Pathway leading through a community park', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Warm illustrated pathway winding through olive trees and community garden beds at golden hour, 16:10 aspect ratio',
			),
		),
		'css'         => 'nonprofit',
	);
}

add_filter(
	'gnn_template_registry',
	function ( $templates ) {
		$templates['nonprofit'] = 'gnn_template_data_nonprofit';
		return $templates;
	}
);
