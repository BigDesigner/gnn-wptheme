<?php
/**
 * Original Architecture starter: portable blocks and non-destructive draft installation.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Editable concept projects, never presented as completed customer commissions. */
function gnn_architecture_projects() {
	return array(
		'courtyard' => array(
			'title'    => __( 'Courtyard Residence', 'gnn' ),
			'type'     => __( 'Residential', 'gnn' ),
			'category' => 'residential',
			'image'    => 'courtyard',
			'text'     => __( 'A quiet home organised around a planted courtyard. Deep thresholds connect shared rooms, protected outdoor space and changing daylight.', 'gnn' ),
		),
		'urban'     => array(
			'title'    => __( 'Urban House', 'gnn' ),
			'type'     => __( 'Residential', 'gnn' ),
			'category' => 'residential',
			'image'    => 'urban',
			'text'     => __( 'A compact urban study that balances density and privacy. Repeated openings, sheltered terraces and a clear structural rhythm give the facade its identity.', 'gnn' ),
		),
		'civic'     => array(
			'title'    => __( 'Civic Gallery', 'gnn' ),
			'type'     => __( 'Public spaces', 'gnn' ),
			'category' => 'cultural',
			'image'    => 'civic',
			'text'     => __( 'An open cultural building with a generous public edge. A simple roof brings exhibition rooms, a shaded colonnade and a shared gathering space together.', 'gnn' ),
		),
		'interior'  => array(
			'title'    => __( 'Materials Studio', 'gnn' ),
			'type'     => __( 'Workspaces', 'gnn' ),
			'category' => 'commercial',
			'image'    => 'interior',
			'text'     => __( 'A flexible workplace shaped by natural light and tactile materials. A restrained palette supports focused work, informal meetings and everyday collaboration.', 'gnn' ),
		),
	);
}

/**
 * Delegate comment/attribute escaping to WordPress's own block serializer.
 *
 * @param string $name Native block name.
 * @param array  $attrs Block attributes.
 * @param string $html Escaped saved markup, including nested native blocks.
 * @return string
 */
function gnn_architecture_block( $name, $attrs, $html ) {
	return get_comment_delimited_block_content( $name, $attrs, $html );
}

/**
 * Native group wrapper shared by editor and frontend.
 *
 * @param string $css_class CSS classes.
 * @param string $content Serialized blocks.
 * @param string $anchor Optional unique section ID.
 * @return string
 */
function gnn_architecture_group( $css_class, $content, $anchor = '' ) {
	$attrs = array( 'className' => $css_class );
	if ( $anchor ) {
		$attrs['anchor'] = $anchor;
	}
	return gnn_architecture_block( 'core/group', $attrs, '<div' . ( $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '' ) . ' class="wp-block-group ' . esc_attr( $css_class ) . '">' . $content . '</div>' );
}

/**
 * Native editable heading.
 *
 * @param string $text Heading text.
 * @param int    $level Heading level.
 * @param string $css_class CSS class.
 * @return string
 */
function gnn_architecture_heading( $text, $level = 2, $css_class = '' ) {
	$attrs = array( 'level' => $level );
	if ( $css_class ) {
		$attrs['className'] = $css_class;
	}
	return gnn_architecture_block( 'core/heading', $attrs, '<h' . (int) $level . ' class="wp-block-heading' . ( $css_class ? ' ' . esc_attr( $css_class ) : '' ) . '">' . esc_html( $text ) . '</h' . (int) $level . '>' );
}

/**
 * Native editable text.
 *
 * @param string $text Paragraph text.
 * @param string $css_class CSS class.
 * @return string
 */
function gnn_architecture_text( $text, $css_class = '' ) {
	return gnn_architecture_block( 'core/paragraph', $css_class ? array( 'className' => $css_class ) : array(), '<p' . ( $css_class ? ' class="' . esc_attr( $css_class ) . '"' : '' ) . '>' . esc_html( $text ) . '</p>' );
}

/**
 * Native button/link; empty destinations do not create dead controls in patterns.
 *
 * @param string $text Link label.
 * @param string $url Destination.
 * @param string $css_class Optional wrapper class.
 * @return string
 */
function gnn_architecture_button( $text, $url, $css_class = '' ) {
	if ( ! $url ) {
		return '';
	}
	$button = gnn_architecture_block( 'core/button', $css_class ? array( 'className' => $css_class ) : array(), '<div class="wp-block-button' . ( $css_class ? ' ' . esc_attr( $css_class ) : '' ) . '"><a class="wp-block-button__link wp-element-button" href="' . esc_url( $url ) . '">' . esc_html( $text ) . '</a></div>' );
	return gnn_architecture_block( 'core/buttons', array(), '<div class="wp-block-buttons">' . $button . '</div>' );
}

/**
 * Replaceable native Image block, with core's optional expand-on-click behavior.
 *
 * @param string $name Bundled original illustration name.
 * @param string $alt Image description.
 * @param string $url Optional project destination.
 * @param bool   $lightbox Enable native WordPress lightbox when supported.
 * @return string
 */
function gnn_architecture_image( $name, $alt, $url = '', $lightbox = false ) {
	$allowed = array( 'courtyard', 'urban', 'civic', 'interior', 'detail', 'plan' );
	$name    = in_array( $name, $allowed, true ) ? $name : 'courtyard';
	$src     = get_template_directory_uri() . '/assets/images/architecture/' . $name . '.svg';
	$attrs   = array(
		'sizeSlug'        => 'full',
		'linkDestination' => $url ? 'custom' : 'none',
		'className'       => 'gnn-arch-image',
	);
	if ( $lightbox && ! $url ) {
		$attrs['lightbox'] = array( 'enabled' => true );
	}
	$img = '<img src="' . esc_url( $src ) . '" alt="' . esc_attr( $alt ) . '"/>';
	if ( $url ) {
		$attrs['href'] = $url;
		$img           = '<a href="' . esc_url( $url ) . '">' . $img . '</a>';
	}
	return gnn_architecture_block( 'core/image', $attrs, '<figure class="wp-block-image size-full gnn-arch-image">' . $img . '</figure>' );
}

/**
 * Stable ID links continue working after drafts are renamed or published.
 *
 * @param array  $ids Installed page IDs.
 * @param string $key Page identifier.
 * @return string
 */
function gnn_architecture_url( $ids, $key ) {
	return ! empty( $ids[ $key ] ) && 'trash' !== get_post_status( $ids[ $key ] ) ? add_query_arg( 'page_id', (int) $ids[ $key ], home_url( '/' ) ) : '';
}

/**
 * Native two-column project cards.
 *
 * @param array $ids Installed page IDs.
 * @param int   $limit Number of projects, zero for all.
 * @return string
 */
function gnn_architecture_portfolio( $ids = array(), $limit = 0 ) {
	$content = '';
	$count   = 0;
	foreach ( gnn_architecture_projects() as $key => $project ) {
		if ( $limit && $count >= $limit ) {
			break;
		}
		$url = gnn_architecture_url( $ids, $key );
		if ( $ids && ! $url ) {
			continue; }
		$card     = gnn_architecture_image( $project['image'], $project['title'], $url );
		$card    .= gnn_architecture_text( $project['type'] . ' / ' . __( 'Concept study', 'gnn' ), 'gnn-arch-eyebrow' );
		$card    .= gnn_architecture_heading( $project['title'], 3 );
		$card    .= gnn_architecture_button( __( 'Explore project', 'gnn' ), $url, 'gnn-arch-text-link' );
		$content .= gnn_architecture_group( 'gnn-arch-project gnn-arch-project--' . $project['category'], $card );
		++$count;
	}
	return gnn_architecture_group( 'gnn-arch-projects', $content, $ids ? 'architecture-projects' : '' );
}

/** Native gallery retains image editing and core lightbox controls. */
function gnn_architecture_gallery() {
	$images = '';
	foreach ( array( 'courtyard', 'urban', 'civic', 'interior', 'detail', 'plan' ) as $name ) {
		$projects = gnn_architecture_projects();
		$alt      = isset( $projects[ $name ] ) ? $projects[ $name ]['title'] : ( 'plan' === $name ? __( 'Architectural plan concept', 'gnn' ) : __( 'Architectural material junction study', 'gnn' ) );
		$images  .= gnn_architecture_image( $name, $alt, '', true );
	}
	return gnn_architecture_block(
		'core/gallery',
		array(
			'columns'   => 3,
			'imageCrop' => false,
			'linkTo'    => 'none',
			'className' => 'gnn-arch-gallery',
		),
		'<figure class="wp-block-gallery has-nested-images columns-3 gnn-arch-gallery">' . $images . '</figure>'
	);
}

/** Reusable service cards, fully editable as native headings and paragraphs. */
function gnn_architecture_services() {
	$items   = array(
		array( __( 'Architecture', 'gnn' ), __( 'From the first site reading to a clear spatial concept, we shape buildings around their place and the people who use them.', 'gnn' ) ),
		array( __( 'Interior design', 'gnn' ), __( 'Material, light and proportion work together to create interiors that feel considered, practical and enduring.', 'gnn' ) ),
		array( __( 'Planning and feasibility', 'gnn' ), __( 'We examine the brief, site constraints and development options to establish a sound foundation for the design.', 'gnn' ) ),
		array( __( 'Project coordination', 'gnn' ), __( 'Clear documentation and collaboration connect design intent with the teams responsible for delivery.', 'gnn' ) ),
	);
	$content = '';
	foreach ( $items as $i => $item ) {
		$content .= gnn_architecture_group( 'gnn-arch-service', gnn_architecture_text( '0' . ( $i + 1 ), 'gnn-arch-number' ) . gnn_architecture_heading( $item[0], 3 ) . gnn_architecture_text( $item[1] ) );
	}
	return gnn_architecture_group( 'gnn-arch-services', $content );
}

/**
 * Shared contextual navigation and introduction.
 *
 * @param array  $ids Page IDs.
 * @param string $title Page title.
 * @param string $intro Introductory text.
 * @return string
 */
function gnn_architecture_intro( $ids, $title, $intro ) {
	return gnn_architecture_group( 'gnn-arch-page-intro', gnn_architecture_text( __( 'Architecture / Design / Construction', 'gnn' ), 'gnn-arch-eyebrow' ) . gnn_architecture_heading( $title, 1 ) . gnn_architecture_text( $intro, 'gnn-arch-lead' ) );
}

/**
 * Connected page layouts, built solely from native editable blocks.
 *
 * @param string $key Page identifier.
 * @param array  $ids Installed page IDs.
 * @return string
 */
function gnn_architecture_page_content( $key, $ids ) {
	$projects = gnn_architecture_projects();
	$content  = '';
	if ( 'home' === $key ) {
		$hero_text = gnn_architecture_text( __( 'Architecture with a sense of place', 'gnn' ), 'gnn-arch-eyebrow' ) . gnn_architecture_heading( __( 'Space. Light. Life.', 'gnn' ), 1, 'gnn-arch-hero-title' ) . gnn_architecture_text( __( 'Thoughtful architecture and interiors for the way we live, work and come together.', 'gnn' ), 'gnn-arch-lead' ) . gnn_architecture_button( __( 'View selected work', 'gnn' ), gnn_architecture_url( $ids, 'portfolio' ) );
		$content   = gnn_architecture_group( 'gnn-arch-hero', gnn_architecture_group( 'gnn-arch-hero-copy', $hero_text ) . gnn_architecture_image( 'courtyard', __( 'Courtyard residence concept illustration', 'gnn' ) ) );
		$content  .= gnn_architecture_group( 'gnn-arch-statement', gnn_architecture_text( __( 'A considered approach', 'gnn' ), 'gnn-arch-eyebrow' ) . gnn_architecture_heading( __( 'Good buildings begin with good questions.', 'gnn' ) ) . gnn_architecture_text( __( 'We listen to the brief, study the setting and test possibilities. The result is a clear relationship between architecture, landscape and everyday life.', 'gnn' ), 'gnn-arch-lead' ) . gnn_architecture_button( __( 'Meet the studio', 'gnn' ), gnn_architecture_url( $ids, 'studio' ), 'gnn-arch-text-link' ) );
		$content  .= gnn_architecture_group( 'gnn-arch-section', gnn_architecture_text( __( 'Selected work', 'gnn' ), 'gnn-arch-eyebrow' ) . gnn_architecture_heading( __( 'Places with purpose.', 'gnn' ) ) . gnn_architecture_portfolio( $ids, 2 ) . gnn_architecture_button( __( 'View all projects', 'gnn' ), gnn_architecture_url( $ids, 'portfolio' ), 'gnn-arch-text-link' ) );
		$content  .= gnn_architecture_group( 'gnn-arch-section', gnn_architecture_heading( __( 'From an idea to a place.', 'gnn' ) ) . gnn_architecture_services() );
	} elseif ( 'portfolio' === $key ) {
		$content = gnn_architecture_intro( $ids, __( 'Portfolio / 2 Columns', 'gnn' ), __( 'Explore residential, workplace and public-space concepts through their setting, material and spatial ideas.', 'gnn' ) );
		$filters = '';
		foreach ( array(
			'all'         => __( 'All projects', 'gnn' ),
			'residential' => __( 'Residential', 'gnn' ),
			'commercial'  => __( 'Workspaces', 'gnn' ),
			'cultural'    => __( 'Public spaces', 'gnn' ),
		) as $filter => $label ) {
			$filters .= gnn_architecture_button( $label, '#architecture-' . $filter, 'gnn-arch-filter' );
		}
		$content .= gnn_architecture_group( 'gnn-arch-filters', $filters ) . gnn_architecture_portfolio( $ids );
	} elseif ( 'gallery' === $key ) {
		$content = gnn_architecture_intro( $ids, __( 'Gallery', 'gnn' ), __( 'A visual notebook of form, atmosphere, material and detail.', 'gnn' ) ) . gnn_architecture_gallery();
	} elseif ( 'services' === $key ) {
		$content = gnn_architecture_intro( $ids, __( 'A complete design perspective.', 'gnn' ), __( 'A flexible scope for architecture practices, interior studios and construction teams.', 'gnn' ) ) . gnn_architecture_services();
		$steps   = '';
		foreach ( array(
			__( 'Discover', 'gnn' )   => __( 'Understand the brief, the site and the priorities.', 'gnn' ),
			__( 'Develop', 'gnn' )    => __( 'Test space, light, material and the design direction.', 'gnn' ),
			__( 'Coordinate', 'gnn' ) => __( 'Bring documentation, specialist input and decisions together.', 'gnn' ),
			__( 'Deliver', 'gnn' )    => __( 'Support a clear handover and review the completed work.', 'gnn' ),
		) as $title => $text ) {
			$steps .= gnn_architecture_group( 'gnn-arch-process-step', gnn_architecture_heading( $title, 3 ) . gnn_architecture_text( $text ) );
		}
		$content .= gnn_architecture_group( 'gnn-arch-section', gnn_architecture_heading( __( 'A clear process. A shared direction.', 'gnn' ) ) . gnn_architecture_group( 'gnn-arch-process', $steps ) );
	} elseif ( 'studio' === $key ) {
		$content  = gnn_architecture_intro( $ids, __( 'A studio shaped by curiosity.', 'gnn' ), __( 'We bring together observation, craft and a practical understanding of how spaces are made.', 'gnn' ) );
		$content .= gnn_architecture_group( 'gnn-arch-split', gnn_architecture_image( 'interior', __( 'Interior concept with natural materials and daylight', 'gnn' ) ) . gnn_architecture_group( 'gnn-arch-copy', gnn_architecture_heading( __( 'Context first. People always.', 'gnn' ) ) . gnn_architecture_text( __( 'Every commission has its own setting and ambitions. Our role is to turn these into a thoughtful, buildable and legible design.', 'gnn' ) ) . gnn_architecture_text( __( 'Our interests span homes, workplaces, public buildings and interior environments. We value simple ideas, careful coordination and an honest use of materials.', 'gnn' ) ) ) );
		$content .= gnn_architecture_group( 'gnn-arch-section', gnn_architecture_heading( __( 'What guides the work.', 'gnn' ) ) . gnn_architecture_services() );
	} elseif ( isset( $projects[ $key ] ) ) {
		$project = $projects[ $key ];
		$content = gnn_architecture_intro( $ids, $project['title'], $project['text'] ) . gnn_architecture_image( $project['image'], $project['title'], '', true );
		$facts   = '';
		foreach ( array(
			__( 'Discipline', 'gnn' )    => $project['type'],
			__( 'Project stage', 'gnn' ) => __( 'Concept study', 'gnn' ),
			__( 'Design focus', 'gnn' )  => __( 'Light, material and proportion', 'gnn' ),
		) as $title => $value ) {
			$facts .= gnn_architecture_group( 'gnn-arch-fact', gnn_architecture_text( $title, 'gnn-arch-eyebrow' ) . gnn_architecture_text( $value ) );
		}
		$content .= gnn_architecture_group( 'gnn-arch-facts', $facts );
		$content .= gnn_architecture_group( 'gnn-arch-split gnn-arch-section', gnn_architecture_group( 'gnn-arch-copy', gnn_architecture_heading( __( 'The idea behind the space.', 'gnn' ) ) . gnn_architecture_text( $project['text'] ) . gnn_architecture_text( __( 'The study explores how a clear structural order and a restrained material palette can create adaptable spaces with a distinct character.', 'gnn' ) ) ) . gnn_architecture_image( 'plan', __( 'Architectural plan concept', 'gnn' ) ) );
		$content .= gnn_architecture_group( 'gnn-arch-split', gnn_architecture_image( 'detail', __( 'Architectural material junction study', 'gnn' ), '', true ) . gnn_architecture_image( 'interior', __( 'Interior material and daylight study', 'gnn' ), '', true ) );
		$keys     = array_keys( $projects );
		$index    = array_search( $key, $keys, true );
		$previous = $keys[ ( $index + count( $keys ) - 1 ) % count( $keys ) ];
		$next     = $keys[ ( $index + 1 ) % count( $keys ) ];
		$content .= gnn_architecture_group( 'gnn-arch-project-navigation', gnn_architecture_button( __( 'Previous project', 'gnn' ) . ' / ' . $projects[ $previous ]['title'], gnn_architecture_url( $ids, $previous ), 'gnn-arch-text-link' ) . gnn_architecture_button( __( 'Next project', 'gnn' ) . ' / ' . $projects[ $next ]['title'], gnn_architecture_url( $ids, $next ), 'gnn-arch-text-link' ) );
	} else {
		$content = gnn_architecture_intro( $ids, __( 'Let’s talk about your project.', 'gnn' ), __( 'Tell us about your site, your brief and the possibilities you see.', 'gnn' ) );
		$contact = gnn_architecture_heading( __( 'Project enquiries', 'gnn' ) );
		foreach ( array( 'company_email', 'company_phone', 'company_address', 'company_hours' ) as $field ) {
			$value = (string) gnn_translated_option( $field );
			if ( $value ) {
				if ( 'company_email' === $field && is_email( $value ) ) {
					$contact .= gnn_architecture_button( $value, 'mailto:' . sanitize_email( $value ), 'gnn-arch-text-link' );
				} elseif ( 'company_phone' === $field && preg_match( '/^\+?[0-9\s().-]{5,30}$/', $value ) ) {
					$contact .= gnn_architecture_button( $value, 'tel:' . preg_replace( '/[^0-9+]/', '', $value ), 'gnn-arch-text-link' );
				} else {
					$contact .= gnn_architecture_text( $value );
				}
			}
		}
		$contact .= gnn_architecture_text( __( 'Add your studio’s public contact details and a contact form block before publishing.', 'gnn' ), 'gnn-arch-editor-note' );
		$content .= gnn_architecture_group( 'gnn-arch-split', gnn_architecture_group( 'gnn-arch-copy', $contact ) . gnn_architecture_image( 'detail', __( 'Warm architectural material study', 'gnn' ) ) );
	}
	if ( 'contact' !== $key ) {
		$content .= gnn_architecture_group( 'gnn-arch-callout', gnn_architecture_text( __( 'A new conversation', 'gnn' ), 'gnn-arch-eyebrow' ) . gnn_architecture_heading( __( 'What could your place become?', 'gnn' ) ) . gnn_architecture_button( __( 'Start a conversation', 'gnn' ), gnn_architecture_url( $ids, 'contact' ) ) );
	}
	return gnn_architecture_group( 'gnn-architecture gnn-arch-page--' . sanitize_html_class( $key ), $content );
}

// phpcs:disable WordPress.DB.SlowDBQuery -- bounded metadata lookups during explicit administrator installation.
/**
 * Allocate isolated draft pages first, then connect their blocks. Retryable without replacing edits.
 *
 * @return array|WP_Error Page IDs, or an installation error.
 */
function gnn_install_architecture_starter() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return new WP_Error( 'gnn_architecture_permission', __( 'Permission denied.', 'gnn' ) ); }
	$pages = array(
		'home'      => __( 'Architecture Home', 'gnn' ),
		'studio'    => __( 'Studio', 'gnn' ),
		'services'  => __( 'Services', 'gnn' ),
		'portfolio' => __( 'Portfolio / 2 Columns', 'gnn' ),
		'gallery'   => __( 'Gallery', 'gnn' ),
		'contact'   => __( 'Contact', 'gnn' ),
	);
	foreach ( gnn_architecture_projects() as $key => $project ) {
		$pages[ $key ] = $project['title'];
	}
	$owned = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'draft', 'publish', 'private', 'pending', 'future', 'trash' ),
			'posts_per_page' => 1,
			'meta_key'       => '_gnn_starter',
			'meta_value'     => 'architecture:home',
		)
	); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key,WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- bounded, infrequent administrator install.
	if ( $owned && 'trash' === $owned[0]->post_status ) {
		return new WP_Error( 'gnn_architecture_trash', __( 'Restore the Architecture starter home from Trash before adding missing pages.', 'gnn' ) );
	}
	$ids = array();
	foreach ( $pages as $key => $title ) {
		$existing = 'home' === $key ? $owned : get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => array( 'draft', 'publish', 'private', 'pending', 'future', 'trash' ),
				'posts_per_page' => 1,
				'meta_query'     => array(
					array(
						'key'   => '_gnn_starter',
						'value' => 'architecture:' . $key,
					),
					array(
						'key'   => '_gnn_architecture_root',
						'value' => (int) $ids['home'],
					),
				),
			)
		); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- bounded installation lookup.
		if ( $existing ) {
			$ids[ $key ] = $existing[0]->ID;
			continue;
		}
		$slug   = 'home' === $key ? 'architecture' : $key;
		$parent = 'home' === $key ? 0 : $ids['home'];
		$base   = $slug;
		$suffix = 2;
		while ( get_page_by_path( ( $parent ? get_page_uri( $parent ) . '/' : '' ) . $slug ) ) {
			if ( $suffix > 100 ) {
				return new WP_Error( 'gnn_architecture_path', __( 'No unused Architecture page path is available.', 'gnn' ) ); }
			$slug = $base . '-starter-' . $suffix;
			++$suffix;
		}
		$id = wp_insert_post(
			array(
				'post_type'      => 'page',
				'post_name'      => $slug,
				'post_parent'    => $parent,
				'post_title'     => $title,
				'post_content'   => '',
				'post_status'    => 'draft',
				'comment_status' => 'closed',
			),
			true
		);
		if ( is_wp_error( $id ) ) {
			return $id;
		}
		$ids[ $key ] = $id;
		update_post_meta( $id, '_gnn_starter', 'architecture:' . $key );
		update_post_meta( $id, '_gnn_architecture_root', $ids['home'] );
		update_post_meta( $id, '_gnn_architecture_pending', '1' );
		update_post_meta( $id, '_wp_page_template', 'page-templates/tpl-architecture.php' );
	}
	foreach ( $ids as $key => $id ) {
		if ( 'trash' === get_post_status( $id ) || '1' !== get_post_meta( $id, '_gnn_architecture_pending', true ) ) {
			continue;
		}
		if ( '' === trim( (string) get_post_field( 'post_content', $id ) ) ) {
			$result = wp_update_post(
				array(
					'ID'           => $id,
					'post_content' => gnn_architecture_page_content( $key, $ids ),
				),
				true
			);
			if ( is_wp_error( $result ) ) {
				return $result;
			}
		}
		delete_post_meta( $id, '_gnn_architecture_pending' );
	}
	update_post_meta( $ids['home'], '_gnn_architecture_pages', $ids );
	return $ids;
}

// phpcs:enable WordPress.DB.SlowDBQuery

/** Load the presentation only where Architecture blocks or its template are used. */
function gnn_enqueue_architecture() {
	$post = get_queried_object();
	if ( ! is_singular() || ! $post instanceof WP_Post || ( ! is_page_template( 'page-templates/tpl-architecture.php' ) && false === strpos( $post->post_content, 'gnn-arch' ) ) ) {
		return;
	}
	wp_enqueue_style( 'gnn-architecture', get_template_directory_uri() . '/assets/css/architecture.css', array( 'gnn-design-system' ), GNN_VERSION );
	wp_enqueue_script( 'gnn-architecture', get_template_directory_uri() . '/assets/js/architecture.js', array(), GNN_VERSION, true );
	wp_localize_script(
		'gnn-architecture',
		'gnnArchitecture',
		array(
			'filters' => __( 'Project categories', 'gnn' ),
			'one'     => __( '1 project shown', 'gnn' ),
			/* translators: %d: Number of visible portfolio projects. */
			'many'    => __( '%d projects shown', 'gnn' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'gnn_enqueue_architecture', 15 );

/** Keep Architecture blocks styled inside the native editor too. */
function gnn_architecture_editor_styles() {
	add_editor_style( 'assets/css/architecture.css' );
}
add_action( 'after_setup_theme', 'gnn_architecture_editor_styles', 20 );

/** Original sections can be inserted independently through the pattern inserter. */
function gnn_architecture_patterns() {
	if ( ! function_exists( 'register_block_pattern' ) ) {
		return;
	}
	foreach ( array(
		'architecture-services'  => array( __( 'Architecture services', 'gnn' ), gnn_architecture_services() ),
		'architecture-gallery'   => array( __( 'Architecture gallery', 'gnn' ), gnn_architecture_gallery() ),
		'architecture-portfolio' => array( __( 'Architecture portfolio — two columns', 'gnn' ), gnn_architecture_portfolio() ),
	) as $slug => $pattern ) {
		register_block_pattern(
			'gnn/' . $slug,
			array(
				'title'      => $pattern[0],
				'categories' => array( 'gnn' ),
				'content'    => gnn_architecture_group( 'gnn-architecture', $pattern[1] ),
			)
		);
	}
}
add_action( 'init', 'gnn_architecture_patterns', 20 );

/**
 * Preview-only draft links stay navigable without storing preview flags in content.
 *
 * @param string $content Rendered architecture content.
 * @return string
 */
function gnn_architecture_preview_links( $content ) {
	if ( ! is_preview() || ! in_array( get_page_template_slug(), array( 'page-templates/tpl-architecture.php', 'page-templates/tpl-starter.php' ), true ) || ! current_user_can( 'edit_pages' ) ) {
		return $content; }
	$root = (int) get_post_meta( get_queried_object_id(), '_gnn_starter_root', true );
	$ids  = (array) get_post_meta( $root, '_gnn_starter_pages', true );
	if ( ! $root ) {
		$root = (int) get_post_meta( get_queried_object_id(), '_gnn_architecture_root', true );
		$ids  = (array) get_post_meta( $root, '_gnn_architecture_pages', true );
	}
	return preg_replace_callback(
		'/href=(["\'])([^"\']+)\1/',
		function ( $matches ) use ( $ids ) {
			$url    = html_entity_decode( $matches[2], ENT_QUOTES, 'UTF-8' );
			$parts  = wp_parse_url( $url );
			$origin = wp_parse_url( home_url() );
			if ( ! is_array( $parts ) || ( $parts['host'] ?? '' ) !== ( $origin['host'] ?? '' ) || ( $parts['scheme'] ?? '' ) !== ( $origin['scheme'] ?? '' ) || ( $parts['port'] ?? null ) !== ( $origin['port'] ?? null ) ) {
				return $matches[0]; }
			parse_str( $parts['query'] ?? '', $args );
			$id = isset( $args['page_id'] ) && is_scalar( $args['page_id'] ) ? absint( $args['page_id'] ) : 0;
			if ( $id && in_array( $id, array_map( 'absint', $ids ), true ) && 'draft' === get_post_status( $id ) && current_user_can( 'edit_post', $id ) ) {
				return 'href=' . $matches[1] . esc_url( get_preview_post_link( $id ) ) . $matches[1];
			}
			return $matches[0];
		},
		$content
	);
}
add_filter( 'the_content', 'gnn_architecture_preview_links', 12 );

// phpcs:disable WordPress.DB.SlowDBQuery -- one bounded lookup for the administrator's starter preview card.
/** Completed starter home, without exposing trashed or unfinished installations. */
function gnn_architecture_starter_home() {
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'draft', 'publish', 'private', 'pending', 'future' ),
			'posts_per_page' => 1,
			'meta_key'       => '_gnn_starter',
			'meta_value'     => 'architecture:home',
		)
	);
	if ( ! $pages || ! is_array( get_post_meta( $pages[0]->ID, '_gnn_architecture_pages', true ) ) ) {
		return 0; }
	return $pages[0]->ID;
}
// phpcs:enable WordPress.DB.SlowDBQuery
