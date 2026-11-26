<?php
/**
 * Front-end wiring for GNN Panel features: top bar, scroll-to-top, mobile
 * dock, preloader / loading screen, maintenance mode, smooth scroll and
 * scroll-in animations. Every piece is driven by a panel option.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Extra nav-menu locations used by the centered header layout.
 */
function gnn_register_extra_menus() {
	register_nav_menus(
		array(
			'header-left'  => __( 'Header Left (centered layout)', 'gnn' ),
			'header-right' => __( 'Header Right (centered layout)', 'gnn' ),
		)
	);
}
add_action( 'after_setup_theme', 'gnn_register_extra_menus', 20 );

/**
 * Keep each top-level menu branch together when splitting the primary menu.
 *
 * @param object[] $items Menu items.
 * @param object   $args Menu arguments.
 * @return object[]
 */
function gnn_split_header_items( $items, $args ) {
	if ( empty( $args->gnn_header_half ) || ! in_array( $args->gnn_header_half, array( 'left', 'right' ), true ) ) {
		return $items;
	}
	$parents = array();
	$roots   = array();
	foreach ( $items as $item ) {
		$parents[ (int) $item->ID ] = (int) $item->menu_item_parent;
		if ( ! $item->menu_item_parent ) {
			$roots[] = (int) $item->ID;
		}
	}
	$middle = (int) ceil( count( $roots ) / 2 );
	$keep   = 'left' === $args->gnn_header_half ? array_slice( $roots, 0, $middle ) : array_slice( $roots, $middle );
	return array_values(
		array_filter(
			$items,
			static function ( $item ) use ( $parents, $keep ) {
				$root = (int) $item->ID;
				$seen = array();
				while ( ! empty( $parents[ $root ] ) && ! isset( $seen[ $root ] ) ) {
					$seen[ $root ] = true;
					$root          = $parents[ $root ];
				}
				return in_array( $root, $keep, true );
			}
		)
	);
}
add_filter( 'wp_nav_menu_objects', 'gnn_split_header_items', 20, 2 );

/**
 * Enqueue the small features script when any interactive option needs it.
 */
function gnn_enqueue_features() {
	$needs_js = gnn_option( 'smooth_scroll' ) || gnn_option( 'scroll_top' )
		|| gnn_option( 'preloader' ) || gnn_option( 'loading_screen' )
		|| gnn_option( 'scroll_anim' );

	if ( $needs_js ) {
		wp_enqueue_script( 'gnn-features', get_template_directory_uri() . '/assets/js/features.js', array(), GNN_VERSION, true );
		wp_localize_script(
			'gnn-features',
			'gnnFeatures',
			array(
				'smoothScroll' => (bool) gnn_option( 'smooth_scroll' ),
				'scrollTop'    => (bool) gnn_option( 'scroll_top' ),
				'preloader'    => (bool) gnn_option( 'preloader' ),
				'loading'      => (bool) gnn_option( 'loading_screen' ),
				'scrollAnim'   => (bool) gnn_option( 'scroll_anim' ),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'gnn_enqueue_features' );

/**
 * Render the top announcement bar (called from header.php).
 */
function gnn_render_topbar( $config = null ) {
	$config = $config ?? gnn_header_config();
	if ( ! $config['topbar'] ) {
		return;
	}

	$vars = array();
	foreach ( array( 'bg' => 'topbar_bg', 'fg' => 'topbar_text_color' ) as $part => $key ) {
		$color = sanitize_hex_color( (string) gnn_option( $key ) );
		if ( $color ) {
			$vars[] = '--gnn-topbar-' . $part . ':' . $color;
		}
	}
	?>
	<div class="gnn-topbar" style="<?php echo esc_attr( implode( ';', $vars ) ); ?>">
		<div class="gnn-topbar__inner">
			<?php if ( '' !== $config['topbar_text'] ) : ?>
				<span class="gnn-topbar__text"><?php echo esc_html( $config['topbar_text'] ); ?></span>
			<?php endif; ?>
			<div class="gnn-topbar__contact"><?php gnn_render_header_contact( $config['contact'] ); ?></div>
		</div>
	</div>
	<?php
}

/**
 * Render loading indicators before content can be painted.
 * Footer insertion can cover content that is already visible on streamed pages.
 */
function gnn_render_loading_indicators() {
	if ( gnn_option( 'preloader' ) ) {
		echo '<div class="gnn-preloader" aria-hidden="true"><span class="gnn-preloader__bar"></span></div>';
	}
	if ( gnn_option( 'loading_screen' ) ) {
		echo '<div class="gnn-loading" aria-hidden="true"><span class="gnn-loading__spinner"></span></div>';
	}
}
add_action( 'wp_body_open', 'gnn_render_loading_indicators', 0 );

/** Print footer controls: scroll-to-top and mobile dock. */
function gnn_render_footer_extras() {
	// Scroll to top.
	if ( gnn_option( 'scroll_top' ) ) {
		printf(
			'<button class="gnn-scrolltop" aria-label="%s"><span aria-hidden="true">&#8593;</span></button>',
			esc_attr__( 'Scroll to top', 'gnn' )
		);
	}

	// Mobile bottom dock.
	if ( gnn_option( 'mobile_dock' ) ) {
		gnn_render_mobile_dock();
	}
}
add_action( 'wp_footer', 'gnn_render_footer_extras' );

/**
 * Auto-tag top-level content blocks with .gnn-anim for scroll-in reveal
 * (skips nested blocks so containers don't double-animate their children).
 *
 * @param string $content Rendered post content.
 * @return string
 */
function gnn_add_scroll_anim_class( $content ) {
	if ( ! gnn_option( 'scroll_anim' ) || is_admin() || is_feed() ) {
		return $content;
	}
	$tagged = preg_replace(
		'/<(figure|img|h[1-4]|blockquote)\b(?![^>]*class=")/',
		'<$1 class="gnn-anim"',
		$content,
		-1
	);
	return null !== $tagged ? $tagged : $content;
}
add_filter( 'the_content', 'gnn_add_scroll_anim_class', 20 );

// ----- Menu item badges (Appearance → Menus → item → Badge, e.g. "New", "Hot") ----------------------------------------------------------
// Rendered by GNN_Walker_Nav_Menu using the .gnn-badge design shared with WooCommerce sale/stock badges.

/**
 * Add a "Badge" text field to each menu item in Appearance → Menus.
 *
 * @param int $item_id Menu item post ID.
 */
function gnn_menu_item_badge_field( $item_id ) {
	$value = get_post_meta( $item_id, '_gnn_menu_badge', true );
	?>
	<p class="field-gnn-badge description description-wide">
		<label for="edit-menu-item-gnn-badge-<?php echo esc_attr( $item_id ); ?>">
			<?php esc_html_e( 'Badge (e.g. New, Hot, Sale)', 'gnn' ); ?><br>
			<input type="text" id="edit-menu-item-gnn-badge-<?php echo esc_attr( $item_id ); ?>"
				class="widefat code edit-menu-item-gnn-badge"
				name="menu-item-gnn-badge[<?php echo esc_attr( $item_id ); ?>]"
				value="<?php echo esc_attr( $value ); ?>">
		</label>
	</p>
	<?php
}
add_action( 'wp_nav_menu_item_custom_fields', 'gnn_menu_item_badge_field', 10, 1 );

/**
 * Save the menu item badge field.
 *
 * @param int $menu_id      Navigation menu ID.
 * @param int $menu_item_id Menu item post ID.
 */
function gnn_save_menu_item_badge( $menu_id, $menu_item_id ) {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	if ( ! isset( $_POST['menu-item-gnn-badge'][ $menu_item_id ] ) ) {
		return;
	}
	if ( ! is_scalar( $_POST['menu-item-gnn-badge'][ $menu_item_id ] ) ) {
		return;
	}
	check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );
	$badge = sanitize_text_field( wp_unslash( $_POST['menu-item-gnn-badge'][ $menu_item_id ] ) );
	if ( '' !== $badge ) {
		update_post_meta( $menu_item_id, '_gnn_menu_badge', $badge );
	} else {
		delete_post_meta( $menu_item_id, '_gnn_menu_badge' );
	}
}
add_action( 'wp_update_nav_menu_item', 'gnn_save_menu_item_badge', 10, 2 );

/**
 * Mobile bottom app dock.
 */
function gnn_render_mobile_dock() {
	$config = gnn_header_config();
	$items  = gnn_dock_items( $config );
	if ( ! $items ) {
		return;
	}
	echo '<nav class="gnn-dock" aria-label="' . esc_attr__( 'Mobile actions', 'gnn' ) . '">';
	foreach ( $items as $item ) {
		$type = $item['type'];
		$label = '<span class="gnn-dock__lbl">' . esc_html( $item['label'] ) . '</span>';
		if ( 'theme' === $type ) {
			gnn_render_header_theme_toggle( 'gnn-dock__item', $item['label'] );
			continue;
		}
		$search = 'search' === $type && 'link' !== $config['header_search_mode'];
		?>
		<a class="gnn-dock__item" href="<?php echo esc_url( $item['url'] ); ?>"<?php if ( $search ) : ?> data-gnn-dock-search<?php endif; ?>>
			<?php gnn_header_icon( $type, 'gnn-dock__ico' ); ?>
			<?php echo $label; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?>
			<?php if ( 'cart' === $type && function_exists( 'gnn_render_cart_count' ) ) { gnn_render_cart_count(); } ?>
		</a>
		<?php
	}
	echo '</nav>';
}

/**
 * Maintenance / coming-soon mode for logged-out visitors.
 */
function gnn_maintenance_mode() {
	if ( ! gnn_option( 'maintenance_mode' ) ) {
		return;
	}
	if ( is_user_logged_in() && current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		return;
	}
	// Allow the login screen through.
	if ( isset( $GLOBALS['pagenow'] ) && 'wp-login.php' === $GLOBALS['pagenow'] ) {
		return;
	}

	$message = trim( (string) gnn_option( 'maintenance_message' ) );
	if ( '' === $message ) {
		$message = __( 'We are performing scheduled maintenance. Please check back soon.', 'gnn' );
	}

	status_header( 503 );
	header( 'Retry-After: 3600' );
	nocache_headers();
	?>
	<!doctype html>
	<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php echo esc_html( get_bloginfo( 'name' ) ); ?></title>
		<?php wp_head(); ?>
	</head>
	<body class="gnn-maintenance">
		<main class="gnn-maintenance__inner">
			<div class="gnn-maintenance__brand"><?php gnn_the_logo(); ?></div>
			<p class="gnn-maintenance__msg"><?php echo esc_html( $message ); ?></p>
		</main>
		<?php wp_footer(); ?>
	</body>
	</html>
	<?php
	exit;
}
add_action( 'template_redirect', 'gnn_maintenance_mode' );

/**
 * Keep content REST routes closed while maintenance is on. Feeds already pass through template_redirect.
 * Users who can edit content keep REST access so the editors continue to work.
 *
 * @param mixed           $result Earlier short-circuit result.
 * @param WP_REST_Server  $server REST server.
 * @param WP_REST_Request $request Current request.
 * @return mixed
 */
function gnn_maintenance_rest( $result, $server, $request ) {
	if ( null !== $result || ! gnn_option( 'maintenance_mode' ) ) {
		return $result;
	}
	if ( is_user_logged_in() && ( current_user_can( 'manage_options' ) || current_user_can( 'edit_posts' ) ) ) {
		return $result;
	}
	$route = (string) $request->get_route();
	if ( 0 === strpos( $route, '/wp/v2/' ) || '/wp/v2' === $route || 0 === strpos( $route, '/oembed/' ) ) {
		return new WP_Error( 'gnn_maintenance', __( 'The site is under maintenance.', 'gnn' ), array( 'status' => 503 ) );
	}
	return $result;
}
add_filter( 'rest_pre_dispatch', 'gnn_maintenance_rest', 10, 3 );
