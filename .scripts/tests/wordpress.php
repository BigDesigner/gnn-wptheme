<?php
/** Integration assertions executed inside an isolated, real WordPress install. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$checks = array();
function gnn_qa_check( $name, $condition, $detail = '' ) {
	$GLOBALS['checks'][] = array( 'name' => $name, 'pass' => (bool) $condition, 'detail' => $detail );
}
wp_set_current_user( 1 );

if ( 'updater' === $suite ) {
	$updater = gnn_updater();
	gnn_qa_check( 'A03 updater registered outside admin', false !== has_filter( 'pre_set_site_transient_update_themes', array( $updater, 'check_for_update' ) ) && ! is_admin() );
	gnn_qa_check( 'A04 unsafe post-install filter removed', false === has_filter( 'upgrader_post_install', array( $updater, 'after_install' ) ) );
	$key = 'gnn_github_update_check_v2';
	$qa_requests = 0;
	$qa_release = array();
	$intercept = function ( $pre, $args, $url ) use ( &$qa_release, &$qa_requests ) {
		if ( 0 !== strpos( $url, 'https://api.github.com/repos/BigDesigner/gnn-wptheme/' ) ) { return $pre; }
		$qa_requests++;
		return array( 'headers' => array(), 'body' => wp_json_encode( $qa_release ), 'response' => array( 'code' => 200 ), 'cookies' => array() );
	};
	add_filter( 'pre_http_request', $intercept, 10, 3 );
	$good = function ( $version ) {
		return array( 'tag_name' => 'v' . $version, 'assets' => array( array( 'name' => 'gnn-' . $version . '.zip',
			'browser_download_url' => 'https://github.com/BigDesigner/gnn-wptheme/releases/download/v' . $version . '/gnn-' . $version . '.zip' ) ),
			'html_url' => 'https://github.com/BigDesigner/gnn-wptheme/releases/tag/v' . $version );
	};
	$qa_release = $good( '1.6.2' );
	delete_transient( $key );
	$release = $updater->get_remote_release();
	$updater->get_remote_release();
	gnn_qa_check( 'A05 exact release asset accepted and cached', is_object( $release ) && '1.6.2' === $release->version && 1 === $qa_requests );
	$transient = (object) array( 'checked' => array( 'gnn' => GNN_VERSION ), 'response' => array(), 'no_update' => array( 'gnn' => array() ) );
	$transient = $updater->check_for_update( $transient );
	gnn_qa_check( 'A03 newer release offered exclusively', isset( $transient->response['gnn'] ) && ! isset( $transient->no_update['gnn'] ) );
	$qa_release = $good( GNN_VERSION ); delete_transient( $key );
	$transient = $updater->check_for_update( $transient );
	gnn_qa_check( 'A04 stale update removed for current version', ! isset( $transient->response['gnn'] ) && isset( $transient->no_update['gnn'] ) );
	foreach ( array( 'no-asset', 'foreign-host', 'wrong-name', 'prerelease', 'invalid-tag' ) as $case ) {
		$qa_release = $good( '1.6.2' );
		if ( 'no-asset' === $case ) { $qa_release['assets'] = array(); $qa_release['zipball_url'] = 'https://api.github.com/repos/BigDesigner/gnn-wptheme/zipball/v1.6.2'; }
		if ( 'foreign-host' === $case ) { $qa_release['assets'][0]['browser_download_url'] = 'https://example.invalid/gnn-1.6.2.zip'; }
		if ( 'wrong-name' === $case ) { $qa_release['assets'][0]['name'] = 'source.zip'; }
		if ( 'prerelease' === $case ) { $qa_release['prerelease'] = true; }
		if ( 'invalid-tag' === $case ) { $qa_release['tag_name'] = 'release-latest'; }
		delete_transient( $key );
		gnn_qa_check( 'A05 reject ' . $case, false === $updater->get_remote_release() );
	}
	$transient->response['gnn'] = array( 'new_version' => '99.0.0' );
	$transient = $updater->check_for_update( $transient );
	gnn_qa_check( 'A05 missing asset clears stale offers', ! isset( $transient->response['gnn'] ) );
	delete_transient( $key );
	remove_filter( 'pre_http_request', $intercept, 10 );
	$offline = function ( $pre, $args, $url ) { return false !== strpos( $url, 'api.github.com' ) ? new WP_Error( 'qa_offline', 'Offline fixture' ) : $pre; };
	add_filter( 'pre_http_request', $offline, 10, 3 );
	gnn_qa_check( 'A05 network error negatively cached', false === $updater->get_remote_release() && 'no-release' === get_transient( $key ) );
	remove_filter( 'pre_http_request', $offline, 10 );
	$error = new WP_Error( 'qa_previous', 'Previous filter error' );
	gnn_qa_check( 'A04 previous WP_Error preserved', $error === $updater->validate_source( $error, '/tmp/none', null, array( 'theme' => 'gnn' ) ) );
	gnn_qa_check( 'A04 unrelated theme source unchanged', '/untouched/' === $updater->validate_source( '/untouched/', '/tmp/none', null, array( 'theme' => 'other' ) ) );
	require_once ABSPATH . 'wp-admin/includes/file.php';
	WP_Filesystem();
	$qa_root = trailingslashit( get_temp_dir() ) . 'gnn-qa-update-' . wp_rand() . '/';
	wp_mkdir_p( $qa_root . 'gnn' );
	gnn_qa_check( 'A04 invalid extracted package rejected', is_wp_error( $updater->validate_source( $qa_root . 'gnn/', $qa_root, null, array( 'theme' => 'gnn' ) ) ) );
	file_put_contents( $qa_root . 'gnn/style.css', "/*\nTheme Name: GNN\nVersion: 1.6.2\n*/" );
	file_put_contents( $qa_root . 'gnn/index.php', '<?php' );
	gnn_qa_check( 'A04 valid source retained before install', $qa_root . 'gnn/' === $updater->validate_source( $qa_root . 'gnn/', $qa_root, null, array( 'theme' => 'gnn' ) ) );
	wp_mkdir_p( $qa_root . 'different' );
	copy( $qa_root . 'gnn/style.css', $qa_root . 'different/style.css' ); copy( $qa_root . 'gnn/index.php', $qa_root . 'different/index.php' );
	$blocked = $updater->validate_source( $qa_root . 'different/', $qa_root, null, array( 'theme' => 'gnn' ) );
	gnn_qa_check( 'A04 occupied rename target returns error and preserves files', is_wp_error( $blocked ) && file_exists( $qa_root . 'different/style.css' ) && file_exists( $qa_root . 'gnn/style.css' ) );
	gnn_qa_check( 'A04 extracted path cannot escape root', is_wp_error( $updater->validate_source( $qa_root . '../gnn/', $qa_root, null, array( 'theme' => 'gnn' ) ) ) );
	if ( 0 === strpos( $qa_root, trailingslashit( get_temp_dir() ) . 'gnn-qa-update-' ) ) { $wp_filesystem->delete( $qa_root, true ); }
	update_option( 'gnn_github_updates_enable', 0 );
	$disabled = new GNN_GitHub_Updater();
	gnn_qa_check( 'A03 disabled updater registers no filter', false === has_filter( 'pre_set_site_transient_update_themes', array( $disabled, 'check_for_update' ) ) );
	update_option( 'gnn_github_updates_enable', 1 );
	delete_transient( $key );
}

if ( 'templates' === $suite ) {
	gnn_qa_check( 'A02 actual Elementor runtime loaded', defined( 'ELEMENTOR_VERSION' ) && post_type_exists( 'elementor_library' ) );
	gnn_qa_check( 'A02 initial library saved', gnn_install_elementor_templates() );
	$ids = get_posts( array( 'post_type' => 'elementor_library', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_key' => '_gnn_elementor_template', 'orderby' => 'ID', 'order' => 'ASC' ) );
	gnn_qa_check( 'A02 expected library size', 15 === count( $ids ), count( $ids ) );
	$edited = $ids[0]; $untouched = $ids[1]; $legacy = $ids[2]; $trashed = $ids[3]; $missing = $ids[4];
	update_post_meta( $edited, '_elementor_data', wp_slash( '[{"user":"custom section"}]' ) );
	wp_update_post( array( 'ID' => $edited, 'post_title' => 'User custom title' ) );
	delete_post_meta( $legacy, '_gnn_elementor_baseline' );
	update_post_meta( $legacy, '_elementor_data', 'legacy custom payload' );
	wp_trash_post( $trashed );
	$old_data = get_post_meta( $untouched, '_elementor_data', true );
	update_option( 'gnn_elementor_templates_installed', 'old-theme-version' );
	set_theme_mod( 'gnn_accent_color', '#ff7711' );
	gnn_maybe_install_elementor_templates();
	gnn_qa_check( 'A02 user title/data and ID survive migration', 'User custom title' === get_the_title( $edited ) && '[{"user":"custom section"}]' === get_post_meta( $edited, '_elementor_data', true ) );
	gnn_qa_check( 'A02 untouched template refreshed in place', get_post( $untouched ) && $old_data !== get_post_meta( $untouched, '_elementor_data', true ) && gnn_elementor_template_fingerprint( $untouched ) === get_post_meta( $untouched, '_gnn_elementor_baseline', true ) );
	gnn_qa_check( 'A02 unknown legacy payload preserved', 'legacy custom payload' === get_post_meta( $legacy, '_elementor_data', true ) );
	gnn_qa_check( 'A02 trash decision preserved', 'trash' === get_post_status( $trashed ) );
	$missing_slug = get_post_meta( $missing, '_gnn_elementor_template', true );
	wp_delete_post( $missing, true ); // Deliberate fixture mutation in disposable DB.
	gnn_maybe_install_elementor_templates();
	$healed = get_posts( array( 'post_type' => 'elementor_library', 'meta_key' => '_gnn_elementor_template', 'meta_value' => $missing_slug, 'fields' => 'ids' ) );
	gnn_qa_check( 'A02 missing entry healed without replacing siblings', 1 === count( $healed ) && get_post( $edited ) && get_post( $legacy ) );
	wp_delete_post( $healed[0], true );
	update_option( 'gnn_elementor_templates_installed', 'retry-needed' );
	$reject_insert = function ( $empty, $post ) { return 'elementor_library' === $post['post_type'] && empty( $post['ID'] ) ? true : $empty; };
	add_filter( 'wp_insert_post_empty_content', $reject_insert, 10, 2 );
	$complete = gnn_install_elementor_templates();
	gnn_qa_check( 'A02 failed insert does not mark migration complete', false === $complete && 'retry-needed' === get_option( 'gnn_elementor_templates_installed' ) && get_post( $edited ) );
	remove_filter( 'wp_insert_post_empty_content', $reject_insert, 10 );
	gnn_qa_check( 'A02 failed migration can retry successfully', gnn_install_elementor_templates() && GNN_ELEMENTOR_LIBRARY_VERSION === get_option( 'gnn_elementor_templates_installed' ) );
	wp_set_current_user( 0 ); update_option( 'gnn_elementor_templates_installed', 'unauthorized' );
	gnn_maybe_install_elementor_templates();
	gnn_qa_check( 'A02 anonymous request cannot migrate templates', 'unauthorized' === get_option( 'gnn_elementor_templates_installed' ) );
	wp_set_current_user( 1 ); gnn_maybe_install_elementor_templates();
}

if ( 'woocommerce' === $suite ) {
	gnn_qa_check( 'A06 actual WooCommerce loaded', defined( 'WC_VERSION' ) && shortcode_exists( 'products' ) );
	foreach ( array( 'products', 'product_category', 'product_categories', 'add_to_cart', 'add_to_cart_url', 'product_page', 'woocommerce_cart', 'woocommerce_order_tracking' ) as $tag ) {
		gnn_qa_check( 'A06 detect shortcode ' . $tag, gnn_content_uses_woocommerce( '[' . $tag . ' id="1"]' ) );
	}
	gnn_qa_check( 'A06 nested product collection detected', gnn_content_uses_woocommerce( '<!-- wp:group --><div><!-- wp:woocommerce/product-collection {} /--></div><!-- /wp:group -->' ) );
	$ref = wp_insert_post( array( 'post_type' => 'wp_block', 'post_status' => 'publish', 'post_title' => 'Product reference', 'post_content' => '[product_category category="hardware"]' ) );
	gnn_qa_check( 'A06 referenced block detected', gnn_content_uses_woocommerce( '<!-- wp:block {"ref":' . $ref . '} /-->' ) );
	$cycle = wp_insert_post( array( 'post_type' => 'wp_block', 'post_status' => 'publish', 'post_title' => 'Cyclic block' ) );
	wp_update_post( array( 'ID' => $cycle, 'post_content' => '<!-- wp:block {"ref":' . $cycle . '} /-->' ) );
	gnn_qa_check( 'A06 cyclic reference terminates', false === gnn_content_uses_woocommerce( '<!-- wp:block {"ref":' . $cycle . '} /-->' ) );
	gnn_qa_check( 'A06 plain article is eligible for scoping', false === gnn_content_uses_woocommerce( '<!-- wp:paragraph --><p>Article</p><!-- /wp:paragraph -->' ) );
	wp_set_sidebars_widgets( array( 'sidebar-1' => array( 'block-22' ), 'wp_inactive_widgets' => array() ) );
	$GLOBALS['_wp_sidebars_widgets'] = null; // Reset core's per-request cache after the fixture mutation.
	update_option( 'widget_block', array( 22 => array( 'content' => '[product_category]' ) ) );
	gnn_qa_check( 'A06 product block widget retains assets', gnn_woocommerce_widgets_active() );
	wp_set_sidebars_widgets( array( 'wp_inactive_widgets' => array( 'block-22' ) ) );
	$GLOBALS['_wp_sidebars_widgets'] = null;
	gnn_qa_check( 'A06 inactive product widget ignored', ! gnn_woocommerce_widgets_active() );
	$page = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Scoped products', 'post_content' => '[product_category]' ) );
	$GLOBALS['wp_query'] = new WP_Query( array( 'page_id' => $page ) );
	do_action( 'wp_enqueue_scripts' ); // Register Woo's real frontend handles before asserting the queue.
	wp_enqueue_style( 'gnn-woocommerce', get_template_directory_uri() . '/assets/css/woocommerce.css' );
	wp_enqueue_script( 'wc-add-to-cart' );
	gnn_scope_woo_assets();
	gnn_qa_check( 'A06 actual queued assets retained for product page', wp_style_is( 'gnn-woocommerce', 'enqueued' ) && wp_script_is( 'wc-add-to-cart', 'enqueued' ) );
	wp_update_post( array( 'ID' => $page, 'post_content' => 'Plain article' ) );
	$GLOBALS['wp_query'] = new WP_Query( array( 'page_id' => $page ) );
	gnn_scope_woo_assets();
	gnn_qa_check( 'A06 actual assets removed for ordinary page', ! wp_style_is( 'gnn-woocommerce', 'enqueued' ) && ! wp_script_is( 'wc-add-to-cart', 'enqueued' ) );
	gnn_qa_check( 'A06 header cart fragments preserved', wp_script_is( 'wc-cart-fragments', 'enqueued' ) );
	update_post_meta( $page, '_elementor_edit_mode', 'builder' );
	wp_enqueue_style( 'gnn-woocommerce' ); gnn_scope_woo_assets();
	gnn_qa_check( 'A06 dynamic Elementor content retains assets', wp_style_is( 'gnn-woocommerce', 'enqueued' ) );
	wp_reset_query();
}

if ( 'reset' === $suite ) {
	update_option( 'gnn_options', array( 'topbar_enable' => 1 ) );
	set_theme_mod( 'gnn_accent_color', '#123456' ); set_theme_mod( 'gnn_default_theme', 'light' ); set_theme_mod( 'gnn_footer_legal', 'Legal fixture' );
	set_theme_mod( 'nav_menu_locations', array( 'primary' => 22 ) );
	update_option( 'site_icon', 42 ); update_option( 'gnn_github_updates_enable', 0 );
	gnn_reset_panel_settings();
	gnn_qa_check( 'A09 panel option removed', false === get_option( 'gnn_options' ) );
	gnn_qa_check( 'A09 shared brand settings reset', false === get_theme_mod( 'gnn_accent_color' ) && false === get_theme_mod( 'gnn_default_theme' ) && false === get_theme_mod( 'gnn_footer_legal' ) );
	gnn_qa_check( 'A09 unrelated site/update settings preserved', 42 === (int) get_option( 'site_icon' ) && 0 === (int) get_option( 'gnn_github_updates_enable' ) && array( 'primary' => 22 ) === get_theme_mod( 'nav_menu_locations' ) );
	$nonce = wp_create_nonce( 'gnn_reset_settings' );
	gnn_qa_check( 'A09 reset nonce bound to action', 1 === wp_verify_nonce( $nonce, 'gnn_reset_settings' ) && false === wp_verify_nonce( $nonce, 'unrelated' ) );
	wp_set_current_user( 0 );
	gnn_qa_check( 'A09 anonymous user lacks reset capability', ! current_user_can( 'manage_options' ) );
	wp_set_current_user( 1 );
}

if ( 'fixtures' === $suite ) {
	foreach ( get_posts( array( 'post_type' => 'post', 'posts_per_page' => -1, 'post_status' => 'any', 'fields' => 'ids' ) ) as $id ) { wp_delete_post( $id, true ); }
	for ( $i = 1; $i <= 5; $i++ ) { wp_insert_post( array( 'post_title' => 'Audit post ' . $i, 'post_content' => 'Audit post body ' . $i, 'post_status' => 'publish', 'post_date' => '2026-01-0' . $i . ' 12:00:00' ) ); }
	update_option( 'posts_per_page', 2 ); update_option( 'show_on_front', 'posts' );
	update_option( 'gnn_options', array( 'google_material_icons' => 0, 'slider_autoplay' => 1, 'slider_interval' => 2 ) );
	update_option( 'site_icon', 0 ); update_option( 'gnn_github_updates_enable', 0 );
	for ( $i = 1; $i <= 3; $i++ ) {
		$id = wp_insert_post( array( 'post_type' => 'gnn_slide', 'post_status' => 'publish', 'post_title' => 'Audit slide ' . $i, 'menu_order' => $i ) );
		update_post_meta( $id, '_gnn_slide_cta_label', 'Open slide ' . $i );
		update_post_meta( $id, '_gnn_slide_cta_url', '#slide-' . $i );
	}
	$home = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Audit home', 'post_content' => '<p>STATIC FRONT CONTENT</p>' ) );
	update_option( 'gnn_qa_home_id', $home );
}
echo 'GNN_QA_RESULT:' . wp_json_encode( $checks );
