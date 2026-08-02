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
function gnn_render_topbar() {
	if ( ! gnn_option( 'topbar_enable' ) ) {
		return;
	}
	$text  = trim( (string) gnn_option( 'topbar_text' ) );
	$email = trim( (string) gnn_option( 'topbar_email' ) );
	$phone = trim( (string) gnn_option( 'topbar_phone' ) );
	if ( '' === $text && '' === $email && '' === $phone ) {
		return;
	}

	$bg   = (string) gnn_option( 'topbar_bg' );
	$fg   = (string) gnn_option( 'topbar_text_color' );
	$vars = array();
	if ( $bg ) {
		$vars[] = '--gnn-topbar-bg:' . $bg;
	}
	if ( $fg ) {
		$vars[] = '--gnn-topbar-fg:' . $fg;
	}
	$style = $vars ? ' style="' . esc_attr( implode( ';', $vars ) ) . '"' : '';
	?>
	<div class="gnn-topbar"<?php echo $style; // phpcs:ignore WordPress.Security.EscapeOutput -- built from esc_attr()'d hex colors above. ?>>
		<div class="gnn-topbar__inner">
			<?php if ( '' !== $text ) : ?>
				<span class="gnn-topbar__text">
					<svg class="gnn-topbar__icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M11 5 6 9H2v6h4l5 4z"></path><path d="M15.5 8.5a5 5 0 0 1 0 7"></path></svg>
					<?php echo esc_html( $text ); ?>
				</span>
			<?php endif; ?>
			<span class="gnn-topbar__contact">
				<?php if ( '' !== $phone ) : ?>
					<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>">
						<svg class="gnn-topbar__icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.9.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
						<?php echo esc_html( $phone ); ?>
					</a>
				<?php endif; ?>
				<?php if ( '' !== $email ) : ?>
					<a href="mailto:<?php echo esc_attr( $email ); ?>">
						<svg class="gnn-topbar__icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="m22 7-10 6L2 7"></path></svg>
						<?php echo esc_html( $email ); ?>
					</a>
				<?php endif; ?>
			</span>
		</div>
	</div>
	<?php
}

/**
 * Print the footer widgets: scroll-to-top, mobile dock, preloader/overlay.
 */
function gnn_render_footer_extras() {
	// Preloader / loading overlay.
	if ( gnn_option( 'preloader' ) ) {
		echo '<div class="gnn-preloader" aria-hidden="true"><span class="gnn-preloader__bar"></span></div>';
	}
	if ( gnn_option( 'loading_screen' ) ) {
		echo '<div class="gnn-loading" aria-hidden="true"><span class="gnn-loading__spinner"></span></div>';
	}

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
 * @param int $menu_item_id Menu item post ID.
 */
function gnn_save_menu_item_badge( $menu_item_id ) {
	if ( ! isset( $_POST['menu-item-gnn-badge'][ $menu_item_id ] ) ) {
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
add_action( 'wp_update_nav_menu_item', 'gnn_save_menu_item_badge' );

/**
 * Mobile bottom app dock.
 */
function gnn_render_mobile_dock() {
	$cart_url = class_exists( 'WooCommerce' ) ? wc_get_cart_url() : gnn_contact_url();
	$cart_lbl = class_exists( 'WooCommerce' ) ? __( 'Cart', 'gnn' ) : __( 'Contact', 'gnn' );
	// Same Feather-style outline SVGs as the header (gnn-icon class), so the
	// dock never depends on the optional Google Material Symbols toggle.
	$cart_icon = class_exists( 'WooCommerce' )
		? '<circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>'
		: '<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22 6 12 13 2 6"></polyline>';
	?>
	<nav class="gnn-dock" aria-label="<?php esc_attr_e( 'Mobile navigation', 'gnn' ); ?>">
		<a class="gnn-dock__item" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<svg class="gnn-dock__ico" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
			<span class="gnn-dock__lbl"><?php esc_html_e( 'Home', 'gnn' ); ?></span>
		</a>
		<a class="gnn-dock__item" href="<?php echo esc_url( home_url( '/?s=' ) ); ?>">
			<svg class="gnn-dock__ico" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
			<span class="gnn-dock__lbl"><?php esc_html_e( 'Search', 'gnn' ); ?></span>
		</a>
		<?php if ( gnn_option( 'show_toggle' ) ) : ?>
			<button class="gnn-dock__item theme-toggle" aria-label="<?php esc_attr_e( 'Toggle dark / light mode', 'gnn' ); ?>">
				<svg class="gnn-dock__ico theme-toggle__icon theme-toggle__icon--sun" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="5" fill="currentColor" stroke="none"></circle><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"></path></svg>
				<svg class="gnn-dock__ico theme-toggle__icon theme-toggle__icon--moon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
				<span class="gnn-dock__lbl"><?php esc_html_e( 'Theme', 'gnn' ); ?></span>
			</button>
		<?php endif; ?>
		<a class="gnn-dock__item" href="<?php echo esc_url( $cart_url ); ?>">
			<svg class="gnn-dock__ico" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><?php echo $cart_icon; // phpcs:ignore WordPress.Security.EscapeOutput -- literal, hardcoded SVG markup, not user input. ?></svg>
			<span class="gnn-dock__lbl"><?php echo esc_html( $cart_lbl ); ?></span>
		</a>
	</nav>
	<?php
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
