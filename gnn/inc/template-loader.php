<?php
/**
 * Data-driven template registry, validator, compiler and frontend identity loader.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allowed header presets from the template data contract.
 *
 * @return string[]
 */
function gnn_template_allowed_presets() {
	return array( 'classic', 'centered', 'stacked', 'transparent', 'minimal', 'shop', 'floating' );
}

/**
 * WooCommerce section IDs and variants accepted when WooCommerce is inactive
 * for templates that explicitly declare `woocommerce` in `requires`.
 *
 * @return array<string, string[]>
 */
function gnn_template_woo_sections() {
	return array(
		'product-categories' => array( 'cols-4', 'cols-3' ),
		'featured-products'  => array( 'cols-4', 'cols-3' ),
		'promo-banners'      => array( 'overlay', 'split-card' ),
		'usp-strip'          => array( 'strip', 'cards' ),
	);
}

/**
 * Check whether a value is a strict 6-digit hexadecimal color (`#rrggbb`).
 *
 * @param mixed $color Candidate color value.
 * @return bool
 */
function gnn_template_is_hex( $color ) {
	return is_string( $color ) && 1 === preg_match( '/^#[0-9a-fA-F]{6}$/D', $color );
}

/**
 * Log an invalid template definition only when WP_DEBUG is enabled.
 *
 * @param string $slug    Template key.
 * @param string $message Validation error message.
 * @return void
 */
function gnn_template_log_error( $slug, $message ) {
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- diagnostic log for invalid template data when WP_DEBUG is enabled.
		error_log( sprintf( '[GNN template %s] %s', sanitize_key( (string) $slug ), $message ) );
	}
}

/**
 * Load all PHP template data files from `inc/template-data/` once per request.
 *
 * @return void
 */
function gnn_template_load_files() {
	static $loaded = false;
	if ( $loaded ) {
		return;
	}
	$loaded = true;
	$files  = glob( get_template_directory() . '/inc/template-data/*.php' );
	if ( ! is_array( $files ) ) {
		return;
	}
	sort( $files );
	foreach ( $files as $file ) {
		require_once $file;
	}
}

/**
 * Validate a template data definition against the v1 template contract.
 *
 * @param mixed  $data          Candidate template data array.
 * @param string $expected_slug Optional registry key to match against `slug`.
 * @return true|WP_Error
 */
function gnn_template_validate( $data, $expected_slug = '' ) {
	if ( ! is_array( $data ) ) {
		return new WP_Error( 'gnn_tpl_type', 'Template data must be an array.' );
	}
	$required_keys = array( 'slug', 'label', 'description', 'requires', 'tokens', 'header', 'pages', 'menu' );
	foreach ( $required_keys as $key ) {
		if ( ! array_key_exists( $key, $data ) ) {
			return new WP_Error( 'gnn_tpl_missing_key', sprintf( 'Missing required key: %s.', $key ) );
		}
	}
	if ( ! is_string( $data['slug'] ) || '' === $data['slug'] || sanitize_key( $data['slug'] ) !== $data['slug'] ) {
		return new WP_Error( 'gnn_tpl_slug', 'Template slug must be a non-empty sanitized key.' );
	}
	if ( '' !== $expected_slug && $expected_slug !== $data['slug'] ) {
		return new WP_Error( 'gnn_tpl_slug_mismatch', 'Template slug does not match registry key.' );
	}
	if ( in_array( $data['slug'], array( 'gnn', 'pro', 'commerce', 'design', 'architecture' ), true ) ) {
		return new WP_Error( 'gnn_tpl_slug_reserved', 'Template slug conflicts with a reserved profile or theme slug.' );
	}
	if ( ! is_string( $data['label'] ) || '' === trim( $data['label'] ) ) {
		return new WP_Error( 'gnn_tpl_label', 'Template label must be a non-empty string.' );
	}
	if ( ! is_string( $data['description'] ) ) {
		return new WP_Error( 'gnn_tpl_description', 'Template description must be a string.' );
	}
	if ( ! is_array( $data['requires'] ) ) {
		return new WP_Error( 'gnn_tpl_requires', 'Template requires must be an array.' );
	}
	foreach ( $data['requires'] as $req ) {
		if ( ! is_string( $req ) || '' === $req ) {
			return new WP_Error( 'gnn_tpl_requires_item', 'Each requirement must be a non-empty string.' );
		}
	}

	$tokens = $data['tokens'];
	if ( ! is_array( $tokens ) ) {
		return new WP_Error( 'gnn_tpl_tokens', 'Template tokens must be an array.' );
	}
	foreach ( array( 'accent', 'accent_light', 'accent_ink' ) as $color_key ) {
		if ( empty( $tokens[ $color_key ] ) || ! gnn_template_is_hex( $tokens[ $color_key ] ) ) {
			return new WP_Error( 'gnn_tpl_color', sprintf( 'Invalid hex color for tokens.%s.', $color_key ) );
		}
	}
	if ( ! isset( $tokens['radius'] ) || ! is_numeric( $tokens['radius'] ) || (int) $tokens['radius'] < 0 || (int) $tokens['radius'] > 40 ) {
		return new WP_Error( 'gnn_tpl_radius', 'Token radius must be between 0 and 40.' );
	}
	foreach ( array( 'font', 'heading_font' ) as $font_key ) {
		if ( empty( $tokens[ $font_key ] ) || ! in_array( $tokens[ $font_key ], array( 'manrope', 'space' ), true ) ) {
			return new WP_Error( 'gnn_tpl_font', sprintf( 'Invalid font for tokens.%s.', $font_key ) );
		}
	}
	if ( empty( $tokens['mode'] ) || ! in_array( $tokens['mode'], array( 'light', 'dark' ), true ) ) {
		return new WP_Error( 'gnn_tpl_mode', 'Token mode must be light or dark.' );
	}
	foreach ( array( 'light', 'dark' ) as $palette_key ) {
		if ( empty( $tokens[ $palette_key ] ) || ! is_array( $tokens[ $palette_key ] ) || 6 !== count( $tokens[ $palette_key ] ) ) {
			return new WP_Error( 'gnn_tpl_palette', sprintf( 'Token palette %s must contain 6 colors.', $palette_key ) );
		}
		foreach ( $tokens[ $palette_key ] as $palette_color ) {
			if ( ! gnn_template_is_hex( $palette_color ) ) {
				return new WP_Error( 'gnn_tpl_palette_color', sprintf( 'Invalid hex color in tokens.%s.', $palette_key ) );
			}
		}
	}

	$header = $data['header'];
	if ( ! is_array( $header ) || empty( $header['preset'] ) || ! in_array( $header['preset'], gnn_template_allowed_presets(), true ) ) {
		return new WP_Error( 'gnn_tpl_header_preset', 'Invalid or missing header preset.' );
	}
	if ( isset( $header['cta_label'] ) && ! is_string( $header['cta_label'] ) ) {
		return new WP_Error( 'gnn_tpl_header_cta', 'Header cta_label must be a string.' );
	}
	if ( isset( $header['options'] ) && ! is_array( $header['options'] ) ) {
		return new WP_Error( 'gnn_tpl_header_options', 'Header options must be an array.' );
	}

	$pages = $data['pages'];
	if ( ! is_array( $pages ) || empty( $pages ) || ! isset( $pages['home'] ) ) {
		return new WP_Error( 'gnn_tpl_pages', 'Template pages must be a non-empty array containing home.' );
	}
	$sections     = function_exists( 'gnn_sections' ) ? gnn_sections() : array();
	$woo_required = in_array( 'woocommerce', $data['requires'], true );
	$woo_sections = gnn_template_woo_sections();
	foreach ( $pages as $page_key => $page ) {
		if ( ! is_string( $page_key ) || '' === $page_key || sanitize_key( $page_key ) !== $page_key ) {
			return new WP_Error( 'gnn_tpl_page_key', 'Page keys must be valid sanitized keys.' );
		}
		if ( ! is_array( $page ) || empty( $page['title'] ) || ! is_string( $page['title'] ) || ! isset( $page['sections'] ) || ! is_array( $page['sections'] ) ) {
			return new WP_Error( 'gnn_tpl_page_shape', sprintf( 'Invalid page structure for %s.', $page_key ) );
		}
		foreach ( $page['sections'] as $index => $section ) {
			if ( ! is_array( $section ) || ! isset( $section[0], $section[1] ) || ! is_string( $section[0] ) || ! is_string( $section[1] ) ) {
				return new WP_Error( 'gnn_tpl_section_shape', sprintf( 'Invalid section entry at %s[%d].', $page_key, $index ) );
			}
			$sec_id  = $section[0];
			$variant = $section[1];
			if ( isset( $sections[ $sec_id ] ) ) {
				$variants = isset( $sections[ $sec_id ]['variants'] ) && is_array( $sections[ $sec_id ]['variants'] ) ? $sections[ $sec_id ]['variants'] : array();
				if ( ! isset( $variants[ $variant ] ) ) {
					return new WP_Error( 'gnn_tpl_variant', sprintf( 'Unknown variant %s for section %s on %s.', $variant, $sec_id, $page_key ) );
				}
			} elseif ( $woo_required && isset( $woo_sections[ $sec_id ] ) ) {
				if ( ! in_array( $variant, $woo_sections[ $sec_id ], true ) ) {
					return new WP_Error( 'gnn_tpl_variant', sprintf( 'Unknown Woo variant %s for section %s on %s.', $variant, $sec_id, $page_key ) );
				}
			} else {
				return new WP_Error( 'gnn_tpl_section', sprintf( 'Unregistered section %s on %s.', $sec_id, $page_key ) );
			}
			if ( isset( $section[2] ) && ! is_array( $section[2] ) ) {
				return new WP_Error( 'gnn_tpl_overrides', sprintf( 'Section overrides must be an array at %s[%d].', $page_key, $index ) );
			}
		}
	}

	if ( ! is_array( $data['menu'] ) ) {
		return new WP_Error( 'gnn_tpl_menu', 'Template menu must be an array.' );
	}
	foreach ( $data['menu'] as $menu_page ) {
		if ( ! is_string( $menu_page ) || ! isset( $pages[ $menu_page ] ) ) {
			return new WP_Error( 'gnn_tpl_menu_page', 'Every menu entry must reference a defined page key.' );
		}
	}

	$media_map = isset( $data['media'] ) && is_array( $data['media'] ) ? $data['media'] : array();
	if ( isset( $data['media'] ) && ! is_array( $data['media'] ) ) {
		return new WP_Error( 'gnn_tpl_media', 'Template media must be an array.' );
	}
	foreach ( $media_map as $media_key => $media_item ) {
		if ( ! is_string( $media_key ) || '' === $media_key || ! is_array( $media_item ) || ! isset( $media_item['alt'] ) || ! is_string( $media_item['alt'] ) ) {
			return new WP_Error( 'gnn_tpl_media_item', 'Each media entry must define an alt string.' );
		}
	}

	if ( isset( $data['content'] ) ) {
		if ( ! is_array( $data['content'] ) ) {
			return new WP_Error( 'gnn_tpl_content', 'Template content must be an array.' );
		}
		foreach ( array( 'projects', 'posts' ) as $group ) {
			if ( isset( $data['content'][ $group ] ) ) {
				if ( ! is_array( $data['content'][ $group ] ) ) {
					return new WP_Error( 'gnn_tpl_content_group', sprintf( 'Template content.%s must be an array.', $group ) );
				}
				foreach ( $data['content'][ $group ] as $row ) {
					if ( ! is_array( $row ) || empty( $row['title'] ) || ! is_string( $row['title'] ) ) {
						return new WP_Error( 'gnn_tpl_content_row', sprintf( 'Each item in content.%s must define a title.', $group ) );
					}
					if ( isset( $row['media'] ) && '' !== $row['media'] && ! isset( $media_map[ $row['media'] ] ) ) {
						return new WP_Error( 'gnn_tpl_content_media', sprintf( 'Missing media key %s referenced in content.%s.', (string) $row['media'], $group ) );
					}
				}
			}
		}
	}

	if ( isset( $data['css'] ) && ( ! is_string( $data['css'] ) || '' === $data['css'] || sanitize_key( $data['css'] ) !== $data['css'] ) ) {
		return new WP_Error( 'gnn_tpl_css', 'Template css key must be a sanitized string.' );
	}

	return true;
}

/**
 * Return all validated templates registered via `gnn_template_registry`.
 *
 * @return array<string, array>
 */
function gnn_templates() {
	gnn_template_load_files();
	$registry = apply_filters( 'gnn_template_registry', array() );
	if ( ! is_array( $registry ) ) {
		return array();
	}
	$valid = array();
	foreach ( $registry as $slug => $provider ) {
		$slug_key = sanitize_key( (string) $slug );
		if ( '' === $slug_key || $slug_key !== (string) $slug ) {
			gnn_template_log_error( (string) $slug, 'Invalid registry key.' );
			continue;
		}
		if ( is_callable( $provider ) ) {
			$data = call_user_func( $provider );
		} elseif ( is_array( $provider ) ) {
			$data = $provider;
		} else {
			gnn_template_log_error( $slug_key, 'Template provider is neither callable nor an array.' );
			continue;
		}
		$check = gnn_template_validate( $data, $slug_key );
		if ( is_wp_error( $check ) ) {
			gnn_template_log_error( $slug_key, $check->get_error_message() );
			continue;
		}
		$valid[ $slug_key ] = $data;
	}
	return $valid;
}

/**
 * Fetch one validated template definition by slug.
 *
 * @param string $slug Template slug.
 * @return array|null
 */
function gnn_template( $slug ) {
	$slug      = sanitize_key( (string) $slug );
	$templates = gnn_templates();
	return isset( $templates[ $slug ] ) ? $templates[ $slug ] : null;
}

/**
 * Check whether all plugin prerequisites for a template are currently active.
 *
 * @param array $data Validated template data.
 * @return bool
 */
function gnn_template_requires_met( $data ) {
	$requires = isset( $data['requires'] ) && is_array( $data['requires'] ) ? $data['requires'] : array( 'elementor' );
	foreach ( $requires as $dependency ) {
		if ( 'elementor' === $dependency ) {
			if ( ! function_exists( 'gnn_starter_elementor_available' ) || ! gnn_starter_elementor_available() ) {
				return false;
			}
		} elseif ( 'woocommerce' === $dependency ) {
			if ( ! class_exists( 'WooCommerce' ) ) {
				return false;
			}
		} else {
			return false;
		}
	}
	return true;
}

/**
 * Resolve `media:key` references to an uploaded attachment URL, bundled file URL, or placeholder SVG.
 *
 * @param string $key        Media key.
 * @param string $slug       Template slug.
 * @param array  $media_urls Optional map of media key => resolved URL.
 * @return string
 */
function gnn_template_resolve_media_url( $key, $slug, $media_urls = array() ) {
	$key = sanitize_key( (string) $key );
	if ( '' !== $key && isset( $media_urls[ $key ] ) && '' !== (string) $media_urls[ $key ] ) {
		return (string) $media_urls[ $key ];
	}
	$relative = 'assets/images/demo/' . sanitize_key( $slug ) . '/' . $key . '.jpg';
	if ( '' !== $key && is_readable( get_theme_file_path( $relative ) ) ) {
		return get_theme_file_uri( $relative );
	}
	return get_theme_file_uri( 'assets/img/placeholder-cover.svg' );
}

/**
 * Recursively resolve `media:key` and `page:key` tokens inside section override arrays.
 *
 * @param array  $overrides  Section override parameters.
 * @param string $slug       Template slug.
 * @param array  $page_ids   Map of page key => post ID.
 * @param array  $media_urls Map of media key => URL.
 * @param array  $media_meta Template media definitions for alt text fallback.
 * @return array
 */
function gnn_template_resolve_overrides( $overrides, $slug, $page_ids = array(), $media_urls = array(), $media_meta = array() ) {
	$resolved = array();
	foreach ( $overrides as $k => $value ) {
		if ( is_array( $value ) ) {
			$resolved[ $k ] = gnn_template_resolve_overrides( $value, $slug, $page_ids, $media_urls, $media_meta );
			continue;
		}
		if ( is_string( $value ) ) {
			if ( 0 === strpos( $value, 'page:' ) ) {
				$page_key       = sanitize_key( substr( $value, 5 ) );
				$resolved[ $k ] = isset( $page_ids[ $page_key ] ) ? (string) get_permalink( $page_ids[ $page_key ] ) : home_url( '/' );
				continue;
			}
			if ( 0 === strpos( $value, 'media:' ) ) {
				$media_key      = sanitize_key( substr( $value, 6 ) );
				$resolved[ $k ] = gnn_template_resolve_media_url( $media_key, $slug, $media_urls );
				if ( 'image' === $k && empty( $overrides['image_alt'] ) && isset( $media_meta[ $media_key ]['alt'] ) ) {
					$resolved['image_alt'] = (string) $media_meta[ $media_key ]['alt'];
				}
				continue;
			}
		}
		$resolved[ $k ] = $value;
	}
	return $resolved;
}

/**
 * Compile one template page into a flat list of Elementor Free elements.
 *
 * @param array  $data       Validated template definition.
 * @param string $page_key   Page key inside `$data['pages']`.
 * @param array  $page_ids   Map of page key => post ID.
 * @param array  $media_urls Optional map of media key => URL.
 * @return array
 */
function gnn_template_compile_page( $data, $page_key, $page_ids = array(), $media_urls = array() ) {
	if ( empty( $data['pages'][ $page_key ]['sections'] ) || ! is_array( $data['pages'][ $page_key ]['sections'] ) ) {
		return array();
	}
	if ( ! function_exists( 'gnn_section_build' ) ) {
		return array();
	}
	$slug       = (string) $data['slug'];
	$media_meta = isset( $data['media'] ) && is_array( $data['media'] ) ? $data['media'] : array();
	$elements   = array();
	foreach ( $data['pages'][ $page_key ]['sections'] as $section ) {
		if ( ! is_array( $section ) || ! isset( $section[0], $section[1] ) ) {
			continue;
		}
		$sec_id    = (string) $section[0];
		$variant   = (string) $section[1];
		$overrides = isset( $section[2] ) && is_array( $section[2] ) ? $section[2] : array();
		$overrides = gnn_template_resolve_overrides( $overrides, $slug, $page_ids, $media_urls, $media_meta );

		$overrides['variant'] = $variant;
		$built                = gnn_section_build( $sec_id, $overrides );
		if ( ! empty( $built ) && is_array( $built ) ) {
			$elements = array_merge( $elements, $built );
		}
	}
	return $elements;
}

/**
 * Append validated data-driven templates to `gnn_starter_profiles()` without altering the existing 4 profiles.
 *
 * @param array $profiles Existing starter profiles.
 * @return array
 */
function gnn_template_register_starter_profiles( $profiles ) {
	foreach ( gnn_templates() as $slug => $tpl ) {
		if ( isset( $profiles[ $slug ] ) ) {
			continue;
		}
		$tokens            = $tpl['tokens'];
		$header            = $tpl['header'];
		$options           = isset( $header['options'] ) && is_array( $header['options'] ) ? $header['options'] : array();
		$profiles[ $slug ] = array(
			'label'        => $tpl['label'],
			'description'  => $tpl['description'],
			'accent'       => $tokens['accent'],
			'accent_light' => $tokens['accent_light'],
			'accent_ink'   => $tokens['accent_ink'],
			'header'       => $header['preset'],
			'radius'       => (int) $tokens['radius'],
			'font'         => $tokens['font'],
			'heading_font' => $tokens['heading_font'],
			'mode'         => $tokens['mode'],
			'light'        => $tokens['light'],
			'dark'         => $tokens['dark'],
			'options'      => array_merge(
				$options,
				array(
					'header_cta_label' => isset( $header['cta_label'] ) ? (string) $header['cta_label'] : '',
				)
			),
			'requires'     => $tpl['requires'],
		);
	}
	return $profiles;
}
add_filter( 'gnn_starter_profiles', 'gnn_template_register_starter_profiles' );

/**
 * Add the `gnn-site-<slug>` body class when the active site segment is a registered template.
 *
 * @param string[] $classes Existing body classes.
 * @return string[]
 */
function gnn_template_body_class( $classes ) {
	if ( ! function_exists( 'gnn_option' ) || ! function_exists( 'gnn_starter_profile_key' ) ) {
		return $classes;
	}
	$segment = gnn_starter_profile_key( (string) gnn_option( 'site_segment' ) );
	if ( '' !== $segment && null !== gnn_template( $segment ) ) {
		$class = 'gnn-site-' . sanitize_html_class( $segment );
		if ( ! in_array( $class, $classes, true ) ) {
			$classes[] = $class;
		}
	}
	return $classes;
}
add_filter( 'body_class', 'gnn_template_body_class' );

/**
 * Enqueue template identity CSS when the active site segment or viewed page matches a template.
 *
 * @return void
 */
function gnn_template_enqueue_assets() {
	if ( ! function_exists( 'gnn_option' ) || ! function_exists( 'gnn_starter_profile_key' ) ) {
		return;
	}
	$candidates = array();
	$segment    = gnn_starter_profile_key( (string) gnn_option( 'site_segment' ) );
	if ( '' !== $segment ) {
		$candidates[] = $segment;
	}
	$post = is_singular() ? get_queried_object() : null;
	if ( $post instanceof WP_Post ) {
		$starter = (string) get_post_meta( $post->ID, '_gnn_starter', true );
		if ( false !== strpos( $starter, ':' ) ) {
			$candidates[] = gnn_starter_profile_key( explode( ':', $starter, 2 )[0] );
		}
	}
	$ver = defined( 'GNN_VERSION' ) ? GNN_VERSION : '1.0.0';
	foreach ( array_unique( array_filter( $candidates ) ) as $slug ) {
		$tpl = gnn_template( $slug );
		if ( ! $tpl || empty( $tpl['css'] ) ) {
			continue;
		}
		$css_name = sanitize_file_name( (string) $tpl['css'] );
		if ( '' === $css_name ) {
			continue;
		}
		$relative = 'assets/css/templates/' . $css_name . '.css';
		if ( is_readable( get_theme_file_path( $relative ) ) ) {
			wp_enqueue_style(
				'gnn-template-' . sanitize_key( $css_name ),
				get_theme_file_uri( $relative ),
				array( 'gnn-design-system' ),
				$ver
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'gnn_template_enqueue_assets', 18 );

/**
 * Hold and restore a registered template's `site_segment` slug around `gnn_options_sanitize()`
 * without modifying `inc/platform.php`.
 *
 * @param string|null $slug Slug to hold, or null to read.
 * @param bool        $clear Reset held slug.
 * @return string|null
 */
function gnn_template_held_segment( $slug = null, $clear = false ) {
	static $held = null;
	if ( $clear ) {
		$held = null;
		return null;
	}
	if ( null !== $slug ) {
		$held = $slug;
	}
	return $held;
}

/**
 * Temporarily substitute a valid template slug before strict `site_segment` choice validation.
 *
 * @param mixed $value Option payload passed to `sanitize_option_gnn_options`.
 * @return mixed
 */
function gnn_template_sanitize_segment_pre( $value ) {
	gnn_template_held_segment( null, true );
	if ( is_array( $value ) && isset( $value['site_segment'] ) && is_string( $value['site_segment'] ) ) {
		$segment = sanitize_key( $value['site_segment'] );
		if ( '' !== $segment && null !== gnn_template( $segment ) ) {
			gnn_template_held_segment( $segment );
			$value['site_segment'] = 'general';
		}
	}
	return $value;
}
add_filter( 'sanitize_option_gnn_options', 'gnn_template_sanitize_segment_pre', 5 );

/**
 * Restore the held template `site_segment` slug when `gnn_options_sanitize()` succeeds.
 *
 * @param mixed $value Sanitized option payload.
 * @return mixed
 */
function gnn_template_sanitize_segment_post( $value ) {
	$held = gnn_template_held_segment();
	gnn_template_held_segment( null, true );
	if ( null !== $held && is_array( $value ) && ( ! function_exists( 'gnn_configuration_rejection' ) || ! gnn_configuration_rejection() ) ) {
		$value['site_segment'] = $held;
	}
	return $value;
}
add_filter( 'sanitize_option_gnn_options', 'gnn_template_sanitize_segment_post', 15 );
