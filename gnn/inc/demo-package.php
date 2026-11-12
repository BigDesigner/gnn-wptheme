<?php
/**
 * Portable, versioned site content packages. Archives are never extracted.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Return a short public error without filesystem details. */
function gnn_package_error() {
	return new WP_Error( 'gnn_package', __( 'The demo package is invalid or unsupported.', 'gnn' ) );
}

/** Fixed media extensions, independent from permissive third-party upload filters. */
function gnn_package_extensions() {
	return array( 'jpg', 'jpeg', 'jpe', 'png', 'gif', 'webp', 'avif', 'ico', 'pdf', 'woff', 'woff2', 'mp4', 'm4v', 'mov', 'webm', 'mp3', 'm4a', 'oga', 'ogg', 'wav', 'flac', 'txt' );
}

/**
 * Validate dates before any destructive operation, including core's empty date.
 *
 * @param mixed $value Exported date.
 * @return bool
 */
function gnn_package_valid_date( $value ) {
	if ( '0000-00-00 00:00:00' === $value ) {
		return true;
	}
	if ( ! is_string( $value ) || ! preg_match( '/^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$/D', $value, $parts ) ) {
		return false;
	}
	return checkdate( (int) $parts[2], (int) $parts[3], (int) $parts[1] ) && (int) $parts[4] < 24 && (int) $parts[5] < 60 && (int) $parts[6] < 60;
}

/** Fixed document types; transactions and user data are outside this format. */
function gnn_package_types() {
	return array( 'page', 'post', 'attachment', 'gnn_project', 'gnn_product', 'gnn_album', 'gnn_slide', 'gnn_layout', 'elementor_library', 'wp_block', 'nav_menu_item' );
}

/** Fixed taxonomy schema. */
function gnn_package_taxonomies() {
	return array( 'category', 'post_tag', 'gnn_project_category', 'gnn_product_category', 'gnn_slide_group', 'nav_menu', 'elementor_library_type' );
}

/** Only rendering metadata is portable; caches and importer receipts are not. */
function gnn_package_meta_keys() {
	return array( '_thumbnail_id', '_wp_page_template', '_wp_attachment_image_alt', '_elementor_data', '_elementor_edit_mode', '_elementor_template_type', '_elementor_page_settings', '_gnn_layout', '_gnn_hide_title', '_gnn_hide_breadcrumb', '_gnn_title_overlay', '_gnn_block_role', '_gnn_gallery_ids', '_gnn_project_location', '_gnn_project_year', '_gnn_project_client', '_gnn_project_illustration', '_gnn_product_code', '_gnn_product_material', '_gnn_product_dimensions', '_gnn_slide_image_2x', '_gnn_slide_kicker', '_gnn_slide_text', '_gnn_slide_cta_label', '_gnn_slide_cta_url', '_gnn_slide_fit', '_gnn_menu_layout', '_gnn_menu_image', '_menu_item_type', '_menu_item_menu_item_parent', '_menu_item_object_id', '_menu_item_object', '_menu_item_target', '_menu_item_classes', '_menu_item_xfn', '_menu_item_url' );
}

/** Known theme settings except integrations, injected code and operation state. */
function gnn_package_option_keys() {
	return array_values(
		array_filter(
			array_keys( gnn_option_defaults() ),
			static function ( $key ) {
				return ! preg_match( '/(?:api|secret|token|password|license|recaptcha|tracking|_key|ga4|gtm|analytics|custom_js|head_html|body_html|maintenance|heartbeat|disable_|platform_fields)/', $key );
			}
		)
	);
}

/** Theme mods with a visual purpose only. */
function gnn_package_mod_keys() {
	return array( 'gnn_accent_color', 'gnn_default_theme', 'gnn_footer_legal', 'logo_light', 'logo_light_2x', 'logo_dark', 'logo_dark_2x', 'custom_logo', 'nav_menu_locations' );
}

/** Create private storage outside both the site and content roots. */
function gnn_package_temp() {
	$base = realpath( sys_get_temp_dir() );
	if ( ! $base || ! wp_is_writable( $base ) ) {
		return gnn_package_error();
	}
	foreach ( array( ABSPATH, WP_CONTENT_DIR ) as $root ) {
		$root = realpath( $root );
		if ( $root && 0 === strpos( wp_normalize_path( trailingslashit( $base ) ), wp_normalize_path( trailingslashit( $root ) ) ) ) {
			return gnn_package_error();
		}
	}
	$file = tempnam( $base, 'gnn-demo-' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_tempnam -- private system directory, explicitly outside webroot.
	if ( ! $file ) {
		return gnn_package_error();
	}
	chmod( $file, 0600 ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_chmod -- private temporary archive, not a public upload.
	return $file;
}

/**
 * Read only rendering metadata using WordPress serialization handling.
 *
 * @param int $id Post ID.
 */
function gnn_package_metadata( $id ) {
	$out = array();
	foreach ( gnn_package_meta_keys() as $key ) {
		if ( metadata_exists( 'post', $id, $key ) ) {
			$out[ $key ] = get_post_meta( $id, $key, true );
			if ( '_elementor_data' === $key && '' === $out[ $key ] ) {
				$out[ $key ] = '[]';
			}
		}
	}
	return $out;
}

/**
 * Assemble a local ZIP. The caller owns download and cleanup.
 *
 * @return string|WP_Error Temporary archive.
 */
function gnn_package_export() {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'unfiltered_html' ) || is_multisite() || get_option( 'gnn_wipe_job' ) || ! class_exists( 'ZipArchive' ) ) {
		return gnn_package_error();
	}
	$file = gnn_package_temp();
	if ( is_wp_error( $file ) ) {
		return $file;
	}
	$zip = new ZipArchive();
	if ( true !== $zip->open( $file, ZipArchive::OVERWRITE ) ) {
		wp_delete_file( $file );
		return gnn_package_error();
	}
	$document = array(
		'format'         => 'gnn-demo',
		'version'        => 1,
		'theme_version'  => GNN_VERSION,
		'editor_version' => defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '',
		'home'           => home_url( '/' ),
		'title'          => get_bloginfo( 'name' ),
		'description'    => get_bloginfo( 'description' ),
		'posts'          => array(),
		'terms'          => array(),
		'options'        => array_intersect_key( (array) get_option( 'gnn_options', array() ), array_flip( gnn_package_option_keys() ) ),
		'mods'           => array_intersect_key( (array) get_theme_mods(), array_flip( gnn_package_mod_keys() ) ),
		'front'          => (int) get_option( 'page_on_front' ),
		'blog'           => (int) get_option( 'page_for_posts' ),
		'show_on_front'  => get_option( 'show_on_front' ),
		'icon'           => (int) get_option( 'site_icon' ),
		'kit'            => (int) get_option( 'elementor_active_kit' ),
	);
	$posts    = get_posts(
		array(
			'post_type'   => gnn_package_types(),
			'post_status' => array( 'publish', 'draft', 'inherit' ),
			'numberposts' => 2001, // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_numberposts -- explicit bounded export; 2001 detects overflow.
			'orderby'     => 'ID',
			'order'       => 'ASC',
		)
	); // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_numberposts -- explicit bounded export, rejects more than 2000 records.
	$total    = 0;
	$error    = count( $posts ) > 2000;
	foreach ( $posts as $post ) {
		if ( '' !== $post->post_password ) {
			$zip->close();
			wp_delete_file( $file );
			return new WP_Error( 'gnn_package_password', __( 'Remove password protection from demo content before exporting.', 'gnn' ) );
		}
		$row = array(
			'id'       => $post->ID,
			'type'     => $post->post_type,
			'status'   => $post->post_status,
			'title'    => $post->post_title,
			'slug'     => $post->post_name,
			'content'  => $post->post_content,
			'excerpt'  => $post->post_excerpt,
			'parent'   => (int) $post->post_parent,
			'date'     => $post->post_date,
			'date_gmt' => $post->post_date_gmt,
			'comments' => $post->comment_status,
			'order'    => (int) $post->menu_order,
			'url'      => get_permalink( $post ),
			'meta'     => gnn_package_metadata( $post->ID ),
			'terms'    => array(),
		);
		foreach ( gnn_package_taxonomies() as $taxonomy ) {
			if ( taxonomy_exists( $taxonomy ) && is_object_in_taxonomy( $post->post_type, $taxonomy ) ) {
				$row['terms'][ $taxonomy ] = wp_get_object_terms( $post->ID, $taxonomy, array( 'fields' => 'ids' ) );
			}
		}
		if ( 'attachment' === $post->post_type ) {
			$path = wp_get_original_image_path( $post->ID );
			$path = $path ? $path : get_attached_file( $post->ID );
			$base = realpath( wp_upload_dir()['basedir'] );
			$real = $path ? realpath( $path ) : false;
			if ( ! $real || ! $base || 0 !== strpos( wp_normalize_path( $real ), wp_normalize_path( trailingslashit( $base ) ) ) || is_link( $path ) || ! is_readable( $real ) ) {
				$error = true;
				break;
			}
			$size   = filesize( $real );
			$total += $size;
			$check  = wp_check_filetype_and_ext( $real, basename( $real ) );
			if ( ! in_array( $check['ext'], gnn_package_extensions(), true ) || $size > 32 * MB_IN_BYTES || $total > 256 * MB_IN_BYTES ) {
				$error = true;
				break;
			}
			$row['asset'] = array(
				'entry'  => 'media/' . $post->ID . '.' . $check['ext'],
				'name'   => sanitize_file_name( basename( $real ) ),
				'mime'   => $check['type'],
				'sha256' => hash_file( 'sha256', $real ),
				'url'    => wp_get_attachment_url( $post->ID ),
				'sizes'  => array(),
			);
			$metadata     = wp_get_attachment_metadata( $post->ID );
			if ( ! empty( $metadata['original_image'] ) ) {
				$row['asset']['sizes']['gnn-original'] = wp_get_original_image_url( $post->ID );
			}
			foreach ( (array) ( $metadata['sizes'] ?? array() ) as $name => $image ) {
				$row['asset']['sizes'][ $name ] = trailingslashit( dirname( $row['asset']['url'] ) ) . $image['file'];
			}
			if ( ! $zip->addFile( $real, $row['asset']['entry'] ) ) {
				$error = true;
				break;
			}
		}
		$document['posts'][] = $row;
	}
	foreach ( gnn_package_taxonomies() as $taxonomy ) {
		if ( ! taxonomy_exists( $taxonomy ) ) {
			continue;
		}
		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'number'     => 1001,
			)
		);
		if ( is_wp_error( $terms ) || count( $terms ) + count( $document['terms'] ) > 1000 ) {
			$error = true;
			break;
		}
		foreach ( $terms as $term ) {
			$document['terms'][] = array(
				'id'          => $term->term_id,
				'taxonomy'    => $taxonomy,
				'name'        => $term->name,
				'slug'        => $term->slug,
				'description' => $term->description,
				'parent'      => $term->parent,
			);
		}
	}
	$json = wp_json_encode( $document );
	if ( ! $json || strlen( $json ) > 8 * MB_IN_BYTES || $error || ! $zip->addFromString( 'manifest.json', $json ) ) {
		$zip->close();
		wp_delete_file( $file );
		return gnn_package_error();
	}
	if ( ! $zip->close() ) {
		wp_delete_file( $file );
		return gnn_package_error();
	}
	$validated = gnn_package_validate( $file );
	if ( is_wp_error( $validated ) ) {
		wp_delete_file( $file );
		return $validated;
	}
	return $file;
}

/**
 * Validate a complete ZIP before it can be attached to a destructive job.
 *
 * @param string $file Private local archive.
 * @return array|WP_Error Decoded manifest.
 */
function gnn_package_validate( $file ) {
	if ( ! class_exists( 'ZipArchive' ) || ! is_readable( $file ) || filesize( $file ) > 256 * MB_IN_BYTES ) {
		return gnn_package_error();
	}
	$zip = new ZipArchive();
	if ( true !== $zip->open( $file ) ) {
		return gnn_package_error();
	}
	try {
		$names = array();
		$total = 0;
		$count = $zip->numFiles; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- native ZipArchive API.
		if ( $count > 2001 ) {
			return gnn_package_error();
		}
		for ( $i = 0; $i < $count; ++$i ) {
			$stat = $zip->statIndex( $i );
			if ( ! $stat || isset( $names[ $stat['name'] ] ) || ! preg_match( '~^(manifest\.json|media/[1-9][0-9]*\.[a-z0-9]+)$~D', $stat['name'] ) || $stat['size'] > 32 * MB_IN_BYTES ) {
				return gnn_package_error();
			}
			$total                 += $stat['size'];
			$names[ $stat['name'] ] = true;
		}
		$stat = $zip->statName( 'manifest.json' );
		if ( ! $stat || $stat['size'] > 8 * MB_IN_BYTES || $total > 256 * MB_IN_BYTES ) {
			return gnn_package_error();
		}
		$doc = json_decode( $zip->getFromName( 'manifest.json' ), true, 64 );
		if ( ! is_array( $doc ) || 'gnn-demo' !== ( $doc['format'] ?? '' ) || 1 !== ( $doc['version'] ?? 0 ) || ! is_array( $doc['posts'] ?? null ) || ! is_array( $doc['terms'] ?? null ) || count( $doc['posts'] ) > 2000 || count( $doc['terms'] ) > 1000 || ! is_string( $doc['home'] ?? null ) || ! in_array( wp_parse_url( $doc['home'], PHP_URL_SCHEME ), array( 'http', 'https' ), true ) || ! wp_parse_url( $doc['home'], PHP_URL_HOST ) ) {
			return gnn_package_error();
		}
		if ( array_diff( array_keys( $doc ), array( 'format', 'version', 'theme_version', 'editor_version', 'home', 'title', 'description', 'posts', 'terms', 'options', 'mods', 'front', 'blog', 'show_on_front', 'icon', 'kit' ) ) || ! is_array( $doc['options'] ?? null ) || ! is_array( $doc['mods'] ?? null ) || array_diff( array_keys( $doc['options'] ), gnn_package_option_keys() ) || array_diff( array_keys( $doc['mods'] ), gnn_package_mod_keys() ) ) {
			return gnn_package_error();
		}
		if ( isset( $doc['editor_version'] ) && ! is_string( $doc['editor_version'] ) ) {
			return gnn_package_error();
		}
		if ( ! empty( $doc['editor_version'] ) && ( ! defined( 'ELEMENTOR_VERSION' ) || version_compare( ELEMENTOR_VERSION, $doc['editor_version'], '<' ) ) ) {
			return new WP_Error( 'gnn_package_editor_version', __( 'Update Elementor before loading this package.', 'gnn' ) );
		}
		foreach ( array( 'title', 'description', 'theme_version', 'show_on_front' ) as $key ) {
			if ( ! is_string( $doc[ $key ] ?? null ) ) {
				return gnn_package_error();
			}
		}
		foreach ( array( 'front', 'blog', 'icon', 'kit' ) as $key ) {
			if ( ! is_int( $doc[ $key ] ?? null ) || $doc[ $key ] < 0 ) {
				return gnn_package_error();
			}
		}
		foreach ( $doc['options'] as $value ) {
			if ( ! is_scalar( $value ) ) {
				return gnn_package_error();
			}
		}
		foreach ( $doc['mods'] as $key => $value ) {
			if ( 'nav_menu_locations' === $key ? ! is_array( $value ) : ! is_scalar( $value ) ) {
				return gnn_package_error();
			}
			if ( 'nav_menu_locations' === $key ) {
				foreach ( $value as $location => $id ) {
					if ( ! is_string( $location ) || ! is_int( $id ) || $id < 0 ) {
						return gnn_package_error();
					}
				}
			}
		}
		$ids     = array();
		$parents = array();
		foreach ( $doc['posts'] as $row ) {
			if ( ! is_array( $row ) || ! is_int( $row['id'] ?? null ) || $row['id'] < 1 || isset( $ids[ $row['id'] ] ) || ! in_array( $row['type'] ?? '', gnn_package_types(), true ) || ! post_type_exists( $row['type'] ) || ! in_array( $row['status'] ?? '', array( 'publish', 'draft', 'inherit' ), true ) || ! is_array( $row['meta'] ?? null ) || array_diff( array_keys( $row['meta'] ), gnn_package_meta_keys() ) || ! is_array( $row['terms'] ?? null ) ) {
				return gnn_package_error();
			}
			foreach ( array( 'title', 'slug', 'content', 'excerpt', 'url' ) as $key ) {
				if ( ! is_string( $row[ $key ] ?? null ) ) {
					return gnn_package_error();
				}
			}
			foreach ( array( 'date', 'date_gmt' ) as $key ) {
				if ( isset( $row[ $key ] ) && ! gnn_package_valid_date( $row[ $key ] ) ) {
					return gnn_package_error();
				}
			}
			if ( ! is_int( $row['parent'] ?? null ) || $row['parent'] < 0 || ! is_int( $row['order'] ?? null ) ) {
				return gnn_package_error();
			}
			$parents[ $row['id'] ] = $row['parent'];
			foreach ( $row['meta'] as $key => $value ) {
				if ( in_array( $key, array( '_elementor_page_settings', '_gnn_layout', '_gnn_gallery_ids', '_menu_item_classes' ), true ) ? ! is_array( $value ) : ! is_scalar( $value ) ) {
					return gnn_package_error();
				}
				if ( in_array( $key, array( '_gnn_gallery_ids', '_menu_item_classes' ), true ) ) {
					foreach ( $value as $item ) {
						if ( ! is_scalar( $item ) ) {
							return gnn_package_error();
						}
					}
				}
			}
			if ( isset( $row['meta']['_elementor_data'] ) && ! gnn_starter_elementor_available() ) {
				return new WP_Error( 'gnn_package_editor', __( 'This demo requires Elementor.', 'gnn' ) );
			}
			if ( isset( $row['meta']['_elementor_data'] ) ) {
				$tree = is_string( $row['meta']['_elementor_data'] ) ? json_decode( $row['meta']['_elementor_data'], true, 64 ) : null;
				if ( ! is_array( $tree ) ) {
					return gnn_package_error();
				}
				$dependency = gnn_package_editor_check( $tree );
				if ( is_wp_error( $dependency ) ) {
					return $dependency;
				}
			}
			$ids[ $row['id'] ] = true;
			if ( 'attachment' === $row['type'] ) {
				$asset = $row['asset'] ?? array();
				if ( ! is_array( $asset ) || ! is_string( $asset['entry'] ?? null ) || ! isset( $names[ $asset['entry'] ] ) || 0 !== strpos( $asset['entry'], 'media/' . $row['id'] . '.' ) || ! is_string( $asset['name'] ?? null ) || sanitize_file_name( $asset['name'] ) !== $asset['name'] || ! is_array( $asset['sizes'] ?? null ) || ! is_string( $asset['url'] ?? null ) ) {
					return gnn_package_error();
				}
				$bytes = $zip->getFromName( $asset['entry'] );
				foreach ( $asset['sizes'] as $url ) {
					if ( ! is_string( $url ) ) {
						return gnn_package_error();
					}
				}
				if ( ! is_string( $bytes ) || ! is_string( $asset['sha256'] ?? null ) || ! hash_equals( $asset['sha256'], hash( 'sha256', $bytes ) ) ) {
					return gnn_package_error();
				}
				$temp = gnn_package_temp();
				if ( is_wp_error( $temp ) ) {
					return $temp;
				}
				file_put_contents( $temp, $bytes ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- MIME verification in private temporary storage.
				$check = wp_check_filetype_and_ext( $temp, $asset['name'] );
				$image = ! empty( $check['type'] ) && 0 === strpos( $check['type'], 'image/' ) ? wp_get_image_mime( $temp ) : '';
				wp_delete_file( $temp );
				if ( ! in_array( $check['ext'], gnn_package_extensions(), true ) || ( $asset['mime'] ?? '' ) !== $check['type'] || ( '' !== $image && $image !== $check['type'] ) ) {
					return gnn_package_error();
				}
				unset( $names[ $asset['entry'] ] );
			}
		}
		if ( array_diff( array_keys( $names ), array( 'manifest.json' ) ) ) {
			return gnn_package_error();
		}
		$terms = array();
		foreach ( $doc['terms'] as $term ) {
			if ( ! is_array( $term ) || ! is_int( $term['id'] ?? null ) || $term['id'] < 1 || isset( $terms[ $term['id'] ] ) || ! in_array( $term['taxonomy'] ?? '', gnn_package_taxonomies(), true ) || ! taxonomy_exists( $term['taxonomy'] ) || ! is_string( $term['name'] ?? null ) || '' === trim( wp_strip_all_tags( $term['name'] ) ) || ! is_string( $term['slug'] ?? null ) ) {
				return gnn_package_error();
			}
			$terms[ $term['id'] ] = $term['taxonomy'];
			if ( ! is_int( $term['parent'] ?? null ) || $term['parent'] < 0 || ! is_string( $term['description'] ?? null ) ) {
				return gnn_package_error();
			}
		}
		foreach ( $doc['terms'] as $term ) {
			if ( $term['parent'] && ( $terms[ $term['parent'] ] ?? '' ) !== $term['taxonomy'] ) {
				return gnn_package_error();
			}
		}
		foreach ( $parents as $id => $parent ) {
			$seen = array( $id => true );
			while ( $parent ) {
				if ( isset( $seen[ $parent ] ) || ! isset( $parents[ $parent ] ) ) {
					return gnn_package_error();
				}
				$seen[ $parent ] = true;
				$parent          = $parents[ $parent ];
			}
		}
		foreach ( array( 'front', 'blog', 'icon', 'kit' ) as $key ) {
			if ( $doc[ $key ] && ! isset( $ids[ $doc[ $key ] ] ) ) {
				return gnn_package_error();
			}
		}
		foreach ( $doc['posts'] as $row ) {
			if ( ! empty( $row['parent'] ) && ! isset( $ids[ $row['parent'] ] ) ) {
				return gnn_package_error();
			}
			foreach ( $row['terms'] as $taxonomy => $values ) {
				if ( ! is_array( $values ) ) {
					return gnn_package_error();
				}
				foreach ( $values as $id ) {
					if ( ! is_int( $id ) || ( $terms[ $id ] ?? '' ) !== $taxonomy ) {
						return gnn_package_error();
					}
				}
			}
		}
		return $doc;
	} finally {
		$zip->close();
	}
}

/**
 * Reject unavailable editor widgets before deleting any existing content.
 *
 * @param array $elements Elementor document tree.
 * @return true|WP_Error
 */
function gnn_package_editor_check( $elements ) {
	foreach ( $elements as $element ) {
		if ( ! is_array( $element ) || ! is_array( $element['settings'] ?? null ) || ! is_array( $element['elements'] ?? null ) ) {
			return gnn_package_error();
		}
		if ( isset( $element['widgetType'] ) && ! is_string( $element['widgetType'] ) ) {
			return gnn_package_error();
		}
		if ( ! empty( $element['widgetType'] ) && ! \Elementor\Plugin::$instance->widgets_manager->get_widget_types( $element['widgetType'] ) ) {
			/* translators: %s: editor widget name. */
			return new WP_Error( 'gnn_package_widget', sprintf( __( 'Install the plugin for this widget before loading the package: %s', 'gnn' ), sanitize_text_field( $element['widgetType'] ) ) );
		}
		$check = gnn_package_editor_check( $element['elements'] );
		if ( is_wp_error( $check ) ) {
			return $check;
		}
	}
	return true;
}
