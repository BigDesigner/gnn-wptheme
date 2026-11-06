<?php
/**
 * Preserve native dependency order for critical optimized integrations.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve critical script roots and their complete registered dependencies.
 *
 * @return string[] Native handles which must stay under WordPress control.
 */
function gnn_critical_script_handles() {
	$scripts = wp_scripts();
	$roots   = array( 'jquery', 'jquery-core', 'jquery-migrate' );
	foreach ( $scripts->registered as $handle => $script ) {
		$source = is_string( $script->src ) ? $script->src : '';
		if ( 0 === strpos( $handle, 'elementor-' ) || false !== strpos( $source, '/elementor/' ) || false !== strpos( $source, '/elementor-pro/' ) ) {
			$roots[] = $handle;
		}
	}
	$seen  = array();
	$visit = function ( $handle ) use ( &$visit, &$seen, $scripts ) {
		if ( isset( $seen[ $handle ] ) ) {
			return;
		}
		$seen[ $handle ] = true;
		if ( isset( $scripts->registered[ $handle ] ) ) {
			foreach ( $scripts->registered[ $handle ]->deps as $dependency ) {
				$visit( $dependency );
			}
		}
	};
	foreach ( $roots as $handle ) {
		$visit( $handle );
	}
	return array_keys( $seen );
}

/**
 * Return critical resources to WP rather than a deferred/combined HB group.
 *
 * Hummingbird's collection filter preserves the original handle, dependency
 * order and inline-before/after association. Its defer filter alone cannot
 * reverse a defer flag already stored on an existing optimization group.
 *
 * @param bool   $excluded Existing exclusion decision.
 * @param string $handle   Native resource handle.
 * @param string $source   Resource URL.
 * @param string $type     scripts or styles.
 * @return bool
 */
function gnn_hummingbird_critical_resource( $excluded, $handle, $source, $type ) {
	if ( $excluded || ! gnn_option( 'compatibility_mode' ) ) {
		return $excluded;
	}
	// Consent CSS must not disappear into a cached or delayed stylesheet group.
	if ( 0 === strpos( $handle, 'faz-cookie-' ) || false !== strpos( (string) $source, '/faz-cookie-manager/' ) ) {
		return true;
	}
	return 'scripts' === $type && in_array( $handle, gnn_critical_script_handles(), true );
}
add_filter( 'wphb_dont_add_handle_to_collection', 'gnn_hummingbird_critical_resource', 100, 4 );

/** Invalidate generated files once after upgrading, preserving HB settings. */
function gnn_refresh_optimized_assets() {
	if ( ! current_user_can( 'manage_options' ) || ! gnn_option( 'compatibility_mode' ) || GNN_VERSION === get_option( 'gnn_optimized_assets_version' ) ) {
		return;
	}
	$utilities = 'Hummingbird\\Core\\Utils';
	if ( ! class_exists( $utilities ) ) {
		return;
	}
	$minify = $utilities::get_module( 'minify' );
	if ( ! is_object( $minify ) || ! is_callable( array( $minify, 'clear_cache' ) ) ) {
		return;
	}
	// Preserve all optimization settings; discard generated files and stale queues.
	$minify->clear_cache( false, false, true );
	do_action( 'wphb_clear_page_cache' );
	update_option( 'gnn_optimized_assets_version', GNN_VERSION, false );
}
add_action( 'admin_init', 'gnn_refresh_optimized_assets', 30 );
