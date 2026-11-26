<?php
/**
 * Native header, shared-header menus and responsive style tokens.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Values for an explicit on/off override or legacy inheritance. */
function gnn_header_overlay_choices() {
	return array( '' => __( 'Inherit', 'gnn' ), 1 => __( 'On', 'gnn' ), 0 => __( 'Off', 'gnn' ) );
}

/** Initial geometry and the only viewport-dependent header settings. */
function gnn_header_css() {
	$config = gnn_header_config();
	$main   = $config['header_height'];
	$nav    = $config['two_rows'] ? 44 : 0;
	$space  = 'floating' === $config['header_shape'] ? 12 : 0;
	$hidden = -2 === $config['header_template'];
	$bar    = ! $hidden && $config['topbar'] ? 40 : 0;
	$height = $hidden ? 0 : $main + $nav + $space;
	$admin  = is_admin_bar_showing() ? 32 : 0;
	$css    = ':root{--gnn-header-height:' . $main . 'px;--gnn-header-h:' . $height . 'px;--gnn-topbar-h:' . $bar . 'px;--gnn-admin-h:' . $admin . 'px}';
	if ( $admin ) {
		$css .= '@media(max-width:782px){:root{--gnn-admin-h:46px}}';
	}
	$mobile_height = $hidden ? 0 : $main + $space;
	$mobile_max    = number_format( $config['header_mobile_breakpoint'] - 0.02, 2, '.', '' );
	$css .= '@media(max-width:' . $mobile_max . 'px){:root{--gnn-header-h:' . $mobile_height . 'px;--gnn-topbar-h:' . ( $config['topbar_mobile'] ? $bar : 0 ) . 'px;--gnn-header-fallback-position:relative;--gnn-header-fallback-inset:auto;--gnn-header-desktop:none;--gnn-header-mobile:block;--gnn-header-mobile-grid:auto minmax(0,1fr);--gnn-header-brand-column:2;--gnn-header-brand-max:100%;--gnn-header-logo-limit:var(--gnn-logo-h-mobile,34px);--gnn-header-topbar:' . ( $config['topbar_mobile'] ? 'block' : 'none' ) . '}}';

	foreach ( array( 'dark', 'light' ) as $mode ) {
		$bg     = $config[ 'header_bg_' . $mode ];
		$text   = $config[ 'header_text_' . $mode ];
		$accent = $config[ 'header_accent_' . $mode ];
		$check  = $bg ?: sanitize_hex_color( (string) gnn_option( 'color_bg_' . $mode ) );
		$check  = $check ?: ( 'light' === $mode ? '#fafafa' : '#0a0a0b' );
		$css   .= ( 'light' === $mode ? ':root[data-theme="light"]' : ':root:not([data-theme="light"])' ) . '{';
		if ( $bg ) {
			$css .= '--gnn-header-custom-bg:' . $bg . ';';
		}
		if ( $bg || $text || $accent ) {
			$css .= '--gnn-header-custom-panel:' . $check . ';';
		}
		if ( $text || $bg ) {
			$css .= '--gnn-header-custom-text:' . gnn_readable_brand_color( $text ?: gnn_accent_ink_for( $check ), $check ) . ';';
			if ( $text ) {
				$css .= '--gnn-header-overlay-text:' . gnn_readable_brand_color( $text, '#404040' ) . ';';
			}
		}
		if ( $accent || $bg ) {
			$fill = $accent ?: gnn_brand_accent( $mode );
			$css .= '--gnn-header-custom-accent:' . $fill . ';--gnn-header-custom-accent-text:' . gnn_readable_brand_color( $fill, $check ) . ';--gnn-header-custom-ink:' . gnn_accent_ink_for( $fill ) . ';';
			if ( $accent ) {
				$css .= '--gnn-header-overlay-accent:' . gnn_readable_brand_color( $accent, '#404040' ) . ';';
			}
		}
		$css .= '}';
	}
	return $css;
}

/** Render the top bar once, with either a native or a shared header. */
function gnn_render_header() {
	$config = gnn_header_config();
	if ( -2 === $config['header_template'] ) {
		return;
	}
	?>
	<div class="gnn-header-shell" data-gnn-header-shell data-behavior="<?php echo esc_attr( $config['header_behavior'] ); ?>" data-overlay="<?php echo $config['header_overlay'] ? '1' : '0'; ?>">
		<?php
		gnn_render_topbar( $config );
		if ( ! gnn_render_layout_region( 'header' ) && ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) ) {
			gnn_render_native_header( $config );
		}
		?>
	</div>
	<?php
}

/** Give each independently rendered shortcode a unique control namespace. */
function gnn_header_shortcode() {
	$config = gnn_header_config();
	$prefix = wp_unique_id( 'gnn-header-menu-' );
	ob_start();
	?>
	<div class="gnn-header-menu gnn-header-controls" data-gnn-header data-breakpoint="<?php echo esc_attr( $config['header_mobile_breakpoint'] ); ?>" data-overflow="<?php echo esc_attr( $config['header_overflow'] ); ?>">
		<?php gnn_render_header_toggle( $prefix ); ?>
		<?php gnn_render_header_nav( $prefix ); ?>
		<?php gnn_render_header_tail( $config, $prefix ); ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'gnn_header_menu', 'gnn_header_shortcode' );

/** Native structure uses explicit main and navigation rows. */
function gnn_render_native_header( $config ) {
	$structure = $config['header_structure'];
	$centered  = 'centered' === $structure;
	$classes   = 'site-header gnn-header-controls gnn-header-structure-' . $structure;
	if ( $centered ) {
		$classes .= ' gnn-header-centered';
	}
	?>
	<header id="masthead" class="<?php echo esc_attr( $classes ); ?>" data-gnn-header
		data-breakpoint="<?php echo esc_attr( $config['header_mobile_breakpoint'] ); ?>" data-overflow="<?php echo esc_attr( $config['header_overflow'] ); ?>"
		data-surface="<?php echo esc_attr( $config['header_surface'] ); ?>" data-shape="<?php echo esc_attr( $config['header_shape'] ); ?>"
		data-width="<?php echo esc_attr( $config['header_width'] ); ?>" data-align="<?php echo esc_attr( $config['header_menu_align'] ); ?>"
		<?php if ( 'minimal' === $config['preset'] ) : ?>data-minimal<?php endif; ?>>
		<div class="site-header__inner gnn-header-row">
			<?php gnn_render_header_toggle( '' ); ?>
			<?php if ( $centered ) : ?>
				<?php gnn_render_header_nav( '', 'left' ); ?>
			<?php endif; ?>
			<div class="site-branding gnn-header-slot--brand<?php echo $centered ? ' site-branding--center' : ''; ?>">
				<?php gnn_the_logo( $config['header_overlay'] ? $config['logo_overlay'] : 0 ); ?>
			</div>
			<?php if ( $centered ) : ?><div class="gnn-header-right-group"><?php endif; ?>
			<?php if ( ! $config['two_rows'] ) : ?>
				<?php gnn_render_header_nav( '', $centered ? 'right' : '' ); ?>
			<?php endif; ?>
			<div class="header-actions gnn-header-slot--actions"><?php gnn_render_header_actions( $config, 'desktop', '' ); ?></div>
			<?php if ( $centered ) : ?></div><?php endif; ?>
		</div>
		<?php if ( $config['two_rows'] ) : ?>
			<div class="gnn-header-nav-row"><?php gnn_render_header_nav( '' ); ?></div>
		<?php endif; ?>
		<?php gnn_render_header_tail( $config, '' ); ?>
	</header>
	<?php
}

/** Use the legacy IDs on the native header and scoped IDs in shortcodes. */
function gnn_header_id( $prefix, $name ) {
	return $prefix ? $prefix . '-' . $name : $name;
}

/** Resolve menu assignments while avoiding duplicates across split locations. */
function gnn_header_menu_locations() {
	$locations = get_nav_menu_locations();
	$assigned  = array();
	foreach ( array( 'primary', 'mobile', 'header-left', 'header-right' ) as $slot ) {
		if ( ! empty( $locations[ $slot ] ) && has_nav_menu( $slot ) ) {
			$assigned[ $slot ] = (int) $locations[ $slot ];
		}
	}
	return $assigned;
}

/** Render a list using a namespaced walker. */
function gnn_header_menu_list( $slot, $id, $mobile = false, $half = '' ) {
	return wp_nav_menu(
		array(
			'theme_location'         => $slot,
			'menu_id'                => $id,
			'menu_class'             => 'menu',
			'items_wrap'             => '%3$s',
			'container'              => false,
			'fallback_cb'            => false,
			'echo'                   => false,
			'gnn_header_half'        => $half,
			'gnn_header_menu_prefix' => $id,
			'walker'                 => $mobile ? new GNN_Walker_Mobile_Menu() : new GNN_Walker_Nav_Menu(),
		)
	);
}

/** Render split navigation or a normal desktop menu. */
function gnn_render_header_nav( $prefix, $side = '' ) {
	$assigned = gnn_header_menu_locations();
	$locations = array();
	$half = '';
	if ( $side ) {
		if ( isset( $assigned['header-left'] ) || isset( $assigned['header-right'] ) ) {
			$slot = 'header-' . $side;
			if ( isset( $assigned[ $slot ] ) ) {
				$locations[] = $slot;
			}
		} elseif ( $assigned ) {
			$locations[] = isset( $assigned['primary'] ) ? 'primary' : 'mobile';
			$half = $side;
		}
	} elseif ( isset( $assigned['primary'] ) ) {
		$locations[] = 'primary';
	} else {
		$locations = array_keys( array_unique( array_intersect_key( $assigned, array( 'header-left' => true, 'header-right' => true ) ) ) );
		if ( ! $locations && isset( $assigned['mobile'] ) ) {
			$locations[] = 'mobile';
		}
	}
	$primary_side = isset( $assigned['header-left'] ) || ( $assigned && ! isset( $assigned['header-right'] ) ) ? 'left' : 'right';
	$canonical = '' === $side || $side === $primary_side;
	$id     = gnn_header_id( $prefix, $canonical ? 'primary-menu' : 'header-' . $side . '-menu' );
	$nav_id = gnn_header_id( $prefix, $canonical ? 'site-navigation' : 'site-navigation-' . $side );
	$label  = 'left' === $side ? __( 'Header left', 'gnn' ) : ( 'right' === $side ? __( 'Header right', 'gnn' ) : __( 'Primary', 'gnn' ) );
	$items  = '';
	foreach ( $locations as $index => $slot ) {
		$item_prefix = $canonical && ! $prefix && 0 === $index ? 'menu' : $id . '-' . $index;
		$items      .= gnn_header_menu_list( $slot, $item_prefix, false, $half );
	}
	?>
	<nav id="<?php echo esc_attr( $nav_id ); ?>" class="main-navigation gnn-header-slot--menu<?php echo $side ? ' main-navigation--' . esc_attr( $side ) : ''; ?>" aria-label="<?php echo esc_attr( $label ); ?>"<?php if ( ! $items && ( $assigned || 'left' === $side ) ) : ?> aria-hidden="true"<?php endif; ?>>
		<?php if ( $items ) : ?>
			<ul id="<?php echo esc_attr( $id ); ?>" class="menu">
				<?php echo $items; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped by the menu walker. ?>
				<li class="gnn-menu-more" hidden>
					<button class="gnn-submenu-toggle" type="button" aria-controls="<?php echo esc_attr( $id . '-more' ); ?>" aria-expanded="false">
						<?php echo esc_html__( 'More', 'gnn' ); ?><span class="dropdown-caret" aria-hidden="true"></span>
					</button>
					<ul id="<?php echo esc_attr( $id . '-more' ); ?>" class="sub-menu"></ul>
				</li>
			</ul>
		<?php elseif ( ! $assigned && 'left' !== $side ) : ?>
			<?php gnn_header_menu_hint(); ?>
		<?php endif; ?>
	</nav>
	<?php
}

/** Mobile prioritizes a dedicated menu, then primary, then assigned side menus. */
function gnn_header_mobile_menus( $prefix ) {
	$assigned = gnn_header_menu_locations();
	$slots    = array( 'mobile', 'primary', 'header-left', 'header-right' );
	$id       = gnn_header_id( $prefix, 'mobile-menu' );
	$items    = '';
	$seen     = array();
	foreach ( $slots as $slot ) {
		if ( ! isset( $assigned[ $slot ] ) || isset( $seen[ $assigned[ $slot ] ] ) ) {
			continue;
		}
		$seen[ $assigned[ $slot ] ] = true;
		$items .= gnn_header_menu_list( $slot, $id . '-' . $slot, true );
		if ( $items && in_array( $slot, array( 'mobile', 'primary' ), true ) ) {
			break;
		}
	}
	if ( $items ) {
		echo '<ul id="' . esc_attr( $id ) . '" class="menu">' . $items . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped by the menu walker.
	} else {
		gnn_header_menu_hint();
	}
}

/** A setup prompt is visible only to users who can administer site settings. */
function gnn_header_menu_hint() {
	if ( current_user_can( 'manage_options' ) ) {
		echo '<a class="gnn-menu-hint" href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">' . esc_html__( 'Assign a menu', 'gnn' ) . '</a>';
	}
}

/** Render the disclosure control in the main row or shortcode. */
function gnn_render_header_toggle( $prefix ) {
	?>
	<button type="button" class="menu-toggle" aria-controls="<?php echo esc_attr( gnn_header_id( $prefix, 'mobile-navigation' ) ); ?>" aria-expanded="false">
		<span class="menu-toggle__bar"></span><span class="menu-toggle__bar"></span><span class="menu-toggle__bar"></span>
		<span class="screen-reader-text"><?php echo esc_html__( 'Menu', 'gnn' ); ?></span>
	</button>
	<?php
}

/** Render the mobile panel and the shared search dialog. */
function gnn_render_header_tail( $config, $prefix ) {
	?>
	<div class="gnn-header-backdrop" hidden></div>
	<nav id="<?php echo esc_attr( gnn_header_id( $prefix, 'mobile-navigation' ) ); ?>" class="mobile-navigation" aria-label="<?php echo esc_attr__( 'Mobile', 'gnn' ); ?>" tabindex="-1">
		<button type="button" class="gnn-header-close" data-gnn-menu-close><?php echo esc_html__( 'Close menu', 'gnn' ); ?></button>
		<?php gnn_header_mobile_menus( $prefix ); ?>
		<div class="header-actions gnn-mobile-actions"><?php gnn_render_header_actions( $config, 'mobile', $prefix ); ?></div>
	</nav>
	<?php if ( 'link' !== $config['header_search_mode'] && ( $config['enabled']['search'] || gnn_option( 'mobile_dock' ) ) ) : ?>
		<dialog class="gnn-search-dialog" id="<?php echo esc_attr( gnn_header_id( $prefix, 'gnn-search-dialog' ) ); ?>" aria-labelledby="<?php echo esc_attr( gnn_header_id( $prefix, 'gnn-search-title' ) ); ?>">
			<div class="gnn-search-dialog__inner">
				<h2 id="<?php echo esc_attr( gnn_header_id( $prefix, 'gnn-search-title' ) ); ?>"><?php echo esc_html__( 'Search', 'gnn' ); ?></h2>
				<form method="dialog"><button type="submit" class="gnn-header-button"><?php echo esc_html__( 'Close', 'gnn' ); ?></button></form>
				<?php gnn_header_search_form( gnn_header_id( $prefix, 'gnn-search-overlay-field' ), $config, true ); ?>
			</div>
		</dialog>
	<?php endif; ?>
	<?php
}

/** Render enabled actions in order for the requested viewport. */
function gnn_render_header_actions( $config, $device, $prefix ) {
	$dock = gnn_option( 'mobile_dock' ) ? array_column( gnn_dock_items( $config ), 'type' ) : array();
	foreach ( $config['header_elements'] as $element ) {
		$id = $element['id'];
		if ( ! $config['enabled'][ $id ] || ! $element[ $device ] ) {
			continue;
		}
		$duplicate = in_array( $id, array( 'search', 'theme', 'cart' ), true ) && in_array( $id, $dock, true );
		echo '<div class="gnn-header-action gnn-header-action--' . esc_attr( $id ) . ( $duplicate ? ' gnn-dock-duplicate' : '' ) . '">';
		switch ( $id ) {
			case 'contact':
				gnn_render_header_contact( $config['contact'], $dock );
				break;
			case 'language':
				gnn_render_header_language();
				break;
			case 'search':
				gnn_render_header_search( $config, $device, $prefix );
				break;
			case 'theme':
				gnn_render_header_theme_toggle();
				break;
			case 'cart':
				gnn_render_header_cart();
				break;
			case 'cta':
				echo '<a class="gnn-btn header-cta" href="' . esc_url( gnn_option( 'header_cta_url' ) ) . '">' . esc_html( gnn_translated_option( 'header_cta_label' ) ) . '</a>';
				break;
		}
		echo '</div>';
	}
}

/** Phone and email values share the same source as the dock. */
function gnn_render_header_contact( $details, $dock = array() ) {
	$phone = preg_replace( '/[^0-9+]/', '', $details['phone'] );
	$email = sanitize_email( $details['email'] );
	if ( '' === $phone && '' === $email ) {
		return;
	}

	$phone_duplicate = '' !== $phone && in_array( 'phone', $dock, true );
	$email_duplicate = '' !== $email && in_array( 'email', $dock, true );
	$all_duplicates  = ( '' === $phone || $phone_duplicate ) && ( '' === $email || $email_duplicate );
	?>
	<div class="header-contact<?php if ( $all_duplicates ) : ?> gnn-dock-duplicate<?php endif; ?>">
		<?php if ( '' !== $phone ) : ?>
			<a<?php if ( $phone_duplicate ) : ?> class="gnn-dock-duplicate"<?php endif; ?> href="<?php echo esc_url( 'tel:' . $phone ); ?>"><?php gnn_header_icon( 'phone' ); ?><?php echo esc_html( $details['phone'] ); ?></a>
		<?php endif; ?>
		<?php if ( '' !== $email ) : ?>
			<a<?php if ( $email_duplicate ) : ?> class="gnn-dock-duplicate"<?php endif; ?> href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php gnn_header_icon( 'email' ); ?><?php echo esc_html( $details['email'] ); ?></a>
		<?php endif; ?>
	</div>
	<?php
}

/** Render the active language switcher with its usual plugin integration. */
function gnn_render_header_language() {
	if ( function_exists( 'pll_the_languages' ) ) {
		$links = pll_the_languages( array( 'show_flags' => 0, 'show_names' => 1, 'echo' => 0 ) );
		if ( $links ) {
			echo '<ul class="gnn-languages">' . wp_kses_post( $links ) . '</ul>';
		}
	} elseif ( has_action( 'wpml_add_language_selector' ) ) {
		do_action( 'wpml_add_language_selector' );
	}
}

/** Search URLs remain usable without JavaScript. */
function gnn_header_search_url( $config ) {
	$url = home_url( '/?s=' );
	return 'products' === $config['header_search_scope'] ? add_query_arg( 'post_type', 'product', $url ) : $url;
}

/** A scoped field, dialog trigger or link. */
function gnn_render_header_search( $config, $device, $prefix ) {
	if ( 'field' === $config['header_search_mode'] ) {
		gnn_header_search_form( gnn_header_id( $prefix, 'gnn-header-search-field-' . $device ), $config );
		return;
	}
	?>
	<a class="header-search-link gnn-header-button" href="<?php echo esc_url( gnn_header_search_url( $config ) ); ?>"
		<?php if ( 'overlay' === $config['header_search_mode'] ) : ?>data-gnn-search="<?php echo esc_attr( gnn_header_id( $prefix, 'gnn-search-dialog' ) ); ?>"<?php endif; ?>
		aria-label="<?php echo esc_attr__( 'Search', 'gnn' ); ?>"><?php gnn_header_icon( 'search' ); ?></a>
	<?php
}

/** A native search field submits directly using the visitor's current scope. */
function gnn_header_search_form( $id, $config, $focus = false ) {
	?>
	<form class="gnn-header-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label class="screen-reader-text" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html__( 'Search', 'gnn' ); ?></label>
		<input type="search" id="<?php echo esc_attr( $id ); ?>" name="s" value="<?php echo esc_attr( get_search_query( false ) ); ?>" placeholder="<?php echo esc_attr__( 'Search', 'gnn' ); ?>"<?php if ( $focus ) : ?> autofocus<?php endif; ?>>
		<?php if ( 'products' === $config['header_search_scope'] ) : ?>
			<input type="hidden" name="post_type" value="product">
		<?php endif; ?>
		<button type="submit" aria-label="<?php echo esc_attr__( 'Search', 'gnn' ); ?>"><?php gnn_header_icon( 'search' ); ?></button>
	</form>
	<?php
}

/** Round mode toggle or a labeled dock action. */
function gnn_render_header_theme_toggle( $class = 'gnn-header-button', $label = '' ) {
	$icon_class = $label ? 'gnn-dock__ico ' : 'gnn-icon ';
	?>
	<button type="button" class="theme-toggle <?php echo esc_attr( $class ); ?>" aria-pressed="<?php echo 'light' === get_theme_mod( 'gnn_default_theme', 'dark' ) ? 'false' : 'true'; ?>" aria-label="<?php echo esc_attr( $label ?: __( 'Toggle dark / light mode', 'gnn' ) ); ?>">
		<?php gnn_header_icon( 'sun', $icon_class . 'theme-toggle__icon theme-toggle__icon--sun' ); ?>
		<?php gnn_header_icon( 'moon', $icon_class . 'theme-toggle__icon theme-toggle__icon--moon' ); ?>
		<?php if ( $label ) : ?><span class="gnn-dock__lbl"><?php echo esc_html( $label ); ?></span><?php endif; ?>
	</button>
	<?php
}

/** The cart count uses the same markup in AJAX fragments. */
function gnn_render_header_cart() {
	?>
	<a class="cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
		<?php gnn_header_icon( 'cart' ); ?>
		<span class="screen-reader-text"><?php echo esc_html__( 'Cart', 'gnn' ); ?></span>
		<?php gnn_render_cart_count(); ?>
	</a>
	<?php
}

/** Output a fixed, trusted outline icon. */
function gnn_header_icon( $name, $class = 'gnn-icon' ) {
	$paths = array(
		'home'    => '<path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2zM9 22V12h6v10"/>',
		'search'  => '<circle cx="11" cy="11" r="7"/><path d="m21 21-5-5"/>',
		'cart'    => '<circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/>',
		'phone'   => '<path d="M22 17v3a2 2 0 0 1-2.2 2 20 20 0 0 1-8.6-3A20 20 0 0 1 5 13 20 20 0 0 1 2 4.2 2 2 0 0 1 4 2h3a2 2 0 0 1 2 1.7l1 4a2 2 0 0 1-.5 2L8 11a16 16 0 0 0 5 5l1.4-1.5a2 2 0 0 1 2-.5l4 1a2 2 0 0 1 1.6 2z"/>',
		'email'   => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/>',
		'sun'     => '<circle cx="12" cy="12" r="5"/><path d="M12 1v2m0 18v2M1 12h2m18 0h2M4 4l1.5 1.5m13 13L20 20M4 20l1.5-1.5m13-13L20 4"/>',
		'moon'    => '<path d="M21 13a9 9 0 1 1-10-10 7 7 0 0 0 10 10z"/>',
		'link'    => '<path d="M6 18 18 6M8 6h10v10"/>',
	);
	$icon = $paths[ 'contact' === $name ? 'email' : $name ] ?? $paths['link'];
	echo '<svg class="' . esc_attr( $class ) . '" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $icon . '</svg>'; // phpcs:ignore WordPress.Security.EscapeOutput -- fixed SVG paths.
}
