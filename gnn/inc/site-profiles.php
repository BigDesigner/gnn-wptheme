<?php
/**
 * Visual identity of the ready-made sites: body classes and assets.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Normalised ready-made site key, or '' for a site without one. */
function gnn_site_profile() {
	return gnn_starter_profile_key( (string) gnn_option( 'site_segment' ) );
}

/**
 * Add the site key so each design can restyle the header and shared parts.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function gnn_site_profile_body_class( $classes ) {
	$profile = gnn_site_profile();
	if ( '' !== $profile ) {
		$classes[] = 'gnn-site-' . $profile;
	}
	return $classes;
}
add_filter( 'body_class', 'gnn_site_profile_body_class' );

/** Identity styles load only for a ready-made site, a page built from one, or a Slider Revolution hero. */
function gnn_site_profile_assets() {
	$post     = is_singular() ? get_queried_object() : null;
	$starter  = $post instanceof WP_Post ? (string) get_post_meta( $post->ID, '_gnn_starter', true ) : '';
	$built    = $post instanceof WP_Post && ( false !== strpos( $starter, ':' ) || 0 === strpos( (string) get_page_template_slug( $post ), 'page-templates/tpl-starter' ) );
	$profile  = gnn_site_profile();
	$profiles = array( $profile );
	if ( $built && false !== strpos( $starter, ':' ) ) {
		$profiles[] = gnn_starter_profile_key( explode( ':', $starter, 2 )[0] );
	}
	$revslider = gnn_revslider_active();
	if ( '' === $profile && ! $built && ! $revslider ) {
		return;
	}
	$uri = get_template_directory_uri() . '/assets/css/profiles/';
	wp_enqueue_style( 'gnn-profile-common', $uri . 'common.css', array( 'gnn-design-system' ), GNN_VERSION );
	foreach ( array_unique( $profiles ) as $key ) {
		if ( 'architecture' === $key ) {
			wp_enqueue_style( 'gnn-profile-' . $key, $uri . $key . '.css', array( 'gnn-profile-common' ), GNN_VERSION );
			continue;
		}
		$demo = function_exists( 'gnn_demo_get' ) ? gnn_demo_get( (string) $key ) : null;
		if ( ! $demo ) {
			continue;
		}
		if ( '' !== $demo['css'] ) {
			wp_enqueue_style( 'gnn-demo-' . $key, get_template_directory_uri() . '/assets/css/' . ltrim( $demo['css'], '/' ), array( 'gnn-profile-common' ), GNN_VERSION );
		}
		if ( '' !== $demo['js'] ) {
			wp_enqueue_script( 'gnn-demo-' . $key, get_template_directory_uri() . '/assets/js/' . ltrim( $demo['js'], '/' ), array(), GNN_VERSION, true );
		}
	}
	if ( $revslider ) {
		wp_enqueue_style( 'gnn-profile-revslider', $uri . 'revslider.css', array( 'gnn-profile-common' ), GNN_VERSION );
	}
}
add_action( 'wp_enqueue_scripts', 'gnn_site_profile_assets', 17 );

/**
 * Resolve retired bundled image URLs in saved Elementor documents at render time.
 * A completed old demo may still have its copy in the Media Library.
 *
 * @param string                  $html   Rendered widget markup.
 * @param Elementor\Widget_Base $widget Elementor widget.
 * @return string
 */
function gnn_legacy_starter_image( $html, $widget ) {
	if ( 'image' !== $widget->get_name() ) {
		return $html;
	}
	$image  = $widget->get_settings_for_display( 'image' );
	$url    = $image['url'] ?? '';
	$prefix = get_template_directory_uri() . '/assets/images/demo/';
	if ( ! is_string( $url ) || 0 !== strpos( $url, $prefix ) || ! preg_match( '~^([a-z]+)/([a-z0-9-]+)\.jpg$~D', substr( $url, strlen( $prefix ) ), $asset )
		|| ! gnn_is_legacy_site_design( $asset[1] ) || is_readable( get_template_directory() . '/assets/images/demo/' . $asset[0] ) ) {
		return $html;
	}
	static $resolved = array();
	if ( ! isset( $resolved[ $url ] ) ) {
		$ids = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key,WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Bounded lookup for a retired asset, cached for the request.
				'meta_key'       => '_gnn_demo',
				'meta_value'     => $asset[1] . ':image-' . $asset[2],
			)
		);
		$attachment = $ids ? wp_get_attachment_url( $ids[0] ) : false;
		$resolved[ $url ] = $attachment ?: get_theme_file_uri( 'assets/img/placeholder-cover.svg' );
	}
	return str_replace( esc_url( $url ), esc_url( $resolved[ $url ] ), $html );
}
add_filter( 'elementor/widget/render_content', 'gnn_legacy_starter_image', 10, 2 );
