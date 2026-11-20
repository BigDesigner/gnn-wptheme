<?php
/**
 * Content settings and code output.
 *
 * @package GNN_Content
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; }
/** Allowed content settings. */
function gnn_content_fields() {
	return array(
		'ga4_id'             => __( 'GA4 ID', 'gnn-content' ),
		'gtm_id'             => __( 'GTM ID', 'gnn-content' ),
		'analytics_delivery' => __( 'Analytics delivery', 'gnn-content' ),
		'head_html'          => __( 'Head HTML', 'gnn-content' ),
		'body_html'          => __( 'Body HTML', 'gnn-content' ),
		'custom_js_head'     => __( 'Head JavaScript', 'gnn-content' ),
		'custom_js_footer'   => __( 'Footer JavaScript', 'gnn-content' ),
		'disable_oembed'     => __( 'Disable embeds', 'gnn-content' ),
	);
}
/** Keep content screens beneath one plugin menu. */
function gnn_content_menu() {
	add_menu_page( 'GNN Content', 'GNN Content', 'manage_options', 'gnn-panel', 'gnn_content_settings', 'dashicons-screenoptions', '79.103' );
	foreach ( array( 'gnn_project', 'gnn_product', 'gnn_album', 'gnn_slide', 'gnn_layout' ) as $type ) {
		$object = get_post_type_object( $type );
		if ( $object ) {
			add_submenu_page( 'gnn-panel', $object->labels->name, $object->labels->name, $object->cap->edit_posts, 'edit.php?post_type=' . $type ); }
	}
	add_submenu_page( 'gnn-panel', __( 'Used in', 'gnn-content' ), __( 'Used in', 'gnn-content' ), 'manage_options', 'gnn-usage', 'gnn_usage_page' );
}
add_action( 'admin_menu', 'gnn_content_menu' );
/** Settings form. */
function gnn_content_settings() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return; }
	echo '<div class="wrap"><h1>GNN Content</h1><form method="post" action="' . esc_url( admin_url( 'options.php' ) ) . '">';
	settings_fields( 'gnn_content' );
	echo '<table class="form-table">';
	foreach ( gnn_content_fields() as $key => $label ) {
		if ( in_array( $key, array( 'head_html', 'body_html', 'custom_js_head', 'custom_js_footer' ), true ) && ! current_user_can( 'unfiltered_html' ) ) {
			continue; }
		echo '<tr><th><label for="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label></th><td>';
		if ( 'analytics_delivery' === $key ) {
			echo '<select id="analytics_delivery" name="gnn_options[analytics_delivery]">';
			foreach ( array(
				'consent'  => __( 'Consent', 'gnn-content' ),
				'external' => __( 'External', 'gnn-content' ),
			) as $value => $text ) {
				echo '<option value="' . esc_attr( $value ) . '" ' . selected( gnn_option( $key ), $value, false ) . '>' . esc_html( $text ) . '</option>'; }
			echo '</select>';
		} elseif ( 'disable_oembed' === $key ) {
			echo '<input type="hidden" name="gnn_options[disable_oembed]" value="0"><input type="checkbox" id="disable_oembed" name="gnn_options[disable_oembed]" value="1" ' . checked( (bool) gnn_option( $key ), true, false ) . '>';
		} else {
			echo '<textarea class="large-text" id="' . esc_attr( $key ) . '" name="gnn_options[' . esc_attr( $key ) . ']">' . esc_textarea( gnn_option( $key ) ) . '</textarea>'; }
		echo '</td></tr>';
	}
	echo '</table>';
	submit_button();
	echo '</form></div>';
}
/**
 * Validate a partial update; never reset unrelated options.
 *
 * @param array $input Input.
 * @return array
 */
function gnn_content_sanitize_settings( $input ) {
	$stored = get_option( 'gnn_options', array() );
	$stored = is_array( $stored ) ? $stored : array();
	foreach ( gnn_content_fields() as $key => $label ) {
		if ( ! isset( $input[ $key ] ) || ! is_string( $input[ $key ] ) ) {
			continue; }
		$value = $input[ $key ];
		if ( in_array( $key, array( 'head_html', 'body_html', 'custom_js_head', 'custom_js_footer' ), true ) ) {
			if ( ! current_user_can( 'unfiltered_html' ) ) {
				continue; }
			$stored[ $key ] = 0 === strpos( $key, 'custom_js_' ) ? str_ireplace( '</script', '<\/script', $value ) : $value;
		} elseif ( 'ga4_id' === $key || 'gtm_id' === $key ) {
			$pattern        = 'ga4_id' === $key ? '/^G-[A-Z0-9]{4,}$/i' : '/^GTM-[A-Z0-9]{4,}$/i';
			$stored[ $key ] = preg_match( $pattern, trim( $value ) ) ? strtoupper( trim( $value ) ) : '';
		} elseif ( 'analytics_delivery' === $key ) {
			$stored[ $key ] = 'consent' === $value ? 'consent' : 'external';
		} else {
			$stored[ $key ] = '1' === $value; }
	}
	return $stored;
}
/** Register the existing shared option with a partial-update sanitizer. */
function gnn_content_register_settings() {
	register_setting(
		'gnn_content',
		'gnn_options',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'gnn_content_sanitize_settings',
		)
	);
}
add_action( 'admin_init', 'gnn_content_register_settings' );
/**
 * Print stored privileged markup.
 *
 * @param string $position Output position.
 */
function gnn_content_code( $position ) {
	$html = 'head' === $position ? gnn_option( 'head_html' ) : ( 'body' === $position ? gnn_option( 'body_html' ) : '' );
	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- only unfiltered_html users can save this markup.
	if ( 'body' !== $position ) {
		$js = str_ireplace( '</script', '<\/script', (string) gnn_option( 'custom_js_' . $position ) );
		if ( '' !== trim( $js ) ) {
			echo '<script>' . $js . '</script>'; } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- privileged code, closing tags neutralized.
	}
}
add_action(
	'wp_head',
	function () {
		gnn_content_code( 'head' );
	},
	20
);
add_action(
	'wp_body_open',
	function () {
		gnn_content_code( 'body' );
	},
	20
);
add_action(
	'wp_footer',
	function () {
		gnn_content_code( 'footer' );
	},
	99
);
/**
 * Preserve font-upload capability boundary.
 *
 * @param array $mimes Types.
 */
function gnn_content_upload_mimes( $mimes ) {
	if ( current_user_can( 'manage_options' ) ) {
		$mimes['woff2'] = 'font/woff2'; }
	return $mimes;
}
add_filter( 'upload_mimes', 'gnn_content_upload_mimes' );
/** Honor the stored embed switch. */
function gnn_content_performance() {
	if ( gnn_option( 'disable_oembed' ) && ! gnn_option( 'compatibility_mode' ) ) {
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' ); }
}
add_action( 'init', 'gnn_content_performance' );
