<?php
/**
 * The template for displaying archive pages (Category / Tag / Author / Date).
 *
 * @package GNN
 */

get_header();

// Elementor Pro Theme Builder: an "archive" template replaces this layout.
if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'archive' ) ) {
	get_footer();
	return;
}
?>

<main id="primary" class="site-main gnn-container archive-main">

	<?php gnn_breadcrumb(); ?>

	<?php if ( is_author() ) : ?>
		<header class="page-header author-header">
			<?php echo get_avatar( get_the_author_meta( 'ID' ), 88, '', '', array( 'class' => 'author-header__avatar' ) ); ?>
			<div>
				<div class="entry-kicker"><?php esc_html_e( 'Author', 'gnn' ); ?></div>
				<h1 class="page-title"><?php echo esc_html( get_the_author() ); ?></h1>
				<div class="author-header__meta">
					<?php
					if ( get_the_author_meta( 'description' ) ) {
						echo esc_html( get_the_author_meta( 'description' ) ) . ' — ';
					}
					/* translators: %s: number of articles. */
					printf( esc_html( _n( '%s article', '%s articles', (int) $wp_query->found_posts, 'gnn' ) ), esc_html( number_format_i18n( (int) $wp_query->found_posts ) ) );
					?>
				</div>
			</div>
		</header>
	<?php else : ?>
		<header class="page-header term-header">
			<div class="entry-kicker">
				<?php
				if ( is_category() ) {
					esc_html_e( 'Category', 'gnn' );
				} elseif ( is_tag() ) {
					esc_html_e( 'Tag', 'gnn' );
				} else {
					esc_html_e( 'Archive', 'gnn' );
				}
				?>
			</div>
			<h1 class="page-title"><?php echo esc_html( wp_strip_all_tags( get_the_archive_title() ) ); ?></h1>
			<p class="page-intro">
				<?php
				if ( get_the_archive_description() ) {
					echo esc_html( wp_strip_all_tags( get_the_archive_description() ) );
				} else {
					/* translators: %s: number of articles. */
					printf( esc_html( _n( '%s article in this archive.', '%s articles in this archive.', (int) $wp_query->found_posts, 'gnn' ) ), esc_html( number_format_i18n( (int) $wp_query->found_posts ) ) );
				}
				?>
			</p>
		</header>
	<?php endif; ?>

	<div class="content-sidebar-layout archive-layout<?php echo gnn_show_sidebar() ? ' has-sidebar' : ''; ?>">

		<div class="content-area">
			<?php if ( have_posts() ) : ?>
				<div class="posts-grid posts-grid--archive">
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
		</div><!-- .content-area -->

		<?php get_sidebar(); ?>

	</div><!-- .content-sidebar-layout -->

</main><!-- #primary -->

<?php
get_footer();
