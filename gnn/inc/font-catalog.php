<?php
/**
 * Admin-only Google Fonts catalogue, with a bundled offline fallback.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Reduce upstream metadata to bounded family names and upright weights.
 *
 * @param mixed $families Upstream family metadata.
 * @return array
 */
function gnn_font_catalog_normalize( $families ) {
	if ( ! is_array( $families ) || count( $families ) > 5000 ) {
		return array();
	}
	$catalog = array();
	foreach ( $families as $family ) {
		if ( ! is_array( $family ) || ! is_string( $family['family'] ?? null ) || ! preg_match( '/^[A-Za-z0-9 ]{1,60}$/D', $family['family'] ) || ! is_array( $family['fonts'] ?? null ) ) {
			continue;
		}
		$weights = array();
		foreach ( array_keys( $family['fonts'] ) as $weight ) {
			if ( in_array( (string) $weight, gnn_typography_weights(), true ) ) {
				$weights[] = (string) $weight;
			}
		}
		if ( $weights ) {
			$catalog[ $family['family'] ] = array_values( array_unique( $weights ) );
		}
	}
	uksort( $catalog, 'strnatcasecmp' );
	return $catalog;
}

/** Serve only public font metadata to an authenticated theme administrator. */
function gnn_font_catalog_ajax() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied.', 'gnn' ) ), 403 );
	}
	check_ajax_referer( 'gnn_font_catalog', 'nonce' );
	$catalog = get_transient( 'gnn_font_catalog_v1' );
	$source  = 'cache';
	if ( ! is_array( $catalog ) || ! $catalog ) {
		$catalog = array();
		// A short retry lock prevents repeated remote requests when offline.
		if ( ! get_transient( 'gnn_font_catalog_retry' ) ) {
			set_transient( 'gnn_font_catalog_retry', 1, 15 * MINUTE_IN_SECONDS );
			$response = wp_safe_remote_get(
				'https://fonts.google.com/metadata/fonts',
				array(
					'timeout'             => 8,
					'redirection'         => 0,
					'limit_response_size' => 8 * MB_IN_BYTES,
				)
			);
			if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
				$body    = preg_replace( '/^\)\]\}\x27\s*/', '', wp_remote_retrieve_body( $response ) );
				$data    = json_decode( $body, true, 32 );
				$catalog = gnn_font_catalog_normalize( $data['familyMetadataList'] ?? null );
				if ( $catalog ) {
					set_transient( 'gnn_font_catalog_v1', $catalog, WEEK_IN_SECONDS );
					$source = 'google';
				}
			}
		}
		if ( ! $catalog ) {
			$source  = 'bundled';
			$catalog = json_decode( (string) file_get_contents( get_template_directory() . '/assets/data/google-fonts.json' ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- bundled public metadata, never a remote or user-supplied path.
		}
	}
	wp_send_json_success(
		array(
			'families' => $catalog,
			'source'   => $source,
		)
	);
}
add_action( 'wp_ajax_gnn_font_catalog', 'gnn_font_catalog_ajax' );
