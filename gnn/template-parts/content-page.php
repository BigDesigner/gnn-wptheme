<?php
/**
 * Page content: breadcrumb, title, featured image, content.
 *
 * @package GNN
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
	$gnn_hide_title = function_exists( 'gnn_hide_title' ) && gnn_hide_title();
	$gnn_hide_bc    = function_exists( 'gnn_hide_breadcrumb' ) && gnn_hide_breadcrumb();
	// Only render the header when it has something to show.
	if ( ! $gnn_hide_title || ! $gnn_hide_bc ) :
		?>
		<header class="entry-header">
			<?php gnn_breadcrumb(); ?>
			<?php if ( ! $gnn_hide_title ) : ?>
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
			<?php endif; ?>
		</header>
	<?php endif; ?>

	<?php gnn_post_thumbnail( 'gnn-wide', false ); // No empty placeholder on pages. ?>

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

</article>
