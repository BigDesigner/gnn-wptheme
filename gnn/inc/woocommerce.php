<?php
/**
 * WooCommerce integration for the GNN theme.
 *
 * Cart, Checkout and My Account keep WooCommerce's standard templates and
 * classes; the design is applied through assets/css/woocommerce.css so
 * plugin updates stay safe. The shop-loop product card is overridden in
 * woocommerce/content-product.php to match the design exactly.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Declare WooCommerce support (+ product gallery features per spec §6).
 */
function gnn_woocommerce_setup() {
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 640,
			'single_image_width'    => 960,
		)
	);
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'gnn_woocommerce_setup' );

/**
 * Wrap WooCommerce pages in the theme's main container.
 */
function gnn_woocommerce_wrapper_before() {
	echo '<main id="primary" class="site-main gnn-container woocommerce-main">';
}
/**
 * Close the theme wrapper opened above.
 */
function gnn_woocommerce_wrapper_after() {
	echo '</main>';
}
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
add_action( 'woocommerce_before_main_content', 'gnn_woocommerce_wrapper_before' );
add_action( 'woocommerce_after_main_content', 'gnn_woocommerce_wrapper_after' );

// The theme ships its own sidebar handling; the shop grid is full-width.
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/**
 * Shop loop: columns / per-page come from the GNN Panel (Blog & Shop tab).
 */
add_filter(
	'loop_shop_columns',
	function () {
		return (int) gnn_option( 'shop_columns' );
	}
);
add_filter(
	'loop_shop_per_page',
	function () {
		return (int) gnn_option( 'shop_per_page' );
	}
);

/**
 * Related products: 4 columns.
 *
 * @param array $args Related products query args.
 * @return array
 */
function gnn_related_products_args( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'gnn_related_products_args' );

/**
 * Keep the header cart count in sync via AJAX fragments.
 *
 * @param array $fragments Cart fragments.
 * @return array
 */
function gnn_cart_count_fragment( $fragments ) {
	ob_start();
	?>
	<span class="cart-count"><?php echo esc_html( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?></span>
	<?php
	$fragments['.cart-count'] = ob_get_clean();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'gnn_cart_count_fragment' );

/**
 * Breadcrumb markup aligned with the theme's .gnn-breadcrumb style.
 *
 * @param array $defaults Breadcrumb defaults.
 * @return array
 */
function gnn_woocommerce_breadcrumbs( $defaults ) {
	$defaults['delimiter']   = '<span class="gnn-breadcrumb__sep">/</span>';
	$defaults['wrap_before'] = '<nav class="gnn-breadcrumb woocommerce-breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'gnn' ) . '">';
	$defaults['wrap_after']  = '</nav>';
	return $defaults;
}
add_filter( 'woocommerce_breadcrumb_defaults', 'gnn_woocommerce_breadcrumbs' );

/**
 * "Add to cart" label matching the design.
 */
add_filter(
	'woocommerce_product_add_to_cart_text',
	function ( $text, $product ) {
		if ( $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() ) {
			return __( 'Add to cart', 'gnn' );
		}
		return $text;
	},
	10,
	2
);
