<?php
/**
 * GNNpersonal ready-made site template data.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GNNpersonal: clean personal website, resume and advisory showcase.
 *
 * @return array Template configuration array.
 */
function gnn_template_data_personal() {
	return array(
		'slug'        => 'personal',
		'label'       => __( 'GNNpersonal', 'gnn' ),
		'description' => __( 'A concise personal website, career timeline and advisory portfolio for independent specialists and advisors.', 'gnn' ),
		'requires'    => array( 'elementor' ),
		'tokens'      => array(
			'accent'       => '#7c2d58',
			'accent_light' => '#56183a',
			'accent_ink'   => '#ffffff',
			'radius'       => 8,
			'font'         => 'manrope',
			'heading_font' => 'space',
			'mode'         => 'light',
			'light'        => array( '#faf9f6', '#f2f0eb', '#e5e2da', '#1a1917', '#54514b', '#d8d4c9' ),
			'dark'         => array( '#121110', '#1a1918', '#262422', '#f5f3ef', '#a39f97', '#2e2b28' ),
		),
		'header'      => array(
			'preset'    => 'minimal',
			'cta_label' => __( 'Get in touch', 'gnn' ),
			'options'   => array(
				'topbar_enable'     => 0,
				'header_height'     => 64,
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
						'stacked-cta',
						array(
							'eyebrow'         => __( 'Independent advisor & specialist', 'gnn' ),
							'title'           => __( 'Clear thinking. Practical delivery. Lasting results.', 'gnn' ),
							'text'            => __( 'I advise leadership teams on strategy, operational models and critical transitions with direct collaboration and low overhead.', 'gnn' ),
							'button_label'    => __( 'Get in touch', 'gnn' ),
							'button_url'      => 'page:contact',
							'secondary_label' => __( 'View experience', 'gnn' ),
							'secondary_url'   => 'page:experience',
							'items'           => array(
								__( '12+ years consulting and operational leadership', 'gnn' ),
								__( 'Available for advisory retainers and project sprints', 'gnn' ),
								__( 'Based in London · working globally', 'gnn' ),
							),
						),
					),
					array(
						'process',
						'split-timeline',
						array(
							'eyebrow'      => __( 'Experience timeline', 'gnn' ),
							'title'        => __( 'A steady track record across leadership and advisory roles', 'gnn' ),
							'text'         => __( 'Key career milestones spanning operational design, strategic research and programme governance.', 'gnn' ),
							'button_label' => __( 'Full resume', 'gnn' ),
							'button_url'   => 'page:experience',
							'items'        => array(
								array(
									'title' => __( 'Principal Advisor', 'gnn' ),
									'text'  => __( 'Advising executive teams on operating models, governance and change execution.', 'gnn' ),
								),
								array(
									'title' => __( 'Director of Operations', 'gnn' ),
									'text'  => __( 'Led cross-functional teams across delivery, finance and organizational transformation.', 'gnn' ),
								),
								array(
									'title' => __( 'Senior Strategy Lead', 'gnn' ),
									'text'  => __( 'Managed research initiatives, stakeholder alignment and multi-market project rollouts.', 'gnn' ),
								),
								array(
									'title' => __( 'Management Consultant', 'gnn' ),
									'text'  => __( 'Delivered diagnostic reviews, feasibility studies and operational improvement sprints.', 'gnn' ),
								),
							),
						),
					),
					array(
						'services',
						'lined',
						array(
							'eyebrow'      => __( 'Focus areas', 'gnn' ),
							'title'        => __( 'How I help organizations move forward', 'gnn' ),
							'text'         => __( 'Engagements are structured around clearly bounded questions with defined milestones.', 'gnn' ),
							'button_label' => __( 'Discuss an engagement', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title' => __( 'Strategy & Governance', 'gnn' ),
									'text'  => __( 'Clarify priorities, assess options and establish governance frameworks that teams can actually follow.', 'gnn' ),
								),
								array(
									'title' => __( 'Operating Model Design', 'gnn' ),
									'text'  => __( 'Streamline handoffs, clarify role accountability and eliminate organizational bottlenecks.', 'gnn' ),
								),
								array(
									'title' => __( 'Project Leadership', 'gnn' ),
									'text'  => __( 'Step into critical transitions to maintain delivery momentum and prepare smooth internal handovers.', 'gnn' ),
								),
							),
						),
					),
					array(
						'portfolio',
						'cols-2',
						array(
							'eyebrow'      => __( 'Selected work', 'gnn' ),
							'title'        => __( 'Recent engagements and case outcomes', 'gnn' ),
							'text'         => __( 'A curated selection of advisory projects delivered across professional sectors.', 'gnn' ),
							'button_label' => __( 'All projects', 'gnn' ),
							'button_url'   => 'page:work',
						),
					),
					array(
						'testimonial',
						'featured',
						array(
							'eyebrow' => __( 'Peer reference', 'gnn' ),
							'title'   => __( 'Add a reference from a client or colleague here.', 'gnn' ),
							'text'    => __( 'Add a customer quote here.', 'gnn' ),
							'items'   => array(
								array(
									'quote'  => __( 'Add a customer quote here.', 'gnn' ),
									'author' => __( 'Name Surname', 'gnn' ),
									'role'   => __( 'Chief Operating Officer', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'email',
						array(
							'eyebrow'      => __( 'Direct contact', 'gnn' ),
							'title'        => __( 'Have an advisory project or challenge in mind?', 'gnn' ),
							'text'         => __( 'Send an email with a brief summary of your timeline and goals. I reply within one business day.', 'gnn' ),
							'button_label' => 'hello@example.com',
							'button_url'   => 'mailto:hello@example.com',
						),
					),
				),
			),
			'about'      => array(
				'title'    => __( 'About', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Background & values', 'gnn' ),
							'title'        => __( 'Independent perspective grounded in real delivery.', 'gnn' ),
							'text'         => __( 'A brief summary of your background, academic qualifications and core professional philosophy.', 'gnn' ),
							'button_label' => __( 'View resume', 'gnn' ),
							'button_url'   => 'page:experience',
						),
					),
					array(
						'about-split',
						'image-left',
						array(
							'eyebrow'      => __( 'Way of working', 'gnn' ),
							'title'        => __( 'Bridging analytical rigor and practical execution', 'gnn' ),
							'text'         => __( 'I combine structured problem solving with deep respect for organizational culture, ensuring solutions stick after the engagement ends.', 'gnn' ),
							'button_label' => __( 'Selected work', 'gnn' ),
							'button_url'   => 'page:work',
							'image'        => 'media:portrait',
							'items'        => array(
								__( 'Master of Science in Management and Economics', 'gnn' ),
								__( 'Certified in agile governance and project assurance', 'gnn' ),
								__( 'Regular contributor to industry publications and roundtables', 'gnn' ),
							),
						),
					),
					array(
						'values',
						'cards',
						array(
							'eyebrow' => __( 'Principles', 'gnn' ),
							'title'   => __( 'What you can expect in every collaboration', 'gnn' ),
							'text'    => __( 'Simple, non-negotiable standards that keep projects focused and productive.', 'gnn' ),
							'items'   => array(
								array(
									'title' => __( 'Intellectual Honesty', 'gnn' ),
									'text'  => __( 'I tell you what the evidence shows, not what is easiest to hear.', 'gnn' ),
								),
								array(
									'title' => __( 'Low Overhead', 'gnn' ),
									'text'  => __( 'Direct communication with no layers of junior staff or unnecessary meetings.', 'gnn' ),
								),
								array(
									'title' => __( 'Clean Handover', 'gnn' ),
									'text'  => __( 'Every deliverable includes documentation and training your team can maintain.', 'gnn' ),
								),
							),
						),
					),
					array(
						'process',
						'timeline',
						array(
							'eyebrow' => __( 'Education & credentials', 'gnn' ),
							'title'   => __( 'Academic and professional foundation', 'gnn' ),
							'text'    => __( 'Qualifications and specialized training that shape my advisory approach.', 'gnn' ),
							'items'   => array(
								array(
									'title' => __( 'Postgraduate Studies in Economics', 'gnn' ),
									'text'  => __( 'Quantitative analysis, industrial economics and policy evaluation.', 'gnn' ),
								),
								array(
									'title' => __( 'Bachelor of Science in Engineering', 'gnn' ),
									'text'  => __( 'Systems analysis, optimization methods and project management.', 'gnn' ),
								),
								array(
									'title' => __( 'Executive Leadership Programme', 'gnn' ),
									'text'  => __( 'Advanced negotiation, board governance and stakeholder alignment.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'split',
						array(
							'eyebrow'      => __( 'Get in touch', 'gnn' ),
							'title'        => __( 'Interested in exploring a collaboration?', 'gnn' ),
							'text'         => __( 'Let us schedule an introductory conversation to discuss your objectives.', 'gnn' ),
							'button_label' => __( 'Contact me', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'experience' => array(
				'title'    => __( 'Experience', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Resume & history', 'gnn' ),
							'title'        => __( 'Professional roles, appointments and credentials.', 'gnn' ),
							'text'         => __( 'A chronological record of executive positions, advisory assignments and operational leadership.', 'gnn' ),
							'button_label' => __( 'Download summary', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'process',
						'timeline',
						array(
							'eyebrow' => __( 'Career history', 'gnn' ),
							'title'   => __( 'Roles and organizational milestones', 'gnn' ),
							'text'    => __( 'Chronological progression through executive and consulting leadership positions.', 'gnn' ),
							'items'   => array(
								array(
									'title' => __( 'Independent Practice · Principal Advisor', 'gnn' ),
									'text'  => __( 'Direct advisory to boards and chief executives on strategy and change execution.', 'gnn' ),
								),
								array(
									'title' => __( 'Regional Group · Director of Operations', 'gnn' ),
									'text'  => __( 'Managed 30+ specialists across operational delivery, process compliance and financial reporting.', 'gnn' ),
								),
								array(
									'title' => __( 'Advisory Services · Senior Strategy Lead', 'gnn' ),
									'text'  => __( 'Led strategic assessment programmes, regulatory reviews and multi-partner agreements.', 'gnn' ),
								),
								array(
									'title' => __( 'Management Consultancy · Consultant', 'gnn' ),
									'text'  => __( 'Supported organizational diagnostic sprints, benchmarking and process streamlining.', 'gnn' ),
								),
							),
						),
					),
					array(
						'services',
						'rows',
						array(
							'eyebrow'      => __( 'Domain expertise', 'gnn' ),
							'title'        => __( 'Core functional capabilities', 'gnn' ),
							'text'         => __( 'Technical disciplines developed through operational and advisory engagements.', 'gnn' ),
							'button_label' => __( 'Discuss a project', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title' => __( 'Organizational Restructuring', 'gnn' ),
									'text'  => __( 'Aligning management tiers, reporting lines and capacity models to core strategy.', 'gnn' ),
								),
								array(
									'title' => __( 'Governance & Risk Oversight', 'gnn' ),
									'text'  => __( 'Designing audit committees, risk registers and compliance reporting routines.', 'gnn' ),
								),
								array(
									'title' => __( 'Executive Coaching & Mentoring', 'gnn' ),
									'text'  => __( 'Supporting newly appointed operational directors and functional department leads.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'email',
						array(
							'eyebrow'      => __( 'Direct communication', 'gnn' ),
							'title'        => __( 'Request a complete curriculum vitae or references', 'gnn' ),
							'text'         => __( 'Reach out directly if you require full references, case briefs or detailed credentials.', 'gnn' ),
							'button_label' => 'hello@example.com',
							'button_url'   => 'mailto:hello@example.com',
						),
					),
				),
			),
			'work'       => array(
				'title'    => __( 'Work', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Selected projects', 'gnn' ),
							'title'        => __( 'Representative engagements and measurable outcomes.', 'gnn' ),
							'text'         => __( 'Client challenges, diagnostic findings and lasting operational improvements.', 'gnn' ),
							'button_label' => __( 'Contact me', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'portfolio',
						'filtered',
						array(
							'eyebrow' => __( 'Case studies', 'gnn' ),
							'title'   => __( 'Explore by engagement type', 'gnn' ),
							'text'    => __( 'Filter by governance, operations, strategy or project leadership.', 'gnn' ),
						),
					),
					array(
						'features',
						'split-head',
						array(
							'eyebrow'      => __( 'Impact metrics', 'gnn' ),
							'title'        => __( 'Measurable outcomes delivered for client organizations', 'gnn' ),
							'text'         => __( 'Advisory work is evaluated against tangible operational and financial improvements.', 'gnn' ),
							'button_label' => __( 'Inquire', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title' => __( 'Cost Optimization', 'gnn' ),
									'text'  => __( 'Identified and eliminated redundant workflows across operating units.', 'gnn' ),
								),
								array(
									'title' => __( 'Decision Velocity', 'gnn' ),
									'text'  => __( 'Shortened approval cycles with clear delegation and escalation matrices.', 'gnn' ),
								),
								array(
									'title' => __( 'Regulatory Clearance', 'gnn' ),
									'text'  => __( 'Achieved full compliance readiness on complex multi-jurisdiction audits.', 'gnn' ),
								),
								array(
									'title' => __( 'Knowledge Retention', 'gnn' ),
									'text'  => __( 'Embedded internal playbooks so teams maintain progress independently.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'split',
						array(
							'eyebrow'      => __( 'Start a dialogue', 'gnn' ),
							'title'        => __( 'Facing a similar strategic or organizational challenge?', 'gnn' ),
							'text'         => __( 'Contact me to discuss how we can structure an effective advisory scope.', 'gnn' ),
							'button_label' => __( 'Schedule a call', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'writing'    => array(
				'title'    => __( 'Writing', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Articles & notes', 'gnn' ),
							'title'        => __( 'Observations on leadership, strategy and operations.', 'gnn' ),
							'text'         => __( 'Practical articles drawn from real client engagements and organizational research.', 'gnn' ),
							'button_label' => __( 'Browse archive', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'blog',
						'list',
						array(
							'eyebrow'      => __( 'Recent publications', 'gnn' ),
							'title'        => __( 'Essays and field notes', 'gnn' ),
							'text'         => __( 'Short reads on organizational design, governance and decision making.', 'gnn' ),
							'button_label' => __( 'Get in touch', 'gnn' ),
							'button_url'   => 'page:contact',
							'image'        => 'media:news-1',
						),
					),
					array(
						'newsletter',
						'split',
						array(
							'eyebrow'      => __( 'Stay in touch', 'gnn' ),
							'title'        => __( 'Subscribe for occasional essays and case notes', 'gnn' ),
							'text'         => __( 'Sent once or twice a quarter. Focused exclusively on practical management insight.', 'gnn' ),
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
							'eyebrow'      => __( 'Inquiries & speaking', 'gnn' ),
							'title'        => __( 'Let us start a conversation.', 'gnn' ),
							'text'         => __( 'Whether you are scoping an advisory assignment, speaking event or board role, feel free to reach out.', 'gnn' ),
							'button_label' => __( 'View resume', 'gnn' ),
							'button_url'   => 'page:experience',
						),
					),
					array(
						'contact',
						'split',
						array(
							'eyebrow'    => __( 'Direct communication', 'gnn' ),
							'title'      => __( 'How to get in touch', 'gnn' ),
							'text'       => __( 'Reach me via email or submit a project overview using the form.', 'gnn' ),
							'form_title' => __( 'Send an inquiry', 'gnn' ),
							'form_text'  => __( 'Add your contact form here with the form plugin you use.', 'gnn' ),
						),
					),
					array(
						'faq',
						'single',
						array(
							'eyebrow' => __( 'Engagement details', 'gnn' ),
							'title'   => __( 'Frequently asked questions', 'gnn' ),
							'text'    => __( 'Practical information on how advisory engagements and consultations are scheduled.', 'gnn' ),
							'items'   => array(
								array(
									'question' => __( 'What formats do you offer for advisory work?', 'gnn' ),
									'answer'   => __( 'I undertake diagnostic sprints (1–2 weeks), strategic project leadership, and ongoing monthly advisory retainers.', 'gnn' ),
								),
								array(
									'question' => __( 'Where are you available to work?', 'gnn' ),
									'answer'   => __( 'I am based in London and work on-site across Europe and remotely with global organizations.', 'gnn' ),
								),
								array(
									'question' => __( 'How do we get started?', 'gnn' ),
									'answer'   => __( 'Send a short summary of your situation. We will arrange an introductory call to assess mutual fit and define next steps.', 'gnn' ),
								),
							),
						),
					),
				),
			),
		),
		'menu'        => array( 'home', 'about', 'experience', 'work', 'writing', 'contact' ),
		'content'     => array(
			'projects' => array(
				array(
					'title'    => __( 'Operating model reorganization', 'gnn' ),
					'excerpt'  => __( 'Redesigned functional reporting structures and approval hierarchies across four business units.', 'gnn' ),
					'category' => __( 'Operations', 'gnn' ),
					'year'     => '2026',
					'media'    => 'case-1',
				),
				array(
					'title'    => __( 'Strategic governance framework', 'gnn' ),
					'excerpt'  => __( 'Established board oversight routines, risk escalation matrices and delegation policies.', 'gnn' ),
					'category' => __( 'Governance', 'gnn' ),
					'year'     => '2025',
					'media'    => 'case-2',
				),
				array(
					'title'    => __( 'Post-merger integration roadmap', 'gnn' ),
					'excerpt'  => __( 'Unified two regional operations teams and standard operating procedures within six months.', 'gnn' ),
					'category' => __( 'Strategy', 'gnn' ),
					'year'     => '2025',
					'media'    => 'case-3',
				),
				array(
					'title'    => __( 'Executive transition leadership', 'gnn' ),
					'excerpt'  => __( 'Interim operational stewardship during leadership changeover, maintaining full delivery pace.', 'gnn' ),
					'category' => __( 'Leadership', 'gnn' ),
					'year'     => '2024',
					'media'    => 'case-4',
				),
			),
			'posts'    => array(
				array(
					'title'   => __( 'Designing a decision framework that teams actually use', 'gnn' ),
					'excerpt' => __( 'A good framework is short, visible and owned by the people who use it. Start with the three questions every decision must answer.', 'gnn' ),
					'media'   => 'news-1',
				),
				array(
					'title'   => __( 'How to run organizational change without losing momentum', 'gnn' ),
					'excerpt' => __( 'Momentum comes from small, visible wins. Plan the first ninety days around results your team can measure directly.', 'gnn' ),
					'media'   => 'news-2',
				),
				array(
					'title'   => __( 'What leadership teams should clarify before hiring an advisor', 'gnn' ),
					'excerpt' => __( 'Clarify scope, who owns delivery internally, and how success will be measured before bringing external counsel on board.', 'gnn' ),
					'media'   => 'news-3',
				),
			),
		),
		'media'       => array(
			'hero'      => array(
				'alt'     => __( 'Minimal desk with notebook, fountain pen and clean architectural light', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Calm architectural workspace with oak surface, notebook, subtle plum ceramic mug and soft natural morning light, aspect ratio 16:10.',
			),
			'portrait'  => array(
				'alt'     => __( 'Quiet portrait backdrop in warm muted stone and plum shadows', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Abstract minimalist portrait backdrop with warm paper texture, deep plum tonal shadows and soft contrast, aspect ratio 4:5.',
			),
			'workspace' => array(
				'alt'     => __( 'Organized consultation table with documents and natural daylight', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Overhead view of an organized consultation table with papers, linen notebook and brass pen in warm daylight, aspect ratio 4:3.',
			),
			'case-1'    => array(
				'alt'     => __( 'Abstract geometric organizational diagram in plum and warm gray', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Abstract clean diagram illustration representing operational workflow and team structure in plum and slate tones, aspect ratio 4:3.',
			),
			'case-2'    => array(
				'alt'     => __( 'Architectural arches and clean balance in natural limestone', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Architectural geometry representing governance structure with clean stone lines and deep shadows, aspect ratio 4:3.',
			),
			'case-3'    => array(
				'alt'     => __( 'Converging lines representing organizational integration', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Minimal abstract composition of converging geometric lines in deep plum, warm bone and charcoal, aspect ratio 4:3.',
			),
			'case-4'    => array(
				'alt'     => __( 'Solid stone block and clean horizon representing leadership stability', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Clean sculptural composition of stacked sandstone blocks in warm neutral and subtle berry tones, aspect ratio 4:3.',
			),
			'news-1'    => array(
				'alt'     => __( 'Open notebook with fountain pen and morning light on oak desk', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Close-up of open linen journal with handwritten notes and soft side lighting on natural timber, aspect ratio 16:10.',
			),
			'news-2'    => array(
				'alt'     => __( 'Abstract architectural staircase representing steady progress', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Minimal architectural staircase with rhythmic light steps and warm shadows, aspect ratio 16:10.',
			),
			'news-3'    => array(
				'alt'     => __( 'Neatly aligned consultation papers on warm wooden table', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Still life of typed report dossier and fountain pen on textured paper background, aspect ratio 16:10.',
			),
		),
		'css'         => 'personal',
	);
}
add_filter(
	'gnn_template_registry',
	function ( $templates ) {
		$templates['personal'] = 'gnn_template_data_personal';
		return $templates;
	}
);
