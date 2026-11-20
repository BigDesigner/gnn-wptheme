<?php
/**
 * Complete demo installation for data-driven templates.
 * Reuses gnn_dp_record, gnn_dp_media, gnn_dp_term, gnn_demo_writable and gnn_demo_save.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.DB.SlowDBQuery -- bounded administrator-only ownership queries.

/**
 * Install one data-driven template: media, sample content, pages, menu, design preset and front page.
 * Existing customer content and edited starter records are preserved.
 *
 * @param string      $slug     Registered template slug.
 * @param string|null $revision Expected configuration revision token.
 * @return array|WP_Error Installed record IDs and status details, or WP_Error on failure.
 */
function gnn_tpl_import( $slug, $revision = null ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return new WP_Error( 'gnn_demo_permission', __( 'Permission denied.', 'gnn' ) );
	}
	$slug = sanitize_key( (string) $slug );
	$tpl  = function_exists( 'gnn_template' ) ? gnn_template( $slug ) : null;
	if ( ! $tpl ) {
		return new WP_Error( 'gnn_template_missing', __( 'This demo template is not available.', 'gnn' ) );
	}
	$requires = isset( $tpl['requires'] ) && is_array( $tpl['requires'] ) ? $tpl['requires'] : array( 'elementor' );
	if ( in_array( 'elementor', $requires, true ) && ! gnn_starter_elementor_available() ) {
		return new WP_Error( 'gnn_demo_editor', __( 'This demo requires Elementor.', 'gnn' ) );
	}
	if ( in_array( 'woocommerce', $requires, true ) && ! class_exists( 'WooCommerce' ) ) {
		return new WP_Error( 'gnn_starter_woocommerce', __( 'Activate WooCommerce and complete store setup before importing this demo.', 'gnn' ) );
	}
	if ( ! gnn_template_requires_met( $tpl ) ) {
		return new WP_Error( 'gnn_template_requires', __( 'Required plugins for this demo are not active.', 'gnn' ) );
	}

	$lock = gnn_configuration_begin( 'complete-demo', null === $revision ? gnn_configuration_form_revision() : $revision );
	if ( is_wp_error( $lock ) ) {
		return $lock;
	}
	$completed = get_option( 'gnn_demo_' . $slug . '_complete', array() );
	if ( $completed ) {
		return $completed;
	}

	// Media first: missing local files fall back to the bundled SVG placeholder without aborting import.
	$media_ids       = array();
	$media_urls      = array();
	$missing_media   = array();
	$placeholder_url = get_theme_file_uri( 'assets/img/placeholder-cover.svg' );
	$media_defs      = isset( $tpl['media'] ) && is_array( $tpl['media'] ) ? $tpl['media'] : array();

	foreach ( $media_defs as $key => $meta ) {
		$key  = sanitize_key( (string) $key );
		$file = get_template_directory() . '/assets/images/demo/' . $slug . '/' . $key . '.jpg';
		if ( is_readable( $file ) ) {
			$alt   = ! empty( $meta['alt'] ) ? (string) $meta['alt'] : ucfirst( str_replace( '-', ' ', $key ) );
			$image = gnn_dp_media( $slug, $key, $alt );
			if ( is_wp_error( $image ) ) {
				return $image;
			}
			$media_ids[ $key ]  = (int) $image;
			$url                = wp_get_attachment_url( $image );
			$media_urls[ $key ] = $url ? $url : $placeholder_url;
		} else {
			$media_urls[ $key ] = $placeholder_url;
			$missing_media[]    = $key;
		}
	}

	$result = array(
		'projects'      => array(),
		'posts'         => array(),
		'missing_media' => $missing_media,
		'notices'       => empty( $missing_media ) ? array() : array( 'eksik görsel' ),
	);

	// Sample portfolio/project records.
	$projects = isset( $tpl['content']['projects'] ) && is_array( $tpl['content']['projects'] ) ? $tpl['content']['projects'] : array();
	foreach ( $projects as $index => $project ) {
		$key       = isset( $project['key'] ) ? sanitize_key( (string) $project['key'] ) : 'project-' . ( $index + 1 );
		$title     = (string) ( $project['title'] ?? '' );
		$excerpt   = (string) ( $project['excerpt'] ?? '' );
		$category  = (string) ( $project['category'] ?? '' );
		$year      = (string) ( $project['year'] ?? '2026' );
		$media_key = isset( $project['media'] ) ? sanitize_key( (string) $project['media'] ) : '';

		$id = gnn_dp_record( $slug, 'gnn_project', $key, $title );
		if ( is_wp_error( $id ) ) {
			return $id;
		}
		$result['projects'][ $key ] = $id;
		if ( gnn_demo_writable( $id ) ) {
			if ( '' !== $category ) {
				$term = gnn_dp_term( $category, 'gnn_project_category' );
				if ( is_wp_error( $term ) ) {
					return $term;
				}
				wp_set_object_terms( $id, $term, 'gnn_project_category' );
			}
			wp_update_post(
				array(
					'ID'           => $id,
					'post_excerpt' => $excerpt,
				)
			);
			if ( '' !== $media_key && isset( $media_ids[ $media_key ] ) ) {
				set_post_thumbnail( $id, $media_ids[ $media_key ] );
				update_post_meta( $id, '_gnn_gallery_ids', array( $media_ids[ $media_key ] ) );
			}
			update_post_meta( $id, '_gnn_project_year', $year );
			update_post_meta( $id, '_gnn_starter_baseline', gnn_starter_fingerprint( $id ) );
			$body  = function_exists( 'gnn_architecture_text' ) ? gnn_architecture_text( $excerpt ) : '<!-- wp:paragraph --><p>' . esc_html( $excerpt ) . '</p><!-- /wp:paragraph -->';
			$saved = gnn_demo_save( $id, $body );
			if ( is_wp_error( $saved ) ) {
				return $saved;
			}
		}
	}

	// Sample blog posts.
	$articles = isset( $tpl['content']['posts'] ) && is_array( $tpl['content']['posts'] ) ? $tpl['content']['posts'] : array();
	foreach ( $articles as $index => $article ) {
		$title     = (string) ( $article['title'] ?? '' );
		$excerpt   = (string) ( $article['excerpt'] ?? '' );
		$media_key = isset( $article['media'] ) ? sanitize_key( (string) $article['media'] ) : '';

		$id = gnn_dp_record( $slug, 'post', 'journal-' . $index, $title );
		if ( is_wp_error( $id ) ) {
			return $id;
		}
		if ( gnn_demo_writable( $id ) ) {
			if ( '' !== $media_key && isset( $media_ids[ $media_key ] ) ) {
				set_post_thumbnail( $id, $media_ids[ $media_key ] );
			}
			wp_update_post(
				array(
					'ID'           => $id,
					'post_excerpt' => $excerpt,
				)
			);
			update_post_meta( $id, '_gnn_starter_baseline', gnn_starter_fingerprint( $id ) );
			$body  = function_exists( 'gnn_architecture_text' ) ? gnn_architecture_text( $excerpt ) : '<!-- wp:paragraph --><p>' . esc_html( $excerpt ) . '</p><!-- /wp:paragraph -->';
			$saved = gnn_demo_save( $id, $body );
			if ( is_wp_error( $saved ) ) {
				return $saved;
			}
		}
		$result['posts'][] = $id;
	}

	// Posts archive page (`page_for_posts`) must exist before blog sections resolve its permalink.
	$blog_archive = gnn_dp_record( $slug, 'page', 'blog', __( 'Blog', 'gnn' ) );
	if ( is_wp_error( $blog_archive ) ) {
		return $blog_archive;
	}
	if ( gnn_demo_writable( $blog_archive ) ) {
		wp_update_post(
			array(
				'ID'          => $blog_archive,
				'post_status' => 'publish',
			)
		);
		delete_post_meta( $blog_archive, '_gnn_demo_pending' );
		update_post_meta( $blog_archive, '_gnn_starter_baseline', gnn_starter_fingerprint( $blog_archive ) );
	}
	update_option( 'page_for_posts', $blog_archive );

	// Create/locate owned Elementor starter pages first so every page:key link resolves.
	$pages = array();
	foreach ( $tpl['pages'] as $page_key => $page_def ) {
		$existing = gnn_starter_owned_page( $slug, 'elementor', $page_key );
		if ( $existing ) {
			if ( 'home' === $page_key && 'trash' === $existing->post_status ) {
				return new WP_Error( 'gnn_starter_trash', __( 'Restore this edition’s starter home from Trash before adding missing pages.', 'gnn' ) );
			}
			$pages[ $page_key ] = (int) $existing->ID;
			continue;
		}
		$post_slug = 'home' === $page_key ? $slug . '-elementor' : $page_key;
		$parent    = 'home' === $page_key ? 0 : (int) $pages['home'];
		$base      = $post_slug;
		$suffix    = 2;
		while ( get_page_by_path( ( $parent ? get_page_uri( $parent ) . '/' : '' ) . $post_slug ) ) {
			if ( $suffix > 100 ) {
				return new WP_Error( 'gnn_starter_path', __( 'No unused starter page path is available.', 'gnn' ) );
			}
			$post_slug = $base . '-starter-' . $suffix;
			++$suffix;
		}
		$id = wp_insert_post(
			array(
				'post_type'      => 'page',
				'post_status'    => 'draft',
				'post_title'     => (string) $page_def['title'],
				'post_name'      => $post_slug,
				'post_parent'    => $parent,
				'comment_status' => 'closed',
			),
			true
		);
		if ( is_wp_error( $id ) ) {
			return $id;
		}
		$pages[ $page_key ] = (int) $id;
		update_post_meta( $id, '_gnn_starter', $slug . ':elementor:' . $page_key );
		update_post_meta( $id, '_gnn_starter_pending', '1' );
		update_post_meta( $id, '_gnn_demo_pending', '1' );
		update_post_meta( $id, '_gnn_starter_editor', 'elementor' );
		update_post_meta( $id, '_gnn_starter_root', $pages['home'] );
		update_post_meta( $id, '_wp_page_template', 'page-templates/tpl-starter.php' );
	}

	// Record page map on home before computing baselines so fingerprints stay consistent.
	$home_writable = gnn_demo_writable( $pages['home'] );
	if ( $home_writable ) {
		update_post_meta( $pages['home'], '_gnn_starter_pages', $pages );
	}

	// Compile and save Elementor documents for untouched pages only.
	foreach ( $pages as $page_key => $id ) {
		if ( 'trash' === get_post_status( $id ) || ! gnn_demo_writable( $id ) ) {
			continue;
		}
		$elements = gnn_template_compile_page( $tpl, $page_key, $pages, $media_urls );
		$saved    = gnn_starter_save_elementor( $id, $elements );
		if ( is_wp_error( $saved ) ) {
			return $saved;
		}
		$published = wp_update_post(
			array(
				'ID'          => $id,
				'post_status' => 'publish',
			),
			true
		);
		if ( is_wp_error( $published ) ) {
			return $published;
		}
		delete_post_meta( $id, '_gnn_starter_pending' );
		delete_post_meta( $id, '_gnn_demo_pending' );
		update_post_meta( $id, '_gnn_starter_baseline', gnn_starter_fingerprint( $id ) );
	}

	// Navigation menu.
	$menus = get_terms(
		array(
			'taxonomy'   => 'nav_menu',
			'hide_empty' => false,
			'meta_key'   => '_gnn_demo',
			'meta_value' => $slug . ':menu',
		)
	);
	if ( is_wp_error( $menus ) ) {
		return $menus;
	}
	$menu = $menus ? (int) $menus[0]->term_id : wp_create_nav_menu( $tpl['label'] );
	if ( is_wp_error( $menu ) ) {
		return $menu;
	}
	update_term_meta( $menu, '_gnn_demo', $slug . ':menu' );
	$linked = array_map( 'intval', wp_list_pluck( (array) wp_get_nav_menu_items( $menu ), 'object_id' ) );
	foreach ( $tpl['menu'] as $page_key ) {
		if ( ! isset( $pages[ $page_key ] ) || in_array( (int) $pages[ $page_key ], $linked, true ) ) {
			continue;
		}
		$item = wp_update_nav_menu_item(
			$menu,
			0,
			array(
				'menu-item-title'     => get_the_title( $pages[ $page_key ] ),
				'menu-item-object-id' => $pages[ $page_key ],
				'menu-item-object'    => 'page',
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
			)
		);
		if ( is_wp_error( $item ) ) {
			return $item;
		}
	}

	// Apply design preset and template header options.
	$preset = gnn_apply_design_preset( $slug );
	if ( is_wp_error( $preset ) ) {
		return $preset;
	}
	$options                 = (array) get_option( 'gnn_options', array() );
	$options['site_segment'] = $slug;
	$options['hero_style']   = 'none';
	if ( isset( $pages['contact'] ) ) {
		$options['contact_page_id'] = $pages['contact'];
		$options['header_cta_url']  = get_permalink( $pages['contact'] );
	}
	update_option( 'gnn_options', $options );

	$locations            = (array) get_theme_mod( 'nav_menu_locations', array() );
	$locations['primary'] = $menu;
	$locations['mobile']  = $menu;
	set_theme_mod( 'nav_menu_locations', $locations );
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $pages['home'] );

	$result['pages'] = $pages;
	$result['home']  = $pages['home'];
	$result['menu']  = $menu;
	update_option( 'gnn_demo_' . $slug . '_complete', $result, false );
	return $result;
}
// phpcs:enable WordPress.DB.SlowDBQuery
