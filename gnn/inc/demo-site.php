<?php
/**
 * Explicit complete-site installation with owned, resumable sample records.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.DB.SlowDBQuery -- bounded administrator-only ownership queries.

/**
 * Find one owned record without adopting a customer's matching slug or title.
 *
 * @param string $type Post type.
 * @param string $key Stable sample key.
 * @param string $title Initial title.
 * @return int|WP_Error
 */
function gnn_demo_record( $type, $key, $title ) {
	$ids = get_posts(
		array(
			'post_type'      => $type,
			'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'trash' ),
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_gnn_demo',
			'meta_value'     => 'architecture:' . $key,
		)
	); // phpcs:ignore WordPress.DB.SlowDBQuery
	if ( $ids ) {
		return (int) $ids[0];
	}
	return wp_insert_post(
		array(
			'post_type'      => $type,
			'post_status'    => 'draft',
			'post_title'     => $title,
			'comment_status' => 'closed',
			'meta_input'     => array(
				'_gnn_demo'         => 'architecture:' . $key,
				'_gnn_demo_pending' => '1',
			),
		),
		true
	);
}

/**
 * Resume only untouched records; edited, trashed and unknown records stay intact.
 *
 * @param int $id Record ID.
 * @return bool
 */
function gnn_demo_writable( $id ) {
	if ( 'trash' === get_post_status( $id ) ) {
		return false;
	}
	$baseline = get_post_meta( $id, '_gnn_starter_baseline', true );
	return $baseline ? hash_equals( $baseline, gnn_starter_fingerprint( $id ) ) : '1' === get_post_meta( $id, '_gnn_demo_pending', true );
}

/**
 * Save a complete native document before publishing it.
 *
 * @param int    $id Record ID.
 * @param string $content Generated editable blocks.
 * @param bool   $elementor Use Elementor document API.
 * @return true|WP_Error
 */
function gnn_demo_save( $id, $content, $elementor = false ) {
	if ( ! gnn_demo_writable( $id ) ) {
		return true;
	}
	if ( $elementor ) {
		$elements = gnn_starter_elementor_elements( parse_blocks( $content ) );
		$result   = is_wp_error( $elements ) ? $elements : gnn_starter_save_elementor( $id, $elements );
	} else {
		$result = wp_update_post(
			array(
				'ID'           => $id,
				'post_content' => $content,
			),
			true
		);
	}
	if ( is_wp_error( $result ) ) {
		return $result;
	}
	$result = wp_update_post(
		array(
			'ID'          => $id,
			'post_status' => 'publish',
		),
		true
	);
	if ( is_wp_error( $result ) ) {
		return $result;
	}
	delete_post_meta( $id, '_gnn_demo_pending' );
	update_post_meta( $id, '_gnn_starter_baseline', gnn_starter_fingerprint( $id ) );
	return true;
}

/**
 * Copy bundled photographs into editable WordPress media, once per sample key.
 *
 * @param int    $number Bundled photograph index.
 * @param string $title Accessible image description.
 * @return int|WP_Error
 */
function gnn_demo_media( $number, $title ) {
	$key = 'image-' . sprintf( '%02d', $number );
	$ids = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_gnn_demo',
			'meta_value'     => 'architecture:' . $key,
		)
	); // phpcs:ignore WordPress.DB.SlowDBQuery
	if ( $ids ) {
		return (int) $ids[0];
	}
	$file = get_template_directory() . '/assets/images/demo/architecture/' . $key . '.jpg';
	if ( ! is_readable( $file ) ) {
		return new WP_Error( 'gnn_demo_image', __( 'A demo image is missing. Reinstall the theme.', 'gnn' ) );
	}
	$bytes  = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local bundled asset.
	$upload = wp_upload_bits( 'gnn-architecture-' . $key . '.jpg', null, $bytes );
	if ( $upload['error'] ) {
		return new WP_Error( 'gnn_demo_upload', $upload['error'] );
	}
	$id = wp_insert_attachment(
		array(
			'post_title'     => $title,
			'post_mime_type' => 'image/jpeg',
			'post_status'    => 'inherit',
			'meta_input'     => array(
				'_gnn_demo'                => 'architecture:' . $key,
				'_wp_attachment_image_alt' => $title,
			),
		),
		$upload['file'],
		0,
		true
	);
	if ( is_wp_error( $id ) ) {
		return $id;
	}
	require_once ABSPATH . 'wp-admin/includes/image.php';
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	return $id;
}

/**
 * Native image with attachment identity retained in both supported editors.
 *
 * @param int $id Attachment ID.
 * @return string
 */
function gnn_demo_image( $id ) {
	return gnn_architecture_block(
		'core/image',
		array(
			'id'        => $id,
			'sizeSlug'  => 'large',
			'className' => 'gnn-arch-image',
		),
		'<figure class="wp-block-image gnn-arch-image">' . wp_get_attachment_image( $id, 'large' ) . '</figure>'
	);
}

/**
 * Build a complete Architecture site. Call only after an explicit complete-site confirmation.
 * Existing customer content is never deleted or silently rewritten.
 *
 * @param string      $profile Selected profile.
 * @param bool        $confirmed Explicit publication/navigation confirmation.
 * @param string|null $revision Submitted configuration revision.
 * @return array|WP_Error Installed IDs or a recoverable error.
 */
function gnn_import_demo_site( $profile, $confirmed = false, $revision = null ) {
	if ( ! current_user_can( 'manage_options' ) || true !== $confirmed ) {
		return new WP_Error( 'gnn_demo_confirm', __( 'Confirm the complete site installation.', 'gnn' ) );
	}
	if ( 'architecture' !== $profile || ! gnn_starter_elementor_available() ) {
		return new WP_Error( 'gnn_demo_editor', __( 'This demo requires Elementor.', 'gnn' ) );
	}
	$lock = gnn_configuration_begin( 'complete-demo', null === $revision ? gnn_configuration_form_revision() : $revision );
	if ( is_wp_error( $lock ) ) {
		return $lock;
	}
	$completed = get_option( 'gnn_demo_architecture_complete', array() );
	if ( $completed ) {
		return $completed;
	}
	$pages = gnn_install_profile_pages( 'architecture', 'elementor' );
	if ( is_wp_error( $pages ) ) {
		return $pages;
	}
	foreach ( $pages as $id ) {
		if ( ! gnn_demo_writable( $id ) && 'publish' !== get_post_status( $id ) ) {
			return new WP_Error( 'gnn_demo_changed', __( 'A starter page was edited or removed. Review it before installing the complete site.', 'gnn' ) );
		}
	}
	$result   = array(
		'pages'    => $pages,
		'projects' => array(),
		'albums'   => array(),
		'slides'   => array(),
		'posts'    => array(),
		'media'    => array(),
	);
	$projects = array(
		'canopy'  => array( __( 'Canopy Residences', 'gnn' ), __( 'A planted courtyard brings daylight and shared outdoor space into a compact urban residence. Timber screens, deep balconies and natural stone create a quiet transition between street and home.', 'gnn' ), array( 18, 16, 17, 19, 20, 21, 22 ), __( 'Urban housing', 'gnn' ), '2026' ),
		'horizon' => array( __( 'Horizon Tower', 'gnn' ), __( 'A vertical neighbourhood organised around generous terraces and shared spaces. The glazed facade frames distant views while shaded balconies create comfortable outdoor rooms on every level.', 'gnn' ), array( 6, 0, 1, 2, 3, 4, 7 ), __( 'Residential towers', 'gnn' ), '2025' ),
		'olive'   => array( __( 'Olive Garden House', 'gnn' ), __( 'A garden residence shaped around outdoor living. Open family rooms, sheltered terraces and a restrained material palette connect the interior to a sequence of planted spaces.', 'gnn' ), array( 15, 12, 13, 8, 9, 10, 11, 14 ), __( 'Garden homes', 'gnn' ), '2026' ),
	);
	foreach ( $projects as $key => $project ) {
		$id = gnn_demo_record( 'gnn_project', $key, $project[0] );
		if ( is_wp_error( $id ) ) {
			return $id;
		}
		$result['projects'][ $key ] = $id;
		$gallery                    = array();
		foreach ( $project[2] as $index ) {
			$media = gnn_demo_media( $index, $project[0] );
			if ( is_wp_error( $media ) ) {
				return $media;
			}
			$result['media'][ $index ] = $media;
			$gallery[]                 = $media;
		}
		if ( gnn_demo_writable( $id ) ) {
			$term = term_exists( sanitize_title( $project[3] ), 'gnn_project_category' );
			if ( ! $term ) {
				$term = wp_insert_term( $project[3], 'gnn_project_category' );
			}
			if ( is_wp_error( $term ) ) {
				return $term;
			}
			$shared = term_exists( 'gnn-residential', 'gnn_project_category' );
			if ( ! $shared ) {
				$shared = wp_insert_term( __( 'Residential', 'gnn' ), 'gnn_project_category', array( 'slug' => 'gnn-residential' ) );
			}
			if ( is_wp_error( $shared ) ) {
				return $shared;
			}
			wp_set_object_terms( $id, array( (int) ( is_array( $term ) ? $term['term_id'] : $term ), (int) ( is_array( $shared ) ? $shared['term_id'] : $shared ) ), 'gnn_project_category' );
			wp_update_post(
				array(
					'ID'           => $id,
					'post_excerpt' => $project[1],
				)
			);
			set_post_thumbnail( $id, $gallery[0] );
			update_post_meta( $id, '_gnn_gallery_ids', $gallery );
			update_post_meta( $id, '_gnn_project_year', $project[4] );
			update_post_meta( $id, '_gnn_project_location', __( 'Concept location', 'gnn' ) );
			// Record changes below belong to this same import, before its new baseline.
			update_post_meta( $id, '_gnn_starter_baseline', gnn_starter_fingerprint( $id ) );
			$content = gnn_architecture_heading( __( 'The idea', 'gnn' ) ) . gnn_architecture_text( $project[1] ) . gnn_architecture_heading( __( 'Material and light', 'gnn' ) ) . gnn_architecture_text( __( 'Carefully placed openings bring natural light deep into the plan. Durable surfaces, tactile finishes and sheltered outdoor spaces support everyday use throughout the seasons.', 'gnn' ) );
			$saved   = gnn_demo_save( $id, $content, true );
			if ( is_wp_error( $saved ) ) {
				return $saved;
			}
		}
	}
	foreach ( range( 23, 28 ) as $index ) {
		$media = gnn_demo_media( $index, __( 'Architecture studio: drawings, materials and places', 'gnn' ) );
		if ( is_wp_error( $media ) ) {
			return $media;
		}
		$result['media'][ $index ] = $media;
	}
	$album = gnn_demo_record( 'gnn_album', 'studio-album', __( 'Studio Journal', 'gnn' ) );
	if ( is_wp_error( $album ) ) {
		return $album;
	}
	if ( gnn_demo_writable( $album ) ) {
		update_post_meta( $album, '_gnn_gallery_ids', array_values( $result['media'] ) );
		set_post_thumbnail( $album, $result['media'][23] );
		update_post_meta( $album, '_gnn_starter_baseline', gnn_starter_fingerprint( $album ) );
		$saved = gnn_demo_save( $album, gnn_architecture_text( __( 'Spaces, details and the work behind each project.', 'gnn' ) ) );
		if ( is_wp_error( $saved ) ) {
			return $saved;
		}
	}
	$result['albums'][] = $album;
	$group              = term_exists( 'gnn-architecture-demo', 'gnn_slide_group' );
	if ( ! $group ) {
		$group = wp_insert_term( __( 'Architecture highlights', 'gnn' ), 'gnn_slide_group', array( 'slug' => 'gnn-architecture-demo' ) );
	}
	if ( is_wp_error( $group ) ) {
		return $group;
	}
	$group = (int) ( is_array( $group ) ? $group['term_id'] : $group );
	foreach ( $projects as $key => $project ) {
		$id = gnn_demo_record( 'gnn_slide', 'slide-' . $key, $project[0] );
		if ( is_wp_error( $id ) ) {
			return $id;
		}
		if ( gnn_demo_writable( $id ) ) {
			set_post_thumbnail( $id, $result['media'][ $project[2][0] ] );
			wp_set_object_terms( $id, $group, 'gnn_slide_group' );
			update_post_meta( $id, '_gnn_slide_kicker', __( 'Architecture / Selected projects', 'gnn' ) );
			update_post_meta( $id, '_gnn_slide_text', $project[1] );
			update_post_meta( $id, '_gnn_slide_cta_label', __( 'View project', 'gnn' ) );
			update_post_meta( $id, '_gnn_slide_cta_url', get_permalink( $result['projects'][ $key ] ) );
			update_post_meta( $id, '_gnn_starter_baseline', gnn_starter_fingerprint( $id ) );
			$saved = gnn_demo_save( $id, '' );
			if ( is_wp_error( $saved ) ) {
				return $saved;
			}
		}
		$result['slides'][] = $id;
	}
	return gnn_demo_finish( $result, $group );
}

/**
 * Create the linked pages, journal and navigation after the content graph is ready.
 *
 * @param array $result Installed records.
 * @param int   $group Hero slide group.
 * @return array|WP_Error
 */
function gnn_demo_finish( $result, $group ) {
	$pages = $result['pages'];
	$news  = gnn_demo_record( 'page', 'journal', __( 'Journal', 'gnn' ) );
	if ( is_wp_error( $news ) ) {
		return $news;
	}
	$pages['news'] = $news;
	$articles      = array(
		array( __( 'Designing around daylight', 'gnn' ), 26, __( 'Daylight shapes how a room feels throughout the day. We begin with orientation, depth and the movement of the sun, then tune each opening to balance brightness, privacy and summer shade.', 'gnn' ) ),
		array( __( 'A courtyard as a shared room', 'gnn' ), 28, __( 'A planted courtyard can become the social centre of a building. Clear thresholds, comfortable seating and views from the surrounding rooms help this outdoor space become part of daily life.', 'gnn' ) ),
		array( __( 'From sketch to material', 'gnn' ), 25, __( 'Physical samples and working models connect an early idea to the finished space. Testing junctions, textures and proportions together helps the team make deliberate decisions before construction begins.', 'gnn' ) ),
	);
	foreach ( $articles as $index => $article ) {
		$id = gnn_demo_record( 'post', 'journal-' . $index, $article[0] );
		if ( is_wp_error( $id ) ) {
			return $id;
		}
		if ( gnn_demo_writable( $id ) ) {
			set_post_thumbnail( $id, $result['media'][ $article[1] ] );
			wp_update_post(
				array(
					'ID'           => $id,
					'post_excerpt' => $article[2],
				)
			);
			update_post_meta( $id, '_gnn_starter_baseline', gnn_starter_fingerprint( $id ) );
			$content = gnn_architecture_text( $article[2] ) . gnn_demo_image( $result['media'][ $article[1] ] ) . gnn_architecture_heading( __( 'A considered approach', 'gnn' ) ) . gnn_architecture_text( __( 'Every brief begins with listening. We study the place, discuss the ways it will be used and develop a clear sequence of design decisions. Drawings, models and material samples keep the conversation grounded in the experience of the finished building.', 'gnn' ) ) . gnn_architecture_button( __( 'Explore our projects', 'gnn' ), get_permalink( $pages['portfolio'] ) );
			$saved   = gnn_demo_save( $id, $content );
			if ( is_wp_error( $saved ) ) {
				return $saved;
			}
		}
		$result['posts'][] = $id;
	}
	foreach ( $pages as $key => $id ) {
		if ( ! gnn_demo_writable( $id ) ) {
			continue;
		}
		$content = gnn_demo_page_content( $key, $pages, $result, $group );
		$saved   = gnn_demo_save( $id, $content, 'news' !== $key );
		if ( is_wp_error( $saved ) ) {
			return $saved;
		}
	}
	$footer = gnn_demo_record( 'gnn_layout', 'footer', __( 'Architecture footer', 'gnn' ) );
	if ( is_wp_error( $footer ) ) {
		return $footer;
	}
	$links = '';
	foreach ( array( 'studio', 'portfolio', 'services', 'contact', 'news' ) as $key ) {
		$links .= gnn_architecture_button( get_the_title( $pages[ $key ] ), get_permalink( $pages[ $key ] ), 'gnn-arch-text-link' );
	}
	$content = gnn_architecture_group( 'gnn-architecture gnn-demo-footer', gnn_architecture_heading( __( 'Let’s shape your next space.', 'gnn' ), 2 ) . gnn_architecture_text( __( 'Architecture, interiors and places for everyday life.', 'gnn' ) ) . gnn_architecture_group( 'gnn-arch-services', $links ) );
	$saved   = gnn_demo_save( $footer, $content, true );
	if ( is_wp_error( $saved ) ) {
		return $saved;
	}
	$menus = get_terms(
		array(
			'taxonomy'   => 'nav_menu',
			'hide_empty' => false,
			'meta_key'   => '_gnn_demo',
			'meta_value' => 'architecture:menu',
		)
	); // phpcs:ignore WordPress.DB.SlowDBQuery
	if ( is_wp_error( $menus ) ) {
		return $menus;
	}
	$menu = $menus ? (int) $menus[0]->term_id : wp_create_nav_menu( __( 'Architecture navigation', 'gnn' ) );
	if ( is_wp_error( $menu ) ) {
		return $menu;
	}
	update_term_meta( $menu, '_gnn_demo', 'architecture:menu' );
	$existing = wp_get_nav_menu_items( $menu );
	$linked   = $existing ? array_map( 'intval', wp_list_pluck( $existing, 'object_id' ) ) : array();
	foreach ( array( 'home', 'studio', 'portfolio', 'services', 'gallery', 'news', 'contact' ) as $key ) {
		if ( in_array( (int) $pages[ $key ], $linked, true ) ) {
			continue;
		}
		$item = wp_update_nav_menu_item(
			$menu,
			0,
			array(
				'menu-item-title'     => 'home' === $key ? __( 'Home', 'gnn' ) : get_the_title( $pages[ $key ] ),
				'menu-item-object-id' => $pages[ $key ],
				'menu-item-object'    => 'page',
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
			)
		);
		if ( is_wp_error( $item ) ) {
			return $item;
		}
	}
	$preset = gnn_apply_design_preset( 'architecture' );
	if ( is_wp_error( $preset ) ) {
		return $preset;
	}
	$options                      = (array) get_option( 'gnn_options', array() );
	$options['footer_template']   = $footer;
	$options['portfolio_columns'] = 2;
	$options['contact_page_id']   = $pages['contact'];
	$options['hero_style']        = 'none';
	update_option( 'gnn_options', $options );
	$locations            = (array) get_theme_mod( 'nav_menu_locations', array() );
	$locations['primary'] = $menu;
	$locations['mobile']  = $menu;
	set_theme_mod( 'nav_menu_locations', $locations );
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $pages['home'] );
	update_option( 'page_for_posts', $pages['news'] );
	$result['pages']  = $pages;
	$result['home']   = $pages['home'];
	$result['menu']   = $menu;
	$result['footer'] = $footer;
	update_option( 'gnn_demo_architecture_complete', $result, false );
	return $result;
}

/**
 * Editable Architecture pages using shared project, gallery and slider records.
 *
 * @param string $key Page key.
 * @param array  $pages Page IDs.
 * @param array  $data Content IDs.
 * @param int    $group Slide group.
 * @return string
 */
function gnn_demo_page_content( $key, $pages, $data, $group ) {
	$cta = gnn_architecture_group( 'gnn-arch-callout', gnn_architecture_heading( __( 'A new place starts with a conversation.', 'gnn' ) ) . gnn_architecture_button( __( 'Start a project', 'gnn' ), get_permalink( $pages['contact'] ) ) );
	if ( 'home' === $key ) {
		$content  = gnn_module_block(
			'slider',
			array(
				'category' => $group,
				'limit'    => 3,
			)
		);
		$content .= gnn_architecture_group( 'gnn-arch-statement', gnn_architecture_text( __( 'Architecture / Interiors / Landscape', 'gnn' ), 'gnn-arch-eyebrow' ) . gnn_architecture_heading( __( 'Spaces for the way we live.', 'gnn' ), 1 ) . gnn_architecture_text( __( 'We connect thoughtful architecture with the life around it. From a private home to a shared neighbourhood, our work brings light, landscape and carefully chosen materials into balance.', 'gnn' ), 'gnn-arch-lead' ) . gnn_architecture_button( __( 'Meet the studio', 'gnn' ), get_permalink( $pages['studio'] ) ) );
		$content .= gnn_architecture_group(
			'gnn-arch-section',
			gnn_architecture_heading( __( 'Selected projects', 'gnn' ) ) . gnn_module_block(
				'portfolio',
				array(
					'columns' => 2,
					'limit'   => 3,
					'filters' => false,
				)
			) . gnn_architecture_button(
				__(
					'All projects',
					'gnn'
				),
				get_permalink( $pages['portfolio'] )
			)
		);
		$content .= gnn_architecture_group( 'gnn-arch-split', gnn_demo_image( $data['media'][23] ) . gnn_architecture_group( 'gnn-arch-copy', gnn_architecture_heading( __( 'From the first sketch to the final detail.', 'gnn' ) ) . gnn_architecture_text( __( 'Our studio works across architecture, interior design and landscape. We stay close to each project through research, design development and coordination on site.', 'gnn' ) ) . gnn_architecture_button( __( 'Our services', 'gnn' ), get_permalink( $pages['services'] ) ) ) );
		$content .= gnn_architecture_services();
		$journal  = '';
		foreach ( $data['posts'] as $post_id ) {
			$journal .= gnn_architecture_group( 'gnn-arch-project', gnn_demo_image( get_post_thumbnail_id( $post_id ) ) . gnn_architecture_heading( get_the_title( $post_id ), 3 ) . gnn_architecture_text( get_the_excerpt( $post_id ) ) . gnn_architecture_button( __( 'Read story', 'gnn' ), get_permalink( $post_id ) ) );
		}
		$content .= gnn_architecture_group( 'gnn-arch-section', gnn_architecture_heading( __( 'From the journal', 'gnn' ) ) . gnn_architecture_group( 'gnn-arch-projects', $journal ) . gnn_architecture_button( __( 'All stories', 'gnn' ), get_permalink( $pages['news'] ) ) );
	} elseif ( 'portfolio' === $key ) {
		$content = gnn_architecture_intro( $pages, __( 'A collection of places.', 'gnn' ), __( 'Residential architecture, interiors and shared spaces. Explore the projects by category.', 'gnn' ) ) . gnn_module_block(
			'portfolio',
			array(
				'columns' => 2,
				'limit'   => 12,
				'filters' => true,
			)
		);
	} elseif ( 'gallery' === $key ) {
		$content = gnn_architecture_intro( $pages, __( 'In the details.', 'gnn' ), __( 'Materials, rooms and landscape from our projects and studio.', 'gnn' ) ) . gnn_module_block(
			'gallery',
			array(
				'columns' => 3,
				'limit'   => 30,
				'source'  => 'album',
				'record'  => $data['albums'][0],
			)
		);
	} elseif ( 'studio' === $key ) {
		$content  = gnn_architecture_intro( $pages, __( 'Curiosity. Clarity. Care.', 'gnn' ), __( 'We are a collaborative architecture studio interested in how places can support a better everyday life.', 'gnn' ) );
		$content .= gnn_architecture_group( 'gnn-arch-split', gnn_demo_image( $data['media'][24] ) . gnn_architecture_group( 'gnn-arch-copy', gnn_architecture_heading( __( 'A shared design process.', 'gnn' ) ) . gnn_architecture_text( __( 'Architects, interior designers and project coordinators work together from the first conversation. Each project grows through close dialogue with its users and a careful reading of its surroundings.', 'gnn' ) ) ) );
		$content .= gnn_architecture_group( 'gnn-arch-section', gnn_architecture_heading( __( 'How we work', 'gnn' ) ) . gnn_architecture_text( __( 'Listen to the brief. Understand the site. Test the possibilities. Develop the details. Follow the work through construction.', 'gnn' ) ) . gnn_architecture_heading( __( 'Our team', 'gnn' ) ) . gnn_architecture_text( __( 'Architecture and planning lead the spatial strategy; interior design develops materials and daily use; project coordination brings specialists and construction teams together.', 'gnn' ) ) );
	} elseif ( 'services' === $key ) {
		$content = gnn_architecture_page_content( 'services', $pages );
		$content = str_replace( get_template_directory_uri() . '/assets/images/architecture/detail.svg', wp_get_attachment_image_url( $data['media'][26], 'large' ), $content );
		return $content;
	} elseif ( 'contact' === $key ) {
		$email   = sanitize_email( gnn_option( 'company_email' ) );
		$email   = $email ? $email : 'hello@example.com';
		$phone   = (string) gnn_option( 'company_phone' );
		$address = (string) gnn_option( 'company_address' );
		$copy    = gnn_architecture_heading( __( 'Project enquiries', 'gnn' ) ) . gnn_architecture_text( __( 'Tell us about the place, the people who will use it and what you would like to create. We welcome conversations about new homes, shared buildings and interior spaces.', 'gnn' ) );
		$copy   .= gnn_architecture_button( $email, 'mailto:' . $email );
		if ( $phone ) {
			$copy .= gnn_architecture_button( $phone, 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ) );
		}
		$copy   .= gnn_architecture_heading( __( 'Visit the studio', 'gnn' ), 3 ) . gnn_architecture_text( $address ? $address : __( 'Studio visits by appointment.', 'gnn' ) );
		$content = gnn_architecture_intro( $pages, __( 'Let’s talk about your project.', 'gnn' ), __( 'Every project begins with a conversation.', 'gnn' ) ) . gnn_architecture_group( 'gnn-arch-split', gnn_architecture_group( 'gnn-arch-copy', $copy ) . gnn_demo_image( $data['media'][23] ) );
		return gnn_architecture_group( 'gnn-architecture', $content );
	} else {
		return '';
	}
	return gnn_architecture_group( 'gnn-architecture', $content . $cta );
}

/** Load the shared demo footer's presentation on archives and project details too. */
function gnn_demo_site_styles() {
	if ( get_option( 'gnn_demo_architecture_complete' ) ) {
		wp_enqueue_style( 'gnn-architecture', get_template_directory_uri() . '/assets/css/architecture.css', array( 'gnn-design-system' ), GNN_VERSION );
		wp_enqueue_style( 'gnn-starter-editors', get_template_directory_uri() . '/assets/css/starter-editors.css', array( 'gnn-design-system' ), GNN_VERSION );
	}
}
add_action( 'wp_enqueue_scripts', 'gnn_demo_site_styles', 17 );
