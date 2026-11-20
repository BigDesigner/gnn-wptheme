<?php
/**
 * GNNlegal ready-made site template data.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GNNlegal: dignified, authoritative template for law firms, barristers and legal counsel.
 *
 * @return array Template data definition.
 */
function gnn_template_data_legal() {
	return array(
		'slug'        => 'legal',
		'label'       => __( 'GNNlegal', 'gnn' ),
		'description' => __( 'A stately, authoritative website for law firms, barristers and legal advisory practices with practice areas, team profiles and matters.', 'gnn' ),
		'requires'    => array( 'elementor' ),
		'tokens'      => array(
			'accent'       => '#1c355e',
			'accent_light' => '#122442',
			'accent_ink'   => '#ffffff',
			'radius'       => 4,
			'font'         => 'manrope',
			'heading_font' => 'space',
			'mode'         => 'light',
			'light'        => array( '#f8fafc', '#f1f5f9', '#e2e8f0', '#0f172a', '#475569', '#cbd5e1' ),
			'dark'         => array( '#0a0f1d', '#101728', '#192238', '#f8fafc', '#94a3b8', '#243048' ),
		),
		'header'      => array(
			'preset'    => 'classic',
			'cta_label' => __( 'Book consultation', 'gnn' ),
			'options'   => array(
				'topbar_enable'     => 1,
				'header_height'     => 74,
				'header_search_bar' => 0,
				'show_cart'         => 0,
				'show_search'       => 1,
			),
		),
		'pages'       => array(
			'home'      => array(
				'title'    => __( 'Home', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'stacked-cta',
						array(
							'eyebrow'         => __( 'Legal counsel & advisory', 'gnn' ),
							'title'           => __( 'Strategic counsel for complex legal challenges.', 'gnn' ),
							'text'            => __( 'We deliver rigorous, business-focused legal representation across corporate transactions, commercial dispute resolution and regulatory compliance.', 'gnn' ),
							'button_label'    => __( 'Book consultation', 'gnn' ),
							'button_url'      => 'page:contact',
							'secondary_label' => __( 'Our practice areas', 'gnn' ),
							'secondary_url'   => 'page:practices',
							'items'           => array(
								__( 'Direct partner involvement on every matter', 'gnn' ),
								__( 'Clear, predictable fee structures without surprises', 'gnn' ),
								__( 'Strict professional confidentiality and data security', 'gnn' ),
							),
						),
					),
					array(
						'logos',
						'strip',
						array(
							'eyebrow' => __( 'Accreditations and legal panels', 'gnn' ),
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
						'services',
						'cards-4',
						array(
							'eyebrow'      => __( 'Expertise', 'gnn' ),
							'title'        => __( 'Core practice areas', 'gnn' ),
							'text'         => __( 'Experienced legal guidance tailored to corporate, institutional and private clients.', 'gnn' ),
							'button_label' => __( 'All practice areas', 'gnn' ),
							'button_url'   => 'page:practices',
							'items'        => array(
								array(
									'title' => __( 'Corporate & commercial', 'gnn' ),
									'text'  => __( 'Entity structuring, cross-border mergers, commercial contracts and corporate governance advisory.', 'gnn' ),
								),
								array(
									'title' => __( 'Dispute resolution', 'gnn' ),
									'text'  => __( 'Commercial litigation, international arbitration and structured settlement negotiation across jurisdictions.', 'gnn' ),
								),
								array(
									'title' => __( 'Real estate & finance', 'gnn' ),
									'text'  => __( 'Commercial acquisitions, planning approvals, asset financing and commercial lease management.', 'gnn' ),
								),
								array(
									'title' => __( 'Employment & governance', 'gnn' ),
									'text'  => __( 'Executive agreements, workplace compliance, redundancy management and internal dispute policies.', 'gnn' ),
								),
							),
						),
					),
					array(
						'about-split',
						'image-right',
						array(
							'eyebrow'      => __( 'The firm', 'gnn' ),
							'title'        => __( 'Decisive advice grounded in commercial reality', 'gnn' ),
							'text'         => __( 'Our partners combine technical legal depth with an understanding of industry context to protect client interests and advance transactions efficiently.', 'gnn' ),
							'button_label' => __( 'About our practice', 'gnn' ),
							'button_url'   => 'page:about',
							'image'        => 'media:about',
							'items'        => array(
								__( 'Partner-led teams dedicated to your transaction or dispute', 'gnn' ),
								__( 'Proven advisory record across finance, industry and public institutions', 'gnn' ),
								__( 'Clear reporting and risk assessment at each procedural milestone', 'gnn' ),
							),
						),
					),
					array(
						'process',
						'split-timeline',
						array(
							'eyebrow'      => __( 'Engagement approach', 'gnn' ),
							'title'        => __( 'How we manage your legal matter', 'gnn' ),
							'text'         => __( 'A structured process ensuring full alignment from initial assessment through conclusion.', 'gnn' ),
							'button_label' => __( 'Schedule discussion', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title' => __( 'Conflict check & intake', 'gnn' ),
									'text'  => __( 'We verify independence, define scope and establish initial confidentiality parameters.', 'gnn' ),
								),
								array(
									'title' => __( 'Assessment & options', 'gnn' ),
									'text'  => __( 'We evaluate factual evidence, assess legal risk and provide plain-language strategy options.', 'gnn' ),
								),
								array(
									'title' => __( 'Execution & advocacy', 'gnn' ),
									'text'  => __( 'We draft filings, lead negotiations and represent your position firmly in all forums.', 'gnn' ),
								),
								array(
									'title' => __( 'Resolution & closure', 'gnn' ),
									'text'  => __( 'We complete settlements, secure closing documentation and provide guidance on ongoing compliance.', 'gnn' ),
								),
							),
						),
					),
					array(
						'team',
						'four',
						array(
							'eyebrow' => __( 'Leadership', 'gnn' ),
							'title'   => __( 'Senior partners and counsel', 'gnn' ),
							'text'    => __( 'Replace these cards with your partners, senior associates and counsel.', 'gnn' ),
							'items'   => array(
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Managing partner', 'gnn' ),
									'bio'  => __( 'Add a short biography or practice specialisation here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Partner, corporate & finance', 'gnn' ),
									'bio'  => __( 'Add a short biography or practice specialisation here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Partner, dispute resolution', 'gnn' ),
									'bio'  => __( 'Add a short biography or practice specialisation here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Senior counsel, regulatory', 'gnn' ),
									'bio'  => __( 'Add a short biography or practice specialisation here.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'split',
						array(
							'eyebrow'      => __( 'Confidential consultation', 'gnn' ),
							'title'        => __( 'Discuss your legal matter with our partners.', 'gnn' ),
							'text'         => __( 'Contact our offices to arrange a confidential review of your requirements.', 'gnn' ),
							'button_label' => __( 'Book consultation', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'practices' => array(
				'title'    => __( 'Practices', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'left-large',
						array(
							'eyebrow'      => __( 'Legal capabilities', 'gnn' ),
							'title'        => __( 'Practice areas built around complex commercial needs.', 'gnn' ),
							'text'         => __( 'Comprehensive legal representation across core business disciplines and regulatory environments.', 'gnn' ),
							'button_label' => __( 'Book consultation', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'services',
						'rows',
						array(
							'eyebrow'      => __( 'Practice breakdown', 'gnn' ),
							'title'        => __( 'Detailed areas of practice', 'gnn' ),
							'text'         => __( 'Review our core legal capabilities and representative counsel services.', 'gnn' ),
							'button_label' => __( 'Inquire on practice', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title' => __( 'Corporate governance & M&A', 'gnn' ),
									'text'  => __( 'Advising boards and investors on mergers, acquisitions, restructuring, joint ventures and director duties.', 'gnn' ),
								),
								array(
									'title' => __( 'Commercial litigation & arbitration', 'gnn' ),
									'text'  => __( 'Representing clients in complex contract disputes, shareholder litigation, injunctions and mediation.', 'gnn' ),
								),
								array(
									'title' => __( 'Real estate & asset infrastructure', 'gnn' ),
									'text'  => __( 'Commercial development, zoning agreements, portfolio acquisitions and commercial landlord-tenant disputes.', 'gnn' ),
								),
								array(
									'title' => __( 'Regulatory compliance & investigations', 'gnn' ),
									'text'  => __( 'Advising on statutory compliance, data protection standards, sector licenses and internal audits.', 'gnn' ),
								),
							),
						),
					),
					array(
						'features',
						'split-head',
						array(
							'eyebrow'      => __( 'Our standards', 'gnn' ),
							'title'        => __( 'What defines our advisory practice', 'gnn' ),
							'text'         => __( 'Four operational principles ensuring consistent, high-standard representation.', 'gnn' ),
							'button_label' => __( 'Contact a specialist', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									__( 'Technical excellence', 'gnn' ),
									__( 'Rigorous statutory interpretation and thorough evidentiary preparation on every filing.', 'gnn' ),
								),
								array(
									__( 'Commercial acumen', 'gnn' ),
									__( 'Legal solutions framed within your broader commercial timeline and operational priorities.', 'gnn' ),
								),
								array(
									__( 'Fee transparency', 'gnn' ),
									__( 'Clear estimates, structured caps and transparent monthly reporting on hours and outlays.', 'gnn' ),
								),
								array(
									__( 'Conflict-free ethics', 'gnn' ),
									__( 'Strict independence protocols ensuring uncompromised loyalty to client interests.', 'gnn' ),
								),
							),
						),
					),
					array(
						'faq',
						'single',
						array(
							'eyebrow' => __( 'Client engagement', 'gnn' ),
							'title'   => __( 'Frequently asked engagement questions', 'gnn' ),
							'text'    => __( 'Common questions regarding representation, conflicts and fee structures.', 'gnn' ),
							'items'   => array(
								array(
									'question' => __( 'How do you conduct conflict of interest checks?', 'gnn' ),
									'answer'   => __( 'Prior to receiving any confidential information, our registry screens all involved parties against active matters.', 'gnn' ),
								),
								array(
									'question' => __( 'What fee structures does your firm offer?', 'gnn' ),
									'answer'   => __( 'We offer hourly billing, capped fee arrangements and staged milestone retainers depending on matter type.', 'gnn' ),
								),
								array(
									'question' => __( 'Can you represent clients in cross-border disputes?', 'gnn' ),
									'answer'   => __( 'Yes. Our partners frequently coordinate multi-jurisdictional proceedings alongside trusted international counsel.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'band',
						array(
							'eyebrow'      => __( 'Direct discussion', 'gnn' ),
							'title'        => __( 'Need legal guidance on an urgent matter?', 'gnn' ),
							'text'         => __( 'Reach out to schedule an initial consultation with one of our practice partners.', 'gnn' ),
							'button_label' => __( 'Contact our partners', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'about'     => array(
				'title'    => __( 'About', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'split',
						array(
							'eyebrow'      => __( 'About the firm', 'gnn' ),
							'title'        => __( 'An independent law firm built on integrity and diligence.', 'gnn' ),
							'text'         => __( 'Introduce your firm’s history, founders and the values that govern your professional representation.', 'gnn' ),
							'button_label' => __( 'View practices', 'gnn' ),
							'button_url'   => 'page:practices',
							'image'        => 'media:counsel',
						),
					),
					array(
						'values',
						'cards',
						array(
							'eyebrow' => __( 'Professional values', 'gnn' ),
							'title'   => __( 'Principles that guide our representation', 'gnn' ),
							'text'    => __( 'Standards maintained across every brief, negotiation and court appearance.', 'gnn' ),
							'items'   => array(
								array(
									'title' => __( 'Integrity', 'gnn' ),
									'text'  => __( 'Honest risk appraisals and objective counsel, even when the advice is difficult to deliver.', 'gnn' ),
								),
								array(
									'title' => __( 'Precision', 'gnn' ),
									'text'  => __( 'Meticulous drafting and thorough case preparation that leave no vulnerability unexamined.', 'gnn' ),
								),
								array(
									'title' => __( 'Diligence', 'gnn' ),
									'text'  => __( 'Proactive case management with steadfast adherence to agreed milestones and procedural deadlines.', 'gnn' ),
								),
							),
						),
					),
					array(
						'team',
						'four',
						array(
							'eyebrow' => __( 'The team', 'gnn' ),
							'title'   => __( 'Partners and legal practitioners', 'gnn' ),
							'text'    => __( 'Replace these profiles with your partners, senior associates and counsel.', 'gnn' ),
							'items'   => array(
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Senior partner, corporate', 'gnn' ),
									'bio'  => __( 'Add a short biography or professional credentials here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Partner, commercial litigation', 'gnn' ),
									'bio'  => __( 'Add a short biography or professional credentials here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Partner, real estate & finance', 'gnn' ),
									'bio'  => __( 'Add a short biography or professional credentials here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Counsel, employment & compliance', 'gnn' ),
									'bio'  => __( 'Add a short biography or professional credentials here.', 'gnn' ),
								),
							),
						),
					),
					array(
						'about-split',
						'numbered',
						array(
							'eyebrow'      => __( 'Our commitment', 'gnn' ),
							'title'        => __( 'Dedicated to client success and institutional trust', 'gnn' ),
							'text'         => __( 'We measure our standing by the longevity of our client partnerships and the outcomes achieved under rigorous scrutiny.', 'gnn' ),
							'button_label' => __( 'Schedule consultation', 'gnn' ),
							'button_url'   => 'page:contact',
							'image'        => 'media:meeting',
							'items'        => array(
								__( 'Clear procedural timelines and regular risk assessments', 'gnn' ),
								__( 'Accessible communication with leading partners', 'gnn' ),
								__( 'Strict adherence to legal ethics and confidentiality protocols', 'gnn' ),
							),
						),
					),
					array(
						'cta',
						'split',
						array(
							'eyebrow'      => __( 'Consultation', 'gnn' ),
							'title'        => __( 'Ready to review your legal matter?', 'gnn' ),
							'text'         => __( 'Contact our partners for a confidential preliminary discussion.', 'gnn' ),
							'button_label' => __( 'Book consultation', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'cases'     => array(
				'title'    => __( 'Matters', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Representative experience', 'gnn' ),
							'title'        => __( 'Representative matters and transactions.', 'gnn' ),
							'text'         => __( 'Anonymized examples of our corporate advisory and litigation representation.', 'gnn' ),
							'button_label' => __( 'Book consultation', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'portfolio',
						'filtered',
						array(
							'eyebrow' => __( 'Selected matters', 'gnn' ),
							'title'   => __( 'Track record across practice sectors', 'gnn' ),
							'text'    => __( 'Filter matters by practice discipline and transaction scope.', 'gnn' ),
						),
					),
					array(
						'features',
						'grid-2x2',
						array(
							'eyebrow'      => __( 'Sector focus', 'gnn' ),
							'title'        => __( 'Industry sectors we regularly advise', 'gnn' ),
							'text'         => __( 'Deep experience across heavily regulated and commercially demanding industries.', 'gnn' ),
							'button_label' => __( 'Ask about your sector', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									__( 'Banking & financial services', 'gnn' ),
									__( 'Regulatory compliance, lending facilities, asset recovery and capital markets.', 'gnn' ),
								),
								array(
									__( 'Commercial real estate & logistics', 'gnn' ),
									__( 'Development schemes, institutional leases, planning disputes and joint ventures.', 'gnn' ),
								),
								array(
									__( 'Technology & intellectual property', 'gnn' ),
									__( 'Licensing agreements, data security regulations and IP infringement protection.', 'gnn' ),
								),
								array(
									__( 'Manufacturing & distribution', 'gnn' ),
									__( 'Supply chain contracting, product liability defense and cross-border transport agreements.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'band',
						array(
							'eyebrow'      => __( 'Specific inquiry', 'gnn' ),
							'title'        => __( 'Have a matter requiring specialized industry counsel?', 'gnn' ),
							'text'         => __( 'Speak directly with a partner experienced in your sector.', 'gnn' ),
							'button_label' => __( 'Inquire on matter', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'news'      => array(
				'title'    => __( 'Insights', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Legal briefings', 'gnn' ),
							'title'        => __( 'Legal insights and regulatory analysis.', 'gnn' ),
							'text'         => __( 'Periodic updates on statutory changes, judicial precedents and compliance requirements.', 'gnn' ),
							'button_label' => __( 'View practice areas', 'gnn' ),
							'button_url'   => 'page:practices',
						),
					),
					array(
						'blog',
						'list',
						array(
							'eyebrow' => __( 'Recent publications', 'gnn' ),
							'title'   => __( 'Articles and legal commentary', 'gnn' ),
							'text'    => __( 'Clear commentary on emerging corporate regulations and court decisions.', 'gnn' ),
						),
					),
					array(
						'newsletter',
						'split',
						array(
							'eyebrow'      => __( 'Regulatory bulletin', 'gnn' ),
							'title'        => __( 'Subscribe to our quarterly legal bulletin', 'gnn' ),
							'text'         => __( 'Key legal developments and regulatory shifts delivered directly to your inbox.', 'gnn' ),
							'button_label' => __( 'Subscribe', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'contact'   => array(
				'title'    => __( 'Contact', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Office locations', 'gnn' ),
							'title'        => __( 'Contact our offices in confidence.', 'gnn' ),
							'text'         => __( 'Reach our reception or practice clerks to schedule an initial consultation.', 'gnn' ),
							'button_label' => __( 'View practices', 'gnn' ),
							'button_url'   => 'page:practices',
						),
					),
					array(
						'contact',
						'cards',
						array(
							'eyebrow'    => __( 'Direct communication', 'gnn' ),
							'title'      => __( 'Offices, phone and secure correspondence', 'gnn' ),
							'text'       => __( 'All communications are handled with strict professional confidentiality.', 'gnn' ),
							'form_title' => __( 'Confidential inquiry form', 'gnn' ),
							'form_text'  => __( 'Add your contact form here with the form plugin you use.', 'gnn' ),
						),
					),
					array(
						'faq',
						'two-column',
						array(
							'eyebrow' => __( 'Intake FAQ', 'gnn' ),
							'title'   => __( 'Consultation and intake process', 'gnn' ),
							'text'    => __( 'Helpful information before scheduling an initial meeting.', 'gnn' ),
							'items'   => array(
								array(
									'question' => __( 'What happens during an initial consultation?', 'gnn' ),
									'answer'   => __( 'We review the primary facts of your matter, assess legal options and discuss engagement terms in confidence.', 'gnn' ),
								),
								array(
									'question' => __( 'Are consultation discussions confidential?', 'gnn' ),
									'answer'   => __( 'Yes. All preliminary communications are protected by strict professional legal confidentiality standards.', 'gnn' ),
								),
								array(
									'question' => __( 'What documentation should we bring?', 'gnn' ),
									'answer'   => __( 'Please bring relevant contracts, correspondence, notices or regulatory filings pertinent to your matter.', 'gnn' ),
								),
								array(
									'question' => __( 'How quickly can your firm take action?', 'gnn' ),
									'answer'   => __( 'Urgent injunctions and time-sensitive filings can be addressed immediately following our intake conflict check.', 'gnn' ),
								),
							),
						),
					),
				),
			),
		),
		'menu'        => array( 'home', 'practices', 'about', 'cases', 'news', 'contact' ),
		'content'     => array(
			'projects' => array(
				array(
					'title'    => __( 'Cross-border industrial merger advisory', 'gnn' ),
					'excerpt'  => __( 'Advised an industrial equipment manufacturer on multi-jurisdiction regulatory clearance and acquisition.', 'gnn' ),
					'category' => __( 'Corporate & M&A', 'gnn' ),
					'year'     => '2026',
					'media'    => 'case-1',
				),
				array(
					'title'    => __( 'Commercial development leasing agreement', 'gnn' ),
					'excerpt'  => __( 'Structured anchor tenant agreements and long-term commercial leases for a major logistics hub.', 'gnn' ),
					'category' => __( 'Real estate', 'gnn' ),
					'year'     => '2026',
					'media'    => 'case-2',
				),
				array(
					'title'    => __( 'Shareholder dispute arbitration settlement', 'gnn' ),
					'excerpt'  => __( 'Represented majority shareholders in complex institutional arbitration resulting in favorable settlement.', 'gnn' ),
					'category' => __( 'Dispute resolution', 'gnn' ),
					'year'     => '2025',
					'media'    => 'case-3',
				),
				array(
					'title'    => __( 'Financial services regulatory restructuring', 'gnn' ),
					'excerpt'  => __( 'Guided a regional investment firm through capital compliance review and statutory restructuring.', 'gnn' ),
					'category' => __( 'Regulatory', 'gnn' ),
					'year'     => '2025',
					'media'    => 'case-4',
				),
				array(
					'title'    => __( 'Executive restructuring and severance governance', 'gnn' ),
					'excerpt'  => __( 'Drafted executive management transitions and non-compete covenants for an enterprise group.', 'gnn' ),
					'category' => __( 'Employment', 'gnn' ),
					'year'     => '2025',
					'media'    => 'case-5',
				),
			),
			'posts'    => array(
				array(
					'title'   => __( 'Navigating recent commercial contract compliance changes', 'gnn' ),
					'excerpt' => __( 'A summary of statutory amendments impacting limitation of liability and indemnification clauses.', 'gnn' ),
					'media'   => 'news-1',
				),
				array(
					'title'   => __( 'Key considerations for cross-border joint ventures', 'gnn' ),
					'excerpt' => __( 'Structuring governing law, arbitration seats and deadlock resolution in multi-party agreements.', 'gnn' ),
					'media'   => 'news-2',
				),
				array(
					'title'   => __( 'Director duties in times of financial and market restructuring', 'gnn' ),
					'excerpt' => __( 'Practical guidance for boards on maintaining solvency reviews and documenting fiduciary decisions.', 'gnn' ),
					'media'   => 'news-3',
				),
			),
		),
		'media'       => array(
			'hero'    => array(
				'alt'     => __( 'Prestigious modern law firm boardroom with mahogany table and city view', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Authoritative modern law firm boardroom with polished wood table, leather chairs and quiet architectural skyline view; deep navy, slate and bronze tones; dignified corporate composition, 16:9 aspect ratio',
			),
			'about'   => array(
				'alt'     => __( 'Partners discussing legal documents in an executive library office', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Attorneys in professional attire conferring over contracts in an elegant law library with leather-bound books; natural soft daylight, 4:3 aspect ratio',
			),
			'counsel' => array(
				'alt'     => __( 'Close-up of legal documents and pen on partner desk', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Executive study desk with legal briefs, fountain pen and bronze desk lamp in soft focus; calm professional navy and warm paper tones, 4:3 aspect ratio',
			),
			'meeting' => array(
				'alt'     => __( 'Clean contemporary conference room with glass partitions', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Minimalist meeting room with architectural glass walls and slate conference table in balanced daylight, 4:3 aspect ratio',
			),
			'case-1'  => array(
				'alt'     => __( 'Modern architectural corporate building facade', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Geometric glass and steel corporate headquarters facade reflecting blue sky, 16:10 aspect ratio',
			),
			'case-2'  => array(
				'alt'     => __( 'Commercial development architectural blueprint and model', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Architectural plans and urban property documents in sharp overhead focus, 16:10 aspect ratio',
			),
			'case-3'  => array(
				'alt'     => __( 'Formal arbitration hearing room setting', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Dignified hearing chamber with oak paneling, leather chairs and quiet formal lighting, 16:10 aspect ratio',
			),
			'case-4'  => array(
				'alt'     => __( 'Abstract financial and regulatory data columns', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Subtle geometric financial columns and clean lines in slate grey and deep navy, 16:10 aspect ratio',
			),
			'case-5'  => array(
				'alt'     => __( 'Corporate office lobby with marble and bronze detail', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Prestigious corporate office reception with dark stone floor and warm bronze accents, 16:10 aspect ratio',
			),
			'news-1'  => array(
				'alt'     => __( 'Signed commercial contract with seal on desk', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Legal contract paperwork with embossed seal and fountain pen in soft side lighting, 16:9 aspect ratio',
			),
			'news-2'  => array(
				'alt'     => __( 'International commercial shipping port and logistics terminal', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Distant container terminal at dusk representing global commerce and trade law, 16:9 aspect ratio',
			),
			'news-3'  => array(
				'alt'     => __( 'Law library shelves with bound statutory volumes', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Row of dark leather and gold-lettered legal reference books in soft library lighting, 16:9 aspect ratio',
			),
		),
		'css'         => 'legal',
	);
}

add_filter(
	'gnn_template_registry',
	function ( $templates ) {
		$templates['legal'] = 'gnn_template_data_legal';
		return $templates;
	}
);
