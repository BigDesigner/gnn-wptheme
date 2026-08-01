<?php
/**
 * GNN block-pattern markup. Output-buffered by inc/patterns.php,
 * which supplies $gnn_ph (portable placeholder image URL).
 *
 * @package GNN
 */

?>
<!-- wp:cover {"url":"<?php echo esc_url( $gnn_ph ); ?>","dimRatio":30,"minHeight":66,"minHeightUnit":"vh","isDark":false,"metadata":{"name":"Cover Image with Bold Heading and Button, Left"},"align":"full","style":{"spacing":{"padding":{"top":"48px","right":"48px","bottom":"48px","left":"48px"},"margin":{"top":"0"}},"color":{"duotone":["#094850","#f9644e"]}}} -->
<div class="wp-block-cover alignfull is-light" style="margin-top:0;padding-top:48px;padding-right:48px;padding-bottom:48px;padding-left:48px;min-height:66vh"><img class="wp-block-cover__image-background" alt="" src="<?php echo esc_url( $gnn_ph ); ?>" data-object-fit="cover"/><span aria-hidden="true" class="wp-block-cover__background has-background-dim-30 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:group {"style":{"spacing":{"blockGap":"48px"}},"layout":{"type":"constrained","contentSize":"75%","justifyContent":"left","wideSize":"75%"}} -->
<div class="wp-block-group"><!-- wp:heading {"style":{"typography":{"fontSize":"100px","textTransform":"uppercase","fontStyle":"normal","fontWeight":"700","letterSpacing":"0px","lineHeight":"1"}},"textColor":"white"} -->
<h2 class="wp-block-heading has-text-align-left has-white-color has-text-color" style="font-size:100px;font-style:normal;font-weight:700;letter-spacing:0px;line-height:1;text-transform:uppercase">A bold headline goes here</h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:spacer {"height":"72px"} -->
<div style="height:72px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"left","flexWrap":"nowrap"}} -->
<div class="wp-block-buttons"><!-- wp:button {"textColor":"white","className":"is-style-outline","style":{"spacing":{"padding":{"top":"24px","right":"48px","bottom":"24px","left":"48px"}},"typography":{"fontSize":"16px","fontStyle":"normal","fontWeight":"600","textTransform":"uppercase","letterSpacing":"0px"}}} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-white-color has-text-color has-custom-font-size wp-element-button" style="padding-top:24px;padding-right:48px;padding-bottom:24px;padding-left:48px;font-size:16px;font-style:normal;font-weight:600;letter-spacing:0px;text-transform:uppercase">Explore</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div></div>
<!-- /wp:cover -->
