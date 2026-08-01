<?php
/**
 * GNN block-pattern markup. Output-buffered by inc/patterns.php,
 * which supplies $gnn_ph (portable placeholder image URL).
 *
 * @package GNN
 */

?>
<!-- wp:cover {"minHeight":66,"minHeightUnit":"vh","customGradient":"linear-gradient(90deg,rgb(35,74,20) 50%,rgb(225,137,116) 50%)","isDark":false,"metadata":{"name":"Centered image with two-tone background color"},"align":"full","style":{"spacing":{"padding":{"top":"5vw","right":"5vw","bottom":"5vw","left":"5vw"},"margin":{"top":"0"}}}} -->
<div class="wp-block-cover alignfull is-light" style="margin-top:0;padding-top:5vw;padding-right:5vw;padding-bottom:5vw;padding-left:5vw;min-height:66vh"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-100 has-background-dim has-background-gradient" style="background:linear-gradient(90deg,rgb(35,74,20) 50%,rgb(225,137,116) 50%)"></span><div class="wp-block-cover__inner-container"><!-- wp:group {"style":{"spacing":{"blockGap":"0px"}},"layout":{"type":"constrained","contentSize":"600px","wideSize":"1200px"}} -->
<div class="wp-block-group"><!-- wp:spacer -->
<div style="height:100px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:image {"sizeSlug":"large","className":"is-style-default","style":{"color":{"duotone":["#000000","#ffffff"]}}} -->
<figure class="wp-block-image size-large is-style-default"><img src="<?php echo esc_url( $gnn_ph ); ?>" alt=""/></figure>
<!-- /wp:image -->

<!-- wp:spacer {"height":"48px"} -->
<div style="height:48px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"align":"wide","style":{"typography":{"fontSize":"50px","fontStyle":"normal","fontWeight":"400","textTransform":"uppercase","letterSpacing":"32px","lineHeight":"1"},"spacing":{"padding":{"left":"32px"}}},"textColor":"white"} -->
<h2 class="wp-block-heading has-text-align-center alignwide has-white-color has-text-color" style="padding-left:32px;font-size:50px;font-style:normal;font-weight:400;letter-spacing:32px;line-height:1;text-transform:uppercase">Etcetera</h2>
<!-- /wp:heading -->

<!-- wp:spacer -->
<div style="height:100px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div>
<!-- /wp:group --></div></div>
<!-- /wp:cover -->
