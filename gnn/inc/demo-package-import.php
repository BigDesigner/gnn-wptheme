<?php
/**
 * Bounded package installation and reference mapping.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Map known content references without changing unrelated numbers or widget IDs.
 *
 * @param mixed  $value Value or document tree.
 * @param array  $state Durable maps.
 * @param string $key Field name.
 * @return mixed
 */
function gnn_package_remap( $value, $state, $key = '' ) {
	$posts = $state['posts'] ?? array();
	$terms = $state['terms'] ?? array();
	if ( is_array( $value ) ) {
		if ( in_array( $key, array( 'category', 'categories', 'group' ), true ) ) {
			return array_map(
				static function ( $id ) use ( $terms ) {
					return is_scalar( $id ) ? ( $terms[ $id ] ?? 0 ) : 0;
				},
				$value
			);
		}
		if ( in_array( $key, array( '_gnn_gallery_ids', 'ids', 'include', 'exclude', 'excluded_ids', 'selected_ids', 'nav_menu_locations' ), true ) ) {
			$map = 'nav_menu_locations' === $key ? $terms : $posts;
			return array_map(
				static function ( $id ) use ( $map ) {
					return is_scalar( $id ) ? ( $map[ $id ] ?? 0 ) : 0;
				},
				$value
			);
		}
		foreach ( $value as $name => $item ) {
			// Elementor element IDs are strings; numeric image IDs are references.
			if ( 'id' === $name && isset( $value['elType'] ) ) {
				continue;
			}
			$value[ $name ] = gnn_package_remap( $item, $state, (string) $name );
		}
		return $value;
	}
	if ( ( is_int( $value ) || ( is_string( $value ) && ctype_digit( $value ) ) ) && (int) $value > 0 ) {
		if ( in_array( $key, array( 'category', 'group', 'hero_group', 'menu', 'menu_id' ), true ) ) {
			return $terms[ $value ] ?? 0;
		}
		$refs = array( 'id', 'ref', 'record', 'template_id', 'shared_block', 'shared_block_id', 'mediaId', 'image_id', 'attachment_id', 'contact_page_id', 'local_font', 'logo_light', 'logo_light_2x', 'logo_dark', 'logo_dark_2x', 'footer_logo_light', 'footer_logo_light_2x', 'footer_logo_dark', 'footer_logo_dark_2x', 'error404_image', 'custom_logo', '_thumbnail_id', '_gnn_slide_image_2x', '_gnn_menu_image', '_menu_item_menu_item_parent' );
		if ( in_array( $key, $refs, true ) || preg_match( '/_template$/', $key ) ) {
			return $posts[ $value ] ?? 0;
		}
	}
	if ( is_string( $value ) ) {
		$value = strtr( $value, $state['urls'] ?? array() );
		if ( in_array( $key, array( 'shortcode', 'editor', 'html', 'text' ), true ) ) {
			$value = gnn_package_shortcodes( $value, $state );
		}
		if ( in_array( $key, array( 'ids', 'include', 'exclude', 'excluded_ids', 'selected_ids' ), true ) && preg_match( '/^\d+(?:,\s*\d+)*$/D', $value ) ) {
			return implode(
				',',
				array_map(
					static function ( $id ) use ( $posts ) {
						return $posts[ (int) $id ] ?? 0;
					},
					explode( ',', $value )
				)
			);
		}
	}
	return $value;
}

/**
 * Rewrite block attributes and supported shortcode references alongside URLs.
 *
 * @param string $content Authored content.
 * @param array  $state Maps.
 * @return string
 */
function gnn_package_content( $content, $state ) {
	$content = strtr( $content, $state['urls'] );
	$blocks  = parse_blocks( $content );
	$walk    = static function ( $blocks ) use ( &$walk, $state ) {
		foreach ( $blocks as &$block ) {
			$block['attrs']       = gnn_package_remap( $block['attrs'], $state );
			$block['innerBlocks'] = $walk( $block['innerBlocks'] );
		}
		return $blocks;
	};
	$content = serialize_blocks( $walk( $blocks ) );
	$content = gnn_package_shortcodes( $content, $state );
	$content = preg_replace_callback(
		'/\bwp-image-([0-9]+)\b/',
		static function ( $parts ) use ( $state ) {
			return 'wp-image-' . ( $state['posts'][ (int) $parts[1] ] ?? 0 );
		},
		$content
	);
	return $content;
}

/**
 * Remap supported content shortcodes in both editors.
 *
 * @param string $content Authored text.
 * @param array  $state Reference maps.
 * @return string
 */
function gnn_package_shortcodes( $content, $state ) {
	return preg_replace_callback(
		'/\[(gnn_block|gnn_portfolio|gnn_gallery|gnn_slider|gallery|elementor-template)\b([^\]]*)\]/',
		static function ( $parts ) use ( $state ) {
			$attrs = shortcode_parse_atts( $parts[2] );
			if ( ! is_array( $attrs ) ) {
				return $parts[0];
			}
			$text = '[' . $parts[1];
			foreach ( $attrs as $key => $value ) {
				if ( is_string( $key ) ) {
					$text .= ' ' . sanitize_key( $key ) . '="' . esc_attr( gnn_package_remap( $value, $state, $key ) ) . '"';
				}
			}
			return $text . ']';
		},
		$content
	);
}

/**
 * Find an already inserted record after a lost response or interrupted request.
 *
 * @param string $key Package receipt.
 * @return int
 */
function gnn_package_record( $key ) {
	$found = get_posts(
		array(
			'post_type'   => gnn_package_types(),
			'post_status' => array( 'publish', 'draft', 'inherit' ),
			'numberposts' => 1,
			'fields'      => 'ids',
			'meta_key'    => '_gnn_package_key', // phpcs:ignore WordPress.DB.SlowDBQuery -- explicit bounded recovery lookup.
			'meta_value'  => $key, // phpcs:ignore WordPress.DB.SlowDBQuery -- explicit bounded recovery lookup.
		)
	); // phpcs:ignore WordPress.DB.SlowDBQuery -- bounded explicit installation recovery lookup.
	return $found ? (int) $found[0] : 0;
}

/**
 * Resolve the private staged archive, bound to its uploader and current site.
 *
 * @param string $profile Staging token.
 * @return array|WP_Error
 */
function gnn_package_stage( $profile ) {
	if ( ! preg_match( '/^package-[a-f0-9]{32}$/D', $profile ) || ! current_user_can( 'unfiltered_html' ) ) {
		return gnn_package_error();
	}
	$stage = get_option( 'gnn_' . $profile );
	if ( ! is_array( $stage ) || get_current_user_id() !== $stage['owner'] || ! hash_equals( home_url( '/' ), $stage['site'] ) || ! is_readable( $stage['file'] ) || ! hash_equals( $stage['hash'], hash_file( 'sha256', $stage['file'] ) ) ) {
		return gnn_package_error();
	}
	$doc = gnn_package_validate( $stage['file'] );
	return is_wp_error( $doc ) ? $doc : array(
		'stage'    => $stage,
		'document' => $doc,
	);
}

/**
 * Advance one item. The existing reset endpoint owns the batch mutex.
 *
 * @param array $job Owned job after deletion.
 * @return array|WP_Error
 */
function gnn_package_import_step( $job ) {
	$stage = get_option( 'gnn_' . $job['profile'] );
	if ( ! is_array( $stage ) || ! is_readable( $stage['file'] ) || get_current_user_id() !== $stage['owner'] || ! hash_equals( $stage['site'], home_url( '/' ) ) || ! current_user_can( 'unfiltered_html' ) ) {
		return gnn_package_error();
	}
	$zip = new ZipArchive();
	if ( true !== $zip->open( $stage['file'] ) ) {
		return gnn_package_error();
	}
	$doc = json_decode( $zip->getFromName( 'manifest.json' ), true, 64 );
	if ( ! is_array( $doc ) ) {
		$zip->close();
		return gnn_package_error();
	}
	$state   = $job['package'] ?? array(
		'phase'  => 'terms',
		'cursor' => 0,
		'posts'  => array(),
		'terms'  => array(),
		'urls'   => array(),
	);
	$index   = $state['cursor'];
	$cleanup = false;
	try {
		if ( 'terms' === $state['phase'] ) {
			if ( isset( $doc['terms'][ $index ] ) ) {
				$row = $doc['terms'][ $index ];
				$id  = term_exists( $row['slug'], $row['taxonomy'] );
				if ( ! $id ) {
					$id = wp_insert_term(
						$row['name'],
						$row['taxonomy'],
						array(
							'slug'        => $row['slug'],
							'description' => wp_kses_post( $row['description'] ?? '' ),
						)
					);
				}
				if ( is_wp_error( $id ) ) {
					return $id;
				}
				$state['terms'][ $row['id'] ] = (int) $id['term_id'];
				++$state['cursor'];
			} else {
				$state['phase']  = 'term_parents';
				$state['cursor'] = 0;
			}
		} elseif ( 'posts' === $state['phase'] ) {
			if ( isset( $doc['posts'][ $index ] ) ) {
				$row = $doc['posts'][ $index ];
				$key = $job['profile'] . ':' . $row['id'];
				$id  = gnn_package_record( $key );
				if ( ! $id ) {
					$id = wp_insert_post(
						wp_slash(
							array(
								'post_type'     => $row['type'],
								'post_status'   => 'attachment' === $row['type'] ? 'inherit' : 'draft',
								'post_title'    => sanitize_text_field( $row['title'] ),
								'post_content'  => $row['content'],
								'post_excerpt'  => $row['excerpt'],
								'post_name'     => sanitize_title( $row['slug'] ),
								'post_author'   => get_current_user_id(),
								'post_date'     => $row['date'] ?? current_time( 'mysql' ),
								'post_date_gmt' => $row['date_gmt'] ?? current_time( 'mysql', true ),
								'menu_order'    => (int) $row['order'],
								'meta_input'    => array( '_gnn_package_key' => $key ),
							)
						),
						true
					);
					if ( is_wp_error( $id ) || ! $id ) {
						return is_wp_error( $id ) ? $id : gnn_package_error();
					}
				}
				if ( 'attachment' === $row['type'] && ! get_post_meta( $id, '_gnn_package_ready', true ) ) {
					$asset = $row['asset'];
					$bits  = $zip->getFromName( $asset['entry'] );
					if ( ! is_string( $bits ) || ! hash_equals( $asset['sha256'], hash( 'sha256', $bits ) ) ) {
						return gnn_package_error();
					}
					$path = get_attached_file( $id );
					if ( ! $path || ! is_file( $path ) ) {
						$file = wp_upload_bits( 'demo-' . $id . '-' . $asset['name'], null, $bits );
						if ( ! empty( $file['error'] ) ) {
							return new WP_Error( 'gnn_package_media', __( 'Media could not be saved. Check available disk space and retry.', 'gnn' ) );
						}
						$path = $file['file'];
						update_attached_file( $id, $path );
					}
					wp_update_post(
						array(
							'ID'             => $id,
							'post_mime_type' => $asset['mime'],
						)
					);
					require_once ABSPATH . 'wp-admin/includes/image.php';
					$metadata = wp_generate_attachment_metadata( $id, $path );
					wp_update_attachment_metadata( $id, $metadata );
					update_post_meta( $id, '_gnn_package_ready', 1 );
				}
				$state['posts'][ $row['id'] ] = $id;
				++$state['cursor'];
			} else {
				$state['phase']  = 'parents';
				$state['cursor'] = 0;
			}
		} elseif ( 'parents' === $state['phase'] ) {
			// Establish every parent and status before generating permalinks.
			if ( isset( $doc['posts'][ $index ] ) ) {
				$row = $doc['posts'][ $index ];
				$id  = $state['posts'][ $row['id'] ];
				wp_update_post(
					array(
						'ID'          => $id,
						'post_parent' => $state['posts'][ $row['parent'] ] ?? 0,
						'post_status' => $row['status'],
					)
				);
				$result = gnn_package_assign_terms( $id, $row['terms'], $state );
				if ( is_wp_error( $result ) ) {
					return $result;
				}
				++$state['cursor'];
			} else {
				update_option( 'page_on_front', $state['posts'][ $doc['front'] ] ?? 0 );
				update_option( 'show_on_front', 'page' === $doc['show_on_front'] ? 'page' : 'posts' );
				$state['phase']  = 'urls';
				$state['cursor'] = 0;
			}
		} elseif ( 'urls' === $state['phase'] ) {
			foreach ( array_slice( $doc['posts'], $index, 20 ) as $row ) {
				$id = $state['posts'][ $row['id'] ];
				if ( 'attachment' === $row['type'] ) {
					$url                                   = wp_get_attachment_url( $id );
					$state['urls'][ $row['asset']['url'] ] = $url;
					$metadata                              = wp_get_attachment_metadata( $id );
					foreach ( $row['asset']['sizes'] as $size => $old ) {
						$state['urls'][ $old ] = isset( $metadata['sizes'][ $size ] ) ? trailingslashit( dirname( $url ) ) . $metadata['sizes'][ $size ]['file'] : $url;
					}
				} elseif ( $row['url'] && 'nav_menu_item' !== $row['type'] ) {
					$state['urls'][ $row['url'] ] = get_permalink( $id );
				}
				++$state['cursor'];
			}
			if ( $state['cursor'] >= count( $doc['posts'] ) ) {
				$state['urls'][ untrailingslashit( $doc['home'] ) ] = untrailingslashit( home_url( '/' ) );
				$state['phase']                                     = 'links';
				$state['cursor']                                    = 0;
			}
		} elseif ( 'links' === $state['phase'] ) {
			if ( isset( $doc['posts'][ $index ] ) ) {
				$row    = $doc['posts'][ $index ];
				$id     = $state['posts'][ $row['id'] ];
				$result = wp_update_post(
					wp_slash(
						array(
							'ID'             => $id,
							'post_status'    => $row['status'],
							'post_content'   => gnn_package_content( $row['content'], $state ),
							'post_excerpt'   => gnn_package_content( $row['excerpt'], $state ),
							'post_date'      => $row['date'] ?? get_post_field( 'post_date', $id ),
							'post_date_gmt'  => $row['date_gmt'] ?? get_post_field( 'post_date_gmt', $id ),
							'comment_status' => 'open' === ( $row['comments'] ?? '' ) ? 'open' : 'closed',
						)
					),
					true
				);
				if ( is_wp_error( $result ) ) {
					return $result;
				}
				foreach ( $row['meta'] as $name => $value ) {
					if ( '_elementor_data' === $name ) {
						$value = wp_json_encode( gnn_package_remap( json_decode( $value, true ), $state ) );
					} elseif ( '_menu_item_object_id' === $name ) {
						$map   = 'taxonomy' === ( $row['meta']['_menu_item_type'] ?? '' ) ? $state['terms'] : $state['posts'];
						$value = $map[ $value ] ?? 0;
					} else {
						$value = gnn_package_remap( $value, $state, $name );
					}
					update_post_meta( $id, $name, wp_slash( $value ) );
				}
				++$state['cursor'];
			} else {
				$state['phase']  = 'settings';
				$state['cursor'] = 0;
			}
		} elseif ( 'term_parents' === $state['phase'] ) {
			if ( isset( $doc['terms'][ $index ] ) ) {
				$row = $doc['terms'][ $index ];
				wp_update_term( $state['terms'][ $row['id'] ], $row['taxonomy'], array( 'parent' => $state['terms'][ $row['parent'] ] ?? 0 ) );
				++$state['cursor'];
			} else {
				$state['phase']  = 'posts';
				$state['cursor'] = 0;
			}
		} elseif ( 'settings' === $state['phase'] ) {
			$options = gnn_package_remap( $doc['options'], $state );
			update_option( 'gnn_options', array_replace( (array) get_option( 'gnn_options', array() ), $options ) );
			foreach ( gnn_package_remap( $doc['mods'], $state ) as $key => $value ) {
				set_theme_mod( $key, $value );
			}
			foreach ( array(
				'page_on_front'        => 'front',
				'page_for_posts'       => 'blog',
				'site_icon'            => 'icon',
				'elementor_active_kit' => 'kit',
			) as $key => $field ) {
				if ( ! empty( $doc[ $field ] ) ) {
					update_option( $key, $state['posts'][ $doc[ $field ] ] ?? 0 );
				}
			}
			update_option( 'blogname', sanitize_text_field( $doc['title'] ) );
			update_option( 'blogdescription', sanitize_text_field( $doc['description'] ) );
			update_option( 'show_on_front', 'page' === $doc['show_on_front'] ? 'page' : 'posts' );
			if ( class_exists( '\Elementor\Plugin' ) ) {
				\Elementor\Plugin::$instance->files_manager->clear_cache();
			}
			$job['phase']    = 'complete';
			$job['redirect'] = admin_url( 'admin.php?page=gnn-workspace&path=ready' );
			update_option(
				'gnn_demo_replace_last',
				array(
					'id'       => $job['id'],
					'owner'    => $job['owner'],
					'site'     => $job['site'],
					'phase'    => 'complete',
					'redirect' => $job['redirect'],
					'done'     => $job['done'],
				),
				false
			);
			delete_option( 'gnn_wipe_job' );
			delete_option( 'gnn_' . $job['profile'] );
			$cleanup = true;
			flush_rewrite_rules( false );
			return $job;
		}
	} finally {
		$zip->close();
		if ( $cleanup ) {
			wp_delete_file( $stage['file'] );
		}
	}
	$job['package'] = $state;
	++$job['done'];
	update_option( 'gnn_wipe_job', $job, false );
	return $job;
}

/**
 * Assign terms before generating category-based permalinks.
 *
 * @param int   $id Destination post.
 * @param array $taxonomies Source relationships.
 * @param array $state Reference maps.
 * @return true|WP_Error
 */
function gnn_package_assign_terms( $id, $taxonomies, $state ) {
	foreach ( $taxonomies as $taxonomy => $ids ) {
		$result = wp_set_object_terms(
			$id,
			array_map(
				static function ( $old ) use ( $state ) {
					return $state['terms'][ $old ];
				},
				$ids
			),
			$taxonomy
		);
		if ( is_wp_error( $result ) ) {
			return $result;
		}
	}
	return true;
}
