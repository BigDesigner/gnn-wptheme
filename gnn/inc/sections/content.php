<?php
/**
 * Portfolio, gallery, blog and product catalog sections for the Elementor Free section library.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build a section heading or heading row with an optional action link.
 *
 * @param string $eyebrow      Eyebrow text.
 * @param string $title        Main section heading.
 * @param string $text         Lead paragraph.
 * @param string $button_label Optional link button text.
 * @param string $button_url   Optional link button destination.
 * @return array
 */
function gnn_sec_content_header( $eyebrow, $title, $text, $button_label = '', $button_url = '' ) {
	$head = gnn_st_head( $eyebrow, $title, $text );
	if ( '' !== $button_label ) {
		$url = '' !== $button_url ? $button_url : home_url( '/' );
		return gnn_st_c(
			'gnn-headrow',
			array(
				$head,
				gnn_st_b( $button_label, $url, 'gnn-link' ),
			),
			true
		);
	}
	return $head;
}

/**
 * Build the Portfolio (`portfolio`) section wrapping the `gnn-portfolio` widget.
 *
 * Variants: `cols-3`, `cols-2`, `cols-4`, `filtered`.
 *
 * @param array $a Section arguments and overrides.
 * @return array
 */
function gnn_sec_build_portfolio( $a = array() ) {
	$raw_variant = isset( $a['variant'] ) && is_string( $a['variant'] ) ? sanitize_key( $a['variant'] ) : 'cols-3';
	$variant     = in_array( $raw_variant, array( 'cols-3', 'cols-2', 'cols-4', 'filtered' ), true ) ? $raw_variant : 'cols-3';

	if ( ! empty( $a['columns'] ) && in_array( (int) $a['columns'], array( 2, 3, 4 ), true ) ) {
		$columns = (int) $a['columns'];
	} elseif ( 'cols-2' === $variant ) {
		$columns = 2;
	} elseif ( 'cols-4' === $variant ) {
		$columns = 4;
	} else {
		$columns = 3;
	}

	if ( isset( $a['filters'] ) ) {
		$filters = ( true === $a['filters'] || 'yes' === $a['filters'] || 1 === $a['filters'] || '1' === $a['filters'] ) ? 'yes' : '';
	} else {
		$filters = 'filtered' === $variant ? 'yes' : '';
	}

	$limit        = ! empty( $a['limit'] ) ? max( 1, min( 48, absint( $a['limit'] ) ) ) : ( 'filtered' === $variant ? 6 : $columns );
	$category     = ! empty( $a['category'] ) ? absint( $a['category'] ) : 0;
	$eyebrow      = isset( $a['eyebrow'] ) ? (string) $a['eyebrow'] : __( 'Selected work', 'gnn' );
	$title        = ! empty( $a['title'] ) ? (string) $a['title'] : __( 'Recent projects', 'gnn' );
	$text         = isset( $a['text'] ) ? (string) $a['text'] : __( 'Explore recent work across disciplines and sectors.', 'gnn' );
	$button_label = ! empty( $a['button_label'] ) ? (string) $a['button_label'] : '';
	$button_url   = ! empty( $a['button_url'] ) ? (string) $a['button_url'] : '';

	$children = array(
		gnn_sec_content_header( $eyebrow, $title, $text, $button_label, $button_url ),
		gnn_st_mod(
			'portfolio',
			array(
				'columns'  => $columns,
				'limit'    => $limit,
				'filters'  => $filters,
				'category' => $category,
			)
		),
	);

	$elements                                = array( gnn_st_band( 'gnn-sec', 'portfolio', $children ) );
	$elements[0]['settings']['css_classes'] .= ' gnn-sec--' . $variant;
	return $elements;
}

/**
 * Build the Gallery (`gallery`) section wrapping the `gnn-gallery` widget.
 *
 * Variants: `cols-3`, `cols-4`, `cols-2`.
 *
 * @param array $a Section arguments and overrides.
 * @return array
 */
function gnn_sec_build_gallery( $a = array() ) {
	$raw_variant = isset( $a['variant'] ) && is_string( $a['variant'] ) ? sanitize_key( $a['variant'] ) : 'cols-3';
	$variant     = in_array( $raw_variant, array( 'cols-3', 'cols-4', 'cols-2' ), true ) ? $raw_variant : 'cols-3';

	if ( ! empty( $a['columns'] ) && in_array( (int) $a['columns'], array( 2, 3, 4 ), true ) ) {
		$columns = (int) $a['columns'];
	} elseif ( 'cols-2' === $variant ) {
		$columns = 2;
	} elseif ( 'cols-4' === $variant ) {
		$columns = 4;
	} else {
		$columns = 3;
	}

	$limit   = ! empty( $a['limit'] ) ? max( 1, min( 48, absint( $a['limit'] ) ) ) : ( $columns * 2 );
	$source  = ! empty( $a['source'] ) && in_array( $a['source'], array( 'projects', 'project', 'product', 'album' ), true ) ? $a['source'] : 'projects';
	$record  = ! empty( $a['record'] ) ? absint( $a['record'] ) : 0;
	$eyebrow = isset( $a['eyebrow'] ) ? (string) $a['eyebrow'] : __( 'Gallery', 'gnn' );
	$title   = ! empty( $a['title'] ) ? (string) $a['title'] : __( 'Visual highlights', 'gnn' );
	$text    = isset( $a['text'] ) ? (string) $a['text'] : __( 'Browse images from recent projects and albums.', 'gnn' );

	$children = array(
		gnn_st_head( $eyebrow, $title, $text ),
		gnn_st_mod(
			'gallery',
			array(
				'columns' => $columns,
				'limit'   => $limit,
				'source'  => $source,
				'record'  => $record,
			)
		),
	);

	$elements                                = array( gnn_st_band( 'gnn-sec', 'gallery', $children ) );
	$elements[0]['settings']['css_classes'] .= ' gnn-sec--' . $variant;
	return $elements;
}

/**
 * Build one escaped blog post card element.
 *
 * @param string $image_url  Card cover image URL.
 * @param string $image_alt  Card image alt text.
 * @param string $date_iso   ISO 8601 date string.
 * @param string $date_label Human-readable date string.
 * @param string $title      Post title.
 * @param string $excerpt    Post summary text.
 * @param string $url        Post permalink URL.
 * @param bool   $is_sample  Whether this card is a placeholder when no posts exist.
 * @return array
 */
function gnn_sec_blog_card( $image_url, $image_alt, $date_iso, $date_label, $title, $excerpt, $url, $is_sample = false ) {
	$safe_img_url = esc_url( $image_url );
	$safe_img_alt = esc_attr( $image_alt );
	$safe_url     = esc_url( $url );
	$safe_iso     = esc_attr( $date_iso );
	$safe_date    = esc_html( $date_label );
	$safe_title   = esc_html( $title );
	$safe_excerpt = esc_html( $excerpt );

	$date_markup = '' !== $safe_iso
		? '<p class="gnn-post-card__date"><time datetime="' . $safe_iso . '">' . $safe_date . '</time></p>'
		: '<p class="gnn-post-card__date">' . $safe_date . '</p>';

	$card_class = 'gnn-card gnn-post-card' . ( $is_sample ? ' gnn-post-card--placeholder' : '' );

	return gnn_st_c(
		$card_class,
		array(
			gnn_st_w(
				'image',
				array(
					'image'      => array(
						'url' => $safe_img_url,
						'id'  => '',
						'alt' => $safe_img_alt,
					),
					'image_size' => 'full',
					'link_to'    => 'none',
				),
				'gnn-post-card__media'
			),
			gnn_st_w(
				'text-editor',
				array(
					'editor' => $date_markup,
				),
				'gnn-post-card__meta'
			),
			gnn_st_h( $safe_title, 3, 'gnn-post-card__title' ),
			gnn_st_w(
				'text-editor',
				array(
					'editor' => '<p>' . $safe_excerpt . '</p>',
				),
				'gnn-post-card__excerpt'
			),
			'' !== $safe_url ? gnn_st_b( esc_html__( 'Read article', 'gnn' ), $safe_url, 'gnn-link' ) : null,
		),
		true
	);
}

/**
 * Build the Blog (`blog`) section displaying recent posts via `get_posts`.
 *
 * Variants: `grid`, `list`.
 *
 * @param array $a Section arguments and overrides.
 * @return array
 */
function gnn_sec_build_blog( $a = array() ) {
	$raw_variant = isset( $a['variant'] ) && is_string( $a['variant'] ) ? sanitize_key( $a['variant'] ) : 'grid';
	if ( 'cols-3' === $raw_variant ) {
		$raw_variant = 'grid';
	}
	$variant = in_array( $raw_variant, array( 'grid', 'list' ), true ) ? $raw_variant : 'grid';

	$eyebrow     = isset( $a['eyebrow'] ) ? (string) $a['eyebrow'] : __( 'Journal', 'gnn' );
	$title       = ! empty( $a['title'] ) ? (string) $a['title'] : __( 'Latest articles', 'gnn' );
	$text        = isset( $a['text'] ) ? (string) $a['text'] : __( 'Recent insights, guides and updates from our team.', 'gnn' );
	$default_img = ! empty( $a['image'] ) ? (string) $a['image'] : get_theme_file_uri( 'assets/img/placeholder-cover.svg' );

	$blog_page_id = (int) get_option( 'page_for_posts' );
	$default_url  = ( $blog_page_id > 0 && 'publish' === get_post_status( $blog_page_id ) ) ? (string) get_permalink( $blog_page_id ) : home_url( '/' );
	$button_label = isset( $a['button_label'] ) ? (string) $a['button_label'] : __( 'All articles', 'gnn' );
	$button_url   = ! empty( $a['button_url'] ) ? (string) $a['button_url'] : $default_url;

	$limit = ! empty( $a['limit'] ) ? max( 1, min( 12, absint( $a['limit'] ) ) ) : 3;
	$posts = get_posts(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => $limit,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'has_password'        => false,
		)
	);

	$cards = array();
	if ( ! empty( $posts ) ) {
		foreach ( $posts as $post ) {
			if ( ! ( $post instanceof WP_Post ) ) {
				continue;
			}
			$thumb_url  = has_post_thumbnail( $post->ID ) ? (string) get_the_post_thumbnail_url( $post->ID, 'gnn-card' ) : '';
			$card_img   = '' !== $thumb_url ? $thumb_url : $default_img;
			$post_title = get_the_title( $post );
			$post_title = '' !== $post_title ? $post_title : __( '(Untitled)', 'gnn' );
			$post_url   = (string) get_permalink( $post );
			$date_iso   = (string) get_the_date( 'c', $post );
			$date_label = (string) get_the_date( '', $post );
			$raw_text   = has_excerpt( $post ) ? (string) $post->post_excerpt : (string) $post->post_content;
			$excerpt    = wp_trim_words( wp_strip_all_tags( $raw_text ), 20 );
			if ( '' === $excerpt ) {
				$excerpt = __( 'Read the full article for details.', 'gnn' );
			}

			$cards[] = gnn_sec_blog_card(
				$card_img,
				$post_title,
				$date_iso,
				$date_label,
				$post_title,
				$excerpt,
				$post_url,
				false
			);
		}
	}

	if ( empty( $cards ) ) {
		$cards[] = gnn_sec_blog_card(
			$default_img,
			__( 'Placeholder article image', 'gnn' ),
			'',
			__( 'Sample post', 'gnn' ),
			__( 'Publish your first article', 'gnn' ),
			__( 'No posts have been published yet. Publish an article from Posts to replace this placeholder card automatically.', 'gnn' ),
			$default_url,
			true
		);
	}

	$grid_class = 'list' === $variant ? 'gnn-cards gnn-blog-grid gnn-blog-grid--list' : 'gnn-cards gnn-cols-3 gnn-blog-grid';

	$children = array(
		gnn_sec_content_header( $eyebrow, $title, $text, $button_label, $button_url ),
		gnn_st_c( $grid_class, $cards, true ),
	);

	$elements                                = array( gnn_st_band( 'gnn-sec', 'blog', $children ) );
	$elements[0]['settings']['css_classes'] .= ' gnn-sec--' . $variant;
	return $elements;
}

/**
 * Build the Catalog (`catalog`) section wrapping the `gnn-catalog` widget.
 *
 * Variants: `cols-3`, `cols-4`, `filtered`.
 *
 * @param array $a Section arguments and overrides.
 * @return array
 */
function gnn_sec_build_catalog( $a = array() ) {
	$raw_variant = isset( $a['variant'] ) && is_string( $a['variant'] ) ? sanitize_key( $a['variant'] ) : 'cols-3';
	$variant     = in_array( $raw_variant, array( 'cols-3', 'cols-4', 'filtered' ), true ) ? $raw_variant : 'cols-3';

	if ( ! empty( $a['columns'] ) && in_array( (int) $a['columns'], array( 2, 3, 4 ), true ) ) {
		$columns = (int) $a['columns'];
	} elseif ( 'cols-4' === $variant ) {
		$columns = 4;
	} else {
		$columns = 3;
	}

	if ( isset( $a['filters'] ) ) {
		$filters = ( true === $a['filters'] || 'yes' === $a['filters'] || 1 === $a['filters'] || '1' === $a['filters'] ) ? 'yes' : '';
	} else {
		$filters = 'filtered' === $variant ? 'yes' : '';
	}

	$limit        = ! empty( $a['limit'] ) ? max( 1, min( 48, absint( $a['limit'] ) ) ) : ( 'filtered' === $variant ? 6 : $columns );
	$category     = ! empty( $a['category'] ) ? absint( $a['category'] ) : 0;
	$eyebrow      = isset( $a['eyebrow'] ) ? (string) $a['eyebrow'] : __( 'Catalog', 'gnn' );
	$title        = ! empty( $a['title'] ) ? (string) $a['title'] : __( 'Product showcase', 'gnn' );
	$text         = isset( $a['text'] ) ? (string) $a['text'] : __( 'Browse specifications and details across our product line.', 'gnn' );
	$button_label = ! empty( $a['button_label'] ) ? (string) $a['button_label'] : '';
	$button_url   = ! empty( $a['button_url'] ) ? (string) $a['button_url'] : '';

	$children = array(
		gnn_sec_content_header( $eyebrow, $title, $text, $button_label, $button_url ),
		gnn_st_mod(
			'catalog',
			array(
				'columns'  => $columns,
				'limit'    => $limit,
				'filters'  => $filters,
				'category' => $category,
			)
		),
	);

	$elements                                = array( gnn_st_band( 'gnn-sec', 'catalog', $children ) );
	$elements[0]['settings']['css_classes'] .= ' gnn-sec--' . $variant;
	return $elements;
}

/**
 * Register portfolio, gallery, blog and catalog sections.
 *
 * @return void
 */
function gnn_sections_register_content() {
	if ( ! function_exists( 'gnn_section_register' ) ) {
		return;
	}

	gnn_section_register(
		'portfolio',
		array(
			'label'    => __( 'Portfolio', 'gnn' ),
			'category' => 'gallery',
			'variants' => array(
				'cols-3'   => __( 'Three columns', 'gnn' ),
				'cols-2'   => __( 'Two columns', 'gnn' ),
				'cols-4'   => __( 'Four columns', 'gnn' ),
				'filtered' => __( 'With category filter', 'gnn' ),
			),
			'build'    => 'gnn_sec_build_portfolio',
		)
	);

	gnn_section_register(
		'gallery',
		array(
			'label'    => __( 'Gallery', 'gnn' ),
			'category' => 'gallery',
			'variants' => array(
				'cols-3' => __( 'Three columns', 'gnn' ),
				'cols-4' => __( 'Four columns', 'gnn' ),
				'cols-2' => __( 'Two columns', 'gnn' ),
			),
			'build'    => 'gnn_sec_build_gallery',
		)
	);

	gnn_section_register(
		'blog',
		array(
			'label'    => __( 'Latest posts', 'gnn' ),
			'category' => 'blog',
			'variants' => array(
				'grid' => __( 'Three-column grid', 'gnn' ),
				'list' => __( 'Editorial list', 'gnn' ),
			),
			'build'    => 'gnn_sec_build_blog',
		)
	);

	gnn_section_register(
		'catalog',
		array(
			'label'    => __( 'Product catalog', 'gnn' ),
			'category' => 'gallery',
			'variants' => array(
				'cols-3'   => __( 'Three columns', 'gnn' ),
				'cols-4'   => __( 'Four columns', 'gnn' ),
				'filtered' => __( 'With category filter', 'gnn' ),
			),
			'build'    => 'gnn_sec_build_catalog',
		)
	);
}
add_action( 'gnn_sections_register', 'gnn_sections_register_content' );
