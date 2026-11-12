<?php
/**
 * Central projects and albums. Every presentation queries the same records.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Register content independently of the selected design profile or editor. */
function gnn_register_content_modules() {
	if ( ! post_type_exists( 'gnn_project' ) ) {
		register_post_type(
			'gnn_project',
			array(
				'label'           => __( 'Portfolio', 'gnn' ),
				'labels'          => array(
					'name'          => __( 'Portfolio', 'gnn' ),
					'singular_name' => __( 'Project', 'gnn' ),
					'add_new_item'  => __( 'Add new project', 'gnn' ),
					'edit_item'     => __( 'Edit project', 'gnn' ),
				),
				'public'          => true,
				'show_ui'         => true,
				'show_in_rest'    => true,
				'show_in_menu'    => 'gnn-panel',
				'has_archive'     => false,
				'rewrite'         => array(
					'slug'       => 'projects',
					'with_front' => false,
				),
				'supports'        => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes', 'custom-fields' ),
				'capability_type' => 'page',
				'map_meta_cap'    => true,
			)
		);
	}
	add_post_type_support( 'gnn_project', 'elementor' );
	if ( ! taxonomy_exists( 'gnn_project_category' ) ) {
		register_taxonomy(
			'gnn_project_category',
			'gnn_project',
			array(
				'label'             => __( 'Project categories', 'gnn' ),
				'show_admin_column' => true,
				'public'            => true,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'hierarchical'      => true,
				'rewrite'           => array(
					'slug'       => 'project-category',
					'with_front' => false,
				),
			)
		);
	}
	if ( ! post_type_exists( 'gnn_product' ) ) {
		register_post_type(
			'gnn_product',
			array(
				'label'           => __( 'Products', 'gnn' ),
				'labels'          => array(
					'name'          => __( 'Products', 'gnn' ),
					'singular_name' => __( 'Product', 'gnn' ),
					'add_new_item'  => __( 'Add new product', 'gnn' ),
					'edit_item'     => __( 'Edit product', 'gnn' ),
				),
				'public'          => true,
				'show_ui'         => true,
				'show_in_rest'    => true,
				'show_in_menu'    => 'gnn-panel',
				'has_archive'     => false,
				'rewrite'         => array(
					'slug'       => 'products',
					'with_front' => false,
				),
				'supports'        => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes', 'custom-fields' ),
				'capability_type' => 'page',
				'map_meta_cap'    => true,
			)
		);
	}
	add_post_type_support( 'gnn_product', 'elementor' );
	if ( ! taxonomy_exists( 'gnn_product_category' ) ) {
		register_taxonomy(
			'gnn_product_category',
			'gnn_product',
			array(
				'label'             => __( 'Product categories', 'gnn' ),
				'show_admin_column' => true,
				'public'            => true,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'hierarchical'      => true,
				'rewrite'           => array(
					'slug'       => 'product-category',
					'with_front' => false,
				),
			)
		);
	}
	if ( ! post_type_exists( 'gnn_album' ) ) {
		register_post_type(
			'gnn_album',
			array(
				'label'           => __( 'Gallery', 'gnn' ),
				'labels'          => array(
					'name'          => __( 'Gallery', 'gnn' ),
					'singular_name' => __( 'Album', 'gnn' ),
					'add_new_item'  => __( 'Add new album', 'gnn' ),
					'edit_item'     => __( 'Edit album', 'gnn' ),
				),
				'public'          => false,
				'show_ui'         => true,
				'show_in_rest'    => true,
				'show_in_menu'    => 'gnn-panel',
				'supports'        => array( 'title', 'revisions', 'custom-fields' ),
				'capability_type' => 'page',
				'map_meta_cap'    => true,
			)
		);
	}
	foreach ( array( 'gnn_project', 'gnn_product', 'gnn_album' ) as $type ) {
		register_post_meta(
			$type,
			'_gnn_gallery_ids',
			array(
				'type'              => 'array',
				'single'            => true,
				'default'           => array(),
				'show_in_rest'      => array(
					'schema' => array(
						'type'  => 'array',
						'items' => array( 'type' => 'integer' ),
					),
				),
				'sanitize_callback' => 'gnn_gallery_ids',
				'auth_callback'     => 'gnn_module_meta_permission',
				'revisions_enabled' => true,
			)
		);
	}
	foreach ( array( '_gnn_project_location', '_gnn_project_year', '_gnn_project_client' ) as $key ) {
		register_post_meta(
			'gnn_project',
			$key,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => 'gnn_module_meta_permission',
				'revisions_enabled' => true,
			)
		);
	}
	foreach ( array( '_gnn_product_code', '_gnn_product_material', '_gnn_product_dimensions' ) as $key ) {
		register_post_meta(
			'gnn_product',
			$key,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => 'gnn_module_meta_permission',
				'revisions_enabled' => true,
			)
		);
	}
	foreach ( array( 'portfolio', 'catalog', 'gallery', 'products', 'slider' ) as $module ) {
		register_block_type(
			'gnn/' . $module,
			array(
				'attributes'      => gnn_module_attributes(),
				'render_callback' => function ( $attrs ) use ( $module ) {
					return gnn_render_module( $module, $attrs );
				},
			)
		);
	}
}
add_action( 'init', 'gnn_register_content_modules', 9 );

/**
 * Metadata remains editable only through the record's own capability.
 *
 * @param bool   $allowed Original decision.
 * @param string $key Meta key.
 * @param int    $id Record ID.
 */
function gnn_module_meta_permission( $allowed, $key, $id ) {
	return current_user_can( 'edit_post', $id );
}

/**
 * Bounded valid image attachments; no raw URLs or duplicated media records.
 *
 * @param mixed $value Attachment IDs.
 */
function gnn_gallery_ids( $value ) {
	$ids = array();
	foreach ( array_slice( is_array( $value ) ? $value : explode( ',', (string) $value ), 0, 100 ) as $id ) {
		$id = is_scalar( $id ) ? absint( $id ) : 0;
		if ( $id && wp_attachment_is_image( $id ) ) {
			$ids[] = $id;
		}
	}
	return array_values( array_unique( $ids ) );
}

/** Shared dynamic settings for native Elementor widgets and server rendering. */
function gnn_module_attributes() {
	return array(
		'category' => array(
			'type'    => 'integer',
			'default' => 0,
		),
		'columns'  => array(
			'type'    => 'integer',
			'default' => 0,
		),
		'limit'    => array(
			'type'    => 'integer',
			'default' => 12,
		),
		'source'   => array(
			'type'    => 'string',
			'default' => 'projects',
		),
		'record'   => array(
			'type'    => 'integer',
			'default' => 0,
		),
		'filters'  => array(
			'type'    => 'boolean',
			'default' => true,
		),
	);
}

/**
 * Store only query settings; never freeze the current query result into the page.
 *
 * @param string $module Module key.
 * @param array  $attrs Query settings.
 */
function gnn_module_block( $module, $attrs = array() ) {
	return get_comment_delimited_block_content( 'gnn/' . $module, $attrs, '' );
}

/** Admin media collections and project facts. */
function gnn_module_meta_boxes() {
	foreach ( array( 'gnn_project', 'gnn_product', 'gnn_album' ) as $type ) {
		add_meta_box( 'gnn-content-collection', __( 'Gallery images', 'gnn' ), 'gnn_module_meta_box', $type, 'normal', 'high' );
	}
}
add_action( 'add_meta_boxes', 'gnn_module_meta_boxes' );

/**
 * Native media library supports ordered collections shared by every frontend.
 *
 * @param WP_Post $post Current record.
 */
function gnn_module_meta_box( $post ) {
	wp_nonce_field( 'gnn_content_collection', 'gnn_collection_nonce' );
	$ids = gnn_gallery_ids( get_post_meta( $post->ID, '_gnn_gallery_ids', true ) );
	echo '<div class="gnn-media-collection"><input type="hidden" name="gnn_gallery_ids" value="' . esc_attr( implode( ',', $ids ) ) . '"><button type="button" class="button gnn-collection-select">' . esc_html__( 'Choose and order gallery images', 'gnn' ) . '</button> <button type="button" class="button gnn-collection-clear">' . esc_html__( 'Clear selection', 'gnn' ) . '</button><div class="gnn-collection-preview">';
	foreach ( $ids as $id ) {
		echo wp_get_attachment_image( $id, 'thumbnail' );
	}
	echo '</div><p class="description">' . esc_html__( 'Select images in display order. Removing a selection never deletes media from the library.', 'gnn' ) . '</p></div>';
	if ( 'gnn_project' === $post->post_type ) {
		foreach ( array(
			'location' => __( 'Project location', 'gnn' ),
			'year'     => __( 'Project year', 'gnn' ),
			'client'   => __( 'Client or project context', 'gnn' ),
		) as $key => $label ) {
			echo '<p><label>' . esc_html( $label ) . '<br><input type="text" class="widefat" name="gnn_project_' . esc_attr( $key ) . '" value="' . esc_attr( get_post_meta( $post->ID, '_gnn_project_' . $key, true ) ) . '"></label></p>';
		}
	}
	if ( 'gnn_product' === $post->post_type ) {
		foreach ( array(
			'code'       => __( 'Product code', 'gnn' ),
			'material'   => __( 'Material', 'gnn' ),
			'dimensions' => __( 'Dimensions', 'gnn' ),
		) as $key => $label ) {
			echo '<p><label>' . esc_html( $label ) . '<br><input type="text" class="widefat" name="gnn_product_' . esc_attr( $key ) . '" value="' . esc_attr( get_post_meta( $post->ID, '_gnn_product_' . $key, true ) ) . '"></label></p>';
		}
	}
}

/**
 * Save after explicit nonce and per-record authorization; REST uses registered metadata rules.
 *
 * @param int $id Record ID.
 */
function gnn_save_module_collection( $id ) {
	if ( ! in_array( get_post_type( $id ), array( 'gnn_project', 'gnn_product', 'gnn_album' ), true ) || ! current_user_can( 'edit_post', $id ) || wp_is_post_revision( $id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! isset( $_POST['gnn_collection_nonce'] ) || ! is_string( $_POST['gnn_collection_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['gnn_collection_nonce'] ) ), 'gnn_content_collection' ) ) {
		return;
	}
	if ( isset( $_POST['gnn_gallery_ids'] ) && is_string( $_POST['gnn_gallery_ids'] ) ) {
		update_post_meta( $id, '_gnn_gallery_ids', gnn_gallery_ids( sanitize_text_field( wp_unslash( $_POST['gnn_gallery_ids'] ) ) ) );
	}
	foreach ( array( 'location', 'year', 'client' ) as $key ) {
		if ( isset( $_POST[ 'gnn_project_' . $key ] ) && is_string( $_POST[ 'gnn_project_' . $key ] ) ) {
			update_post_meta( $id, '_gnn_project_' . $key, sanitize_text_field( wp_unslash( $_POST[ 'gnn_project_' . $key ] ) ) );
		}
	}
	foreach ( array( 'code', 'material', 'dimensions' ) as $key ) {
		if ( isset( $_POST[ 'gnn_product_' . $key ] ) && is_string( $_POST[ 'gnn_product_' . $key ] ) ) {
			update_post_meta( $id, '_gnn_product_' . $key, sanitize_text_field( wp_unslash( $_POST[ 'gnn_product_' . $key ] ) ) );
		}
	}
}
add_action( 'save_post', 'gnn_save_module_collection' );

/** Register editor media controls only on the two central content screens. */
function gnn_module_admin_assets() {
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->post_type, array( 'gnn_project', 'gnn_product', 'gnn_album' ), true ) ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script( 'gnn-collections', get_template_directory_uri() . '/assets/js/collections-admin.js', array( 'jquery', 'media-views' ), GNN_VERSION, true );
	wp_enqueue_style( 'gnn-modules', get_template_directory_uri() . '/assets/css/content-modules.css', array(), GNN_VERSION );
}
add_action( 'admin_enqueue_scripts', 'gnn_module_admin_assets' );

/**
 * One validated query contract. Drafts are never leaked through public widgets.
 *
 * @param array  $attrs    Query settings.
 * @param string $type     Content type.
 * @param string $taxonomy Category taxonomy.
 */
function gnn_collection_query( $attrs, $type, $taxonomy ) {
	$args = array(
		'post_type'      => $type,
		'post_status'    => 'publish',
		'has_password'   => false,
		'posts_per_page' => max( 1, min( 48, absint( $attrs['limit'] ?? 12 ) ) ),
		'orderby'        => array(
			'menu_order' => 'ASC',
			'date'       => 'DESC',
			'ID'         => 'DESC',
		),
		'paged'          => max( 1, absint( $attrs['page'] ?? 1 ) ),
	);
	if ( in_array( $attrs['orderby'] ?? '', array( 'date', 'title', 'menu_order' ), true ) ) {
		$args['orderby'] = array(
			$attrs['orderby'] => 'ASC' === ( $attrs['order'] ?? '' ) ? 'ASC' : 'DESC',
			'ID'              => 'DESC',
		);
	}
	$exclude = isset( $attrs['exclude_ids'] ) && is_string( $attrs['exclude_ids'] ) ? preg_split( '/[\s,]+/', $attrs['exclude_ids'] ) : array();
	$exclude = array_values( array_filter( array_map( 'absint', array_slice( $exclude, 0, 100 ) ) ) );
	if ( 'yes' === ( $attrs['related'] ?? '' ) && ! is_singular( $type ) ) {
		$args['post__in'] = array( 0 );
	}
	if ( 'yes' === ( $attrs['related'] ?? '' ) && is_singular( $type ) ) {
		$current    = get_queried_object_id();
		$exclude[]  = $current;
		$categories = wp_get_post_terms( $current, $taxonomy, array( 'fields' => 'ids' ) );
		if ( is_wp_error( $categories ) || ! $categories ) {
			$args['post__in'] = array( 0 );
		} else {
			$attrs['category'] = $categories;
		}
	}
	if ( $exclude ) {
		$args['post__not_in'] = array_values( array_unique( $exclude ) ); // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in -- bounded explicit selection.
	}
	if ( ! empty( $attrs['category'] ) ) {
		// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- indexed native term relationships.
		$args['tax_query'] = array(
			array(
				'taxonomy' => $taxonomy,
				'field'    => 'term_id',
				'terms'    => array_map( 'absint', (array) $attrs['category'] ),
			),
		); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- native taxonomy selection, indexed relationships.
	}
	return new WP_Query( $args );
}

/**
 * Query portfolio records using the shared public collection contract.
 *
 * @param array $attrs Query settings.
 */
function gnn_project_query( $attrs ) {
	return gnn_collection_query( $attrs, 'gnn_project', 'gnn_project_category' );
}

/**
 * Query catalogue records without requiring a commerce plugin.
 *
 * @param array $attrs Query settings.
 */
function gnn_product_query( $attrs ) {
	return gnn_collection_query( $attrs, 'gnn_product', 'gnn_product_category' );
}

/**
 * The same renderer is used by Elementor widgets, reusable blocks and project galleries.
 *
 * @param string $module Module name.
 * @param array  $attrs Query settings.
 * @return string Escaped rendered presentation.
 */
function gnn_render_module( $module, $attrs = array() ) {
	return gnn_render_module_inner( $module, $attrs );
}

/** Render a live collection with its own query controls.
 *
 * @param string $module Module name.
 * @param array  $attrs Query settings.
 */
function gnn_render_module_inner( $module, $attrs = array() ) {
	wp_enqueue_style( 'gnn-content-modules' );
	wp_enqueue_script( 'gnn-content-modules' );
	$default_columns   = 'gallery' === $module ? gnn_option( 'gallery_columns' ) : ( 'products' === $module ? gnn_option( 'shop_columns' ) : gnn_option( 'portfolio_columns' ) );
	$requested_columns = absint( $attrs['columns'] ?? 0 );
	$columns           = max( 1, min( 4, $requested_columns ? $requested_columns : (int) $default_columns ) );
	if ( 'products' === $module ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return current_user_can( 'manage_options' ) ? '<p>' . esc_html__( 'Activate WooCommerce to display the live product collection.', 'gnn' ) . '</p>' : '';
		}
		$category = ! empty( $attrs['category'] ) ? get_term( absint( $attrs['category'] ), 'product_cat' ) : null;
		$category = $category && ! is_wp_error( $category ) ? $category : null;
		if ( 'categories' === ( $attrs['source'] ?? '' ) ) {
			return do_shortcode( '[product_categories limit="' . max( 1, min( 24, absint( $attrs['limit'] ?? 8 ) ) ) . '" columns="' . $columns . '" hide_empty="1"' . ( $category ? ' ids="' . (int) $category->term_id . '"' : '' ) . ']' );
		}
		return do_shortcode( '[products limit="' . max( 1, min( 24, absint( $attrs['limit'] ?? 8 ) ) ) . '" columns="' . $columns . '" visibility="visible"' . ( $category ? ' category="' . sanitize_title( $category->slug ) . '"' : '' ) . ']' );
	}
	if ( 'slider' === $module ) {
		wp_enqueue_script( 'gnn-slider', get_template_directory_uri() . '/assets/js/slider.js', array(), GNN_VERSION, true );
		wp_localize_script(
			'gnn-slider',
			'gnnSlider',
			array(
				'autoplay' => (bool) gnn_option( 'slider_autoplay' ),
				'interval' => max( 2, (int) gnn_option( 'slider_interval' ) ) * 1000,
			)
		);
		ob_start();
		get_template_part(
			'template-parts/hero-slider',
			null,
			array(
				'embedded' => true,
				'group'    => absint( $attrs['category'] ?? 0 ),
			)
		);
		return ob_get_clean();
	}
	if ( 'portfolio' === $module ) {
		$instance = wp_unique_id( 'gnn-portfolio-' );
		$category = absint( $attrs['category'] ?? 0 );
		$param    = 'gnn_category';
		$selected = isset( $_GET[ $param ] ) && is_scalar( $_GET[ $param ] ) ? absint( $_GET[ $param ] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public read-only filter.
		if ( ! $category && $selected && false !== ( $attrs['filters'] ?? true ) ) {
			$attrs['category'] = $selected;
		}
		$page          = isset( $_GET['gnn_projects_page'] ) && is_scalar( $_GET['gnn_projects_page'] ) ? max( 1, absint( $_GET['gnn_projects_page'] ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public pagination.
		$attrs['page'] = $page;
		$query         = gnn_project_query( $attrs );
		$html          = '<section class="gnn-content-module" aria-label="' . esc_attr__( 'Portfolio', 'gnn' ) . '">';
		if ( ! $category && false !== ( $attrs['filters'] ?? true ) ) {
			$base  = remove_query_arg( array( $param, 'gnn_projects_page' ) );
			$html .= '<nav class="gnn-module-filters" aria-label="' . esc_attr__( 'Project categories', 'gnn' ) . '"><a href="' . esc_url( $base ) . '"' . ( ! $selected ? ' aria-current="true"' : '' ) . '>' . esc_html__( 'All projects', 'gnn' ) . '</a>';
			$terms = get_terms(
				array(
					'taxonomy'   => 'gnn_project_category',
					'hide_empty' => true,
					'number'     => 100,
				)
			);
			foreach ( is_wp_error( $terms ) ? array() : $terms as $term ) {
				$html .= '<a href="' . esc_url( add_query_arg( $param, $term->term_id, $base ) ) . '"' . ( $selected === $term->term_id ? ' aria-current="true"' : '' ) . '>' . esc_html( $term->name ) . '</a>';
			}
			$html .= '</nav>';
		}
		$html .= '<div id="' . esc_attr( $instance ) . '" class="gnn-module-grid gnn-module-columns-' . $columns . '">';
		foreach ( $query->posts as $project ) {
			$url   = get_permalink( $project );
			$terms = get_the_terms( $project, 'gnn_project_category' );
			$html .= '<article class="gnn-module-project"><a class="gnn-module-project-image" aria-label="' . esc_attr( get_the_title( $project ) ) . '" href="' . esc_url( $url ) . '">' . gnn_project_image( $project->ID ) . '</a><p class="gnn-module-kicker">' . esc_html( $terms && ! is_wp_error( $terms ) ? implode( ' / ', wp_list_pluck( $terms, 'name' ) ) : '' ) . '</p><h3><a href="' . esc_url( $url ) . '">' . esc_html( get_the_title( $project ) ) . '</a></h3><p>' . esc_html( wp_trim_words( $project->post_excerpt, 24 ) ) . '</p></article>';
		}
		if ( ! $query->posts ) {
			$html .= '<p>' . esc_html__( 'No projects have been published in this selection yet.', 'gnn' ) . '</p>';
		}
		$html .= '</div>';
		if ( $query->max_num_pages > 1 ) {
			$html .= '<nav class="gnn-module-pagination" aria-label="' . esc_attr__( 'Project pages', 'gnn' ) . '">' . paginate_links(
				array(
					'base'    => add_query_arg( 'gnn_projects_page', '%#%' ),
					'format'  => '',
					'current' => $page,
					'total'   => $query->max_num_pages,
				)
			) . '</nav>';
		}
		return $html . '</section>';
	}
	if ( 'catalog' === $module ) {
		$instance = wp_unique_id( 'gnn-catalog-' );
		$category = absint( $attrs['category'] ?? 0 );
		$param    = 'gnn_product_category';
		$selected = isset( $_GET[ $param ] ) && is_scalar( $_GET[ $param ] ) ? absint( $_GET[ $param ] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public read-only filter.
		if ( ! $category && $selected && false !== ( $attrs['filters'] ?? true ) ) {
			$attrs['category'] = $selected;
		}
		$page          = isset( $_GET['gnn_products_page'] ) && is_scalar( $_GET['gnn_products_page'] ) ? max( 1, absint( $_GET['gnn_products_page'] ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public pagination.
		$attrs['page'] = $page;
		$query         = gnn_product_query( $attrs );
		$html          = '<section class="gnn-content-module" aria-label="' . esc_attr__( 'Products', 'gnn' ) . '">';
		if ( ! $category && false !== ( $attrs['filters'] ?? true ) ) {
			$base  = remove_query_arg( array( $param, 'gnn_products_page' ) );
			$html .= '<nav class="gnn-module-filters" aria-label="' . esc_attr__( 'Product categories', 'gnn' ) . '"><a href="' . esc_url( $base ) . '"' . ( ! $selected ? ' aria-current="true"' : '' ) . '>' . esc_html__( 'All products', 'gnn' ) . '</a>';
			$terms = get_terms(
				array(
					'taxonomy'   => 'gnn_product_category',
					'hide_empty' => true,
					'number'     => 100,
				)
			);
			foreach ( is_wp_error( $terms ) ? array() : $terms as $term ) {
				$html .= '<a href="' . esc_url( add_query_arg( $param, $term->term_id, $base ) ) . '"' . ( $selected === $term->term_id ? ' aria-current="true"' : '' ) . '>' . esc_html( $term->name ) . '</a>';
			}
			$html .= '</nav>';
		}
		$html .= '<div id="' . esc_attr( $instance ) . '" class="gnn-module-grid gnn-module-columns-' . $columns . '">';
		foreach ( $query->posts as $product ) {
			$url   = get_permalink( $product );
			$terms = get_the_terms( $product, 'gnn_product_category' );
			$html .= '<article class="gnn-module-product"><a class="gnn-module-project-image" aria-label="' . esc_attr( get_the_title( $product ) ) . '" href="' . esc_url( $url ) . '">' . gnn_product_image( $product->ID ) . '</a><p class="gnn-module-kicker">' . esc_html( $terms && ! is_wp_error( $terms ) ? implode( ' / ', wp_list_pluck( $terms, 'name' ) ) : '' ) . '</p><h3><a href="' . esc_url( $url ) . '">' . esc_html( get_the_title( $product ) ) . '</a></h3><p>' . esc_html( wp_trim_words( $product->post_excerpt, 24 ) ) . '</p></article>';
		}
		if ( ! $query->posts ) {
			$html .= '<p>' . esc_html__( 'No products have been published in this selection yet.', 'gnn' ) . '</p>';
		}
		$html .= '</div>';
		if ( $query->max_num_pages > 1 ) {
			$html .= '<nav class="gnn-module-pagination" aria-label="' . esc_attr__( 'Product pages', 'gnn' ) . '">' . paginate_links(
				array(
					'base'    => add_query_arg( 'gnn_products_page', '%#%' ),
					'format'  => '',
					'current' => $page,
					'total'   => $query->max_num_pages,
				)
			) . '</nav>';
		}
		return $html . '</section>';
	}
	if ( 'gallery' === $module ) {
		$record = absint( $attrs['record'] ?? 0 );
		$source = $attrs['source'] ?? 'projects';
		$ids    = array();
		if ( in_array( $source, array( 'album', 'project', 'product' ), true ) ) {
			$expected = 'album' === $source ? 'gnn_album' : ( 'product' === $source ? 'gnn_product' : 'gnn_project' );
			if ( $record && get_post_type( $record ) === $expected && 'publish' === get_post_status( $record ) && ! post_password_required( $record ) ) {
				$ids = gnn_gallery_ids( get_post_meta( $record, '_gnn_gallery_ids', true ) );
			}
		} else {
			$query = gnn_project_query( $attrs );
			foreach ( $query->posts as $project ) {
				$ids = array_merge( $ids, array_filter( array( get_post_thumbnail_id( $project ) ) ), (array) get_post_meta( $project->ID, '_gnn_gallery_ids', true ) );
			}
			$ids = gnn_gallery_ids( $ids );
		}
		$html = '<div data-close-label="' . esc_attr__( 'Close', 'gnn' ) . '" class="gnn-content-module gnn-module-gallery gnn-module-grid gnn-module-columns-' . $columns . '">';
		foreach ( $ids as $id ) {
			$html .= '<a class="gnn-gallery-expand" href="' . esc_url( wp_get_attachment_image_url( $id, 'full' ) ) . '" aria-label="' . esc_attr__( 'Expand image', 'gnn' ) . '">' . wp_get_attachment_image( $id, 'large' ) . '</a>';
		}
		if ( ! $ids && current_user_can( 'edit_pages' ) ) {
			$html .= '<p>' . esc_html__( 'Select an album or publish projects with gallery images to populate this collection.', 'gnn' ) . '</p>';
		}
		return $html . '</div>';
	}
	return '';
}

/**
 * Bundled sample illustrations are replaceable; real featured images always take precedence.
 *
 * @param int $id Project ID.
 */
function gnn_project_image( $id ) {
	if ( has_post_thumbnail( $id ) ) {
		return get_the_post_thumbnail( $id, 'large' );
	}
	$name = get_post_meta( $id, '_gnn_project_illustration', true );
	if ( in_array( $name, array( 'courtyard', 'urban', 'civic', 'interior' ), true ) ) {
		return '<img src="' . esc_url( get_template_directory_uri() . '/assets/images/architecture/' . $name . '.svg' ) . '" alt="' . esc_attr( get_the_title( $id ) ) . '" loading="lazy" width="1600" height="1100">';
	}
	return '';
}

/**
 * Product cards use native featured images only.
 *
 * @param int $id Product ID.
 */
function gnn_product_image( $id ) {
	return has_post_thumbnail( $id ) ? get_the_post_thumbnail( $id, 'large' ) : '';
}

/**
 * Automatic adjacency follows the current category and native project publication dates.
 *
 * @param int $id Current project.
 */
function gnn_project_navigation( $id ) {
	global $post;
	$original      = $post;
	$post          = get_post( $id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- native adjacent-post API requires contextual global, restored immediately.
	$terms         = get_the_terms( $id, 'gnn_project_category' );
	$same_category = $terms && ! is_wp_error( $terms );
	$previous      = get_adjacent_post( $same_category, '', true, 'gnn_project_category' );
	$next          = get_adjacent_post( $same_category, '', false, 'gnn_project_category' );
	$post          = $original; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- restore native query context.
	$html          = '<nav class="gnn-project-navigation" aria-label="' . esc_attr__( 'Project navigation', 'gnn' ) . '">';
	foreach ( array( array( $previous, __( 'Previous project', 'gnn' ) ), array( $next, __( 'Next project', 'gnn' ) ) ) as $item ) {
		if ( $item[0] instanceof WP_Post ) {
			$html .= '<a href="' . esc_url( get_permalink( $item[0] ) ) . '">' . esc_html( $item[1] . ' / ' . get_the_title( $item[0] ) ) . '</a>';
		}
	}
	return $html . '</nav>';
}

/**
 * Automatic adjacency follows the current product category.
 *
 * @param int $id Current product.
 */
function gnn_product_navigation( $id ) {
	global $post;
	$original      = $post;
	$post          = get_post( $id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- native adjacent-post API requires contextual global, restored immediately.
	$terms         = get_the_terms( $id, 'gnn_product_category' );
	$same_category = $terms && ! is_wp_error( $terms );
	$previous      = get_adjacent_post( $same_category, '', true, 'gnn_product_category' );
	$next          = get_adjacent_post( $same_category, '', false, 'gnn_product_category' );
	$post          = $original; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- restore native query context.
	$html          = '<nav class="gnn-project-navigation" aria-label="' . esc_attr__( 'Product navigation', 'gnn' ) . '">';
	foreach ( array( array( $previous, __( 'Previous product', 'gnn' ) ), array( $next, __( 'Next product', 'gnn' ) ) ) as $item ) {
		if ( $item[0] instanceof WP_Post ) {
			$html .= '<a href="' . esc_url( get_permalink( $item[0] ) ) . '">' . esc_html( $item[1] . ' / ' . get_the_title( $item[0] ) ) . '</a>';
		}
	}
	return $html . '</nav>';
}

/** Load small central collection assets; no frontend remote requests. */
function gnn_register_module_assets() {
	wp_register_style( 'gnn-content-modules', get_template_directory_uri() . '/assets/css/content-modules.css', array( 'gnn-design-system' ), GNN_VERSION );
	wp_register_script( 'gnn-content-modules', get_template_directory_uri() . '/assets/js/content-modules.js', array(), GNN_VERSION, true );
	wp_register_script( 'gnn-slider', get_template_directory_uri() . '/assets/js/slider.js', array(), GNN_VERSION, true );
}
add_action( 'init', 'gnn_register_module_assets', 11 );

/** Emit live geometry and load assets only for pages containing these modules. */
function gnn_module_assets() {
	gnn_register_module_assets();
	wp_localize_script( 'gnn-content-modules', 'gnnModules', array( 'close' => __( 'Close', 'gnn' ) ) );
	$post = get_queried_object();
	$data = $post instanceof WP_Post ? (string) get_post_meta( $post->ID, '_elementor_data', true ) : '';
	if ( gnn_layout_value( 'sidebar_template' ) > 0 || is_singular( array( 'gnn_project', 'gnn_product' ) ) || is_tax( array( 'gnn_project_category', 'gnn_product_category' ) ) || ( $post instanceof WP_Post && ( false !== strpos( $post->post_content, 'wp:gnn/' ) || preg_match( '/"widgetType"\s*:\s*"gnn-(portfolio|catalog|gallery|products|slider)"/', $data ) ) ) ) {
		wp_enqueue_style( 'gnn-content-modules' );
		wp_enqueue_script( 'gnn-content-modules' );
	}
}
add_action( 'wp_enqueue_scripts', 'gnn_module_assets', 17 );

/** Flush only when the content schema changes, not on visitor requests. */
function gnn_module_rewrite_setup() {
	if ( current_user_can( 'manage_options' ) && '2' !== get_option( 'gnn_content_schema' ) ) {
		flush_rewrite_rules( false );
		update_option( 'gnn_content_schema', '2', false );
	}
}
add_action( 'admin_init', 'gnn_module_rewrite_setup' );
