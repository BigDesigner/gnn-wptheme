<?php
/**
 * Independent archive and single-post presentation controls.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** One validated schema for panel fields, imports and post overrides. */
function gnn_blog_schema() {
	$ratios = array(
		'16/9' => '16:9',
		'3/2'  => '3:2',
		'4/3'  => '4:3',
		'1/1'  => '1:1',
		'3/4'  => '3:4',
	);
	$fits   = array(
		'cover'   => __( 'Cover (fill and crop)', 'gnn' ),
		'contain' => __( 'Contain (show the whole image)', 'gnn' ),
	);
	$modes  = array(
		'ratio'   => __( 'Aspect ratio', 'gnn' ),
		'fixed'   => __( 'Fixed height', 'gnn' ),
		'natural' => __( 'Original proportions', 'gnn' ),
	);
	return array(
		'blog_width'                   => array( __( 'Listing content width (px, 0 = global container)', 'gnn' ), 'number', 0, 'blog_archive', 0, 2560 ),
		'blog_layout'                  => array(
			__( 'Listing layout', 'gnn' ),
			'select',
			'grid',
			'blog_archive',
			array(
				'grid' => __( 'Grid', 'gnn' ),
				'list' => __( 'List', 'gnn' ),
			),
		),
		'blog_columns_tablet'          => array( __( 'Maximum columns on tablet', 'gnn' ), 'number', 2, 'blog_archive', 1, 3 ),
		'blog_columns_mobile'          => array( __( 'Maximum columns on mobile', 'gnn' ), 'number', 1, 'blog_archive', 1, 2 ),
		'blog_gap'                     => array( __( 'Space between cards (px)', 'gnn' ), 'number', 24, 'blog_archive', 0, 80 ),
		'blog_card_categories'         => array( __( 'Show card categories', 'gnn' ), 'bool', 1, 'blog_archive' ),
		'blog_card_excerpt'            => array( __( 'Show card excerpts', 'gnn' ), 'bool', 0, 'blog_archive' ),
		'excerpt_length'               => array( __( 'Excerpt length (words)', 'gnn' ), 'number', 24, 'blog_archive', 5, 100 ),
		'blog_card_image_mode'         => array( __( 'Card image sizing', 'gnn' ), 'select', 'ratio', 'blog_cards', $modes + array( 'hidden' => __( 'Hide image', 'gnn' ) ) ),
		'blog_card_image_ratio'        => array( __( 'Card image aspect ratio', 'gnn' ), 'select', '16/9', 'blog_cards', $ratios ),
		'blog_card_image_height'       => array( __( 'Card image fixed height (px)', 'gnn' ), 'number', 240, 'blog_cards', 80, 1000 ),
		'blog_card_image_fit'          => array( __( 'Card image fit', 'gnn' ), 'select', 'cover', 'blog_cards', $fits ),
		'blog_card_image_position'     => array(
			__( 'Card image alignment', 'gnn' ),
			'select',
			'center',
			'blog_cards',
			array(
				'top'    => __( 'Top', 'gnn' ),
				'center' => __( 'Center', 'gnn' ),
				'bottom' => __( 'Bottom', 'gnn' ),
			),
		),
		'post_content_width'           => array( __( 'Single post content width (px)', 'gnn' ), 'number', 760, 'blog_single', 360, 2000 ),
		'post_meta'                    => array( __( 'Show metadata on single posts', 'gnn' ), 'bool', 1, 'blog_single' ),
		'post_author_box'              => array( __( 'Show the author biography box', 'gnn' ), 'bool', 1, 'blog_single' ),
		'post_show_tags'               => array( __( 'Show post tags', 'gnn' ), 'bool', 1, 'blog_single' ),
		'post_show_comments'           => array( __( 'Show the comments section', 'gnn' ), 'bool', 1, 'blog_single' ),
		'post_image_layout'            => array(
			__( 'Single featured image width', 'gnn' ),
			'select',
			'content',
			'blog_media',
			array(
				'content' => __( 'Match content width', 'gnn' ),
				'wide'    => __( 'Independent width', 'gnn' ),
				'full'    => __( 'Full viewport width', 'gnn' ),
				'hidden'  => __( 'Hide image', 'gnn' ),
			),
		),
		'post_image_width'             => array( __( 'Independent image width (px)', 'gnn' ), 'number', 1200, 'blog_media', 360, 2560 ),
		'post_image_height_mode'       => array( __( 'Single image sizing', 'gnn' ), 'select', 'fixed', 'blog_media', $modes ),
		'post_image_ratio'             => array( __( 'Single image aspect ratio', 'gnn' ), 'select', '16/9', 'blog_media', $ratios ),
		'post_featured_image_height'   => array( __( 'Blog image height (px)', 'gnn' ), 'number', 500, 'blog_media', 80, 800 ),
		'post_featured_image_fit'      => array( __( 'Blog image fit', 'gnn' ), 'select', 'cover', 'blog_media', $fits + array( 'fill' => __( 'Fill (stretch)', 'gnn' ) ) ),
		'post_featured_image_position' => array(
			__( 'Blog image alignment', 'gnn' ),
			'select',
			'center',
			'blog_media',
			array(
				'top'    => __( 'Top', 'gnn' ),
				'center' => __( 'Center', 'gnn' ),
				'bottom' => __( 'Bottom', 'gnn' ),
			),
		),
		'post_image_caption'           => array( __( 'Show the featured image caption', 'gnn' ), 'bool', 0, 'blog_media' ),
	);
}

/**
 * Validate inherited or per-post geometry again at its rendering boundary.
 *
 * @param string $key Blog schema key.
 * @return int|string
 */
function gnn_blog_value( $key ) {
	$schema = gnn_blog_schema();
	$field  = $schema[ $key ];
	$value  = is_singular( 'post' ) ? gnn_layout_value( $key ) : gnn_option( $key );
	if ( ! is_scalar( $value ) ) {
		return $field[2];
	}
	if ( 'number' === $field[1] ) {
		$value = max( $field[4], min( $field[5], (int) $value ) );
		return 'blog_width' === $key && $value > 0 ? max( 360, $value ) : $value;
	}
	return 'select' === $field[1] && ! isset( $field[4][ $value ] ) ? $field[2] : $value;
}

/**
 * Responsive source selection reflects the actual media slot, not a cropped derivative.
 *
 * @param bool $single Single-post image rather than a listing image.
 * @return string
 */
function gnn_blog_image_sizes( $single = false ) {
	if ( $single ) {
		$layout = gnn_blog_value( 'post_image_layout' );
		if ( 'full' === $layout ) {
			return '100vw';
		}
		$width = (int) gnn_blog_value( 'wide' === $layout ? 'post_image_width' : 'post_content_width' );
		return '(max-width: ' . ( $width + 64 ) . 'px) calc(100vw - 32px), ' . $width . 'px';
	}
	$width  = (int) gnn_option( 'blog_width' );
	$width  = $width ? $width : (int) gnn_option( 'container_width' );
	$cols   = max( 1, (int) gnn_option( 'blog_columns' ) );
	$tablet = min( $cols, (int) gnn_blog_value( 'blog_columns_tablet' ) );
	$mobile = min( $tablet, (int) gnn_blog_value( 'blog_columns_mobile' ) );
	if ( 'list' === gnn_blog_value( 'blog_layout' ) ) {
		$cols   = 3;
		$tablet = 3;
		$mobile = 1; }
	return '(max-width: 600px) ' . (int) ceil( 100 / $mobile ) . 'vw, (max-width: 900px) ' . (int) ceil( 100 / $tablet ) . 'vw, ' . (int) ceil( $width / $cols ) . 'px';
}

/** Render uncropped, responsive single-post media independently from the text column. */
function gnn_single_featured_image() {
	$layout = gnn_blog_value( 'post_image_layout' );
	if ( 'hidden' === $layout || post_password_required() || ! has_post_thumbnail() || is_attachment() ) {
		return;
	}
	echo '<figure class="post-thumbnail gnn-post-media--' . esc_attr( $layout ) . ' gnn-post-media-height--' . esc_attr( gnn_blog_value( 'post_image_height_mode' ) ) . '">';
	the_post_thumbnail( 'full', array( 'sizes' => gnn_blog_image_sizes( true ) ) );
	echo gnn_title_overlay_html(); // phpcs:ignore WordPress.Security.EscapeOutput -- helper escapes title and styles.
	$caption = wp_get_attachment_caption( get_post_thumbnail_id() );
	if ( gnn_option( 'post_image_caption' ) && $caption ) {
		echo '<figcaption>' . esc_html( $caption ) . '</figcaption>';
	}
	echo '</figure>';
}

/**
 * Emit scoped geometry after the design system, retaining all established option keys.
 */
function gnn_enqueue_blog_styles() {
	if ( ! ( is_home() || is_category() || is_tag() || is_author() || is_date() || is_singular( 'post' ) ) ) {
		return;
	}
	wp_enqueue_style( 'gnn-blog', get_template_directory_uri() . '/assets/css/blog.css', array( 'gnn-design-system' ), GNN_VERSION );
	$vars = '';
	foreach ( array( 'post_content_width', 'post_image_width', 'post_featured_image_height', 'blog_gap', 'blog_card_image_height' ) as $key ) {
		$vars .= '--gnn-' . str_replace( '_', '-', $key ) . ':' . (int) gnn_blog_value( $key ) . 'px;';
	}
	foreach ( array( 'post_image_ratio', 'post_featured_image_fit', 'post_featured_image_position', 'blog_card_image_ratio', 'blog_card_image_fit', 'blog_card_image_position' ) as $key ) {
		$vars .= '--gnn-' . str_replace( '_', '-', $key ) . ':' . gnn_blog_value( $key ) . ';';
	}
	$width = (int) gnn_blog_value( 'blog_width' );
	if ( $width ) {
		$vars .= '--gnn-blog-outer:calc(' . $width . 'px + 2 * var(--gnn-post-gutter));';
	}
	$desktop = max( 1, (int) gnn_option( 'blog_columns' ) );
	$tablet  = min( $desktop, (int) gnn_blog_value( 'blog_columns_tablet' ) );
	$mobile  = min( $tablet, (int) gnn_blog_value( 'blog_columns_mobile' ) );
	$vars   .= '--gnn-blog-tablet:' . $tablet . ';--gnn-blog-mobile:' . $mobile . ';';
	wp_add_inline_style( 'gnn-blog', ':root{' . $vars . '}' );
}
add_action( 'wp_enqueue_scripts', 'gnn_enqueue_blog_styles', 15 );

/**
 * Classes select validated presentation modes without inline untrusted CSS.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function gnn_blog_body_classes( $classes ) {
	$classes[] = 'gnn-blog-layout-' . gnn_blog_value( 'blog_layout' );
	$classes[] = 'gnn-blog-images-' . gnn_blog_value( 'blog_card_image_mode' );
	return $classes;
}
add_filter( 'body_class', 'gnn_blog_body_classes' );
