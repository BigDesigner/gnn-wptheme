<?php
/**
 * The blog posts index (Blog screen).
 *
 * @package GNN
 */

get_header();
if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'archive' ) ) {
	get_footer();
	return; }
?>

<main id="primary" class="site-main gnn-container blog-index">

	<header class="page-header">
		<h1 class="page-title">
			<?php
			$blog_page = get_option( 'page_for_posts' );
			echo $blog_page ? esc_html( get_the_title( $blog_page ) ) : esc_html__( 'Blog', 'gnn' );
			?>
		</h1>
		<?php if ( $blog_page && has_excerpt( $blog_page ) ) : ?>
			<p class="page-intro"><?php echo esc_html( get_the_excerpt( $blog_page ) ); ?></p>
		<?php endif; ?>
	</header>

	<?php if ( have_posts() ) : ?>

		<?php if ( ! is_paged() && gnn_option( 'blog_featured' ) ) : // Feature the newest post on page 1. ?>
			<?php the_post(); ?>
			<article <?php post_class( 'featured-post-card' ); ?>>
				<?php if ( 'hidden' !== gnn_blog_value( 'blog_card_image_mode' ) ) : ?>
				<a class="featured-post-card__media" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
					<?php gnn_post_thumbnail( 'large', true, array( 'sizes' => '(max-width: 600px) 100vw, 50vw' ) ); ?>
				</a>
				<?php endif; ?>
				<div class="featured-post-card__body">
					<?php
					if ( gnn_option( 'blog_card_categories' ) ) :
						?>
						<div class="entry-kicker">
						<?php
						/* translators: %s: post category. */
						printf( esc_html__( 'Featured — %s', 'gnn' ), esc_html( get_the_category()[0]->name ?? __( 'Post', 'gnn' ) ) );
						?>
					</div><?php endif; ?>
					<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<div class="entry-summary"><?php the_excerpt(); ?></div>
					<?php gnn_entry_meta(); ?>
				</div>
			</article>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<div class="posts-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content', 'archive' );
				endwhile;
				?>
			</div>
		<?php endif; ?>

		<?php gnn_pagination(); ?>

	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>

</main><!-- #primary -->

<?php
get_footer();
