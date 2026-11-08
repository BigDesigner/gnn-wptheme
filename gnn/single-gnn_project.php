<?php
/**
 * A centrally managed project with automatic gallery and category adjacency.
 *
 * @package GNN
 */

// phpcs:disable WordPress.Files.FileName -- WordPress template hierarchy requires the registered CPT/taxonomy key.
get_header();
?>
<main id="primary" class="gnn-project-single">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<article class="gnn-project-canvas">
			<header class="gnn-project-intro"><h1><?php the_title(); ?></h1><?php the_excerpt(); ?></header>
			<?php if ( ! post_password_required() ) : ?>
				<div class="gnn-project-cover"><?php echo gnn_project_image( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- native attachment HTML or escaped illustration helper. ?></div>
				<dl class="gnn-project-facts">
					<?php
					foreach ( array(
						'location' => __( 'Project location', 'gnn' ),
						'year'     => __( 'Project year', 'gnn' ),
						'client'   => __( 'Client or project context', 'gnn' ),
					) as $gnn_key => $gnn_label ) :
						?>
													<?php $gnn_value = get_post_meta( get_the_ID(), '_gnn_project_' . $gnn_key, true ); ?>
													<?php
													if ( $gnn_value ) :
														?>
														<div><dt><?php echo esc_html( $gnn_label ); ?></dt><dd><?php echo esc_html( $gnn_value ); ?></dd></div><?php endif; ?>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>
			<?php the_content(); ?>
			<?php if ( ! post_password_required() ) : ?>
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shared renderer escapes output.
				echo gnn_render_module(
					'gallery',
					array(
						'source' => 'project',
						'record' => get_the_ID(),
					)
				); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shared escaped renderer.
				?>
				<?php echo gnn_project_navigation( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper escapes every label/URL. ?>
			<?php endif; ?>
		</article>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
