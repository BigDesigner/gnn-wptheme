<?php
/**
 * GNN Panel — option storage and every front-end consumer.
 *
 * One autoloaded array option (`gnn_options`), zero framework.
 * Shared brand values (accent, default theme mode, footer legal) live in
 * theme_mods so the Customizer live-preview stays the single source; the
 * panel writes to the same mods.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Option defaults. Performance switches default ON — light is the goal.
 *
 * @return array
 */
function gnn_option_defaults() {
	return array(
		// Brand / logo (existing — do not remove).
		'logo_light'                   => 0,
		'logo_light_2x'                => 0,
		'logo_dark'                    => 0,
		'logo_dark_2x'                 => 0,
		'logo_max_height'              => 40,
		'logo_max_height_mobile'       => 34,
		'footer_copyright'             => '',
		// Theme mode.
		'show_toggle'                  => 1,
		'remember_mode'                => 1,
		// Header.
		'sticky_header'                => 1,
		'show_search'                  => 1,
		'show_cart'                    => 1,
		'header_layout'                => 'standard', // standard | centered.
		'header_menu_align'            => 'right',    // left | center | right.
		'mobile_dock'                  => 0,
		'topbar_enable'                => 0,
		'topbar_text'                  => '',
		'topbar_email'                 => '',
		'topbar_phone'                 => '',
		'topbar_bg'                    => '',
		'topbar_text_color'            => '',
		// Footer.
		'footer_brand_type'            => 'text',     // text | image | both | none.
		'footer_logo_light'            => 0,
		'footer_logo_light_2x'         => 0,
		'footer_logo_dark'             => 0,
		'footer_logo_dark_2x'          => 0,
		'footer_logo_height'           => 40,
		'footer_tagline'               => '',
		'footer_menu_align'            => 'left',
		// Blog / shop.
		'sidebar_position'             => 'right',
		'content_top_padding'          => 50,
		'content_bottom_padding'       => 64,
		'page_featured_image_height'   => 250,
		'page_featured_image_fit'      => 'cover',  // cover | contain | fill.
		'page_featured_image_position' => 'center', // top | center | bottom.
		'post_featured_image_height'   => 500,
		'post_featured_image_fit'      => 'cover',
		'post_featured_image_position' => 'center',
		'excerpt_length'               => 24,
		'shop_columns'                 => 4,
		'shop_per_page'                => 8,
		// 404.
		'error404_title'               => '',
		'error404_text'                => '',
		'error404_search'              => 1,
		'error404_button'              => '',
		'error404_image'               => 0,
		// UX toggles.
		'smooth_scroll'                => 1,
		'scroll_top'                   => 1,
		'scroll_anim'                  => 0,
		'preloader'                    => 0,
		'loading_screen'               => 0,
		// Maintenance.
		'maintenance_mode'             => 0,
		'maintenance_message'          => '',
		// Slider.
		'slider_autoplay'              => 1,
		'slider_interval'              => 5,
		'slider_full_height'           => 0,
		// Custom code.
		'custom_css'                   => '',
		'custom_js_head'               => '',
		'custom_js_footer'             => '',
		'ga4_id'                       => '',
		'gtm_id'                       => '',
		'head_html'                    => '',
		'body_html'                    => '',
		// Icons.
		'google_material_icons'        => 1,
		'material_icons_style'         => 'outlined', // outlined | rounded | sharp | filled.
		// Performance.
		'disable_emoji'                => 1,
		'disable_oembed'               => 1,
		'disable_migrate'              => 1,
		'heartbeat_slow'               => 1,
		'woo_scope'                    => 1,
		'font_preload'                 => 1,
	);
}

/**
 * Read a panel option with its default.
 *
 * @param string $key Option key.
 * @return mixed
 */
function gnn_option( $key ) {
	static $options = null;
	if ( null === $options ) {
		$options = wp_parse_args( (array) get_option( 'gnn_options', array() ), gnn_option_defaults() );
	}
	return $options[ $key ] ?? null;
}

// ----- Branding: light/dark logo pair (falls back to core custom logo / title) ----------------------------------------------------------

/**
 * Render one retina-ready logo <img>.
 *
 * The 1x attachment sets the displayed dimensions; the optional 2x attachment
 * is added as a `2x` srcset descriptor so high-DPI screens fetch the sharper
 * file. CSS caps the height (see --gnn-logo-h), width stays auto.
 *
 * @param int    $id_1x     Attachment ID for the standard (1x) logo.
 * @param int    $id_2x     Attachment ID for the retina (2x) logo, or 0.
 * @param string $css_class Extra class (mode selector).
 * @return string HTML, or '' when no 1x id.
 */
function gnn_logo_img( $id_1x, $id_2x, $css_class ) {
	$id_1x = (int) $id_1x;
	if ( ! $id_1x ) {
		return '';
	}
	$src = wp_get_attachment_image_url( $id_1x, 'full' );
	if ( ! $src ) {
		return '';
	}
	$meta   = wp_get_attachment_metadata( $id_1x );
	$width  = ! empty( $meta['width'] ) ? (int) $meta['width'] : null;
	$height = ! empty( $meta['height'] ) ? (int) $meta['height'] : null;
	$alt    = get_bloginfo( 'name' );

	$srcset = '';
	$src_2x = $id_2x ? wp_get_attachment_image_url( (int) $id_2x, 'full' ) : '';
	if ( $src_2x ) {
		$srcset = ' srcset="' . esc_url( $src ) . ' 1x, ' . esc_url( $src_2x ) . ' 2x"';
	}

	return sprintf(
		'<img class="gnn-logo %1$s" src="%2$s"%3$s%4$s%5$s alt="%6$s" decoding="async" loading="eager">',
		esc_attr( $css_class ),
		esc_url( $src ),
		$srcset, // Already escaped above.
		$width ? ' width="' . esc_attr( $width ) . '"' : '',
		$height ? ' height="' . esc_attr( $height ) . '"' : '',
		esc_attr( $alt )
	);
}

/**
 * Output the site logo / title, honoring the panel's light+dark logo pair
 * with retina (2x) variants.
 */
function gnn_the_logo() {
	$light    = (int) gnn_option( 'logo_light' );
	$light_2x = (int) gnn_option( 'logo_light_2x' );
	$dark     = (int) gnn_option( 'logo_dark' );
	$dark_2x  = (int) gnn_option( 'logo_dark_2x' );

	if ( $light || $dark ) {
		// With only one mode uploaded, it serves both.
		$on_light_1x = $light ? $light : $dark;
		$on_light_2x = $light ? $light_2x : $dark_2x;
		$on_dark_1x  = $dark ? $dark : $light;
		$on_dark_2x  = $dark ? $dark_2x : $light_2x;

		$height = max( 12, (int) gnn_option( 'logo_max_height' ) );
		echo '<a class="site-logo" href="' . esc_url( home_url( '/' ) ) . '" rel="home" aria-label="' . esc_attr( get_bloginfo( 'name' ) ) . '" style="--gnn-logo-h:' . esc_attr( $height ) . 'px">';
		echo gnn_logo_img( $on_dark_1x, $on_dark_2x, 'gnn-logo--on-dark' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper.
		if ( $on_light_1x !== $on_dark_1x || $on_light_2x !== $on_dark_2x ) {
			echo gnn_logo_img( $on_light_1x, $on_light_2x, 'gnn-logo--on-light' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper.
		}
		echo '</a>';
		return;
	}

	// No panel logo set: fall back to the site title (core custom-logo is not
	// registered — logos live in GNN Theme Panel → Brand).
	?>
	<a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
		<?php bloginfo( 'name' ); ?><span class="site-title__dot">.</span>
	</a>
	<?php
}

/**
 * Output the footer logo pair (light/dark, retina) at the panel-set width.
 * Returns false when no footer logo is configured.
 *
 * @return bool True if a logo was output.
 */
function gnn_the_footer_logo() {
	$light    = (int) gnn_option( 'footer_logo_light' );
	$light_2x = (int) gnn_option( 'footer_logo_light_2x' );
	$dark     = (int) gnn_option( 'footer_logo_dark' );
	$dark_2x  = (int) gnn_option( 'footer_logo_dark_2x' );

	if ( ! $light && ! $dark ) {
		return false;
	}
	$on_light_1x = $light ? $light : $dark;
	$on_light_2x = $light ? $light_2x : $dark_2x;
	$on_dark_1x  = $dark ? $dark : $light;
	$on_dark_2x  = $dark ? $dark_2x : $light_2x;

	$height = max( 12, (int) gnn_option( 'footer_logo_height' ) );

	echo '<a class="footer-logo-link" href="' . esc_url( home_url( '/' ) ) . '" rel="home" style="--gnn-footer-logo-h:' . esc_attr( $height ) . 'px;">';
	echo gnn_logo_img( $on_dark_1x, $on_dark_2x, 'gnn-logo--on-dark gnn-footer-logo' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper.
	if ( $on_light_1x !== $on_dark_1x || $on_light_2x !== $on_dark_2x ) {
		echo gnn_logo_img( $on_light_1x, $on_light_2x, 'gnn-logo--on-light gnn-footer-logo' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper.
	}
	echo '</a>';
	return true;
}

// ----- Layout switches → body classes ----------------------------------------------------------

/**
 * Panel layout switches as body classes.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function gnn_option_body_classes( $classes ) {
	if ( ! gnn_option( 'sticky_header' ) ) {
		$classes[] = 'gnn-no-sticky';
	}
	if ( 'left' === gnn_option( 'sidebar_position' ) ) {
		$classes[] = 'gnn-sidebar-left';
	}
	$classes[] = 'gnn-header-' . sanitize_html_class( (string) gnn_option( 'header_layout' ) );
	$classes[] = 'gnn-menu-' . sanitize_html_class( (string) gnn_option( 'header_menu_align' ) );
	$classes[] = 'gnn-fmenu-' . sanitize_html_class( (string) gnn_option( 'footer_menu_align' ) );
	if ( gnn_option( 'mobile_dock' ) ) {
		$classes[] = 'gnn-has-dock';
	}
	if ( gnn_option( 'topbar_enable' ) ) {
		$classes[] = 'gnn-has-topbar';
	}
	return $classes;
}
add_filter( 'body_class', 'gnn_option_body_classes' );

/**
 * Whether the theme sidebar should render at all (panel can turn it off).
 *
 * @return bool
 */
function gnn_show_sidebar() {
	return 'none' !== gnn_option( 'sidebar_position' ) && is_active_sidebar( 'sidebar-1' );
}

/**
 * Excerpt length from the panel (front-end only).
 *
 * @param int $length Default length.
 * @return int
 */
function gnn_excerpt_length( $length ) {
	return is_admin() ? $length : max( 5, (int) gnn_option( 'excerpt_length' ) );
}
add_filter( 'excerpt_length', 'gnn_excerpt_length', 20 );

// ----- Custom code + analytics output ----------------------------------------------------------

/**
 * Print the panel's custom CSS (tags stripped on save and output).
 */
function gnn_output_custom_css() {
	$css = trim( (string) gnn_option( 'custom_css' ) );
	if ( '' !== $css ) {
		wp_add_inline_style( 'gnn-style', wp_strip_all_tags( $css ) );
	}
}
add_action( 'wp_enqueue_scripts', 'gnn_output_custom_css', 20 );

/**
 * GA4 / GTM / extra head HTML / head JS output.
 */
function gnn_output_head_code() {
	$ga4 = trim( (string) gnn_option( 'ga4_id' ) );
	if ( preg_match( '/^G-[A-Z0-9]{4,}$/i', $ga4 ) ) {
		$ga4 = esc_js( $ga4 );
		echo "<script async src=\"https://www.googletagmanager.com/gtag/js?id={$ga4}\"></script>\n"; // phpcs:ignore
		echo "<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{$ga4}');</script>\n"; // phpcs:ignore
	}

	$gtm = trim( (string) gnn_option( 'gtm_id' ) );
	if ( preg_match( '/^GTM-[A-Z0-9]{4,}$/i', $gtm ) ) {
		$gtm = esc_js( $gtm );
		echo "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{$gtm}');</script>\n"; // phpcs:ignore
	}

	$head_html = (string) gnn_option( 'head_html' );
	if ( '' !== trim( $head_html ) ) {
		echo $head_html . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- gated by unfiltered_html on save.
	}

	$js = trim( (string) gnn_option( 'custom_js_head' ) );
	if ( '' !== $js ) {
		echo '<script id="gnn-custom-js-head">' . $js . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- gated by unfiltered_html on save.
	}
}
add_action( 'wp_head', 'gnn_output_head_code', 20 );

/**
 * GTM noscript + after-<body> HTML output.
 */
function gnn_output_body_open_code() {
	$gtm = trim( (string) gnn_option( 'gtm_id' ) );
	if ( preg_match( '/^GTM-[A-Z0-9]{4,}$/i', $gtm ) ) {
		echo '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . esc_attr( $gtm ) . '" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>' . "\n";
	}
	$body_html = (string) gnn_option( 'body_html' );
	if ( '' !== trim( $body_html ) ) {
		echo $body_html . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- gated by unfiltered_html on save.
	}
}
add_action( 'wp_body_open', 'gnn_output_body_open_code', 20 );

/**
 * Footer custom JS output.
 */
function gnn_output_footer_code() {
	$js = trim( (string) gnn_option( 'custom_js_footer' ) );
	if ( '' !== $js ) {
		echo '<script id="gnn-custom-js-footer">' . $js . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- gated by unfiltered_html on save.
	}
}
add_action( 'wp_footer', 'gnn_output_footer_code', 99 );

// ----- Performance switches (all default ON) ----------------------------------------------------------

/**
 * Performance switches: emoji, oEmbed, Heartbeat.
 */
function gnn_perf_boot() {
	if ( gnn_option( 'disable_emoji' ) ) {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		add_filter( 'emoji_svg_url', '__return_false' );
	}

	if ( gnn_option( 'disable_oembed' ) ) {
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	}

	if ( gnn_option( 'heartbeat_slow' ) ) {
		add_filter(
			'heartbeat_settings',
			function ( $settings ) {
				$settings['interval'] = 60;
				return $settings;
			}
		);
	}
}
add_action( 'init', 'gnn_perf_boot' );

/**
 * Front-end script diet: drop jQuery Migrate when enabled.
 */
function gnn_perf_scripts() {
	if ( is_admin() ) {
		return;
	}

	// jQuery Migrate off (front-end only).
	if ( gnn_option( 'disable_migrate' ) ) {
		global $wp_scripts;
		if ( isset( $wp_scripts->registered['jquery'] ) ) {
			$deps = &$wp_scripts->registered['jquery']->deps;
			$deps = array_diff( $deps, array( 'jquery-migrate' ) );
		}
	}
}
add_action( 'wp_default_scripts', 'gnn_perf_scripts' );

/**
 * Keep WooCommerce assets off pages that don't use them.
 * A page "uses" Woo when it's a Woo page or its content contains a Woo
 * shortcode / add-to-cart markup (e.g. the front page's [products] grid).
 */
function gnn_scope_woo_assets() {
	if ( ! class_exists( 'WooCommerce' ) || ! gnn_option( 'woo_scope' ) || is_admin() ) {
		return;
	}
	if ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) {
		return;
	}
	$post = get_post();
	$uses = $post && ( has_shortcode( (string) $post->post_content, 'products' )
		|| false !== strpos( (string) $post->post_content, 'add-to-cart' )
		|| has_block( 'woocommerce/all-products', $post )
		|| has_block( 'woocommerce/cart', $post )
		|| has_block( 'woocommerce/checkout', $post ) );
	if ( $uses || apply_filters( 'gnn_page_uses_woocommerce', false ) ) {
		return;
	}

	foreach ( array( 'woocommerce-general', 'woocommerce-layout', 'woocommerce-smallscreen', 'wc-blocks-style', 'brands-styles', 'gnn-woocommerce' ) as $style ) {
		wp_dequeue_style( $style );
	}
	foreach ( array( 'wc-cart-fragments', 'woocommerce', 'wc-add-to-cart', 'sourcebuster-js', 'wc-order-attribution' ) as $script ) {
		wp_dequeue_script( $script );
	}
}
add_action( 'wp_enqueue_scripts', 'gnn_scope_woo_assets', 99 );

/**
 * Preload the two primary font files (latin subsets).
 */
function gnn_font_preload() {
	if ( ! gnn_option( 'font_preload' ) ) {
		return;
	}
	$base = get_template_directory_uri() . '/assets/fonts/';
	foreach ( array( 'manrope-latin.woff2', 'space-grotesk-latin.woff2' ) as $font ) {
		echo '<link rel="preload" href="' . esc_url( $base . $font ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
	}
}
add_action( 'wp_head', 'gnn_font_preload', 2 );

// ----- Google Material Symbols / Icons ----------------------------------------------------------

/**
 * Map a panel icon style to its Google font family. "Filled" isn't a
 * separate Material Symbols family — it's the Outlined family with the
 * FILL variable axis set to 1 (see the .material-symbols-filled CSS rule).
 *
 * @param string $style One of: outlined, rounded, sharp, filled.
 * @return string Google Fonts family slug (URL-encoded).
 */
function gnn_material_icons_family( $style ) {
	$map = array(
		'outlined' => 'Material+Symbols+Outlined',
		'rounded'  => 'Material+Symbols+Rounded',
		'sharp'    => 'Material+Symbols+Sharp',
		'filled'   => 'Material+Symbols+Outlined',
	);
	return $map[ $style ] ?? $map['outlined'];
}

/**
 * Enqueue the Google Material Symbols stylesheet when enabled.
 */
function gnn_material_icons_assets() {
	if ( ! gnn_option( 'google_material_icons' ) ) {
		return;
	}
	$style  = (string) gnn_option( 'material_icons_style' );
	$style  = in_array( $style, array( 'outlined', 'rounded', 'sharp', 'filled' ), true ) ? $style : 'outlined';
	$family = gnn_material_icons_family( $style );
	$fill   = 'filled' === $style ? '1' : '0';

	wp_enqueue_style(
		'gnn-material-symbols',
		esc_url_raw( "https://fonts.googleapis.com/css2?family={$family}:opsz,wght,FILL,GRAD@20..48,400,{$fill},0&display=block" ),
		array(),
		null // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- external CDN URL; a ?ver= query string must not be appended to it.
	);
}
add_action( 'wp_enqueue_scripts', 'gnn_material_icons_assets' );

/**
 * Preconnect to the Google Fonts hosts when Material Symbols is enabled.
 */
function gnn_material_icons_preconnect() {
	if ( ! gnn_option( 'google_material_icons' ) ) {
		return;
	}
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
add_action( 'wp_head', 'gnn_material_icons_preconnect', 1 );
