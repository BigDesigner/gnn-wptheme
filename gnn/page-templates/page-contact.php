<?php
/**
 * Template Name: Contact
 * Template Post Type: page
 *
 * Contact page: full-width, no breadcrumb/featured image; the two-column
 * form + info-card layout comes from the page content (see demo content),
 * so any form plugin (CF7, WPForms, …) can be dropped in.
 *
 * @package GNN
 */

get_header();
?>

<main id="primary" class="site-main gnn-container page-contact">

	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="entry-header">
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				<?php if ( has_excerpt() ) : ?>
					<p class="page-intro"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</header>
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
		</article>
	<?php endwhile; ?>

</main><!-- #primary -->

<?php
get_footer();
