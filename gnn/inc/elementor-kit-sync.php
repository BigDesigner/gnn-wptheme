<?php
/**
 * Opt-in, one-way Elementor global styles for panel saves.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Defer until the Settings API has stored options and shared theme mods.
 *
 * @param array $value Sanitized panel settings.
 * @return array
 */
function gnn_kit_sync_queue( $value ) {
	// The Settings API validates this same nonce before updating the option.
	if ( ! is_admin() || ! current_user_can( 'manage_options' )
		|| 'gnn_options_group' !== sanitize_key( wp_unslash( $_POST['option_page'] ?? '' ) )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ?? '' ) ), 'gnn_options_group-options' ) ) {
		return $value;
	}
	foreach ( $GLOBALS['wp_settings_errors'] ?? array() as $error ) {
		if ( 'error' === $error['type'] ) {
			return $value;
		}
	}
	if ( ! isset( $GLOBALS['gnn_kit_sync_pending'] ) ) {
		$previous                            = get_option( 'gnn_options', array() );
		$GLOBALS['gnn_kit_sync_was_enabled'] = ! empty( $previous['elementor_kit_sync'] );
	}
	$GLOBALS['gnn_kit_sync_pending'] = $value;
	if ( ! has_action( 'shutdown', 'gnn_kit_sync_flush' ) ) {
		add_action( 'shutdown', 'gnn_kit_sync_flush' );
	}
	return $value;
}
add_filter( 'sanitize_option_gnn_options', 'gnn_kit_sync_queue', 100 );

/** Apply only a successfully persisted panel save; never log integration failures. */
function gnn_kit_sync_flush() {
	if ( ! isset( $GLOBALS['gnn_kit_sync_pending'] ) ) {
		return;
	}
	$options     = $GLOBALS['gnn_kit_sync_pending'];
	$was_enabled = ! empty( $GLOBALS['gnn_kit_sync_was_enabled'] );
	unset( $GLOBALS['gnn_kit_sync_pending'] );
	unset( $GLOBALS['gnn_kit_sync_was_enabled'] );
	if ( get_option( 'gnn_options' ) !== $options || ! class_exists( '\Elementor\Plugin' ) ) {
		return;
	}
	try {
		$manager = \Elementor\Plugin::$instance->kits_manager ?? null;
		if ( ! $manager ) {
			return;
		}
		$kit = $manager->get_active_kit();
		if ( ! $kit || ! $kit->get_id() ) {
			return;
		}
		$id      = $kit->get_id();
		$backups = get_option( 'gnn_kit_sync_backup', array() );
		$enabled = ! empty( $options['elementor_kit_sync'] );
		$keys    = array( 'system_colors', 'custom_colors', 'system_typography' );
		$all     = $kit->get_settings();
		if ( ! $enabled ) {
			if ( $was_enabled && isset( $backups[ $id ] ) ) {
				$kit->save( array( 'settings' => array_replace( $all, $backups[ $id ] ) ) );
			}
			return;
		}
		$settings = array_intersect_key( $all, array_flip( $keys ) );
		foreach ( $keys as $key ) {
			$settings[ $key ] = $settings[ $key ] ?? array();
		}
		if ( ! isset( $backups[ $id ] ) ) {
			$backups[ $id ] = $settings;
			update_option( 'gnn_kit_sync_backup', $backups, false );
			if ( get_option( 'gnn_kit_sync_backup' ) !== $backups ) {
				return;
			}
		}
		$mode     = 'light' === get_theme_mod( 'gnn_default_theme', 'dark' ) ? 'light' : 'dark';
		$defaults = 'light' === $mode
			? array(
				'fg'   => '#101013',
				'fg2'  => '#5c5c64',
				'bg2'  => '#ffffff',
				'line' => '#e5e5e9',
			)
			: array(
				'fg'   => '#f5f5f4',
				'fg2'  => '#9d9da4',
				'bg2'  => '#121214',
				'line' => '#232328',
			);
		$colors   = array();
		foreach ( $defaults as $token => $fallback ) {
			$color            = sanitize_hex_color( $options[ 'color_' . $token . '_' . $mode ] ?? '' );
			$colors[ $token ] = $color ? $color : $fallback;
		}
		$system = array(
			'primary'   => gnn_brand_accent( $mode ),
			'accent'    => gnn_brand_accent( $mode ),
			'text'      => $colors['fg'],
			'secondary' => $colors['fg2'],
		);
		foreach ( $settings['system_colors'] as &$row ) {
			if ( isset( $system[ $row['_id'] ] ) ) {
				$row['color'] = $system[ $row['_id'] ];
			}
		}
		unset( $row );
		foreach ( array(
			'bg2'  => __( 'Background', 'gnn' ),
			'line' => __( 'Border', 'gnn' ),
		) as $token => $title ) {
			$found = false;
			foreach ( $settings['custom_colors'] as &$row ) {
				if ( 'gnn_' . $token === $row['_id'] ) {
					$row['color'] = $colors[ $token ];
					$found        = true;
				}
			}
			unset( $row );
			if ( ! $found ) {
				$settings['custom_colors'][] = array(
					'_id'   => 'gnn_' . $token,
					'title' => $title,
					'color' => $colors[ $token ],
				);
			}
		}
		$fonts = array(
			'manrope' => 'Manrope',
			'space'   => 'Space Grotesk',
			'system'  => 'system-ui',
			'custom'  => 'GNN Local',
		);
		foreach ( $settings['system_typography'] as &$row ) {
			$role                          = 'text' === $row['_id'] || 'accent' === $row['_id'] ? 'body_font' : 'heading_font';
			$row['typography_typography']  = 'custom';
			$row['typography_font_family'] = $fonts[ $options[ $role ] ?? '' ] ?? 'system-ui';
		}
		unset( $row );
		$kit->save( array( 'settings' => array_replace( $all, $settings ) ) );
	} catch ( \Throwable $error ) {
		// Elementor availability or save errors must not interrupt panel saves.
		return;
	}
}
