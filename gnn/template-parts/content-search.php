<?php
/**
 * Search result row with post-type badge.
 *
 * @package GNN
 */

$gnn_type_labels = array(
	'post'    => __( 'Post', 'gnn' ),
	'page'    => __( 'Page', 'gnn' ),
	'product' => __( 'Product', 'gnn' ),
);
$gnn_type        = get_post_type();
$gnn_type_label  = $gnn_type_labels[ $gnn_type ] ?? get_post_type_object( $gnn_type )->labels->singular_name ?? $gnn_type;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'search-result-row' ); ?>>
	<a class="search-result-row__link" href="<?php the_permalink(); ?>">
		<span class="search-result-row__badge"><?php echo esc_html( $gnn_type_label ); ?></span>
		<span class="search-result-row__title"><?php the_title(); ?></span>
		<span class="search-result-row__arrow" aria-hidden="true">&rarr;</span>
	</a>
</article>
