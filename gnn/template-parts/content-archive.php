<?php
/**
 * Post card used by blog index, archives and the index fallback.
 *
 * @package GNN
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
	<?php if ( 'hidden' !== gnn_blog_value( 'blog_card_image_mode' ) ) : ?>
	<a class="post-card__media" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
		<?php gnn_post_thumbnail( 'large', true, array( 'sizes' => gnn_blog_image_sizes() ) ); ?>
	</a>
	<?php endif; ?>
	<div class="post-card__body">
		<?php $gnn_cat = get_the_category(); ?>
		<?php if ( gnn_option( 'blog_card_categories' ) && ! empty( $gnn_cat ) ) : ?>
			<div class="entry-kicker"><?php echo esc_html( $gnn_cat[0]->name ); ?></div>
		<?php endif; ?>
		<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<?php
		if ( gnn_option( 'blog_card_excerpt' ) ) :
			?>
			<div class="entry-summary"><?php the_excerpt(); ?></div><?php endif; ?>
		<?php gnn_entry_meta(); ?>
	</div>
</article>
