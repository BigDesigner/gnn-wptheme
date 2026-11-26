<?php
/**
 * GNNeducation starter template data.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GNNeducation: structured courses, academic programmes and enrolment paths.
 *
 * @return array Template data definition.
 */
function gnn_template_data_education() {
	return array(
		'slug'        => 'education',
		'label'       => __( 'GNNeducation', 'gnn' ),
		'description' => __( 'Structured courses, academic programmes and clear enrolment paths for schools, academies and training providers.', 'gnn' ),
		'requires'    => array( 'elementor' ),
		'tokens'      => array(
			'accent'       => '#4338ca',
			'accent_light' => '#3730a3',
			'accent_ink'   => '#ffffff',
			'radius'       => 10,
			'font'         => 'manrope',
			'heading_font' => 'space',
			'mode'         => 'light',
			'light'        => array( '#fcfbf8', '#f3f1ea', '#e6e2d6', '#171821', '#525466', '#dcd8ca' ),
			'dark'         => array( '#10121c', '#171a28', '#202437', '#f4f3ee', '#9ea3b8', '#2a2f45' ),
		),
		'header'      => array(
			'preset'    => 'stacked',
			'cta_label' => __( 'Apply now', 'gnn' ),
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
			'home'       => array(
				'title'    => __( 'Home', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'cards',
						array(
							'eyebrow'      => __( 'Open for enrolment', 'gnn' ),
							'title'        => __( 'Practical learning built around clear curricula.', 'gnn' ),
							'text'         => __( 'Structured programmes, guided workshops and direct mentor feedback designed to help learners build durable skills.', 'gnn' ),
							'button_label' => __( 'Explore programmes', 'gnn' ),
							'button_url'   => 'page:programs',
							'image'        => 'media:hero',
							'items'        => array(
								array(
									__( 'Foundational tracks', 'gnn' ),
									__( 'Step-by-step core modules for learners starting a new discipline from the ground up.', 'gnn' ),
								),
								array(
									__( 'Applied workshops', 'gnn' ),
									__( 'Hands-on studio sessions focused on real assignments and guided critique.', 'gnn' ),
								),
								array(
									__( 'Flexible study pace', 'gnn' ),
									__( 'Evening and weekend cohorts organised around clear weekly milestones.', 'gnn' ),
								),
							),
						),
					),
					array(
						'portfolio',
						'filtered',
						array(
							'eyebrow'      => __( 'Course catalogue', 'gnn' ),
							'title'        => __( 'Current study tracks', 'gnn' ),
							'text'         => __( 'Browse programmes by subject area, format and study level.', 'gnn' ),
							'button_label' => __( 'Full syllabus', 'gnn' ),
							'button_url'   => 'page:curriculum',
						),
					),
					array(
						'process',
						'split-timeline',
						array(
							'eyebrow'      => __( 'Learning path', 'gnn' ),
							'title'        => __( 'How each term is structured', 'gnn' ),
							'text'         => __( 'Every cohort follows a clear weekly rhythm from orientation to final presentation.', 'gnn' ),
							'button_label' => __( 'Enrolment guide', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									__( 'Orientation & placement', 'gnn' ),
									__( 'Review syllabus objectives, set up study tools and meet your cohort.', 'gnn' ),
								),
								array(
									__( 'Core seminars', 'gnn' ),
									__( 'Weekly instructor-led lessons paired with structured reading and exercises.', 'gnn' ),
								),
								array(
									__( 'Project studio', 'gnn' ),
									__( 'Apply concepts to a practical capstone brief with regular mentor reviews.', 'gnn' ),
								),
								array(
									__( 'Assessment & portfolio', 'gnn' ),
									__( 'Present completed work for feedback and receive your completion record.', 'gnn' ),
								),
							),
						),
					),
					array(
						'features',
						'split-image',
						array(
							'eyebrow'      => __( 'Study environment', 'gnn' ),
							'title'        => __( 'Designed for focused, collaborative study', 'gnn' ),
							'text'         => __( 'Small seminar groups and structured materials keep every learner supported throughout the term.', 'gnn' ),
							'button_label' => __( 'About the academy', 'gnn' ),
							'button_url'   => 'page:about',
							'image'        => 'media:campus',
							'items'        => array(
								array(
									__( 'Small cohorts', 'gnn' ),
									__( 'Focused seminar groups that leave room for questions and discussion.', 'gnn' ),
								),
								array(
									__( 'Structured materials', 'gnn' ),
									__( 'Clear reading lists, worksheets and recorded summaries for every module.', 'gnn' ),
								),
								array(
									__( 'Direct feedback', 'gnn' ),
									__( 'Regular review sessions on assignments with teaching faculty.', 'gnn' ),
								),
								array(
									__( 'Capstone archive', 'gnn' ),
									__( 'Graduates finish with documented project work ready to share.', 'gnn' ),
								),
							),
						),
					),
					array(
						'faq',
						'two-column',
						array(
							'eyebrow' => __( 'Admissions FAQ', 'gnn' ),
							'title'   => __( 'Common questions before applying', 'gnn' ),
							'text'    => __( 'Everything you need to know about prerequisites, schedules and registration.', 'gnn' ),
							'items'   => array(
								array(
									'question' => __( 'Do I need prior experience to enrol?', 'gnn' ),
									'answer'   => __( 'Foundational tracks require no previous background. Advanced studios list their prerequisites on the programme page.', 'gnn' ),
								),
								array(
									'question' => __( 'How much time should I set aside each week?', 'gnn' ),
									'answer'   => __( 'Most courses combine scheduled seminar hours with independent project work. Check each syllabus for weekly expectations.', 'gnn' ),
								),
								array(
									'question' => __( 'Are classes held in person or online?', 'gnn' ),
									'answer'   => __( 'Cohorts can run on campus, live online or in a hybrid format depending on the term schedule.', 'gnn' ),
								),
								array(
									'question' => __( 'How does tuition payment work?', 'gnn' ),
									'answer'   => __( 'You can pay per term or arrange staged instalments during enrolment confirmation.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'split',
						array(
							'eyebrow'      => __( 'Next intake', 'gnn' ),
							'title'        => __( 'Need help choosing the right programme?', 'gnn' ),
							'text'         => __( 'Speak with an admissions advisor to compare syllabi and find the track that fits your goals.', 'gnn' ),
							'button_label' => __( 'Request syllabus', 'gnn' ),
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
						'minimal',
						array(
							'eyebrow'      => __( 'Academic catalogue', 'gnn' ),
							'title'        => __( 'Programmes and study tracks.', 'gnn' ),
							'text'         => __( 'Compare foundational courses, intensive workshops and specialist certificates.', 'gnn' ),
							'button_label' => __( 'Apply now', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'portfolio',
						'cols-3',
						array(
							'eyebrow' => __( 'Disciplines', 'gnn' ),
							'title'   => __( 'Featured courses', 'gnn' ),
							'text'    => __( 'Each programme combines structured lectures with guided project work.', 'gnn' ),
						),
					),
					array(
						'services',
						'rows',
						array(
							'eyebrow'      => __( 'Study formats', 'gnn' ),
							'title'        => __( 'Choose how you learn', 'gnn' ),
							'text'         => __( 'Programmes are available in three scheduling formats across the academic year.', 'gnn' ),
							'button_label' => __( 'Enquire', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									__( 'Full-term certificate', 'gnn' ),
									__( 'Multi-week structured curriculum with weekly lectures, studio critiques and a graded capstone.', 'gnn' ),
								),
								array(
									__( 'Evening intensive', 'gnn' ),
									__( 'Focused twice-weekly sessions designed for working professionals upgrading specific skills.', 'gnn' ),
								),
								array(
									__( 'Weekend workshop', 'gnn' ),
									__( 'Short practical modules covering a single tool, method or creative workflow.', 'gnn' ),
								),
							),
						),
					),
					array(
						'pricing',
						'three',
						array(
							'eyebrow'      => __( 'Tuition & fees', 'gnn' ),
							'title'        => __( 'Transparent programme tuition', 'gnn' ),
							'text'         => __( 'Replace the placeholders below with your term fees and payment options.', 'gnn' ),
							'button_label' => __( 'Start application', 'gnn' ),
							'button_url'   => 'page:contact',
							'items'        => array(
								array(
									'title'    => __( 'Workshop pass', 'gnn' ),
									'text'     => __( 'Single short course or weekend module including study materials.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Per workshop', 'gnn' ),
									'featured' => false,
									'features' => array(
										__( 'Live workshop sessions', 'gnn' ),
										__( 'Digital workbook and files', 'gnn' ),
										__( 'Completion statement', 'gnn' ),
									),
								),
								array(
									'title'    => __( 'Certificate track', 'gnn' ),
									'text'     => __( 'Full-term structured course with mentor feedback and capstone review.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Per term', 'gnn' ),
									'featured' => true,
									'features' => array(
										__( 'Weekly seminar and studio', 'gnn' ),
										__( 'Individual assignment reviews', 'gnn' ),
										__( 'Final portfolio critique', 'gnn' ),
									),
								),
								array(
									'title'    => __( 'Team training', 'gnn' ),
									'text'     => __( 'Tailored cohort syllabus and private instruction for organisations.', 'gnn' ),
									'price'    => __( 'Add your price here.', 'gnn' ),
									'period'   => __( 'Custom cohort', 'gnn' ),
									'featured' => false,
									'features' => array(
										__( 'Customised curriculum scope', 'gnn' ),
										__( 'Dedicated teaching lead', 'gnn' ),
										__( 'Progress reporting', 'gnn' ),
									),
								),
							),
						),
					),
					array(
						'cta',
						'band',
						array(
							'eyebrow'      => __( 'Admissions', 'gnn' ),
							'title'        => __( 'Ready to reserve your place?', 'gnn' ),
							'text'         => __( 'Submit an enrolment enquiry and we will send the upcoming term dates.', 'gnn' ),
							'button_label' => __( 'Go to enrolment', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'curriculum' => array(
				'title'    => __( 'Curriculum', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'split',
						array(
							'eyebrow'      => __( 'Syllabus & method', 'gnn' ),
							'title'        => __( 'A curriculum built around doing the work.', 'gnn' ),
							'text'         => __( 'Every module pairs conceptual foundations with practical assignments you complete step by step.', 'gnn' ),
							'button_label' => __( 'View programmes', 'gnn' ),
							'button_url'   => 'page:programs',
							'image'        => 'media:curriculum',
						),
					),
					array(
						'process',
						'timeline',
						array(
							'eyebrow' => __( 'Module progression', 'gnn' ),
							'title'   => __( 'Four stages of a standard term', 'gnn' ),
							'text'    => __( 'Courses move from core principles to independent application.', 'gnn' ),
							'items'   => array(
								array(
									__( 'Foundations & vocabulary', 'gnn' ),
									__( 'Build a shared framework of core concepts, methods and reference cases.', 'gnn' ),
								),
								array(
									__( 'Guided exercises', 'gnn' ),
									__( 'Practice specific techniques through weekly structured assignments.', 'gnn' ),
								),
								array(
									__( 'Independent project', 'gnn' ),
									__( 'Scope and develop an original piece of work with instructor guidance.', 'gnn' ),
								),
								array(
									__( 'Critique & documentation', 'gnn' ),
									__( 'Present your findings to the cohort and refine the final submission.', 'gnn' ),
								),
							),
						),
					),
					array(
						'services',
						'cards-4',
						array(
							'eyebrow'      => __( 'Academic standards', 'gnn' ),
							'title'        => __( 'What every syllabus includes', 'gnn' ),
							'text'         => __( 'Clear expectations and accessible materials from day one.', 'gnn' ),
							'button_label' => '',
							'items'        => array(
								array(
									__( 'Weekly reading pack', 'gnn' ),
									__( 'Curated notes, reference sheets and practical examples for each topic.', 'gnn' ),
								),
								array(
									__( 'Live studio hours', 'gnn' ),
									__( 'Dedicated time for practical problem-solving alongside instructors.', 'gnn' ),
								),
								array(
									__( 'Written feedback', 'gnn' ),
									__( 'Actionable review notes on every milestone submission.', 'gnn' ),
								),
								array(
									__( 'Resource library', 'gnn' ),
									__( 'Templates, checklists and study guides available throughout the term.', 'gnn' ),
								),
							),
						),
					),
					array(
						'cta',
						'split',
						array(
							'eyebrow'      => __( 'Course outlines', 'gnn' ),
							'title'        => __( 'Request a detailed week-by-week syllabus', 'gnn' ),
							'text'         => __( 'Ask our admissions team for the full module breakdown of any programme.', 'gnn' ),
							'button_label' => __( 'Request syllabus', 'gnn' ),
							'button_url'   => 'page:contact',
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
							'eyebrow'      => __( 'About the academy', 'gnn' ),
							'title'        => __( 'Teaching practical skills with academic rigour.', 'gnn' ),
							'text'         => __( 'Replace this introduction with your school history, mission and teaching philosophy.', 'gnn' ),
							'button_label' => __( 'Contact admissions', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'about-split',
						'numbered',
						array(
							'eyebrow'      => __( 'Our approach', 'gnn' ),
							'title'        => __( 'Learning works best when theory meets practice', 'gnn' ),
							'text'         => __( 'We keep cohorts intentionally focused so instructors can work closely with every participant.', 'gnn' ),
							'button_label' => __( 'Explore programmes', 'gnn' ),
							'button_url'   => 'page:programs',
							'image'        => 'media:campus',
							'items'        => array(
								__( 'Curricula updated regularly to reflect current practice', 'gnn' ),
								__( 'Instructors with active teaching and subject expertise', 'gnn' ),
								__( 'Clear assessment rubrics and constructive critique', 'gnn' ),
							),
						),
					),
					array(
						'values',
						'cards',
						array(
							'eyebrow' => __( 'Academic values', 'gnn' ),
							'title'   => __( 'Principles behind our teaching', 'gnn' ),
							'text'    => __( 'Standards that guide every classroom, workshop and critique.', 'gnn' ),
							'items'   => array(
								array(
									__( 'Clarity first', 'gnn' ),
									__( 'Complex subjects broken down into structured, approachable steps.', 'gnn' ),
								),
								array(
									__( 'Active practice', 'gnn' ),
									__( 'Students learn by building, testing and refining real work.', 'gnn' ),
								),
								array(
									__( 'Supportive critique', 'gnn' ),
									__( 'Honest, respectful feedback that helps every learner improve.', 'gnn' ),
								),
							),
						),
					),
					array(
						'team',
						'four',
						array(
							'eyebrow' => __( 'Faculty', 'gnn' ),
							'title'   => __( 'Instructors and programme leads', 'gnn' ),
							'text'    => __( 'Replace these placeholders with your faculty members, their disciplines and short biographies.', 'gnn' ),
							'image'   => 'media:faculty',
							'items'   => array(
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Academic director', 'gnn' ),
									'bio'  => __( 'Add a short biography or teaching background here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Lead instructor, design systems', 'gnn' ),
									'bio'  => __( 'Add a short biography or teaching background here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Lead instructor, data & methods', 'gnn' ),
									'bio'  => __( 'Add a short biography or teaching background here.', 'gnn' ),
								),
								array(
									'name' => __( 'Name Surname', 'gnn' ),
									'role' => __( 'Student advisor', 'gnn' ),
									'bio'  => __( 'Add a short biography or teaching background here.', 'gnn' ),
								),
							),
						),
					),
					array(
						'gallery',
						'cols-3',
						array(
							'eyebrow' => __( 'Campus & studios', 'gnn' ),
							'title'   => __( 'Inside our classrooms and workshops', 'gnn' ),
							'text'    => __( 'Highlights from recent seminars, project studios and exhibitions.', 'gnn' ),
						),
					),
				),
			),
			'news'       => array(
				'title'    => __( 'Updates', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Academic updates', 'gnn' ),
							'title'        => __( 'Articles, study guides and term news.', 'gnn' ),
							'text'         => __( 'Read notes from our faculty, open-day announcements and student project highlights.', 'gnn' ),
							'button_label' => __( 'Enrolment dates', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
					array(
						'blog',
						'list',
						array(
							'eyebrow' => __( 'Latest posts', 'gnn' ),
							'title'   => __( 'From the faculty desk', 'gnn' ),
							'text'    => __( 'Recent articles on study methods, curriculum updates and campus events.', 'gnn' ),
							'image'   => 'media:news-1',
						),
					),
					array(
						'newsletter',
						'split',
						array(
							'eyebrow'      => __( 'Term bulletin', 'gnn' ),
							'title'        => __( 'Get notified when new cohorts open', 'gnn' ),
							'text'         => __( 'Receive term dates, open-day invitations and study resources.', 'gnn' ),
							'button_label' => __( 'Subscribe', 'gnn' ),
							'button_url'   => 'page:contact',
						),
					),
				),
			),
			'contact'    => array(
				'title'    => __( 'Enrolment', 'gnn' ),
				'sections' => array(
					array(
						'hero',
						'minimal',
						array(
							'eyebrow'      => __( 'Enrolment & enquiries', 'gnn' ),
							'title'        => __( 'Start your application or ask a question.', 'gnn' ),
							'text'         => __( 'Contact our admissions office for syllabus details, term dates and tuition guidance.', 'gnn' ),
							'button_label' => __( 'View programmes', 'gnn' ),
							'button_url'   => 'page:programs',
						),
					),
					array(
						'contact',
						'split',
						array(
							'eyebrow' => __( 'Admissions office', 'gnn' ),
							'title'   => __( 'Get in touch with our team', 'gnn' ),
							'text'    => __( 'Reach us directly or add your application form using your preferred form plugin.', 'gnn' ),
						),
					),
					array(
						'faq',
						'single',
						array(
							'eyebrow' => __( 'Application steps', 'gnn' ),
							'title'   => __( 'What happens after you enquire', 'gnn' ),
							'text'    => __( 'A clear process from first conversation to confirmed seat.', 'gnn' ),
							'items'   => array(
								array(
									'question' => __( 'When do applications close for the next term?', 'gnn' ),
									'answer'   => __( 'Enrolment remains open until cohort seats are filled. Early inquiry is recommended for studio tracks.', 'gnn' ),
								),
								array(
									'question' => __( 'Can I speak with an instructor before enrolling?', 'gnn' ),
									'answer'   => __( 'Yes. Request an advisory call through the contact form and we will arrange a short syllabus walkthrough.', 'gnn' ),
								),
								array(
									'question' => __( 'Can my employer sponsor my tuition?', 'gnn' ),
									'answer'   => __( 'We provide formal programme outlines and invoices for employer-sponsored training.', 'gnn' ),
								),
							),
						),
					),
				),
			),
		),
		'menu'        => array( 'home', 'programs', 'curriculum', 'about', 'news', 'contact' ),
		'content'     => array(
			'projects' => array(
				array(
					'title'    => __( 'Digital Product Design Foundations', 'gnn' ),
					'excerpt'  => __( 'An eight-module course covering user research, interface structure, prototyping and critique.', 'gnn' ),
					'category' => __( 'Design', 'gnn' ),
					'year'     => '2026',
					'media'    => 'course-1',
				),
				array(
					'title'    => __( 'Applied Data Analysis & Visualisation', 'gnn' ),
					'excerpt'  => __( 'Hands-on training in cleaning datasets, exploratory analysis and clear visual reporting.', 'gnn' ),
					'category' => __( 'Data', 'gnn' ),
					'year'     => '2026',
					'media'    => 'course-2',
				),
				array(
					'title'    => __( 'Front-End Architecture Studio', 'gnn' ),
					'excerpt'  => __( 'Build accessible, maintainable web interfaces using modern semantic HTML, CSS and JavaScript.', 'gnn' ),
					'category' => __( 'Engineering', 'gnn' ),
					'year'     => '2026',
					'media'    => 'course-3',
				),
				array(
					'title'    => __( 'Editorial Writing & Content Systems', 'gnn' ),
					'excerpt'  => __( 'Learn structured writing, information architecture and style guide governance for digital teams.', 'gnn' ),
					'category' => __( 'Communication', 'gnn' ),
					'year'     => '2026',
					'media'    => 'course-4',
				),
				array(
					'title'    => __( 'Project Leadership Intensive', 'gnn' ),
					'excerpt'  => __( 'Practical frameworks for scoping, scheduling and coordinating cross-functional work.', 'gnn' ),
					'category' => __( 'Management', 'gnn' ),
					'year'     => '2026',
					'media'    => 'course-5',
				),
			),
			'posts'    => array(
				array(
					'title'   => __( 'How to choose between a foundational track and an intensive studio', 'gnn' ),
					'excerpt' => __( 'Start by mapping your current skills against the weekly syllabus outcomes and time commitment.', 'gnn' ),
					'media'   => 'news-1',
				),
				array(
					'title'   => __( 'Building a study habit that lasts through the term', 'gnn' ),
					'excerpt' => __( 'Consistent weekly practice and early feedback matter more than last-minute marathon sessions.', 'gnn' ),
					'media'   => 'news-2',
				),
				array(
					'title'   => __( 'What makes a strong capstone portfolio project', 'gnn' ),
					'excerpt' => __( 'Document the problem, the constraints you faced and the reasoning behind your final decisions.', 'gnn' ),
					'media'   => 'news-3',
				),
			),
		),
		'media'       => array(
			'hero'       => array(
				'alt'     => __( 'Bright seminar studio with study tables and architectural daylight', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Warm parchment and deep indigo palette (#fcfbf8, #4338ca, #171821); bright academic studio interior with geometric bookshelves, study desk and natural light, ratio 16:10',
			),
			'campus'     => array(
				'alt'     => __( 'Modern academy library and collaborative study lounge', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Warm cream, slate navy and cobalt indigo palette; architectural reading room with wooden study tables and structured shelving, ratio 4:3',
			),
			'curriculum' => array(
				'alt'     => __( 'Structured course diagrams and printed syllabus notebooks', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Clean flat-lay composition of open grid notebooks, geometric diagrams and indigo ink markers on warm paper background, ratio 4:3',
			),
			'faculty'    => array(
				'alt'     => __( 'Abstract geometric portrait placeholder in indigo and warm stone', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Minimal abstract editorial portrait silhouette with layered indigo, slate and warm parchment shapes, ratio 1:1',
			),
			'course-1'   => array(
				'alt'     => __( 'Interface wireframes and design system grid blocks', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Abstract modular UI grid cards and geometric layout lines in cobalt indigo and warm paper tones, ratio 16:10',
			),
			'course-2'   => array(
				'alt'     => __( 'Structured data charts and analytical coordinate grid', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Clean isometric chart bars and coordinate lines in deep indigo, soft violet and parchment white, ratio 16:10',
			),
			'course-3'   => array(
				'alt'     => __( 'Modular code structure and geometric component tree', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Architectural diagram of nested structural blocks and crisp connection lines on dark navy and indigo canvas, ratio 16:10',
			),
			'course-4'   => array(
				'alt'     => __( 'Editorial typography grid and baseline sheets', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Minimal typographic layout study with Swiss grid lines, serif/sans balance and indigo accent rules on cream paper, ratio 16:10',
			),
			'course-5'   => array(
				'alt'     => __( 'Project timeline milestones and planning board', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Structured milestone cards and timeline connectors in indigo, warm grey and ivory, ratio 16:10',
			),
			'news-1'     => array(
				'alt'     => __( 'Study notes and course planning desk', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Editorial overhead view of study syllabus sheets, fountain pen and geometric bookmark in indigo and warm paper tones, ratio 16:10',
			),
			'news-2'     => array(
				'alt'     => __( 'Weekly study calendar and notebook pages', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Minimalist weekly planner grid with highlighted indigo study blocks on warm stone desk surface, ratio 16:10',
			),
			'news-3'     => array(
				'alt'     => __( 'Presentation display of capstone project boards', 'gnn' ),
				'license' => 'original',
				'brief'   => 'Gallery pin-up wall with framed project boards and structured layout cards in warm daylight, ratio 16:10',
			),
		),
		'css'         => 'education',
	);
}
add_filter(
	'gnn_template_registry',
	function ( $templates ) {
		$templates['education'] = 'gnn_template_data_education';
		return $templates;
	}
);
