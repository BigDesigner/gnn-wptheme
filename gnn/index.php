<?php
/**
 * The main template file (final fallback in the hierarchy).
 *
 * @package GNN
 */

get_header();
?>

<main id="primary" class="site-main gnn-container">

	<header class="page-header">
		<h1 class="page-title">
			<?php is_home() ? bloginfo( 'name' ) : the_archive_title(); ?>
		</h1>
	</header>

	<?php if ( have_posts() ) : ?>

		<div class="posts-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', 'archive' );
			endwhile;
			?>
		</div>

		<?php gnn_pagination(); ?>

	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>

</main><!-- #primary -->

<?php
get_footer();
