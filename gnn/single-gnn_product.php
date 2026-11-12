<?php
/**
 * A centrally managed non-commerce product detail.
 *
 * @package GNN
 */

// phpcs:disable WordPress.Files.FileName -- WordPress template hierarchy requires the registered CPT key.
get_header();
?>
<main id="primary" class="gnn-project-single<?php echo gnn_layout_value( 'sidebar_template' ) > 0 ? ' gnn-custom-sidebar' : ''; ?>">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<article class="gnn-project-canvas">
			<header class="gnn-project-intro"><h1><?php the_title(); ?></h1><?php the_excerpt(); ?></header>
			<?php if ( ! post_password_required() ) : ?>
				<div class="gnn-project-cover"><?php echo gnn_product_image( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- native featured-image HTML. ?></div>
				<dl class="gnn-project-facts">
					<?php
					foreach ( array(
						'code'       => __( 'Product code', 'gnn' ),
						'material'   => __( 'Material', 'gnn' ),
						'dimensions' => __( 'Dimensions', 'gnn' ),
					) as $gnn_key => $gnn_label ) :
						$gnn_value = get_post_meta( get_the_ID(), '_gnn_product_' . $gnn_key, true );
						if ( $gnn_value ) :
							?>
							<div><dt><?php echo esc_html( $gnn_label ); ?></dt><dd><?php echo esc_html( $gnn_value ); ?></dd></div>
						<?php endif; ?>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>
			<?php the_content(); ?>
			<?php if ( ! post_password_required() ) : ?>
				<?php
				// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Shared renderer escapes output.
				echo gnn_render_module(
					'gallery',
					array(
						'source' => 'product',
						'record' => get_the_ID(),
					)
				);
				// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
				<?php
				// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Navigation helper escapes output.
				echo gnn_product_navigation( get_the_ID() );
				// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			<?php endif; ?>
		</article>
	<?php endwhile; ?>
	<?php
	if ( gnn_layout_value( 'sidebar_template' ) > 0 ) {
		get_sidebar(); }
	?>
</main>
<?php get_footer(); ?>
