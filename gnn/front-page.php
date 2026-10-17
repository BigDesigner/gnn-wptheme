<?php
/**
 * The static front page: managed hero slider + page content.
 *
 * All sections live in the front page's own content (Gutenberg blocks or
 * Elementor) — the theme hardcodes no content. See the demo WXR for the
 * designed sections as editable blocks.
 *
 * @package GNN
 */

// front-page.php takes precedence over home.php even in latest-posts mode.
if ( 'posts' === get_option( 'show_on_front' ) ) {
	get_template_part( 'home' );
	return;
}

get_header();
?>

<main id="primary" class="site-main front-page-main">

	<?php get_template_part( 'template-parts/hero-slider' ); ?>

	<?php
	// The latest-posts path has already delegated to the blog template.
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
