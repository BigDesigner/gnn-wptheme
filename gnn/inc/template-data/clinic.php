<?php
/**
 * GNNclinic ready-made site template data.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GNNclinic: calm, trustworthy template for medical clinics, specialist practices and health centres.
 *
 * @return array Template data definition.
 */
function gnn_template_data_clinic() {
	return array(
		'slug'        => 'clinic',
		'label'       => __( 'GNNclinic', 'gnn' ),
		'description' => __( 'A calm, structured website for medical clinics, specialist practices and health centres.', 'gnn' ),
		'requires'    => array( 'elementor' ),
		'tokens'      => array(
			'accent'       => '#0e7c7b',
			'accent_light' => '#095857',
			'accent_ink'   => '#ffffff',
			'radius'       => 10,
			'font'         => 'manrope',
			'heading_font' => 'manrope',
			'mode'         => 'light',
			'light'        => array( '#f8fbfb', '#eef5f5', '#dfecec', '#102a2e', '#486368', '#cfe0e0' ),
			'dark'         => array( '#0b181a', '#112326', '#183034', '#eaf4f4', '#97b2b5', '#234045' ),
		),
		'header'      => array(
			'preset'    => 'stacked',
			'cta_label' => __( 'Book an appointment', 'gnn' ),
			'options'   => array(
				'header_structure'  => 'stacked',
				'header_surface'    => 'solid',
				'header_shape'      => 'full',
				'header_width'      => 'contained',
				'header_behavior'   => 'sticky',
				'header_overlay'    => 0,
				'topbar_enable'     => 1,
				'header_height'     => 74,
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
						'cards',
						array(
							'eyebrow'         => __( 'Outpatient & specialist care', 'gnn' ),
							'title'           => __( 'Attentive medical care with clear guidance at every step.', 'gnn' ),
							'text'            => __( 'From routine check-ups to specialist consultations, our clinical team takes the time to listen, explain your options and coordinate your care.', 'gnn' ),
							'button_label'    => __( 'Book an appointment', 'gnn' ),
							'button_url'      => 'page:contact',
							'secondary_label' => __( 'Our services', 'gnn' ),
							'secondary_url'   => 'page:services',
							'image'           => 'media:hero',
							'items'           => array(
								array(
									'title' => __( 'Planned consultations', 'gnn' ),
									'text'  => __( 'Unhurried appointments with time reserved for your questions and medical history.', 'gnn' ),
								),
								array(
									'title' => __( 'Coordinated specialists', 'gnn' ),
									'text'  => __( 'General clinicians and diagnostic specialists working under one shared care record.', 'gnn' ),
								),
								array(
									'title' => __( 'Clear follow-up', 'gnn' ),
									'text'  => __( 'Written care summaries, test result reviews and straightforward next steps.', 'gnn' ),
								),
							),
						),
					),
					array(
						'features',
						'split-head',
						array(
							'eyebrow'      => __( 'Patient experience', 'gnn' ),
							'title'        => __( 'Designed around calm, predictable visits', 'gnn' ),
							'text'         => __( 'We remove administrative friction so you can focus on your health and conversations with your clinician.', 'gnn' ),
							'button_label' => __( 'About the clinic', 'gnn' ),
							'button_url'   => 'page:about',
							'items'        => array(
								array(
									'title' => __( 'Plain-language guidance', 'gnn' ),
									'text'  => __( 'Every finding and treatment option is explained clearly before any decision is made.', 'gnn' ),
								),
								array(
									'title' => __( 'On-site diagnostics', 'gnn' ),
									'text'  => __( 'Routine laboratory collection and imaging handled in the same facility.', 'gnn' ),
								),
								array(
									'title' => __( 'Continuity of care', 'gnn' ),
									'text'  => __( 'You see a consistent clinical team that knows your history and ongoing plan.', 'gnn' ),
								),
								array(
									'title' => __( 'Accessible scheduling', 'gnn' ),
									'text'  => __( 'Request, reschedule or prepare for appointments through our reception desk.', 'gnn' ),
								),
							),
						),
					),
					array(
						'services',
						'cards-4',
						array(
							'eyebrow'      => __( 'Clinical services', 'gnn' ),
							'title'        => __( 'Care areas and consultations', 'gnn' ),
							'text'         => __( 'Comprehensive support for everyday health, prevention and specialist referrals.', 'gnn' ),
							'button_label' => __( 'View details', 'gnn' ),
							'button_url'   => 'page:services',
							'items'        => array(
								array(
									'title' => __( 'General medicine', 'gnn' ),
									'text'  => __( 'Primary consultations, acute symptoms, ongoing condition reviews and referrals.', 'gnn' ),
								),
								array(
									'title' => __( 'Preventive check-ups', 'gnn' ),
									'text'  => __( 'Structured annual health screenings tailored to your age and risk profile.', 'gnn' ),
								),
								array(
									'title' => __( 'Diagnostics & lab', 'gnn' ),
									'text'  => __( 'Blood panels, ultrasound imaging and functional tests with timely clinician review.', 'gnn' ),
								),
								array(
									'title' => __( 'Rehabilitation support', 'gnn' ),
									'text'  => __( 'Guided recovery plans, mobility assessments and physical therapy coordination.', 'gnn' ),
								),
							),
						),
					),
					array(
						'process',
						'split-timeline',
						array(
							'eyebrow'      => __( 'Your visit', 'gnn' ),
							'title'        => __( 'What to expect from booking to follow-up', 'gnn' ),
							'text'         => __( 'A clear four-step flow for new and returning patients.', 'gnn' ),
							'button_label' => __( 'Request an appointment', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title' => __( 'Request a time', 'gnn' ),
									'text'  => __( 'Choose your department or clinician and share the reason for your visit.', 'gnn' ),
								),
								array(
									'title' => __( 'Initial consultation', 'gnn' ),
									'text'  => __( 'Review your history, symptoms and questions in a calm, private consultation room.', 'gnn' ),
								),
								array(
									'title' => __( 'Tests & assessment', 'gnn' ),
									'text'  => __( 'Complete any needed laboratory or diagnostic checks with clear preparation instructions.', 'gnn' ),
								),
								array(
									'title' => __( 'Care plan & review', 'gnn' ),
									'text'  => __( 'Receive a written summary of findings, treatment steps and follow-up timing.', 'gnn' ),
								),
							),
						),
					),
					array(
						'team',
						'four',
						array(
							'eyebrow' => __( 'Specialists', 'gnn' ),
							'title'   => __( 'Clinicians you will meet', 'gnn' ),
							'text'    => __( 'Replace these placeholder profiles with your physicians, nurses and care coordinators.', 'gnn' ),
							'image'   => 'media:team',
							'items'   => array(
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Medical director & general physician', 'gnn' ),
									'bio'  => __( 'Add clinical qualifications, focus areas and registration details here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Internal medicine specialist', 'gnn' ),
									'bio'  => __( 'Add clinical qualifications, focus areas and registration details here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Diagnostic imaging lead', 'gnn' ),
									'bio'  => __( 'Add clinical qualifications, focus areas and registration details here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Patient care coordinator', 'gnn' ),
									'bio'  => __( 'Add clinical qualifications, focus areas and registration details here.', 'gnn' ),
								),
							),
						),
					),
					array(
						'faq',
						'single',
						array(
							'eyebrow' => __( 'Patient FAQ', 'gnn' ),
							'title'   => __( 'Preparing for your appointment', 'gnn' ),
							'text'    => __( 'Answers to common questions before visiting the clinic.', 'gnn' ),
							'items'   => array(
								array(
									'question' => __( 'What should I bring to my first appointment?', 'gnn' ),
									'answer'   => __( 'Bring a valid photo ID, a list of current medications, any recent test results and your insurance or referral documents if applicable.', 'gnn' ),
								),
								array(
									'question' => __( 'Do I need a referral to book a consultation?', 'gnn' ),
									'answer'   => __( 'General consultations can be booked directly. Some specialist assessments or insurance plans may request a primary care referral.', 'gnn' ),
								),
								array(
									'question' => __( 'How are diagnostic test results shared?', 'gnn' ),
									'answer'   => __( 'Your clinician reviews every result first and discusses the findings with you during a follow-up call or visit.', 'gnn' ),
								),
								array(
									'question' => __( 'How can I reschedule an existing booking?', 'gnn' ),
									'answer'   => __( 'Contact reception by phone or email at least twenty-four hours before your scheduled time to move your appointment.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'band',
						array(
							'eyebrow'      => __( 'Appointments', 'gnn' ),
							'title'        => __( 'Ready to schedule your visit?', 'gnn' ),
							'text'         => __( 'Contact our reception team and we will help you arrange a convenient appointment time.', 'gnn' ),
							'button_label' => __( 'Book an appointment', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'services'    => array(
				'title'    => __( 'Services', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'split',
						array(
							'eyebrow'      => __( 'Medical services', 'gnn' ),
							'title'        => __( 'Thorough consultations and coordinated diagnostics.', 'gnn' ),
							'text'         => __( 'Describe the medical services, screenings and specialist consultations available at your clinic.', 'gnn' ),
							'button_label' => __( 'Book an appointment', 'gnn' ),
							'button_url'   => 'page:contact',
							'image'        => 'media:consultation',
						),
					),
					array(
						'services',
						'lined',
						array(
							'eyebrow'      => __( 'Departments', 'gnn' ),
							'title'        => __( 'Clinical care disciplines', 'gnn' ),
							'text'         => __( 'Each service is led by experienced clinicians following evidence-based protocols.', 'gnn' ),
							'button_label' => __( 'Enquire', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title' => __( 'Primary & family consultations', 'gnn' ),
									'text'  => __( 'Comprehensive evaluation for acute concerns, ongoing health management and preventive guidance.', 'gnn' ),
								),
								array(
									'title' => __( 'Preventive health screenings', 'gnn' ),
									'text'  => __( 'Structured check-ups covering cardiovascular, metabolic and general wellness indicators.', 'gnn' ),
								),
								array(
									'title' => __( 'Laboratory & diagnostic testing', 'gnn' ),
									'text'  => __( 'Sample collection, diagnostic ultrasound and Resting ECG with clear result interpretation.', 'gnn' ),
								),
								array(
									'title' => __( 'Chronic condition management', 'gnn' ),
									'text'  => __( 'Regular monitoring, medication reviews and lifestyle planning for long-term conditions.', 'gnn' ),
								),
								array(
									'title' => __( 'Physical recovery & mobility', 'gnn' ),
									'text'  => __( 'Post-injury assessment, posture and movement guidance tailored to daily comfort.', 'gnn' ),
								),
								array(
									'title' => __( 'Specialist referrals & coordination', 'gnn' ),
									'text'  => __( 'Direct collaboration with partner hospitals and surgical specialists when advanced care is required.', 'gnn' ),
								),
							),
						),
					),
					array(
						'about-split',
						'numbered',
						array(
							'eyebrow'      => __( 'Diagnostic clarity', 'gnn' ),
							'title'        => __( 'Care decisions supported by accurate testing', 'gnn' ),
							'text'         => __( 'We combine careful clinical history-taking with modern diagnostic tools so treatment plans rest on clear evidence.', 'gnn' ),
							'button_label' => __( 'Explore care programs', 'gnn' ),
							'button_url'   => 'page:departments',
							'image'        => 'media:diagnostics',
							'items'        => array(
								__( 'Clear instructions before any laboratory or imaging test', 'gnn' ),
								__( 'Direct review of results with your treating clinician', 'gnn' ),
								__( 'Written summary provided for your personal health records', 'gnn' ),
							),
						),
					),
					array(
						'pricing',
						'two',
						array(
							'eyebrow'      => __( 'Fee guide', 'gnn' ),
							'title'        => __( 'Standard visits and check-up packages', 'gnn' ),
							'text'         => __( 'Replace the placeholders below with your clinic consultation fees and insurance notes.', 'gnn' ),
							'button_label' => __( 'Book this visit', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title'    => __( 'Standard consultation', 'gnn' ),
									'text'     => __( 'Individual physician appointment for new concerns or routine follow-up.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Per visit', 'gnn' ),
									'featured' => false,
									'features' => array(
										__( 'Full medical history and physical examination', 'gnn' ),
										__( 'Treatment plan and prescription if indicated', 'gnn' ),
										__( 'Written visit summary', 'gnn' ),
									),
								),
								array(
									'title'    => __( 'Comprehensive health check', 'gnn' ),
									'text'     => __( 'Extended preventive assessment with baseline laboratory panel and review.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Complete package', 'gnn' ),
									'featured' => true,
									'features' => array(
										__( 'Extended consultation and vital measurements', 'gnn' ),
										__( 'Core blood panel and diagnostic screening', 'gnn' ),
										__( 'Dedicated follow-up session to review results', 'gnn' ),
									),
								),
							),
						),
					),
					array(
						'cta',
						'split',
						array(
							'eyebrow'      => __( 'Reception', 'gnn' ),
							'title'        => __( 'Not sure which consultation you need?', 'gnn' ),
							'text'         => __( 'Speak with our patient reception desk and we will guide you to the right clinician.', 'gnn' ),
							'button_label' => __( 'Contact reception', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'about'       => array(
				'title'    => __( 'About the clinic', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'stacked-cta',
						array(
							'eyebrow'         => __( 'About the clinic', 'gnn' ),
							'title'           => __( 'A calm environment built around patient trust.', 'gnn' ),
							'text'            => __( 'Share your clinic history, your accreditation standards and the principles that guide your medical team.', 'gnn' ),
							'button_label'    => __( 'Book an appointment', 'gnn' ),
							'button_url'      => 'page:contact',
							'secondary_label' => __( 'View services', 'gnn' ),
							'secondary_url'   => 'page:services',
							'items'           => array(
								__( 'Evidence-based clinical standards', 'gnn' ),
								__( 'Private consultation rooms', 'gnn' ),
								__( 'Coordinated multi-disciplinary team', 'gnn' ),
							),
						),
					),
					array(
						'about-split',
						'image-left',
						array(
							'eyebrow'      => __( 'Our practice', 'gnn' ),
							'title'        => __( 'Time to listen and care that stays connected', 'gnn' ),
							'text'         => __( 'We founded the clinic to offer unhurried medical consultations where clinicians and diagnostic specialists collaborate closely.', 'gnn' ),
							'button_label' => __( 'Meet our programs', 'gnn' ),
							'button_url'   => 'page:departments',
							'image'        => 'media:about',
							'items'        => array(
								__( 'Clear appointment times without rushed consultations', 'gnn' ),
								__( 'Direct communication between primary and specialist clinicians', 'gnn' ),
								__( 'Respectful, confidential care for every patient', 'gnn' ),
							),
						),
					),
					array(
						'values',
						'split',
						array(
							'eyebrow' => __( 'Clinical standards', 'gnn' ),
							'title'   => __( 'Principles that guide our practice', 'gnn' ),
							'text'    => __( 'Every member of our medical and support team works by the same commitments.', 'gnn' ),
							'items'   => array(
								array(
									'title' => __( 'Listening first', 'gnn' ),
									'text'  => __( 'Understanding your context and concerns is the starting point of accurate care.', 'gnn' ),
								),
								array(
									'title' => __( 'Clinical rigour', 'gnn' ),
									'text'  => __( 'Recommendations follow current medical evidence and verified diagnostic findings.', 'gnn' ),
								),
								array(
									'title' => __( 'Transparency', 'gnn' ),
									'text'  => __( 'Clear explanations of options, expected timelines and fees before procedures begin.', 'gnn' ),
								),
							),
						),
					),
					array(
						'team',
						'four',
						array(
							'eyebrow' => __( 'Clinical leadership', 'gnn' ),
							'title'   => __( 'Our medical and patient care team', 'gnn' ),
							'text'    => __( 'Replace these cards with your licensed clinicians and their credentials.', 'gnn' ),
							'image'   => 'media:team',
						),
					),
					array(
						'logos',
						'strip',
						array(
							'eyebrow' => __( 'Accreditations & insurance partners', 'gnn' ),
							'title'   => __( 'Recognised partners', 'gnn' ),
							'text'    => __( 'Replace these placeholder boxes with your accredited health bodies or insurance providers.', 'gnn' ),
						),
					),
				),
			),
			'departments' => array(
				'title'    => __( 'Programs', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Care programs & facilities', 'gnn' ),
							'title'        => __( 'Structured programs and clinical suites.', 'gnn' ),
							'text'         => __( 'Explore our preventive screening pathways, specialist units and clinic rooms.', 'gnn' ),
							'button_label' => __( 'Book an appointment', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'portfolio',
						'filtered',
						array(
							'eyebrow'      => __( 'Clinical units', 'gnn' ),
							'title'        => __( 'Programs and care pathways', 'gnn' ),
							'text'         => __( 'Filter by department to see our dedicated clinical programs and facilities.', 'gnn' ),
							'button_label' => __( 'Request consultation', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'gallery',
						'cols-3',
						array(
							'eyebrow' => __( 'Facilities', 'gnn' ),
							'title'   => __( 'Inside the clinic', 'gnn' ),
							'text'    => __( 'Consultation rooms, diagnostic suites and reception spaces designed for comfort and privacy.', 'gnn' ),
						),
					),
					array(
						'cta',
						'band',
						array(
							'eyebrow'      => __( 'Questions', 'gnn' ),
							'title'        => __( 'Need information about a specific program?', 'gnn' ),
							'text'         => __( 'Our reception and nursing team can explain preparation steps and appointment availability.', 'gnn' ),
							'button_label' => __( 'Contact the clinic', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'blog'        => array(
				'title'    => __( 'Health guides', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Patient information', 'gnn' ),
							'title'        => __( 'Health guides and clinic updates.', 'gnn' ),
							'text'         => __( 'Practical articles written by our clinical team to help you prepare for visits and look after everyday health.', 'gnn' ),
							'button_label' => __( 'Book an appointment', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'blog',
						'list',
						array(
							'eyebrow'      => __( 'Recent guides', 'gnn' ),
							'title'        => __( 'Articles from our clinicians', 'gnn' ),
							'text'         => __( 'Clear, practical reading on prevention, check-ups and preparing for tests.', 'gnn' ),
							'button_label' => __( 'All articles', 'gnn' ),
							'image'        => 'media:news-1',
						),
					),
					array(
						'newsletter',
						'split',
						array(
							'eyebrow'      => __( 'Clinic updates', 'gnn' ),
							'title'        => __( 'Seasonal health notes and clinic hours', 'gnn' ),
							'text'         => __( 'Receive occasional patient guides, preventive screening reminders and holiday schedule notices.', 'gnn' ),
							'button_label' => __( 'Subscribe', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'contact'     => array(
				'title'    => __( 'Appointments', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Appointments & reception', 'gnn' ),
							'title'        => __( 'Book an appointment or reach our desk.', 'gnn' ),
							'text'         => __( 'Send an appointment request using your booking form or call reception during clinic hours.', 'gnn' ),
							'button_label' => __( 'View services', 'gnn' ),
							'button_url'   => 'page:services',
						),
					),
					array(
						'contact',
						'cards',
						array(
							'eyebrow'    => __( 'Direct contact', 'gnn' ),
							'title'      => __( 'Reception, address and clinic hours', 'gnn' ),
							'text'       => __( 'For urgent medical emergencies, please contact your local emergency service immediately.', 'gnn' ),
							'form_title' => __( 'Appointment request form', 'gnn' ),
							'form_text'  => __( 'Add your appointment booking or contact form here with the form plugin you use.', 'gnn' ),
						),
					),
					array(
						'faq',
						'two-column',
						array(
							'eyebrow' => __( 'Visiting the clinic', 'gnn' ),
							'title'   => __( 'Reception and billing questions', 'gnn' ),
							'text'    => __( 'Practical details for arriving at the clinic and managing your booking.', 'gnn' ),
							'items'   => array(
								array(
									'question' => __( 'How early should I arrive before my appointment?', 'gnn' ),
									'answer'   => __( 'Please arrive ten minutes before your scheduled time so reception can verify your details calmly.', 'gnn' ),
								),
								array(
									'question' => __( 'Which insurance plans or payment methods do you accept?', 'gnn' ),
									'answer'   => __( 'Add your accepted insurance providers, direct-billing policies and payment methods here.', 'gnn' ),
								),
								array(
									'question' => __( 'Is step-free access available at the clinic?', 'gnn' ),
									'answer'   => __( 'Add your building accessibility, elevator access and nearby parking or transit details here.', 'gnn' ),
								),
								array(
									'question' => __( 'Can a family member attend the consultation with me?', 'gnn' ),
									'answer'   => __( 'Yes. You are welcome to bring a family member or support person to your consultation.', 'gnn' ),
								),
							),
						),
					),
				),
			),
		),
		'menu'        => array( 'home', 'services', 'about', 'departments', 'blog', 'contact' ),
		'content'     => array(
			'projects' => array(
				array(
					'title'    => __( 'Preventive health screening pathway', 'gnn' ),
					'excerpt'  => __( 'Structured annual health assessment combining physician examination, core laboratory panels and follow-up review.', 'gnn' ),
					'category' => __( 'Check-ups', 'gnn' ),
					'year'     => '2026',
					'media'    => 'preventive',
				),
				array(
					'title'    => __( 'Diagnostic imaging & ultrasound suite', 'gnn' ),
					'excerpt'  => __( 'On-site imaging and functional diagnostics with direct review by your treating clinician.', 'gnn' ),
					'category' => __( 'Diagnostics', 'gnn' ),
					'year'     => '2026',
					'media'    => 'diagnostics',
				),
				array(
					'title'    => __( 'General & family medicine unit', 'gnn' ),
					'excerpt'  => __( 'Primary consultations for everyday health questions, acute symptoms and coordinated specialist care.', 'gnn' ),
					'category' => __( 'Primary care', 'gnn' ),
					'year'     => '2026',
					'media'    => 'consultation',
				),
				array(
					'title'    => __( 'Mobility & physical recovery program', 'gnn' ),
					'excerpt'  => __( 'Individual movement assessment and guided rehabilitation for joint, back and post-injury recovery.', 'gnn' ),
					'category' => __( 'Rehabilitation', 'gnn' ),
					'year'     => '2025',
					'media'    => 'rehab',
				),
				array(
					'title'    => __( 'Private consultation & treatment suites', 'gnn' ),
					'excerpt'  => __( 'Calm, daylight-filled rooms designed for confidential consultations and minor outpatient procedures.', 'gnn' ),
					'category' => __( 'Facilities', 'gnn' ),
					'year'     => '2025',
					'media'    => 'about',
				),
			),
			'posts'    => array(
				array(
					'title'   => __( 'How to prepare for your first consultation', 'gnn' ),
					'excerpt' => __( 'A short checklist of documents, medication notes and questions to bring so you get the most from your visit.', 'gnn' ),
					'media'   => 'news-1',
				),
				array(
					'title'   => __( 'What a routine preventive check-up covers', 'gnn' ),
					'excerpt' => __( 'Learn which baseline measurements and screenings help track your health calmly over time.', 'gnn' ),
					'media'   => 'news-2',
				),
				array(
					'title'   => __( 'Understanding your diagnostic test results', 'gnn' ),
					'excerpt' => __( 'How our clinicians review laboratory ranges in context and explain what requires action and what does not.', 'gnn' ),
					'media'   => 'news-3',
				),
			),
		),
		'media'       => array(
			'hero'         => array(
				'alt'     => __( 'Calm medical clinic reception and bright consultation corridor', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Bright, calm modern medical clinic interior with soft natural daylight, light wood accents and clinical teal-sage tones; reassuring architectural composition, aspect ratio 16:9',
			),
			'about'        => array(
				'alt'     => __( 'Private medical consultation room with daylight and clean furnishings', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Calm medical consultation room with light oak desk, comfortable chairs and soft teal and white palette; clean healthcare interior, aspect ratio 4:3',
			),
			'consultation' => array(
				'alt'     => __( 'Clinician discussing a care plan across a consultation desk', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Doctor and patient reviewing notes at a bright wooden consultation desk; calm teal, soft mist and warm neutral tones; trustworthy clinical composition, aspect ratio 4:3',
			),
			'diagnostics'  => array(
				'alt'     => __( 'Modern diagnostic equipment in a clean clinical suite', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Clean diagnostic and ultrasound medical room with soft indirect lighting; clinical aqua-grey, white and deep teal palette, aspect ratio 4:3',
			),
			'preventive'   => array(
				'alt'     => __( 'Stethoscope and medical chart on a clean clinic desk', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Minimal medical still life with stethoscope, notepad and glass of water on a bright surface; soft sage, teal and white palette, aspect ratio 4:3',
			),
			'rehab'        => array(
				'alt'     => __( 'Bright physical therapy and rehabilitation room', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Daylight-filled rehabilitation and mobility studio with clean mats and wooden bars; calm teal and warm neutral colours, aspect ratio 4:3',
			),
			'team'         => array(
				'alt'     => __( 'Clinical team portrait placeholder in a bright medical practice', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Abstract minimal portrait silhouette in a bright clinical setting; soft teal, slate and mist grey tones, aspect ratio 1:1',
			),
			'news-1'       => array(
				'alt'     => __( 'Patient preparation checklist and notebook on a desk', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Clean notebook and pen beside a medical appointment card on a light desk; soft teal and mist palette, aspect ratio 16:9',
			),
			'news-2'       => array(
				'alt'     => __( 'Preventive health screening instruments in a bright clinic', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Orderly clinical tray with blood pressure cuff and stethoscope in natural light; calm aqua-grey and teal tones, aspect ratio 16:9',
			),
			'news-3'       => array(
				'alt'     => __( 'Clinician reviewing diagnostic charts on a tablet screen', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Close view of a clinician desk with diagnostic chart graphics and soft daylight; deep teal, white and slate palette, aspect ratio 16:9',
			),
		),
		'css'         => 'clinic',
	);
}
add_filter(
	'gnn_template_registry',
	function ( $templates ) {
		$templates['clinic'] = 'gnn_template_data_clinic';
		return $templates;
	}
);
