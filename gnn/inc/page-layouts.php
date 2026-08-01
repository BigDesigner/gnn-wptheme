<?php
/**
 * Page layout system.
 *
 * One shared renderer drives every page template so markup stays identical
 * across layouts — only the wrapper class (and whether a sidebar loads)
 * changes. Templates in page-templates/ are thin callers:
 *
 *   Default (page.php) ....... full width, no sidebar   (gnn_render_page 'full')
 *   Boxed .................... centered column, max 1200 (…'boxed')
 *   Right Sidebar ........... content + sidebar right    (…'right')
 *   Left Sidebar ............ content + sidebar left     (…'left')
 *   Blank Canvas ............ no header/footer           (its own file)
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render a standard page in the given layout.
 *
 * @param string $layout One of: full, boxed, right, left.
 */
function gnn_render_page( $layout = 'full' ) {
	$layout = in_array( $layout, array( 'full', 'boxed', 'right', 'left' ), true ) ? $layout : 'full';

	get_header();

	// A sidebar only appears for the sidebar layouts, and never on the
	// WooCommerce utility pages (cart/checkout/account) which bring their own.
	$with_sidebar = in_array( $layout, array( 'right', 'left' ), true )
		&& gnn_show_sidebar()
		&& ! gnn_is_wc_utility_page();

	$classes = array( 'gnn-container', 'gnn-page', 'gnn-page--' . $layout );
	if ( $with_sidebar ) {
		$classes[] = 'has-sidebar';
		if ( 'left' === $layout ) {
			$classes[] = 'sidebar-left';
		}
	}
	?>
	<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">

		<main id="primary" class="site-main content-area">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', 'page' );

				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
			endwhile;
			?>
		</main><!-- #primary -->

		<?php
		if ( $with_sidebar ) {
			get_sidebar();
		}
		?>

	</div><!-- .gnn-page -->
	<?php
	get_footer();
}

/**
 * Show the page-template labels in the site language (the dropdown otherwise
 * shows the raw English "Template Name" headers).
 *
 * @param string[] $templates Map of template file => label.
 * @return string[]
 */
function gnn_translate_page_templates( $templates ) {
	$labels = array(
		'page-templates/tpl-boxed.php'         => __( 'Boxed (max 1200px)', 'gnn' ),
		'page-templates/tpl-right-sidebar.php' => __( 'Right Sidebar', 'gnn' ),
		'page-templates/tpl-left-sidebar.php'  => __( 'Left Sidebar', 'gnn' ),
		'page-templates/tpl-blank.php'         => __( 'Blank Canvas (no header/footer)', 'gnn' ),
		'page-templates/page-contact.php'      => __( 'Contact', 'gnn' ),
	);
	foreach ( $labels as $file => $label ) {
		if ( isset( $templates[ $file ] ) ) {
			$templates[ $file ] = $label;
		}
	}
	return $templates;
}
add_filter( 'theme_page_templates', 'gnn_translate_page_templates' );
