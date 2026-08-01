<?php
/**
 * GNN block-pattern markup. Output-buffered by inc/patterns.php,
 * which supplies $gnn_ph (portable placeholder image URL).
 *
 * @package GNN
 */

?>
<!-- wp:cover {"customOverlayColor":"#36220c","isUserOverlayColor":true,"contentPosition":"top center","metadata":{"name":"Cover Poster on Left, Paragraph on Right"},"align":"full","style":{"spacing":{"padding":{"top":"5vw","right":"5vw","bottom":"5vw","left":"5vw"},"margin":{"top":"0"}}}} -->
<div class="wp-block-cover alignfull has-custom-content-position is-position-top-center" style="margin-top:0;padding-top:5vw;padding-right:5vw;padding-bottom:5vw;padding-left:5vw"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-100 has-background-dim" style="background-color:#36220c"></span><div class="wp-block-cover__inner-container"><!-- wp:group {"style":{"spacing":{"blockGap":"3vw"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"stretch","justifyContent":"space-between"}} -->
<div class="wp-block-group"><!-- wp:cover {"url":"<?php echo esc_url( $gnn_ph ); ?>","dimRatio":50,"minHeight":75,"minHeightUnit":"vh","contentPosition":"top right","isDark":false,"style":{"layout":{"selfStretch":"fill"},"spacing":{"padding":{"top":"4vw","right":"4vw","bottom":"4vw","left":"4vw"}},"color":{"duotone":["rgb(137, 75, 0)","rgb(210, 210, 210)"]}}} -->
<div class="wp-block-cover is-light has-custom-content-position is-position-top-right" style="padding-top:4vw;padding-right:4vw;padding-bottom:4vw;padding-left:4vw;min-height:75vh"><img class="wp-block-cover__image-background" alt="" src="<?php echo esc_url( $gnn_ph ); ?>" data-object-fit="cover"/><span aria-hidden="true" class="wp-block-cover__background has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"flex","orientation":"vertical","flexWrap":"nowrap","justifyContent":"right"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"style":{"typography":{"fontSize":"140px","textTransform":"uppercase","fontStyle":"normal","fontWeight":"800","lineHeight":"0.8","letterSpacing":"-6px"},"color":{"text":"#e5683a"}}} -->
<p class="has-text-color" style="color:#e5683a;font-size:140px;font-style:normal;font-weight:800;letter-spacing:-6px;line-height:0.8;text-transform:uppercase">Sample</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"130px","textTransform":"uppercase","fontStyle":"normal","fontWeight":"800","lineHeight":"0.8","letterSpacing":"-6px"},"color":{"text":"#e5683a"}}} -->
<p class="has-text-color" style="color:#e5683a;font-size:130px;font-style:normal;font-weight:800;letter-spacing:-6px;line-height:0.8;text-transform:uppercase">title</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div></div>
<!-- /wp:cover -->

<!-- wp:group {"style":{"layout":{"selfStretch":"fixed","flexSize":"420px"}},"layout":{"type":"flex","orientation":"vertical","flexWrap":"nowrap","verticalAlignment":"bottom"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"style":{"typography":{"fontSize":"18px","letterSpacing":"0px","lineHeight":"1.7","fontStyle":"normal","fontWeight":"400"},"color":{"text":"#e59937"}}} -->
<p class="has-text-color" style="color:#e59937;font-size:18px;font-style:normal;font-weight:400;letter-spacing:0px;line-height:1.7">Add your introductory copy here. Describe the feature, product or story you want to highlight in a couple of short sentences.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"18px","letterSpacing":"0px","lineHeight":"1.7","fontStyle":"normal","fontWeight":"400"},"color":{"text":"#e59937"}}} -->
<p class="has-text-color" style="color:#e59937;font-size:18px;font-style:normal;font-weight:400;letter-spacing:0px;line-height:1.7">A second paragraph gives you room for more detail — swap in your own text and image from the block toolbar.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"left"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline","style":{"color":{"text":"#e59937"},"border":{"radius":"0px"},"spacing":{"padding":{"top":"16px","right":"32px","bottom":"16px","left":"32px"}},"typography":{"fontSize":"16px","textTransform":"uppercase","fontStyle":"normal","fontWeight":"700","letterSpacing":"0px"}}} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-text-color has-custom-font-size wp-element-button" style="border-radius:0px;color:#e59937;padding-top:16px;padding-right:32px;padding-bottom:16px;padding-left:32px;font-size:16px;font-style:normal;font-weight:700;letter-spacing:0px;text-transform:uppercase">Learn more</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

<!-- wp:spacer {"height":"0px","style":{"layout":{"flexSize":"7.5vw","selfStretch":"fixed"}}} -->
<div style="height:0px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div></div>
<!-- /wp:cover -->
