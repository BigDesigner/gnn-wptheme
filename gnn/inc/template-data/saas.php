<?php
/**
 * GNNapp template data definition.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GNNapp: Software and application showcase focused on features, workflows and pricing.
 *
 * @return array Template configuration array.
 */
function gnn_template_data_saas() {
	return array(
		'slug'        => 'saas',
		'label'       => __( 'GNNapp', 'gnn' ),
		'description' => __( 'A product and software website focused on features, workflows, pricing and release updates.', 'gnn' ),
		'requires'    => array( 'elementor' ),
		'tokens'      => array(
			'accent'       => '#635bff',
			'accent_light' => '#4f46e5',
			'accent_ink'   => '#ffffff',
			'radius'       => 16,
			'font'         => 'manrope',
			'heading_font' => 'space',
			'mode'         => 'dark',
			'light'        => array( '#f8f9fe', '#ffffff', '#eef1fb', '#0f1324', '#4d5574', '#dce1f2' ),
			'dark'         => array( '#0b0d17', '#121626', '#1b2138', '#f3f5ff', '#9ba4c7', '#252c48' ),
		),
		'header'      => array(
			'preset'    => 'centered',
			'cta_label' => __( 'Start free trial', 'gnn' ),
			'options'   => array(
				'topbar_enable'     => 0,
				'header_height'     => 68,
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
						'video-placeholder',
						array(
							'eyebrow'      => __( 'Product platform', 'gnn' ),
							'title'        => __( 'One workspace for product, workflows and releases.', 'gnn' ),
							'text'         => __( 'Plan tasks, automate routine handoffs and track shipping progress in a clean interface built for daily use.', 'gnn' ),
							'button_label' => __( 'Start free trial', 'gnn' ),
							'button_url'   => 'page:pricing',
							'image'        => 'media:hero',
						),
					),
					array(
						'logos',
						'strip',
						array(
							'eyebrow' => __( 'Teams and integrations', 'gnn' ),
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
						'features',
						'grid-2x2',
						array(
							'eyebrow'      => __( 'Core capabilities', 'gnn' ),
							'title'        => __( 'Designed to keep product work moving', 'gnn' ),
							'text'         => __( 'Replace scattered spreadsheets and status threads with structured views that stay in sync.', 'gnn' ),
							'button_label' => __( 'Explore all features', 'gnn' ),
							'button_url'   => 'page:features',
							'items'        => array(
								array(
									__( 'Shared roadmaps', 'gnn' ),
									__( 'Group milestones, dependencies and owners in one view that updates as tasks progress.', 'gnn' ),
								),
								array(
									__( 'Rule-based automation', 'gnn' ),
									__( 'Trigger status changes, notifications and assignments when work moves between stages.', 'gnn' ),
								),
								array(
									__( 'Role permissions', 'gnn' ),
									__( 'Control who can view, edit or approve items across internal teams and external guests.', 'gnn' ),
								),
								array(
									__( 'Open API and webhooks', 'gnn' ),
									__( 'Connect your source control, support desk and reporting tools with documented endpoints.', 'gnn' ),
								),
							),
						),
					),
					array(
						'about-split',
						'image-right',
						array(
							'eyebrow'      => __( 'Daily workflow', 'gnn' ),
							'title'        => __( 'See status at a glance without extra meetings', 'gnn' ),
							'text'         => __( 'Every item links directly to its context, activity log and release target so teams spend less time chasing updates.', 'gnn' ),
							'button_label' => __( 'See how it works', 'gnn' ),
							'button_url'   => 'page:features',
							'image'        => 'media:dashboard',
							'items'        => array(
								__( 'Keyboard shortcuts for fast triage and navigation', 'gnn' ),
								__( 'Custom views filtered by cycle, team or priority', 'gnn' ),
								__( 'Exportable summaries for weekly stakeholder reviews', 'gnn' ),
							),
						),
					),
					array(
						'pricing',
						'three',
						array(
							'eyebrow'      => __( 'Pricing', 'gnn' ),
							'title'        => __( 'Predictable plans for every stage', 'gnn' ),
							'text'         => __( 'Start with a trial workspace and upgrade when your team is ready.', 'gnn' ),
							'button_label' => __( 'Select plan', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title'    => __( 'Starter', 'gnn' ),
									'text'     => __( 'Core workspace for individuals and early projects.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Monthly / Annual', 'gnn' ),
									'featured' => false,
									'features' => array(
										__( 'Core project boards and lists', 'gnn' ),
										__( 'Standard integrations', 'gnn' ),
										__( 'Community and email support', 'gnn' ),
									),
								),
								array(
									'title'    => __( 'Team', 'gnn' ),
									'text'     => __( 'Shared workflows, automation rules and permissions for active teams.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Monthly / Annual', 'gnn' ),
									'featured' => true,
									'features' => array(
										__( 'Everything in Starter', 'gnn' ),
										__( 'Custom automation rules and webhooks', 'gnn' ),
										__( 'Guest access and role permissions', 'gnn' ),
									),
								),
								array(
									'title'    => __( 'Scale', 'gnn' ),
									'text'     => __( 'Advanced security controls, audit logs and dedicated onboarding.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Monthly / Annual', 'gnn' ),
									'featured' => false,
									'features' => array(
										__( 'Everything in Team', 'gnn' ),
										__( 'Single sign-on and audit history', 'gnn' ),
										__( 'Priority support agreement', 'gnn' ),
									),
								),
							),
						),
					),
					array(
						'faq',
						'two-column',
						array(
							'eyebrow' => __( 'FAQ', 'gnn' ),
							'title'   => __( 'Common questions before you start', 'gnn' ),
							'text'    => __( 'Details on setup, data migration and billing flexibility.', 'gnn' ),
							'items'   => array(
								array(
									'question' => __( 'How long does initial setup take?', 'gnn' ),
									'answer'   => __( 'You can create a workspace in minutes and import existing tasks from CSV or supported tools.', 'gnn' ),
								),
								array(
									'question' => __( 'Can we change plans as our team grows?', 'gnn' ),
									'answer'   => __( 'Yes. You can add seats or switch between tiers at any time from your billing settings.', 'gnn' ),
								),
								array(
									'question' => __( 'Can we export our data if we leave?', 'gnn' ),
									'answer'   => __( 'All workspace records, attachments and logs can be exported in standard JSON or CSV formats.', 'gnn' ),
								),
								array(
									'question' => __( 'Do you offer a trial before billing begins?', 'gnn' ),
									'answer'   => __( 'Every new workspace starts with a trial period so your team can test workflows with real tasks.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'split',
						array(
							'eyebrow'      => __( 'Get started', 'gnn' ),
							'title'        => __( 'Ready to test it with your own workflow?', 'gnn' ),
							'text'         => __( 'Set up a trial workspace or book a live walkthrough with our product team.', 'gnn' ),
							'button_label' => __( 'Request a walkthrough', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'features' => array(
				'title'    => __( 'Features', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'stacked-cta',
						array(
							'eyebrow'      => __( 'Platform tour', 'gnn' ),
							'title'        => __( 'Tools that fit how software teams actually ship.', 'gnn' ),
							'text'         => __( 'From first specification to release notes, keep every stage connected in one fast workspace.', 'gnn' ),
							'button_label' => __( 'View pricing', 'gnn' ),
							'button_url'   => 'page:pricing',
							'items'        => array(
								__( 'Structured boards and timelines', 'gnn' ),
								__( 'Documented REST API', 'gnn' ),
								__( 'Granular role permissions', 'gnn' ),
							),
						),
					),
					array(
						'features',
						'split-image',
						array(
							'eyebrow'      => __( 'Automation', 'gnn' ),
							'title'        => __( 'Remove repetitive status updates', 'gnn' ),
							'text'         => __( 'Set clear rules that route incoming requests, assign reviewers and notify channels when blockers appear.', 'gnn' ),
							'button_label' => __( 'Talk to our team', 'gnn' ),
							'button_url'   => 'page:contact',
							'image'        => 'media:workflow',
							'items'        => array(
								array(
									__( 'Event triggers', 'gnn' ),
									__( 'Run actions automatically on label changes, due dates or pull request merges.', 'gnn' ),
								),
								array(
									__( 'Custom templates', 'gnn' ),
									__( 'Standardize recurring checklists for bug reports, launches and security reviews.', 'gnn' ),
								),
								array(
									__( 'Live notifications', 'gnn' ),
									__( 'Send targeted summaries to chat channels or email without noisy alerts.', 'gnn' ),
								),
								array(
									__( 'Audit trail', 'gnn' ),
									__( 'Review a timestamped history of every rule execution and configuration change.', 'gnn' ),
								),
							),
						),
					),
					array(
						'services',
						'cards-4',
						array(
							'eyebrow'      => __( 'Architecture', 'gnn' ),
							'title'        => __( 'Four layers of the platform', 'gnn' ),
							'text'         => __( 'Use individual modules or run your entire delivery cycle in one place.', 'gnn' ),
							'button_label' => __( 'Ask a question', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									__( 'Planning', 'gnn' ),
									__( 'Capture ideas, scope cycles and align capacity across product and engineering.', 'gnn' ),
								),
								array(
									__( 'Execution', 'gnn' ),
									__( 'Track tasks, code branches and review queues with real-time status updates.', 'gnn' ),
								),
								array(
									__( 'Insights', 'gnn' ),
									__( 'Measure cycle duration, throughput and scope changes without manual charts.', 'gnn' ),
								),
								array(
									__( 'Governance', 'gnn' ),
									__( 'Manage workspace roles, authentication policies and data retention rules.', 'gnn' ),
								),
							),
						),
					),
					array(
						'portfolio',
						'filtered',
						array(
							'eyebrow' => __( 'Modules', 'gnn' ),
							'title'   => __( 'Explore workspace modules', 'gnn' ),
							'text'    => __( 'Filter core components and integration templates included with the platform.', 'gnn' ),
						),
					),
					array(
						'cta',
						'band',
						array(
							'eyebrow'      => __( 'Start building', 'gnn' ),
							'title'        => __( 'See how the platform fits your stack', 'gnn' ),
							'text'         => __( 'Start a trial workspace or ask our engineers about custom integrations.', 'gnn' ),
							'button_label' => __( 'Start free trial', 'gnn' ),
							'button_url'   => 'page:pricing',
						),
					),
				),
			),
			'pricing'  => array(
				'title'    => __( 'Pricing', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Plans and billing', 'gnn' ),
							'title'        => __( 'Transparent pricing that scales with your team.', 'gnn' ),
							'text'         => __( 'Choose monthly or annual billing. Upgrade, downgrade or cancel whenever your needs change.', 'gnn' ),
							'button_label' => __( 'Contact sales', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'pricing',
						'three',
						array(
							'eyebrow'      => __( 'Compare tiers', 'gnn' ),
							'title'        => __( 'Pick the workspace that fits your stage', 'gnn' ),
							'text'         => __( 'Replace the placeholder rates below with your own monthly or annual pricing.', 'gnn' ),
							'button_label' => __( 'Choose plan', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title'    => __( 'Starter', 'gnn' ),
									'text'     => __( 'Essential tools for small projects and initial evaluation.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Monthly / Annual', 'gnn' ),
									'featured' => false,
									'features' => array(
										__( 'Unlimited tasks and views', 'gnn' ),
										__( 'Basic webhook integrations', 'gnn' ),
										__( 'Standard email support', 'gnn' ),
									),
								),
								array(
									'title'    => __( 'Team', 'gnn' ),
									'text'     => __( 'Complete collaboration and automation for growing product teams.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Monthly / Annual', 'gnn' ),
									'featured' => true,
									'features' => array(
										__( 'Everything in Starter', 'gnn' ),
										__( 'Multi-step automation builder', 'gnn' ),
										__( 'Private teams and guest roles', 'gnn' ),
									),
								),
								array(
									'title'    => __( 'Scale', 'gnn' ),
									'text'     => __( 'Custom security controls, dedicated onboarding and SLA support.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Monthly / Annual', 'gnn' ),
									'featured' => false,
									'features' => array(
										__( 'Everything in Team', 'gnn' ),
										__( 'SAML SSO and audit exports', 'gnn' ),
										__( 'Dedicated technical specialist', 'gnn' ),
									),
								),
							),
						),
					),
					array(
						'process',
						'horizontal',
						array(
							'eyebrow' => __( 'Onboarding', 'gnn' ),
							'title'   => __( 'From sign-up to full rollout in four steps', 'gnn' ),
							'text'    => __( 'Move your existing projects over without disrupting active sprints.', 'gnn' ),
							'items'   => array(
								array(
									__( 'Create workspace', 'gnn' ),
									__( 'Set up your team structure, cycles and default workflow states.', 'gnn' ),
								),
								array(
									__( 'Import records', 'gnn' ),
									__( 'Bring existing issues and documentation from CSV or connected tools.', 'gnn' ),
								),
								array(
									__( 'Connect stack', 'gnn' ),
									__( 'Link code repositories, notifications and single sign-on.', 'gnn' ),
								),
								array(
									__( 'Invite team', 'gnn' ),
									__( 'Assign member roles and run your first planning cycle together.', 'gnn' ),
								),
							),
						),
					),
					array(
						'faq',
						'single',
						array(
							'eyebrow' => __( 'Billing FAQ', 'gnn' ),
							'title'   => __( 'Questions about plans and licensing', 'gnn' ),
							'text'    => __( 'Clear answers on seat counts, invoicing and security reviews.', 'gnn' ),
							'items'   => array(
								array(
									'question' => __( 'How are active seats calculated?', 'gnn' ),
									'answer'   => __( 'You are billed only for active team members with edit permissions. Read-only guests can be invited according to your plan limits.', 'gnn' ),
								),
								array(
									'question' => __( 'Do you support annual invoicing?', 'gnn' ),
									'answer'   => __( 'Yes. Teams choosing annual billing can pay by card or request an invoice for bank transfer.', 'gnn' ),
								),
								array(
									'question' => __( 'Can we request a security and compliance review?', 'gnn' ),
									'answer'   => __( 'Contact our team to receive our security documentation, data processing agreement and architecture overview.', 'gnn' ),
								),
							),
						),
					),
				),
			),
			'about'    => array(
				'title'    => __( 'About', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'centered',
						array(
							'eyebrow'      => __( 'Our company', 'gnn' ),
							'title'        => __( 'We build calm, dependable software for focused teams.', 'gnn' ),
							'text'         => __( 'Share your company story, why you built the product and the problems you set out to solve.', 'gnn' ),
							'button_label' => __( 'Get in touch', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'about-split',
						'numbered',
						array(
							'eyebrow'      => __( 'How we build', 'gnn' ),
							'title'        => __( 'Crafted for speed, clarity and long-term reliability', 'gnn' ),
							'text'         => __( 'We believe daily tools should feel fast, respect user attention and keep data portable.', 'gnn' ),
							'button_label' => __( 'Explore features', 'gnn' ),
							'button_url'   => 'page:features',
							'image'        => 'media:team-work',
							'items'        => array(
								__( 'Fast interactions designed around keyboard and screen-reader accessibility', 'gnn' ),
								__( 'Clear documentation and predictable release notes on every update', 'gnn' ),
								__( 'Straightforward data ownership with one-click export tools', 'gnn' ),
							),
						),
					),
					array(
						'values',
						'cards',
						array(
							'eyebrow' => __( 'Principles', 'gnn' ),
							'title'   => __( 'What guides our product decisions', 'gnn' ),
							'text'    => __( 'Every feature is weighed against three simple standards.', 'gnn' ),
							'items'   => array(
								array(
									__( 'Simplicity first', 'gnn' ),
									__( 'Solve the core workflow cleanly before adding configuration knobs.', 'gnn' ),
								),
								array(
									__( 'Earn trust daily', 'gnn' ),
									__( 'Communicate clearly about uptime, roadmap priorities and support.', 'gnn' ),
								),
								array(
									__( 'Open by default', 'gnn' ),
									__( 'Provide clean APIs and webhooks so customers never feel locked in.', 'gnn' ),
								),
							),
						),
					),
					array(
						'team',
						'four',
						array(
							'eyebrow' => __( 'Team', 'gnn' ),
							'title'   => __( 'People behind the product', 'gnn' ),
							'text'    => __( 'Replace these placeholder profiles with your founders, engineers and product leads.', 'gnn' ),
							'items'   => array(
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Co-founder & product', 'gnn' ),
									'bio'  => __( 'Add a short biography or area of focus here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Co-founder & engineering', 'gnn' ),
									'bio'  => __( 'Add a short biography or area of focus here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Head of design', 'gnn' ),
									'bio'  => __( 'Add a short biography or area of focus here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Customer engineering', 'gnn' ),
									'bio'  => __( 'Add a short biography or area of focus here.', 'gnn' ),
								),
							),
						),
					),
				),
			),
			'blog'     => array(
				'title'    => __( 'Updates', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Changelog & guides', 'gnn' ),
							'title'        => __( 'Product updates and engineering notes.', 'gnn' ),
							'text'         => __( 'New releases, workflow guides and behind-the-scenes articles from our team.', 'gnn' ),
							'button_label' => __( 'Start free trial', 'gnn' ),
							'button_url'   => 'page:pricing',
						),
					),
					array(
						'blog',
						'grid',
						array(
							'eyebrow' => __( 'Recent releases', 'gnn' ),
							'title'   => __( 'Latest product articles', 'gnn' ),
							'text'    => __( 'Read about new capabilities, API improvements and workflow tips.', 'gnn' ),
						),
					),
					array(
						'newsletter',
						'split',
						array(
							'eyebrow'      => __( 'Release digest', 'gnn' ),
							'title'        => __( 'Get monthly product updates', 'gnn' ),
							'text'         => __( 'A concise summary of new features and guides delivered once a month.', 'gnn' ),
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
							'eyebrow'      => __( 'Support & sales', 'gnn' ),
							'title'        => __( 'Talk to our product team.', 'gnn' ),
							'text'         => __( 'Ask about technical requirements, custom onboarding or support for your workspace.', 'gnn' ),
							'button_label' => __( 'View plans', 'gnn' ),
							'button_url'   => 'page:pricing',
						),
					),
					array(
						'contact',
						'split',
						array(
							'eyebrow' => __( 'Direct channels', 'gnn' ),
							'title'   => __( 'Reach the right team', 'gnn' ),
							'text'    => __( 'Use the contact details below or connect your preferred form plugin.', 'gnn' ),
						),
					),
					array(
						'faq',
						'two-column',
						array(
							'eyebrow' => __( 'Quick answers', 'gnn' ),
							'title'   => __( 'Before you reach out', 'gnn' ),
							'text'    => __( 'Answers to common support and pre-sales questions.', 'gnn' ),
							'items'   => array(
								array(
									'question' => __( 'Where can we find API documentation?', 'gnn' ),
									'answer'   => __( 'Link your developer portal or documentation pages here so engineers can explore endpoints directly.', 'gnn' ),
								),
								array(
									'question' => __( 'Can we book a live product walkthrough?', 'gnn' ),
									'answer'   => __( 'Yes. Send a message with your team size and workflow goals to schedule a session.', 'gnn' ),
								),
							),
						),
					),
				),
			),
		),
		'menu'        => array( 'home', 'features', 'pricing', 'about', 'blog', 'contact' ),
		'content'     => array(
			'projects' => array(
				array(
					'title'    => __( 'Visual workflow builder', 'gnn' ),
					'excerpt'  => __( 'Map multi-step triggers, approvals and notifications with an inspectable rule graph.', 'gnn' ),
					'category' => __( 'Automation', 'gnn' ),
					'year'     => '2026',
					'media'    => 'module-1',
				),
				array(
					'title'    => __( 'Cycle & roadmap views', 'gnn' ),
					'excerpt'  => __( 'Switch between board, list and timeline views while keeping filters and milestones aligned.', 'gnn' ),
					'category' => __( 'Planning', 'gnn' ),
					'year'     => '2026',
					'media'    => 'module-2',
				),
				array(
					'title'    => __( 'Role & permission matrix', 'gnn' ),
					'excerpt'  => __( 'Manage workspace access, guest permissions and audit logs from a single control panel.', 'gnn' ),
					'category' => __( 'Security', 'gnn' ),
					'year'     => '2026',
					'media'    => 'module-3',
				),
				array(
					'title'    => __( 'Webhook & API sync', 'gnn' ),
					'excerpt'  => __( 'Stream workspace events to your repositories, analytics pipeline and support tools.', 'gnn' ),
					'category' => __( 'Integrations', 'gnn' ),
					'year'     => '2026',
					'media'    => 'module-4',
				),
			),
			'posts'    => array(
				array(
					'title'   => __( 'Designing keyboard-first navigation for daily tools', 'gnn' ),
					'excerpt' => __( 'How clear focus states, predictable shortcuts and low-latency views speed up everyday triage.', 'gnn' ),
					'media'   => 'news-1',
				),
				array(
					'title'   => __( 'How to automate handoffs without losing human context', 'gnn' ),
					'excerpt' => __( 'Good automation removes repetitive clicks while keeping ownership and decision notes visible.', 'gnn' ),
					'media'   => 'news-2',
				),
				array(
					'title'   => __( 'A practical checklist for workspace permissions', 'gnn' ),
					'excerpt' => __( 'Structure team roles, guest access and audit trails before scaling your workspace across departments.', 'gnn' ),
					'media'   => 'news-3',
				),
			),
		),
		'media'       => array(
			'hero'      => array(
				'alt'     => __( 'Software workspace interface preview with roadmap and task panels', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Abstract modern SaaS application dashboard frame on deep midnight indigo (#0b0d17) with electric violet (#635bff) accents, clean UI cards and timeline bars, 16:10 aspect ratio',
			),
			'dashboard' => array(
				'alt'     => __( 'Application analytics and cycle status view', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Clean software interface board showing structured kanban columns, status pills and progress bars in cool slate and indigo (#4f46e5, #1b2138), 4:3 aspect ratio',
			),
			'workflow'  => array(
				'alt'     => __( 'Rule-based automation node diagram', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Minimal geometric flow diagram connecting trigger nodes and action cards on a dark navy grid (#121626) with glowing indigo connector lines (#635bff), 4:3 aspect ratio',
			),
			'team-work' => array(
				'alt'     => __( 'Product engineering architecture diagram', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Abstract modular layers and clean geometric blocks in soft lavender, white and electric indigo representing software architecture, 4:3 aspect ratio',
			),
			'module-1'  => array(
				'alt'     => __( 'Visual workflow builder module illustration', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Abstract UI card composition showing branching logic nodes in indigo (#635bff) and cool slate (#1b2138), 16:10 aspect ratio',
			),
			'module-2'  => array(
				'alt'     => __( 'Cycle and roadmap timeline module illustration', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Minimal horizontal gantt and milestone bars on a dark slate canvas with violet and lavender highlights, 16:10 aspect ratio',
			),
			'module-3'  => array(
				'alt'     => __( 'Role and permission matrix module illustration', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Structured grid of toggle switches and shield geometry in deep indigo and crisp white lines, 16:10 aspect ratio',
			),
			'module-4'  => array(
				'alt'     => __( 'Webhook and API integration module illustration', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Geometric code brackets and concentric data rings in electric violet (#635bff) on midnight navy (#0b0d17), 16:10 aspect ratio',
			),
			'news-1'    => array(
				'alt'     => __( 'Keyboard navigation and focus rings graphic', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Stylized minimal keycaps and crisp focus outline rectangles in indigo and cool grey, 16:10 aspect ratio',
			),
			'news-2'    => array(
				'alt'     => __( 'Connected workflow stages illustration', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Abstract relay curves linking three luminous cards across a dark slate surface, 16:10 aspect ratio',
			),
			'news-3'    => array(
				'alt'     => __( 'Workspace access layers graphic', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Concentric translucent rings and structured grid lines in indigo (#4f46e5) and soft lavender (#eef1fb), 16:10 aspect ratio',
			),
		),
		'css'         => 'saas',
	);
}

add_filter(
	'gnn_template_registry',
	function ( $templates ) {
		$templates['saas'] = 'gnn_template_data_saas';
		return $templates;
	}
);
