<?php
/**
 * Paired starter editions. Only theme-generated blocks are mapped to native Elementor elements.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Native container support is required; never silently fall back to an HTML widget. */
function gnn_starter_elementor_available() {
	return defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.6.0', '>=' ) && class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->elements_manager->get_element_types( 'container' );
}

/**
 * Extract one attribute from our escaped generated markup (also works on WordPress 6.0).
 *
 * @param string $html Generated markup, not arbitrary imported content.
 * @param string $name Attribute name.
 */
function gnn_starter_attribute( $html, $name ) {
	return preg_match( '/\b' . preg_quote( $name, '/' ) . '=["\']([^"\']*)["\']/', $html, $match ) ? html_entity_decode( $match[1], ENT_QUOTES, 'UTF-8' ) : '';
}

/**
 * Map the supported generated block vocabulary to editable Free widgets and nested containers.
 * Refuse unsupported blocks instead of hiding whole sections inside an HTML/text widget.
 *
 * @param array $blocks Parsed generated blocks.
 * @return array|WP_Error Elementor elements or unsupported-block error.
 */
function gnn_starter_elementor_elements( $blocks ) {
	$elements = array();
	foreach ( $blocks as $block ) {
		$name = $block['blockName'];
		if ( ! $name && '' === trim( $block['innerHTML'] ) ) {
			continue;
		}
		$attrs    = $block['attrs'];
		$settings = array( '_css_classes' => $attrs['className'] ?? '' );
		$element  = array(
			'id'       => substr( md5( wp_generate_uuid4() ), 0, 7 ),
			'settings' => $settings,
			'elements' => array(),
			'isInner'  => false,
		);
		if ( 0 === strpos( (string) $name, 'gnn/' ) && in_array( substr( $name, 4 ), array( 'portfolio', 'gallery', 'products', 'slider' ), true ) ) {
			$element['elType']     = 'widget';
			$element['widgetType'] = 'gnn-' . substr( $name, 4 );
			$element['settings']   = array_merge( $settings, $attrs );
			if ( isset( $attrs['filters'] ) ) {
				$element['settings']['filters'] = $attrs['filters'] ? 'yes' : '';
			}
		} elseif ( in_array( $name, array( 'core/group', 'core/buttons', 'core/gallery' ), true ) ) {
			$children = gnn_starter_elementor_elements( $block['innerBlocks'] );
			if ( is_wp_error( $children ) ) {
				return $children;
			}
			$element['elType']   = 'container';
			$element['elements'] = $children;
			$element['settings'] = array(
				'css_classes'    => trim( 'gnn-starter-container wp-block-group ' . ( $attrs['className'] ?? '' ) . ( 'core/buttons' === $name ? ' gnn-starter-buttons wp-block-buttons' : '' ) ),
				'content_width'  => 'full',
				'flex_direction' => 'column',
				'css_id'         => $attrs['anchor'] ?? '',
			);
		} else {
			$element['elType'] = 'widget';
			$html              = $block['innerHTML'];
			if ( 'core/heading' === $name ) {
				$element['widgetType']                = 'heading';
				$element['settings']['title']         = wp_kses_post( strip_tags( $html, '<strong><em><a><br><span>' ) );
				$element['settings']['header_size']   = 'h' . (int) ( $attrs['level'] ?? 2 );
				$element['settings']['_css_classes'] .= ' gnn-starter-heading';
			} elseif ( 'core/paragraph' === $name ) {
				$element['widgetType']                = 'text-editor';
				$element['settings']['editor']        = wp_kses_post( $html );
				$element['settings']['_css_classes'] .= ' gnn-starter-text';
			} elseif ( 'core/button' === $name ) {
				$element['widgetType']                = 'button';
				$element['settings']['text']          = html_entity_decode( wp_strip_all_tags( $html ), ENT_QUOTES, 'UTF-8' );
				$element['settings']['link']          = array( 'url' => esc_url_raw( gnn_starter_attribute( $html, 'href' ) ) );
				$element['settings']['_css_classes'] .= ' gnn-starter-button';
			} elseif ( 'core/image' === $name ) {
				$src                                  = esc_url_raw( gnn_starter_attribute( $html, 'src' ) );
				$link                                 = esc_url_raw( gnn_starter_attribute( $html, 'href' ) );
				$lightbox                             = ! empty( $attrs['lightbox']['enabled'] );
				$element['widgetType']                = 'image';
				$element['settings']['image']         = array(
					'url' => $src,
					'id'  => 0,
					'alt' => gnn_starter_attribute( $html, 'alt' ),
				);
				$element['settings']['image_size']    = 'full';
				$element['settings']['link_to']       = $link ? 'custom' : ( $lightbox ? 'file' : 'none' );
				$element['settings']['link']          = array( 'url' => $link );
				$element['settings']['open_lightbox'] = $lightbox ? 'yes' : 'no';
			} else {
				return new WP_Error( 'gnn_starter_block', __( 'This starter contains a block without an Elementor equivalent.', 'gnn' ) );
			}
		}
		$elements[] = $element;
	}
	return $elements;
}

/**
 * Persist with Elementor's document API so editable data, caches and generated content agree.
 *
 * @param int   $id New or interrupted empty draft ID.
 * @param array $elements Native Elementor elements.
 * @return true|WP_Error
 */
function gnn_starter_save_elementor( $id, $elements ) {
	if ( ! current_user_can( 'edit_post', $id ) ) {
		return new WP_Error( 'gnn_starter_permission', __( 'Permission denied.', 'gnn' ) );
	}
	try {
		$document = \Elementor\Plugin::$instance->documents->get( $id );
		if ( ! $document ) {
			return new WP_Error( 'gnn_starter_document', __( 'Elementor could not open the starter document.', 'gnn' ) );
		}
		$document->set_is_built_with_elementor( true );
		$saved = $document->save(
			array(
				'elements' => $elements,
				'settings' => array( 'hide_title' => 'yes' ),
			)
		);
		if ( ! $saved ) {
			return new WP_Error( 'gnn_starter_save', __( 'Elementor could not save the starter. Retry adding missing draft pages.', 'gnn' ) );
		}
		return true;
	} catch ( \Throwable $error ) {
		return new WP_Error( 'gnn_starter_save', __( 'Elementor could not save the starter. Retry adding missing draft pages.', 'gnn' ) );
	}
}

// phpcs:disable WordPress.DB.SlowDBQuery -- bounded, explicit administrator installation and workspace lookups.
/**
 * Locate only pages owned by this profile/editor edition, including intentional trash decisions.
 *
 * @param string $profile Profile key.
 * @param string $editor Editor edition.
 * @param string $key Page key.
 * @return WP_Post|null
 */
function gnn_starter_owned_page( $profile, $editor, $key = 'home' ) {
	$owner = $profile . ':' . ( 'elementor' === $editor ? 'elementor:' : '' ) . $key;
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'draft', 'publish', 'private', 'pending', 'future', 'trash' ),
			'posts_per_page' => 1,
			'meta_key'       => '_gnn_starter',
			'meta_value'     => $owner,
		)
	);
	return $pages ? $pages[0] : null;
}

/**
 * Isolated, retryable installations. No editor conversion, publication, menu or homepage mutation.
 *
 * @param string $profile Validated profile key.
 * @param string $editor Validated editor key.
 * @return array|WP_Error
 */
function gnn_install_profile_pages( $profile, $editor ) {
	if ( ! current_user_can( 'manage_options' ) || ! isset( gnn_starter_profiles()[ $profile ] ) || ! in_array( $editor, array( 'gutenberg', 'elementor' ), true ) ) {
		return new WP_Error( 'gnn_starter', __( 'Invalid starter profile.', 'gnn' ) );
	}
	if ( 'elementor' === $editor && ! gnn_starter_elementor_available() ) {
		return new WP_Error( 'gnn_starter_elementor', __( 'Activate Elementor 3.6 or newer before installing this edition.', 'gnn' ) );
	}
	$pages = array(
		'home'     => __( 'Home', 'gnn' ),
		'about'    => __( 'About', 'gnn' ),
		'services' => __( 'Services', 'gnn' ),
		'news'     => __( 'News', 'gnn' ),
		'contact'  => __( 'Contact', 'gnn' ),
	);
	if ( 'architecture' === $profile ) {
		$pages = array(
			'home'      => __( 'Architecture Home', 'gnn' ),
			'studio'    => __( 'Studio', 'gnn' ),
			'services'  => __( 'Services', 'gnn' ),
			'portfolio' => __( 'Portfolio / 2 Columns', 'gnn' ),
			'gallery'   => __( 'Gallery', 'gnn' ),
			'contact'   => __( 'Contact', 'gnn' ),
		);
	}
	if ( 'design' === $profile ) {
		$pages['portfolio'] = __( 'Portfolio', 'gnn' );
		$pages['gallery']   = __( 'Gallery', 'gnn' );
	}
	$root_existing = gnn_starter_owned_page( $profile, $editor );
	$root_new      = ! $root_existing || '1' === get_post_meta( $root_existing->ID, '_gnn_starter_pending', true );
	$root_baseline = $root_existing ? get_post_meta( $root_existing->ID, '_gnn_starter_baseline', true ) : '';
	$root_safe     = $root_baseline && hash_equals( $root_baseline, gnn_starter_fingerprint( $root_existing->ID ) );
	$ids           = array();
	foreach ( $pages as $key => $title ) {
		$existing = gnn_starter_owned_page( $profile, $editor, $key );
		if ( $existing ) {
			if ( 'home' === $key && 'trash' === $existing->post_status ) {
				return new WP_Error( 'gnn_starter_trash', __( 'Restore this edition’s starter home from Trash before adding missing pages.', 'gnn' ) );
			}
			$ids[ $key ] = $existing->ID;
			continue;
		}
		$slug   = 'home' === $key ? $profile . ( 'elementor' === $editor ? '-elementor' : '' ) : $key;
		$parent = 'home' === $key ? 0 : $ids['home'];
		$base   = $slug;
		$suffix = 2;
		while ( get_page_by_path( ( $parent ? get_page_uri( $parent ) . '/' : '' ) . $slug ) ) {
			if ( $suffix > 100 ) {
				return new WP_Error( 'gnn_starter_path', __( 'No unused starter page path is available.', 'gnn' ) );
			}
			$slug = $base . '-starter-' . $suffix;
			++$suffix;
		}
		$id = wp_insert_post(
			array(
				'post_type'      => 'page',
				'post_status'    => 'draft',
				'post_title'     => $title,
				'post_name'      => $slug,
				'post_parent'    => $parent,
				'comment_status' => 'closed',
			),
			true
		);
		if ( is_wp_error( $id ) ) {
			return $id;
		}
		$ids[ $key ] = $id;
		update_post_meta( $id, '_gnn_starter', $profile . ':' . ( 'elementor' === $editor ? 'elementor:' : '' ) . $key );
		update_post_meta( $id, '_gnn_starter_pending', '1' );
		update_post_meta( $id, '_gnn_starter_editor', $editor );
		update_post_meta( $id, '_gnn_starter_root', $ids['home'] );
		update_post_meta( $id, '_wp_page_template', 'page-templates/tpl-' . ( 'architecture' === $profile ? 'architecture' : 'starter' ) . '.php' );
		if ( 'architecture' === $profile ) {
			update_post_meta( $id, '_gnn_architecture_root', $ids['home'] );
		}
	}
	$links_changed = get_post_meta( $ids['home'], '_gnn_starter_pages', true ) !== $ids;
	foreach ( $ids as $key => $id ) {
		$baseline  = get_post_meta( $id, '_gnn_starter_baseline', true );
		$refresh   = $links_changed && $baseline && hash_equals( $baseline, gnn_starter_fingerprint( $id ) );
		$generated = false;
		if ( 'trash' === get_post_status( $id ) || ( ! $refresh && '1' !== get_post_meta( $id, '_gnn_starter_pending', true ) ) ) {
			continue;
		}
		if ( $refresh || ( '' === trim( (string) get_post_field( 'post_content', $id ) ) && ! get_post_meta( $id, '_elementor_data', true ) ) ) {
			$content = 'architecture' === $profile ? gnn_architecture_page_content( $key, $ids ) : gnn_profile_page_content( $profile, $key, $ids );
			if ( 'architecture' === $profile ) {
				$content = gnn_starter_live_architecture( $content, 'home' === $key );
			}
			if ( 'elementor' === $editor ) {
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
			$generated = true;
		}
		if ( $generated ) {
			update_post_meta( $id, '_gnn_starter_baseline', gnn_starter_fingerprint( $id ) );
		}
		delete_post_meta( $id, '_gnn_starter_pending' );
	}
	update_post_meta( $ids['home'], '_gnn_starter_pages', $ids );
	if ( 'architecture' === $profile ) {
		update_post_meta( $ids['home'], '_gnn_architecture_pages', $ids );
	}
	if ( $root_safe || ( $root_new && get_post_meta( $ids['home'], '_gnn_starter_baseline', true ) ) ) {
		update_post_meta( $ids['home'], '_gnn_starter_baseline', gnn_starter_fingerprint( $ids['home'] ) );
	}
	return $ids;
}
// phpcs:enable WordPress.DB.SlowDBQuery

/**
 * Replace only generated Architecture showcase sections with live query widgets.
 * Existing saved pages are never transformed.
 *
 * @param string $content Newly generated content.
 * @param bool   $home Selected-work limit.
 */
function gnn_starter_live_architecture( $content, $home = false ) {
	$map = function ( $blocks ) use ( &$map, $home ) {
		$result = array();
		foreach ( $blocks as $block ) {
			$class = $block['attrs']['className'] ?? '';
			if ( 'gnn-arch-filters' === $class ) {
				continue;
			}
			if ( 'gnn-arch-projects' === $class || 'core/gallery' === $block['blockName'] ) {
				$module = 'core/gallery' === $block['blockName'] ? 'gallery' : 'portfolio';
				$block  = parse_blocks(
					gnn_module_block(
						$module,
						array(
							'columns' => 0,
							'limit'   => $home ? 2 : 12,
							'filters' => ! $home,
						)
					)
				)[0];
			} elseif ( $block['innerBlocks'] ) {
				// Rebuild child placeholders when dropping the generated filter group.
				$html  = '';
				$index = 0;
				foreach ( $block['innerContent'] as $part ) {
					$html .= null === $part ? serialize_block( $block['innerBlocks'][ $index++ ] ) : $part;
				}
				$old = $block['innerBlocks'];
				foreach ( $old as $i => $child ) {
					$replacement = $map( array( $child ) );
					$html        = str_replace( serialize_block( $child ), serialize_blocks( $replacement ), $html );
				}
				$serialized = get_comment_delimited_block_content( $block['blockName'], $block['attrs'], $html );
				$block      = parse_blocks( $serialized )[0];
			}
			$result[] = $block;
		}
		return $result;
	};
	return serialize_blocks( $map( parse_blocks( $content ) ) );
}
