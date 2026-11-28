<?php
/**
 * Editable Gutenberg starters and shared frontend and editor styles.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A small portable native-block vocabulary is also the source for the Elementor edition.
 *
 * @param string $profile Profile key.
 * @param string $key Page key.
 * @param array  $ids Linked draft page IDs.
 */
function gnn_profile_page_content( $profile, $key, $ids = array() ) {
	$details = gnn_starter_profiles()[ $profile ];
	$promise = __( 'We begin with your objectives and explain the options before agreeing the scope.', 'gnn' );
	$labels   = array(
		'home'      => __( 'Overview', 'gnn' ),
		'about'     => __( 'About', 'gnn' ),
		'services'  => __( 'Services', 'gnn' ),
		'news'      => __( 'News', 'gnn' ),
		'contact'   => __( 'Contact', 'gnn' ),
		'portfolio' => __( 'Portfolio', 'gnn' ),
		'gallery'   => __( 'Gallery', 'gnn' ),
	);
	$nav      = '';
	foreach ( array_intersect_key( $labels, $ids ) as $page => $label ) {
		$nav .= gnn_architecture_button( $label, gnn_architecture_url( $ids, $page ) );
	}
	$content  = gnn_architecture_group( 'gnn-starter-nav', $nav );
	$title    = 'home' === $key ? ( $details['heading'] ?? $details['label'] ) : $labels[ $key ];
	$content .= gnn_architecture_group( 'gnn-starter-hero', gnn_architecture_text( $details['label'], 'gnn-starter-eyebrow' ) . gnn_architecture_heading( $title, 1 ) . gnn_architecture_text( $details['description'], 'gnn-starter-lead' ) );
	if ( in_array( $key, array( 'home', 'about' ), true ) ) {
		$content .= gnn_architecture_group( 'gnn-starter-section gnn-starter-split', gnn_architecture_heading( $labels['about'] ) . gnn_architecture_group( 'gnn-starter-copy', gnn_architecture_text( $promise ) . gnn_architecture_text( __( 'Introduce your team, approach and relevant experience here. Replace this draft copy with your own verified information before publishing.', 'gnn' ) ) . gnn_architecture_button( __( 'Meet the team', 'gnn' ), gnn_architecture_url( $ids, 'about' ) ) ) );
	}
	if ( in_array( $key, array( 'home', 'services' ), true ) ) {
		$cards = '';
		foreach ( array( __( 'Advisory', 'gnn' ), __( 'Strategy and assessment', 'gnn' ), __( 'Ongoing partnership', 'gnn' ) ) as $index => $service ) {
			$cards .= gnn_architecture_group( 'gnn-starter-card', gnn_architecture_text( '0' . ( $index + 1 ), 'gnn-starter-eyebrow' ) . gnn_architecture_heading( $service, 3 ) . gnn_architecture_text( __( 'Describe the scope, who it helps and what a client can expect. Add your own process and relevant examples.', 'gnn' ) ) . gnn_architecture_button( __( 'Discuss your requirements', 'gnn' ), gnn_architecture_url( $ids, 'contact' ) ) );
		}
		$content .= gnn_architecture_group( 'gnn-starter-section', gnn_architecture_heading( __( 'How we can help', 'gnn' ) ) . gnn_architecture_group( 'gnn-starter-cards', $cards ) );
	}
	if ( in_array( $key, array( 'services', 'about' ), true ) ) {
		$steps = '';
		foreach ( array( __( 'Listen and define', 'gnn' ), __( 'Plan and collaborate', 'gnn' ), __( 'Deliver and review', 'gnn' ) ) as $step ) {
			$steps .= gnn_architecture_group( 'gnn-starter-card', gnn_architecture_heading( $step, 3 ) . gnn_architecture_text( $promise ) );
		}
		$content .= gnn_architecture_group( 'gnn-starter-section', gnn_architecture_heading( __( 'A clear way of working', 'gnn' ) ) . gnn_architecture_group( 'gnn-starter-cards', $steps ) );
	}
	if ( 'news' === $key ) {
		$content .= gnn_architecture_group( 'gnn-starter-section', gnn_architecture_heading( __( 'Insights from the team', 'gnn' ) ) . gnn_architecture_text( __( 'Use this editorial page for announcements, resources and selected articles. Add your own article summaries and links in your chosen editor.', 'gnn' ) ) );
		$blog     = (int) get_option( 'page_for_posts' );
		if ( $blog && 'publish' === get_post_status( $blog ) ) {
			$content .= gnn_architecture_button( __( 'Read the latest articles', 'gnn' ), get_permalink( $blog ) );
		}
	}
	if ( 'contact' === $key ) {
		$contact = gnn_architecture_heading( __( 'Tell us about your project', 'gnn' ) );
		foreach ( array( 'company_email', 'company_phone', 'company_address', 'company_hours' ) as $field ) {
			$value = (string) gnn_translated_option( $field );
			if ( 'company_email' === $field && is_email( $value ) ) {
				$contact .= gnn_architecture_button( $value, 'mailto:' . sanitize_email( $value ) );
			} elseif ( 'company_phone' === $field && preg_match( '/^\+?[0-9\s().-]{5,30}$/', $value ) ) {
				$contact .= gnn_architecture_button( $value, 'tel:' . preg_replace( '/[^0-9+]/', '', $value ) );
			} elseif ( $value ) {
				$contact .= gnn_architecture_text( $value );
			}
		}
		$content .= gnn_architecture_group( 'gnn-starter-section gnn-starter-split', $contact . gnn_architecture_text( __( 'Add your public contact details and a tested form from your preferred form plugin before publishing.', 'gnn' ), 'gnn-starter-note' ) );
	} else {
		$content .= gnn_architecture_group( 'gnn-starter-callout', gnn_architecture_heading( __( 'Let’s plan the next step.', 'gnn' ) ) . gnn_architecture_text( $promise ) . gnn_architecture_button( __( 'Contact us', 'gnn' ), gnn_architecture_url( $ids, 'contact' ) ) );
	}
	return gnn_architecture_group( 'gnn-starter-site gnn-starter-profile--' . sanitize_html_class( $profile ), $content );
}

/** Conditional frontend presentation and Elementor editor preview assets. */
function gnn_starter_styles() {
	$post = get_queried_object();
	if ( ! is_singular() || ! $post instanceof WP_Post || ( ! in_array( get_page_template_slug(), array( 'page-templates/tpl-starter.php', 'page-templates/tpl-architecture.php' ), true ) && false === strpos( $post->post_content, 'gnn-starter' ) && false === strpos( $post->post_content, 'gnn-arch' ) ) ) {
		return;
	}
	wp_enqueue_style( 'gnn-starter-editors', get_template_directory_uri() . '/assets/css/starter-editors.css', array( 'gnn-design-system' ), GNN_VERSION );
}
add_action( 'wp_enqueue_scripts', 'gnn_starter_styles', 16 );

/** Native editor shares the profile presentation, too. */
function gnn_starter_editor_styles() {
	add_editor_style( 'assets/css/starter-editors.css' );
}
add_action( 'after_setup_theme', 'gnn_starter_editor_styles', 20 );
