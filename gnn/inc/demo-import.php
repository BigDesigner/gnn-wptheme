<?php
/**
 * Demo import for real WordPress installs.
 *
 * Two supported paths:
 *
 * 1. One Click Demo Import plugin (free, wordpress.org):
 *    Appearance → Import Demo Data → "GNN Demo". Content, front page,
 *    menu locations and Customizer demo values are applied automatically.
 *
 * 2. Manual: Tools → Import → WordPress (demo/gnn-demo-content.xml),
 *    then either follow the checklist in the XML header or run:
 *        wp eval 'gnn_apply_demo_setup();'
 *
 * All demo copy lives in demo/customizer.json and the WXR file —
 * the theme's templates render nothing hardcoded.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'gnn_apply_demo_setup' ) ) :
	/**
	 * Post-import wiring: front page, posts page, menu locations,
	 * Customizer demo values, permalinks.
	 */
	function gnn_apply_demo_setup() {
		// Static front page + posts page.
		$home = get_page_by_path( 'home' );
		$blog = get_page_by_path( 'blog' );
		if ( $home && $blog ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $home->ID );
			update_option( 'page_for_posts', $blog->ID );
		}

		// Menu locations (menus themselves come from the WXR).
		$locations = array();
		foreach ( array(
			'primary'  => 'Primary',
			'footer-1' => 'Footer Products',
			'footer-2' => 'Footer Company',
		) as $gnn_location => $gnn_menu_name ) {
			$gnn_menu = wp_get_nav_menu_object( $gnn_menu_name );
			if ( $gnn_menu ) {
				$locations[ $gnn_location ] = $gnn_menu->term_id;
			}
		}
		if ( $locations ) {
			set_theme_mod( 'nav_menu_locations', $locations );
		}

		// Customizer demo values (footer legal) from demo data.
		$gnn_file = get_parent_theme_file_path( 'demo/customizer.json' );
		if ( is_readable( $gnn_file ) ) {
			// phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown, WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme file, not a remote URL.
			$gnn_data = json_decode( (string) file_get_contents( $gnn_file ), true );
			if ( is_array( $gnn_data ) ) {
				foreach ( $gnn_data as $gnn_mod => $gnn_value ) {
					if ( is_string( $gnn_value ) ) {
						set_theme_mod( sanitize_key( $gnn_mod ), sanitize_text_field( $gnn_value ) );
					}
				}
			}
		}

		// Demo hero slides (gnn_slide CPT — see inc/slider.php).
		if ( function_exists( 'gnn_get_slides' ) && empty( gnn_get_slides() ) ) {
			$gnn_demo_slides = array(
				array(
					'kicker'    => 'Cybersecurity',
					'title'     => 'Defense that never sleeps.',
					'text'      => '24/7 managed detection and response, powered by a global threat-intelligence network.',
					'cta_label' => 'Explore Products',
					'cta_url'   => '/shop/',
				),
				array(
					'kicker'    => 'Hardware',
					'title'     => 'Infrastructure, engineered.',
					'text'      => 'Servers and appliances built for zero-downtime enterprise workloads.',
					'cta_label' => 'View Hardware',
					'cta_url'   => '/product-category/hardware/',
				),
				array(
					'kicker'    => 'Software',
					'title'     => 'One platform. Total visibility.',
					'text'      => 'Correlate billions of events a day into signals your team can act on.',
					'cta_label' => 'See the Platform',
					'cta_url'   => '/shop/',
				),
			);
			foreach ( $gnn_demo_slides as $gnn_order => $gnn_row ) {
				$gnn_slide_id = wp_insert_post(
					array(
						'post_type'   => 'gnn_slide',
						'post_title'  => $gnn_row['title'],
						'post_status' => 'publish',
						'menu_order'  => $gnn_order,
					)
				);
				if ( $gnn_slide_id && ! is_wp_error( $gnn_slide_id ) ) {
					update_post_meta( $gnn_slide_id, '_gnn_slide_kicker', $gnn_row['kicker'] );
					update_post_meta( $gnn_slide_id, '_gnn_slide_text', $gnn_row['text'] );
					update_post_meta( $gnn_slide_id, '_gnn_slide_cta_label', $gnn_row['cta_label'] );
					update_post_meta( $gnn_slide_id, '_gnn_slide_cta_url', $gnn_row['cta_url'] );
					update_post_meta( $gnn_slide_id, '_gnn_slide_fit', 'cover' );
				}
			}
		}

		flush_rewrite_rules();
	}
endif;

/* --- One Click Demo Import integration --------------------------------- */

add_filter(
	'ocdi/import_files',
	function () {
		return array(
			array(
				'import_file_name'  => __( 'GNN Demo', 'gnn' ),
				'local_import_file' => get_parent_theme_file_path( 'demo/gnn-demo-content.xml' ),
				'import_notice'     => __( 'Install and activate WooCommerce first so the demo products import correctly.', 'gnn' ),
			),
		);
	}
);

add_action( 'ocdi/after_import', 'gnn_apply_demo_setup' );
add_filter( 'ocdi/disable_pt_branding', '__return_true' );
