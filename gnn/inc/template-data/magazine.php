<?php
/**
 * GNNmagazine ready-made site template data.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GNNmagazine: editorial magazine and journal template focused on essays, article grids and cultural departments.
 *
 * @return array Template data definition.
 */
function gnn_template_data_magazine() {
	return array(
		'slug'        => 'magazine',
		'label'       => __( 'GNNmagazine', 'gnn' ),
		'description' => __( 'An editorial magazine and journal for critical essays, cultural commentary and investigative reportage.', 'gnn' ),
		'requires'    => array( 'elementor' ),
		'tokens'      => array(
			'accent'       => '#b91c1c',
			'accent_light' => '#991b1b',
			'accent_ink'   => '#ffffff',
			'radius'       => 0,
			'font'         => 'manrope',
			'heading_font' => 'space',
			'mode'         => 'light',
			'light'        => array( '#faf8f5', '#f2eee7', '#e5ded3', '#1c1917', '#57534e', '#d6cec2' ),
			'dark'         => array( '#141211', '#1c1917', '#292524', '#f5f5f4', '#a8a29e', '#3d3835' ),
		),
		'header'      => array(
			'preset'    => 'centered',
			'cta_label' => __( 'Subscribe', 'gnn' ),
			'options'   => array(
				'topbar_enable'     => 1,
				'header_height'     => 84,
				'header_search_bar' => 0,
				'show_cart'         => 0,
				'show_search'       => 1,
			),
		),
		'pages'       => array(
			'home'        => array(
				'title'    => __( 'Home', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'left-large',
						array(
							'eyebrow'      => __( 'Critical Review · Weekly Dispatches', 'gnn' ),
							'title'        => __( 'Ideas, criticism and investigations shaping modern culture.', 'gnn' ),
							'text'         => __( 'An independent journal published weekly in print and digital formats, free from sponsored influence.', 'gnn' ),
							'button_label' => __( 'Read current issue', 'gnn' ),
							'button_url'   => 'page:articles',
						),
					),
					array(
						'blog',
						'grid',
						array(
							'eyebrow'      => __( 'Cover stories', 'gnn' ),
							'title'        => __( 'Lead essays and investigative features', 'gnn' ),
							'text'         => __( 'In-depth longform reportage from our contributing editors and scholars.', 'gnn' ),
							'button_label' => __( 'All articles', 'gnn' ),
							'image'        => 'media:news-1',
						),
					),
					array(
						'features',
						'grid-2x2',
						array(
							'eyebrow'      => __( 'Editorial desks', 'gnn' ),
							'title'        => __( 'Four perspectives on the contemporary world', 'gnn' ),
							'text'         => __( 'Our coverage is organised around key cultural disciplines and sustained commentary.', 'gnn' ),
							'button_label' => __( 'Explore departments', 'gnn' ),
							'button_url'   => 'page:departments',
							'items'        => array(
								array(
									'title' => __( 'Architecture & cities', 'gnn' ),
									'text'  => __( 'Spatial criticism, civic infrastructure, public squares and the built environment.', 'gnn' ),
								),
								array(
									'title' => __( 'Literature & letters', 'gnn' ),
									'text'  => __( 'Book reviews, translation essays, author interviews and philosophical studies.', 'gnn' ),
								),
								array(
									'title' => __( 'Visual culture & design', 'gnn' ),
									'text'  => __( 'Typography, print history, photographic monographs and exhibition analysis.', 'gnn' ),
								),
								array(
									'title' => __( 'Public ideas & commons', 'gnn' ),
									'text'  => __( 'Debates on democracy, institutional durability, digital commons and ecology.', 'gnn' ),
								),
							),
						),
					),
					array(
						'about-split',
						'numbered',
						array(
							'eyebrow'      => __( 'Editorial charter', 'gnn' ),
							'title'        => __( 'A commitment to depth, accuracy and independence', 'gnn' ),
							'text'         => __( 'In an age of fragmented attention, we produce essays that reward slow, attentive reading.', 'gnn' ),
							'button_label' => __( 'Read our mission', 'gnn' ),
							'button_url'   => 'page:about',
							'image'        => 'media:about',
							'items'        => array(
								__( 'Funded entirely by reader subscriptions with zero sponsored placements', 'gnn' ),
								__( 'Double fact-checking protocol on every historical and scientific citation', 'gnn' ),
								__( 'Permanent open digital archive accessible without paywalls after twelve months', 'gnn' ),
							),
						),
					),
					array(
						'testimonial',
						'featured',
						array(
							'eyebrow' => __( 'Reader dispatch', 'gnn' ),
							'title'   => __( 'From our reading community', 'gnn' ),
							'text'    => __( 'Replace this placeholder with verified reader correspondence.', 'gnn' ),
							'items'   => array(
								array(
									'quote'  => __( 'Add a customer quote here.', 'gnn' ),
									'author' => __( 'Name Surname', 'gnn' ),
									'role'   => __( 'Contributing Reader', 'gnn' ),
								),
							),
						),
					),
					array(
						'team',
						'four',
						array(
							'eyebrow' => __( 'The masthead', 'gnn' ),
							'title'   => __( 'Editors and principal columnists', 'gnn' ),
							'text'    => __( 'Replace these placeholder cards with your editorial team and regular writers.', 'gnn' ),
							'image'   => 'media:about',
							'items'   => array(
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Editor in chief', 'gnn' ),
									'bio'  => __( 'Add a short biography or editorial background here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Literary editor', 'gnn' ),
									'bio'  => __( 'Add a short biography or critical writing background here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Architecture critic', 'gnn' ),
									'bio'  => __( 'Add a short biography or spatial analysis background here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Managing editor', 'gnn' ),
									'bio'  => __( 'Add a short biography or publication production background here.', 'gnn' ),
								),
							),
						),
					),
					array(
						'newsletter',
						'split',
						array(
							'eyebrow'      => __( 'Weekly digest', 'gnn' ),
							'title'        => __( 'Curated reading delivered every Friday morning.', 'gnn' ),
							'text'         => __( 'Receive our lead essay, three recommended books and highlights from the archives.', 'gnn' ),
							'button_label' => __( 'Subscribe free', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'articles'    => array(
				'title'    => __( 'Articles', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Archive & issues', 'gnn' ),
							'title'        => __( 'All published essays, reviews and dossiers.', 'gnn' ),
							'text'         => __( 'Browse current and past editions chronologically or explore by department.', 'gnn' ),
							'button_label' => __( 'View departments', 'gnn' ),
							'button_url'   => 'page:departments',
						),
					),
					array(
						'blog',
						'list',
						array(
							'eyebrow'      => __( 'Chronological index', 'gnn' ),
							'title'        => __( 'Recent dispatches and commentary', 'gnn' ),
							'text'         => __( 'Regular weekly entries and cultural reporting in an editorial list format.', 'gnn' ),
							'button_label' => __( 'Browse issues', 'gnn' ),
							'image'        => 'media:news-2',
						),
					),
					array(
						'features',
						'split-head',
						array(
							'eyebrow'      => __( 'Special series', 'gnn' ),
							'title'        => __( 'Long-term investigations and recurring columns', 'gnn' ),
							'text'         => __( 'Multi-part research dossiers tracking systemic changes across architecture, culture and technology.', 'gnn' ),
							'button_label' => __( 'Explore dossiers', 'gnn' ),
							'button_url'   => 'page:departments',
							'items'        => array(
								array(
									'title' => __( 'The City Reconsidered', 'gnn' ),
									'text'  => __( 'A six-part investigation into urban transit, public housing and the future of town squares.', 'gnn' ),
								),
								array(
									'title' => __( 'Translations & Margins', 'gnn' ),
									'text'  => __( 'Essays on literature beyond the anglophone sphere, focusing on small press editions.', 'gnn' ),
								),
								array(
									'title' => __( 'Material Matters', 'gnn' ),
									'text'  => __( 'Physical crafts, book design, print typography and physical industrial artifacts.', 'gnn' ),
								),
								array(
									'title' => __( 'The Digital Commons', 'gnn' ),
									'text'  => __( 'Critical assessments of software monopolies, open source tools and user autonomy.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'band',
						array(
							'eyebrow'      => __( 'Join our readers', 'gnn' ),
							'title'        => __( 'Subscribe to receive the quarterly print edition.', 'gnn' ),
							'text'         => __( 'Printed on FSC-certified paper with Smyth-sewn binding and shipped worldwide.', 'gnn' ),
							'button_label' => __( 'Subscription plans', 'gnn' ),
							'button_url'   => 'page:subscribe',
						),
					),
				),
			),
			'departments' => array(
				'title'    => __( 'Departments', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'centered',
						array(
							'eyebrow'      => __( 'Thematic desks', 'gnn' ),
							'title'        => __( 'Editorial sections and critical archives.', 'gnn' ),
							'text'         => __( 'Follow specific desks to read dedicated reporting on architecture, literature, arts and public ideas.', 'gnn' ),
							'button_label' => __( 'Browse articles', 'gnn' ),
							'button_url'   => 'page:articles',
						),
					),
					array(
						'services',
						'lined',
						array(
							'eyebrow'      => __( 'The desks', 'gnn' ),
							'title'        => __( 'Core areas of inquiry and criticism', 'gnn' ),
							'text'         => __( 'Each department is overseen by dedicated contributing editors.', 'gnn' ),
							'button_label' => __( 'Read desk', 'gnn' ),
							'button_url'   => 'page:articles',
							'items'        => array(
								array(
									'title' => __( 'Architecture & spatial policy', 'gnn' ),
									'text'  => __( 'Critical writing on public infrastructure, civic space and the history of modern building.', 'gnn' ),
								),
								array(
									'title' => __( 'Essays & philosophy', 'gnn' ),
									'text'  => __( 'Longform intellectual inquiry examining social history, language and political theory.', 'gnn' ),
								),
								array(
									'title' => __( 'Design & typography', 'gnn' ),
									'text'  => __( 'Graphic history, publishing traditions and the material culture of physical books.', 'gnn' ),
								),
							),
						),
					),
					array(
						'portfolio',
						'filtered',
						array(
							'eyebrow'      => __( 'Dossiers & series', 'gnn' ),
							'title'        => __( 'Curated research collections', 'gnn' ),
							'text'         => __( 'Special multi-part editorial dossiers curated around a single topic.', 'gnn' ),
							'button_label' => __( 'View full archive', 'gnn' ),
							'button_url'   => 'page:articles',
						),
					),
					array(
						'cta',
						'split',
						array(
							'eyebrow'      => __( 'Support the work', 'gnn' ),
							'title'        => __( 'Help fund rigorous independent reporting.', 'gnn' ),
							'text'         => __( 'Subscriptions keep our writers paid fairly and our digital archives open to everyone.', 'gnn' ),
							'button_label' => __( 'Choose a plan', 'gnn' ),
							'button_url'   => 'page:subscribe',
						),
					),
				),
			),
			'about'       => array(
				'title'    => __( 'About', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'split',
						array(
							'eyebrow'      => __( 'Our foundation', 'gnn' ),
							'title'        => __( 'A journal founded on the belief that ideas matter.', 'gnn' ),
							'text'         => __( 'Introduce your magazine, founding year, editorial philosophy and the community of readers you serve.', 'gnn' ),
							'button_label' => __( 'Explore archive', 'gnn' ),
							'button_url'   => 'page:articles',
							'image'        => 'media:about',
						),
					),
					array(
						'about-split',
						'image-left',
						array(
							'eyebrow'      => __( 'Publishing philosophy', 'gnn' ),
							'title'        => __( 'Patient criticism in an accelerated world', 'gnn' ),
							'text'         => __( 'We do not run breaking news or aggregated headlines. We publish writing that takes weeks to report and months to deliberate, providing context that lasts.', 'gnn' ),
							'button_label' => __( 'Meet the editors', 'gnn' ),
							'button_url'   => 'page:contact',
							'image'        => 'media:dossier-1',
							'items'        => array(
								__( 'Independent ownership by a nonprofit cultural foundation', 'gnn' ),
								__( 'Standardised transparent contributor fee schedule for all writers', 'gnn' ),
								__( 'Ad-free reading environment both on the web and in print', 'gnn' ),
							),
						),
					),
					array(
						'values',
						'cards',
						array(
							'eyebrow' => __( 'Editorial standards', 'gnn' ),
							'title'   => __( 'The principles that guide our publishing', 'gnn' ),
							'text'    => __( 'Our core commitments to readers, authors and historical accuracy.', 'gnn' ),
							'items'   => array(
								array(
									'title' => __( 'Factual integrity', 'gnn' ),
									'text'  => __( 'Every piece undergoes independent fact-checking and primary source verification.', 'gnn' ),
								),
								array(
									'title' => __( 'Stylistic care', 'gnn' ),
									'text'  => __( 'We value clarity of thought, precise prose and careful typographical composition.', 'gnn' ),
								),
								array(
									'title' => __( 'Open debate', 'gnn' ),
									'text'  => __( 'We publish differing critical viewpoints provided they are argued with rigour.', 'gnn' ),
								),
							),
						),
					),
					array(
						'team',
						'four',
						array(
							'eyebrow' => __( 'Editorial board', 'gnn' ),
							'title'   => __( 'The people behind each edition', 'gnn' ),
							'text'    => __( 'Replace these cards with your publication editors and staff directors.', 'gnn' ),
							'image'   => 'media:about',
							'items'   => array(
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Editor in chief', 'gnn' ),
									'bio'  => __( 'Add a short biography or editorial background here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Managing editor', 'gnn' ),
									'bio'  => __( 'Add a short biography or publishing background here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Senior editor, essays', 'gnn' ),
									'bio'  => __( 'Add a short biography or academic background here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Art & design director', 'gnn' ),
									'bio'  => __( 'Add a short biography or design background here.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'band',
						array(
							'eyebrow'      => __( 'Submissions', 'gnn' ),
							'title'        => __( 'We welcome pitches for essays and criticism.', 'gnn' ),
							'text'         => __( 'Review our pitch guidelines before submitting proposals to our department editors.', 'gnn' ),
							'button_label' => __( 'Pitch guidelines', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'subscribe'   => array(
				'title'    => __( 'Subscribe', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'centered',
						array(
							'eyebrow'      => __( 'Reader membership', 'gnn' ),
							'title'        => __( 'Join our subscriber community.', 'gnn' ),
							'text'         => __( 'Choose a digital or combined print subscription to support independent cultural criticism.', 'gnn' ),
							'button_label' => __( 'Compare plans', 'gnn' ),
							'button_url'   => '#pricing',
						),
					),
					array(
						'pricing',
						'two',
						array(
							'eyebrow'      => __( 'Membership options', 'gnn' ),
							'title'        => __( 'Simple, transparent subscription pricing', 'gnn' ),
							'text'         => __( 'Replace the rate placeholders below with your actual publication prices.', 'gnn' ),
							'button_label' => __( 'Subscribe now', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title'    => __( 'Digital Membership', 'gnn' ),
									'text'     => __( 'Unlimited access to the digital archive and weekly subscriber editions.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Annual or monthly', 'gnn' ),
									'featured' => false,
									'features' => array(
										__( 'Complete access to full digital archive', 'gnn' ),
										__( 'Ad-free reading across all devices', 'gnn' ),
										__( 'Weekly subscriber-only dispatches', 'gnn' ),
									),
								),
								array(
									'title'    => __( 'Print & Digital Bundle', 'gnn' ),
									'text'     => __( 'Four quarterly print volumes delivered to your door, plus complete digital access.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Annual subscription', 'gnn' ),
									'featured' => true,
									'features' => array(
										__( 'Four beautifully printed quarterly volumes', 'gnn' ),
										__( 'Worldwide shipping with protective packaging', 'gnn' ),
										__( 'Full digital access and archive included', 'gnn' ),
									),
								),
							),
						),
					),
					array(
						'faq',
						'single',
						array(
							'eyebrow' => __( 'Subscription details', 'gnn' ),
							'title'   => __( 'Common questions about delivery and access', 'gnn' ),
							'text'    => __( 'Everything you need to know about renewal, international post and student rates.', 'gnn' ),
							'items'   => array(
								array(
									'question' => __( 'How often are print issues published?', 'gnn' ),
									'answer'   => __( 'We publish four quarterly print volumes each year in March, June, September and December.', 'gnn' ),
								),
								array(
									'question' => __( 'Do you offer student or institutional discounts?', 'gnn' ),
									'answer'   => __( 'Yes. Accredited students, teachers and university libraries can apply for a forty percent rate reduction.', 'gnn' ),
								),
								array(
									'question' => __( 'Can I cancel or pause my subscription?', 'gnn' ),
									'answer'   => __( 'You can pause your print deliveries or cancel your subscription renewal at any time through your account portal.', 'gnn' ),
								),
								array(
									'question' => __( 'What paper stock is used for the print journal?', 'gnn' ),
									'answer'   => __( 'Each issue is printed on FSC-certified 120gsm warm white uncoated text stock with a heavyweight 300gsm textured cover.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'split',
						array(
							'eyebrow'      => __( 'Gift subscriptions', 'gnn' ),
							'title'        => __( 'Share thoughtful reading with a colleague or friend.', 'gnn' ),
							'text'         => __( 'Gift subscriptions include a personalised printed letter and a full year of print or digital access.', 'gnn' ),
							'button_label' => __( 'Give a gift subscription', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'contact'     => array(
				'title'    => __( 'Contact', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Editorial contacts', 'gnn' ),
							'title'        => __( 'Letters, pitches and subscriber enquiries.', 'gnn' ),
							'text'         => __( 'Reach out to our editorial desk, rights department or subscription support team.', 'gnn' ),
							'button_label' => __( 'Read archive', 'gnn' ),
							'button_url'   => 'page:articles',
						),
					),
					array(
						'contact',
						'cards',
						array(
							'eyebrow'    => __( 'Editorial offices', 'gnn' ),
							'title'      => __( 'Contact the journal', 'gnn' ),
							'text'       => __( 'For letters to the editor, rights permissions and press inquiries.', 'gnn' ),
							'form_title' => __( 'Send an editorial message', 'gnn' ),
							'form_text'  => __( 'Add your editorial enquiry or contact form here with the form plugin you use.', 'gnn' ),
						),
					),
					array(
						'faq',
						'two-column',
						array(
							'eyebrow' => __( 'Submission guidelines', 'gnn' ),
							'title'   => __( 'Pitching essays and book reviews', 'gnn' ),
							'text'    => __( 'Please review these guidelines before sending unsolicited drafts.', 'gnn' ),
							'items'   => array(
								array(
									'question' => __( 'How should essay pitches be formatted?', 'gnn' ),
									'answer'   => __( 'Send a two-paragraph outline explaining your central argument, evidence sources and why this topic demands attention now. Include two links to previous published work.', 'gnn' ),
								),
								array(
									'question' => __( 'How long does editorial review take?', 'gnn' ),
									'answer'   => __( 'Our department editors review pitches weekly and respond within ten working days if the proposal fits our upcoming commissioning plan.', 'gnn' ),
								),
								array(
									'question' => __( 'What rights does the magazine acquire?', 'gnn' ),
									'answer'   => __( 'We purchase first serial rights for print and digital editions. All copyright remains with the contributing author, who may anthologise after three months.', 'gnn' ),
								),
								array(
									'question' => __( 'Do you consider photography and illustration portfolios?', 'gnn' ),
									'answer'   => __( 'Yes. Art and design directors review visual portfolios submitted as compact PDF attachments or web links on a rolling monthly basis.', 'gnn' ),
								),
							),
						),
					),
				),
			),
		),
		'menu'        => array( 'home', 'articles', 'departments', 'about', 'subscribe', 'contact' ),
		'content'     => array(
			'projects' => array(
				array(
					'title'    => __( 'The Future of the European Commons', 'gnn' ),
					'excerpt'  => __( 'A six-part investigation into urban transit, municipal forests and shared digital public goods.', 'gnn' ),
					'category' => __( 'Public Ideas', 'gnn' ),
					'year'     => '2026',
					'media'    => 'dossier-1',
				),
				array(
					'title'    => __( 'Concrete & Light: Modernist Sanctuary', 'gnn' ),
					'excerpt'  => __( 'An architectural appraisal of mid-century civic structures and contemporary restoration.', 'gnn' ),
					'category' => __( 'Architecture', 'gnn' ),
					'year'     => '2026',
					'media'    => 'dossier-2',
				),
				array(
					'title'    => __( 'Algorithms, Authorship and the Literary Essay', 'gnn' ),
					'excerpt'  => __( 'How computational text systems are reshaping editorial craft, style and literary criticism.', 'gnn' ),
					'category' => __( 'Literature', 'gnn' ),
					'year'     => '2026',
					'media'    => 'dossier-3',
				),
				array(
					'title'    => __( 'Port Cities of the Eastern Mediterranean', 'gnn' ),
					'excerpt'  => __( 'Maritime trade, diaspora literature and harbor transformations across centuries.', 'gnn' ),
					'category' => __( 'Global Letters', 'gnn' ),
					'year'     => '2025',
					'media'    => 'dossier-4',
				),
				array(
					'title'    => __( 'Quiet Landscapes of Northern Typography', 'gnn' ),
					'excerpt'  => __( 'Typefounding traditions, specimen books and the preservation of metal letterforms.', 'gnn' ),
					'category' => __( 'Design', 'gnn' ),
					'year'     => '2025',
					'media'    => 'dossier-5',
				),
			),
			'posts'    => array(
				array(
					'title'   => __( 'Why longform criticism still matters in an era of instant reaction', 'gnn' ),
					'excerpt' => __( 'Patient reading allows ideas to develop nuance, challenge intuition and resist superficial consensus.', 'gnn' ),
					'media'   => 'news-1',
				),
				array(
					'title'   => __( 'The disappearing public bench: Notes on hostile architecture', 'gnn' ),
					'excerpt' => __( 'How urban design choices quietly discourage gathering and erode the accessibility of civic space.', 'gnn' ),
					'media'   => 'news-2',
				),
				array(
					'title'   => __( 'Collecting photobooks as historical documents', 'gnn' ),
					'excerpt' => __( 'Examining how sequencing, paper texture and typography create visual narratives that outlive single images.', 'gnn' ),
					'media'   => 'news-3',
				),
			),
		),
		'media'       => array(
			'hero'      => array(
				'alt'     => __( 'Monochromatic architectural facade with striking morning light and shadows', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Striking architectural facade with sharp diagonal shadows, geometry and clean lines in morning sunlight; monochromatic and rich ink tones; editorial magazine cover composition, aspect ratio 16:9',
			),
			'about'     => array(
				'alt'     => __( 'Open print magazine on a dark wooden library desk with fountain pen', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Printed cultural review journal open on a dark walnut table with a notebook and reading lamp; warm paper, dark ink and subtle crimson bookmark; literary still life, aspect ratio 4:3',
			),
			'dossier-1' => array(
				'alt'     => __( 'People walking through a European civic square surrounded by classical architecture', 'gnn' ),
				'license' => 'original',
				'brief'   => 'European urban public square with pedestrians walking between historic stone colonnades and modern pavings; natural daylight, architectural documentary style, aspect ratio 4:3',
			),
			'dossier-2' => array(
				'alt'     => __( 'Board-marked concrete interior illuminated by a dramatic beam of natural light', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Board-formed concrete interior of a modernist civic building with a sharp shaft of sunlight across textured grey wall; brutalist architectural photography, aspect ratio 4:3',
			),
			'dossier-3' => array(
				'alt'     => __( 'Antique metal typesetting blocks arranged in a printmaker tray with black ink', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Vintage lead movable type letterforms arranged in a wooden compositor tray with rich black ink sheen; macro typography craft composition, aspect ratio 4:3',
			),
			'dossier-4' => array(
				'alt'     => __( 'Historical port harbor with stone quays and cargo ships at dusk', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Mediterranean harbor with historic stone breakwater, cargo vessel silhouettes and twilight water reflections; atmospheric travel documentary, aspect ratio 4:3',
			),
			'dossier-5' => array(
				'alt'     => __( 'Printed typography specimen sheets and book covers pinned to a studio board', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Black and white typographical specimen sheets and grid drawings neatly pinned to a cork studio wall; graphic design editorial composition, aspect ratio 4:3',
			),
			'news-1'    => array(
				'alt'     => __( 'Stack of printed quarterly journals on a wooden reading room table', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Stack of minimalist printed literary magazines with visible spine stitching on an oak table; rich paper texture and deep ink typography, aspect ratio 16:9',
			),
			'news-2'    => array(
				'alt'     => __( 'Lone wooden park bench in an urban square in quiet morning mist', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Solitary timber bench in a public urban park surrounded by paving stones and soft morning mist; thoughtful civic essay concept, aspect ratio 16:9',
			),
			'news-3'    => array(
				'alt'     => __( 'Photographic print archive laid out with linen gloves on a viewing surface', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Black and white photographic prints carefully laid out on a gallery lightbox with white cotton curatorial gloves; visual arts documentation, aspect ratio 16:9',
			),
		),
		'css'         => 'magazine',
	);
}
add_filter(
	'gnn_template_registry',
	function ( $templates ) {
		$templates['magazine'] = 'gnn_template_data_magazine';
		return $templates;
	}
);
