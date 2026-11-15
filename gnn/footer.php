<?php
/**
 * The footer for the GNN theme.
 *
 * @package GNN
 */

?>
	<?php
	// A page-level "Hide" remains authoritative for the whole footer.
	$gnn_footer_hidden = -2 === (int) gnn_layout_value( 'footer_template' );
	$gnn_shared_footer = false;
	if ( ! $gnn_footer_hidden && gnn_option( 'footer_show_shared' ) ) {
		$gnn_shared_footer = gnn_render_layout_region( 'footer' );
	}
	if ( ! $gnn_footer_hidden && ! $gnn_shared_footer && function_exists( 'elementor_theme_do_location' ) ) {
		$gnn_shared_footer = elementor_theme_do_location( 'footer' );
	}
	if ( ! $gnn_footer_hidden && ( ! $gnn_shared_footer || gnn_option( 'footer_show_with_shared' ) ) ) :
		?>
	<footer id="colophon" class="site-footer">
		<div class="site-footer__widgets">
			<?php
			$gnn_fbrand = (string) gnn_option( 'footer_brand_type' ); // text | image | both | none.
			$gnn_ftag   = trim( (string) gnn_translated_option( 'footer_tagline' ) );
			if ( '' === $gnn_ftag ) {
				$gnn_ftag = (string) get_bloginfo( 'description' );
			}
			if ( ( gnn_option( 'footer_show_brand' ) && 'none' !== $gnn_fbrand ) || ( gnn_option( 'footer_show_tagline' ) && '' !== trim( $gnn_ftag ) ) ) :
				?>
				<div class="footer-column footer-column--brand">
					<?php
					$gnn_footer_logo_shown = false;
					if ( gnn_option( 'footer_show_brand' ) && ( 'image' === $gnn_fbrand || 'both' === $gnn_fbrand ) ) {
						$gnn_footer_logo_shown = gnn_the_footer_logo();
					}
					// Text brand when set to text/both, or as a fallback when an
					// image brand was requested but no logo is uploaded.
					if ( gnn_option( 'footer_show_brand' ) && 'none' !== $gnn_fbrand && ( 'text' === $gnn_fbrand || 'both' === $gnn_fbrand || ! $gnn_footer_logo_shown ) ) :
						?>
						<div class="footer-brand">
							<?php bloginfo( 'name' ); ?>
							<?php
							if ( gnn_option( 'brand_dot' ) ) :
								?>
								<span class="site-title__dot">.</span><?php endif; ?>
						</div>
					<?php endif; ?>

					<?php
					if ( gnn_option( 'footer_show_tagline' ) && '' !== trim( $gnn_ftag ) ) :
						?>
						<div class="footer-tagline"><?php echo wp_kses_post( $gnn_ftag ); ?></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php $gnn_footer_columns = gnn_option( 'footer_show_widgets' ) ? (int) gnn_option( 'footer_columns' ) : 0; ?>
			<?php for ( $i = 1; $i <= $gnn_footer_columns; $i++ ) : ?>
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
		<?php if ( gnn_option( 'footer_show_company' ) ) { ?>
			<?php gnn_footer_company_details(); ?>
		<?php } ?></div><!-- .site-footer__widgets -->

		<?php if ( gnn_option( 'footer_show_copyright' ) || ( gnn_option( 'footer_show_legal' ) && '' !== trim( (string) get_theme_mod( 'gnn_footer_legal', '' ) ) ) ) : ?>
		<div class="site-footer__bottom">
			<?php if ( gnn_option( 'footer_show_copyright' ) ) : ?>
			<span class="site-copyright">
				<?php
				$gnn_copy = trim( (string) gnn_translated_option( 'footer_copyright' ) );
				if ( '' !== $gnn_copy ) {
					// Panel text with %year% / %site% placeholders.
					echo esc_html( str_replace( array( '%year%', '%site%' ), array( gmdate( 'Y' ), get_bloginfo( 'name' ) ), $gnn_copy ) );
				} else {
					/* translators: 1: current year, 2: site name. */
					printf( esc_html__( '© %1$s %2$s. All rights reserved.', 'gnn' ), esc_html( gmdate( 'Y' ) ), esc_html( get_bloginfo( 'name' ) ) );
				}
				?>
			</span>
			<?php endif; ?>
			<?php $gnn_legal = get_theme_mod( 'gnn_footer_legal', '' ); ?>
			<?php if ( gnn_option( 'footer_show_legal' ) && '' !== trim( (string) $gnn_legal ) ) : ?>
				<span class="site-legal"><?php echo esc_html( $gnn_legal ); ?></span>
			<?php endif; ?>
		</div><!-- .site-footer__bottom -->
		<?php endif; ?>
	</footer><!-- #colophon -->
	<?php endif; // End theme footer (Elementor location fallback). ?>
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
