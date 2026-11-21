<?php
/**
 * GNN theme functions and definitions.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GNN_VERSION', '2.13.1' );

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
			'footer-4' => __( 'Footer Column 4', 'gnn' ),
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

	for ( $i = 1; $i <= 4; $i++ ) {
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
	if ( is_rtl() ) {
		$gnn_rtl = get_template_directory_uri() . '/rtl.css';
		wp_enqueue_style( 'gnn-rtl', $gnn_rtl, array( 'gnn-style' ), GNN_VERSION );
		// Core's locale_stylesheet() (wp_head, priority 10) also prints
		// get_locale_stylesheet_uri(), which on RTL sites resolves to the
		// active stylesheet directory's rtl.css. Drop it only when that is this
		// file, so a child theme's own rtl.css or a {locale}.css still loads.
		if ( get_locale_stylesheet_uri() === $gnn_rtl ) {
			remove_action( 'wp_head', 'locale_stylesheet' );
		}
	}

	// Layout tokens. wp_add_inline_style() prints right after main.css, so
	// these always win over its own :root defaults. Accent tokens are
	// emitted once, by gnn_brand_design_tokens() (inc/design-system.php).
	$gnn_top_pad = max( 0, min( 200, (int) gnn_layout_value( 'content_top_padding' ) ) );
	$gnn_bot_pad = max( 0, min( 300, (int) gnn_layout_value( 'content_bottom_padding' ) ) );

	$gnn_page_thumb_h   = max( 80, min( 600, (int) gnn_option( 'page_featured_image_height' ) ) );
	$gnn_page_thumb_fit = in_array( gnn_option( 'page_featured_image_fit' ), array( 'cover', 'contain', 'fill' ), true ) ? gnn_option( 'page_featured_image_fit' ) : 'cover';
	$gnn_page_thumb_pos = in_array( gnn_option( 'page_featured_image_position' ), array( 'top', 'center', 'bottom' ), true ) ? gnn_option( 'page_featured_image_position' ) : 'center';
	$gnn_post_thumb_h   = max( 80, min( 800, (int) gnn_option( 'post_featured_image_height' ) ) );
	$gnn_post_thumb_fit = in_array( gnn_option( 'post_featured_image_fit' ), array( 'cover', 'contain', 'fill' ), true ) ? gnn_option( 'post_featured_image_fit' ) : 'cover';
	$gnn_post_thumb_pos = in_array( gnn_option( 'post_featured_image_position' ), array( 'top', 'center', 'bottom' ), true ) ? gnn_option( 'post_featured_image_position' ) : 'center';

	$gnn_sidebar_w = max( 200, min( 500, (int) gnn_option( 'sidebar_width' ) ) );
	$gnn_boxed_w   = max( 600, min( 1600, (int) gnn_option( 'boxed_width' ) ) );
	$gnn_full_w    = max( 900, min( 1920, (int) gnn_option( 'full_width' ) ) );

	wp_add_inline_style(
		'gnn-main',
		':root{--gnn-content-top-pad:' . $gnn_top_pad . 'px;--gnn-content-bottom-pad:' . $gnn_bot_pad . 'px;' .
		'--gnn-page-thumb-h:' . $gnn_page_thumb_h . 'px;--gnn-page-thumb-fit:' . esc_html( $gnn_page_thumb_fit ) . ';--gnn-page-thumb-pos:' . esc_html( $gnn_page_thumb_pos ) . ';' .
		'--gnn-post-thumb-h:' . $gnn_post_thumb_h . 'px;--gnn-post-thumb-fit:' . esc_html( $gnn_post_thumb_fit ) . ';--gnn-post-thumb-pos:' . esc_html( $gnn_post_thumb_pos ) . ';' .
		'--gnn-sidebar-w:' . $gnn_sidebar_w . 'px;--gnn-boxed-width:' . $gnn_boxed_w . 'px;--gnn-full-width:' . $gnn_full_w . 'px;}'
	);

	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_style( 'gnn-woocommerce', get_template_directory_uri() . '/assets/css/woocommerce.css', array( 'gnn-main' ), GNN_VERSION );
	}

	wp_enqueue_script( 'gnn-theme', get_template_directory_uri() . '/assets/js/theme.js', array(), GNN_VERSION, true );

	// The hero carries its own data-autoplay / data-interval attributes, so no
	// global config is localized here; content modules enqueue the same
	// handle themselves when they render a slider.
	if ( gnn_front_hero_renders() ) {
		wp_enqueue_script( 'gnn-slider', get_template_directory_uri() . '/assets/js/slider.js', array(), GNN_VERSION, true );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'gnn_scripts' );

/**
 * Whether front-page.php will render the hero slider on this request
 * (mirrors its template branches and template-parts/hero-slider.php).
 *
 * @return bool
 */
function gnn_front_hero_renders() {
	if ( ! is_front_page() || 'page' !== get_option( 'show_on_front' ) ) {
		return false;
	}
	if ( is_page_template( 'page-templates/tpl-architecture.php' ) || is_page_template( 'page-templates/tpl-starter.php' ) ) {
		return false;
	}
	if ( function_exists( 'gnn_revslider_active' ) && gnn_revslider_active() ) {
		return true;
	}
	if ( 'none' === gnn_layout_value( 'hero_style' ) || ! function_exists( 'gnn_get_slides' ) ) {
		return false;
	}
	return (bool) gnn_get_slides();
}

/**
 * Defer the theme's own scripts (they attach listeners; order is irrelevant).
 *
 * @param string $tag    The script tag HTML.
 * @param string $handle The registered script handle.
 * @return string
 */
function gnn_defer_scripts( $tag, $handle ) {
	if ( in_array( $handle, array( 'gnn-theme', 'gnn-slider', 'gnn-layout-geometry' ), true ) && false === strpos( $tag, ' defer' ) ) {
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
 *
 * When the toggle is hidden (show_toggle off), a visitor's OWN choice can
 * no longer be recorded going forward — but a STALE gnn-theme value saved
 * from before the toggle was hidden would otherwise still override the
 * chosen Default mode, defeating a single-style lock. So localStorage is
 * only consulted while the toggle is actually visible.
 */
function gnn_head_bootstrap() {
	$default = 'light' === get_theme_mod( 'gnn_default_theme', 'dark' ) ? 'light' : 'dark';
	$locked  = ! gnn_option( 'show_toggle' );
	?>
	<script id="gnn-theme-mode">
	(function () {
		document.documentElement.classList.add('gnn-js');
		var mode, locked = <?php echo $locked ? 'true' : 'false'; ?>,
			remember = <?php echo gnn_option( 'remember_mode' ) ? 'true' : 'false'; ?>;
		if (remember && !locked) { try { mode = localStorage.getItem('gnn-theme'); } catch (e) {} }
		if (mode !== 'dark' && mode !== 'light') { mode = '<?php echo esc_js( $default ); ?>'; }
		document.documentElement.setAttribute('data-theme', mode);
		window.gnnThemeRemember = remember && !locked;
	})();
	</script>
	<?php
}
add_action( 'wp_head', 'gnn_head_bootstrap', 0 );

/**
 * Keep the block editor's "Accent" palette swatch in sync with the panel's
 * accent color. theme.json can only carry a static value, so the editor
 * would otherwise keep offering the shipped default long after the site's
 * accent changed. Reads the real palette back out of theme.json and swaps
 * only the accent entry, so the other colors stay defined in one place.
 *
 * `wp_theme_json_data_theme` needs WP 6.1+; on 6.0 the filter simply never
 * fires and the static theme.json value applies, as before.
 *
 * @param WP_Theme_JSON_Data $theme_json Theme's theme.json data.
 * @return WP_Theme_JSON_Data
 */
function gnn_sync_editor_accent( $theme_json ) {
	$accent = sanitize_hex_color( (string) get_theme_mod( 'gnn_accent_color', '#34d399' ) );
	if ( ! $accent ) {
		return $theme_json;
	}
	$data    = $theme_json->get_data();
	$palette = isset( $data['settings']['color']['palette'] ) ? $data['settings']['color']['palette'] : array();
	if ( ! $palette ) {
		return $theme_json;
	}
	$changed = false;
	foreach ( $palette as $i => $color ) {
		if ( isset( $color['slug'] ) && 'accent' === $color['slug'] ) {
			$palette[ $i ]['color'] = $accent;
			$changed                = true;
		}
	}
	if ( ! $changed ) {
		return $theme_json;
	}
	return $theme_json->update_with(
		array(
			'version'  => 2,
			'settings' => array( 'color' => array( 'palette' => $palette ) ),
		)
	);
}
add_filter( 'wp_theme_json_data_theme', 'gnn_sync_editor_accent' );

/**
 * Add a pingback url auto-discovery header for single posts.
 */
function gnn_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'gnn_pingback_header' );

/**
 * Whether block patterns are registered on init: wp-admin (editors, AJAX) and WP-CLI.
 *
 * @return bool
 */
function gnn_block_patterns_eager() {
	return is_admin() || ( defined( 'WP_CLI' ) && WP_CLI );
}

/**
 * Pattern callbacks deferred outside wp-admin, keyed by their original init priority.
 *
 * @param callable|null $callback Callback to record.
 * @param int           $priority Original init priority.
 * @return array
 */
function gnn_deferred_block_patterns( $callback = null, $priority = 10 ) {
	static $callbacks = array();
	if ( null !== $callback ) {
		$callbacks[ $priority ][] = $callback;
		ksort( $callbacks );
	}
	return $callbacks;
}

/**
 * Register a pattern callback where patterns are read. Visitor pages skip the markup;
 * REST requests (the editor inserter) register on rest_api_init, since REST_REQUEST
 * is not known yet on init.
 *
 * @param callable $callback Pattern registration callback.
 * @param int      $priority Hook priority.
 */
function gnn_add_block_patterns( $callback, $priority = 10 ) {
	if ( gnn_block_patterns_eager() ) {
		add_action( 'init', $callback, $priority );
		return;
	}
	gnn_deferred_block_patterns( $callback, $priority );
	add_action( 'rest_api_init', $callback, $priority );
}

/**
 * A core/pattern block that references a GNN pattern still renders on visitor pages.
 *
 * @param string|null $pre_render   Short-circuit output.
 * @param array       $parsed_block Block being rendered.
 * @return string|null
 */
function gnn_block_patterns_on_demand( $pre_render, $parsed_block ) {
	$slug = isset( $parsed_block['blockName'], $parsed_block['attrs']['slug'] ) && 'core/pattern' === $parsed_block['blockName'] ? (string) $parsed_block['attrs']['slug'] : '';
	if ( 0 === strpos( $slug, 'gnn/' ) && class_exists( 'WP_Block_Patterns_Registry' ) && ! WP_Block_Patterns_Registry::get_instance()->is_registered( $slug ) ) {
		foreach ( gnn_deferred_block_patterns() as $callbacks ) {
			foreach ( $callbacks as $callback ) {
				call_user_func( $callback );
			}
		}
	}
	return $pre_render;
}
if ( ! gnn_block_patterns_eager() ) {
	add_filter( 'pre_render_block', 'gnn_block_patterns_on_demand', 10, 2 );
}

require get_template_directory() . '/inc/blog.php';
require get_template_directory() . '/inc/platform.php';
require get_template_directory() . '/inc/site-platform.php';
require get_template_directory() . '/inc/authoring.php';
require get_template_directory() . '/inc/shared-library.php';
require get_template_directory() . '/inc/layout-system.php';
require get_template_directory() . '/inc/design-system.php';
require get_template_directory() . '/inc/integrations.php';
require get_template_directory() . '/inc/templates/common.php';
require get_template_directory() . '/inc/sections/registry.php';
require get_template_directory() . '/inc/templates/pro.php';
require get_template_directory() . '/inc/templates/commerce.php';
require get_template_directory() . '/inc/templates/design.php';
require get_template_directory() . '/inc/templates/architecture.php';
require get_template_directory() . '/inc/starters.php';
require get_template_directory() . '/inc/architecture.php';
require get_template_directory() . '/inc/starter-layouts.php';
require get_template_directory() . '/inc/site-profiles.php';
require get_template_directory() . '/inc/template-loader.php';
require get_template_directory() . '/inc/content-modules.php';
// Hooked here, after content-modules.php, to keep the frontend stylesheet order.
add_action( 'wp_enqueue_scripts', 'gnn_demo_site_styles', 17 );
require get_template_directory() . '/inc/configuration.php';
require get_template_directory() . '/inc/options.php';
require get_template_directory() . '/inc/asset-compatibility.php';
require get_template_directory() . '/inc/typography.php';
require get_template_directory() . '/inc/font-catalog.php';
require get_template_directory() . '/inc/button-styles.php';
if ( is_admin() ) {
	require get_template_directory() . '/inc/admin-start.php';
	require get_template_directory() . '/inc/admin-panel.php';
	require get_template_directory() . '/inc/workspace.php';
	require get_template_directory() . '/inc/import-preview.php';
	require get_template_directory() . '/inc/admin-screens.php';
	require get_template_directory() . '/inc/demo/reset-engine.php';
	require get_template_directory() . '/inc/demo/site-reset.php';
	require get_template_directory() . '/inc/demo/demo-reset.php';
}
// Starter, demo and package tools run only from wp-admin (including AJAX and admin-post),
// WP-Cron (package expiry) and WP-CLI; visitor and REST requests never call them.
if ( is_admin() || wp_doing_cron() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	require get_template_directory() . '/inc/starter-editors.php';
	require get_template_directory() . '/inc/demo/demo-profiles.php';
	require get_template_directory() . '/inc/demo/demo-templates.php';
	require get_template_directory() . '/inc/starter-maintenance.php';
	require get_template_directory() . '/inc/demo/demo-site.php';
	require get_template_directory() . '/inc/portability.php';
	require get_template_directory() . '/inc/demo/demo-package.php';
	require get_template_directory() . '/inc/demo/demo-package-import.php';
	require get_template_directory() . '/inc/demo/demo-package-admin.php';
	require get_template_directory() . '/inc/demo/demo-import.php';
} else {
	// ALTERNATE_WP_CRON runs due events inside an ordinary request; load the expiry handler first.
	add_action(
		'gnn_package_expire',
		function () {
			require_once get_template_directory() . '/inc/demo/demo-package.php';
			require_once get_template_directory() . '/inc/demo/demo-package-admin.php';
		},
		9
	);
}
// Theme updates are also checked by WP-Cron and WP-CLI outside wp-admin.
require get_template_directory() . '/inc/updater.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/frontend.php';
require get_template_directory() . '/inc/menu-controls.php';
require get_template_directory() . '/inc/page-layouts.php';
require get_template_directory() . '/inc/class-gnn-walker-nav-menu.php';
require get_template_directory() . '/inc/class-gnn-walker-comment.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/slider.php';
require get_template_directory() . '/inc/page-meta.php';
require get_template_directory() . '/inc/patterns.php';

if ( class_exists( 'WooCommerce' ) ) {
	require get_template_directory() . '/inc/woocommerce.php';
}

if ( defined( 'ELEMENTOR_VERSION' ) ) {
	require get_template_directory() . '/inc/elementor.php';
	require get_template_directory() . '/inc/elementor-kit-sync.php';
	add_action(
		'elementor/widgets/register',
		function ( $manager ) {
			require_once get_template_directory() . '/inc/elementor-modules.php';
			gnn_register_elementor_modules( $manager );
		}
	);
	require get_template_directory() . '/inc/elementor-templates.php';
}
