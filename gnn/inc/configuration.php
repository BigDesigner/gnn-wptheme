<?php
/**
 * Revisioned configuration, recovery snapshots and delegated capabilities.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Return configuration owned by this theme; unrelated theme mods stay outside recovery. */
function gnn_configuration_state() {
	$mods = array();
	foreach ( (array) get_theme_mods() as $key => $value ) {
		if ( 0 === strpos( $key, 'gnn_' ) || in_array( $key, array( 'logo_light', 'logo_dark', 'logo_light_2x', 'logo_dark_2x' ), true ) ) {
			$mods[ $key ] = $value;
		}
	}
	ksort( $mods );
	return array(
		'options'   => (array) get_option( 'gnn_options', array() ),
		'mods'      => $mods,
		'site_icon' => (int) get_option( 'site_icon', 0 ),
	);
}

/** Opaque revision token, also protects same-request shared brand changes. */
function gnn_configuration_revision() {
	return hash_hmac( 'sha256', wp_json_encode( gnn_configuration_state() ), wp_salt( 'auth' ) );
}

/**
 * Start one request-scoped write transaction with a database-backed mutex.
 *
 * @param string $action Operation identifier.
 * @param string $revision Expected configuration revision.
 */
function gnn_configuration_begin( $action, $revision = '' ) {
	static $owned = false;
	if ( $owned ) {
		return true;
	}
	$token = wp_generate_uuid4();
	$lock  = array(
		'token' => $token,
		'time'  => time(),
	);
	if ( ! add_option( 'gnn_configuration_lock', $lock, '', false ) ) {
		return new WP_Error( 'gnn_busy', __( 'Another settings operation is in progress. Reload and try again.', 'gnn' ) );
	}
	register_shutdown_function(
		function () use ( $token ) {
			$lock = get_option( 'gnn_configuration_lock' );
			if ( is_array( $lock ) && isset( $lock['token'] ) && hash_equals( $token, $lock['token'] ) ) {
				delete_option( 'gnn_configuration_lock' );
			}
		}
	);
	if ( '' !== $revision && ! hash_equals( gnn_configuration_revision(), $revision ) ) {
		delete_option( 'gnn_configuration_lock' );
		return new WP_Error( 'gnn_conflict', __( 'Settings changed after this form was opened. Your changes were not applied. Reload the latest settings before trying again.', 'gnn' ) );
	}
	$owned = true;
	$state = gnn_configuration_state();
	$items = (array) get_option( 'gnn_configuration_history', array() );
	array_unshift(
		$items,
		array(
			'id'       => wp_generate_uuid4(),
			'at'       => gmdate( 'c' ),
			'user'     => get_current_user_id(),
			'action'   => sanitize_key( $action ),
			'revision' => gnn_configuration_revision(),
			'state'    => $state,
		)
	);
	update_option( 'gnn_configuration_history', array_slice( $items, 0, 10 ), false );
	return true;
}

/**
 * Safe metadata log: never record configuration values or executable content.
 *
 * @param array $old_value Previous stored options.
 * @param array $value New stored options.
 */
function gnn_configuration_event( $old_value, $value ) {
	if ( $old_value === $value ) {
		return;
	}
	$keys = array();
	foreach ( array_unique( array_merge( array_keys( (array) $old_value ), array_keys( (array) $value ) ) ) as $key ) {
		if ( ( $old_value[ $key ] ?? null ) !== ( $value[ $key ] ?? null ) ) {
			$keys[] = sanitize_key( $key );
		}
	}
	$log = (array) get_option( 'gnn_configuration_events', array() );
	array_unshift(
		$log,
		array(
			'at'       => gmdate( 'c' ),
			'user'     => get_current_user_id(),
			'keys'     => $keys,
			'revision' => gnn_configuration_revision(),
		)
	);
	update_option( 'gnn_configuration_events', array_slice( $log, 0, 100 ), false );
}
add_action( 'update_option_gnn_options', 'gnn_configuration_event', 10, 2 );

/**
 * Include shared branding in the masked history, without unrelated theme mods.
 *
 * @param array $old Previous theme modifications.
 * @param array $value New theme modifications.
 */
function gnn_brand_configuration_event( $old, $value ) {
	$states = array();
	foreach ( array( (array) $old, (array) $value ) as $mods ) {
		$state = array();
		foreach ( $mods as $key => $item ) {
			if ( 0 === strpos( $key, 'gnn_' ) || in_array( $key, array( 'logo_light', 'logo_dark', 'logo_light_2x', 'logo_dark_2x' ), true ) ) {
				$state[ 'brand_' . $key ] = $item;
			}
		}
		$states[] = $state;
	}
	gnn_configuration_event( $states[0], $states[1] );
}
add_action( 'update_option_theme_mods_' . get_stylesheet(), 'gnn_brand_configuration_event', 10, 2 );

/**
 * Track site icon changes with the same value-free event contract.
 *
 * @param int $old Previous attachment ID.
 * @param int $value New attachment ID.
 */
function gnn_icon_configuration_event( $old, $value ) {
	gnn_configuration_event( array( 'site_icon' => $old ), array( 'site_icon' => $value ) );
}
add_action( 'update_option_site_icon', 'gnn_icon_configuration_event', 10, 2 );

/**
 * Initial writes use add_option rather than update_option hooks.
 *
 * @param string $option Added option name.
 * @param mixed  $value Stored value.
 */
function gnn_initial_configuration_event( $option, $value ) {
	if ( 'gnn_options' === $option ) {
		gnn_configuration_event( array(), $value );
	} elseif ( 'site_icon' === $option ) {
		gnn_icon_configuration_event( 0, $value );
	} elseif ( 'theme_mods_' . get_stylesheet() === $option ) {
		gnn_brand_configuration_event( array(), $value );
	}
}
add_action( 'added_option', 'gnn_initial_configuration_event', 10, 2 );

/**
 * Restore a local snapshot; raw-code recovery is restricted to trusted operators.
 *
 * @param int|string $id Record identifier.
 * @param string     $revision Expected configuration revision.
 */
function gnn_restore_configuration( $id, $revision ) {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'unfiltered_html' ) ) {
		return new WP_Error( 'gnn_permission', __( 'You are not allowed to restore configuration snapshots.', 'gnn' ) );
	}
	foreach ( (array) get_option( 'gnn_configuration_history', array() ) as $item ) {
		if ( isset( $item['id'], $item['state'] ) && hash_equals( $item['id'], $id ) ) {
			$started = gnn_configuration_begin( 'restore', $revision );
			if ( is_wp_error( $started ) ) {
				return $started;
			}
			foreach ( gnn_configuration_state()['mods'] as $key => $value ) {
				remove_theme_mod( $key );
			}
			foreach ( $item['state']['mods'] as $key => $value ) {
				set_theme_mod( $key, $value );
			}
			update_option( 'gnn_options', $item['state']['options'] );
			update_option( 'site_icon', $item['state']['site_icon'] );
			return true;
		}
	}
	return new WP_Error( 'gnn_missing_snapshot', __( 'The requested snapshot is no longer available.', 'gnn' ) );
}

/** The current form revision is outside the option payload. */
function gnn_configuration_form_revision() {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- the Settings API or action handler checks its own nonce.
	return isset( $_POST['gnn_revision'] ) && is_string( $_POST['gnn_revision'] ) ? sanitize_text_field( wp_unslash( $_POST['gnn_revision'] ) ) : '';
}

/** Render a revision token for a mutable form. */
function gnn_configuration_revision_field() {
	echo '<input type="hidden" name="gnn_revision" value="' . esc_attr( gnn_configuration_revision() ) . '">';
}

/** Register optional agency roles without assigning them to existing users. */
function gnn_register_agency_roles() {
	$editor = get_role( 'editor' );
	if ( ! $editor ) {
		return;
	}
	foreach ( array(
		'gnn_client'   => array( 'GNN Client', 'gnn_manage_brand' ),
		'gnn_designer' => array( 'GNN Designer', 'gnn_manage_design' ),
	) as $name => $profile ) {
		if ( ! get_role( $name ) ) {
			$caps                    = $editor->capabilities;
			$caps['unfiltered_html'] = false;
			$caps[ $profile[1] ]     = true;
			add_role( $name, $profile[0], $caps );
		}
	}
	if ( ! get_role( 'gnn_support' ) ) {
		add_role(
			'gnn_support',
			'GNN Support',
			array(
				'read'                 => true,
				'gnn_view_diagnostics' => true,
			)
		);
	}
}
add_action( 'admin_init', 'gnn_register_agency_roles' );
