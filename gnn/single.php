<?php
/**
 * The template for displaying all single posts (Blog Post screen).
 *
 * @package GNN
 */

get_header();

// Elementor Pro Theme Builder: a "single" template replaces this layout.
if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'single' ) ) {
	get_footer();
	return;
}
?>

<main id="primary" class="site-main single-main">

	<?php
	while ( have_posts() ) :
		the_post();
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-article' ); ?>>

			<header class="entry-header">
				<?php gnn_breadcrumb(); ?>
				<?php if ( ! ( function_exists( 'gnn_hide_title' ) && gnn_hide_title() ) ) : ?>
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				<?php endif; ?>
				<div class="entry-author-row">
					<?php echo get_avatar( get_the_author_meta( 'ID' ), 42, '', '', array( 'class' => 'entry-author-row__avatar' ) ); ?>
					<div>
						<div class="entry-author-row__name">
							<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a>
						</div>
						<?php gnn_entry_meta(); ?>
					</div>
				</div>
			</header>

			<?php gnn_post_thumbnail( 'gnn-cover', false ); // No empty placeholder on single posts. ?>

			<div class="entry-content">
				<?php
				the_content();
				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'gnn' ),
						'after'  => '</div>',
					)
				);
				?>
			</div>

			<footer class="entry-footer">
				<?php the_tags( '<div class="entry-tags">', '', '</div>' ); ?>

				<div class="author-box">
					<?php echo get_avatar( get_the_author_meta( 'ID' ), 64, '', '', array( 'class' => 'author-box__avatar' ) ); ?>
					<div class="author-box__body">
						<span class="author-box__kicker"><?php esc_html_e( 'Written by', 'gnn' ); ?></span>
						<a class="author-box__name" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a>
						<?php if ( get_the_author_meta( 'description' ) ) : ?>
							<p class="author-box__bio"><?php the_author_meta( 'description' ); ?></p>
						<?php endif; ?>
					</div>
				</div>

				<nav class="post-navigation" aria-label="<?php esc_attr_e( 'Posts', 'gnn' ); ?>">
					<?php
					previous_post_link( '<div class="post-nav-card post-nav-card--prev"><span class="post-nav-card__label">' . esc_html__( '← Previous', 'gnn' ) . '</span>%link</div>', '%title' );
					next_post_link( '<div class="post-nav-card post-nav-card--next"><span class="post-nav-card__label">' . esc_html__( 'Next →', 'gnn' ) . '</span>%link</div>', '%title' );
					?>
				</nav>
			</footer>

			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>

		</article>

	<?php endwhile; ?>

</main><!-- #primary -->

<?php
get_footer();
