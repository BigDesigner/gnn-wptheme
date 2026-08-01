<?php
/**
 * Shop-loop product card matching the GNN design.
 *
 * Overrides woocommerce/templates/content-product.php.
 *
 * @package GNN
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<li <?php wc_product_class( 'product-card', $product ); ?>>

	<a href="<?php the_permalink(); ?>" class="product-card__media woocommerce-LoopProduct-link">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'gnn-card' );
		} else {
			echo '<span class="product-card__media-empty" aria-hidden="true"></span>';
		}
		?>
	</a>

	<div class="product-card__body">
		<?php
		$gnn_terms = get_the_terms( $product->get_id(), 'product_cat' );
		if ( $gnn_terms && ! is_wp_error( $gnn_terms ) ) :
			?>
			<div class="entry-kicker product-card__cat"><?php echo esc_html( $gnn_terms[0]->name ); ?></div>
		<?php endif; ?>

		<a href="<?php the_permalink(); ?>" class="product-card__title woocommerce-loop-product__title"><?php the_title(); ?></a>

		<div class="product-card__footer">
			<span class="price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
			<?php
			woocommerce_template_loop_add_to_cart(
				array(
					'class' => implode(
						' ',
						array_filter(
							array(
								'button',
								'product-card__button',
								'product_type_' . $product->get_type(),
								$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
								$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
							)
						)
					),
				)
			);
			?>
		</div>
	</div>

</li>
