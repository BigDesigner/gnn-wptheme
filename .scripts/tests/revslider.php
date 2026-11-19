<?php
/** WordPress integration checks using a fake public shortcode, not the commercial plugin. */
$checks = array();
$check = function ( $name, $pass, $detail = '' ) use ( &$checks ) {
	$checks[] = array( 'name' => $name, 'pass' => (bool) $pass, 'detail' => $detail );
};
$old_options = get_option( 'gnn_options', array() );
$old_front = get_option( 'page_on_front' );
$old_show = get_option( 'show_on_front' );
$old_query = $GLOBALS['wp_query'];
$old_post = $GLOBALS['post'] ?? null;
$old_shortcode = $GLOBALS['shortcode_tags']['rev_slider'] ?? null;
$front = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Slider test front' ) );
$other = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Slider test page' ) );
$slide = wp_insert_post( array( 'post_type' => 'gnn_slide', 'post_status' => 'publish', 'post_title' => 'Fallback slide' ) );
$set_options = function ( $changes ) use ( $old_options ) {
	update_option( 'gnn_options', array_merge( $old_options, array( 'hero_engine' => 'revslider', 'hero_revslider' => 'home-test', 'hero_style' => 'centered', 'hero_group' => 0 ), $changes ) );
};
$query = function ( $id ) {
	$GLOBALS['wp_query'] = new WP_Query( array( 'page_id' => $id ) );
};
$render = function () {
	ob_start();
	include get_template_directory() . '/front-page.php';
	return ob_get_clean();
};
try {
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $front );
	$set_options( array() );
	$query( $front );
	remove_shortcode( 'rev_slider' );
	$html = $render();
	$check( 'SR01 missing shortcode falls back to GNN Slider on front page', ! gnn_revslider_active() && false !== strpos( $html, 'gnn-hero__track' ) && false === strpos( $html, 'class="gnn-revslider"' ) );
	$received = null;
	add_shortcode( 'rev_slider', function ( $atts ) use ( &$received ) {
		$received = $atts['alias'] ?? '';
		return '<div class="qa-revslider">Shortcode fixture</div>';
	} );
	$query( $front );
	$html = $render();
	$check( 'SR02 public shortcode output is wrapped and replaces GNN hero', false !== strpos( $html, '<div class="gnn-revslider"><div class="qa-revslider">' ) && false === strpos( $html, 'gnn-hero__track' ) && 'home-test' === $received );
	$check( 'SR03 body class matches active hero', in_array( 'gnn-has-revslider', gnn_revslider_body_class( array() ), true ) );
	$set_options( array( 'hero_revslider' => 'home"><[bad alias="x"]/_-09' ) );
	ob_start(); gnn_render_revslider(); $html = ob_get_clean();
	$check( 'SR04 unsafe alias characters cannot inject shortcode attributes', 'homebadaliasx_-09' === $received && 'homebadaliasx_-09' === gnn_revslider_alias() );
	$set_options( array( 'hero_revslider' => '[] /"' ) );
	$check( 'SR05 empty sanitized alias falls back', ! gnn_revslider_active() && gnn_front_hero_renders() );
	$set_options( array() );
	$query( $other );
	ob_start(); gnn_render_revslider(); $html = ob_get_clean();
	$check( 'SR06 other pages do not render managed hero or body class', ! gnn_revslider_active() && '' === $html && array() === gnn_revslider_body_class( array() ) );
	$query( $front );
	$set_options( array( 'hero_style' => 'none' ) );
	$html = $render();
	$check( 'SR07 none hides both heroes and overlay state with shortcode', ! gnn_front_hero_renders() && ! gnn_revslider_active() && false === strpos( $html, 'class="gnn-revslider"' ) && false === strpos( $html, 'gnn-hero__track' ) && array() === gnn_revslider_body_class( array() ) );
	remove_shortcode( 'rev_slider' );
	$query( $front );
	$html = $render();
	$check( 'SR08 none remains hidden without shortcode', ! gnn_front_hero_renders() && false === strpos( $html, 'gnn-hero__track' ) );
	$set_options( array() );
	add_shortcode( 'rev_slider', function () { return 'fixture'; } );
	foreach ( array( 'page-templates/tpl-starter.php', 'page-templates/tpl-architecture.php' ) as $template ) {
		update_post_meta( $front, '_wp_page_template', $template );
		$query( $front );
		$check( 'SR09 delegated template excludes managed hero: ' . $template, ! gnn_revslider_active() && ! gnn_front_hero_renders() );
	}
	delete_post_meta( $front, '_wp_page_template' );
	update_option( 'show_on_front', 'posts' );
	$GLOBALS['wp_query'] = new WP_Query();
	$check( 'SR10 latest posts excludes managed hero', ! gnn_revslider_active() );
} finally {
	update_option( 'gnn_options', $old_options );
	update_option( 'page_on_front', $old_front );
	update_option( 'show_on_front', $old_show );
	foreach ( array( $front, $other, $slide ) as $id ) { wp_delete_post( $id, true ); }
	remove_shortcode( 'rev_slider' );
	if ( null !== $old_shortcode ) { add_shortcode( 'rev_slider', $old_shortcode ); }
	$GLOBALS['wp_query'] = $old_query;
	$GLOBALS['post'] = $old_post;
}
echo 'GNN_QA_RESULT:' . wp_json_encode( $checks );
