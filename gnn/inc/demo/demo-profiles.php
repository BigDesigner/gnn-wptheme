<?php
/**
 * Complete demo sites for GNNpro, GNNcommerce and GNNdesign: pages, sample records, menu and design.
 * Sample records are owned by a marker, so edited or removed records are never overwritten.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.DB.SlowDBQuery -- bounded administrator-only ownership queries.

/**
 * Bundled profile data.
 *
 * @param string $profile Design key.
 */
function gnn_dp_data( $profile ) {
	switch ( $profile ) {
		case 'pro':
			return gnn_dp_data_pro();
		case 'commerce':
			return gnn_dp_data_commerce();
		case 'design':
			return gnn_dp_data_design();
		default:
			$demo = function_exists( 'gnn_demo_get' ) ? gnn_demo_get( (string) $profile ) : null;
			if ( $demo && is_callable( $demo['data'] ) ) {
				$data = call_user_func( $demo['data'] );
				return is_array( $data ) ? $data : array();
			}
			return array();
	}
}

/**
 * Image names bundled for each design, used for the pre-install readiness check.
 *
 * @param string $profile Design key.
 */
function gnn_dp_images( $profile ) {
	return gnn_dp_data( $profile )['images'] ?? array();
}

/**
 * Find or create one owned record.
 *
 * @param string $profile Design key.
 * @param string $type Post type.
 * @param string $key Stable sample key.
 * @param string $title Initial title.
 * @return int|WP_Error
 */
function gnn_dp_record( $profile, $type, $key, $title ) {
	$marker = $profile . ':' . $key;
	$ids    = get_posts(
		array(
			'post_type'      => $type,
			'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'trash' ),
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_gnn_demo',
			'meta_value'     => $marker,
		)
	);
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
				'_gnn_demo'         => $marker,
				'_gnn_demo_pending' => '1',
			),
		),
		true
	);
}

/**
 * Copy one bundled image into the media library, once.
 *
 * @param string $profile Design key.
 * @param string $name Image name without extension.
 * @param string $title Accessible description.
 * @return int|WP_Error
 */
function gnn_dp_media( $profile, $name, $title ) {
	$marker = $profile . ':image-' . $name;
	$ids    = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_gnn_demo',
			'meta_value'     => $marker,
		)
	);
	if ( $ids ) {
		return (int) $ids[0];
	}
	$found = gnn_dp_media_file( $profile, $name );
	if ( ! $found ) {
		return new WP_Error( 'gnn_demo_image', __( 'A demo image is missing. Reinstall the theme.', 'gnn' ) );
	}
	$bytes  = file_get_contents( $found['path'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local bundled asset.
	$upload = wp_upload_bits( 'gnn-' . $profile . '-' . $name . '.' . $found['ext'], null, $bytes );
	if ( $upload['error'] ) {
		return new WP_Error( 'gnn_demo_upload', $upload['error'] );
	}
	$id = wp_insert_attachment(
		array(
			'post_title'     => $title,
			'post_mime_type' => $found['mime'],
			'post_status'    => 'inherit',
			'meta_input'     => array(
				'_gnn_demo'                => $marker,
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
 * Term ID by name for a taxonomy, creating it when missing.
 *
 * @param string $name Term name.
 * @param string $taxonomy Taxonomy name.
 */
function gnn_dp_term( $name, $taxonomy ) {
	$term = term_exists( sanitize_title( $name ), $taxonomy );
	if ( ! $term ) {
		$term = wp_insert_term( $name, $taxonomy );
	}
	if ( is_wp_error( $term ) ) {
		return $term;
	}
	return (int) ( is_array( $term ) ? $term['term_id'] : $term );
}

/**
 * Sample records for the portfolio of GNNpro and GNNdesign.
 *
 * @param string $profile Design key.
 */
function gnn_dp_projects( $profile ) {
	$data = gnn_dp_data( $profile );
	return isset( $data['projects'] ) && is_array( $data['projects'] ) ? $data['projects'] : array();
}

/**
 * Journal articles for each design.
 *
 * @param string $profile Design key.
 */
function gnn_dp_articles( $profile ) {
	$data = gnn_dp_data( $profile );
	return isset( $data['articles'] ) && is_array( $data['articles'] ) ? $data['articles'] : array();
}

/**
 * Complete Slider group for the storefront hero.
 *
 * @param string $profile Design key.
 * @param array  $media Imported image IDs.
 */
function gnn_dp_slides( $profile, $media ) {
	$data       = gnn_dp_data( $profile );
	$group_name = ! empty( $data['slide_group'] ) ? $data['slide_group'] : ( gnn_starter_profiles()[ $profile ]['label'] ?? $profile );
	$group      = gnn_dp_term( $group_name, 'gnn_slide_group' );
	if ( is_wp_error( $group ) ) {
		return $group;
	}
	$shop  = function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'shop' ) > 0 ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/' );
	$items = isset( $data['slides'] ) && is_array( $data['slides'] ) ? $data['slides'] : array();
	foreach ( $items as $key => $item ) {
		$id = gnn_dp_record( $profile, 'gnn_slide', $key, $item[0] );
		if ( is_wp_error( $id ) ) {
			return $id;
		}
		if ( gnn_demo_writable( $id ) ) {
			if ( isset( $media[ $key ] ) ) {
				set_post_thumbnail( $id, $media[ $key ] );
			}
			wp_set_object_terms( $id, $group, 'gnn_slide_group' );
			update_post_meta( $id, '_gnn_slide_kicker', $item[1] );
			update_post_meta( $id, '_gnn_slide_text', $item[2] );
			update_post_meta( $id, '_gnn_slide_cta_label', $item[3] );
			update_post_meta( $id, '_gnn_slide_cta_url', $shop );
			update_post_meta( $id, '_gnn_starter_baseline', gnn_starter_fingerprint( $id ) );
			$saved = gnn_demo_save( $id, '' );
			if ( is_wp_error( $saved ) ) {
				return $saved;
			}
		}
	}
	update_option( 'gnn_demo_slide_group_' . $profile, $group, false );
	return $group;
}

/**
 * WooCommerce sample products.
 *
 * @param string $profile Design key.
 * @param array  $media Imported image IDs.
 */
function gnn_dp_products( $profile, $media ) {
	$cats  = gnn_dp_data( $profile )['product_categories'];
	$terms = array();
	foreach ( $cats as $slug => $label ) {
		$term = gnn_dp_term( $label, 'product_cat' );
		if ( is_wp_error( $term ) ) {
			return $term;
		}
		$terms[ $slug ] = $term;
		// The first product of a category supplies the category picture.
		foreach ( gnn_dp_data( $profile )['products'] as $key => $product ) {
			if ( $product[2] === $slug && isset( $media[ $key ] ) ) {
				update_term_meta( $term, 'thumbnail_id', $media[ $key ] );
				break;
			}
		}
	}
	$items = gnn_dp_data( $profile )['products'];
	foreach ( $items as $key => $item ) {
		$id = gnn_dp_record( $profile, 'product', $key, $item[0] );
		if ( is_wp_error( $id ) ) {
			return $id;
		}
		if ( gnn_demo_writable( $id ) ) {
			$product = wc_get_product( $id );
			if ( ! $product ) {
				return new WP_Error( 'gnn_demo_product', __( 'Activate WooCommerce and complete store setup before importing GNNcommerce.', 'gnn' ) );
			}
			$product->set_regular_price( $item[1] );
			$product->set_short_description( $item[3] );
			$product->set_image_id( $media[ $key ] );
			$product->set_category_ids( array( $terms[ $item[2] ] ) );
			$product->save();
			update_post_meta( $id, '_gnn_starter_baseline', gnn_starter_fingerprint( $id ) );
			$saved = gnn_demo_save( $id, '' );
			if ( is_wp_error( $saved ) ) {
				return $saved;
			}
		}
	}
	return true;
}

/**
 * Install one complete design. Existing customer content is never deleted or silently rewritten.
 *
 * @param string $profile pro, commerce or design.
 * @param string $revision Settings revision.
 * @return array|WP_Error
 */
function gnn_dp_import( $profile, $revision ) {
	if ( ! gnn_starter_elementor_available() ) {
		return new WP_Error( 'gnn_demo_editor', __( 'This demo requires Elementor.', 'gnn' ) );
	}
	if ( 'commerce' === $profile && ! class_exists( 'WooCommerce' ) ) {
		return new WP_Error( 'gnn_starter_woocommerce', __( 'Activate WooCommerce and complete store setup before importing GNNcommerce.', 'gnn' ) );
	}
	$lock = gnn_configuration_begin( 'complete-demo', null === $revision ? gnn_configuration_form_revision() : $revision );
	if ( is_wp_error( $lock ) ) {
		return $lock;
	}
	$completed = get_option( 'gnn_demo_' . $profile . '_complete', array() );
	if ( $completed ) {
		return $completed;
	}
	// Images first: records and pages refer to them.
	$media = array();
	foreach ( gnn_dp_images( $profile ) as $name ) {
		$image = gnn_dp_media( $profile, $name, ucfirst( str_replace( '-', ' ', $name ) ) );
		if ( is_wp_error( $image ) ) {
			return $image;
		}
		$media[ $name ] = $image;
	}
	$result = array(
		'projects' => array(),
		'posts'    => array(),
		'media'    => $media,
	);
	$group  = 0;
	if ( 'commerce' === $profile ) {
		$done = gnn_dp_products( $profile, $media );
		if ( is_wp_error( $done ) ) {
			return $done;
		}
		$group = gnn_dp_slides( $profile, $media );
		if ( is_wp_error( $group ) ) {
			return $group;
		}
	} else {
		foreach ( gnn_dp_projects( $profile ) as $key => $project ) {
			$id = gnn_dp_record( $profile, 'gnn_project', $key, $project[0] );
			if ( is_wp_error( $id ) ) {
				return $id;
			}
			$result['projects'][ $key ] = $id;
			if ( gnn_demo_writable( $id ) ) {
				$term = gnn_dp_term( $project[2], 'gnn_project_category' );
				if ( is_wp_error( $term ) ) {
					return $term;
				}
				wp_set_object_terms( $id, $term, 'gnn_project_category' );
				wp_update_post(
					array(
						'ID'           => $id,
						'post_excerpt' => $project[1],
					)
				);
				if ( isset( $media[ $key ] ) ) {
					set_post_thumbnail( $id, $media[ $key ] );
					update_post_meta( $id, '_gnn_gallery_ids', array( $media[ $key ] ) );
				}
				update_post_meta( $id, '_gnn_project_year', $project[3] );
				update_post_meta( $id, '_gnn_starter_baseline', gnn_starter_fingerprint( $id ) );
				$saved = gnn_demo_save( $id, gnn_architecture_text( $project[1] ) );
				if ( is_wp_error( $saved ) ) {
					return $saved;
				}
			}
		}
		if ( ! empty( gnn_dp_data( $profile )['slides'] ) ) {
			$group = gnn_dp_slides( $profile, $media );
			if ( is_wp_error( $group ) ) {
				return $group;
			}
		}
	}
	foreach ( gnn_dp_articles( $profile ) as $index => $article ) {
		$id = gnn_dp_record( $profile, 'post', 'journal-' . $index, $article[0] );
		if ( is_wp_error( $id ) ) {
			return $id;
		}
		if ( gnn_demo_writable( $id ) ) {
			if ( isset( $media[ 'news-' . ( $index + 1 ) ] ) ) {
				set_post_thumbnail( $id, $media[ 'news-' . ( $index + 1 ) ] );
			}
			wp_update_post(
				array(
					'ID'           => $id,
					'post_excerpt' => $article[1],
				)
			);
			update_post_meta( $id, '_gnn_starter_baseline', gnn_starter_fingerprint( $id ) );
			$saved = gnn_demo_save( $id, gnn_architecture_text( $article[1] ) );
			if ( is_wp_error( $saved ) ) {
				return $saved;
			}
		}
		$result['posts'][] = $id;
	}
	// The blog page must exist before the news page links to it.
	$blog = gnn_dp_record( $profile, 'page', 'blog', __( 'Blog', 'gnn' ) );
	if ( is_wp_error( $blog ) ) {
		return $blog;
	}
	if ( gnn_demo_writable( $blog ) ) {
		wp_update_post(
			array(
				'ID'          => $blog,
				'post_status' => 'publish',
			)
		);
		delete_post_meta( $blog, '_gnn_demo_pending' );
		update_post_meta( $blog, '_gnn_starter_baseline', gnn_starter_fingerprint( $blog ) );
	}
	update_option( 'page_for_posts', $blog );
	$pages = gnn_install_profile_pages( $profile, 'elementor', $result, $group );
	if ( is_wp_error( $pages ) ) {
		return $pages;
	}
	foreach ( $pages as $id ) {
		wp_update_post(
			array(
				'ID'          => $id,
				'post_status' => 'publish',
			)
		);
	}
	// Menu.
	$menus = get_terms(
		array(
			'taxonomy'   => 'nav_menu',
			'hide_empty' => false,
			'meta_key'   => '_gnn_demo',
			'meta_value' => $profile . ':menu',
		)
	);
	if ( is_wp_error( $menus ) ) {
		return $menus;
	}
	$menu = $menus ? (int) $menus[0]->term_id : wp_create_nav_menu( gnn_starter_profiles()[ $profile ]['label'] );
	if ( is_wp_error( $menu ) ) {
		return $menu;
	}
	update_term_meta( $menu, '_gnn_demo', $profile . ':menu' );
	$demo    = function_exists( 'gnn_demo_get' ) ? gnn_demo_get( (string) $profile ) : null;
	$anchors = $demo && ! empty( $demo['anchors'] ) && is_array( $demo['anchors'] ) ? $demo['anchors'] : array();
	if ( $anchors ) {
		$existing_urls = array_map( 'strval', wp_list_pluck( (array) wp_get_nav_menu_items( $menu ), 'url' ) );
		foreach ( $anchors as $section_id => $anchor_label ) {
			$anchor_url = home_url( '/#' . rawurlencode( ltrim( (string) $section_id, '#' ) ) );
			if ( in_array( $anchor_url, $existing_urls, true ) ) {
				continue;
			}
			$item = wp_update_nav_menu_item(
				$menu,
				0,
				array(
					'menu-item-title'  => (string) $anchor_label,
					'menu-item-url'    => $anchor_url,
					'menu-item-type'   => 'custom',
					'menu-item-status' => 'publish',
				)
			);
			if ( is_wp_error( $item ) ) {
				return $item;
			}
		}
	} else {
		$order = array_keys( gnn_st_pages( $profile ) );
		if ( 'commerce' === $profile && function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'shop' ) > 0 ) {
			$pages = array_merge(
				array(
					'home' => $pages['home'],
					'shop' => wc_get_page_id( 'shop' ),
				),
				array_diff_key( $pages, array( 'home' => 1 ) )
			);
			$order = array_keys( $pages );
		}
		$linked = array_map( 'intval', wp_list_pluck( (array) wp_get_nav_menu_items( $menu ), 'object_id' ) );
		foreach ( $order as $key ) {
			if ( ! isset( $pages[ $key ] ) || in_array( (int) $pages[ $key ], $linked, true ) ) {
				continue;
			}
			$item = wp_update_nav_menu_item(
				$menu,
				0,
				array(
					'menu-item-title'     => 'shop' === $key ? __( 'Shop', 'gnn' ) : get_the_title( $pages[ $key ] ),
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
	}
	$preset = gnn_apply_design_preset( $profile );
	if ( is_wp_error( $preset ) ) {
		return $preset;
	}
	$options               = (array) get_option( 'gnn_options', array() );
	$options['hero_style'] = 'none';
	if ( isset( $pages['contact'] ) ) {
		$options['contact_page_id'] = $pages['contact'];
		$options['header_cta_url']  = get_permalink( $pages['contact'] );
	}
	if ( 'commerce' === $profile ) {
		$options['header_cta_label'] = '';
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
	update_option( 'gnn_demo_' . $profile . '_complete', $result, false );
	return $result;
}
