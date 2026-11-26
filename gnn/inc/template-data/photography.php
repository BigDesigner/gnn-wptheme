<?php
/**
 * GNNlens ready-made site template data definition.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GNNlens: visual-first, curated gallery and package-focused photography studio template.
 *
 * @return array Template configuration data.
 */
function gnn_template_data_photography() {
	return array(
		'slug'        => 'photography',
		'label'       => __( 'GNNlens', 'gnn' ),
		'description' => __( 'A visual-first photography website with curated image galleries, session packages, artist statement and production booking.', 'gnn' ),
		'requires'    => array( 'elementor' ),
		'tokens'      => array(
			'accent'       => '#d1a874',
			'accent_light' => '#a37a44',
			'accent_ink'   => '#0c0c0e',
			'radius'       => 4,
			'font'         => 'manrope',
			'heading_font' => 'space',
			'mode'         => 'dark',
			'light'        => array( '#fafaf9', '#f5f5f4', '#e7e5e4', '#18181b', '#71717a', '#d4d4d8' ),
			'dark'         => array( '#0c0c0e', '#141418', '#1c1c22', '#f4f4f6', '#9a9aa4', '#26262e' ),
		),
		'header'      => array(
			'preset'    => 'minimal',
			'cta_label' => __( 'Book a session', 'gnn' ),
			'options'   => array(
				'header_structure'  => 'classic',
				'header_surface'    => 'solid',
				'header_shape'      => 'full',
				'header_width'      => 'wide',
				'header_behavior'   => 'sticky',
				'header_overlay'    => 0,
				'topbar_enable'     => 0,
				'header_height'     => 72,
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
						'centered',
						array(
							'eyebrow'         => __( 'Photographic studio', 'gnn' ),
							'title'           => __( 'Capturing atmosphere, form and unscripted light.', 'gnn' ),
							'text'            => __( 'An independent visual practice collaborating with architects, designers and editorial publications on assignments worldwide.', 'gnn' ),
							'button_label'    => __( 'Explore portfolio', 'gnn' ),
							'button_url'      => 'page:gallery',
							'secondary_label' => __( 'Session packages', 'gnn' ),
							'secondary_url'   => 'page:packages',
						),
					),
					array(
						'gallery',
						'cols-3',
						array(
							'eyebrow' => __( 'Curated work', 'gnn' ),
							'title'   => __( 'Selected frames and series', 'gnn' ),
							'text'    => __( 'Excerpts from architectural studies, editorial portraiture and fine art monographs.', 'gnn' ),
						),
					),
					array(
						'services',
						'lined',
						array(
							'eyebrow'      => __( 'Disciplines', 'gnn' ),
							'title'        => __( 'Specialized photographic commissions', 'gnn' ),
							'text'         => __( 'Available for on-location architectural documentation, commercial brand campaigns and portrait sessions.', 'gnn' ),
							'button_label' => __( 'View all packages', 'gnn' ),
							'button_url'   => 'page:packages',
							'items'        => array(
								array(
									__( 'Architectural documentation', 'gnn' ),
									__( 'Documenting built structures, interior environments and spatial light with geometric discipline.', 'gnn' ),
								),
								array(
									__( 'Editorial & portraiture', 'gnn' ),
									__( 'Characterful portraits for monographs, editorial publications and artist monographs.', 'gnn' ),
								),
								array(
									__( 'Commercial campaigns', 'gnn' ),
									__( 'Visual narratives, lookbooks and product stories crafted for design and lifestyle brands.', 'gnn' ),
								),
							),
						),
					),
					array(
						'about-split',
						'image-left',
						array(
							'eyebrow'      => __( 'Studio philosophy', 'gnn' ),
							'title'        => __( 'Natural light and disciplined composition', 'gnn' ),
							'text'         => __( 'Working across medium-format digital and archival film stocks, we prioritize authentic textures, tonal subtlety and lasting imagery over fleeting trends.', 'gnn' ),
							'button_label' => __( 'About the artist', 'gnn' ),
							'button_url'   => 'page:about',
							'image'        => 'media:about',
							'items'        => array(
								__( 'Available for international travel and on-location production', 'gnn' ),
								__( 'End-to-end creative direction, location scouting and curation', 'gnn' ),
								__( 'Museum-grade archival prints and commercial digital delivery', 'gnn' ),
							),
						),
					),
					array(
						'pricing',
						'three',
						array(
							'eyebrow'      => __( 'Session tiers', 'gnn' ),
							'title'        => __( 'Production and commission packages', 'gnn' ),
							'text'         => __( 'Clear baseline rates for portrait sittings, editorial assignments and full commercial productions.', 'gnn' ),
							'button_label' => __( 'Inquire for dates', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title'    => __( 'Studio portrait session', 'gnn' ),
									'text'     => __( 'A focused two-hour session for artists, founders and editorial features.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Per session', 'gnn' ),
									'featured' => false,
									'features' => array(
										__( 'Two-hour studio or location shoot', 'gnn' ),
										__( 'Online contact sheet and proofs', 'gnn' ),
										__( 'Ten master retouched high-res plates', 'gnn' ),
									),
								),
								array(
									'title'    => __( 'Editorial commission day', 'gnn' ),
									'text'     => __( 'Full-day on-location production for architectural series and publications.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Per day', 'gnn' ),
									'featured' => true,
									'features' => array(
										__( 'Full-day on-location production', 'gnn' ),
										__( 'Creative direction and shot planning', 'gnn' ),
										__( 'Complete curated master plate delivery', 'gnn' ),
									),
								),
								array(
									'title'    => __( 'Commercial campaign', 'gnn' ),
									'text'     => __( 'Multi-day brand campaign including scouting, talent and licensing.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Per project', 'gnn' ),
									'featured' => false,
									'features' => array(
										__( 'Multi-day shoot schedule', 'gnn' ),
										__( 'Global digital and print licensing rights', 'gnn' ),
										__( 'Priority post-production delivery', 'gnn' ),
									),
								),
							),
						),
					),
					array(
						'testimonial',
						'featured',
						array(
							'eyebrow' => __( 'Client note', 'gnn' ),
							'title'   => __( 'Words from creative directors', 'gnn' ),
							'text'    => __( 'Replace this placeholder with verified feedback from past editorial or commercial clients.', 'gnn' ),
							'items'   => array(
								array(
									'quote'  => __( 'Add a customer quote here.', 'gnn' ),
									'author' => __( 'Name Surname', 'gnn' ),
									'role'   => __( 'Editorial creative director', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'band',
						array(
							'eyebrow'      => __( 'Inquiries', 'gnn' ),
							'title'        => __( 'Planning an upcoming exhibition or commission?', 'gnn' ),
							'text'         => __( 'Reach out with your project dates, location requirements and production scope.', 'gnn' ),
							'button_label' => __( 'Check availability', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'gallery'  => array(
				'title'    => __( 'Gallery', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Portfolio archive', 'gnn' ),
							'title'        => __( 'Selected monographs, series and editorial frames.', 'gnn' ),
							'text'         => __( 'Browse documented assignments across architectural monographs, portrait studies and brand campaigns.', 'gnn' ),
							'button_label' => __( 'Commission a shoot', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'gallery',
						'cols-4',
						array(
							'eyebrow' => __( 'Plate collection', 'gnn' ),
							'title'   => __( 'Selected frames archive', 'gnn' ),
							'text'    => __( 'High-resolution excerpts from ongoing assignments and studio explorations.', 'gnn' ),
						),
					),
					array(
						'portfolio',
						'cols-2',
						array(
							'eyebrow'      => __( 'Documented series', 'gnn' ),
							'title'        => __( 'Extended monograph bodies of work', 'gnn' ),
							'text'         => __( 'Comprehensive visual narratives developed across seasons and international travels.', 'gnn' ),
							'button_label' => __( 'Explore session rates', 'gnn' ),
							'button_url'   => 'page:packages',
						),
					),
					array(
						'cta',
						'band',
						array(
							'eyebrow'      => __( 'Limited editions', 'gnn' ),
							'title'        => __( 'Archival museum-grade prints available on request', 'gnn' ),
							'text'         => __( 'Selected plates are produced in limited, hand-numbered fine art editions.', 'gnn' ),
							'button_label' => __( 'Inquire about prints', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'packages' => array(
				'title'    => __( 'Packages', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Production & licensing', 'gnn' ),
							'title'        => __( 'Transparent session formats tailored to your brief.', 'gnn' ),
							'text'         => __( 'Every production includes pre-shoot consultation, creative direction, calibrated lighting and archival delivery.', 'gnn' ),
							'button_label' => __( 'Request a bespoke quote', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'pricing',
						'three',
						array(
							'eyebrow'      => __( 'Session formats', 'gnn' ),
							'title'        => __( 'Commission tiers and licensing options', 'gnn' ),
							'text'         => __( 'Choose a standard format below or get in touch for custom production requirements.', 'gnn' ),
							'button_label' => __( 'Reserve this session', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title'    => __( 'Portrait sitting', 'gnn' ),
									'text'     => __( 'Two-hour sitting for artists, founders and editorial profiles.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Per sitting', 'gnn' ),
									'featured' => false,
									'features' => array(
										__( 'Studio or selected location', 'gnn' ),
										__( 'Online contact sheet review', 'gnn' ),
										__( 'Ten retouched high-resolution plates', 'gnn' ),
									),
								),
								array(
									'title'    => __( 'Editorial day rate', 'gnn' ),
									'text'     => __( 'Full day on-location production for publishers and architects.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Per day', 'gnn' ),
									'featured' => true,
									'features' => array(
										__( 'Full day on-location shoot', 'gnn' ),
										__( 'Lighting and digital technician included', 'gnn' ),
										__( 'Curated master archive delivery', 'gnn' ),
									),
								),
								array(
									'title'    => __( 'Commercial campaign', 'gnn' ),
									'text'     => __( 'Comprehensive multi-day production for brand campaigns.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Per project', 'gnn' ),
									'featured' => false,
									'features' => array(
										__( 'Multi-day production schedule', 'gnn' ),
										__( 'Comprehensive licensing rights', 'gnn' ),
										__( 'Retouching and color grading', 'gnn' ),
									),
								),
							),
						),
					),
					array(
						'process',
						'timeline',
						array(
							'eyebrow' => __( 'The production path', 'gnn' ),
							'title'   => __( 'From moodboard alignment to archival delivery', 'gnn' ),
							'text'    => __( 'A calm, disciplined process ensuring creative clarity and prompt turnaround.', 'gnn' ),
							'items'   => array(
								array(
									'title' => __( 'Brief & scouting', 'gnn' ),
									'text'  => __( 'Reviewing visual references, scouting lighting conditions and agreeing the shot list.', 'gnn' ),
								),
								array(
									'title' => __( 'Production shoot', 'gnn' ),
									'text'  => __( 'Executing the shoot with disciplined composition and calibrated on-set monitoring.', 'gnn' ),
								),
								array(
									'title' => __( 'Curation & proofing', 'gnn' ),
									'text'  => __( 'Delivering digital contact sheets for collaborative selection and feedback.', 'gnn' ),
								),
								array(
									'title' => __( 'Master delivery', 'gnn' ),
									'text'  => __( 'Final color grading, high-res export and secure cloud archive delivery.', 'gnn' ),
								),
							),
						),
					),
					array(
						'faq',
						'single',
						array(
							'eyebrow' => __( 'Production FAQ', 'gnn' ),
							'title'   => __( 'Common questions regarding sessions and licensing', 'gnn' ),
							'text'    => __( 'Key details on travel logistics, delivery timelines and image rights.', 'gnn' ),
							'items'   => array(
								array(
									'question' => __( 'How far in advance should we secure shoot dates?', 'gnn' ),
									'answer'   => __( 'Editorial and studio bookings typically book four to six weeks in advance, while international travel requires eight weeks notice.', 'gnn' ),
								),
								array(
									'question' => __( 'What usage rights are included with delivered images?', 'gnn' ),
									'answer'   => __( 'Standard commissions include digital editorial rights and PR usage. Commercial advertising rights are tailored per campaign scope.', 'gnn' ),
								),
								array(
									'question' => __( 'What is your weather contingency for on-location shoots?', 'gnn' ),
									'answer'   => __( 'Outdoor architectural and landscape sessions include a built-in weather backup date agreed during pre-production.', 'gnn' ),
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
						'split',
						array(
							'eyebrow'      => __( 'About the artist', 'gnn' ),
							'title'        => __( 'Documenting spaces, people and quiet moments.', 'gnn' ),
							'text'         => __( 'With over fifteen years behind the lens, our studio brings an architectural eye to spatial documentation, portraiture and narrative series.', 'gnn' ),
							'button_label' => __( 'Explore the gallery', 'gnn' ),
							'button_url'   => 'page:gallery',
							'image'        => 'media:about',
						),
					),
					array(
						'values',
						'minimal',
						array(
							'eyebrow' => __( 'Artistic values', 'gnn' ),
							'title'   => __( 'Three standards behind every exposure', 'gnn' ),
							'text'    => __( 'How we approach light, subject rapport and print preservation.', 'gnn' ),
							'items'   => array(
								array(
									'title' => __( 'Authentic illumination', 'gnn' ),
									'text'  => __( 'We prioritize natural sunlight and balanced practical lighting over synthetic effects.', 'gnn' ),
								),
								array(
									'title' => __( 'Patient composition', 'gnn' ),
									'text'  => __( 'We wait for the decisive balance between geometry, shadow and expression.', 'gnn' ),
								),
								array(
									'title' => __( 'Archival permanence', 'gnn' ),
									'text'  => __( 'Every master print and digital negative is prepared with longevity and preservation in mind.', 'gnn' ),
								),
							),
						),
					),
					array(
						'logos',
						'strip',
						array(
							'eyebrow' => __( 'Selected publications & clients', 'gnn' ),
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
							'eyebrow'      => __( 'Darkroom visits', 'gnn' ),
							'title'        => __( 'Visit the studio and review print archives', 'gnn' ),
							'text'         => __( 'We welcome editors, architects and art buyers to view physical contact sheets and fine art monographs in person.', 'gnn' ),
							'button_label' => __( 'Schedule a visit', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'journal'  => array(
				'title'    => __( 'Journal', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Field dispatches', 'gnn' ),
							'title'        => __( 'Notes on photographic craft, film stocks and travels.', 'gnn' ),
							'text'         => __( 'Behind-the-scenes production essays, camera reflections and location monographs.', 'gnn' ),
							'button_label' => __( 'View portfolio', 'gnn' ),
							'button_url'   => 'page:gallery',
						),
					),
					array(
						'blog',
						'list',
						array(
							'eyebrow' => __( 'Recent dispatches', 'gnn' ),
							'title'   => __( 'Notes from the field', 'gnn' ),
							'text'    => __( 'Read recent articles documenting shoot productions, lighting experiments and travel journals.', 'gnn' ),
						),
					),
					array(
						'newsletter',
						'centered',
						array(
							'eyebrow'      => __( 'Monograph editions', 'gnn' ),
							'title'        => __( 'Receive notices for new monograph publications', 'gnn' ),
							'text'         => __( 'A quiet update sent twice annually when new fine art print releases or monographs become available.', 'gnn' ),
							'button_label' => __( 'Join the collector list', 'gnn' ),
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
							'eyebrow'      => __( 'Inquiries & booking', 'gnn' ),
							'title'        => __( 'Let’s discuss your shoot or project.', 'gnn' ),
							'text'         => __( 'Share your target dates, location requirements and project vision with our studio.', 'gnn' ),
							'button_label' => __( 'View session packages', 'gnn' ),
							'button_url'   => 'page:packages',
						),
					),
					array(
						'contact',
						'cards',
						array(
							'eyebrow'    => __( 'Studio channels', 'gnn' ),
							'title'      => __( 'Direct communication channels', 'gnn' ),
							'text'       => __( 'Reach the studio directly for time-sensitive assignments or complete the booking form below.', 'gnn' ),
							'form_title' => __( 'Shoot inquiry form', 'gnn' ),
							'form_text'  => __( 'Add your contact form here with the form plugin you use.', 'gnn' ),
						),
					),
					array(
						'faq',
						'two-column',
						array(
							'eyebrow' => __( 'Booking details', 'gnn' ),
							'title'   => __( 'Helpful details before confirming', 'gnn' ),
							'text'    => __( 'Answers to common questions regarding location work and scheduling.', 'gnn' ),
							'items'   => array(
								array(
									'question' => __( 'Do you travel for on-location projects?', 'gnn' ),
									'answer'   => __( 'Yes. We frequently undertake commissions across Europe, North America and international locations with transparent travel budgets.', 'gnn' ),
								),
								array(
									'question' => __( 'How are image proofs delivered after the shoot?', 'gnn' ),
									'answer'   => __( 'A private, password-protected digital contact sheet is provided within five business days of shooting.', 'gnn' ),
								),
								array(
									'question' => __( 'Can clients purchase raw or unedited files?', 'gnn' ),
									'answer'   => __( 'To maintain high craft standards, we only deliver curated, master-retouched plates in high-resolution formats.', 'gnn' ),
								),
								array(
									'question' => __( 'Are physical fine art prints available?', 'gnn' ),
									'answer'   => __( 'Yes. Selected works can be ordered as museum-grade rag prints with certificate of authenticity.', 'gnn' ),
								),
							),
						),
					),
				),
			),
		),
		'menu'        => array( 'home', 'gallery', 'packages', 'about', 'journal', 'contact' ),
		'content'     => array(
			'projects' => array(
				array(
					'title'    => __( 'Nocturne architectural concrete monograph', 'gnn' ),
					'excerpt'  => __( 'A monochrome visual study exploring cast concrete geometry, dusk shadows and linear skylights in Alpine modernism.', 'gnn' ),
					'category' => __( 'Architecture', 'gnn' ),
					'year'     => '2026',
					'media'    => 'photo-1',
				),
				array(
					'title'    => __( 'Nordic natural light editorial portraits', 'gnn' ),
					'excerpt'  => __( 'An intimate portrait series captured on medium-format film documenting artists and ceramists in their workspaces.', 'gnn' ),
					'category' => __( 'Portraiture', 'gnn' ),
					'year'     => '2026',
					'media'    => 'photo-2',
				),
				array(
					'title'    => __( 'Highland solitude landscape study', 'gnn' ),
					'excerpt'  => __( 'Atmospheric dawn plates documenting misty mountain ridges, wind-swept moorlands and quiet horizon lines.', 'gnn' ),
					'category' => __( 'Landscape', 'gnn' ),
					'year'     => '2025',
					'media'    => 'gallery-1',
				),
				array(
					'title'    => __( 'Kinetic form contemporary dance study', 'gnn' ),
					'excerpt'  => __( 'High-speed studio exposures capturing dancers in mid-motion with subtle directional rim lighting.', 'gnn' ),
					'category' => __( 'Fine art', 'gnn' ),
					'year'     => '2025',
					'media'    => 'gallery-2',
				),
				array(
					'title'    => __( 'Tactile artisan craft documentation', 'gnn' ),
					'excerpt'  => __( 'Macro textural study exploring hand-loomed textiles, forged metals and raw woodworking tools in natural atelier light.', 'gnn' ),
					'category' => __( 'Editorial', 'gnn' ),
					'year'     => '2025',
					'media'    => 'gallery-3',
				),
			),
			'posts'    => array(
				array(
					'title'   => __( 'Why medium-format depth changes spatial portraiture', 'gnn' ),
					'excerpt' => __( 'Reflections on lens compression, sensor area and how optical gradation shapes the emotional resonance of a photograph.', 'gnn' ),
					'media'   => 'news-1',
				),
				array(
					'title'   => __( 'Working with shifting natural sunlight on architectural shoots', 'gnn' ),
					'excerpt' => __( 'How to plan shoot schedules around solar angles, cloud cover and reflective surfaces for truthful spatial documentation.', 'gnn' ),
					'media'   => 'news-2',
				),
				array(
					'title'   => __( 'Archival considerations when ordering collector prints', 'gnn' ),
					'excerpt' => __( 'An overview of rag cotton papers, pigment permanence ratings and handling protocols for museum-grade framing.', 'gnn' ),
					'media'   => 'news-3',
				),
			),
		),
		'media'       => array(
			'hero'      => array(
				'alt'     => __( 'Minimal architectural photograph with clean lines, soft shadow and natural light', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Dramatic minimalist architectural facade photograph with sharp diagonal shadows, warm concrete texture and deep obsidian shadows (#0c0c0e) with warm champagne highlights (#d1a874), 16:9 aspect ratio',
			),
			'about'     => array(
				'alt'     => __( 'Photographer with a medium format camera reviewing contact sheets', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Close-up of hands holding an illuminated loupe over black-and-white film contact sheets on a dark wooden studio table, 4:3 aspect ratio',
			),
			'gallery-1' => array(
				'alt'     => __( 'Minimalist mountain ridge shrouded in morning mist', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Misty atmospheric landscape plate with layered mountain silhouettes in muted slate and soft amber dawn light, 4:3 aspect ratio',
			),
			'gallery-2' => array(
				'alt'     => __( 'Contemporary dance artist in dynamic motion with directional rim light', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Monochrome fine art dance capture in motion blur with high-contrast rim lighting against a deep dark charcoal backdrop, 4:3 aspect ratio',
			),
			'gallery-3' => array(
				'alt'     => __( 'Artisan ceramic vase and linen texture still life', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Still life of handcrafted dark stoneware ceramic bowl on raw textured linen with soft raking window light, 4:3 aspect ratio',
			),
			'gallery-4' => array(
				'alt'     => __( 'Modernist concrete staircase with geometric shadows', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Abstract architectural perspective of spiral concrete stairs bathed in warm golden afternoon light, 4:3 aspect ratio',
			),
			'photo-1'   => array(
				'alt'     => __( 'Monograph plate of brutalist architecture with deep contrast', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Architectural study of brutalist geometric concrete overhangs against a dark dusk sky, 16:10 aspect ratio',
			),
			'photo-2'   => array(
				'alt'     => __( 'Natural light portrait of an artist in an atelier', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Editorial character portrait in natural north-facing window light with subtle shadows and warm skin tones, 16:10 aspect ratio',
			),
			'news-1'    => array(
				'alt'     => __( 'Medium format camera lens and aperture ring close up', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Close-up detail of a mechanical manual focus camera lens on a dark velvet surface with warm champagne reflections, 16:9 aspect ratio',
			),
			'news-2'    => array(
				'alt'     => __( 'Sunlight beam casting diagonal shadow on an interior wall', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Quiet architectural interior study showing golden sunlight filtering through an open doorway onto warm plaster, 16:9 aspect ratio',
			),
			'news-3'    => array(
				'alt'     => __( 'Cotton rag archival print in a black wood gallery frame', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Framed fine art print resting against a dark charcoal gallery wall with museum spotlighting, 16:9 aspect ratio',
			),
		),
		'css'         => 'photography',
	);
}

add_filter(
	'gnn_template_registry',
	function ( $templates ) {
		$templates['photography'] = 'gnn_template_data_photography';
		return $templates;
	}
);
