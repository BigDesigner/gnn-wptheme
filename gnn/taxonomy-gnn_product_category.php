<?php
/**
 * Live product-category archive, independent of an imported page.
 *
 * @package GNN
 */

// phpcs:disable WordPress.Files.FileName -- WordPress template hierarchy requires the registered taxonomy key.
get_header();
?>
<main id="primary" class="gnn-project-canvas">
	<h1><?php single_term_title(); ?></h1>
	<?php echo wp_kses_post( term_description() ); ?>
	<?php
	// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Shared renderer escapes output.
	echo gnn_render_module(
		'catalog',
		array(
			'category' => get_queried_object_id(),
			'filters'  => false,
		)
	);
	// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
	?>
</main>
<?php get_footer(); ?>
