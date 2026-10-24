<?php
/**
 * Validate a complete settings document before writing any of its values.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build the document used by the download handler.
 *
 * @return array Export payload with effective shared defaults.
 */
function gnn_settings_export_payload() {
	$mods = array(
		'gnn_accent_color'  => get_theme_mod( 'gnn_accent_color', '#34d399' ),
		'gnn_default_theme' => get_theme_mod( 'gnn_default_theme', 'dark' ),
		'gnn_footer_legal'  => get_theme_mod( 'gnn_footer_legal', '' ),
	);
	foreach ( array( 'logo_light', 'logo_light_2x', 'logo_dark', 'logo_dark_2x' ) as $key ) {
		$mods[ $key ] = get_theme_mod( $key, 0 );
	}
	return array(
		'_gnn_export' => GNN_VERSION,
		'options'     => (object) array_intersect_key( (array) get_option( 'gnn_options', array() ), gnn_option_defaults() ),
		'theme_mods'  => (object) $mods,
	);
}

/**
 * Decode an export, preserving object/array distinctions and bounding resources.
 *
 * @param string $raw JSON file contents.
 * @return array|WP_Error Validated options and theme mods, or an error.
 */
function gnn_decode_settings_import( $raw ) {
	$error = new WP_Error( 'gnn_import_invalid', __( 'Import failed: the file is not a valid GNN settings export.', 'gnn' ) );
	if ( ! is_string( $raw ) || strlen( $raw ) > 1048576 ) {
		return $error;
	}
	$data = json_decode( $raw, false, 16 );
	if ( JSON_ERROR_NONE !== json_last_error() || ! is_object( $data )
		|| ! isset( $data->_gnn_export ) || ! is_string( $data->_gnn_export )
		|| ! preg_match( '/^\d+\.\d+\.\d+$/D', $data->_gnn_export )
		|| ( ! isset( $data->options ) && ! isset( $data->theme_mods ) ) ) {
		return $error;
	}
	$result   = array(
		'options'    => array(),
		'theme_mods' => array(),
	);
	$defaults = gnn_option_defaults();
	foreach ( array( 'options', 'theme_mods' ) as $group ) {
		if ( ! property_exists( $data, $group ) ) {
			continue;
		}
		if ( ! is_object( $data->$group ) ) {
			// Older exports encoded an empty PHP array as [].
			if ( array() === $data->$group ) {
				continue;
			}
			return $error;
		}
		foreach ( get_object_vars( $data->$group ) as $key => $value ) {
			if ( ! is_scalar( $value ) ) {
				return $error;
			}
			if ( 'options' === $group ) {
				if ( ! array_key_exists( $key, $defaults ) ) {
					return $error;
				}
				if ( is_int( $defaults[ $key ] ) && ! is_bool( $value )
					&& ! gnn_is_legacy_logo_url( $key, $value )
					&& ! ( is_int( $value ) && $value >= 0 )
					&& ! ( is_string( $value ) && ctype_digit( $value ) ) ) {
					return $error;
				}
				if ( is_string( $defaults[ $key ] ) && ! is_string( $value ) ) {
					return $error;
				}
			} elseif ( ! in_array( $key, gnn_exportable_theme_mods(), true ) ) {
				return $error;
			} elseif ( 0 === strpos( $key, 'logo_' ) ) {
				if ( ! ( is_int( $value ) && $value >= 0 )
					&& ! ( is_string( $value ) && ctype_digit( $value ) )
					&& ! ( is_string( $value ) && preg_match( '#^https?://#i', $value ) && esc_url_raw( $value ) === $value ) ) {
					return $error;
				}
			} elseif ( 'gnn_accent_color' === $key ) {
				if ( ! is_string( $value ) || ! sanitize_hex_color( $value ) ) {
					return $error;
				}
			} elseif ( 'gnn_default_theme' === $key && ! in_array( $value, array( 'light', 'dark' ), true ) ) {
				return $error;
			} elseif ( ! is_string( $value ) ) {
				return $error;
			}
			$result[ $group ][ $key ] = $value;
		}
	}
	return $result;
}

/**
 * Apply a validated import, preserving values absent from a partial export.
 *
 * @param string $raw JSON file contents.
 * @return true|WP_Error Success or validation/authorization error.
 */
function gnn_import_panel_settings( $raw ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return new WP_Error( 'gnn_import_permission', __( 'Permission denied.', 'gnn' ) );
	}
	$data = gnn_decode_settings_import( $raw );
	if ( is_wp_error( $data ) ) {
		return $data;
	}
	if ( ! current_user_can( 'unfiltered_html' ) ) {
		foreach ( array( 'custom_js_head', 'custom_js_footer', 'head_html', 'body_html' ) as $key ) {
			if ( isset( $data['options'][ $key ] ) && gnn_option( $key ) !== $data['options'][ $key ] ) {
				return new WP_Error( 'gnn_import_code_permission', __( 'Custom script fields were not saved: your account lacks the unfiltered_html capability.', 'gnn' ) );
			}
		}
	}
	if ( $data['options'] ) {
		$current = wp_parse_args( (array) get_option( 'gnn_options', array() ), gnn_option_defaults() );
		update_option( 'gnn_options', gnn_options_sanitize( array_replace( $current, $data['options'] ) ) );
	}
	foreach ( $data['theme_mods'] as $key => $value ) {
		set_theme_mod( $key, 'gnn_footer_legal' === $key ? sanitize_text_field( $value ) : $value );
	}
	return true;
}
