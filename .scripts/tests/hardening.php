<?php
/** Follow-up assertions inside a real, disposable WordPress runtime. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$checks = array();
wp_set_current_user( 1 );
require_once ABSPATH . 'wp-admin/includes/template.php';
require_once get_template_directory() . '/inc/admin-panel.php';
$record = function ( $name, $condition ) use ( &$checks ) { $checks[] = array( 'name' => $name, 'pass' => (bool) $condition ); };
$encode = function ( $options = array(), $mods = array() ) { return wp_json_encode( array( '_gnn_export' => '1.5.1', 'options' => (object) $options, 'theme_mods' => (object) $mods ) ); };

gnn_reset_panel_settings();
$record( 'Fresh-site export can be imported', true === gnn_import_panel_settings( wp_json_encode( gnn_settings_export_payload() ) ) );
$original = gnn_options_sanitize( array_merge( gnn_option_defaults(), array( 'topbar_enable' => 1, 'footer_legal' => 'İletişim ve Gizlilik', 'accent' => '#123abc', 'default_mode' => 'light', 'maintenance_message' => "Birinci satır\nİkinci satır", 'custom_js_head' => "window.example = 'İstanbul';\n// preserved" ) ) );
update_option( 'gnn_options', $original );
$export = wp_json_encode( gnn_settings_export_payload() );
gnn_reset_panel_settings();
$record( 'Complete export/import succeeds', true === gnn_import_panel_settings( $export ) );
$record( 'Complete option round trip preserves values', $original === get_option( 'gnn_options' ) );
$record( 'Shared brand round trip', '#123abc' === get_theme_mod( 'gnn_accent_color' ) && 'light' === get_theme_mod( 'gnn_default_theme' ) && 'İletişim ve Gizlilik' === get_theme_mod( 'gnn_footer_legal' ) );
$record( 'Partial import accepted', true === gnn_import_panel_settings( $encode( array( 'excerpt_length' => 42 ) ) ) );
$record( 'Absent checkbox remains on in partial import', 1 === gnn_option( 'topbar_enable' ) && 42 === gnn_option( 'excerpt_length' ) );
$snapshot = array( get_option( 'gnn_options' ), get_theme_mods(), get_option( 'site_icon' ) );
$bad = array(
 'broken JSON' => '{', 'array root' => '[]', 'missing marker' => '{}',
 'nested field' => $encode( array( 'logo_light' => array( 'id' => 1 ) ) ),
 'object field' => $encode( array( 'footer_copyright' => (object) array( 'x' => 1 ) ) ),
 'null field' => $encode( array( 'footer_copyright' => null ) ),
 'unknown option' => $encode( array( 'administrator' => 1 ) ),
 'unknown mod' => $encode( array(), array( 'unfiltered_html' => '1' ) ),
 'invalid color' => $encode( array( 'excerpt_length' => 60 ), array( 'gnn_accent_color' => 'red; background:url(javascript:1)' ) ),
 'invalid mode' => $encode( array( 'excerpt_length' => 60 ), array( 'gnn_default_theme' => 'unexpected' ) ),
 'invalid number' => $encode( array( 'logo_light' => 'not-an-id' ) ),
 'unsafe legacy logo' => $encode( array(), array( 'logo_light' => 'javascript:alert(1)' ) ),
 'oversize document' => str_repeat( ' ', 1048577 ),
 'deep JSON' => '{"_gnn_export":"1.5.1","options":' . str_repeat( '[', 20 ) . '1' . str_repeat( ']', 20 ) . '}',
);
foreach ( $bad as $name => $raw ) {
 $record( 'Reject ' . $name . ' without any state change', is_wp_error( gnn_import_panel_settings( $raw ) ) && $snapshot === array( get_option( 'gnn_options' ), get_theme_mods(), get_option( 'site_icon' ) ) );
}
$record( 'Malformed settings form returns current values', $snapshot[0] === gnn_options_sanitize( array( 'accent' => array( '#abcdef' ) ) ) );
$subscriber = wp_create_user( 'gnn-qa-reader', 'gnn-qa-local-only', 'reader@example.invalid' );
wp_set_current_user( $subscriber );
$record( 'Subscriber import denied and data retained', is_wp_error( gnn_import_panel_settings( $encode( array( 'excerpt_length' => 88 ) ) ) ) && $snapshot[0] === get_option( 'gnn_options' ) );
wp_set_current_user( 1 );
add_filter( 'map_meta_cap', $deny_unfiltered = function ( $caps, $cap ) { return 'unfiltered_html' === $cap ? array( 'do_not_allow' ) : $caps; }, 10, 2 );
$record( 'Restricted admin cannot import a changed custom-code field', is_wp_error( gnn_import_panel_settings( $encode( array( 'excerpt_length' => 24, 'custom_js_head' => 'alert(1)' ) ) ) ) );
$record( 'Restricted admin cannot replace custom code', $original['custom_js_head'] === gnn_option( 'custom_js_head' ) );
$record( 'Restricted admin may import ordinary settings', true === gnn_import_panel_settings( $encode( array( 'excerpt_length' => 24 ) ) ) );
remove_filter( 'map_meta_cap', $deny_unfiltered, 10 );
update_option( 'gnn_options', array() );
set_theme_mod( 'logo_light', 'https://example.invalid/legacy-logo.png' );
$record( 'Malformed form preserves sparse storage and legacy fallback', array() === gnn_options_sanitize( array( 'accent' => array( '#abcdef' ) ) ) && 'https://example.invalid/legacy-logo.png' === gnn_option( 'logo_light' ) );
$record( 'Legacy logo URL still renders', false !== strpos( gnn_logo_img( gnn_option( 'logo_light' ), 0, 'legacy' ), 'https://example.invalid/legacy-logo.png' ) );
$record( 'One logo uses a shared mode class', false !== strpos( gnn_logo_pair( gnn_option( 'logo_light' ), 0, 0, 0 ), 'gnn-logo--shared' ) );
$legacy_export = wp_json_encode( gnn_settings_export_payload() );
remove_theme_mod( 'logo_light' );
$record( 'Legacy logo survives export/import', true === gnn_import_panel_settings( $legacy_export ) && 'https://example.invalid/legacy-logo.png' === gnn_option( 'logo_light' ) );
ob_start(); gnn_field_media( 'logo_light', 'Logo', gnn_option( 'logo_light' ) ); $field = ob_get_clean();
$record( 'Legacy URL remains in the actual panel media field', false !== strpos( $field, 'value="https://example.invalid/legacy-logo.png"' ) );
$saved = gnn_options_sanitize( array( 'logo_light' => gnn_option( 'logo_light' ) ) ); update_option( 'gnn_options', $saved );
$record( 'Saving the panel preserves a legacy URL', 'https://example.invalid/legacy-logo.png' === gnn_option( 'logo_light' ) );
$record( 'Legacy URL stored in panel options can round trip', true === gnn_import_panel_settings( wp_json_encode( gnn_settings_export_payload() ) ) && 'https://example.invalid/legacy-logo.png' === gnn_option( 'logo_light' ) );
update_option( 'gnn_options', array( 'logo_light' => 0 ) );
$record( 'Explicit panel removal overrides legacy logo', 0 === gnn_option( 'logo_light' ) );
set_theme_mod( 'logo_light', 123 );
update_option( 'gnn_options', array() );
$record( 'Legacy attachment ID remains supported', 123 === gnn_option( 'logo_light' ) );
ob_start(); gnn_the_logo(); $fallback = ob_get_clean();
$record( 'Missing logo attachment falls back to site title', false !== strpos( $fallback, 'class="site-title"' ) );
gnn_reset_panel_settings();
$record( 'Explicit reset clears legacy logo too', 0 === gnn_option( 'logo_light' ) );
$menu = wp_create_nav_menu( 'QA Main Navigation' );
$_POST = array( 'update-nav-menu-nonce' => wp_create_nonce( 'update-nav_menu' ) );
$_REQUEST['update-nav-menu-nonce'] = $_POST['update-nav-menu-nonce'];
$item = wp_update_nav_menu_item( $menu, 0, array( 'menu-item-title' => 'Home', 'menu-item-url' => home_url( '/' ), 'menu-item-status' => 'publish' ) );
$_POST['menu-item-gnn-badge'] = array( $item => '<b>Yeni</b>' );
wp_update_nav_menu_item( $menu, $item, array( 'menu-item-title' => 'Home', 'menu-item-url' => home_url( '/' ), 'menu-item-status' => 'publish' ) );
$record( 'Real menu hook saves badge to item ID', 'Yeni' === get_post_meta( $item, '_gnn_menu_badge', true ) );
set_theme_mod( 'nav_menu_locations', array( 'primary' => $menu ) );
$_POST = array();
update_option( 'gnn_options', array_merge( gnn_option_defaults(), array( 'loading_screen' => 1, 'scroll_anim' => 1, 'google_material_icons' => 0, 'mobile_dock' => 1 ) ) );
wp_set_password( 'gnn-qa-local-only', 1 );
echo 'GNN_QA_RESULT:' . wp_json_encode( $checks );
