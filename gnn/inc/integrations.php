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

/** Render configured mobile actions; an empty list retains the legacy dock. */
function gnn_render_configured_actions() {
	$items = json_decode( (string) gnn_option( 'mobile_actions' ), true );
	if ( ! is_array( $items ) || ! $items ) {
		return false; }
	$glyphs = array(
		'home'    => '⌂',
		'search'  => '⌕',
		'contact' => '✉',
		'phone'   => '☎',
		'email'   => '✉',
		'cart'    => '▣',
		'theme'   => '◐',
		'link'    => '↗',
	);
	echo '<nav class="gnn-dock" aria-label="' . esc_attr__( 'Mobile actions', 'gnn' ) . '">';
	foreach ( $items as $i => $item ) {
		$type = $item['type'];
		$url  = $item['url'];
		if ( 'home' === $type ) {
			$url = home_url( '/' ); }
		if ( 'search' === $type ) {
			$url = home_url( '/?s=' ); }
		if ( 'contact' === $type ) {
			$url = gnn_contact_url(); }
		if ( 'phone' === $type ) {
			if ( ! gnn_option( 'company_phone' ) ) {
				continue; }
			$url = 'tel:' . preg_replace( '/[^0-9+]/', '', gnn_option( 'company_phone' ) ); }
		if ( 'email' === $type ) {
			if ( ! gnn_option( 'company_email' ) ) {
				continue; }
			$url = 'mailto:' . gnn_option( 'company_email' ); }
		if ( 'cart' === $type ) {
			if ( ! function_exists( 'wc_get_cart_url' ) ) {
				continue;
			} $url = wc_get_cart_url(); }
		if ( 'theme' === $type && ! gnn_option( 'show_toggle' ) ) {
			continue; }
		$label   = function_exists( 'pll__' ) ? pll__( $item['label'] ) : apply_filters( 'wpml_translate_single_string', $item['label'], 'GNN', 'action_' . $i );
		$content = '<span class="gnn-dock__glyph" aria-hidden="true">' . esc_html( $glyphs[ $type ] ?? '↗' ) . '</span><span class="gnn-dock__lbl">' . esc_html( $label ) . '</span>';
		if ( 'theme' === $type ) {
			// phpcs:ignore WordPress.Security.EscapeOutput -- content is escaped above.
			echo '<button class="gnn-dock__item theme-toggle" type="button">' . $content . '</button>';
		} elseif ( $url ) {
			echo '<a class="gnn-dock__item" href="' . esc_url( $url ) . '">' . $content . '</a>'; } // phpcs:ignore WordPress.Security.EscapeOutput -- content is escaped above.
	}
	echo '</nav>';
	return true;
}

/** Only one tag owner is emitted; consent is handled before any network request. */
function gnn_enqueue_consent_tracking() {
	if ( 'consent' !== gnn_option( 'analytics_delivery' ) ) {
		return; }
	$ga4 = preg_match( '/^G-[A-Z0-9]{4,}$/i', (string) gnn_option( 'ga4_id' ) ) ? gnn_option( 'ga4_id' ) : '';
	$gtm = preg_match( '/^GTM-[A-Z0-9]{4,}$/i', (string) gnn_option( 'gtm_id' ) ) ? gnn_option( 'gtm_id' ) : '';
	if ( ! $ga4 && ! $gtm ) {
		return; }
	wp_enqueue_script( 'gnn-consent', get_template_directory_uri() . '/assets/js/consent.js', array(), GNN_VERSION, false );
	wp_localize_script(
		'gnn-consent',
		'gnnTracking',
		array(
			'ga4' => $gtm ? '' : $ga4,
			'gtm' => $gtm,
		)
	);
}
add_action( 'wp_enqueue_scripts', 'gnn_enqueue_consent_tracking' );

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
	return in_array( $page, array( 'gnn-panel', 'gnn-workspace' ), true ) || 'gnn_workspace' === $action ? false : $prevent;
}
add_filter( 'woocommerce_prevent_admin_access', 'gnn_support_admin_access' );
