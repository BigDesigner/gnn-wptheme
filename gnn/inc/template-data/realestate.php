<?php
/**
 * GNNestate ready-made site template data.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GNNestate: property listings, neighbourhood guides and brokerage advisory template.
 *
 * @return array Template configuration and sample data.
 */
function gnn_template_data_realestate() {
	return array(
		'slug'        => 'realestate',
		'label'       => __( 'GNNestate', 'gnn' ),
		'description' => __( 'Property listings, neighbourhood guides and advisory pages for real estate agencies and brokerages.', 'gnn' ),
		'requires'    => array( 'elementor' ),
		'tokens'      => array(
			'accent'       => '#9e5a3c',
			'accent_light' => '#7c4329',
			'accent_ink'   => '#ffffff',
			'radius'       => 6,
			'font'         => 'manrope',
			'heading_font' => 'space',
			'mode'         => 'light',
			'light'        => array( '#faf8f5', '#f2ede6', '#e6dfd5', '#1c1917', '#5f5850', '#ddd5c9' ),
			'dark'         => array( '#141210', '#1d1a17', '#282420', '#f5f2ed', '#a8a096', '#332e29' ),
		),
		'header'      => array(
			'preset'    => 'centered',
			'cta_label' => __( 'Book a viewing', 'gnn' ),
			'options'   => array(
				'header_structure'  => 'centered',
				'header_surface'    => 'solid',
				'header_shape'      => 'full',
				'header_width'      => 'contained',
				'header_behavior'   => 'sticky',
				'header_overlay'    => 0,
				'topbar_enable'     => 1,
				'header_height'     => 80,
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
							'eyebrow'      => __( 'Residential & commercial brokerage', 'gnn' ),
							'title'        => __( 'Considered spaces for living and working.', 'gnn' ),
							'text'         => __( 'Curated property listings, transparent market valuations and private viewings guided by local specialists.', 'gnn' ),
							'button_label' => __( 'Browse listings', 'gnn' ),
							'button_url'   => 'page:listings',
							'image'        => 'media:hero',
							'items'        => array(
								array(
									'title' => __( 'Residential sales', 'gnn' ),
									'text'  => __( 'Townhouses, apartments and family homes presented with full floor plans and light studies.', 'gnn' ),
								),
								array(
									'title' => __( 'Commercial leasing', 'gnn' ),
									'text'  => __( 'Street-front retail suites, studios and workspace floors matched to your operational brief.', 'gnn' ),
								),
								array(
									'title' => __( 'Property valuation', 'gnn' ),
									'text'  => __( 'Evidence-based appraisals grounded in comparable sales, building fabric and plot potential.', 'gnn' ),
								),
							),
						),
					),
					array(
						'portfolio',
						'filtered',
						array(
							'eyebrow'      => __( 'Current portfolio', 'gnn' ),
							'title'        => __( 'Featured property listings', 'gnn' ),
							'text'         => __( 'Filter by property category to explore residences and workspaces currently available for viewing.', 'gnn' ),
							'button_label' => __( 'All listings', 'gnn' ),
							'button_url'   => 'page:listings',
						),
					),
					array(
						'features',
						'split-image',
						array(
							'eyebrow'      => __( 'Advisory standards', 'gnn' ),
							'title'        => __( 'Every listing verified in detail before it goes live', 'gnn' ),
							'text'         => __( 'We prepare complete technical packs so buyers, tenants and owners can evaluate each property with clarity.', 'gnn' ),
							'button_label' => __( 'Our services', 'gnn' ),
							'button_url'   => 'page:services',
							'image'        => 'media:interior',
							'items'        => array(
								array(
									'title' => __( 'Measured floor plans', 'gnn' ),
									'text'  => __( 'Accurate room dimensions, orientation notes and storage layouts included with every brief.', 'gnn' ),
								),
								array(
									'title' => __( 'Building documentation', 'gnn' ),
									'text'  => __( 'Clear summaries of maintenance history, service charges and zoning permissions.', 'gnn' ),
								),
								array(
									'title' => __( 'Private appointments', 'gnn' ),
									'text'  => __( 'Unhurried individual viewings scheduled around natural daylight and your calendar.', 'gnn' ),
								),
								array(
									'title' => __( 'Direct negotiation', 'gnn' ),
									'text'  => __( 'One senior advisor manages offers, heads of terms and legal coordination through to handover.', 'gnn' ),
								),
							),
						),
					),
					array(
						'about-split',
						'numbered',
						array(
							'eyebrow'      => __( 'Local insight', 'gnn' ),
							'title'        => __( 'Grounded in architecture and neighbourhood character', 'gnn' ),
							'text'         => __( 'Choosing a property goes beyond square metres. We advise on street character, transport links, renovation scope and long-term resale resilience.', 'gnn' ),
							'button_label' => __( 'About the agency', 'gnn' ),
							'button_url'   => 'page:about',
							'image'        => 'media:agency',
							'items'        => array(
								__( 'Independent representation for sellers, landlords and private buyers', 'gnn' ),
								__( 'Editorial photography and architectural copywriting produced in-house', 'gnn' ),
								__( 'Discreet off-market placement when privacy is preferred', 'gnn' ),
							),
						),
					),
					array(
						'faq',
						'two-column',
						array(
							'eyebrow' => __( 'Common questions', 'gnn' ),
							'title'   => __( 'Buying, selling and leasing guidance', 'gnn' ),
							'text'    => __( 'Practical answers on how viewings, appraisals and representation engagements work.', 'gnn' ),
							'items'   => array(
								array(
									'question' => __( 'How do I book a private viewing?', 'gnn' ),
									'answer'   => __( 'Use the contact page to share the listing name and your preferred times. An advisor will confirm your appointment directly.', 'gnn' ),
								),
								array(
									'question' => __( 'What is included in a property valuation?', 'gnn' ),
									'answer'   => __( 'We inspect the property in person, review comparable transactions and provide a written pricing and marketing recommendation.', 'gnn' ),
								),
								array(
									'question' => __( 'Do you handle off-market transactions?', 'gnn' ),
									'answer'   => __( 'Yes. Where owners prefer discretion, we introduce qualified buyers from our registered search list before public release.', 'gnn' ),
								),
								array(
									'question' => __( 'Can you assist with buyer searches?', 'gnn' ),
									'answer'   => __( 'We take a detailed brief on layout, neighbourhood and budget, then shortlist suitable properties and represent you during negotiations.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'split',
						array(
							'eyebrow'      => __( 'Private consultation', 'gnn' ),
							'title'        => __( 'Planning a sale, lease or acquisition?', 'gnn' ),
							'text'         => __( 'Speak with our advisory team to arrange a confidential appraisal or register your property search criteria.', 'gnn' ),
							'button_label' => __( 'Book a viewing', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'listings' => array(
				'title'    => __( 'Listings', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Property portfolio', 'gnn' ),
							'title'        => __( 'Residences, studios and commercial spaces.', 'gnn' ),
							'text'         => __( 'Browse available properties by category or contact us to register for upcoming and off-market instructions.', 'gnn' ),
							'button_label' => __( 'Register your search', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'portfolio',
						'filtered',
						array(
							'eyebrow' => __( 'Directory', 'gnn' ),
							'title'   => __( 'Current instructions', 'gnn' ),
							'text'    => __( 'Select a category below to filter residential homes, apartments and commercial premises.', 'gnn' ),
						),
					),
					array(
						'process',
						'horizontal',
						array(
							'eyebrow' => __( 'How it works', 'gnn' ),
							'title'   => __( 'From first enquiry to key handover', 'gnn' ),
							'text'    => __( 'A structured four-step process keeps viewings, offers and completion timelines clear for every party.', 'gnn' ),
							'items'   => array(
								array(
									'title' => __( 'Brief & shortlist', 'gnn' ),
									'text'  => __( 'We align on location, floor plan requirements and timeline before curating relevant listings.', 'gnn' ),
								),
								array(
									'title' => __( 'Private viewings', 'gnn' ),
									'text'  => __( 'Walk through shortlisted spaces with full architectural notes and building disclosures.', 'gnn' ),
								),
								array(
									'title' => __( 'Offer & terms', 'gnn' ),
									'text'  => __( 'We structure clear heads of terms and coordinate directly with surveyors and legal counsel.', 'gnn' ),
								),
								array(
									'title' => __( 'Completion', 'gnn' ),
									'text'  => __( 'Final inspection, meter and inventory records, and smooth handover on completion day.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'band',
						array(
							'eyebrow'      => __( 'Bespoke search', 'gnn' ),
							'title'        => __( 'Looking for a specific street or building type?', 'gnn' ),
							'text'         => __( 'Share your requirements with our team to receive early notice of new instructions.', 'gnn' ),
							'button_label' => __( 'Contact an advisor', 'gnn' ),
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
							'eyebrow'      => __( 'Brokerage & advisory', 'gnn' ),
							'title'        => __( 'Tailored representation for owners and buyers.', 'gnn' ),
							'text'         => __( 'From pre-sale preparation and valuation to tenant selection and acquisition searches.', 'gnn' ),
							'button_label' => __( 'Request an appraisal', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'services',
						'cards-4',
						array(
							'eyebrow'      => __( 'Core capabilities', 'gnn' ),
							'title'        => __( 'How we support your property decisions', 'gnn' ),
							'text'         => __( 'Select an individual advisory service or appoint us to manage your portfolio end to end.', 'gnn' ),
							'button_label' => __( 'Enquire', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title' => __( 'Residential sales', 'gnn' ),
									'text'  => __( 'Pricing strategy, architectural photography, buyer qualification and negotiation through to closing.', 'gnn' ),
								),
								array(
									'title' => __( 'Leasing & tenant placement', 'gnn' ),
									'text'  => __( 'Marketing, reference checks, lease coordination and inventory preparation for residential and commercial landlords.', 'gnn' ),
								),
								array(
									'title' => __( 'Valuation & appraisal', 'gnn' ),
									'text'  => __( 'Written market appraisals for sale planning, refinancing, portfolio reviews and development feasibility.', 'gnn' ),
								),
								array(
									'title' => __( 'Buyer acquisition search', 'gnn' ),
									'text'  => __( 'Dedicated search representation covering public listings, private introductions and due diligence coordination.', 'gnn' ),
								),
							),
						),
					),
					array(
						'process',
						'split-timeline',
						array(
							'eyebrow'      => __( 'Seller pathway', 'gnn' ),
							'title'        => __( 'How we prepare and market a property instruction', 'gnn' ),
							'text'         => __( 'Careful preparation before launch attracts qualified enquiries and protects transaction momentum.', 'gnn' ),
							'button_label' => __( 'Book a valuation', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title' => __( 'On-site inspection & appraisal', 'gnn' ),
									'text'  => __( 'We assess architectural features, natural light, condition and comparable sales to agree on positioning.', 'gnn' ),
								),
								array(
									'title' => __( 'Editorial production & floor plans', 'gnn' ),
									'text'  => __( 'Measured drawings, daylight photography and clear property dossiers are assembled for prospective buyers.', 'gnn' ),
								),
								array(
									'title' => __( 'Targeted launch & hosted viewings', 'gnn' ),
									'text'  => __( 'We introduce the property to registered buyers first, followed by accompanied private appointments.', 'gnn' ),
								),
								array(
									'title' => __( 'Offer qualification & closing', 'gnn' ),
									'text'  => __( 'We verify buyer funding, negotiate terms and coordinate with legal teams until completion.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'split',
						array(
							'eyebrow'      => __( 'Get started', 'gnn' ),
							'title'        => __( 'Discuss your property with our team', 'gnn' ),
							'text'         => __( 'We provide straightforward advice on timing, presentation and expected market interest.', 'gnn' ),
							'button_label' => __( 'Arrange a call', 'gnn' ),
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
						'minimal',
						array(
							'eyebrow'      => __( 'Our practice', 'gnn' ),
							'title'        => __( 'An independent brokerage built on architectural care.', 'gnn' ),
							'text'         => __( 'Replace this introduction with your agency background, the neighbourhoods you cover and your advisory philosophy.', 'gnn' ),
							'button_label' => __( 'Meet with us', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'about-split',
						'image-left',
						array(
							'eyebrow'      => __( 'Who we are', 'gnn' ),
							'title'        => __( 'Focused attention on a curated number of instructions', 'gnn' ),
							'text'         => __( 'By keeping our active portfolio selective, senior advisors stay personally involved in every viewing, valuation and negotiation.', 'gnn' ),
							'button_label' => __( 'View current listings', 'gnn' ),
							'button_url'   => 'page:listings',
							'image'        => 'media:agency',
							'items'        => array(
								__( 'Direct access to the advisor leading your instruction', 'gnn' ),
								__( 'Accurate technical details and honest market feedback', 'gnn' ),
								__( 'Long-term relationships with local architects, surveyors and solicitors', 'gnn' ),
							),
						),
					),
					array(
						'values',
						'minimal',
						array(
							'eyebrow' => __( 'Principles', 'gnn' ),
							'title'   => __( 'Standards that guide every transaction', 'gnn' ),
							'text'    => __( 'Clear communication and thorough preparation reduce friction for both sides of a move.', 'gnn' ),
							'items'   => array(
								array(
									'title' => __( 'Considered presentation', 'gnn' ),
									'text'  => __( 'We document spaces honestly with balanced photography, measured plans and clear specifications.', 'gnn' ),
								),
								array(
									'title' => __( 'Transparent advice', 'gnn' ),
									'text'  => __( 'Our valuations reflect real market evidence rather than inflated figures designed to win an instruction.', 'gnn' ),
								),
								array(
									'title' => __( 'Steady follow-through', 'gnn' ),
									'text'  => __( 'Once an offer is agreed, we track every survey and legal milestone through to exchange and handover.', 'gnn' ),
								),
							),
						),
					),
					array(
						'team',
						'three',
						array(
							'eyebrow' => __( 'Advisory team', 'gnn' ),
							'title'   => __( 'Specialists you will work with', 'gnn' ),
							'text'    => __( 'Replace these placeholder profiles with your licensed property advisors and their focus areas.', 'gnn' ),
							'image'   => 'media:advisor',
							'items'   => array(
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Managing partner & valuations', 'gnn' ),
									'bio'  => __( 'Add a short biography covering residential valuation and brokerage experience.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Head of residential sales', 'gnn' ),
									'bio'  => __( 'Add a short biography covering private client sales and buyer acquisitions.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Commercial & leasing lead', 'gnn' ),
									'bio'  => __( 'Add a short biography covering studio, retail and office leasing.', 'gnn' ),
								),
							),
						),
					),
				),
			),
			'news'     => array(
				'title'    => __( 'Market notes', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Journal & guides', 'gnn' ),
							'title'        => __( 'Neighbourhood notes and property guidance.', 'gnn' ),
							'text'         => __( 'Practical articles on preparing a property, evaluating floor plans and navigating sales or leases.', 'gnn' ),
							'button_label' => __( 'Browse listings', 'gnn' ),
							'button_url'   => 'page:listings',
						),
					),
					array(
						'blog',
						'grid',
						array(
							'eyebrow'      => __( 'Latest dispatches', 'gnn' ),
							'title'        => __( 'Recent articles from the practice', 'gnn' ),
							'text'         => __( 'Guides for homeowners, buyers and commercial tenants.', 'gnn' ),
							'button_label' => __( 'Contact our team', 'gnn' ),
							'button_url'   => 'page:contact',
							'image'        => 'media:news-1',
						),
					),
					array(
						'newsletter',
						'split',
						array(
							'eyebrow'      => __( 'Property alerts', 'gnn' ),
							'title'        => __( 'Receive new listings and periodic market notes', 'gnn' ),
							'text'         => __( 'Subscribe for occasional updates when new residences and studio spaces become available.', 'gnn' ),
							'button_label' => __( 'Register interest', 'gnn' ),
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
							'eyebrow'      => __( 'Enquiries & appointments', 'gnn' ),
							'title'        => __( 'Arrange a private viewing or valuation.', 'gnn' ),
							'text'         => __( 'Share the property you would like to view or tell us about the space you are preparing to sell or lease.', 'gnn' ),
							'button_label' => __( 'Explore listings', 'gnn' ),
							'button_url'   => 'page:listings',
						),
					),
					array(
						'contact',
						'cards',
						array(
							'eyebrow'    => __( 'Direct contact', 'gnn' ),
							'title'      => __( 'Speak with an advisor', 'gnn' ),
							'text'       => __( 'Reach our office directly or send your viewing request using the form below.', 'gnn' ),
							'form_title' => __( 'Viewing & valuation request', 'gnn' ),
							'form_text'  => __( 'Add your contact form here with the form plugin you use.', 'gnn' ),
						),
					),
					array(
						'faq',
						'single',
						array(
							'eyebrow' => __( 'Before your visit', 'gnn' ),
							'title'   => __( 'What to expect when you enquire', 'gnn' ),
							'text'    => __( 'How we coordinate appointments, floor plan requests and valuation visits.', 'gnn' ),
							'items'   => array(
								array(
									'question' => __( 'Are viewings accompanied by an advisor?', 'gnn' ),
									'answer'   => __( 'Yes. Every viewing is hosted individually by an advisor familiar with the building history and technical pack.', 'gnn' ),
								),
								array(
									'question' => __( 'Can I request floor plans before visiting?', 'gnn' ),
									'answer'   => __( 'Mention the listing in your message and we will send the full specification sheet and measured plans.', 'gnn' ),
								),
								array(
									'question' => __( 'How long does an appraisal visit take?', 'gnn' ),
									'answer'   => __( 'A standard on-site inspection takes around forty-five minutes, followed by a written report within a few days.', 'gnn' ),
								),
							),
						),
					),
				),
			),
		),
		'menu'        => array( 'home', 'listings', 'services', 'about', 'news', 'contact' ),
		'content'     => array(
			'projects' => array(
				array(
					'title'    => __( 'Courtyard brick townhouse', 'gnn' ),
					'excerpt'  => __( 'Three-bedroom residence arranged around a sheltered south-facing courtyard with double-height living space.', 'gnn' ),
					'category' => __( 'Residential', 'gnn' ),
					'year'     => '2026',
					'media'    => 'listing-1',
				),
				array(
					'title'    => __( 'Parkside corner apartment', 'gnn' ),
					'excerpt'  => __( 'Light-filled corner floor plan featuring bespoke oak joinery, wraparound balcony and step-free lift access.', 'gnn' ),
					'category' => __( 'Apartments', 'gnn' ),
					'year'     => '2026',
					'media'    => 'listing-2',
				),
				array(
					'title'    => __( 'Garden pavilion house', 'gnn' ),
					'excerpt'  => __( 'Single-storey family home with limestone terrace, planted garden studio and generous open kitchen.', 'gnn' ),
					'category' => __( 'Residential', 'gnn' ),
					'year'     => '2026',
					'media'    => 'listing-3',
				),
				array(
					'title'    => __( 'Timber wharf loft studio', 'gnn' ),
					'excerpt'  => __( 'Converted warehouse floor with exposed timber beams, acoustic glazing and flexible studio workspace.', 'gnn' ),
					'category' => __( 'Commercial', 'gnn' ),
					'year'     => '2025',
					'media'    => 'listing-4',
				),
				array(
					'title'    => __( 'Hillside terrace residence', 'gnn' ),
					'excerpt'  => __( 'Two-level home with covered stone loggia, custom library wall and panoramic rooftop outlook.', 'gnn' ),
					'category' => __( 'Residential', 'gnn' ),
					'year'     => '2025',
					'media'    => 'listing-5',
				),
				array(
					'title'    => __( 'Corner gallery and office suite', 'gnn' ),
					'excerpt'  => __( 'Street-level commercial space offering dual-aspect window frontage, private meeting room and kitchenette.', 'gnn' ),
					'category' => __( 'Commercial', 'gnn' ),
					'year'     => '2025',
					'media'    => 'listing-6',
				),
			),
			'posts'    => array(
				array(
					'title'   => __( 'How to prepare a home for private viewings', 'gnn' ),
					'excerpt' => __( 'Simple repairs, balanced daylight and uncluttered circulation help visitors read the proportions of every room.', 'gnn' ),
					'media'   => 'news-1',
				),
				array(
					'title'   => __( 'What to review before signing a commercial lease', 'gnn' ),
					'excerpt' => __( 'Check repair obligations, service charge caps, break clauses and fit-out approvals before agreeing heads of terms.', 'gnn' ),
					'media'   => 'news-2',
				),
				array(
					'title'   => __( 'Reading a floor plan beyond total square metres', 'gnn' ),
					'excerpt' => __( 'Window orientation, ceiling height and storage placement often matter more in daily life than raw floor area.', 'gnn' ),
					'media'   => 'news-3',
				),
			),
		),
		'media'       => array(
			'hero'      => array(
				'alt'     => __( 'Warm limestone residence exterior with planted courtyard at golden hour', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Architectural residential exterior in warm limestone, terracotta and natural timber with soft golden hour light, clean geometric lines, aspect ratio 16:10.',
			),
			'interior'  => array(
				'alt'     => __( 'Sunlit living room with oak joinery and stone hearth', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Minimal architectural interior with warm alabaster walls, oak shelving, travertine table and soft daylight through tall windows, aspect ratio 4:3.',
			),
			'agency'    => array(
				'alt'     => __( 'Architectural floor plans and material samples on a studio table', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Overhead composition of rolled architectural drawings, stone and timber samples on a warm sand desk in natural side light, aspect ratio 4:3.',
			),
			'advisor'   => array(
				'alt'     => __( 'Abstract architectural portrait backdrop in warm stone tones', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Abstract geometric portrait placeholder in warm terracotta, limestone and espresso charcoal tones, aspect ratio 1:1.',
			),
			'listing-1' => array(
				'alt'     => __( 'Courtyard brick townhouse exterior with timber shutters', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Contemporary handmade brick townhouse facade around a planted courtyard with warm terracotta and olive greenery, aspect ratio 4:3.',
			),
			'listing-2' => array(
				'alt'     => __( 'Corner apartment living room overlooking treetops', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Bright corner apartment interior with wide oak floorboards, corner glazing and subtle bronze hardware accents, aspect ratio 4:3.',
			),
			'listing-3' => array(
				'alt'     => __( 'Single-storey garden pavilion residence with stone terrace', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Low-profile modern garden pavilion house in pale stone and cedar opening onto a planted terrace, aspect ratio 4:3.',
			),
			'listing-4' => array(
				'alt'     => __( 'Loft studio workspace with exposed timber beams', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Open-plan architectural loft office with warm timber ceiling columns, brick walls and natural north light, aspect ratio 4:3.',
			),
			'listing-5' => array(
				'alt'     => __( 'Hillside terrace residence with covered stone loggia', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Two-storey hillside house with shaded travertine loggia and warm evening architectural lighting, aspect ratio 4:3.',
			),
			'listing-6' => array(
				'alt'     => __( 'Street-level gallery and studio suite with tall windows', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Refined street-front commercial gallery and office suite with slim bronze window frames and limestone flooring, aspect ratio 4:3.',
			),
			'news-1'    => array(
				'alt'     => __( 'Morning daylight across a quiet residential interior', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Calm architectural detail of morning sunlight casting window shadows across a warm plaster wall and oak bench, aspect ratio 16:10.',
			),
			'news-2'    => array(
				'alt'     => __( 'Modern commercial studio entrance and reception desk', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Tailored commercial studio entrance with timber acoustic panelling and stone counter in warm neutral tones, aspect ratio 16:10.',
			),
			'news-3'    => array(
				'alt'     => __( 'Architectural elevation drawing and scale ruler', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Close-up of clean architectural floor plan linework on cream paper with brass scale bar, aspect ratio 16:10.',
			),
		),
		'css'         => 'realestate',
	);
}
add_filter(
	'gnn_template_registry',
	function ( $templates ) {
		$templates['realestate'] = 'gnn_template_data_realestate';
		return $templates;
	}
);
