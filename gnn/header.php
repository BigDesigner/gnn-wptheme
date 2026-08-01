<?php
/**
 * The header for the GNN theme.
 *
 * @package GNN
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="https://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'gnn' ); ?></a>

	<?php gnn_render_topbar(); // Top announcement bar (panel toggle). ?>

	<?php
	// Elementor Pro Theme Builder: a "header" template replaces the theme header.
	if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) :
		$gnn_centered = 'centered' === gnn_option( 'header_layout' );
		?>
	<header id="masthead" class="site-header">
		<div class="site-header__inner">

			<button class="menu-toggle" aria-controls="mobile-navigation" aria-expanded="false">
				<span class="menu-toggle__bar"></span>
				<span class="menu-toggle__bar"></span>
				<span class="menu-toggle__bar"></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'gnn' ); ?></span>
			</button>

			<?php if ( $gnn_centered ) : ?>

				<nav class="main-navigation main-navigation--left" aria-label="<?php esc_attr_e( 'Header left', 'gnn' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => has_nav_menu( 'header-left' ) ? 'header-left' : 'primary',
							'menu_class'     => 'menu',
							'container'      => false,
							'fallback_cb'    => false,
							'walker'         => new GNN_Walker_Nav_Menu(),
						)
					);
					?>
				</nav>

				<div class="site-branding site-branding--center">
					<?php gnn_the_logo(); ?>
				</div>

				<nav class="main-navigation main-navigation--right" aria-label="<?php esc_attr_e( 'Header right', 'gnn' ); ?>">
					<?php
					if ( has_nav_menu( 'header-right' ) ) {
						wp_nav_menu(
							array(
								'theme_location' => 'header-right',
								'menu_class'     => 'menu',
								'container'      => false,
								'fallback_cb'    => false,
								'walker'         => new GNN_Walker_Nav_Menu(),
							)
						);
					}
					?>
				</nav>

			<?php else : ?>

				<div class="site-branding">
					<?php gnn_the_logo(); // Panel logo pair → site title. ?>
				</div><!-- .site-branding -->

				<nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'Primary', 'gnn' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'menu_id'        => 'primary-menu',
							'menu_class'     => 'menu',
							'container'      => false,
							'fallback_cb'    => false,
							'walker'         => new GNN_Walker_Nav_Menu(),
						)
					);
					?>
				</nav><!-- #site-navigation -->

			<?php endif; ?>

			<div class="header-actions">
				<?php if ( gnn_option( 'show_search' ) ) : ?>
					<a class="header-search-link" href="<?php echo esc_url( home_url( '/?s=' ) ); ?>" aria-label="<?php esc_attr_e( 'Search', 'gnn' ); ?>">
						<svg class="gnn-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
					</a>
				<?php endif; ?>

				<?php if ( gnn_option( 'show_toggle' ) ) : ?>
					<button class="theme-toggle" aria-label="<?php esc_attr_e( 'Toggle dark / light mode', 'gnn' ); ?>">
						<span class="theme-toggle__icon theme-toggle__icon--sun" aria-hidden="true">&#9728;</span>
						<span class="theme-toggle__icon theme-toggle__icon--moon" aria-hidden="true">&#9790;</span>
					</button>
				<?php endif; ?>

				<?php if ( class_exists( 'WooCommerce' ) && gnn_option( 'show_cart' ) ) : ?>
					<a class="cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
						<?php esc_html_e( 'Cart', 'gnn' ); ?>
						<span class="cart-count"><?php echo esc_html( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?></span>
					</a>
				<?php endif; ?>
			</div><!-- .header-actions -->

		</div><!-- .site-header__inner -->

		<nav id="mobile-navigation" class="mobile-navigation" aria-label="<?php esc_attr_e( 'Mobile', 'gnn' ); ?>" hidden>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => has_nav_menu( 'mobile' ) ? 'mobile' : 'primary',
					'menu_id'        => 'mobile-menu',
					'menu_class'     => 'menu',
					'container'      => false,
					'fallback_cb'    => false,
				)
			);
			?>
		</nav><!-- #mobile-navigation -->
	</header><!-- #masthead -->
	<?php endif; // End theme header (Elementor location fallback). ?>
