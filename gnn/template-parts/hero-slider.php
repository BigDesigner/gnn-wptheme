<?php
/**
 * Front-page hero slider. Slides are managed in WP Admin → GNN Slider
 * (unlimited slides, CPT-backed — see inc/slider.php).
 *
 * @package GNN
 */

$gnn_slides = gnn_get_slides();

if ( empty( $gnn_slides ) ) {
	return;
}

$gnn_shop_url    = class_exists( 'WooCommerce' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/' );
$gnn_full_height = (bool) gnn_option( 'slider_full_height' );
?>

<section class="gnn-hero<?php echo $gnn_full_height ? ' gnn-hero--full-height' : ''; ?>" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Highlights', 'gnn' ); ?>">
	<div class="gnn-hero__track">
		<?php foreach ( $gnn_slides as $gnn_index => $gnn_slide ) : ?>
			<div class="gnn-hero__slide<?php echo 0 === $gnn_index ? ' is-active' : ''; ?>" role="group" aria-roledescription="slide">
				<?php if ( ! empty( $gnn_slide['image'] ) ) : ?>
					<div class="gnn-hero__bg gnn-hero__bg--fit-<?php echo esc_attr( $gnn_slide['fit'] ); ?>">
						<?php
						// First slide is the LCP candidate: eager + high priority.
						$gnn_attr = 0 === $gnn_index
							? array(
								'loading'       => 'eager',
								'fetchpriority' => 'high',
							)
							: array( 'loading' => 'lazy' );
						// Add a 2x descriptor when a retina image is set.
						if ( ! empty( $gnn_slide['image_2x'] ) ) {
							$gnn_1x = wp_get_attachment_image_url( (int) $gnn_slide['image'], 'full' );
							$gnn_2x = wp_get_attachment_image_url( (int) $gnn_slide['image_2x'], 'full' );
							if ( $gnn_1x && $gnn_2x ) {
								$gnn_attr['srcset'] = esc_url( $gnn_1x ) . ' 1x, ' . esc_url( $gnn_2x ) . ' 2x';
							}
						}
						echo wp_get_attachment_image( (int) $gnn_slide['image'], 'full', false, $gnn_attr );
						?>
					</div>
				<?php else : ?>
					<div class="gnn-hero__bg gnn-hero__bg--empty" aria-hidden="true"></div>
				<?php endif; ?>
				<div class="gnn-hero__content">
					<?php if ( $gnn_slide['kicker'] ) : ?>
						<div class="entry-kicker"><?php echo esc_html( $gnn_slide['kicker'] ); ?></div>
					<?php endif; ?>
					<h1 class="gnn-hero__title"><?php echo esc_html( $gnn_slide['title'] ); ?></h1>
					<?php if ( $gnn_slide['text'] ) : ?>
						<p class="gnn-hero__text"><?php echo esc_html( $gnn_slide['text'] ); ?></p>
					<?php endif; ?>
					<?php if ( $gnn_slide['cta_label'] ) : ?>
						<div class="gnn-hero__actions">
							<a class="gnn-btn" href="<?php echo esc_url( $gnn_slide['cta_url'] ? $gnn_slide['cta_url'] : $gnn_shop_url ); ?>"><?php echo esc_html( $gnn_slide['cta_label'] ); ?></a>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<?php if ( count( $gnn_slides ) > 1 ) : ?>
		<button class="gnn-hero__arrow gnn-hero__arrow--prev" aria-label="<?php esc_attr_e( 'Previous slide', 'gnn' ); ?>">&lsaquo;</button>
		<button class="gnn-hero__arrow gnn-hero__arrow--next" aria-label="<?php esc_attr_e( 'Next slide', 'gnn' ); ?>">&rsaquo;</button>
		<div class="gnn-hero__dots" role="tablist">
			<?php foreach ( $gnn_slides as $gnn_index => $gnn_slide ) : ?>
				<button class="gnn-hero__dot<?php echo 0 === $gnn_index ? ' is-active' : ''; ?>" role="tab"
					aria-label="<?php echo esc_attr( sprintf( /* translators: %d: slide number. */ __( 'Go to slide %d', 'gnn' ), $gnn_index + 1 ) ); ?>"></button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>
