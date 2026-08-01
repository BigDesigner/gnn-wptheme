<?php
/**
 * Template Name: Blank Canvas (no header/footer)
 * Template Post Type: page
 *
 * A bare canvas: the theme's <head>/<body> bootstrap (so accent color,
 * dark/light mode, plugins and Elementor still work) but no site header,
 * footer or container. Ideal for landing pages and full page-builder layouts.
 *
 * @package GNN
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>

<body <?php body_class( 'gnn-blank-canvas' ); ?>>
<?php wp_body_open(); ?>

	<main id="primary" class="site-main gnn-blank-main">
		<?php
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;
		?>
	</main>

<?php wp_footer(); ?>
</body>
</html>
