<?php
/**
 * Block patterns: the designed front-page sections, insertable from the
 * editor (Patterns → GNN). Content stays user-editable page content —
 * nothing is hardcoded into templates.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the category + patterns.
 */
function gnn_register_patterns() {
	if ( ! function_exists( 'register_block_pattern' ) ) {
		return;
	}

	register_block_pattern_category( 'gnn', array( 'label' => __( 'GNN Theme', 'gnn' ) ) );

	// Bundled placeholder image — portable across sites (resolves to whatever
	// site's theme folder), so patterns never reference an external URL.
	$gnn_ph = esc_url( get_theme_file_uri( 'assets/img/placeholder-cover.svg' ) );

	// Example converted from a core-style pattern: external image URL + the
	// site-specific attachment id/classes stripped, image pointed at the
	// bundled placeholder. Self-contained (no dependency on core patterns).
	$gnn_cover_gradient = '<!-- wp:cover {"url":"' . $gnn_ph . '","dimRatio":60,"customOverlayColor":"#c2b7a4","isUserOverlayColor":true,"isDark":false,"metadata":{"categories":["gnn"],"name":"Cover — gradient text"},"align":"full","style":{"spacing":{"padding":{"top":"5vw","right":"5vw","bottom":"5vw","left":"5vw"},"margin":{"top":"0"}},"color":{"duotone":["#36345d","#abaaaa"]}}} -->
<div class="wp-block-cover alignfull is-light" style="margin-top:0;padding-top:5vw;padding-right:5vw;padding-bottom:5vw;padding-left:5vw"><img class="wp-block-cover__image-background" alt="" src="' . $gnn_ph . '" data-object-fit="cover"/><span aria-hidden="true" class="wp-block-cover__background has-background-dim-60 has-background-dim" style="background-color:#c2b7a4"></span><div class="wp-block-cover__inner-container"><!-- wp:spacer {"height":"48px"} -->
<div style="height:48px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"flex","orientation":"vertical","flexWrap":"nowrap","justifyContent":"center"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"has-text-color","style":{"typography":{"fontSize":"148px","textTransform":"uppercase","fontStyle":"normal","fontWeight":"300","lineHeight":"0.8","letterSpacing":"-4px"},"color":{"text":"#c8c4d3"}}} -->
<p class="has-text-align-right has-text-color" style="color:#c8c4d3;font-size:148px;font-style:normal;font-weight:300;letter-spacing:-4px;line-height:0.8;text-transform:uppercase">GNN</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"has-text-color","style":{"typography":{"fontSize":"148px","textTransform":"uppercase","fontStyle":"normal","fontWeight":"300","lineHeight":"0.8","letterSpacing":"-4px"},"color":{"text":"#ac94b2"}}} -->
<p class="has-text-align-right has-text-color" style="color:#ac94b2;font-size:148px;font-style:normal;font-weight:300;letter-spacing:-4px;line-height:0.8;text-transform:uppercase">GNN</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"has-text-color","style":{"typography":{"fontSize":"148px","textTransform":"uppercase","fontStyle":"normal","fontWeight":"300","lineHeight":"0.8","letterSpacing":"-4px"},"color":{"text":"#71689c"}}} -->
<p class="has-text-align-right has-text-color" style="color:#71689c;font-size:148px;font-style:normal;font-weight:300;letter-spacing:-4px;line-height:0.8;text-transform:uppercase">GNN</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"has-text-color","style":{"typography":{"fontSize":"148px","textTransform":"uppercase","fontStyle":"normal","fontWeight":"300","lineHeight":"0.8","letterSpacing":"-4px"},"color":{"text":"#444178"}}} -->
<p class="has-text-align-right has-text-color" style="color:#444178;font-size:148px;font-style:normal;font-weight:300;letter-spacing:-4px;line-height:0.8;text-transform:uppercase">GNN</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:spacer {"height":"48px"} -->
<div style="height:48px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div></div>
<!-- /wp:cover -->';

	$patterns = array(
		'gnn/cover-gradient-text' => array(
			'title'   => __( 'Cover — gradient text', 'gnn' ),
			'content' => $gnn_cover_gradient,
		),
		'gnn/trusted-strip'       => array(
			'title'   => __( 'Trusted-by logo strip', 'gnn' ),
			'content' => '<!-- wp:html -->
<section class="trusted-strip"><div class="gnn-container trusted-strip__inner"><span class="trusted-strip__label">' . esc_html__( 'Trusted by security teams at', 'gnn' ) . '</span><span class="trusted-strip__logo">AXIOM</span><span class="trusted-strip__logo">NORDPEAK</span><span class="trusted-strip__logo">HELIOSGRID</span><span class="trusted-strip__logo">VANTAGE</span><span class="trusted-strip__logo">KORVUS</span></div></section>
<!-- /wp:html -->',
		),
		'gnn/stats'               => array(
			'title'   => __( 'Statistics cards', 'gnn' ),
			'content' => '<!-- wp:html -->
<section class="stats-section gnn-container"><div class="stat-card"><div class="stat-card__value">99.99%</div><div class="stat-card__label">' . esc_html__( 'Platform uptime SLA', 'gnn' ) . '</div></div><div class="stat-card"><div class="stat-card__value">4B+</div><div class="stat-card__label">' . esc_html__( 'Events analyzed daily', 'gnn' ) . '</div></div><div class="stat-card"><div class="stat-card__value">300+</div><div class="stat-card__label">' . esc_html__( 'Enterprise clients', 'gnn' ) . '</div></div><div class="stat-card"><div class="stat-card__value">15 min</div><div class="stat-card__label">' . esc_html__( 'Mean incident response', 'gnn' ) . '</div></div></section>
<!-- /wp:html -->',
		),
		'gnn/featured-products'   => array(
			'title'   => __( 'Featured products (WooCommerce)', 'gnn' ),
			'content' => '<!-- wp:html -->
<section class="featured-products gnn-container"><div class="section-heading"><h2 class="section-heading__title">' . esc_html__( 'Featured products', 'gnn' ) . '</h2><a class="section-heading__link" href="/shop/">' . esc_html__( 'View all', 'gnn' ) . ' &#8594;</a></div>[products limit="4" columns="4" orderby="date" order="DESC"]</section>
<!-- /wp:html -->',
		),
		'gnn/why-band'            => array(
			'title'   => __( 'Feature band (media + bullets)', 'gnn' ),
			'content' => '<!-- wp:html -->
<section class="why-section"><div class="gnn-container why-section__inner"><div class="why-section__media" aria-hidden="true"></div><div class="why-section__content"><div class="entry-kicker">' . esc_html__( 'Why us', 'gnn' ) . '</div><h2 class="why-section__title">' . esc_html__( 'Full-stack security, one accountable partner.', 'gnn' ) . '</h2><div class="why-section__features"><div class="why-feature"><span class="why-feature__dot" aria-hidden="true"></span><div><div class="why-feature__title">' . esc_html__( 'Feature title', 'gnn' ) . '</div><div class="why-feature__text">' . esc_html__( 'Feature description goes here.', 'gnn' ) . '</div></div></div></div></div></div></section>
<!-- /wp:html -->',
		),
		'gnn/cta-band'            => array(
			'title'   => __( 'Call-to-action band', 'gnn' ),
			'content' => '<!-- wp:html -->
<section class="cta-band"><div class="gnn-container cta-band__inner"><h2 class="cta-band__title">' . esc_html__( 'Ready to secure your stack?', 'gnn' ) . '</h2><a class="gnn-btn gnn-btn--inverse" href="/contact/">' . esc_html__( 'Get a Quote', 'gnn' ) . '</a></div></section>
<!-- /wp:html -->',
		),
	);

	foreach ( $patterns as $name => $pattern ) {
		register_block_pattern(
			$name,
			array(
				'title'      => $pattern['title'],
				'categories' => array( 'gnn' ),
				'content'    => $pattern['content'],
			)
		);
	}

	// Library patterns: markup kept in inc/pattern-parts/*.php (readable, one
	// file each). Each file echoes block markup and uses $gnn_ph for images,
	// so nothing points at an external URL. Titles are translatable here.
	$library = array(
		'cover-poster-left'           => __( 'Cover: poster left, text right', 'gnn' ),
		'cover-two-tone-image'        => __( 'Two-tone background, centered image', 'gnn' ),
		'heading-paragraph-image'     => __( 'Heading & paragraph with image', 'gnn' ),
		'faqs'                        => __( 'FAQs', 'gnn' ),
		'contact-social'              => __( 'Contact with social links', 'gnn' ),
		'dark-banner-heading'         => __( 'Dark banner, top-left heading', 'gnn' ),
		'bold-heading-button'         => __( 'Bold heading, paragraph & button', 'gnn' ),
		'headline-links-gradient'     => __( 'Headline with links, gradient background', 'gnn' ),
		'cover-heading-button-left'   => __( 'Cover: heading & button (left)', 'gnn' ),
		'cover-heading-button-center' => __( 'Cover: heading & button (centered)', 'gnn' ),
		'media-text-left'             => __( 'Media & text (image left)', 'gnn' ),
		'media-text-right'            => __( 'Media & text (image right)', 'gnn' ),
		'cover-large-header-left'     => __( 'Large header over cover (left text)', 'gnn' ),
		'gallery-two-images'          => __( 'Two images side by side', 'gnn' ),
	);
	foreach ( $library as $slug => $title ) {
		$file = get_theme_file_path( 'inc/pattern-parts/' . $slug . '.php' );
		if ( ! is_readable( $file ) ) {
			continue;
		}
		ob_start();
		include $file; // Uses $gnn_ph for portable image URLs.
		$content = ob_get_clean();
		register_block_pattern(
			'gnn/' . $slug,
			array(
				'title'      => $title,
				'categories' => array( 'gnn' ),
				'content'    => $content,
			)
		);
	}
}
add_action( 'init', 'gnn_register_patterns' );

/**
 * Block styles: the theme's pill button as a core/button variation.
 */
function gnn_register_block_styles() {
	if ( ! function_exists( 'register_block_style' ) ) {
		return;
	}
	register_block_style(
		'core/button',
		array(
			'name'         => 'gnn-pill',
			'label'        => __( 'GNN Pill', 'gnn' ),
			'inline_style' => '.is-style-gnn-pill .wp-block-button__link{border-radius:999px;background:var(--accent);color:var(--accent-ink);font-weight:700;}',
		)
	);
}
add_action( 'init', 'gnn_register_block_styles' );
