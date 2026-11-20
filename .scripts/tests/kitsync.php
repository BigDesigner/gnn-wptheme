<?php
/** Real Elementor Free Kit integration, through the panel Settings API path. */
require_once ABSPATH . 'wp-admin/includes/template.php';
$checks = array();
$check = function ( $name, $pass ) use ( &$checks ) {
	$checks[] = array( 'name' => $name, 'pass' => (bool) $pass, 'detail' => '' );
};
if ( ! class_exists( '\Elementor\Plugin' ) ) {
	$check( 'KS01 Elementor absent leaves theme operational', ! function_exists( 'gnn_kit_sync_flush' ) );
	echo 'GNN_QA_RESULT:' . wp_json_encode( $checks );
	return;
}
$options = get_option( 'gnn_options', array() );
$backup = get_option( 'gnn_kit_sync_backup', false );
$mods = get_theme_mods();
$post = $_POST;
$user = get_current_user_id();
$kit = \Elementor\Plugin::$instance->kits_manager->get_active_kit();
$original = $kit->get_settings();
$read = function () use ( $kit ) {
	return get_post_meta( $kit->get_id(), '_elementor_page_settings', true );
};
$save = function ( $changes ) use ( $options ) {
	update_option( 'gnn_options', array_merge( $options, $changes ) );
	gnn_kit_sync_flush();
};
try {
	wp_set_current_user( 1 );
	gnn_panel_settings();
	$_POST['option_page'] = 'gnn_options_group';
	$_POST['_wpnonce'] = wp_create_nonce( 'gnn_options_group-options' );
	delete_option( 'gnn_kit_sync_backup' );
	$kit->save( array( 'settings' => $original ) );
	$before = $read();
	$save( array( 'elementor_kit_sync' => 0, 'color_fg_light' => '#123456' ) );
	$check( 'KS02 disabled panel save leaves Kit unchanged', $before === $read() );
	set_theme_mod( 'gnn_default_theme', 'light' );
	$save( array( 'elementor_kit_sync' => 1, 'accent_light' => '#abcdef', 'color_fg_light' => '#123456', 'heading_font' => 'manrope', 'body_font' => 'space' ) );
	$after = $read();
	$colors = array_column( $after['system_colors'], 'color', '_id' );
	$fonts = array_column( $after['system_typography'], 'typography_font_family', '_id' );
	$check( 'KS03 enabled panel save updates real Kit colors and local font names', '#abcdef' === $colors['primary'] && '#123456' === $colors['text'] && 'Manrope' === $fonts['primary'] && 'Space Grotesk' === $fonts['text'] );
	$first_backup = get_option( 'gnn_kit_sync_backup' );
	$save( array( 'elementor_kit_sync' => 1, 'color_fg_light' => '#654321' ) );
	$check( 'KS04 original backup is not overwritten', $first_backup === get_option( 'gnn_kit_sync_backup' ) );
	$save( array( 'elementor_kit_sync' => 0 ) );
	$restored = $read();
	$check( 'KS05 disabling restores original colors and typography', $before['system_colors'] === $restored['system_colors'] && $before['system_typography'] === $restored['system_typography'] && ( $before['custom_colors'] ?? array() ) === ( $restored['custom_colors'] ?? array() ) );
	$save( array( 'elementor_kit_sync' => 0, 'color_fg_dark' => '#998877' ) );
	$check( 'KS08 later disabled saves leave restored Kit untouched', $restored === $read() );
	unset( $_POST['option_page'] );
	$save( array( 'elementor_kit_sync' => 1, 'accent_light' => '#112233' ) );
	$check( 'KS06 internal option writes do not sync Kit', $restored === $read() );
	$manager = \Elementor\Plugin::$instance->kits_manager;
	try {
		\Elementor\Plugin::$instance->kits_manager = null;
		$GLOBALS['gnn_kit_sync_pending'] = get_option( 'gnn_options' );
		gnn_kit_sync_flush();
		$check( 'KS07 unavailable Elementor manager safely skips sync', $restored === $read() );
	} finally {
		\Elementor\Plugin::$instance->kits_manager = $manager;
	}
} finally {
	$_POST = array();
	update_option( 'gnn_options', $options );
	$kit->save( array( 'settings' => $original ) );
	if ( false === $backup ) { delete_option( 'gnn_kit_sync_backup' ); } else { update_option( 'gnn_kit_sync_backup', $backup ); }
	update_option( 'theme_mods_' . get_option( 'stylesheet' ), $mods );
	$_POST = $post;
	wp_set_current_user( $user );
}
echo 'GNN_QA_RESULT:' . wp_json_encode( $checks );
