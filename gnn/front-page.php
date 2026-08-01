<?php
/**
 * The front page: hero slider (Customizer-managed) + page content.
 *
 * All sections live in the front page's own content (Gutenberg blocks or
 * Elementor) — the theme hardcodes no content. See the demo WXR for the
 * designed sections as editable blocks.
 *
 * @package GNN
 */

get_header();
?>

<main id="primary" class="site-main front-page-main">

	<?php get_template_part( 'template-parts/hero-slider' ); ?>

	<?php
	// Only render page content when a static front page is set; when the
	// front shows latest posts the main query holds posts, which belong
	// to home.php, not here.
	if ( 'page' === get_option( 'show_on_front' ) ) :
		while ( have_posts() ) :
			the_post();
			if ( trim( get_the_content() ) !== '' ) :
				?>
				<div class="entry-content front-page-content">
					<?php the_content(); ?>
				</div>
				<?php
			endif;
		endwhile;
	endif;
	?>

</main><!-- #primary -->

<?php
get_footer();
