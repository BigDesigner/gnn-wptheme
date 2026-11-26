<?php
/**
 * Optional integrations and consent-first tracking; no remote service is required.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

/** Register only public copy for string-translation plugins. */
function gnn_register_translatable_options() {
	foreach ( array( 'company_name', 'company_address', 'company_hours', 'company_phone', 'company_email', 'header_cta_label', 'footer_tagline', 'footer_copyright', 'topbar_text', 'topbar_phone', 'topbar_email', 'maintenance_message', 'error404_title', 'error404_text', 'error404_button' ) as $key ) {
		$value = (string) gnn_option( $key );
		if ( function_exists( 'pll_register_string' ) ) {
			pll_register_string( 'gnn_' . $key, $value, 'GNN', true ); }
		do_action( 'wpml_register_single_string', 'GNN', $key, $value );
	}
	foreach ( (array) json_decode( (string) gnn_option( 'mobile_actions' ), true ) as $i => $item ) {
		if ( ! is_array( $item ) || empty( $item['label'] ) ) {
			continue; }
		if ( function_exists( 'pll_register_string' ) ) {
			pll_register_string( 'gnn_action_' . $i, $item['label'], 'GNN' ); }
		do_action( 'wpml_register_single_string', 'GNN', 'action_' . $i, $item['label'] );
	}
}
add_action( 'admin_init', 'gnn_register_translatable_options' );

/**
 * Both adapters operate on a read copy, preserving source strings.
 *
 * @param string $key Option key.
 */
function gnn_translated_option( $key ) {
	$value = (string) gnn_option( $key );
	if ( function_exists( 'pll__' ) ) {
		return pll__( $value ); }
	return apply_filters( 'wpml_translate_single_string', $value, 'GNN', $key );
}

/**
 * Map selected page/region IDs into the current content language.
 *
 * @param int|string $id Record identifier.
 */
function gnn_translated_post_id( $id ) {
	if ( ! $id ) {
		return 0; }
	if ( function_exists( 'pll_get_post' ) ) {
		$translated = pll_get_post( $id );
		return absint( $translated ? $translated : $id ); }
	return absint( apply_filters( 'wpml_object_id', $id, get_post_type( $id ), true ) );
}

/** Public contact data is reusable without embedding it into a demo. */
function gnn_footer_company_details() {
	$address = gnn_translated_option( 'company_address' );
	$hours   = gnn_translated_option( 'company_hours' );
	if ( '' === $address && '' === $hours && ! gnn_option( 'company_social_url' ) ) {
		return; }
	echo '<div class="footer-column footer-company">';
	if ( $address ) {
		echo '<p>' . esc_html( $address ) . '</p>'; }
	if ( $hours ) {
		echo '<p>' . esc_html( $hours ) . '</p>'; }
	if ( gnn_option( 'company_social_url' ) ) {
		echo '<a href="' . esc_url( gnn_option( 'company_social_url' ) ) . '">' . esc_html__( 'Follow us', 'gnn' ) . '</a>'; }
	echo '</div>';
}

/** Resolve configured and default dock actions before drawing either header or dock. */
function gnn_dock_items( $config ) {
	$raw    = gnn_option( 'mobile_actions' );
	$items  = is_string( $raw ) ? json_decode( $raw, true, 8 ) : null;
	$woo    = function_exists( 'wc_get_cart_url' );
	if ( ! is_array( $items ) || ! $items ) {
		$items = array(
			array( 'type' => 'home', 'label' => __( 'Home', 'gnn' ) ),
			array( 'type' => 'search', 'label' => __( 'Search', 'gnn' ) ),
			array( 'type' => 'theme', 'label' => __( 'Theme', 'gnn' ) ),
			$woo ? array( 'type' => 'cart', 'label' => __( 'Cart', 'gnn' ) ) : array( 'type' => 'contact', 'label' => __( 'Contact', 'gnn' ) ),
		);
	} else {
		foreach ( $items as $index => &$item ) {
			if ( is_array( $item ) && isset( $item['label'] ) && is_string( $item['label'] ) ) {
				$item['label'] = function_exists( 'pll__' ) ? pll__( $item['label'] ) : apply_filters( 'wpml_translate_single_string', $item['label'], 'GNN', 'action_' . $index );
			}
		}
		unset( $item );
	}
	$phone = preg_replace( '/[^0-9+]/', '', $config['contact']['phone'] );
	$email = sanitize_email( $config['contact']['email'] );
	$urls  = array(
		'home'    => home_url( '/' ),
		'search'  => gnn_header_search_url( $config ),
		'contact' => gnn_contact_url(),
		'phone'   => $phone ? 'tel:' . $phone : '',
		'email'   => $email ? 'mailto:' . $email : '',
		'cart'    => $woo ? wc_get_cart_url() : '',
	);
	$list = array();
	foreach ( $items as $item ) {
		if ( ! is_array( $item ) || ! isset( $item['label'], $item['type'] ) || ! is_string( $item['label'] ) || ! is_string( $item['type'] ) ) {
			continue;
		}
		$type = $item['type'];
		$url  = $urls[ $type ] ?? ( 'link' === $type && isset( $item['url'] ) && is_string( $item['url'] ) ? $item['url'] : '' );
		$url  = esc_url_raw( $url, array( 'http', 'https', 'mailto', 'tel' ) );
		if ( $url || ( 'theme' === $type && $config['enabled']['theme'] ) ) {
			$list[] = array( 'type' => $type, 'label' => $item['label'], 'url' => $url );
		}
	}
	return $list;
}

/** Only one tag owner is emitted; consent is handled before any network request. */
function gnn_enqueue_consent_tracking() {
	if ( 'consent' !== gnn_option( 'analytics_delivery' ) ) {
		return; }
	$ga4 = preg_match( '/^G-[A-Z0-9]{4,}$/i', (string) gnn_option( 'ga4_id' ) ) ? gnn_option( 'ga4_id' ) : '';
	$gtm = preg_match( '/^GTM-[A-Z0-9]{4,}$/i', (string) gnn_option( 'gtm_id' ) ) ? gnn_option( 'gtm_id' ) : '';
	if ( ! $ga4 && ! $gtm ) {
		return; }
	// Head placement keeps the consent default ahead of later head code; defer keeps it off the critical path.
	$placement = version_compare( get_bloginfo( 'version' ), '6.3', '>=' )
		? array(
			'in_footer' => false,
			'strategy'  => 'defer',
		)
		: false;
	wp_enqueue_script( 'gnn-consent', get_template_directory_uri() . '/assets/js/consent.js', array(), GNN_VERSION, $placement );
	wp_localize_script(
		'gnn-consent',
		'gnnTracking',
		array(
			'ga4' => $gtm ? '' : $ga4,
			'gtm' => $gtm,
		)
	);
	// Runs immediately: denied default first, and early consent calls are queued until the deferred file runs.
	wp_add_inline_script(
		'gnn-consent',
		"window.dataLayer=window.dataLayer||[];(function(){var q=window.gnnConsentQueue=[];if(!window.gnnConsentDefault){window.gnnConsentDefault=true;(function(){window.dataLayer.push(arguments);})('consent','default',{analytics_storage:'denied',ad_storage:'denied',ad_user_data:'denied',ad_personalization:'denied'});}window.GNNConsent=window.GNNConsent||{setAnalyticsConsent:function(a){q.push(a===true);},hasAnalyticsConsent:function(){return false;}};document.addEventListener('gnn:consent',function(e){if(window.gnnConsentQueue)window.gnnConsentQueue.push(!!e.detail&&e.detail.analytics===true);});})();",
		'before'
	);
}
add_action( 'wp_enqueue_scripts', 'gnn_enqueue_consent_tracking' );

/**
 * Defer consent.js on WordPress versions without script loading strategies.
 *
 * @param string $tag    Script tag HTML.
 * @param string $handle Script handle.
 * @return string
 */
function gnn_defer_consent_script( $tag, $handle ) {
	// Core may print defer before or after the id attribute.
	if ( 'gnn-consent' !== $handle || preg_match( '/<script\b(?=[^>]*\sid=["\']gnn-consent-js["\'])[^>]*\sdefer\b/', $tag ) ) {
		return $tag;
	}
	return (string) preg_replace( '/<script([^>]*\sid=["\']gnn-consent-js["\'])/', '<script defer$1', $tag, 1 );
}
add_filter( 'script_loader_tag', 'gnn_defer_consent_script', 10, 2 );

/** Allow a supported SEO provider to own breadcrumbs, avoiding duplicate trails. */
function gnn_provider_breadcrumb() {
	ob_start();
	if ( function_exists( 'yoast_breadcrumb' ) ) {
		yoast_breadcrumb( '<nav class="gnn-breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'gnn' ) . '">', '</nav>' );
	} elseif ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
		rank_math_the_breadcrumbs();
	}
	$html = ob_get_clean();
	if ( '' === trim( $html ) ) {
		return false;
	}
	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted rendering API of the active SEO provider.
	return true;
}

/** Concrete support state, not a promise of mail delivery or legal compliance. */
function gnn_integration_status() {
	return array(
		'form'     => defined( 'WPCF7_VERSION' ) ? 'Contact Form 7 ' . WPCF7_VERSION : __( 'Contact Form 7 not active', 'gnn' ),
		'language' => function_exists( 'pll__' ) ? 'Polylang' : ( defined( 'ICL_SITEPRESS_VERSION' ) ? 'WPML' : __( 'Single language', 'gnn' ) ),
		'seo'      => defined( 'WPSEO_VERSION' ) ? 'Yoast SEO' : ( defined( 'RANK_MATH_VERSION' ) ? 'Rank Math' : __( 'WordPress defaults', 'gnn' ) ),
		'consent'  => function_exists( 'wp_has_consent' ) ? 'WP Consent API' : __( 'External consent bridge required when analytics is used', 'gnn' ),
	);
}

/** Include reusable layouts and slides in the language plugin's content model.
 *
 * @param array $types Enabled post types.
 * @return array
 */
function gnn_polylang_content_types( $types ) {
	$types['gnn_layout'] = 'gnn_layout';
	$types['gnn_slide']  = 'gnn_slide';
	return $types;
}
add_filter( 'pll_get_post_types', 'gnn_polylang_content_types' );

/**
 * WooCommerce customer redirects must not hide the assigned support workspace.
 *
 * @param bool $prevent Whether WooCommerce should redirect this admin request.
 * @return bool
 */
function gnn_support_admin_access( $prevent ) {
	if ( ! current_user_can( 'gnn_view_diagnostics' ) ) {
		return $prevent;
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- routing only; page/action handlers enforce capabilities and nonces.
	$page = isset( $_GET['page'] ) && is_string( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- routing only; the workspace action independently verifies its nonce.
	$action = isset( $_POST['action'] ) && is_string( $_POST['action'] ) ? sanitize_key( wp_unslash( $_POST['action'] ) ) : '';
	return 'gnn-panel' === $page || 'gnn_workspace' === $action ? false : $prevent;
}
add_filter( 'woocommerce_prevent_admin_access', 'gnn_support_admin_access' );
