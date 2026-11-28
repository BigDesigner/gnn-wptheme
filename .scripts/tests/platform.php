<?php
/** Real WordPress verification of the 2.0 platform. Local QA only. */
require_once ABSPATH . 'wp-admin/includes/template.php';
require_once get_template_directory() . '/inc/admin-panel.php';
require_once get_template_directory() . '/inc/workspace.php';
wp_set_current_user( 1 );
$checks = array();
$check = function ( $name, $pass, $detail = '' ) use ( &$checks ) { $checks[] = array( 'name' => $name, 'pass' => (bool) $pass, 'detail' => $detail ); };
$old = gnn_configuration_state();
$revision = gnn_configuration_revision();
$options = get_option( 'gnn_options', array() ); $options['company_phone'] = 'newer-write'; update_option( 'gnn_options', $options );
$check( 'P01 stale configuration revision rejected before mutation', is_wp_error( gnn_configuration_begin( 'stale-test', $revision ) ) );
$check( 'P02 stale mutation preserves newer value', 'newer-write' === gnn_option( 'company_phone' ) );
$check( 'P03 unknown structured action rejected', is_wp_error( gnn_sanitize_actions( '[{"type":"script","label":"Unsafe"}]' ) ) );
$check( 'P04 invalid analytics ID rejected', is_wp_error( gnn_validate_platform_input( array( 'ga4_id' => 'broken-id' ) ) ) );
$check( 'P05 invalid platform choice rejected', is_wp_error( gnn_validate_platform_input( array( 'header_preset' => 'unknown' ) ) ) );
$check( 'P06 unbounded dimension rejected before writes', is_wp_error( gnn_validate_platform_input( array( 'header_height' => 9000 ) ) ) );
$options = array_merge( gnn_option_defaults(), array( 'company_name' => 'PRIVATE CUSTOMER', 'ga4_id' => 'G-PRIVATE123', 'custom_js_head' => 'window.privateSecret="PRIVATE-CODE";', 'logo_light' => 221 ) );
update_option( 'gnn_options', $options );
$profile = gnn_settings_export_payload( 'design' );
$encoded = wp_json_encode( $profile );
$check( 'P07 portable profile excludes identity and code values', false === strpos( $encoded, 'PRIVATE' ) );
$check( 'P08 portable profile excludes media and page IDs', ! isset( $profile['options']->logo_light ) && ! isset( $profile['options']->contact_page_id ) && ! isset( $profile['options']->header_template ) );
$check( 'P09 portable profile accepts foreign site fingerprint', ! is_wp_error( gnn_decode_settings_import( $encoded ) ) );
$a = wp_insert_attachment( array( 'post_title' => 'Target image', 'post_mime_type' => 'image/png', 'post_status' => 'inherit' ) );
$b = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Target page' ) );
$doc = array( '_gnn_export' => '2.0.0', 'schema_version' => 2, 'scope' => 'site', 'source_site' => 'foreign', 'options' => array( 'logo_light' => $a ) );
$check( 'P10 foreign numeric ID is never silently reused', is_wp_error( gnn_decode_settings_import( wp_json_encode( $doc ) ) ) );
$doc['reference_map'] = array( 'logo_light' => $a );
$mapped = gnn_decode_settings_import( wp_json_encode( $doc ) );
$check( 'P11 explicit media mapping is applied', ! is_wp_error( $mapped ) && $a === $mapped['options']['logo_light'] );
$doc['reference_map']['logo_light'] = $b;
$check( 'P12 wrong target content type rejected', is_wp_error( gnn_decode_settings_import( wp_json_encode( $doc ) ) ) );
$doc['schema_version'] = 999;
$check( 'P13 unsupported schema rejected', is_wp_error( gnn_decode_settings_import( wp_json_encode( $doc ) ) ) );
$before = gnn_configuration_state();
$check( 'P14 recovery snapshot can be created', true === gnn_configuration_begin( 'test', gnn_configuration_revision() ) );
$history = get_option( 'gnn_configuration_history' ); $snapshot = $history[0]['id'];
$options['company_phone'] = 'CHANGED'; update_option( 'gnn_options', $options );
$check( 'P15 exact local state can be recovered', true === gnn_restore_configuration( $snapshot, gnn_configuration_revision() ) && $before === gnn_configuration_state() );
$check( 'P16 change event log excludes raw secret content', false === strpos( wp_json_encode( get_option( 'gnn_configuration_events' ) ), 'PRIVATE-CODE' ) );
gnn_register_agency_roles();
$client = wp_create_user( 'platform-client', 'gnn-qa-local-only', 'platform-client@example.invalid' ); ( new WP_User( $client ) )->set_role( 'gnn_client' );
wp_set_current_user( $client );
$check( 'P17 client receives only delegated brand management', current_user_can( 'gnn_manage_brand' ) && ! current_user_can( 'manage_options' ) && ! current_user_can( 'unfiltered_html' ) );
$check( 'P18 client field allowlist excludes code and tracking', ! in_array( 'custom_js_head', gnn_delegated_keys(), true ) && ! in_array( 'ga4_id', gnn_delegated_keys(), true ) );
$check( 'P19 client cannot restore private configuration', is_wp_error( gnn_restore_configuration( $snapshot, '' ) ) );
$designer = wp_create_user( 'platform-designer', 'gnn-qa-local-only', 'platform-designer@example.invalid' ); ( new WP_User( $designer ) )->set_role( 'gnn_designer' );
wp_set_current_user( $designer );
$check( 'P20 designer can edit layouts without raw script capability', current_user_can( 'edit_gnn_layouts' ) && ! current_user_can( 'unfiltered_html' ) && ! current_user_can( 'manage_options' ) );
wp_set_current_user( 1 );
$identity = gnn_option( 'company_name' ); $private = gnn_option( 'custom_js_head' );
$check( 'P21 GNNpro preset can be applied', true === gnn_apply_design_preset( 'gnnpro' ) );
$check( 'P22 design preset preserves customer identity and code', $identity === gnn_option( 'company_name' ) && $private === gnn_option( 'custom_js_head' ) );
$check( 'P23 design contract emits configured tokens', false !== strpos( gnn_design_css(), '--gnn-radius:3px' ) && false !== strpos( gnn_header_css(), '--gnn-header-height:' ) );
$front = get_option( 'page_on_front' ); $menus = get_theme_mod( 'nav_menu_locations' );
$first = gnn_install_starter( 'gnnpro' );
$check( 'P24 draft starter installation succeeds', is_array( $first ) && 5 === count( $first ) );
$check( 'P25 starter preserves live homepage and menu assignments', $front === get_option( 'page_on_front' ) && $menus === get_theme_mod( 'nav_menu_locations' ) );
$id = $first['about']; wp_update_post( array( 'ID' => $id, 'post_content' => 'CUSTOMER EDIT' ) );
$second = gnn_install_starter( 'gnnpro' );
$check( 'P26 repeat starter preserves IDs and user edits', $second === $first && 'CUSTOMER EDIT' === get_post_field( 'post_content', $id ) );
$check( 'P27 starter drafts are not auto-published', 'draft' === get_post_status( $first['contact'] ) || empty( get_post_meta( $first['contact'], '_gnn_starter', true ) ) );
$region = wp_insert_post( array( 'post_type' => 'gnn_layout', 'post_status' => 'publish', 'post_title' => 'Reusable header', 'post_content' => '<!-- wp:paragraph --><p>NATIVE REGION</p><!-- /wp:paragraph -->' ) );
$options = get_option( 'gnn_options' ); $options['header_template'] = $region; update_option( 'gnn_options', $options );
ob_start(); $rendered = gnn_render_layout_region( 'header' ); $html = ob_get_clean();
$check( 'P28 native reusable header renders without Pro', $rendered && false !== strpos( $html, 'NATIVE REGION' ) );
wp_update_post( array( 'ID' => $region, 'post_status' => 'draft' ) );
ob_start(); $rendered = gnn_render_layout_region( 'header' ); ob_end_clean();
$check( 'P29 missing/unpublished region falls back safely', false === $rendered );
$summary = wp_json_encode( gnn_support_summary() );
$check( 'P30 diagnostics excludes private customer code and name', false === strpos( $summary, 'PRIVATE' ) );
foreach ( gnn_starter_profiles() as $slug => $profile ) {
 $brand=gnn_color_luminance($profile['accent']);$ink=$brand>0.179?0:1;
 $ratios=array((max($brand,$ink)+0.05)/(min($brand,$ink)+0.05));
 foreach(array('#fafafa','#0a0a0b') as $surface){$link=gnn_color_luminance(gnn_readable_brand_color($profile['accent'],$surface));$bg=gnn_color_luminance($surface);$ratios[]=(max($link,$bg)+0.05)/(min($link,$bg)+0.05);}
 $check('Brand contrast: '.$slug.' primary ink and light/dark links',min($ratios)>=4.5,wp_json_encode($ratios));
}
$options = (array) get_option( 'gnn_options' );
$choices = gnn_platform_schema()['site_segment'][4];
$expected = array( 'architecture', 'general', 'gnnagency', 'gnndefault', 'gnnonepage', 'gnnpro' );
$actual = array_keys( $choices );
sort( $actual );
$check( 'Site design offers the five retained designs and None', $expected === $actual && __( 'None', 'gnn' ) === $choices['general'] );

$original_get = $_GET;
$_GET['path'] = 'ready';
ob_start();
gnn_setup_wizard_content();
$ready_html = ob_get_clean();
$_GET = $original_get;
$ready = new DOMDocument();
$ready->loadHTML( $ready_html, LIBXML_NOERROR | LIBXML_NOWARNING );
$xpath = new DOMXPath( $ready );
$previews = array();
foreach ( $xpath->query( '//article[contains(@class,"gnn-profile-card")]' ) as $card ) {
	$button = $xpath->query( './/button[@data-profile]', $card )->item( 0 );
	$image = $xpath->query( './/img[@src]', $card )->item( 0 );
	if ( $button && $image ) {
		$uri = get_template_directory_uri() . '/';
		$src = $image->getAttribute( 'src' );
		$previews[ $button->getAttribute( 'data-profile' ) ] = 0 === strpos( $src, $uri )
			&& is_readable( get_template_directory() . '/' . substr( $src, strlen( $uri ) ) );
	}
}
ksort( $previews );
$check(
	'Ready-made sites lists five designs with bundled previews',
	array_values( array_diff( $expected, array( 'general' ) ) ) === array_keys( $previews )
		&& array( true, true, true, true, true ) === array_values( $previews )
);
$legacy = array(
	'', 'general', 'professional', 'original', 'industrial', 'creative', 'pro', 'commerce', 'design',
	'agency', 'clinic', 'education', 'event', 'fitness', 'hotel', 'legal', 'magazine',
	'nonprofit', 'personal', 'photography', 'realestate', 'restaurant', 'saas', 'travel', 'wedding',
);
$notices = array();
set_error_handler( static function ( $severity, $message ) use ( &$notices ) {
	$notices[] = $message;
	return true;
}, E_WARNING | E_NOTICE | E_USER_WARNING | E_USER_NOTICE | E_DEPRECATED | E_USER_DEPRECATED );
try {
	foreach ( $legacy as $value ) {
		$notices = array();
		$options['site_segment'] = $value;
		update_option( 'gnn_options', $options );
		$check(
			'Legacy Site design uses the base theme: ' . var_export( $value, true ),
			'' === gnn_starter_profile_key( $value ) && '' === gnn_site_profile()
				&& array() === gnn_site_profile_body_class( array() )
				&& $value === get_option( 'gnn_options' )['site_segment'] && ! $notices,
			implode( "\n", $notices )
		);
	}

	$options['site_segment'] = 'commerce';
	update_option( 'gnn_options', $options );
	$original_post = $_POST;
	$_POST['gnn_form_scope'] = 'design';
	try {
		gnn_configuration_rejection( null, true );
		$updated = gnn_options_sanitize( array( 'site_segment' => 'general', 'company_phone' => 'Saved legacy settings' ) );
		$result = gnn_configuration_write( $updated, false );
		$check(
			'Saving settings with None displayed preserves a retired design value and saves other fields',
			! is_wp_error( $result ) && 'Saved legacy settings' === gnn_option( 'company_phone' )
				&& 'commerce' === get_option( 'gnn_options' )['site_segment'] && ! gnn_configuration_rejection()
		);
	} finally {
		$_POST = $original_post;
	}

	$notices = array();
	$content = 'Legacy page content remains visible.';
	$page = wp_insert_post( array(
		'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Legacy page',
		'post_content' => $content,
		'meta_input' => array( '_gnn_starter' => 'clinic:home', '_wp_page_template' => 'page-templates/tpl-starter.php' ),
	) );
	$GLOBALS['wp_query'] = new WP_Query( array( 'page_id' => $page ) );
	ob_start();
	require get_theme_file_path( get_page_template_slug( $page ) );
	$html = ob_get_clean();
	$check(
		'Legacy clinic starter template renders saved content without a retired profile',
		false !== strpos( $html, $content ) && ! wp_style_is( 'gnn-template-clinic' ) && ! wp_style_is( 'gnn-profile-commerce' )
			&& false === strpos( $html, 'gnn-site-commerce' ) && ! $notices,
		implode( "\n", $notices )
	);
	wp_reset_query();
	wp_delete_post( $page, true );

	if ( gnn_starter_elementor_available() ) {
		$url = get_template_directory_uri() . '/assets/images/demo/pro/hero.jpg';
		$widget = Elementor\Plugin::instance()->elements_manager->create_element_instance(
			array(
				'id' => 'legacyimage',
				'elType' => 'widget',
				'widgetType' => 'image',
				'settings' => array( 'image' => array( 'url' => $url, 'id' => '' ) ),
			)
		);
		ob_start();
		$widget->print_element();
		$image_html = ob_get_clean();
		$check(
			'Retired Elementor image widgets render a working placeholder and retain their saved URL',
			false !== strpos( $image_html, '/assets/img/placeholder-cover.svg' )
				&& false === strpos( $image_html, $url ) && $url === $widget->get_settings( 'image' )['url']
		);
	}

} finally {
	restore_error_handler();
}
gnn_reset_panel_settings();
echo 'GNN_QA_RESULT:' . wp_json_encode( $checks );
