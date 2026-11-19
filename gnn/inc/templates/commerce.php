<?php
/**
 * Ready-made site template functions.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Starter profile settings. */
function gnn_st_profile_commerce() {
	return array(
		'label'        => __( 'GNNcommerce', 'gnn' ),
		'accent'       => '#2fbf8f',
		'accent_light' => '#0f7a5a',
		'header'       => 'shop',
		'radius'       => 14,
		'font'         => 'manrope',
		'mode'         => 'light',
		'light'        => array( '#fbfaf7', '#f3f0e9', '#e8e3d8', '#1c2420', '#5a645e', '#e3ded2' ),
		'dark'         => array( '#0f1512', '#161e1a', '#1f2924', '#f2f5f3', '#a3b0a9', '#27322c' ),
		'options'      => array(
			'header_search_bar' => 1,
			'show_search'       => 0,
			'show_cart'         => 1,
			'topbar_enable'     => 1,
			'topbar_text'       => __( 'Easy returns and secure checkout', 'gnn' ),
		),
		'heading'      => __( 'Considered products. Everyday possibilities.', 'gnn' ),
		'description'  => __( 'A WooCommerce storefront with live products, categories, cart, checkout and customer account links.', 'gnn' ),
	);
}

/** Page keys and titles. */
function gnn_st_pages_commerce() {
	return array(
		'home'     => __( 'Home', 'gnn' ),
		'about'    => __( 'Our story', 'gnn' ),
		'delivery' => __( 'Delivery and returns', 'gnn' ),
		'news'     => __( 'Journal', 'gnn' ),
		'contact'  => __( 'Contact', 'gnn' ),
	);
}

/** Bundled demo data. */
function gnn_dp_data_commerce() {
	return array(
		'images'             => array( 'product-1', 'product-2', 'product-3', 'product-4', 'product-5', 'product-6', 'product-7', 'product-8', 'slide-1', 'slide-2', 'slide-3', 'banner-1', 'banner-2', 'news-1', 'news-2', 'news-3' ),
		'projects'           => array(),
		'articles'           => array(
			array( __( 'How to care for everyday ceramics', 'gnn' ), __( 'A few simple habits keep glazed pieces looking new for years: gentle washing, careful stacking and sensible heat.', 'gnn' ) ),
			array( __( 'Five ways to refresh a room', 'gnn' ), __( 'Small changes in light, texture and plants can change how a room feels without a big budget.', 'gnn' ) ),
			array( __( 'Choosing a bag that lasts', 'gnn' ), __( 'Look at the stitching, the handles and the lining. Good construction matters more than the label.', 'gnn' ) ),
		),
		'slide_group' => __( 'Storefront highlights', 'gnn' ),
		'slides'             => array(
			'slide-1' => array( __( 'Made well. Made to last.', 'gnn' ), __( 'New season', 'gnn' ), __( 'Considered products for everyday life, delivered to your door.', 'gnn' ), __( 'Shop now', 'gnn' ) ),
			'slide-2' => array( __( 'Quiet design for busy days', 'gnn' ), __( 'Essentials', 'gnn' ), __( 'Simple, durable pieces that earn their place at home.', 'gnn' ), __( 'Shop essentials', 'gnn' ) ),
			'slide-3' => array( __( 'Brighten your space', 'gnn' ), __( 'Home', 'gnn' ), __( 'Lighting, plants and small details that change a room.', 'gnn' ), __( 'Explore home', 'gnn' ) ),
		),
		'products'           => array(
			'product-1' => array( __( 'Canvas tote bag', 'gnn' ), '34', 'lifestyle', __( 'A sturdy everyday tote with a roomy interior and reinforced handles.', 'gnn' ) ),
			'product-2' => array( __( 'Stoneware mug', 'gnn' ), '18', 'kitchen', __( 'A hand-glazed mug that feels good in the hand and survives the dishwasher.', 'gnn' ) ),
			'product-3' => array( __( 'Table lamp', 'gnn' ), '86', 'home', __( 'A soft, warm light with a simple shade and a weighted base.', 'gnn' ) ),
			'product-4' => array( __( 'Steel water bottle', 'gnn' ), '27', 'kitchen', __( 'Keeps drinks cold for a day and hot for hours. Leak-proof lid.', 'gnn' ) ),
			'product-5' => array( __( 'Lounge chair', 'gnn' ), '249', 'home', __( 'A low, comfortable chair with a solid frame and washable cover.', 'gnn' ) ),
			'product-6' => array( __( 'Potted plant', 'gnn' ), '22', 'home', __( 'A hardy houseplant in a glazed pot, easy to care for.', 'gnn' ) ),
			'product-7' => array( __( 'Linen notebook', 'gnn' ), '14', 'extras', __( 'Stitched pages and a linen cover for notes you want to keep.', 'gnn' ) ),
			'product-8' => array( __( 'Wireless headphones', 'gnn' ), '119', 'extras', __( 'Comfortable over-ear headphones with a long-lasting battery.', 'gnn' ) ),
		),
		'product_categories' => array(
			'home'      => __( 'Home', 'gnn' ),
			'kitchen'   => __( 'Kitchen', 'gnn' ),
			'lifestyle' => __( 'Lifestyle', 'gnn' ),
			'extras'    => __( 'Accessories', 'gnn' ),
		),
	);
}

/* ---------------------------------------------------------------------------
 * GNNcommerce - WooCommerce storefront
 * ------------------------------------------------------------------------- */

/** WooCommerce page URL or the home page. */
function gnn_st_shop_url( $utility ) {
	if ( function_exists( 'wc_get_page_id' ) ) {
		$id = wc_get_page_id( $utility );
		if ( $id > 0 ) {
			return get_permalink( $id );
		}
	}
	return home_url( '/' );
}

function gnn_st_commerce_home( $ids ) {
	$s      = 'gnn-shop';
	$shop   = gnn_st_shop_url( 'shop' );
	$group  = (int) get_option( 'gnn_demo_slide_group_commerce', 0 );
	$hero   = $group
		? gnn_st_mod(
			'slider',
			array(
				'category' => $group,
				'limit'    => 3,
			),
			'gnn-shop-slider'
		)
		: gnn_st_c( $s . ' ' . $s . '-hero', array( gnn_st_c( 'gnn-wrap gnn-copy', array( gnn_st_p( __( 'New season', 'gnn' ), 'gnn-eyebrow' ), gnn_st_h( __( 'Made well. Made to last.', 'gnn' ), 1 ), gnn_st_b( __( 'Shop now', 'gnn' ), $shop, 'gnn-btn-main' ) ), true ) ) );
	$usp    = array();
	foreach ( array(
		array( __( 'Fast dispatch', 'gnn' ), __( 'Orders leave quickly', 'gnn' ) ),
		array( __( 'Easy returns', 'gnn' ), __( 'Simple, clear process', 'gnn' ) ),
		array( __( 'Secure payment', 'gnn' ), __( 'Trusted checkout', 'gnn' ) ),
		array( __( 'Friendly support', 'gnn' ), __( 'Real people to help', 'gnn' ) ),
	) as $item ) {
		$usp[] = gnn_st_c( 'gnn-usp-item', array( gnn_st_h( $item[0], 4 ), gnn_st_p( $item[1] ) ), true );
	}
	return array(
		$hero,
		gnn_st_band( $s, 'usp', array( gnn_st_c( 'gnn-usp', $usp, true ) ) ),
		gnn_st_band(
			$s,
			'categories',
			array(
				gnn_st_head( __( 'Browse', 'gnn' ), __( 'Shop by category', 'gnn' ) ),
				gnn_st_mod(
					'products',
					array(
						'source'  => 'categories',
						'columns' => 4,
						'limit'   => 4,
					)
				),
			)
		),
		gnn_st_band(
			$s,
			'featured',
			array(
				gnn_st_c( 'gnn-headrow', array( gnn_st_head( __( 'Featured', 'gnn' ), __( 'Popular right now', 'gnn' ) ), gnn_st_b( __( 'View all products', 'gnn' ), $shop, 'gnn-link' ) ), true ),
				gnn_st_mod(
					'products',
					array(
						'columns' => 4,
						'limit'   => 8,
					)
				),
			)
		),
		gnn_st_band(
			$s,
			'banners',
			array(
				gnn_st_c(
					'gnn-banners',
					array(
						gnn_st_c( 'gnn-banner', array( gnn_st_img( 'commerce', 'banner-1', __( 'Home collection', 'gnn' ) ), gnn_st_c( 'gnn-banner-copy', array( gnn_st_p( __( 'Collection', 'gnn' ), 'gnn-eyebrow' ), gnn_st_h( __( 'For the home', 'gnn' ), 3 ), gnn_st_b( __( 'Shop the edit', 'gnn' ), $shop, 'gnn-btn-main' ) ), true ) ), true ),
						gnn_st_c( 'gnn-banner', array( gnn_st_img( 'commerce', 'banner-2', __( 'Everyday essentials', 'gnn' ) ), gnn_st_c( 'gnn-banner-copy', array( gnn_st_p( __( 'Essentials', 'gnn' ), 'gnn-eyebrow' ), gnn_st_h( __( 'Everyday favourites', 'gnn' ), 3 ), gnn_st_b( __( 'Shop essentials', 'gnn' ), $shop, 'gnn-btn-main' ) ), true ) ), true ),
					),
					true
				),
			)
		),
		gnn_st_band(
			$s,
			'story',
			array(
				gnn_st_c(
					'gnn-story',
					array(
						gnn_st_h( __( 'Good things, carefully chosen', 'gnn' ), 2 ),
						gnn_st_p( __( 'Tell customers how you select products, who makes them and what you stand for.', 'gnn' ) ),
						gnn_st_b( __( 'Our story', 'gnn' ), gnn_st_link( $ids, 'about' ), 'gnn-btn-ghost' ),
					),
					true
				),
			)
		),
		gnn_st_cta( $s, __( 'Questions about an order?', 'gnn' ), __( 'Delivery and returns', 'gnn' ), gnn_st_link( $ids, 'delivery' ) ),
	);
}

function gnn_st_commerce_about( $ids ) {
	$s = 'gnn-shop';
	return array(
		gnn_st_page_head( $s, __( 'Our story', 'gnn' ), __( 'Why we do what we do.', 'gnn' ), __( 'Introduce the people and ideas behind the store.', 'gnn' ) ),
		gnn_st_band( $s, 'about', array( gnn_st_c( 'gnn-split', array( gnn_st_c( 'gnn-visual', array( gnn_st_img( 'commerce', 'banner-1', __( 'Home collection', 'gnn' ) ) ), true ), gnn_st_c( 'gnn-copy', array( gnn_st_h( __( 'Made with care', 'gnn' ), 2 ), gnn_st_p( __( 'Explain how products are chosen and made.', 'gnn' ) ), gnn_st_b( __( 'Shop now', 'gnn' ), gnn_st_shop_url( 'shop' ), 'gnn-btn-main' ) ), true ) ), true ) ) ),
		gnn_st_cta( $s, __( 'Find something you love', 'gnn' ), __( 'Shop now', 'gnn' ), gnn_st_shop_url( 'shop' ) ),
	);
}

function gnn_st_commerce_delivery( $ids ) {
	$s     = 'gnn-shop';
	$items = array();
	foreach ( array(
		array( __( 'Delivery', 'gnn' ), __( 'Add your delivery areas, times and costs.', 'gnn' ) ),
		array( __( 'Returns', 'gnn' ), __( 'Explain how long customers have and how to send items back.', 'gnn' ) ),
		array( __( 'Payment', 'gnn' ), __( 'List the payment methods you accept.', 'gnn' ) ),
		array( __( 'Help', 'gnn' ), __( 'Say how customers can reach you about an order.', 'gnn' ) ),
	) as $item ) {
		$items[] = gnn_st_c( 'gnn-card', array( gnn_st_h( $item[0], 3 ), gnn_st_p( $item[1] ) ), true );
	}
	return array(
		gnn_st_page_head( $s, __( 'Help', 'gnn' ), __( 'Delivery and returns', 'gnn' ), __( 'What to expect after you order.', 'gnn' ) ),
		gnn_st_band( $s, 'info', array( gnn_st_c( 'gnn-cards gnn-cols-2', $items, true ) ) ),
		gnn_st_cta( $s, __( 'Still need help?', 'gnn' ), __( 'Contact us', 'gnn' ), gnn_st_link( $ids, 'contact' ) ),
	);
}

function gnn_st_commerce_news( $ids ) {
	$s    = 'gnn-shop';
	$blog = (int) get_option( 'page_for_posts' );
	return array(
		gnn_st_page_head( $s, __( 'Journal', 'gnn' ), __( 'Stories and guides.', 'gnn' ), __( 'Product care, new arrivals and ideas.', 'gnn' ) ),
		gnn_st_band( $s, 'news', array( $blog && 'publish' === get_post_status( $blog ) ? gnn_st_b( __( 'Read the latest articles', 'gnn' ), get_permalink( $blog ), 'gnn-btn-main' ) : gnn_st_p( __( 'Publish articles from Posts.', 'gnn' ) ) ) ),
	);
}

function gnn_st_commerce_contact( $ids ) {
	$s = 'gnn-shop';
	return array(
		gnn_st_page_head( $s, __( 'Contact', 'gnn' ), __( 'We’re here to help.', 'gnn' ), __( 'Questions about products or an order? Get in touch.', 'gnn' ) ),
		gnn_st_band( $s, 'contact', array( gnn_st_c( 'gnn-split', array( gnn_st_c( 'gnn-copy', array( gnn_st_h( __( 'Customer care', 'gnn' ), 2 ), gnn_st_contact() ), true ), gnn_st_c( 'gnn-card gnn-note', array( gnn_st_h( __( 'My account', 'gnn' ), 3 ), gnn_st_p( __( 'Track orders and manage your details.', 'gnn' ) ), gnn_st_b( __( 'My account', 'gnn' ), gnn_st_shop_url( 'myaccount' ), 'gnn-btn-main' ) ), true ) ), true ) ) ),
	);
}

