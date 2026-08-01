<?php
/**
 * GNN block-pattern markup. Output-buffered by inc/patterns.php,
 * which supplies $gnn_ph (portable placeholder image URL).
 *
 * @package GNN
 */

?>
<!-- wp:cover {"url":"<?php echo esc_url( $gnn_ph ); ?>","dimRatio":90,"customOverlayColor":"#141414","isUserOverlayColor":true,"focalPoint":{"x":0.5,"y":0},"minHeight":50,"minHeightUnit":"vh","contentPosition":"top left","metadata":{"name":"Fullwidth Dark Banner with Heading Top Left"},"align":"full","style":{"spacing":{"padding":{"top":"5vw","right":"5vw","bottom":"5vw","left":"5vw"},"margin":{"top":"0"}}}} -->
<div class="wp-block-cover alignfull has-custom-content-position is-position-top-left" style="margin-top:0;padding-top:5vw;padding-right:5vw;padding-bottom:5vw;padding-left:5vw;min-height:50vh"><img class="wp-block-cover__image-background" alt="" src="<?php echo esc_url( $gnn_ph ); ?>" style="object-position:50% 0%" data-object-fit="cover" data-object-position="50% 0%"/><span aria-hidden="true" class="wp-block-cover__background has-background-dim-90 has-background-dim" style="background-color:#141414"></span><div class="wp-block-cover__inner-container"><!-- wp:group {"style":{"spacing":{"blockGap":"0px"}},"layout":{"type":"flex","orientation":"vertical","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"has-text-color","style":{"typography":{"fontSize":"160px","fontStyle":"italic","fontWeight":"900","lineHeight":"0.8","letterSpacing":"-4px","textTransform":"none"},"color":{"text":"#d8a557"}}} -->
<p class="has-text-align-left has-text-color" style="color:#d8a557;font-size:160px;font-style:italic;font-weight:900;letter-spacing:-4px;line-height:0.8;text-transform:none">hello!</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div></div>
<!-- /wp:cover -->
