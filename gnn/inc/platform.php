<?php
/**
 * Shared schema and accessible controls for the site platform.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

/** One schema drives defaults, validation and administrative controls. */
function gnn_platform_schema() {
	return array_merge(
		array(
			'company_name'            => array( __( 'Company name', 'gnn' ), 'text', '', 'header' ),
			'company_address'         => array( __( 'Address', 'gnn' ), 'text', '', 'footer' ),
			'company_hours'           => array( __( 'Opening hours', 'gnn' ), 'text', '', 'footer' ),
			'company_phone'           => array( __( 'Public phone number', 'gnn' ), 'text', '', 'header' ),
			'company_email'           => array( __( 'Public email', 'gnn' ), 'email', '', 'header' ),
			'company_social_url'      => array( __( 'Social profile URL', 'gnn' ), 'url', '', 'footer' ),
			'header_height'           => array( __( 'Header height', 'gnn' ), 'number', 64, 'header', 48, 160 ),
			'container_width'         => array( __( 'Global container width', 'gnn' ), 'number', 1280, 'pages', 900, 1920 ),
			'header_preset'           => array(
				__( 'Header style', 'gnn' ),
				'select',
				'legacy',
				'header',
				array(
					'legacy'      => __( 'Existing layout', 'gnn' ),
					'classic'     => __( 'Classic', 'gnn' ),
					'centered'    => __( 'Centered', 'gnn' ),
					'stacked'     => __( 'Stacked', 'gnn' ),
					'transparent' => __( 'Transparent', 'gnn' ),
					'minimal'     => __( 'Minimal', 'gnn' ),
				),
			),
			'header_cta_label'        => array( __( 'Header action label', 'gnn' ), 'text', '', 'header' ),
			'header_cta_url'          => array( __( 'Header action URL', 'gnn' ), 'url', '', 'header' ),
			'header_contact'          => array( __( 'Show contact information in the header', 'gnn' ), 'bool', 0, 'header' ),
			'header_language'         => array( __( 'Show the language switcher when supported', 'gnn' ), 'bool', 0, 'header' ),
			'header_template'         => array( __( 'Reusable header', 'gnn' ), 'region', 0, 'header' ),
			'footer_preset'           => array(
				__( 'Footer style', 'gnn' ),
				'select',
				'legacy',
				'footer',
				array(
					'legacy'   => __( 'Existing layout', 'gnn' ),
					'compact'  => __( 'Compact', 'gnn' ),
					'balanced' => __( 'Balanced columns', 'gnn' ),
					'centered' => __( 'Centered', 'gnn' ),
				),
			),
			'footer_columns'          => array( __( 'Footer widget columns', 'gnn' ), 'number', 3, 'footer', 1, 4 ),
			'footer_template'         => array( __( 'Reusable footer', 'gnn' ), 'region', 0, 'footer' ),
			'archive_header_template' => array( __( 'Archive header override', 'gnn' ), 'region', -1, 'header' ),
			'archive_footer_template' => array( __( 'Archive footer override', 'gnn' ), 'region', -1, 'footer' ),
			'post_header_template'    => array( __( 'Single post header override', 'gnn' ), 'region', -1, 'header' ),
			'post_footer_template'    => array( __( 'Single post footer override', 'gnn' ), 'region', -1, 'footer' ),
			'brand_dot'               => array( __( 'Show the decorative dot after the text logo', 'gnn' ), 'bool', 1, 'header' ),
			'body_font'               => array(
				__( 'Local body font', 'gnn' ),
				'select',
				'manrope',
				'typography',
				array(
					'manrope' => 'Manrope',
					'space'   => 'Space Grotesk',
					'system'  => __( 'System fonts', 'gnn' ),
					'custom'  => __( 'Uploaded local font', 'gnn' ),
				),
			),
			'heading_font'            => array(
				__( 'Local heading font', 'gnn' ),
				'select',
				'space',
				'typography',
				array(
					'space'   => 'Space Grotesk',
					'manrope' => 'Manrope',
					'system'  => __( 'System fonts', 'gnn' ),
					'custom'  => __( 'Uploaded local font', 'gnn' ),
				),
			),
			'local_font'              => array( __( 'Local WOFF2 font attachment ID', 'gnn' ), 'font', 0, 'typography' ),
			'body_size'               => array( __( 'Body font size', 'gnn' ), 'number', 17, 'typography', 14, 24 ),
			'heading_size'            => array( __( 'Maximum H1 size', 'gnn' ), 'number', 56, 'typography', 28, 100 ),
			'heading_mobile_size'     => array( __( 'Mobile H1 size', 'gnn' ), 'number', 34, 'typography', 24, 56 ),
			'body_line_height'        => array( __( 'Body line height (%)', 'gnn' ), 'number', 175, 'typography', 130, 220 ),
			'heading_line_height'     => array( __( 'Heading line height (%)', 'gnn' ), 'number', 115, 'typography', 100, 160 ),
			'heading_tracking'        => array( __( 'Heading letter spacing (hundredths of em)', 'gnn' ), 'number', -1, 'typography', -8, 20 ),
			'design_radius'           => array( __( 'Card and image corner radius', 'gnn' ), 'number', 16, 'styles', 0, 40 ),
			'button_radius'           => array( __( 'Button corner radius', 'gnn' ), 'number', 999, 'styles', 0, 999 ),
			'button_padding'          => array( __( 'Button vertical padding', 'gnn' ), 'number', 14, 'styles', 8, 28 ),
			'design_spacing'          => array( __( 'Section spacing', 'gnn' ), 'number', 64, 'styles', 24, 120 ),
			'design_shadow'           => array(
				__( 'Surface shadow', 'gnn' ),
				'select',
				'none',
				'styles',
				array(
					'none'   => __( 'None', 'gnn' ),
					'soft'   => __( 'Soft', 'gnn' ),
					'strong' => __( 'Strong', 'gnn' ),
				),
			),
			'blog_featured'           => array( __( 'Feature the first blog post', 'gnn' ), 'bool', 1, 'blog_archive' ),
			'blog_meta'               => array( __( 'Show listing date and reading time', 'gnn' ), 'bool', 1, 'blog_archive' ),
			'blog_author'             => array( __( 'Show author information', 'gnn' ), 'bool', 1, 'blog_single' ),
			'blog_navigation'         => array( __( 'Show previous and next posts', 'gnn' ), 'bool', 1, 'blog_single' ),
			'blog_columns'            => array( __( 'Blog columns on desktop', 'gnn' ), 'number', 3, 'blog_archive', 1, 4 ),
			'hero_style'              => array(
				__( 'Hero layout', 'gnn' ),
				'select',
				'centered',
				'global',
				array(
					'centered' => __( 'Centered', 'gnn' ),
					'left'     => __( 'Left aligned', 'gnn' ),
					'compact'  => __( 'Compact banner', 'gnn' ),
					'none'     => __( 'No theme slider', 'gnn' ),
				),
			),
			'hero_group'              => array( __( 'Slider group', 'gnn' ), 'slide_group', 0, 'global' ),
			'mobile_actions'          => array( __( 'Mobile actions', 'gnn' ), 'actions', '[]', 'header' ),
			'analytics_delivery'      => array(
				__( 'Analytics delivery', 'gnn' ),
				'select',
				'consent',
				'code',
				array(
					'off'      => __( 'Disabled', 'gnn' ),
					'consent'  => __( 'After analytics consent', 'gnn' ),
					'external' => __( 'Managed entirely outside the theme', 'gnn' ),
				),
			),
			'site_segment'            => array(
				__( 'Business segment', 'gnn' ),
				'select',
				'general',
				'global',
				array(
					'pro'          => __( 'GNNpro', 'gnn' ),
					'commerce'     => __( 'GNNcommerce', 'gnn' ),
					'design'       => __( 'GNNdesign', 'gnn' ),
					'general'      => __( 'General services', 'gnn' ),
					'professional' => __( 'Professional services', 'gnn' ),
					'industrial'   => __( 'Industrial and B2B', 'gnn' ),
					'creative'     => __( 'Creative agency', 'gnn' ),
					'architecture' => __( 'Architecture', 'gnn' ),
				),
			),
			'portfolio_columns'       => array( __( 'Portfolio columns', 'gnn' ), 'number', 2, 'pages', 1, 4 ),
			'portfolio_per_page'      => array( __( 'Projects per page', 'gnn' ), 'number', 12, 'pages', 1, 48 ),
			'portfolio_image_ratio'   => array(
				__( 'Portfolio image ratio', 'gnn' ),
				'select',
				'4/3',
				'pages',
				array(
					'4/3'  => '4:3',
					'16/9' => '16:9',
					'1/1'  => '1:1',
					'3/4'  => '3:4',
					'auto' => __( 'Natural image ratio', 'gnn' ),
				),
			),
			'portfolio_image_fit'     => array(
				__( 'Portfolio image fit', 'gnn' ),
				'select',
				'cover',
				'pages',
				array(
					'cover'   => __( 'Cover', 'gnn' ),
					'contain' => __( 'Contain', 'gnn' ),
				),
			),
			'gallery_columns'         => array( __( 'Gallery columns', 'gnn' ), 'number', 3, 'pages', 1, 4 ),
			'module_gap'              => array( __( 'Portfolio and gallery spacing', 'gnn' ), 'number', 28, 'pages', 8, 80 ),
			'project_detail_width'    => array( __( 'Project detail maximum width', 'gnn' ), 'number', 1280, 'pages', 800, 1920 ),
			'compatibility_mode'      => array( __( 'Prefer third-party plugin compatibility', 'gnn' ), 'bool', 1, 'advanced' ),
		),
		gnn_blog_schema(),
		function_exists( 'gnn_site_schema' ) ? gnn_site_schema() : array()
	);
}

/** Additional defaults preserve the established option keys. */
function gnn_platform_defaults() {
	$out = array();
	foreach ( gnn_platform_schema() as $key => $field ) {
		$out[ $key ] = $field[2]; }
	return $out;
}

/**
 * Validate semantic values before any shared option or theme mod is changed.
 *
 * @param array $input Submitted options.
 */
function gnn_validate_platform_input( $input ) {
	if ( strlen( wp_json_encode( $input ) ) > 1048576 ) {
		return new WP_Error( 'gnn_size', __( 'Settings exceed the 1 MB limit. Nothing was changed.', 'gnn' ) ); }
	foreach ( gnn_platform_schema() as $key => $field ) {
		if ( ! isset( $input[ $key ] ) ) {
			continue; }
		$value = $input[ $key ];
		if ( ! is_scalar( $value ) ) {
			return new WP_Error( 'gnn_type', __( 'Invalid field value type. No settings were changed.', 'gnn' ) ); }
		$invalid = ! is_scalar( $value ) || ( is_string( $value ) && strlen( $value ) > 65536 );
		if ( 'select' === $field[1] && ! isset( $field[4][ $value ] ) ) {
			$invalid = true; }
		if ( 'optional_number' === $field[1] && '' !== $value && ( ! preg_match( '/^-?[0-9]+$/D', (string) $value ) || $value < $field[4] || $value > $field[5] ) ) {
			$invalid = true; }
		if ( 'number' === $field[1] && ( ! preg_match( '/^-?[0-9]+$/D', (string) $value ) || $value < $field[4] || $value > $field[5] ) ) {
			$invalid = true; }
		if ( 'email' === $field[1] && '' !== $value && ! is_email( $value ) ) {
			$invalid = true; }
		if ( 'url' === $field[1] && '' !== $value && ! esc_url_raw( $value ) ) {
			$invalid = true; }
		if ( 'actions' === $field[1] && is_wp_error( gnn_sanitize_actions( (string) $value ) ) ) {
			$invalid = true; }
		if ( 'hex' === $field[1] && '' !== $value && ! sanitize_hex_color( $value ) ) {
			$invalid = true;
		}
		if ( 'region' === $field[1] && ( ! preg_match( '/^-?[0-9]+$/D', (string) $value ) || (int) $value < -2 || ( (int) $value > 0 && ( 'gnn_layout' !== get_post_type( $value ) || 'publish' !== get_post_status( $value ) ) ) ) ) {
			$invalid = true;
		}
		if ( in_array( $field[1], array( 'layout', 'font', 'slide_group' ), true ) && ! preg_match( '/^[0-9]+$/D', (string) $value ) ) {
			$invalid = true; }
		if ( ! $invalid && $value && 'layout' === $field[1] && ( 'gnn_layout' !== get_post_type( $value ) || 'publish' !== get_post_status( $value ) ) ) {
			$invalid = true; }
		if ( ! $invalid && $value && 'font' === $field[1] && 'font/woff2' !== get_post_mime_type( $value ) ) {
			$invalid = true; }
		if ( $invalid ) {
			/* translators: %s: field name; %d: action position. */
			return new WP_Error( 'gnn_field_' . $key, sprintf( __( 'Invalid value for %s. No settings were changed.', 'gnn' ), $field[0] ) ); }
	}
	foreach ( array(
		'ga4_id' => '/^G-[A-Z0-9]{4,}$/i',
		'gtm_id' => '/^GTM-[A-Z0-9]{4,}$/i',
	) as $key => $pattern ) {
		if ( ! empty( $input[ $key ] ) && ! preg_match( $pattern, (string) $input[ $key ] ) ) {
			return new WP_Error( 'gnn_tracking_id', __( 'The analytics ID is invalid. No settings were changed.', 'gnn' ) ); }
	}
	return true;
}

/**
 * Validate structured actions without permitting arbitrary script or SVG input.
 *
 * @param string $raw Untrusted JSON input.
 */
function gnn_sanitize_actions( $raw ) {
	$items = json_decode( $raw, true, 8 );
	if ( ! is_array( $items ) || count( $items ) > 6 ) {
		return new WP_Error( 'gnn_actions', __( 'Choose at most six valid mobile actions.', 'gnn' ) ); }
	$out = array();
	foreach ( $items as $item ) {
		if ( ! is_array( $item ) || ! isset( $item['type'], $item['label'] ) || ! is_string( $item['label'] ) || ! in_array( $item['type'], array( 'home', 'search', 'theme', 'cart', 'contact', 'phone', 'email', 'link' ), true ) ) {
			return new WP_Error( 'gnn_actions', __( 'A mobile action is invalid.', 'gnn' ) ); }
		$url = isset( $item['url'] ) && is_string( $item['url'] ) ? esc_url_raw( $item['url'], array( 'http', 'https', 'tel', 'mailto' ) ) : '';
		if ( 'link' === $item['type'] && '' === $url ) {
			return new WP_Error( 'gnn_actions', __( 'A link action needs a valid URL.', 'gnn' ) ); }
		$out[] = array(
			'type'  => $item['type'],
			'label' => wp_html_excerpt( sanitize_text_field( $item['label'] ), 40, '' ),
			'url'   => $url,
		);
	}
	return wp_json_encode( $out );
}

/**
 * Platform values pass through the same full-form save contract.
 *
 * @param array $out Sanitized base options.
 * @param array $input Submitted options.
 */
function gnn_sanitize_platform_options( $out, $input ) {
	foreach ( gnn_platform_schema() as $key => $field ) {
		if ( ! array_key_exists( $key, $input ) ) {
			if ( 'bool' === $field[1] && isset( $input['platform_fields_present'] ) && ! isset( gnn_site_schema()[ $key ] ) ) {
				$out[ $key ] = 0; }
			continue;
		}
		$value = $input[ $key ];
		switch ( $field[1] ) {
			case 'hex':
				$value = sanitize_hex_color( $value );
				break;
			case 'region':
				$value = max( -2, (int) $value );
				if ( $value > 0 && ( 'gnn_layout' !== get_post_type( $value ) || 'publish' !== get_post_status( $value ) ) ) {
					$value = -1;
				}
				break;
			case 'bool':
				$value = empty( $value ) ? 0 : 1;
				break;
			case 'optional_number':
				$value = '' === $value ? '' : (string) max( $field[4], min( $field[5], (int) $value ) );
				break;
			case 'number':
				$value = max( $field[4], min( $field[5], (int) $value ) );
				if ( 'blog_width' === $key && $value > 0 ) {
					$value = max( 360, $value ); }
				break;
			case 'email':
				$value = sanitize_email( $value );
				break;
			case 'url':
				$value = esc_url_raw( $value );
				break;
			case 'select':
				if ( ! isset( $field[4][ $value ] ) ) {
					$value = $field[2];
				} break;
			case 'layout':
				$value = absint( $value );
				if ( $value && ( 'gnn_layout' !== get_post_type( $value ) || 'publish' !== get_post_status( $value ) ) ) {
					$value = 0; }
				break;
			case 'font':
				$value = absint( $value );
				if ( $value && ( 'font/woff2' !== get_post_mime_type( $value ) || ! wp_get_attachment_url( $value ) ) ) {
					$value = 0; }
				break;
			case 'slide_group':
				$value = absint( $value );
				break;
			case 'actions':
				$value = gnn_sanitize_actions( $value );
				if ( is_wp_error( $value ) ) {
					add_settings_error( 'gnn_options', $value->get_error_code(), $value->get_error_message() );
					continue 2; }
				break;
			default:
				$value = sanitize_text_field( $value );
		}
		$out[ $key ] = $value;
	}
	return $out;
}

/**
 * Render schema-driven accessible controls in the existing Settings API form.
 *
 * @param string $group Settings group.
 */
function gnn_platform_fields( $group ) {
	$titles = array(
		'global'       => __( 'Business profile and hero', 'gnn' ),
		'header'       => __( 'Company and header configuration', 'gnn' ),
		'footer'       => __( 'Company and footer configuration', 'gnn' ),
		'pages'        => __( 'Content and blog layout', 'gnn' ),
		'styles'       => __( 'Design tokens', 'gnn' ),
		'typography'   => __( 'Local fonts and type scale', 'gnn' ),
		'code'         => __( 'Tracking delivery', 'gnn' ),
		'blog_archive' => __( 'Blog listing', 'gnn' ),
		'blog_cards'   => __( 'Listing images', 'gnn' ),
		'blog_single'  => __( 'Single post layout and details', 'gnn' ),
		'blog_media'   => __( 'Single featured image', 'gnn' ),
	);
	if ( function_exists( 'gnn_section_bar' ) ) {
		gnn_section_bar( $titles[ $group ] ?? __( 'Site platform', 'gnn' ) );
	} else {
		echo '<div class="gnn-section-bar" aria-hidden="true"><span>' . esc_html( $titles[ $group ] ?? __( 'Site platform', 'gnn' ) ) . '</span></div>';
	}
	foreach ( gnn_platform_schema() as $key => $field ) {
		if ( $field[3] !== $group ) {
			continue;
		}
		switch ( $field[1] ) {
			case 'bool':
				gnn_field_checkbox( $key, $field[0] );
				break;
			case 'number':
				gnn_field_number( $key, $field[0], $field[4], $field[5] );
				break;
			case 'select':
				gnn_field_select( $key, $field[0], $field[4] );
				break;
			case 'region':
			case 'layout':
				$choices = array(
					-1 => __( 'Inherit', 'gnn' ),
					0  => __( 'Theme default', 'gnn' ),
				);
				foreach ( get_posts(
					array(
						'post_type'   => 'gnn_layout',
						'numberposts' => 100,
						'post_status' => 'publish',
						'orderby'     => 'title',
						'order'       => 'ASC',
					)
				) as $layout ) {
					$choices[ $layout->ID ] = $layout->post_title;
				}
				gnn_field_select( $key, $field[0], $choices );
				break;
			case 'slide_group':
				$choices = array( 0 => __( 'All slides', 'gnn' ) );
				foreach ( get_terms(
					array(
						'taxonomy'   => 'gnn_slide_group',
						'hide_empty' => false,
					)
				) as $term ) {
					if ( $term instanceof WP_Term ) {
						$choices[ $term->term_id ] = $term->name;
					}
				}
				gnn_field_select( $key, $field[0], $choices );
				break;
			case 'actions':
				echo '<div class="gnn-setting-row gnn-setting-row--actions"><div class="gnn-setting-label-col"><span class="gnn-setting-label">' . esc_html( $field[0] ) . '</span></div><div class="gnn-setting-control-col">';
				gnn_mobile_action_fields();
				echo '</div></div>';
				break;
			case 'font':
				gnn_field_number( $key, $field[0], 0, PHP_INT_MAX, __( 'Upload a trusted WOFF2 file in Media, then choose its attachment ID. No remote font URL is used.', 'gnn' ) );
				echo '<div class="gnn-setting-row"><div class="gnn-setting-label-col"></div><div class="gnn-setting-control-col"><button type="button" class="button gnn-font-pick" data-target="gnn-' . esc_attr( $key ) . '">' . esc_html__( 'Choose local WOFF2 font', 'gnn' ) . '</button></div></div>';
				break;
			default:
				gnn_field_text( $key, $field[0], (string) gnn_option( $key ) );
		}
	}
}

/** Action rows have a useful fallback when JavaScript is unavailable. */
function gnn_mobile_action_fields() {
	$items = json_decode( (string) gnn_option( 'mobile_actions' ), true );
	$items = is_array( $items ) ? array_slice( $items, 0, 6 ) : array();
	echo '<fieldset class="gnn-action-editor"><legend>' . esc_html__( 'Mobile actions in display order (leave labels empty to use the existing dock)', 'gnn' ) . '</legend>';
	$types = array(
		'home'    => __( 'Home', 'gnn' ),
		'search'  => __( 'Search', 'gnn' ),
		'contact' => __( 'Contact', 'gnn' ),
		'phone'   => __( 'Call', 'gnn' ),
		'email'   => __( 'Email', 'gnn' ),
		'link'    => __( 'Custom link', 'gnn' ),
		'cart'    => __( 'Cart', 'gnn' ),
		'theme'   => __( 'Theme', 'gnn' ),
	);
	for ( $i = 0; $i < 6; $i++ ) {
		$item = $items[ $i ] ?? array(
			'type'  => 'link',
			'label' => '',
			'url'   => '',
		);
		/* translators: %s: field name; %d: action position. */
		echo '<div class="gnn-action-row"><select aria-label="' . esc_attr( sprintf( __( 'Action %d type', 'gnn' ), $i + 1 ) ) . '" data-action-type>';
		foreach ( $types as $type => $label ) {
			echo '<option value="' . esc_attr( $type ) . '" ' . selected( $item['type'], $type, false ) . '>' . esc_html( $label ) . '</option>'; }
		echo '</select><input type="text" data-action-label aria-label="' . esc_attr__( 'Action label', 'gnn' ) . '" value="' . esc_attr( $item['label'] ) . '"><input type="url" data-action-url aria-label="' . esc_attr__( 'Action URL', 'gnn' ) . '" value="' . esc_attr( $item['url'] ) . '"></div>';
	}
	echo '<input type="hidden" name="gnn_options[mobile_actions]" data-action-payload value="' . esc_attr( wp_json_encode( $items ) ) . '"><noscript><p>' . esc_html__( 'Action editing requires JavaScript. Existing actions are preserved.', 'gnn' ) . '</p></noscript></fieldset>';
}

/**
 * Register WOFF2 without permitting arbitrary executable upload types.
 *
 * @param array $mimes Allowed MIME types.
 */
function gnn_font_upload_mime( $mimes ) {
	if ( current_user_can( 'manage_options' ) ) {
		$mimes['woff2'] = 'font/woff2'; }
	return $mimes;
}
add_filter( 'upload_mimes', 'gnn_font_upload_mime' );
