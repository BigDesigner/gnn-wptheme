<?php
/**
 * Post card used by blog index, archives and the index fallback.
 *
 * @package GNN
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
	<a class="post-card__media" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
		<?php gnn_post_thumbnail( 'gnn-wide' ); ?>
	</a>
	<div class="post-card__body">
		<?php $gnn_cat = get_the_category(); ?>
		<?php if ( ! empty( $gnn_cat ) ) : ?>
			<div class="entry-kicker"><?php echo esc_html( $gnn_cat[0]->name ); ?></div>
		<?php endif; ?>
		<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<?php gnn_entry_meta(); ?>
	</div>
</article>
