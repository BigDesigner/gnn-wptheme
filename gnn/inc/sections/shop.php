<?php
/**
 * WooCommerce storefront sections for the Elementor Free section library.
 * Registered only when WooCommerce is active.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve the primary WooCommerce shop page URL or fallback to home.
 *
 * @return string
 */
function gnn_sec_shop_page_url() {
	if ( function_exists( 'wc_get_page_id' ) ) {
		$shop_id = (int) wc_get_page_id( 'shop' );
		if ( $shop_id > 0 && 'publish' === get_post_status( $shop_id ) ) {
			return (string) get_permalink( $shop_id );
		}
	}
	return home_url( '/' );
}

/**
 * Build the Product Categories (`product-categories`) section.
 *
 * Variants: `cols-4`, `cols-3`.
 *
 * @param array $a Section arguments and overrides.
 * @return array
 */
function gnn_sec_build_product_categories( $a = array() ) {
	$raw_variant = isset( $a['variant'] ) && is_string( $a['variant'] ) ? sanitize_key( $a['variant'] ) : 'cols-4';
	$variant     = in_array( $raw_variant, array( 'cols-4', 'cols-3' ), true ) ? $raw_variant : 'cols-4';

	if ( ! empty( $a['columns'] ) && in_array( (int) $a['columns'], array( 2, 3, 4 ), true ) ) {
		$columns = (int) $a['columns'];
	} elseif ( 'cols-3' === $variant ) {
		$columns = 3;
	} else {
		$columns = 4;
	}

	$limit    = ! empty( $a['limit'] ) ? max( 1, min( 24, absint( $a['limit'] ) ) ) : $columns;
	$category = ! empty( $a['category'] ) ? absint( $a['category'] ) : 0;
	$eyebrow  = isset( $a['eyebrow'] ) ? (string) $a['eyebrow'] : __( 'Browse', 'gnn' );
	$title    = ! empty( $a['title'] ) ? (string) $a['title'] : __( 'Shop by category', 'gnn' );
	$text     = isset( $a['text'] ) ? (string) $a['text'] : '';

	$children = array(
		gnn_st_head( $eyebrow, $title, $text ),
		gnn_st_mod(
			'products',
			array(
				'source'   => 'categories',
				'columns'  => $columns,
				'limit'    => $limit,
				'category' => $category,
			)
		),
	);

	$elements                                = array( gnn_st_band( 'gnn-sec', 'product-categories', $children ) );
	$elements[0]['settings']['css_classes'] .= ' gnn-sec--' . $variant;
	return $elements;
}

/**
 * Build the Featured Products (`featured-products`) section.
 *
 * Variants: `cols-4`, `cols-3`.
 *
 * @param array $a Section arguments and overrides.
 * @return array
 */
function gnn_sec_build_featured_products( $a = array() ) {
	$raw_variant = isset( $a['variant'] ) && is_string( $a['variant'] ) ? sanitize_key( $a['variant'] ) : 'cols-4';
	$variant     = in_array( $raw_variant, array( 'cols-4', 'cols-3' ), true ) ? $raw_variant : 'cols-4';

	if ( ! empty( $a['columns'] ) && in_array( (int) $a['columns'], array( 3, 4 ), true ) ) {
		$columns = (int) $a['columns'];
	} elseif ( 'cols-3' === $variant ) {
		$columns = 3;
	} else {
		$columns = 4;
	}

	$limit        = ! empty( $a['limit'] ) ? max( 1, min( 24, absint( $a['limit'] ) ) ) : ( $columns * 2 );
	$category     = ! empty( $a['category'] ) ? absint( $a['category'] ) : 0;
	$eyebrow      = isset( $a['eyebrow'] ) ? (string) $a['eyebrow'] : __( 'Featured', 'gnn' );
	$title        = ! empty( $a['title'] ) ? (string) $a['title'] : __( 'Popular right now', 'gnn' );
	$text         = isset( $a['text'] ) ? (string) $a['text'] : '';
	$button_label = isset( $a['button_label'] ) ? (string) $a['button_label'] : __( 'View all products', 'gnn' );
	$button_url   = ! empty( $a['button_url'] ) ? (string) $a['button_url'] : gnn_sec_shop_page_url();

	$head = gnn_st_head( $eyebrow, $title, $text );
	if ( '' !== $button_label ) {
		$header = gnn_st_c(
			'gnn-headrow',
			array(
				$head,
				gnn_st_b( $button_label, $button_url, 'gnn-link' ),
			),
			true
		);
	} else {
		$header = $head;
	}

	$children = array(
		$header,
		gnn_st_mod(
			'products',
			array(
				'source'   => 'products',
				'columns'  => $columns,
				'limit'    => $limit,
				'category' => $category,
			)
		),
	);

	$elements                                = array( gnn_st_band( 'gnn-sec', 'featured-products', $children ) );
	$elements[0]['settings']['css_classes'] .= ' gnn-sec--' . $variant;
	return $elements;
}

/**
 * Build the Promotional Banners (`promo-banners`) section with two image-cover banners.
 *
 * Variants: `overlay`, `split-card`.
 *
 * @param array $a Section arguments and overrides.
 * @return array
 */
function gnn_sec_build_promo_banners( $a = array() ) {
	$raw_variant = isset( $a['variant'] ) && is_string( $a['variant'] ) ? sanitize_key( $a['variant'] ) : 'overlay';
	$variant     = in_array( $raw_variant, array( 'overlay', 'split-card' ), true ) ? $raw_variant : 'overlay';

	$default_img  = ! empty( $a['image'] ) ? (string) $a['image'] : get_theme_file_uri( 'assets/img/placeholder-cover.svg' );
	$default_url  = ! empty( $a['button_url'] ) ? (string) $a['button_url'] : gnn_sec_shop_page_url();
	$default_list = array(
		array(
			'eyebrow'      => __( 'Collection', 'gnn' ),
			'title'        => __( 'Seasonal highlights', 'gnn' ),
			'text'         => __( 'Curated pieces chosen for everyday use.', 'gnn' ),
			'button_label' => __( 'Shop the edit', 'gnn' ),
			'button_url'   => $default_url,
			'image'        => $default_img,
		),
		array(
			'eyebrow'      => __( 'Essentials', 'gnn' ),
			'title'        => __( 'Everyday favourites', 'gnn' ),
			'text'         => __( 'Simple, durable staples back in stock.', 'gnn' ),
			'button_label' => __( 'Shop essentials', 'gnn' ),
			'button_url'   => $default_url,
			'image'        => $default_img,
		),
	);

	$source_items = ! empty( $a['items'] ) && is_array( $a['items'] ) ? array_slice( array_values( $a['items'] ), 0, 4 ) : $default_list;
	$banners      = array();

	foreach ( $source_items as $index => $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}
		$fallback = isset( $default_list[ $index ] ) ? $default_list[ $index ] : $default_list[0];
		$eyebrow  = isset( $item['eyebrow'] ) ? (string) $item['eyebrow'] : ( isset( $item[2] ) ? (string) $item[2] : $fallback['eyebrow'] );
		$b_title  = isset( $item['title'] ) ? (string) $item['title'] : ( isset( $item[0] ) ? (string) $item[0] : $fallback['title'] );
		$b_text   = isset( $item['text'] ) ? (string) $item['text'] : ( isset( $item[1] ) ? (string) $item[1] : $fallback['text'] );
		$b_label  = isset( $item['button_label'] ) ? (string) $item['button_label'] : $fallback['button_label'];
		$b_url    = isset( $item['button_url'] ) ? (string) $item['button_url'] : $fallback['button_url'];
		$b_img    = ! empty( $item['image'] ) ? (string) $item['image'] : $default_img;

		$banners[] = gnn_st_c(
			'gnn-banner',
			array(
				gnn_st_w(
					'image',
					array(
						'image'      => array(
							'url' => $b_img,
							'id'  => '',
							'alt' => $b_title,
						),
						'image_size' => 'full',
						'link_to'    => 'none',
					),
					'gnn-banner-media'
				),
				gnn_st_c(
					'gnn-banner-copy',
					array(
						'' !== $eyebrow ? gnn_st_p( $eyebrow, 'gnn-eyebrow' ) : null,
						gnn_st_h( $b_title, 3 ),
						'' !== $b_text ? gnn_st_p( $b_text ) : null,
						'' !== $b_label ? gnn_st_b( $b_label, $b_url, 'gnn-btn-main' ) : null,
					),
					true
				),
			),
			true
		);
	}

	$children = array(
		! empty( $a['title'] ) ? gnn_st_head( isset( $a['eyebrow'] ) ? (string) $a['eyebrow'] : '', (string) $a['title'], isset( $a['text'] ) ? (string) $a['text'] : '' ) : null,
		gnn_st_c( 'gnn-banners gnn-cols-2', $banners, true ),
	);

	$elements                                = array( gnn_st_band( 'gnn-sec', 'promo-banners', $children ) );
	$elements[0]['settings']['css_classes'] .= ' gnn-sec--' . $variant;
	return $elements;
}

/**
 * Build the USP Strip (`usp-strip`) 4-item store benefit bar.
 *
 * Variants: `strip`, `cards`.
 *
 * @param array $a Section arguments and overrides.
 * @return array
 */
function gnn_sec_build_usp_strip( $a = array() ) {
	$raw_variant = isset( $a['variant'] ) && is_string( $a['variant'] ) ? sanitize_key( $a['variant'] ) : 'strip';
	$variant     = in_array( $raw_variant, array( 'strip', 'cards' ), true ) ? $raw_variant : 'strip';

	$default_items = array(
		array( __( 'Fast dispatch', 'gnn' ), __( 'Orders leave quickly', 'gnn' ) ),
		array( __( 'Easy returns', 'gnn' ), __( 'Simple, clear process', 'gnn' ) ),
		array( __( 'Secure payment', 'gnn' ), __( 'Trusted checkout', 'gnn' ) ),
		array( __( 'Friendly support', 'gnn' ), __( 'Real people to help', 'gnn' ) ),
	);

	$source_items = ! empty( $a['items'] ) && is_array( $a['items'] ) ? array_slice( array_values( $a['items'] ), 0, 4 ) : $default_items;
	$usp_elements = array();

	foreach ( $source_items as $index => $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}
		$fallback   = isset( $default_items[ $index ] ) ? $default_items[ $index ] : $default_items[0];
		$item_title = isset( $item['title'] ) ? (string) $item['title'] : ( isset( $item[0] ) ? (string) $item[0] : $fallback[0] );
		$item_text  = isset( $item['text'] ) ? (string) $item['text'] : ( isset( $item[1] ) ? (string) $item[1] : $fallback[1] );

		$usp_elements[] = gnn_st_c(
			'gnn-usp-item',
			array(
				gnn_st_h( $item_title, 3 ),
				'' !== $item_text ? gnn_st_p( $item_text ) : null,
			),
			true
		);
	}

	$children = array(
		gnn_st_c( 'gnn-usp gnn-cols-4', $usp_elements, true ),
	);

	$elements                                = array( gnn_st_band( 'gnn-sec', 'usp-strip', $children ) );
	$elements[0]['settings']['css_classes'] .= ' gnn-sec--' . $variant;
	return $elements;
}

/**
 * Register WooCommerce sections only when WooCommerce is active.
 *
 * @return void
 */
function gnn_sections_register_shop() {
	if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'gnn_section_register' ) ) {
		return;
	}

	gnn_section_register(
		'product-categories',
		array(
			'label'    => __( 'Product categories', 'gnn' ),
			'category' => 'shop',
			'variants' => array(
				'cols-4' => __( 'Four columns', 'gnn' ),
				'cols-3' => __( 'Three columns', 'gnn' ),
			),
			'build'    => 'gnn_sec_build_product_categories',
		)
	);

	gnn_section_register(
		'featured-products',
		array(
			'label'    => __( 'Featured products', 'gnn' ),
			'category' => 'shop',
			'variants' => array(
				'cols-4' => __( 'Four columns', 'gnn' ),
				'cols-3' => __( 'Three columns', 'gnn' ),
			),
			'build'    => 'gnn_sec_build_featured_products',
		)
	);

	gnn_section_register(
		'promo-banners',
		array(
			'label'    => __( 'Promotional banners', 'gnn' ),
			'category' => 'shop',
			'variants' => array(
				'overlay'    => __( 'Image overlay', 'gnn' ),
				'split-card' => __( 'Split cards', 'gnn' ),
			),
			'build'    => 'gnn_sec_build_promo_banners',
		)
	);

	gnn_section_register(
		'usp-strip',
		array(
			'label'    => __( 'Store highlights', 'gnn' ),
			'category' => 'shop',
			'variants' => array(
				'strip' => __( 'Bordered strip', 'gnn' ),
				'cards' => __( 'Cards', 'gnn' ),
			),
			'build'    => 'gnn_sec_build_usp_strip',
		)
	);
}
add_action( 'gnn_sections_register', 'gnn_sections_register_shop' );
