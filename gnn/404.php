<?php
/**
 * The template for displaying 404 pages (not found).
 * Title, text, search box, button label and image are panel-driven.
 *
 * @package GNN
 */

get_header();

$gnn_title  = trim( (string) gnn_option( 'error404_title' ) );
$gnn_text   = trim( (string) gnn_option( 'error404_text' ) );
$gnn_button = trim( (string) gnn_option( 'error404_button' ) );
$gnn_image  = (int) gnn_option( 'error404_image' );

$gnn_title  = '' !== $gnn_title ? $gnn_title : __( 'Page not found', 'gnn' );
$gnn_text   = '' !== $gnn_text ? $gnn_text : __( 'The page you’re looking for was moved, renamed or never existed.', 'gnn' );
$gnn_button = '' !== $gnn_button ? $gnn_button : __( 'Back to Home', 'gnn' );
?>

<main id="primary" class="site-main error-404-main">
	<div class="error-404__inner">
		<?php if ( $gnn_image ) : ?>
			<div class="error-404__image">
				<?php echo wp_get_attachment_image( $gnn_image, 'gnn-wide', false, array( 'alt' => '' ) ); ?>
			</div>
		<?php else : ?>
			<div class="error-404__code" aria-hidden="true">404</div>
		<?php endif; ?>

		<h1 class="error-404__title"><?php echo esc_html( $gnn_title ); ?></h1>
		<p class="error-404__text"><?php echo esc_html( $gnn_text ); ?></p>

		<?php if ( gnn_option( 'error404_search' ) ) : ?>
			<div class="error-404__search"><?php get_search_form(); ?></div>
		<?php endif; ?>

		<a class="gnn-btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( $gnn_button ); ?></a>
	</div>
</main><!-- #primary -->

<?php
get_footer();
