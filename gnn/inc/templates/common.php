<?php
/**
 * Original Elementor Free page sets for GNNpro, GNNcommerce and GNNdesign.
 * Every section is a native container with Free widgets, so it stays editable.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Page keys and titles for each design that has its own Elementor page set. */
function gnn_st_pages( $profile ) {
	switch ( $profile ) {
		case 'pro':
			return gnn_st_pages_pro();
		case 'commerce':
			return gnn_st_pages_commerce();
		case 'design':
			return gnn_st_pages_design();
		case 'architecture':
			return gnn_st_pages_architecture();
		default:
			return array();
	}
}

/** Whether a design has a bundled one-step demo with its own sample records (not Architecture). */
function gnn_dp_has( $profile ) {
	return in_array( $profile, array( 'pro', 'commerce', 'design' ), true );
}

/** Whether a profile uses the dedicated page generator. */
function gnn_st_has( $profile ) {
	return array() !== gnn_st_pages( $profile );
}

/** Short random Elementor element ID. */
function gnn_st_id() {
	return substr( md5( wp_generate_uuid4() ), 0, 7 );
}

/**
 * Container. Layout comes from the class, so Elementor controls can override it.
 *
 * @param string $class Extra classes.
 * @param array  $children Child elements.
 * @param bool   $inner Nested container.
 */
function gnn_st_c( $class, $children = array(), $inner = false ) {
	return array(
		'id'       => gnn_st_id(),
		'elType'   => 'container',
		'isInner'  => $inner,
		'settings' => array(
			'content_width'  => 'full',
			'flex_direction' => 'column',
			'css_classes'    => 'gnn-st ' . $class,
		),
		'elements' => array_values( array_filter( $children ) ),
	);
}

/** Widget element. */
function gnn_st_w( $type, $settings, $class = '' ) {
	if ( '' !== $class ) {
		$settings['_css_classes'] = $class;
	}
	return array(
		'id'         => gnn_st_id(),
		'elType'     => 'widget',
		'widgetType' => $type,
		'settings'   => $settings,
		'elements'   => array(),
	);
}

/** Heading widget. */
function gnn_st_h( $text, $level = 2, $class = '' ) {
	return gnn_st_w(
		'heading',
		array(
			'title'       => $text,
			'header_size' => 'h' . $level,
		),
		$class
	);
}

/** Paragraph widget. */
function gnn_st_p( $text, $class = '' ) {
	return gnn_st_w( 'text-editor', array( 'editor' => '<p>' . esc_html( $text ) . '</p>' ), $class );
}

/** Button widget. */
function gnn_st_b( $label, $url, $class = '' ) {
	return gnn_st_w(
		'button',
		array(
			'text' => $label,
			'link' => array( 'url' => esc_url_raw( $url ) ),
		),
		$class
	);
}

/** Bundled demo image URL. */
function gnn_st_url( $profile, $name ) {
	return get_template_directory_uri() . '/assets/images/demo/' . $profile . '/' . $name . '.jpg';
}

/** Image widget from a bundled demo file. */
function gnn_st_img( $profile, $name, $alt, $class = '' ) {
	return gnn_st_w(
		'image',
		array(
			'image'      => array(
				'url' => gnn_st_url( $profile, $name ),
				'id'  => '',
				'alt' => $alt,
			),
			'image_size' => 'full',
			'link_to'    => 'none',
		),
		$class
	);
}

/** One of the GNN collection widgets. */
function gnn_st_mod( $module, $settings, $class = '' ) {
	return gnn_st_w( 'gnn-' . $module, $settings, $class );
}

/** Small marked-up block for details a Free widget does not cover. */
function gnn_st_html( $html, $class = '' ) {
	return gnn_st_w( 'html', array( 'html' => $html ), $class );
}

/**
 * Full-width band with a centred content wrapper.
 *
 * @param string $scope Design scope class.
 * @param string $name Section name.
 * @param array  $children Child elements.
 */
function gnn_st_band( $scope, $name, $children ) {
	return gnn_st_c( $scope . ' ' . $scope . '-' . $name, array( gnn_st_c( 'gnn-wrap', $children, true ) ) );
}

/** Eyebrow, heading and optional lead for a section head. */
function gnn_st_head( $eyebrow, $title, $lead = '', $class = '' ) {
	return gnn_st_c(
		'gnn-head ' . $class,
		array(
			$eyebrow ? gnn_st_p( $eyebrow, 'gnn-eyebrow' ) : null,
			gnn_st_h( $title, 2 ),
			$lead ? gnn_st_p( $lead, 'gnn-lead' ) : null,
		),
		true
	);
}

/** Permalink of an installed page, or the home page. */
function gnn_st_link( $ids, $key ) {
	return isset( $ids[ $key ] ) ? get_permalink( $ids[ $key ] ) : home_url( '/' );
}

/** Contact lines from the saved company details, never invented ones. */
function gnn_st_contact( $class = 'gnn-contact-list' ) {
	$items = array();
	$email = sanitize_email( (string) gnn_option( 'company_email' ) );
	$phone = (string) gnn_option( 'company_phone' );
	$addr  = (string) gnn_option( 'company_address' );
	$hours = (string) gnn_option( 'company_hours' );
	$items[] = gnn_st_b( $email ? $email : 'hello@example.com', 'mailto:' . ( $email ? $email : 'hello@example.com' ), 'gnn-link' );
	if ( preg_match( '/^\+?[0-9\s().-]{5,30}$/', $phone ) ) {
		$items[] = gnn_st_b( $phone, 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ), 'gnn-link' );
	}
	$items[] = gnn_st_p( $addr ? $addr : __( 'Add your address here.', 'gnn' ) );
	if ( $hours ) {
		$items[] = gnn_st_p( $hours );
	}
	return gnn_st_c( $class, $items, true );
}

/** Closing call-to-action band. */
function gnn_st_cta( $scope, $title, $label, $url, $text = '' ) {
	return gnn_st_band( $scope, 'cta', array( gnn_st_h( $title, 2 ), $text ? gnn_st_p( $text, 'gnn-lead' ) : null, gnn_st_b( $label, $url, 'gnn-btn-main' ) ) );
}

/** Numbered cards (services, steps, values). */
function gnn_st_cards( $items, $class, $link = '', $link_label = '' ) {
	$cards = array();
	foreach ( $items as $index => $item ) {
		$cards[] = gnn_st_c(
			'gnn-card',
			array(
				gnn_st_p( sprintf( '%02d', $index + 1 ), 'gnn-num' ),
				gnn_st_h( $item[0], 3 ),
				gnn_st_p( $item[1] ),
				$link ? gnn_st_b( $link_label, $link, 'gnn-link' ) : null,
			),
			true
		);
	}
	return gnn_st_c( 'gnn-cards ' . $class, $cards, true );
}

/** Page opening for inner pages. */
function gnn_st_page_head( $scope, $eyebrow, $title, $lead ) {
	return gnn_st_band( $scope, 'pagehead', array( gnn_st_p( $eyebrow, 'gnn-eyebrow' ), gnn_st_h( $title, 1 ), gnn_st_p( $lead, 'gnn-lead' ) ) );
}

/**
 * Elements for one page of a design.
 *
 * @param string $profile pro, commerce or design.
 * @param string $key Page key.
 * @param array  $ids Installed page IDs.
 * @return array
 */
function gnn_st_page( $profile, $key, $ids ) {
	$fn = 'gnn_st_' . $profile . '_' . $key;
	return function_exists( $fn ) ? $fn( $ids ) : array();
}

