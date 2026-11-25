<?php
/**
 * Visual identity of the ready-made sites: body classes, assets and the shop header search field.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Normalised ready-made site key, or '' for a site without one. */
function gnn_site_profile() {
	$key = gnn_starter_profile_key( (string) gnn_option( 'site_segment' ) );
	if ( in_array( $key, array( 'pro', 'commerce', 'design', 'architecture' ), true ) ) {
		return $key;
	}
	return function_exists( 'gnn_demo_get' ) && null !== gnn_demo_get( $key ) ? $key : '';
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
		if ( in_array( $key, array( 'pro', 'commerce', 'design', 'architecture' ), true ) ) {
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

/** Product search field next to the logo when the header option is on. */
function gnn_header_search_bar() {
	if ( ! gnn_option( 'header_search_bar' ) ) {
		return;
	}
	$product = class_exists( 'WooCommerce' );
	?>
	<form class="gnn-header-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label class="screen-reader-text" for="gnn-header-search-field"><?php esc_html_e( 'Search', 'gnn' ); ?></label>
		<input id="gnn-header-search-field" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search', 'gnn' ); ?>">
		<?php if ( $product ) : ?>
			<input type="hidden" name="post_type" value="product">
		<?php endif; ?>
		<button type="submit" aria-label="<?php esc_attr_e( 'Search', 'gnn' ); ?>"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></button>
	</form>
	<?php
}
