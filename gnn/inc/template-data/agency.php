<?php
/**
 * GNNagency ready-made site template data definition.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GNNagency: bold, case-study and team-focused digital product agency template.
 *
 * @return array Template configuration data.
 */
function gnn_template_data_agency() {
	return array(
		'slug'        => 'agency',
		'label'       => __( 'GNNagency Classic', 'gnn' ),
		'description' => __( 'A bold digital agency template focused on case studies, senior craftsmanship and multi-disciplinary product delivery.', 'gnn' ),
		'requires'    => array( 'elementor' ),
		'tokens'      => array(
			'accent'       => '#1d4ed8',
			'accent_light' => '#172554',
			'accent_ink'   => '#ffffff',
			'radius'       => 8,
			'font'         => 'manrope',
			'heading_font' => 'space',
			'mode'         => 'light',
			'light'        => array( '#ffffff', '#f8fafc', '#f1f5f9', '#0f172a', '#475569', '#e2e8f0' ),
			'dark'         => array( '#0b0f19', '#111827', '#1f2937', '#f9fafb', '#9ca3af', '#374151' ),
		),
		'header'      => array(
			'preset'    => 'classic',
			'cta_label' => __( 'Start a project', 'gnn' ),
			'options'   => array(
				'header_structure'  => 'classic',
				'header_surface'    => 'solid',
				'header_shape'      => 'full',
				'header_width'      => 'contained',
				'header_behavior'   => 'sticky',
				'header_overlay'    => 0,
				'topbar_enable'     => 0,
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
						'stacked-cta',
						array(
							'eyebrow'         => __( 'Digital product agency', 'gnn' ),
							'title'           => __( 'We engineer bold digital products that define categories.', 'gnn' ),
							'text'            => __( 'An independent team of senior strategists, designers and engineers partnering with ambitious leaders from first discovery to production scale.', 'gnn' ),
							'button_label'    => __( 'Start a project', 'gnn' ),
							'button_url'      => 'page:contact',
							'secondary_label' => __( 'View case studies', 'gnn' ),
							'secondary_url'   => 'page:work',
							'items'           => array(
								__( 'Senior craftspeople on every sprint', 'gnn' ),
								__( 'Fixed milestones and transparent scope', 'gnn' ),
								__( 'Strategy, design and code in one rhythm', 'gnn' ),
							),
						),
					),
					array(
						'logos',
						'strip',
						array(
							'eyebrow' => __( 'Trusted by ambitious brands', 'gnn' ),
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
						'portfolio',
						'cols-2',
						array(
							'eyebrow'      => __( 'Selected work', 'gnn' ),
							'title'        => __( 'Featured case studies', 'gnn' ),
							'text'         => __( 'Explore how we help market leaders and funded startups build high-impact digital experiences.', 'gnn' ),
							'button_label' => __( 'View all work', 'gnn' ),
							'button_url'   => 'page:work',
						),
					),
					array(
						'services',
						'cards-3',
						array(
							'eyebrow'      => __( 'Core disciplines', 'gnn' ),
							'title'        => __( 'Integrated capabilities from vision to market launch', 'gnn' ),
							'text'         => __( 'We bridge strategy, product design and modern engineering under one accountable delivery team.', 'gnn' ),
							'button_label' => __( 'Explore services', 'gnn' ),
							'button_url'   => 'page:services',
							'items'        => array(
								array(
									__( 'Product strategy & discovery', 'gnn' ),
									__( 'Validate market opportunities, map user architectures and define phased product roadmaps.', 'gnn' ),
								),
								array(
									__( 'Brand identity & UI/UX design', 'gnn' ),
									__( 'Craft distinctive design languages, accessible interfaces and scalable design systems.', 'gnn' ),
								),
								array(
									__( 'Full-stack engineering & scale', 'gnn' ),
									__( 'Build fast web platforms, bespoke web applications and performant APIs built to scale.', 'gnn' ),
								),
							),
						),
					),
					array(
						'about-split',
						'numbered',
						array(
							'eyebrow'      => __( 'The agency model', 'gnn' ),
							'title'        => __( 'Small, senior teams that move with high velocity', 'gnn' ),
							'text'         => __( 'We deliberately keep our teams compact and senior. You collaborate directly with the practitioners designing and building your product.', 'gnn' ),
							'button_label' => __( 'About our studio', 'gnn' ),
							'button_url'   => 'page:about',
							'image'        => 'media:about',
							'items'        => array(
								__( 'Direct collaboration with senior specialists with zero management layers', 'gnn' ),
								__( 'Weekly interactive sprints, transparent demos and working code delivered early', 'gnn' ),
								__( 'Durable architectures and clean documentation ready for internal team handover', 'gnn' ),
							),
						),
					),
					array(
						'team',
						'four',
						array(
							'eyebrow' => __( 'Leadership', 'gnn' ),
							'title'   => __( 'Heads of craft on every client engagement', 'gnn' ),
							'text'    => __( 'Meet the partners leading strategy, design, engineering and client delivery.', 'gnn' ),
							'items'   => array(
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Managing partner & strategy', 'gnn' ),
									'bio'  => __( 'Add a short biography or background here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Design director', 'gnn' ),
									'bio'  => __( 'Add a short biography or background here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Engineering director', 'gnn' ),
									'bio'  => __( 'Add a short biography or background here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Head of product delivery', 'gnn' ),
									'bio'  => __( 'Add a short biography or background here.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'split',
						array(
							'eyebrow'      => __( 'New engagements', 'gnn' ),
							'title'        => __( 'Have an ambitious digital project in mind?', 'gnn' ),
							'text'         => __( 'Tell us about your timeline, vision and challenges. We assemble senior sprint teams within two weeks.', 'gnn' ),
							'button_label' => __( 'Start a conversation', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'work'     => array(
				'title'    => __( 'Work', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Case studies', 'gnn' ),
							'title'        => __( 'Work that shapes industries and moves metrics.', 'gnn' ),
							'text'         => __( 'Browse selected digital products, brand identities and web platforms created across our studio engagements.', 'gnn' ),
							'button_label' => __( 'Discuss a project', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'portfolio',
						'filtered',
						array(
							'eyebrow' => __( 'All engagements', 'gnn' ),
							'title'   => __( 'Filter by discipline', 'gnn' ),
							'text'    => __( 'Explore our recent collaborations across fintech, commerce, healthcare and enterprise software.', 'gnn' ),
						),
					),
					array(
						'testimonial',
						'cards',
						array(
							'eyebrow' => __( 'Client partnerships', 'gnn' ),
							'title'   => __( 'What founders and product leaders say', 'gnn' ),
							'text'    => __( 'Replace these placeholders with verified partner testimonials.', 'gnn' ),
							'items'   => array(
								array(
									'quote'  => __( 'Add a customer quote here.', 'gnn' ),
									'author' => __( 'Name Surname', 'gnn' ),
									'role'   => __( 'Chief executive', 'gnn' ),
								),
								array(
									'quote'  => __( 'Add a customer quote here.', 'gnn' ),
									'author' => __( 'Name Surname', 'gnn' ),
									'role'   => __( 'Head of product', 'gnn' ),
								),
								array(
									'quote'  => __( 'Add a customer quote here.', 'gnn' ),
									'author' => __( 'Name Surname', 'gnn' ),
									'role'   => __( 'Vice president of engineering', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'band',
						array(
							'eyebrow'      => __( 'Collaboration', 'gnn' ),
							'title'        => __( 'Ready to build your next market advantage?', 'gnn' ),
							'text'         => __( 'We partner with early-stage pioneers and established enterprise innovators alike.', 'gnn' ),
							'button_label' => __( 'Schedule an introduction', 'gnn' ),
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
							'eyebrow'      => __( 'Services & methods', 'gnn' ),
							'title'        => __( 'Full-lifecycle digital product development.', 'gnn' ),
							'text'         => __( 'Whether entering a new market or overhauling an existing flagship product, our multidisciplinary teams deliver with momentum.', 'gnn' ),
							'button_label' => __( 'Book a consultation', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'services',
						'rows',
						array(
							'eyebrow'      => __( 'Capabilities', 'gnn' ),
							'title'        => __( 'Structured engagements designed for clarity', 'gnn' ),
							'text'         => __( 'Each engagement is tailored to your product maturity, team size and delivery horizon.', 'gnn' ),
							'button_label' => __( 'Discuss scope', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title' => __( 'Discovery & product architecture', 'gnn' ),
									'text'  => __( 'Stakeholder interviews, technical feasibility analysis and prioritized sprint backlogs.', 'gnn' ),
								),
								array(
									'title' => __( 'Interface design & design systems', 'gnn' ),
									'text'  => __( 'Component libraries, accessibility standards and responsive interface systems.', 'gnn' ),
								),
								array(
									'title' => __( 'Full-stack software engineering', 'gnn' ),
									'text'  => __( 'Scalable backends, high-performance web frontends and clean automated testing suites.', 'gnn' ),
								),
								array(
									'title' => __( 'Launch orchestration & scale', 'gnn' ),
									'text'  => __( 'Continuous deployment pipelines, observability setup and comprehensive team handover.', 'gnn' ),
								),
							),
						),
					),
					array(
						'process',
						'horizontal',
						array(
							'eyebrow' => __( 'Our process', 'gnn' ),
							'title'   => __( 'How we take products from brief to market', 'gnn' ),
							'text'    => __( 'Four iterative phases that eliminate guesswork and keep velocity high.', 'gnn' ),
							'items'   => array(
								array(
									'title' => __( 'Align', 'gnn' ),
									'text'  => __( 'Unpack goals, constraints, technical architecture and success criteria.', 'gnn' ),
								),
								array(
									'title' => __( 'Prototype', 'gnn' ),
									'text'  => __( 'Validate concepts rapidly with clickable prototypes and customer feedback.', 'gnn' ),
								),
								array(
									'title' => __( 'Build', 'gnn' ),
									'text'  => __( 'Deliver production code in bi-weekly sprints with continuous integration.', 'gnn' ),
								),
								array(
									'title' => __( 'Scale', 'gnn' ),
									'text'  => __( 'Launch with confidence, monitor metrics and smoothly hand over to internal teams.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'split',
						array(
							'eyebrow'      => __( 'Sprint kick-off', 'gnn' ),
							'title'        => __( 'Need a sprint team for your upcoming release?', 'gnn' ),
							'text'         => __( 'Share your goals and requirements. We reply with a proposed squad structure and timeline within one working day.', 'gnn' ),
							'button_label' => __( 'Contact our studio', 'gnn' ),
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
							'title'        => __( 'Crafted by specialists who care about the details.', 'gnn' ),
							'text'         => __( 'Founded by veterans of product design and engineering, our studio exists to build durable, beautiful digital products without agency bureaucracy.', 'gnn' ),
							'button_label' => __( 'View our work', 'gnn' ),
							'button_url'   => 'page:work',
							'image'        => 'media:about',
						),
					),
					array(
						'values',
						'cards',
						array(
							'eyebrow' => __( 'Core principles', 'gnn' ),
							'title'   => __( 'The standards that guide every studio sprint', 'gnn' ),
							'text'    => __( 'We hold our work, our code and our client communication to three uncompromising principles.', 'gnn' ),
							'items'   => array(
								array(
									'title' => __( 'Radical clarity', 'gnn' ),
									'text'  => __( 'We communicate openly, avoid agency jargon and keep every decision grounded in evidence.', 'gnn' ),
								),
								array(
									'title' => __( 'Uncompromising craft', 'gnn' ),
									'text'  => __( 'From accessible contrast to performant markup, we obsess over details users feel immediately.', 'gnn' ),
								),
								array(
									'title' => __( 'Shared accountability', 'gnn' ),
									'text'  => __( 'We share ownership of product outcomes, not just task completion lists.', 'gnn' ),
								),
							),
						),
					),
					array(
						'team',
						'four',
						array(
							'eyebrow' => __( 'The team', 'gnn' ),
							'title'   => __( 'Meet the craft leads behind the work', 'gnn' ),
							'text'    => __( 'Meet the team members leading strategy, design, architecture and delivery.', 'gnn' ),
							'items'   => array(
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Managing partner & strategy', 'gnn' ),
									'bio'  => __( 'Add a short biography or background here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Design director', 'gnn' ),
									'bio'  => __( 'Add a short biography or background here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Engineering director', 'gnn' ),
									'bio'  => __( 'Add a short biography or background here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Head of product delivery', 'gnn' ),
									'bio'  => __( 'Add a short biography or background here.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'band',
						array(
							'eyebrow'      => __( 'Careers & collaborations', 'gnn' ),
							'title'        => __( 'Want to build transformative products together?', 'gnn' ),
							'text'         => __( 'We are always looking for visionary clients and exceptional craft talent.', 'gnn' ),
							'button_label' => __( 'Say hello', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'insights' => array(
				'title'    => __( 'Insights', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Studio insights', 'gnn' ),
							'title'        => __( 'Perspectives on digital craft, design and code.', 'gnn' ),
							'text'         => __( 'Articles, architectural breakdowns and case notes written by our practitioners in the field.', 'gnn' ),
							'button_label' => __( 'Read latest articles', 'gnn' ),
							'button_url'   => 'page:insights',
						),
					),
					array(
						'blog',
						'grid',
						array(
							'eyebrow' => __( 'Recent publications', 'gnn' ),
							'title'   => __( 'Latest from the studio journal', 'gnn' ),
							'text'    => __( 'Explore our practical guides, case teardowns and design system reflections.', 'gnn' ),
						),
					),
					array(
						'newsletter',
						'split',
						array(
							'eyebrow'      => __( 'Quarterly digest', 'gnn' ),
							'title'        => __( 'Get our curated digital craft briefings', 'gnn' ),
							'text'         => __( 'A quarterly email sharing our latest architectural teardowns and design essays. Zero spam.', 'gnn' ),
							'button_label' => __( 'Subscribe to digest', 'gnn' ),
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
							'eyebrow'      => __( 'Start a dialogue', 'gnn' ),
							'title'        => __( 'Let’s talk about your next digital initiative.', 'gnn' ),
							'text'         => __( 'Tell us what you are building. Whether an early concept or an active refactor, we are ready to listen.', 'gnn' ),
							'button_label' => __( 'Explore services', 'gnn' ),
							'button_url'   => 'page:services',
						),
					),
					array(
						'contact',
						'split',
						array(
							'eyebrow'    => __( 'Get in touch', 'gnn' ),
							'title'      => __( 'Studio locations and direct channels', 'gnn' ),
							'text'       => __( 'Reach our leadership team directly by email or fill out the project inquiry form below.', 'gnn' ),
							'form_title' => __( 'Project brief', 'gnn' ),
							'form_text'  => __( 'Add your contact form here with the form plugin you use.', 'gnn' ),
						),
					),
					array(
						'faq',
						'two-column',
						array(
							'eyebrow' => __( 'Studio FAQ', 'gnn' ),
							'title'   => __( 'Frequently asked engagement questions', 'gnn' ),
							'text'    => __( 'Key details regarding sprint timing, squad composition and intellectual property.', 'gnn' ),
							'items'   => array(
								array(
									'question' => __( 'How quickly can a sprint team begin?', 'gnn' ),
									'answer'   => __( 'Most studio sprint engagements kick off within two weeks following discovery and scope alignment.', 'gnn' ),
								),
								array(
									'question' => __( 'How do you structure pricing and scope?', 'gnn' ),
									'answer'   => __( 'We offer transparent sprint-based pricing with fixed milestones and clear deliverables on every engagement.', 'gnn' ),
								),
								array(
									'question' => __( 'Who owns the code and design intellectual property?', 'gnn' ),
									'answer'   => __( 'You retain full ownership of all source code, design assets, tokens and documentation upon delivery.', 'gnn' ),
								),
								array(
									'question' => __( 'Can our internal engineers work alongside your team?', 'gnn' ),
									'answer'   => __( 'Yes. We frequently integrate alongside client teams, conducting joint pull request reviews and knowledge transfers.', 'gnn' ),
								),
							),
						),
					),
				),
			),
		),
		'menu'        => array( 'home', 'work', 'services', 'about', 'insights', 'contact' ),
		'content'     => array(
			'projects' => array(
				array(
					'title'    => __( 'Apex mobile banking platform', 'gnn' ),
					'excerpt'  => __( 'A secure, intuitive personal banking application engineered for high-frequency transactions and clear portfolio overviews.', 'gnn' ),
					'category' => __( 'Fintech', 'gnn' ),
					'year'     => '2026',
					'media'    => 'case-1',
				),
				array(
					'title'    => __( 'Veloce electric mobility design system', 'gnn' ),
					'excerpt'  => __( 'A unified multi-platform design token system and component library powering companion vehicle software.', 'gnn' ),
					'category' => __( 'Design systems', 'gnn' ),
					'year'     => '2026',
					'media'    => 'case-2',
				),
				array(
					'title'    => __( 'Cortex enterprise AI analytics workspace', 'gnn' ),
					'excerpt'  => __( 'A calm, data-dense web console enabling operations teams to query real-time predictive pipeline models.', 'gnn' ),
					'category' => __( 'Web application', 'gnn' ),
					'year'     => '2025',
					'media'    => 'case-3',
				),
				array(
					'title'    => __( 'Monolith architectural materials commerce', 'gnn' ),
					'excerpt'  => __( 'A bespoke digital catalog and sample ordering experience designed for commercial architects and interior specifiers.', 'gnn' ),
					'category' => __( 'E-Commerce', 'gnn' ),
					'year'     => '2025',
					'media'    => 'case-4',
				),
				array(
					'title'    => __( 'Strata distributed telemetry dashboard', 'gnn' ),
					'excerpt'  => __( 'A high-throughput monitoring dashboard displaying container health metrics with millisecond latency.', 'gnn' ),
					'category' => __( 'Infrastructure', 'gnn' ),
					'year'     => '2025',
					'media'    => 'case-5',
				),
			),
			'posts'    => array(
				array(
					'title'   => __( 'Why small senior squads consistently out-ship large agencies', 'gnn' ),
					'excerpt' => __( 'Removing account management intermediaries creates direct ownership and dramatically compresses delivery cycles.', 'gnn' ),
					'media'   => 'news-1',
				),
				array(
					'title'   => __( 'Architecting design systems for cross-functional scale', 'gnn' ),
					'excerpt' => __( 'How to structure design tokens and accessibility guidelines so engineering and product teams stay in sync.', 'gnn' ),
					'media'   => 'news-2',
				),
				array(
					'title'   => __( 'Validating high-risk product bets in two-week sprints', 'gnn' ),
					'excerpt' => __( 'A disciplined framework for testing complex user workflows with clickable prototypes before writing production code.', 'gnn' ),
					'media'   => 'news-3',
				),
			),
		),
		'media'       => array(
			'hero'   => array(
				'alt'     => __( 'Digital agency project team working in a modern collaborative studio', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Bold digital studio workspace showing dual-monitor workstations, architecture wireframes on glass boards and cobalt blue accents (#1d4ed8) in daylight, 16:9 aspect ratio',
			),
			'about'  => array(
				'alt'     => __( 'Designers and engineers reviewing digital interface prototypes', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Close-up of hands sketching mobile wireframes and review notes on a natural oak table with clean typography and coffee cup, 4:3 aspect ratio',
			),
			'case-1' => array(
				'alt'     => __( 'Mobile banking application interface design on a smartphone mockup', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Modern dark and blue fintech smartphone interface showing investment balances and clean transaction lists, 16:10 aspect ratio',
			),
			'case-2' => array(
				'alt'     => __( 'Multi-device design system components and token palette', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Modular UI design system layout with button states, color swatches and typography hierarchies on a light slate background, 16:10 aspect ratio',
			),
			'case-3' => array(
				'alt'     => __( 'Enterprise analytics dashboard on an ultra-wide monitor', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Clean analytical web dashboard showing line graphs, predictive data nodes and status tables in cobalt blue and cool gray, 16:10 aspect ratio',
			),
			'case-4' => array(
				'alt'     => __( 'Architectural materials commerce layout and sample cart', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Minimal e-commerce website showing stone and wood textures, product dimensions and elegant typography, 16:10 aspect ratio',
			),
			'case-5' => array(
				'alt'     => __( 'Infrastructure server telemetry monitoring console', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Dark mode developer console displaying container graphs, latency gauges and cluster nodes in crisp blue and white, 16:10 aspect ratio',
			),
			'news-1' => array(
				'alt'     => __( 'Abstract geometric blocks representing sprint velocity', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Minimal architectural perspective composition of cobalt blue and white geometric planes symbolizing focused execution, 16:9 aspect ratio',
			),
			'news-2' => array(
				'alt'     => __( 'Design tokens and component hierarchy diagram', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Clean graphic diagram illustrating design token inheritance from core values to functional component variables, 16:9 aspect ratio',
			),
			'news-3' => array(
				'alt'     => __( 'Two-week sprint roadmap and validation milestones graphic', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Minimal timeline showing discovery, prototype, testing and launch gates on a light slate background, 16:9 aspect ratio',
			),
		),
		'css'         => 'agency',
	);
}

add_filter(
	'gnn_template_registry',
	function ( $templates ) {
		$templates['agency'] = 'gnn_template_data_agency';
		return $templates;
	}
);
