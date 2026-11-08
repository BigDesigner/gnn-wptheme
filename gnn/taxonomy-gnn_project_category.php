<?php
/**
 * Live project-category archive, independent of an imported page.
 *
 * @package GNN
 */

// phpcs:disable WordPress.Files.FileName -- WordPress template hierarchy requires the registered CPT/taxonomy key.
get_header();
?>
<main id="primary" class="gnn-project-canvas">
	<h1><?php single_term_title(); ?></h1>
	<?php echo wp_kses_post( term_description() ); ?>
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shared renderer escapes output.
				echo gnn_render_module(
					'portfolio',
					array(
						'category' => get_queried_object_id(),
						'filters'  => false,
					)
				); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shared escaped renderer.
				?>
</main>
<?php get_footer(); ?>
