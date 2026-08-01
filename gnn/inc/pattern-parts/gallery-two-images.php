<?php
/**
 * GNN block-pattern markup. Output-buffered by inc/patterns.php,
 * which supplies $gnn_ph (portable placeholder image URL).
 *
 * @package GNN
 */

?>
<!-- wp:gallery {"linkTo":"none","metadata":{"name":"Two images side by side"},"align":"wide"} -->
<figure class="wp-block-gallery alignwide has-nested-images columns-default is-cropped"><!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="<?php echo esc_url( $gnn_ph ); ?>" alt=""/></figure>
<!-- /wp:image -->

<!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="<?php echo esc_url( $gnn_ph ); ?>" alt=""/></figure>
<!-- /wp:image --></figure>
<!-- /wp:gallery -->
