<?php
/**
 * Services, features, process, about-split and values sections.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve a string argument from section overrides with fallback.
 *
 * @param array  $a          Section arguments.
 * @param string $key        Argument key.
 * @param string $fallback    Default value when key is not provided.
 * @param bool   $allow_empty Whether an explicit empty string is preserved.
 * @return string Resolved text value.
 */
function gnn_sec_services_arg( $a, $key, $fallback, $allow_empty = true ) {
	if ( is_array( $a ) && array_key_exists( $key, $a ) && null !== $a[ $key ] && is_scalar( $a[ $key ] ) ) {
		$value = (string) $a[ $key ];
		if ( '' !== $value || $allow_empty ) {
			return $value;
		}
	}
	return $fallback;
}

/**
 * Resolve a valid variant slug for a section.
 *
 * @param array  $a       Section arguments.
 * @param array  $allowed Allowed variant slugs.
 * @param string $fallback Default variant slug.
 * @param array  $aliases Optional alias map to canonical variant slugs.
 * @return string Active variant slug.
 */
function gnn_sec_services_variant( $a, $allowed, $fallback, $aliases = array() ) {
	$raw = isset( $a['variant'] ) && is_scalar( $a['variant'] ) ? sanitize_key( (string) $a['variant'] ) : '';
	if ( '' !== $raw && isset( $aliases[ $raw ] ) ) {
		$raw = $aliases[ $raw ];
	}
	if ( in_array( $raw, $allowed, true ) ) {
		return $raw;
	}
	return $fallback;
}

/**
 * Normalize card or row items into two-element title/description pairs.
 *
 * @param array $a        Section arguments.
 * @param array $defaults Default pairs when custom items are absent.
 * @return array List of two-element arrays (title, description).
 */
function gnn_sec_services_pairs( $a, $defaults ) {
	if ( ! isset( $a['items'] ) || ! is_array( $a['items'] ) || empty( $a['items'] ) ) {
		return $defaults;
	}

	$pairs = array();
	foreach ( $a['items'] as $item ) {
		if ( is_array( $item ) ) {
			$title = '';
			$text  = '';
			if ( isset( $item['title'] ) && is_scalar( $item['title'] ) ) {
				$title = (string) $item['title'];
			} elseif ( isset( $item[0] ) && is_scalar( $item[0] ) ) {
				$title = (string) $item[0];
			}

			if ( isset( $item['text'] ) && is_scalar( $item['text'] ) ) {
				$text = (string) $item['text'];
			} elseif ( isset( $item['description'] ) && is_scalar( $item['description'] ) ) {
				$text = (string) $item['description'];
			} elseif ( isset( $item[1] ) && is_scalar( $item[1] ) ) {
				$text = (string) $item[1];
			}

			if ( '' !== $title || '' !== $text ) {
				$pairs[] = array( $title, $text );
			}
		} elseif ( is_scalar( $item ) && '' !== (string) $item ) {
			$pairs[] = array( (string) $item, '' );
		}
	}

	return ! empty( $pairs ) ? $pairs : $defaults;
}

/**
 * Normalize list items into non-empty strings for checklists.
 *
 * @param array $a        Section arguments.
 * @param array $defaults Default string items when custom items are absent.
 * @return array List of strings.
 */
function gnn_sec_services_lines( $a, $defaults ) {
	if ( ! isset( $a['items'] ) || ! is_array( $a['items'] ) || empty( $a['items'] ) ) {
		return $defaults;
	}

	$lines = array();
	foreach ( $a['items'] as $item ) {
		if ( is_scalar( $item ) && '' !== trim( (string) $item ) ) {
			$lines[] = (string) $item;
		} elseif ( is_array( $item ) ) {
			if ( isset( $item['title'] ) && is_scalar( $item['title'] ) && '' !== trim( (string) $item['title'] ) ) {
				$lines[] = (string) $item['title'];
			} elseif ( isset( $item['text'] ) && is_scalar( $item['text'] ) && '' !== trim( (string) $item['text'] ) ) {
				$lines[] = (string) $item['text'];
			} elseif ( isset( $item[0] ) && is_scalar( $item[0] ) && '' !== trim( (string) $item[0] ) ) {
				$lines[] = (string) $item[0];
			}
		}
	}

	return ! empty( $lines ) ? $lines : $defaults;
}

/**
 * Build an Elementor image widget using the theme placeholder fallback.
 *
 * @param array  $a   Section arguments.
 * @param string $alt Accessible image alt text.
 * @return array Elementor image widget element.
 */
function gnn_sec_services_image( $a, $alt ) {
	$default_url = get_theme_file_uri( 'assets/img/placeholder-cover.svg' );
	$image_url   = gnn_sec_services_arg( $a, 'image', $default_url, false );

	return gnn_st_w(
		'image',
		array(
			'image'      => array(
				'url' => esc_url_raw( $image_url ),
				'id'  => '',
				'alt' => $alt,
			),
			'image_size' => 'full',
			'link_to'    => 'none',
		)
	);
}

/**
 * Wrap section children in a scoped band container and append the variant class.
 *
 * @param string $id       Section identifier.
 * @param string $variant  Active variant slug.
 * @param array  $children Inner Elementor elements.
 * @return array Elementor root element list.
 */
function gnn_sec_services_band( $id, $variant, $children ) {
	$elements                                = array(
		gnn_st_band( 'gnn-sec', $id, $children ),
	);
	$elements[0]['settings']['css_classes'] .= ' gnn-sec--' . sanitize_html_class( $variant );

	return $elements;
}

/**
 * Build the services section.
 *
 * Variants:
 * - cards-3: 3-column numbered card grid.
 * - cards-4: 4-column numbered card grid.
 * - rows:    Numbered horizontal row list.
 * - lined:   Iconless top-lined minimal grid.
 *
 * @param array $a Section arguments.
 * @return array Elementor elements.
 */
function gnn_sec_build_services( $a = array() ) {
	$variant = gnn_sec_services_variant(
		$a,
		array( 'cards-3', 'cards-4', 'rows', 'lined' ),
		'cards-3',
		array(
			'cards'    => 'cards-3',
			'grid'     => 'cards-3',
			'grid-3'   => 'cards-3',
			'grid-4'   => 'cards-4',
			'list'     => 'rows',
			'minimal'  => 'lined',
			'bordered' => 'lined',
		)
	);

	$eyebrow      = gnn_sec_services_arg( $a, 'eyebrow', __( 'What we do', 'gnn' ) );
	$title        = gnn_sec_services_arg( $a, 'title', __( 'Services tailored to your goals', 'gnn' ), false );
	$text         = gnn_sec_services_arg( $a, 'text', __( 'Clear scope, practical execution and measurable outcomes across every stage.', 'gnn' ) );
	$button_label = gnn_sec_services_arg( $a, 'button_label', __( 'Learn more', 'gnn' ) );
	$button_url   = gnn_sec_services_arg( $a, 'button_url', '#', false );
	$link_url     = '' !== $button_label ? $button_url : '';

	$defaults = array(
		array(
			__( 'Strategy & planning', 'gnn' ),
			__( 'Assess current priorities, define clear objectives and agree on a practical roadmap.', 'gnn' ),
		),
		array(
			__( 'Design & systems', 'gnn' ),
			__( 'Structure workflows, interfaces and standards that teams can run with confidence.', 'gnn' ),
		),
		array(
			__( 'Delivery & support', 'gnn' ),
			__( 'Execute step by step with regular reviews, clear ownership and smooth handover.', 'gnn' ),
		),
	);

	if ( 'cards-4' === $variant ) {
		$defaults[] = array(
			__( 'Review & optimisation', 'gnn' ),
			__( 'Track outcomes after launch and refine what matters most for long-term value.', 'gnn' ),
		);
	}

	$items = gnn_sec_services_pairs( $a, $defaults );
	$head  = gnn_st_head( $eyebrow, $title, $text );

	if ( 'rows' === $variant ) {
		$rows = array();
		foreach ( $items as $index => $item ) {
			$rows[] = gnn_st_c(
				'gnn-row',
				array(
					gnn_st_p( sprintf( '%02d', $index + 1 ), 'gnn-num' ),
					gnn_st_h( $item[0], 3 ),
					'' !== $item[1] ? gnn_st_p( $item[1] ) : null,
					'' !== $link_url ? gnn_st_b( $button_label, $link_url, 'gnn-link' ) : null,
				),
				true
			);
		}
		$body = gnn_st_c( 'gnn-rows', $rows, true );
	} elseif ( 'lined' === $variant ) {
		$lined_cards = array();
		foreach ( $items as $item ) {
			$lined_cards[] = gnn_st_c(
				'gnn-card gnn-sec-lined-card',
				array(
					gnn_st_h( $item[0], 3 ),
					'' !== $item[1] ? gnn_st_p( $item[1] ) : null,
					'' !== $link_url ? gnn_st_b( $button_label, $link_url, 'gnn-link' ) : null,
				),
				true
			);
		}
		$body = gnn_st_c( 'gnn-cards gnn-cols-3 gnn-sec-lined-grid', $lined_cards, true );
	} elseif ( 'cards-4' === $variant ) {
		$body = gnn_st_cards( $items, 'gnn-cols-4', $link_url, $button_label );
	} else {
		$body = gnn_st_cards( $items, 'gnn-cols-3', $link_url, $button_label );
	}

	return gnn_sec_services_band( 'services', $variant, array( $head, $body ) );
}

/**
 * Build the features section.
 *
 * Variants:
 * - grid-2x2:    Heading with a 2x2 feature grid.
 * - split-image: Split layout with feature grid and visual.
 * - split-head:  Side heading column paired with a 2x2 feature grid.
 *
 * @param array $a Section arguments.
 * @return array Elementor elements.
 */
function gnn_sec_build_features( $a = array() ) {
	$variant = gnn_sec_services_variant(
		$a,
		array( 'grid-2x2', 'split-image', 'split-head' ),
		'grid-2x2',
		array(
			'grid'  => 'grid-2x2',
			'2x2'   => 'grid-2x2',
			'split' => 'split-image',
			'image' => 'split-image',
		)
	);

	$eyebrow      = gnn_sec_services_arg( $a, 'eyebrow', __( 'Key capabilities', 'gnn' ) );
	$title        = gnn_sec_services_arg( $a, 'title', __( 'Built for clarity and everyday reliability', 'gnn' ), false );
	$text         = gnn_sec_services_arg( $a, 'text', __( 'Everything needed to run smoothly, presented in a structure your team and visitors can follow.', 'gnn' ) );
	$button_label = gnn_sec_services_arg( $a, 'button_label', __( 'Explore features', 'gnn' ) );
	$button_url   = gnn_sec_services_arg( $a, 'button_url', '#', false );
	$link_url     = '' !== $button_label ? $button_url : '';

	$defaults = array(
		array(
			__( 'Modular structure', 'gnn' ),
			__( 'Combine and rearrange sections to match the exact flow of your content.', 'gnn' ),
		),
		array(
			__( 'Consistent styling', 'gnn' ),
			__( 'Typography, spacing and colours adapt automatically to your active theme settings.', 'gnn' ),
		),
		array(
			__( 'Responsive layout', 'gnn' ),
			__( 'Every grid and split view adjusts cleanly from wide desktop screens to mobile devices.', 'gnn' ),
		),
		array(
			__( 'Accessible markup', 'gnn' ),
			__( 'Clear heading hierarchy, readable contrast and keyboard-friendly interactive elements.', 'gnn' ),
		),
	);

	$items = gnn_sec_services_pairs( $a, $defaults );

	if ( 'split-image' === $variant ) {
		$feature_items = array();
		foreach ( $items as $index => $item ) {
			$feature_items[] = gnn_st_c(
				'gnn-card gnn-sec-feature-item',
				array(
					gnn_st_p( sprintf( '%02d', $index + 1 ), 'gnn-num' ),
					gnn_st_h( $item[0], 3 ),
					'' !== $item[1] ? gnn_st_p( $item[1] ) : null,
				),
				true
			);
		}

		$children = array(
			gnn_st_c(
				'gnn-split',
				array(
					gnn_st_c(
						'gnn-copy',
						array(
							gnn_st_head( $eyebrow, $title, $text ),
							gnn_st_c( 'gnn-cards gnn-cols-2 gnn-sec-feature-subgrid', $feature_items, true ),
							'' !== $link_url ? gnn_st_c( 'gnn-actions', array( gnn_st_b( $button_label, $link_url, 'gnn-btn-main' ) ), true ) : null,
						),
						true
					),
					gnn_st_c(
						'gnn-visual',
						array(
							gnn_sec_services_image( $a, $title ),
						),
						true
					),
				),
				true
			),
		);
	} elseif ( 'split-head' === $variant ) {
		$children = array(
			gnn_st_c(
				'gnn-split gnn-sec-split-top',
				array(
					gnn_st_c(
						'gnn-copy',
						array(
							gnn_st_head( $eyebrow, $title, $text ),
							'' !== $link_url ? gnn_st_c( 'gnn-actions', array( gnn_st_b( $button_label, $link_url, 'gnn-btn-ghost' ) ), true ) : null,
						),
						true
					),
					gnn_st_cards( $items, 'gnn-cols-2' ),
				),
				true
			),
		);
	} else {
		$children = array(
			gnn_st_head( $eyebrow, $title, $text ),
			gnn_st_cards( $items, 'gnn-cols-2', $link_url, $button_label ),
		);
	}

	return gnn_sec_services_band( 'features', $variant, $children );
}

/**
 * Build the process section.
 *
 * Variants:
 * - horizontal:     4-column horizontal step cards.
 * - timeline:       Vertical numbered timeline.
 * - split-timeline: Side heading paired with a vertical timeline.
 *
 * @param array $a Section arguments.
 * @return array Elementor elements.
 */
function gnn_sec_build_process( $a = array() ) {
	$variant = gnn_sec_services_variant(
		$a,
		array( 'horizontal', 'timeline', 'split-timeline' ),
		'horizontal',
		array(
			'steps'    => 'horizontal',
			'vertical' => 'timeline',
			'split'    => 'split-timeline',
		)
	);

	$eyebrow      = gnn_sec_services_arg( $a, 'eyebrow', __( 'How it works', 'gnn' ) );
	$title        = gnn_sec_services_arg( $a, 'title', __( 'A clear path from first brief to final delivery', 'gnn' ), false );
	$text         = gnn_sec_services_arg( $a, 'text', __( 'Four focused stages keep everyone aligned on scope, progress and outcomes.', 'gnn' ) );
	$button_label = gnn_sec_services_arg( $a, 'button_label', '' );
	$button_url   = gnn_sec_services_arg( $a, 'button_url', '#', false );
	$link_url     = '' !== $button_label ? $button_url : '';

	$defaults = array(
		array(
			__( 'Discover', 'gnn' ),
			__( 'Understand your goals, requirements and constraints before proposing a direction.', 'gnn' ),
		),
		array(
			__( 'Plan', 'gnn' ),
			__( 'Define the scope, milestones, timeline and responsibilities in plain language.', 'gnn' ),
		),
		array(
			__( 'Execute', 'gnn' ),
			__( 'Build and refine the work iteratively with transparent progress check-ins.', 'gnn' ),
		),
		array(
			__( 'Review', 'gnn' ),
			__( 'Verify the results against the original brief and prepare a clean handover.', 'gnn' ),
		),
	);

	$items = gnn_sec_services_pairs( $a, $defaults );

	if ( 'timeline' === $variant || 'split-timeline' === $variant ) {
		$timeline_steps = array();
		foreach ( $items as $index => $item ) {
			$timeline_steps[] = gnn_st_c(
				'gnn-sec-timeline-item',
				array(
					gnn_st_c(
						'gnn-sec-timeline-marker',
						array(
							gnn_st_p( sprintf( '%02d', $index + 1 ), 'gnn-num' ),
						),
						true
					),
					gnn_st_c(
						'gnn-sec-timeline-body',
						array(
							gnn_st_h( $item[0], 3 ),
							'' !== $item[1] ? gnn_st_p( $item[1] ) : null,
						),
						true
					),
				),
				true
			);
		}

		$timeline = gnn_st_c( 'gnn-sec-timeline', $timeline_steps, true );

		if ( 'split-timeline' === $variant ) {
			$children = array(
				gnn_st_c(
					'gnn-split gnn-sec-split-top',
					array(
						gnn_st_c(
							'gnn-copy',
							array(
								gnn_st_head( $eyebrow, $title, $text ),
								'' !== $link_url ? gnn_st_c( 'gnn-actions', array( gnn_st_b( $button_label, $link_url, 'gnn-btn-ghost' ) ), true ) : null,
							),
							true
						),
						$timeline,
					),
					true
				),
			);
		} else {
			$children = array(
				gnn_st_head( $eyebrow, $title, $text ),
				$timeline,
				'' !== $link_url ? gnn_st_c( 'gnn-actions', array( gnn_st_b( $button_label, $link_url, 'gnn-btn-main' ) ), true ) : null,
			);
		}
	} else {
		$children = array(
			gnn_st_head( $eyebrow, $title, $text ),
			gnn_st_cards( $items, 'gnn-cols-4 gnn-steps', $link_url, $button_label ),
		);
	}

	return gnn_sec_services_band( 'process', $variant, $children );
}

/**
 * Build the about-split section (image + text + bullet list).
 *
 * Variants:
 * - image-left:  Visual on the left, copy and checklist on the right.
 * - image-right: Copy and checklist on the left, visual on the right.
 * - numbered:    Visual on the left, copy and numbered key points on the right.
 *
 * @param array $a Section arguments.
 * @return array Elementor elements.
 */
function gnn_sec_build_about_split( $a = array() ) {
	$variant = gnn_sec_services_variant(
		$a,
		array( 'image-left', 'image-right', 'numbered' ),
		'image-left',
		array(
			'left'    => 'image-left',
			'default' => 'image-left',
			'right'   => 'image-right',
			'flip'    => 'image-right',
			'cards'   => 'numbered',
		)
	);

	$eyebrow      = gnn_sec_services_arg( $a, 'eyebrow', __( 'About our approach', 'gnn' ) );
	$title        = gnn_sec_services_arg( $a, 'title', __( 'Practical expertise focused on lasting results', 'gnn' ), false );
	$text         = gnn_sec_services_arg( $a, 'text', __( 'We work closely with your team from initial planning through delivery, keeping communication direct and decisions grounded in evidence.', 'gnn' ) );
	$button_label = gnn_sec_services_arg( $a, 'button_label', __( 'More about us', 'gnn' ) );
	$button_url   = gnn_sec_services_arg( $a, 'button_url', '#', false );
	$link_url     = '' !== $button_label ? $button_url : '';

	$default_lines = array(
		__( 'Clear scope and transparent timelines', 'gnn' ),
		__( 'Direct collaboration with experienced specialists', 'gnn' ),
		__( 'Documentation and handover your team can maintain', 'gnn' ),
	);

	$lines = gnn_sec_services_lines( $a, $default_lines );

	if ( 'numbered' === $variant ) {
		$point_nodes = array();
		foreach ( $lines as $index => $line ) {
			$point_nodes[] = gnn_st_c(
				'gnn-sec-point-item',
				array(
					gnn_st_p( sprintf( '%02d', $index + 1 ), 'gnn-num' ),
					gnn_st_p( $line ),
				),
				true
			);
		}
		$list_node = gnn_st_c( 'gnn-sec-point-list', $point_nodes, true );
	} else {
		$tick_nodes = array();
		foreach ( $lines as $line ) {
			$tick_nodes[] = gnn_st_p( $line );
		}
		$list_node = gnn_st_c( 'gnn-ticks gnn-sec-checklist', $tick_nodes, true );
	}

	$visual_col = gnn_st_c(
		'gnn-visual',
		array(
			gnn_sec_services_image( $a, $title ),
		),
		true
	);

	$copy_col = gnn_st_c(
		'gnn-copy',
		array(
			'' !== $eyebrow ? gnn_st_p( $eyebrow, 'gnn-eyebrow' ) : null,
			gnn_st_h( $title, 2 ),
			'' !== $text ? gnn_st_p( $text ) : null,
			$list_node,
			'' !== $link_url ? gnn_st_c( 'gnn-actions', array( gnn_st_b( $button_label, $link_url, 'gnn-btn-ghost' ) ), true ) : null,
		),
		true
	);

	$columns = 'image-right' === $variant
		? array( $copy_col, $visual_col )
		: array( $visual_col, $copy_col );

	$children = array(
		gnn_st_c( 'gnn-split', $columns, true ),
	);

	return gnn_sec_services_band( 'about-split', $variant, $children );
}

/**
 * Build the values section (3 principle cards).
 *
 * Variants:
 * - cards:   3-column principle cards.
 * - minimal: Top-bordered minimal 3-column principles.
 * - split:   Side heading paired with 3 principle rows.
 *
 * @param array $a Section arguments.
 * @return array Elementor elements.
 */
function gnn_sec_build_values( $a = array() ) {
	$variant = gnn_sec_services_variant(
		$a,
		array( 'cards', 'minimal', 'split' ),
		'cards',
		array(
			'grid'  => 'cards',
			'lined' => 'minimal',
			'rows'  => 'split',
		)
	);

	$eyebrow      = gnn_sec_services_arg( $a, 'eyebrow', __( 'Our principles', 'gnn' ) );
	$title        = gnn_sec_services_arg( $a, 'title', __( 'What guides our work every day', 'gnn' ), false );
	$text         = gnn_sec_services_arg( $a, 'text', __( 'Consistent standards that shape how we plan, collaborate and deliver.', 'gnn' ) );
	$button_label = gnn_sec_services_arg( $a, 'button_label', '' );
	$button_url   = gnn_sec_services_arg( $a, 'button_url', '#', false );
	$link_url     = '' !== $button_label ? $button_url : '';

	$defaults = array(
		array(
			__( 'Clarity', 'gnn' ),
			__( 'We communicate in plain language and make every option easy to evaluate.', 'gnn' ),
		),
		array(
			__( 'Ownership', 'gnn' ),
			__( 'We stand behind our commitments and stay accountable from start to finish.', 'gnn' ),
		),
		array(
			__( 'Quality', 'gnn' ),
			__( 'We focus on durable work that continues to perform well over time.', 'gnn' ),
		),
	);

	$items = gnn_sec_services_pairs( $a, $defaults );

	if ( 'split' === $variant ) {
		$rows = array();
		foreach ( $items as $index => $item ) {
			$rows[] = gnn_st_c(
				'gnn-row',
				array(
					gnn_st_p( sprintf( '%02d', $index + 1 ), 'gnn-num' ),
					gnn_st_h( $item[0], 3 ),
					'' !== $item[1] ? gnn_st_p( $item[1] ) : null,
				),
				true
			);
		}

		$children = array(
			gnn_st_c(
				'gnn-split gnn-sec-split-top',
				array(
					gnn_st_c(
						'gnn-copy',
						array(
							gnn_st_head( $eyebrow, $title, $text ),
							'' !== $link_url ? gnn_st_c( 'gnn-actions', array( gnn_st_b( $button_label, $link_url, 'gnn-btn-ghost' ) ), true ) : null,
						),
						true
					),
					gnn_st_c( 'gnn-rows', $rows, true ),
				),
				true
			),
		);
	} elseif ( 'minimal' === $variant ) {
		$children = array(
			gnn_st_head( $eyebrow, $title, $text ),
			gnn_st_cards( $items, 'gnn-cols-3 gnn-sec-values-minimal', $link_url, $button_label ),
		);
	} else {
		$children = array(
			gnn_st_head( $eyebrow, $title, $text ),
			gnn_st_cards( $items, 'gnn-cols-3', $link_url, $button_label ),
		);
	}

	return gnn_sec_services_band( 'values', $variant, $children );
}

/**
 * Register services, features, process, about-split and values sections.
 *
 * @return void
 */
function gnn_sections_register_services() {
	if ( ! function_exists( 'gnn_section_register' ) ) {
		return;
	}

	gnn_section_register(
		'services',
		array(
			'label'    => __( 'Services', 'gnn' ),
			'category' => 'services',
			'variants' => array(
				'cards-3' => __( '3-column card grid', 'gnn' ),
				'cards-4' => __( '4-column card grid', 'gnn' ),
				'rows'    => __( 'Numbered row list', 'gnn' ),
				'lined'   => __( 'Minimal lined grid', 'gnn' ),
			),
			'build'    => 'gnn_sec_build_services',
		)
	);

	gnn_section_register(
		'features',
		array(
			'label'    => __( 'Features', 'gnn' ),
			'category' => 'services',
			'variants' => array(
				'grid-2x2'    => __( 'Heading with 2x2 feature grid', 'gnn' ),
				'split-image' => __( 'Split feature list with image', 'gnn' ),
				'split-head'  => __( 'Side heading with 2x2 features', 'gnn' ),
			),
			'build'    => 'gnn_sec_build_features',
		)
	);

	gnn_section_register(
		'process',
		array(
			'label'    => __( 'Process', 'gnn' ),
			'category' => 'process',
			'variants' => array(
				'horizontal'     => __( 'Horizontal steps', 'gnn' ),
				'timeline'       => __( 'Vertical timeline', 'gnn' ),
				'split-timeline' => __( 'Split heading with timeline', 'gnn' ),
			),
			'build'    => 'gnn_sec_build_process',
		)
	);

	gnn_section_register(
		'about-split',
		array(
			'label'    => __( 'About split', 'gnn' ),
			'category' => 'services',
			'variants' => array(
				'image-left'  => __( 'Image left, checklist right', 'gnn' ),
				'image-right' => __( 'Checklist left, image right', 'gnn' ),
				'numbered'    => __( 'Image left, numbered points right', 'gnn' ),
			),
			'build'    => 'gnn_sec_build_about_split',
		)
	);

	gnn_section_register(
		'values',
		array(
			'label'    => __( 'Values & principles', 'gnn' ),
			'category' => 'services',
			'variants' => array(
				'cards'   => __( '3 principle cards', 'gnn' ),
				'minimal' => __( 'Minimal top-bordered principles', 'gnn' ),
				'split'   => __( 'Side heading with principle rows', 'gnn' ),
			),
			'build'    => 'gnn_sec_build_values',
		)
	);
}
add_action( 'gnn_sections_register', 'gnn_sections_register_services' );
