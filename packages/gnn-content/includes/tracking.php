<?php
/**
 * GNN Content independent API.
 *
 * @package GNN_Content
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; }
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
	wp_enqueue_script( 'gnn-consent', GNN_CONTENT_URL . '/assets/js/consent.js', array(), GNN_CONTENT_VERSION, $placement );
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
