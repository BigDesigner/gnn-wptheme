<?php
/**
 * GNN block-pattern markup. Output-buffered by inc/patterns.php,
 * which supplies $gnn_ph (portable placeholder image URL).
 *
 * @package GNN
 */

?>
<!-- wp:cover {"url":"<?php echo esc_url( $gnn_ph ); ?>","dimRatio":60,"minHeight":800,"metadata":{"name":"Large header with left-aligned text"},"align":"full"} -->
<div class="wp-block-cover alignfull" style="min-height:800px"><img class="wp-block-cover__image-background" alt="" src="<?php echo esc_url( $gnn_ph ); ?>" data-object-fit="cover"/><span aria-hidden="true" class="wp-block-cover__background has-background-dim-60 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:heading {"align":"wide","style":{"color":{"text":"#ffe074"},"typography":{"fontSize":"64px"}}} -->
<h2 class="wp-block-heading alignwide has-text-color" style="color:#ffe074;font-size:64px">Headline.</h2>
<!-- /wp:heading -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"55%"} -->
<div class="wp-block-column" style="flex-basis:55%"><!-- wp:spacer {"height":"330px"} -->
<div style="height:330px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"style":{"color":{"text":"#ffe074"},"typography":{"lineHeight":"1.3","fontSize":"14px"}}} -->
<p class="has-text-color" style="color:#ffe074;font-size:14px;line-height:1.3"><em>Add a short, evocative introduction here. Replace this placeholder with your own copy describing the section, product or story.</em></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div></div>
<!-- /wp:cover -->
