<?php
/**
 * The template for displaying search results (with designed "no results" state).
 *
 * @package GNN
 */

get_header();
?>

<main id="primary" class="site-main gnn-container search-main">

	<header class="page-header">
		<h1 class="page-title"><?php esc_html_e( 'Search', 'gnn' ); ?></h1>
		<?php get_search_form(); ?>
	</header>

	<?php if ( have_posts() ) : ?>

		<div class="search-results-count">
			<?php
			printf(
				/* translators: 1: number of results, 2: search query. */
				esc_html( _n( '%1$s result for “%2$s”', '%1$s results for “%2$s”', (int) $wp_query->found_posts, 'gnn' ) ),
				esc_html( number_format_i18n( (int) $wp_query->found_posts ) ),
				esc_html( get_search_query() )
			);
			?>
		</div>

		<div class="search-results-list">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', 'search' );
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
