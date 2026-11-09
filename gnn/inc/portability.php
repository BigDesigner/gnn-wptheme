<?php
/**
 * Separate design profiles from private site configuration and remap references.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

/** Stable identifier for same-site recovery; never expose the site's URL. */
function gnn_site_fingerprint() {
	return hash( 'sha256', untrailingslashit( home_url() ) ); }

/** IDs and identity-bearing values never belong in a reusable design profile. */
function gnn_site_specific_keys() {
	$keys = array( 'logo_light', 'logo_light_2x', 'logo_dark', 'logo_dark_2x', 'footer_logo_light', 'footer_logo_light_2x', 'footer_logo_dark', 'footer_logo_dark_2x', 'error404_image', 'contact_page_id', 'local_font', 'header_template', 'footer_template', 'post_header_template', 'post_footer_template', 'archive_header_template', 'archive_footer_template', 'hero_group', 'ga4_id', 'gtm_id', 'analytics_delivery', 'custom_css', 'custom_js_head', 'custom_js_footer', 'head_html', 'body_html', 'topbar_text', 'topbar_phone', 'topbar_email', 'footer_tagline', 'footer_copyright', 'company_name', 'company_phone', 'company_email', 'company_address', 'company_hours', 'company_social_url', 'header_cta_label', 'header_cta_url', 'mobile_actions', 'maintenance_message', 'maintenance_mode', 'error404_text', 'error404_title', 'error404_button' );
	foreach ( gnn_site_schema() as $key => $field ) {
		if ( 'region' === $field[1] ) {
			$keys[] = $key;
		}
	}
	return $keys;
}

/**
 * Describe references without downloading or trusting remote assets.
 *
 * @param array $options Stored options.
 */
function gnn_reference_manifest( $options ) {
	$refs = array();
	foreach ( $options as $key => $id ) {
		$is_media = in_array( $key, array( 'logo_light', 'logo_light_2x', 'logo_dark', 'logo_dark_2x', 'footer_logo_light', 'footer_logo_light_2x', 'footer_logo_dark', 'footer_logo_dark_2x', 'error404_image', 'local_font' ), true );
		$is_post  = 'contact_page_id' === $key || false !== strpos( $key, '_template' );
		if ( ! $is_media && ! $is_post ) {
			continue; }
		if ( ! is_numeric( $id ) || ! $id ) {
			continue; }
		$post = get_post( $id );
		if ( ! $post ) {
			continue; }
		$refs[ $key ] = array(
			'source_id' => (int) $id,
			'type'      => $post->post_type,
			'path'      => $is_media ? get_post_meta( $id, '_wp_attached_file', true ) : get_page_uri( $id ),
		);
		if ( $is_media ) {
			$file                   = get_attached_file( $id );
			$refs[ $key ]['sha256'] = $file && is_readable( $file ) ? hash_file( 'sha256', $file ) : '';
		}
	}
	return $refs;
}

/**
 * Safe profile is the UI default; full exports are explicitly labeled private.
 *
 * @param array  $payload Export document.
 * @param string $scope Export scope or CSS selector.
 */
function gnn_prepare_export_payload( $payload, $scope ) {
	$payload['schema_version'] = 2;
	$payload['scope']          = 'design' === $scope ? 'design' : 'site';
	$payload['source_site']    = gnn_site_fingerprint();
	$options                   = (array) $payload['options'];
	if ( 'design' === $payload['scope'] ) {
		$options               = array_diff_key( $options, array_flip( gnn_site_specific_keys() ) );
		$payload['theme_mods'] = (object) array_intersect_key( (array) $payload['theme_mods'], array_flip( array( 'gnn_accent_color', 'gnn_default_theme' ) ) );
	} else {
		$payload['references'] = (object) gnn_reference_manifest( $options );
	}
	$payload['options'] = (object) $options;
	return $payload;
}

/**
 * Reject schema changes we cannot interpret and never silently reuse foreign IDs.
 *
 * @param array  $result Validated settings.
 * @param object $document Decoded import document.
 */
function gnn_resolve_import_references( $result, $document ) {
	if ( isset( $document->schema_version ) && 2 !== $document->schema_version ) {
		return new WP_Error( 'gnn_schema', __( 'This settings schema is not supported. No changes were made.', 'gnn' ) ); }
	if ( isset( $document->scope ) && ! in_array( $document->scope, array( 'site', 'design' ), true ) ) {
		return new WP_Error( 'gnn_scope', __( 'The export scope is invalid.', 'gnn' ) ); }
	if ( isset( $document->scope ) && 'design' === $document->scope ) {
		if ( array_intersect_key( $result['options'], array_flip( gnn_site_specific_keys() ) ) || array_diff( array_keys( $result['theme_mods'] ), array( 'gnn_accent_color', 'gnn_default_theme' ) ) ) {
			return new WP_Error( 'gnn_profile_identity', __( 'A design profile must not include site identity, tracking, code or media references.', 'gnn' ) ); }
	}
	$same_site = isset( $document->source_site ) && is_string( $document->source_site ) && hash_equals( gnn_site_fingerprint(), $document->source_site );
	if ( $same_site ) {
		return $result; }
	$ref_keys = array( 'contact_page_id', 'local_font', 'header_template', 'footer_template', 'post_header_template', 'post_footer_template', 'archive_header_template', 'archive_footer_template', 'hero_group', 'logo_light', 'logo_light_2x', 'logo_dark', 'logo_dark_2x', 'footer_logo_light', 'footer_logo_light_2x', 'footer_logo_dark', 'footer_logo_dark_2x', 'error404_image' );
	foreach ( gnn_site_schema() as $key => $field ) {
		if ( 'region' === $field[1] ) {
			$ref_keys[] = $key; }
	}
	foreach ( array( 'options', 'theme_mods' ) as $group ) {
		foreach ( array_intersect_key( $result[ $group ], array_flip( $ref_keys ) ) as $key => $id ) {
			if ( is_numeric( $id ) && (int) $id <= 0 ) {
				continue; }
			$mapped = isset( $document->reference_map->$key ) && is_scalar( $document->reference_map->$key ) && ctype_digit( (string) $document->reference_map->$key ) ? absint( $document->reference_map->$key ) : 0;
			$ref    = $document->references->$key ?? null;
			if ( ! $mapped && is_object( $ref ) && isset( $ref->path, $ref->type ) && is_string( $ref->path ) && is_string( $ref->type ) ) {
				if ( in_array( $ref->type, array( 'page', 'gnn_layout' ), true ) ) {
					$post   = get_page_by_path( $ref->path, OBJECT, $ref->type );
					$mapped = $post ? $post->ID : 0; } elseif ( 'attachment' === $ref->type && ! empty( $ref->sha256 ) && is_string( $ref->sha256 ) ) {
					$posts = get_posts(
						array(
							'post_type'   => 'attachment',
							'post_status' => 'inherit',
							'numberposts' => 20,
							// phpcs:ignore WordPress.DB.SlowDBQuery -- bounded explicit configuration/import query, not an unbounded visitor search.
							'meta_key'    => '_wp_attached_file',
							// phpcs:ignore WordPress.DB.SlowDBQuery -- bounded explicit configuration/import query, not an unbounded visitor search.
							'meta_value'  => $ref->path,
						)
					); // phpcs:ignore WordPress.DB.SlowDBQuery -- bounded, explicit import preview only.
					foreach ( $posts as $post ) {
						$file = get_attached_file( $post->ID );
						if ( $file && is_readable( $file ) && hash_equals( $ref->sha256, hash_file( 'sha256', $file ) ) ) {
							$mapped = $post->ID;
							break; }
					}
					}
			}
			if ( ! $mapped ) {
				/* translators: %s: field name; %d: action position. */
				return new WP_Error( 'gnn_reference', sprintf( __( 'Map the reference for %s on this site before importing. No settings were changed.', 'gnn' ), $key ) ); }
			if ( 'hero_group' === $key ) {
				if ( ! term_exists( $mapped, 'gnn_slide_group' ) ) {
					return new WP_Error( 'gnn_reference', __( 'The selected slider group does not exist.', 'gnn' ) ); }
			} else {
				$type = 'contact_page_id' === $key ? 'page' : ( false !== strpos( $key, '_template' ) ? 'gnn_layout' : 'attachment' );
				if ( get_post_type( $mapped ) !== $type ) {
					return new WP_Error( 'gnn_reference', __( 'The selected reference has the wrong content type.', 'gnn' ) ); }
				if ( 'local_font' === $key && 'font/woff2' !== get_post_mime_type( $mapped ) ) {
					return new WP_Error( 'gnn_reference', __( 'The selected reference is not a WOFF2 font.', 'gnn' ) ); }
			}
			$result[ $group ][ $key ] = $mapped;
		}
	}
	return $result;
}
