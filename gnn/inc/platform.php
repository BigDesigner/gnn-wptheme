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
					'shop'        => __( 'Shop', 'gnn' ),
					'floating'    => __( 'Floating', 'gnn' ),
				),
			),
			'header_search_bar'       => array( __( 'Show search field in the header', 'gnn' ), 'bool', 0, 'header' ),
			'header_cta_show'         => array( __( 'Show header action button', 'gnn' ), 'bool', 1, 'header' ),
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
			'footer_show_shared'      => array( __( 'Show shared footer', 'gnn' ), 'bool', 1, 'footer' ),
			'footer_show_with_shared' => array( __( 'Show theme footer with shared footer', 'gnn' ), 'bool', 0, 'footer' ),
			'footer_show_brand'       => array( __( 'Show footer brand', 'gnn' ), 'bool', 1, 'footer' ),
			'footer_show_tagline'     => array( __( 'Show footer tagline', 'gnn' ), 'bool', 1, 'footer' ),
			'footer_show_widgets'     => array( __( 'Show footer columns', 'gnn' ), 'bool', 1, 'footer' ),
			'footer_show_company'     => array( __( 'Show footer contact details', 'gnn' ), 'bool', 1, 'footer' ),
			'footer_show_copyright'   => array( __( 'Show footer copyright', 'gnn' ), 'bool', 1, 'footer' ),
			'footer_show_legal'       => array( __( 'Show footer legal text', 'gnn' ), 'bool', 1, 'footer' ),
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
			// Retained for existing documents that consume this legacy token.
			// Role based type controls are the editable typography scale.
			'heading_tracking'        => array( __( 'Heading letter spacing (hundredths of em)', 'gnn' ), 'number', 0, 'typography', 0, 50 ),
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
			'elementor_kit_sync'       => array( __( 'Sync colors and fonts with Elementor', 'gnn' ), 'bool', 0, 'global' ),
			'hero_group'              => array( __( 'Slider group', 'gnn' ), 'slide_group', 0, 'global' ),
			'hero_engine'             => array(
				__( 'Slider source', 'gnn' ),
				'select',
				'gnn',
				'global',
				array(
					'gnn'       => __( 'GNN Slider', 'gnn' ),
					'revslider' => __( 'Slider Revolution', 'gnn' ),
				),
			),
			'hero_revslider'          => array( __( 'Slider Revolution alias', 'gnn' ), 'text', '', 'global' ),
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
				__( 'Site design', 'gnn' ),
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
				) + ( function_exists( 'gnn_starter_profiles' ) ? wp_list_pluck( gnn_starter_profiles(), 'label' ) : array() ),
			),
			'portfolio_columns'       => array( __( 'Portfolio and catalog columns', 'gnn' ), 'number', 2, 'pages', 1, 4 ),
			'portfolio_per_page'      => array( __( 'Items per page', 'gnn' ), 'number', 12, 'pages', 1, 48 ),
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
		gnn_header_schema(),
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
 * Strict and pattern entries of gnn_options_schema() are checked here.
 *
 * @param array $input Submitted options.
 */
function gnn_validate_platform_input( $input ) {
	if ( strlen( wp_json_encode( $input ) ) > 1048576 ) {
		return new WP_Error( 'gnn_size', __( 'Settings exceed the 1 MB limit. Nothing was changed.', 'gnn' ) ); }
	$schema = gnn_options_schema();
	foreach ( $schema as $key => $field ) {
		if ( empty( $field['strict'] ) || ! isset( $input[ $key ] ) ) {
			continue; }
		$value = $input[ $key ];
		$type  = $field['type'];
		if ( ! is_scalar( $value ) ) {
			return new WP_Error( 'gnn_type', __( 'Invalid field value type. No settings were changed.', 'gnn' ) ); }
		$invalid = is_string( $value ) && strlen( $value ) > 65536;
		if ( 'select' === $type && ! isset( $field['choices'][ $value ] ) ) {
			$invalid = true; }
		if ( 'optional_number' === $type && '' !== $value && ( ! preg_match( '/^-?[0-9]+$/D', (string) $value ) || $value < $field['min'] || $value > $field['max'] ) ) {
			$invalid = true; }
		if ( 'number' === $type && ( ! preg_match( '/^-?[0-9]+$/D', (string) $value ) || $value < $field['min'] || $value > $field['max'] ) ) {
			$invalid = true; }
		if ( 'email' === $type && '' !== $value && ! is_email( $value ) ) {
			$invalid = true; }
		if ( 'url' === $type && '' !== $value && ! esc_url_raw( $value ) ) {
			$invalid = true; }
		if ( 'header_elements' === $type && is_wp_error( gnn_parse_header_elements( $value ) ) ) {
			$invalid = true;
		}
		if ( 'header_overlay' === $key && ! in_array( $value, array( '', 0, 1, '0', '1', false, true ), true ) ) {
			$invalid = true;
		}
		if ( 'topbar_mobile' === $key && ! in_array( $value, array( 0, 1, '0', '1', false, true ), true ) ) {
			$invalid = true;
		}
		if ( 'logo_overlay' === $key && '' !== $value && ( ! preg_match( '/^[0-9]+$/D', (string) $value ) || ( (int) $value && ! wp_attachment_is_image( (int) $value ) ) ) ) {
			$invalid = true;
		}
		if ( 'actions' === $type && is_wp_error( gnn_sanitize_actions( (string) $value ) ) ) {
			$invalid = true; }
		if ( 'hex' === $type && '' !== $value && ! sanitize_hex_color( $value ) ) {
			$invalid = true;
		}
		if ( 'region' === $type && ( ! preg_match( '/^-?[0-9]+$/D', (string) $value ) || (int) $value < -2 || ( (int) $value > 0 && ( 'gnn_layout' !== get_post_type( $value ) || 'publish' !== get_post_status( $value ) ) ) ) ) {
			$invalid = true;
		}
		if ( in_array( $type, array( 'layout', 'font', 'slide_group' ), true ) && ! preg_match( '/^[0-9]+$/D', (string) $value ) ) {
			$invalid = true; }
		if ( ! $invalid && $value && 'layout' === $type && ( 'gnn_layout' !== get_post_type( $value ) || 'publish' !== get_post_status( $value ) ) ) {
			$invalid = true; }
		if ( ! $invalid && $value && 'font' === $type && 'font/woff2' !== get_post_mime_type( $value ) ) {
			$invalid = true; }
		if ( $invalid ) {
			/* translators: %s: field name; %d: action position. */
			return new WP_Error( 'gnn_field_' . $key, sprintf( __( 'Invalid value for %s. No settings were changed.', 'gnn' ), $field['label'] ) ); }
	}
	foreach ( $schema as $key => $field ) {
		if ( isset( $field['pattern'] ) && ! empty( $input[ $key ] ) && ! preg_match( $field['pattern'], (string) $input[ $key ] ) ) {
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
	foreach ( gnn_options_schema() as $key => $field ) {
		if ( empty( $field['strict'] ) ) {
			continue;
		}
		if ( ! array_key_exists( $key, $input ) ) {
			if ( 'bool' === $field['type'] && isset( $field['form_flag'], $input[ $field['form_flag'] ] ) ) {
				$out[ $key ] = 0; }
			continue;
		}
		list( $store, $value ) = gnn_option_sanitize_field( $key, $field, $input[ $key ] );
		if ( $store ) {
			$out[ $key ] = $value;
		}
	}
	return $out;
}

/**
 * Sanitize one submitted value by its gnn_options_schema() entry.
 *
 * Brand fields (`*_mod`, `site_icon`) write their shared value and store
 * nothing in the option. A `code` value without the entry's capability, an
 * `enum` value outside its list and invalid `actions` keep the stored value.
 *
 * @param string $key   Option key.
 * @param array  $field Schema entry.
 * @param mixed  $value Submitted value.
 * @param array  $saved Stored options before this save.
 * @return array Whether to store the value, and the value.
 */
function gnn_option_sanitize_field( $key, $field, $value, $saved = array() ) {
	switch ( $field['type'] ) {
		case 'accent_mod':
			set_theme_mod( $field['mod'], preg_replace( '/^#(.)(.)(.)$/D', '#$1$1$2$2$3$3', $value ) );
			return array( false, null );
		case 'mode_mod':
			set_theme_mod( $field['mod'], 'light' === $value ? 'light' : 'dark' );
			return array( false, null );
		case 'text_mod':
			set_theme_mod( $field['mod'], sanitize_text_field( $value ) );
			return array( false, null );
		case 'site_icon':
			if ( ! empty( $value ) ) {
				update_option( 'site_icon', absint( $value ) );
			} else {
				delete_option( 'site_icon' );
			}
			return array( false, null );
		case 'media':
			// An unchanged, already stored URL is kept even when it is off-site.
			// Older sites keep the header logo URL in theme_mods, which gnn_option() still reads.
			$stored = array_key_exists( $key, $saved ) ? $saved[ $key ] : ( in_array( $key, array( 'logo_light', 'logo_light_2x', 'logo_dark', 'logo_dark_2x' ), true ) ? get_theme_mod( $key ) : null );
			$kept   = is_string( $stored ) && '' !== $stored && $stored === $value;
			return array( true, ( $kept || gnn_is_legacy_logo_url( $key, $value ) ) ? $value : absint( $value ) );
		case 'absint':
			return array( true, absint( $value ) );
		case 'html':
			return array( true, wp_kses_post( $value ) );
		case 'color':
			$value = trim( (string) $value );
			return array( true, ( '' === $value ) ? '' : (string) sanitize_hex_color( $value ) );
		case 'textarea':
			return array( true, sanitize_textarea_field( $value ) );
		case 'enum':
			return in_array( $value, $field['allowed'], true ) ? array( true, $value ) : array( false, null );
		case 'clamp':
			return array( true, min( $field['max'], max( $field['min'], absint( $value ) ) ) );
		case 'checkbox':
		case 'bool':
			if ( 'header_overlay' === $key && '' === $value ) {
				return array( true, '' );
			}
			return array( true, empty( $value ) ? 0 : 1 );
		case 'family':
			return array( true, substr( trim( (string) preg_replace( '/[^A-Za-z0-9 ]/', '', $value ) ), 0, 60 ) );
		case 'code':
			$value = empty( $field['strip_tags'] ) ? (string) $value : wp_strip_all_tags( (string) $value );
			if ( current_user_can( $field['cap'] ) ) {
				return array( true, $value );
			}
			// add_settings_error() exists only once wp-admin/includes/template.php is loaded.
			if ( (string) ( $saved[ $key ] ?? '' ) !== $value && function_exists( 'add_settings_error' ) ) {
				add_settings_error( 'gnn_options', 'gnn_js_cap', __( 'Custom script fields were not saved: your account lacks the unfiltered_html capability.', 'gnn' ) );
			}
			return array( false, null );
		case 'hex':
			return array( true, sanitize_hex_color( $value ) );
		case 'region':
			$value = max( -2, (int) $value );
			if ( $value > 0 && ( 'gnn_layout' !== get_post_type( $value ) || 'publish' !== get_post_status( $value ) ) ) {
				$value = -1;
			}
			return array( true, $value );
		case 'optional_number':
			return array( true, '' === $value ? '' : (string) max( $field['min'], min( $field['max'], (int) $value ) ) );
		case 'number':
			$value = max( $field['min'], min( $field['max'], (int) $value ) );
			if ( isset( $field['floor'] ) && $value > 0 ) {
				$value = max( $field['floor'], $value ); }
			return array( true, $value );
		case 'email':
			return array( true, sanitize_email( $value ) );
		case 'url':
			return array( true, esc_url_raw( $value ) );
		case 'select':
			return array( true, isset( $field['choices'][ $value ] ) ? $value : $field['default'] );
		case 'layout':
			$value = absint( $value );
			if ( $value && ( 'gnn_layout' !== get_post_type( $value ) || 'publish' !== get_post_status( $value ) ) ) {
				$value = 0; }
			return array( true, $value );
		case 'font':
			$value = absint( $value );
			if ( $value && ( 'font/woff2' !== get_post_mime_type( $value ) || ! wp_get_attachment_url( $value ) ) ) {
				$value = 0; }
			return array( true, $value );
		case 'slide_group':
			return array( true, absint( $value ) );
		case 'header_elements':
			$elements = gnn_parse_header_elements( $value );
			return is_wp_error( $elements ) ? array( false, null ) : array( true, null === $elements ? '' : wp_json_encode( $elements ) );
		case 'actions':
			$value = gnn_sanitize_actions( $value );
			if ( is_wp_error( $value ) ) {
				if ( function_exists( 'add_settings_error' ) ) {
					add_settings_error( 'gnn_options', $value->get_error_code(), $value->get_error_message() );
				}
				return array( false, null );
			}
			return array( true, $value );
		default:
			return array( true, sanitize_text_field( $value ) );
	}
}

/**
 * Render schema-driven accessible controls in the existing Settings API form.
 *
 * @param string $group Settings group.
 * @param array  $exclude Keys rendered by another control.
 */
function gnn_platform_fields( $group, $exclude = array(), $only = array(), $title = null ) {
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
	$bar = null === $title ? ( $titles[ $group ] ?? __( 'Site platform', 'gnn' ) ) : $title;
	if ( '' !== $bar ) {
		if ( function_exists( 'gnn_section_bar' ) ) {
			gnn_section_bar( $bar );
		} else {
			echo '<div class="gnn-section-bar" aria-hidden="true"><span>' . esc_html( $bar ) . '</span></div>';
		}
	}
	foreach ( gnn_platform_schema() as $key => $field ) {
		if ( $field[3] !== $group || in_array( $key, $exclude, true ) || ( $only && ! in_array( $key, $only, true ) ) ) {
			continue;
		}
		if ( 'hero_engine' === $key ) {
			gnn_field_select( $key, $field[0], $field[4] );
			if ( ! shortcode_exists( 'rev_slider' ) ) {
				?>
				<p id="gnn-revslider-hint" class="description"<?php echo 'revslider' !== gnn_option( 'hero_engine' ) ? ' hidden' : ''; ?>><?php esc_html_e( 'Slider Revolution is not active.', 'gnn' ); ?></p>
				<script>
				(function () {
					var source = document.getElementById('gnn-hero_engine');
					var hint = document.getElementById('gnn-revslider-hint');
					if (source && hint) {
						source.setAttribute('aria-describedby', hint.id);
						source.addEventListener('change', function () { hint.hidden = source.value !== 'revslider'; });
					}
				}());
				</script>
				<?php
			}
			continue;
		}
		if ( 'header_overlay' === $key ) {
			gnn_field_select( $key, $field[0], gnn_header_overlay_choices() );
			continue;
		}
		if ( 'topbar_mobile' === $key ) {
			gnn_field_select( $key, $field[0], array( 1 => __( 'Show', 'gnn' ), 0 => __( 'Hide', 'gnn' ) ) );
			continue;
		}
		switch ( $field[1] ) {
			case 'bool':
				gnn_field_checkbox( $key, $field[0] );
				break;
			case 'media':
				gnn_field_media( $key, $field[0], gnn_option( $key ) );
				break;
			case 'hex':
				gnn_field_color( $key, $field[0], gnn_option( $key ), '#000000', '', true );
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
				$font_id = absint( gnn_option( $key ) );
				echo '<div class="gnn-setting-row" data-key="' . esc_attr( $key ) . '"><div class="gnn-setting-label-col"><span class="gnn-setting-label">' . esc_html__( 'Local WOFF2 font', 'gnn' ) . '</span></div><div class="gnn-setting-control-col gnn-local-font"><input type="hidden" id="gnn-' . esc_attr( $key ) . '" name="gnn_options[' . esc_attr( $key ) . ']" value="' . (int) $font_id . '"><span class="gnn-local-font-name">' . esc_html( $font_id ? get_the_title( $font_id ) : __( 'No font selected', 'gnn' ) ) . '</span><div class="gnn-media-actions"><button type="button" class="button gnn-font-pick" data-target="gnn-' . esc_attr( $key ) . '">' . esc_html__( 'Choose local WOFF2 font', 'gnn' ) . '</button><button type="button" class="button gnn-font-clear" data-target="gnn-' . esc_attr( $key ) . '"' . ( $font_id ? '' : ' hidden' ) . '>' . esc_html__( 'Remove', 'gnn' ) . '</button></div></div></div>';
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
