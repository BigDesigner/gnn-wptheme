<?php
/**
 * GNN theme functions and definitions.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GNN_VERSION', '1.3.13' );

/**
 * Theme setup.
 */
function gnn_setup() {
	load_theme_textdomain( 'gnn', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	// Logos are managed in GNN Theme Panel → Brand (light/dark + retina),
	// so the core single "custom-logo" is intentionally NOT registered — that
	// would add a second, conflicting Logo field in Customizer → Site Identity.

	// Editor styles (block editor inherits theme typography + tokens).
	add_theme_support( 'editor-styles' );
	add_theme_support( 'dark-editor-style' );
	add_editor_style( 'assets/css/editor.css' );

	register_nav_menus(
		array(
			'primary'  => __( 'Primary Menu', 'gnn' ),
			'mobile'   => __( 'Mobile Menu', 'gnn' ),
			'footer-1' => __( 'Footer Column 1', 'gnn' ),
			'footer-2' => __( 'Footer Column 2', 'gnn' ),
			'footer-3' => __( 'Footer Column 3', 'gnn' ),
		)
	);

	// Image sizes matching the design's aspect ratios.
	add_image_size( 'gnn-card', 640, 480, true );      // 4:3 product / content cards.
	add_image_size( 'gnn-wide', 1200, 675, true );     // 16:9 post cards.
	add_image_size( 'gnn-cover', 1520, 760, true );    // 16:8 single post cover.
	add_image_size( 'gnn-thumb', 112, 84, true );      // Sidebar recent-post rows.
}
add_action( 'after_setup_theme', 'gnn_setup' );

/**
 * Set the content width.
 */
function gnn_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'gnn_content_width', 1280 );
}
add_action( 'after_setup_theme', 'gnn_content_width', 0 );

/**
 * Register widget areas.
 */
function gnn_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Sidebar', 'gnn' ),
			'id'            => 'sidebar-1',
			'description'   => __( 'Appears on archives and pages with a sidebar.', 'gnn' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	for ( $i = 1; $i <= 3; $i++ ) {
		register_sidebar(
			array(
				/* translators: %d: footer column number. */
				'name'          => sprintf( __( 'Footer Column %d', 'gnn' ), $i ),
				'id'            => 'footer-' . $i,
				'description'   => __( 'Appears in the footer widget area.', 'gnn' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}
}
add_action( 'widgets_init', 'gnn_widgets_init' );

/**
 * Enqueue styles and scripts.
 */
function gnn_scripts() {
	wp_enqueue_style( 'gnn-main', get_template_directory_uri() . '/assets/css/main.css', array(), GNN_VERSION );
	wp_enqueue_style( 'gnn-style', get_stylesheet_uri(), array( 'gnn-main' ), GNN_VERSION );

	// Accent color override. wp_add_inline_style() guarantees this prints
	// immediately after gnn-main in the DOM, so it always wins the cascade
	// over main.css's own :root default — a plain <style> tag in wp_head
	// (even at priority 0) can print BEFORE main.css and get overridden by it.
	$gnn_accent  = sanitize_hex_color( get_theme_mod( 'gnn_accent_color', '#34d399' ) );
	$gnn_accent  = $gnn_accent ? $gnn_accent : '#34d399';
	$gnn_top_pad = max( 0, min( 200, (int) gnn_option( 'content_top_padding' ) ) );
	$gnn_bot_pad = max( 0, min( 300, (int) gnn_option( 'content_bottom_padding' ) ) );

	$gnn_page_thumb_h   = max( 80, min( 600, (int) gnn_option( 'page_featured_image_height' ) ) );
	$gnn_page_thumb_fit = in_array( gnn_option( 'page_featured_image_fit' ), array( 'cover', 'contain', 'fill' ), true ) ? gnn_option( 'page_featured_image_fit' ) : 'cover';
	$gnn_page_thumb_pos = in_array( gnn_option( 'page_featured_image_position' ), array( 'top', 'center', 'bottom' ), true ) ? gnn_option( 'page_featured_image_position' ) : 'center';
	$gnn_post_thumb_h   = max( 80, min( 800, (int) gnn_option( 'post_featured_image_height' ) ) );
	$gnn_post_thumb_fit = in_array( gnn_option( 'post_featured_image_fit' ), array( 'cover', 'contain', 'fill' ), true ) ? gnn_option( 'post_featured_image_fit' ) : 'cover';
	$gnn_post_thumb_pos = in_array( gnn_option( 'post_featured_image_position' ), array( 'top', 'center', 'bottom' ), true ) ? gnn_option( 'post_featured_image_position' ) : 'center';

	wp_add_inline_style(
		'gnn-main',
		':root{--accent:' . esc_html( $gnn_accent ) . ';--gnn-content-top-pad:' . $gnn_top_pad . 'px;--gnn-content-bottom-pad:' . $gnn_bot_pad . 'px;' .
		'--gnn-page-thumb-h:' . $gnn_page_thumb_h . 'px;--gnn-page-thumb-fit:' . esc_html( $gnn_page_thumb_fit ) . ';--gnn-page-thumb-pos:' . esc_html( $gnn_page_thumb_pos ) . ';' .
		'--gnn-post-thumb-h:' . $gnn_post_thumb_h . 'px;--gnn-post-thumb-fit:' . esc_html( $gnn_post_thumb_fit ) . ';--gnn-post-thumb-pos:' . esc_html( $gnn_post_thumb_pos ) . ';}'
	);

	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_style( 'gnn-woocommerce', get_template_directory_uri() . '/assets/css/woocommerce.css', array( 'gnn-main' ), GNN_VERSION );
	}

	wp_enqueue_script( 'gnn-theme', get_template_directory_uri() . '/assets/js/theme.js', array(), GNN_VERSION, true );

	if ( is_front_page() ) {
		wp_enqueue_script( 'gnn-slider', get_template_directory_uri() . '/assets/js/slider.js', array(), GNN_VERSION, true );
		wp_localize_script(
			'gnn-slider',
			'gnnSlider',
			array(
				'autoplay' => (bool) gnn_option( 'slider_autoplay' ),
				'interval' => max( 2, (int) gnn_option( 'slider_interval' ) ) * 1000,
			)
		);
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'gnn_scripts' );

/**
 * Defer the theme's own scripts (they attach listeners; order is irrelevant).
 *
 * @param string $tag    The script tag HTML.
 * @param string $handle The registered script handle.
 * @return string
 */
function gnn_defer_scripts( $tag, $handle ) {
	if ( in_array( $handle, array( 'gnn-theme', 'gnn-slider' ), true ) && false === strpos( $tag, ' defer' ) ) {
		$tag = str_replace( ' src=', ' defer src=', $tag );
	}
	return $tag;
}
add_filter( 'script_loader_tag', 'gnn_defer_scripts', 10, 2 );

/**
 * Print the theme-mode bootstrap in <head> (accent color is handled via
 * wp_add_inline_style() in gnn_scripts() so it reliably wins the cascade).
 *
 * The tiny inline script runs before first paint so the persisted
 * (localStorage) or Customizer-default mode is applied without a flash.
 */
function gnn_head_bootstrap() {
	$default = 'light' === get_theme_mod( 'gnn_default_theme', 'dark' ) ? 'light' : 'dark';
	?>
	<script id="gnn-theme-mode">
	(function () {
		var mode, remember = <?php echo gnn_option( 'remember_mode' ) ? 'true' : 'false'; ?>;
		if (remember) { try { mode = localStorage.getItem('gnn-theme'); } catch (e) {} }
		if (mode !== 'dark' && mode !== 'light') { mode = '<?php echo esc_js( $default ); ?>'; }
		document.documentElement.setAttribute('data-theme', mode);
		window.gnnThemeRemember = remember;
	})();
	</script>
	<?php
}
add_action( 'wp_head', 'gnn_head_bootstrap', 0 );

/**
 * Add a pingback url auto-discovery header for single posts.
 */
function gnn_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'gnn_pingback_header' );

require get_template_directory() . '/inc/options.php';
require get_template_directory() . '/inc/typography.php';
if ( is_admin() ) {
	require get_template_directory() . '/inc/admin-panel.php';
	require get_template_directory() . '/inc/updater.php';
}
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/frontend.php';
require get_template_directory() . '/inc/page-layouts.php';
require get_template_directory() . '/inc/class-gnn-walker-nav-menu.php';
require get_template_directory() . '/inc/class-gnn-walker-comment.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/slider.php';
require get_template_directory() . '/inc/page-meta.php';
require get_template_directory() . '/inc/patterns.php';
require get_template_directory() . '/inc/demo-import.php';

if ( class_exists( 'WooCommerce' ) ) {
	require get_template_directory() . '/inc/woocommerce.php';
}

if ( defined( 'ELEMENTOR_VERSION' ) ) {
	require get_template_directory() . '/inc/elementor.php';
	require get_template_directory() . '/inc/elementor-templates.php';
}
