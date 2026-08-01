<?php
/**
 * Designed empty state: no posts / no search results.
 *
 * @package GNN
 */

?>

<section class="no-results not-found">
	<div class="no-results__inner">
		<h2 class="no-results__title">
			<?php
			if ( is_search() ) {
				esc_html_e( 'No results found', 'gnn' );
			} else {
				esc_html_e( 'Nothing here yet', 'gnn' );
			}
			?>
		</h2>
		<p class="no-results__text">
			<?php
			if ( is_search() ) {
				esc_html_e( 'Try a different search term, or browse the catalog and blog from the menu.', 'gnn' );
			} else {
				esc_html_e( 'Check back soon — new content is on the way.', 'gnn' );
			}
			?>
		</p>
		<?php if ( is_search() ) : ?>
			<?php get_search_form(); ?>
		<?php else : ?>
			<a class="gnn-btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to Home', 'gnn' ); ?></a>
		<?php endif; ?>
	</div>
</section>
