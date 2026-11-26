<?php
/** Header integration checks in the real WordPress test installation. */
require_once ABSPATH . 'wp-admin/includes/template.php';
require_once get_template_directory() . '/inc/admin-panel.php';

$checks = array();
$check  = static function ( $name, $pass ) use ( &$checks ) {
	$checks[] = array( 'name' => $name, 'pass' => (bool) $pass );
};
$saved = get_option( 'gnn_options' );
$mods  = get_theme_mods();
wp_set_current_user( 1 );

$legacy = array( 'header_layout' => 'centered', 'sticky_header' => 0, 'show_search' => 0, 'header_search_bar' => 1 );
$custom = array(
	'header_preset'            => 'transparent',
	'header_structure'         => 'shop',
	'header_shape'             => 'floating',
	'header_surface'           => 'solid',
	'header_overlay'           => 0,
	'header_behavior'          => 'hide',
	'header_search_mode'       => 'overlay',
	'header_search_scope'      => 'products',
	'header_mobile_breakpoint' => 780,
	'header_elements'          => '[ {"id":"cart","desktop":false,"mobile":true},{"id":"search","desktop":true,"mobile":false} ]',
	'header_bg_dark'           => '#123456',
	'logo_dark'                => 'https://example.test/legacy.png',
	'company_phone'            => '+1 555 0100',
);
foreach ( array( 'fresh' => array(), 'legacy' => $legacy, 'custom' => $custom ) as $name => $options ) {
	update_option( 'gnn_options', $options );
	$_POST['gnn_form_scope'] = 'header-unchanged';
	$check( "Header unchanged save preserves the exact $name option array", $options === gnn_options_sanitize( array( 'header_structure' => 'classic', 'header_elements' => '' ) ) );
}
delete_option( 'gnn_options' );
$check( 'Header unchanged save preserves a missing option record', false === gnn_options_sanitize( array( 'header_structure' => '' ) ) );
unset( $_POST['gnn_form_scope'] );

update_option( 'gnn_options', $legacy );
$config = gnn_header_config();
$check( 'Header legacy centered, static and field settings resolve', 'centered' === $config['header_structure'] && 'static' === $config['header_behavior'] && 'field' === $config['header_search_mode'] && $config['enabled']['search'] );
update_option( 'gnn_options', $custom );
$config = gnn_header_config();
$check( 'Header explicit fields override preset values', 'shop' === $config['header_structure'] && $config['two_rows'] && 'floating' === $config['header_shape'] && 'solid' === $config['header_surface'] && 'hide' === $config['header_behavior'] );
$check( 'Header explicit overlay Off overrides Transparent', ! $config['header_overlay'] && ! $config['overlay_inherited'] );
$check( 'Header product search requires WooCommerce', ( class_exists( 'WooCommerce' ) ? 'products' : 'all' ) === $config['header_search_scope'] );
$check( 'Header preserves stored order and device visibility', array( 'cart', 'search' ) === array_column( $config['header_elements'], 'id' ) && ! $config['header_elements'][0]['desktop'] && ! $config['header_elements'][1]['mobile'] );
$check( 'Header contact uses the company phone as a fallback', $custom['company_phone'] === $config['contact']['phone'] );
update_option( 'gnn_options', $custom + array( 'topbar_phone' => '+1 555 0101' ) );
$check( 'Header contact prefers the announcement phone', '+1 555 0101' === gnn_header_contact_details()['phone'] );
update_option( 'gnn_options', $custom );

$page = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Header context test' ) );
update_post_meta( $page, '_gnn_layout', array( 'header_preset' => 'stacked', 'header_surface' => 'glass', 'header_overlay' => '1' ) );

$main_query                  = $GLOBALS['wp_query'];
$GLOBALS['wp_query']          = new WP_Query( array( 'page_id' => $page ) );
$config                       = gnn_header_config();
$GLOBALS['wp_query']          = $main_query;
$check( 'Header page preset and explicit page overrides win', 'stacked' === $config['header_structure'] && 'full' === $config['header_shape'] && 'glass' === $config['header_surface'] && $config['header_overlay'] );

$check( 'Header rejects invalid options before mutation', $custom === gnn_options_sanitize( array( 'header_overlay' => 'yes', 'header_mobile_breakpoint' => 599 ) ) );
$check( 'Header elements reject duplicate IDs and non-boolean device flags', is_wp_error( gnn_parse_header_elements( '[{"id":"cart","desktop":true,"mobile":true},{"id":"cart","desktop":true,"mobile":true}]' ) ) && is_wp_error( gnn_parse_header_elements( '[{"id":"cart","desktop":1,"mobile":true}]' ) ) );
$check( 'Header distinguishes inheritance from an explicitly empty action list', null === gnn_parse_header_elements( '' ) && array() === gnn_parse_header_elements( '[]' ) );

$menus = array();
foreach ( array( 'primary', 'mobile', 'header-left', 'header-right' ) as $slot ) {
	$menus[ $slot ] = wp_create_nav_menu( 'Header QA ' . $slot );
	wp_update_nav_menu_item( $menus[ $slot ], 0, array( 'menu-item-title' => 'QA link ' . $slot, 'menu-item-url' => home_url( '/#' . $slot ), 'menu-item-status' => 'publish' ) );
}
foreach ( array(
	'dedicated menu wins' => array( $menus, array( 'mobile' ) ),
	'primary menu wins'   => array( array_diff_key( $menus, array( 'mobile' => true ) ), array( 'primary' ) ),
	'both split menus'    => array( array_intersect_key( $menus, array( 'header-left' => true, 'header-right' => true ) ), array( 'header-left', 'header-right' ) ),
	'right menu alone'    => array( array( 'header-right' => $menus['header-right'] ), array( 'header-right' ) ),
	'identical split IDs' => array( array( 'header-left' => $menus['header-left'], 'header-right' => $menus['header-left'] ), array( 'header-left' ) ),
) as $name => list( $locations, $expected ) ) {
	set_theme_mod( 'nav_menu_locations', $locations );
	ob_start();
	gnn_header_mobile_menus( '' );
	$html = ob_get_clean();
	preg_match_all( '/QA link (primary|mobile|header-left|header-right)/', $html, $matches );
	$check( "Header mobile fallback: $name", $expected === $matches[1] );
}
set_theme_mod( 'nav_menu_locations', $menus );
$shortcodes = do_shortcode( '[gnn_header_menu][gnn_header_menu]' );
preg_match_all( '/id="(gnn-header-menu-[^"]+)"/', $shortcodes, $matches );
$check( 'Header shortcode renders menus with unique IDs for each instance', 2 === substr_count( $shortcodes, 'QA link primary' ) && count( $matches[1] ) > 2 && count( $matches[1] ) === count( array_unique( $matches[1] ) ) );
$layout = wp_insert_post( array( 'post_type' => 'gnn_layout', 'post_status' => 'publish', 'post_title' => 'Header shared layout', 'post_content' => '[gnn_header_menu]' ) );
update_post_meta( $layout, '_gnn_block_role', 'header' );
update_option( 'gnn_options', $custom + array( 'header_template' => $layout, 'topbar_enable' => 1, 'topbar_text' => 'Header QA announcement' ) );
ob_start();
gnn_render_header();
$shared_header = ob_get_clean();
$check( 'Header shared region keeps the announcement and shortcode navigation', str_contains( $shared_header, 'Header QA announcement' ) && str_contains( $shared_header, 'gnn-layout-region--header' ) && str_contains( $shared_header, 'QA link mobile' ) && ! str_contains( $shared_header, 'id="masthead"' ) );
$check( 'Header design switching succeeds', true === gnn_apply_design_preset( 'architecture' ) );
$check( 'Header design switching resets presentation and preserves identity, menus and page overrides',
	84 === gnn_option( 'header_height' ) && 'classic' === gnn_option( 'header_structure' ) && '' === gnn_option( 'header_bg_dark' ) && '' === gnn_option( 'header_elements' ) &&
	$custom['company_phone'] === gnn_option( 'company_phone' ) && $custom['logo_dark'] === gnn_option( 'logo_dark' ) && $menus === get_nav_menu_locations() &&
	$layout === gnn_option( 'header_template' ) && '1' === get_post_meta( $page, '_gnn_layout', true )['header_overlay'] );

foreach ( $menus as $menu ) {
	wp_delete_nav_menu( $menu );
}
wp_delete_post( $page, true );
wp_delete_post( $layout, true );
update_option( 'theme_mods_' . get_option( 'stylesheet' ), $mods );
if ( false === $saved ) {
	delete_option( 'gnn_options' );
} else {
	update_option( 'gnn_options', $saved );
}
echo 'GNN_QA_RESULT:' . wp_json_encode( $checks );
