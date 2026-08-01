<?php
/**
 * The footer for the GNN theme.
 *
 * @package GNN
 */

?>
	<?php
	// Elementor Pro Theme Builder: a "footer" template replaces the theme footer.
	if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) :
		?>
	<footer id="colophon" class="site-footer">
		<div class="site-footer__widgets">
			<?php
			$gnn_fbrand = (string) gnn_option( 'footer_brand_type' ); // text | image | both | none.
			if ( 'none' !== $gnn_fbrand ) :
				?>
				<div class="footer-column footer-column--brand">
					<?php
					$gnn_footer_logo_shown = false;
					if ( 'image' === $gnn_fbrand || 'both' === $gnn_fbrand ) {
						$gnn_footer_logo_shown = gnn_the_footer_logo();
					}
					// Text brand when set to text/both, or as a fallback when an
					// image brand was requested but no logo is uploaded.
					if ( 'text' === $gnn_fbrand || 'both' === $gnn_fbrand || ! $gnn_footer_logo_shown ) :
						?>
						<div class="footer-brand">
							<?php bloginfo( 'name' ); ?><span class="site-title__dot">.</span>
						</div>
					<?php endif; ?>

					<?php
					$gnn_ftag = trim( (string) gnn_option( 'footer_tagline' ) );
					if ( '' === $gnn_ftag ) {
						$gnn_ftag = (string) get_bloginfo( 'description' );
					}
					if ( '' !== trim( $gnn_ftag ) ) :
						?>
						<div class="footer-tagline"><?php echo wp_kses_post( $gnn_ftag ); ?></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
				<?php if ( is_active_sidebar( 'footer-' . $i ) ) : ?>
					<div class="footer-column widget-area">
						<?php dynamic_sidebar( 'footer-' . $i ); ?>
					</div>
				<?php elseif ( has_nav_menu( 'footer-' . $i ) ) : ?>
					<div class="footer-column">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'footer-' . $i,
								'menu_class'     => 'menu footer-menu',
								'container'      => false,
								'depth'          => 1,
								'fallback_cb'    => false,
							)
						);
						?>
					</div>
				<?php endif; ?>
			<?php endfor; ?>
		</div><!-- .site-footer__widgets -->

		<div class="site-footer__bottom">
			<span class="site-copyright">
				<?php
				$gnn_copy = trim( (string) gnn_option( 'footer_copyright' ) );
				if ( '' !== $gnn_copy ) {
					// Panel text with %year% / %site% placeholders.
					echo esc_html( str_replace( array( '%year%', '%site%' ), array( gmdate( 'Y' ), get_bloginfo( 'name' ) ), $gnn_copy ) );
				} else {
					/* translators: 1: current year, 2: site name. */
					printf( esc_html__( '© %1$s %2$s. All rights reserved.', 'gnn' ), esc_html( gmdate( 'Y' ) ), esc_html( get_bloginfo( 'name' ) ) );
				}
				?>
			</span>
			<?php $gnn_legal = get_theme_mod( 'gnn_footer_legal', '' ); ?>
			<?php if ( '' !== trim( (string) $gnn_legal ) ) : // Entered in Customizer → Brand. ?>
				<span class="site-legal"><?php echo esc_html( $gnn_legal ); ?></span>
			<?php endif; ?>
		</div><!-- .site-footer__bottom -->
	</footer><!-- #colophon -->
	<?php endif; // End theme footer (Elementor location fallback). ?>
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
