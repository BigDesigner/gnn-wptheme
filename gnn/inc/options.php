<?php
/**
 * GNN Panel — option storage and every front-end consumer.
 *
 * One autoloaded array option (`gnn_options`), zero framework.
 * Shared brand values (accent, default theme mode, footer legal) live in
 * theme_mods so the Customizer live-preview stays the single source; the
 * panel writes to the same mods.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Option defaults, derived from gnn_options_schema(). Performance switches
 * default ON — light is the goal.
 *
 * @return array
 */
function gnn_option_defaults() {
	$out = array();
	foreach ( gnn_options_schema() as $key => $field ) {
		if ( array_key_exists( 'default', $field ) ) {
			$out[ $key ] = $field['default'];
		}
	}
	return $out;
}

/**
 * The one declarative contract for every `gnn_options` key, plus the shared
 * brand fields the panel form submits (stored as theme_mods, not in the
 * option). gnn_options_sanitize() and gnn_option_defaults() are driven by it.
 *
 * Entry keys:
 * - type      Sanitizer, see gnn_option_sanitize_field().
 * - default   Stored default. Entries without one are not stored in the option.
 * - min/max   Clamp bounds for `clamp`, `number` and `optional_number`.
 * - allowed   `enum` values (strict comparison; other values keep the stored value).
 * - choices   `select` values keyed by value (strict entries only).
 * - cap       Capability required to change a `code` value.
 * - strict    Validated by gnn_validate_platform_input() before anything is
 *             written; an invalid value rejects the whole save.
 * - pattern   Non-empty values must match, otherwise the whole save is rejected.
 * - mod       Theme mod written by a brand field.
 *
 * Strict entries come from gnn_platform_schema(), which also renders those
 * controls, and are listed first. The remaining entries follow in stored key
 * order; keys declared by the platform schema are not repeated below.
 *
 * @return array
 */
function gnn_options_schema() {
	$site   = function_exists( 'gnn_site_schema' ) ? gnn_site_schema() : array();
	$schema = array();
	foreach ( gnn_platform_schema() as $key => $field ) {
		$entry = array(
			'type'    => $field[1],
			'default' => $field[2],
			'label'   => $field[0],
			'group'   => $field[3],
			'strict'  => true,
		);
		if ( 'select' === $field[1] ) {
			$entry['choices'] = $field[4];
		} elseif ( isset( $field[5] ) ) {
			$entry['min'] = $field[4];
			$entry['max'] = $field[5];
		}
		// A full panel form posts `platform_fields_present`; unchecked boxes are then off.
		if ( 'bool' === $field[1] && ! isset( $site[ $key ] ) ) {
			$entry['form_flag'] = 'platform_fields_present';
		}
		// A positive listing width is never narrower than a readable column.
		if ( 'blog_width' === $key ) {
			$entry['floor'] = 360;
		}
		$schema[ $key ] = $entry;
	}

	$checkbox = static function ( $default ) {
		return array(
			'type'    => 'checkbox',
			'default' => $default,
		);
	};
	$clamp    = static function ( $default, $min, $max ) {
		return array(
			'type'    => 'clamp',
			'default' => $default,
			'min'     => $min,
			'max'     => $max,
		);
	};
	$enum     = static function ( $default, $allowed ) {
		return array(
			'type'    => 'enum',
			'default' => $default,
			'allowed' => $allowed,
		);
	};
	$code     = static function ( $strip_tags = false ) {
		return array(
			'type'       => 'code',
			'default'    => '',
			'cap'        => 'unfiltered_html',
			'strip_tags' => $strip_tags,
		);
	};
	$typed    = static function ( $type, $default = '' ) {
		return array(
			'type'    => $type,
			'default' => $default,
		);
	};
	$text     = $typed( 'text' );
	$color    = $typed( 'color' );
	$media    = $typed( 'media', 0 );
	$aligns   = array( 'left', 'center', 'right' );

	$legacy = array(
		// Shared brand values live in theme_mods (Customizer live preview); not stored here.
		'accent'                       => array(
			'type'        => 'accent_mod',
			'mod'         => 'gnn_accent_color',
			'require_hex' => __( 'Enter a valid hex color.', 'gnn' ),
		),
		'default_mode'                 => array(
			'type' => 'mode_mod',
			'mod'  => 'gnn_default_theme',
		),
		'footer_legal'                 => array(
			'type' => 'text_mod',
			'mod'  => 'gnn_footer_legal',
		),
		'site_icon'                    => array( 'type' => 'site_icon' ),
		// Brand / logo (existing — do not remove).
		'logo_light'                   => $media,
		'logo_light_2x'                => $media,
		'logo_dark'                    => $media,
		'logo_dark_2x'                 => $media,
		'logo_max_height'              => $clamp( 40, 12, 200 ),
		'logo_max_height_mobile'       => $clamp( 34, 12, 200 ),
		'footer_copyright'             => $text,
		// Theme mode.
		'show_toggle'                  => $checkbox( 1 ),
		'remember_mode'                => $checkbox( 1 ),
		// Header.
		'sticky_header'                => $checkbox( 1 ),
		'show_search'                  => $checkbox( 1 ),
		'show_cart'                    => $checkbox( 1 ),
		'header_layout'                => $enum( 'standard', array( 'standard', 'centered' ) ),
		'header_menu_align'            => $enum( 'right', $aligns ),
		'mobile_dock'                  => $checkbox( 0 ),
		'topbar_enable'                => $checkbox( 0 ),
		'topbar_text'                  => $text,
		'topbar_email'                 => $typed( 'email' ),
		'topbar_phone'                 => $text,
		'topbar_bg'                    => $color,
		'topbar_text_color'            => $color,
		// Footer.
		'footer_brand_type'            => $enum( 'text', array( 'text', 'image', 'both', 'none' ) ),
		'footer_logo_light'            => $media,
		'footer_logo_light_2x'         => $media,
		'footer_logo_dark'             => $media,
		'footer_logo_dark_2x'          => $media,
		'footer_logo_height'           => $clamp( 40, 12, 300 ),
		'footer_tagline'               => $typed( 'html' ),
		'footer_menu_align'            => $enum( 'left', $aligns ),
		// Blog / shop. Post featured image and excerpt keys are blog schema entries.
		'sidebar_position'             => $enum( 'right', array( 'right', 'left', 'none' ) ),
		'contact_page_id'              => $typed( 'absint', 0 ), // 0 = auto-detect (see gnn_contact_url()).
		'content_top_padding'          => $clamp( 50, 0, 200 ),
		'content_bottom_padding'       => $clamp( 64, 0, 300 ),
		'page_featured_image_height'   => $clamp( 250, 80, 600 ),
		'page_featured_image_fit'      => $enum( 'cover', array( 'cover', 'contain', 'fill' ) ),
		'page_featured_image_position' => $enum( 'center', array( 'top', 'center', 'bottom' ) ),
		// Title overlay: page title rendered centered inside the featured
		// image instead of in the normal content flow. Off by default,
		// per-page overridable — see inc/page-meta.php.
		'title_overlay_enable'         => $checkbox( 0 ),
		'title_overlay_font_size'      => $clamp( 40, 16, 120 ),
		'title_overlay_bg'             => $typed( 'color', '#000000' ),
		'title_overlay_bg_opacity'     => $clamp( 55, 0, 100 ),
		// Layout widths (emitted as CSS vars by gnn_scripts()).
		'sidebar_width'                => $clamp( 320, 200, 500 ),
		'boxed_width'                  => $clamp( 1200, 600, 1600 ),
		'full_width'                   => $clamp( 1520, 900, 1920 ),
		'shop_columns'                 => $clamp( 4, 2, 6 ),
		'shop_per_page'                => $clamp( 8, 2, 48 ),
		// 404.
		'error404_title'               => $text,
		'error404_text'                => $typed( 'textarea' ),
		'error404_search'              => $checkbox( 1 ),
		'error404_button'              => $text,
		'error404_image'               => $media,
		// UX toggles.
		'smooth_scroll'                => $checkbox( 1 ),
		'scroll_top'                   => $checkbox( 1 ),
		'scroll_anim'                  => $checkbox( 0 ),
		'preloader'                    => $checkbox( 0 ),
		'loading_screen'               => $checkbox( 0 ),
		// Maintenance.
		'maintenance_mode'             => $checkbox( 0 ),
		'maintenance_message'          => $typed( 'textarea' ),
		// Slider.
		'slider_autoplay'              => $checkbox( 1 ),
		'slider_interval'              => $clamp( 5, 2, 30 ),
		'slider_full_height'           => $checkbox( 0 ),
		// Custom code: without the capability the stored value is kept.
		'custom_css'                   => $code( true ),
		'custom_js_head'               => $code(),
		'custom_js_footer'             => $code(),
		'ga4_id'                       => $text + array( 'pattern' => '/^G-[A-Z0-9]{4,}$/i' ),
		'gtm_id'                       => $text + array( 'pattern' => '/^GTM-[A-Z0-9]{4,}$/i' ),
		'head_html'                    => $code(),
		'body_html'                    => $code(),
		// Icons.
		'google_material_icons'        => $checkbox( 0 ),
		'material_icons_style'         => $enum( 'outlined', array( 'outlined', 'rounded', 'sharp', 'filled' ) ),
		// Typography (Google Fonts, per element — off by default, see inc/typography.php).
		'typography_google_enable'     => $checkbox( 0 ),
	);
	foreach ( array_keys( gnn_typography_roles() ) as $role ) {
		$legacy[ "typo_{$role}_family" ] = $typed( 'family' );
		$legacy[ "typo_{$role}_weight" ] = $enum( '400', gnn_typography_weights() );
	}
	// Button style slots are site schema entries (see gnn_site_schema()).
	// Colors: Dark/Light pairs for the theme's existing design tokens
	// (empty = keep main.css's own hardcoded default for that mode).
	// Accent's dark value is the pre-existing gnn_accent_color theme_mod
	// (Customizer, rule 1 backward compat); this is only the Light half.
	foreach ( array( 'accent_light', 'accent_ink_dark', 'accent_ink_light', 'color_bg_dark', 'color_bg_light', 'color_bg2_dark', 'color_bg2_light', 'color_bg3_dark', 'color_bg3_light', 'color_fg_dark', 'color_fg_light', 'color_fg2_dark', 'color_fg2_light', 'color_line_dark', 'color_line_light' ) as $key ) {
		$legacy[ $key ] = $color;
	}
	// Performance.
	foreach ( array( 'disable_emoji', 'disable_oembed', 'disable_migrate', 'heartbeat_slow', 'woo_scope', 'font_preload' ) as $key ) {
		$legacy[ $key ] = $checkbox( 1 );
	}
	return $schema + $legacy;
}

/**
 * Read a panel option with its default.
 *
 * @param string $key Option key.
 * @return mixed
 */
function gnn_option( $key ) {
	static $defaults = null;
	if ( null === $defaults ) {
		$defaults = gnn_option_defaults();
	}
	$options = (array) get_option( 'gnn_options', array() );
	if ( array_key_exists( $key, $options ) ) {
		return $options[ $key ];
	}
	if ( 'post_meta' === $key && array_key_exists( 'blog_meta', $options ) ) {
		return (int) (bool) $options['blog_meta']; }
	// An explicit panel value of 0 takes precedence over the old theme_mod.
	if ( in_array( $key, array( 'logo_light', 'logo_light_2x', 'logo_dark', 'logo_dark_2x' ), true ) ) {
		$legacy = get_theme_mod( $key, 0 );
		if ( is_scalar( $legacy ) && $legacy ) {
			return is_numeric( $legacy ) ? absint( $legacy ) : esc_url_raw( $legacy, array( 'http', 'https' ) );
		}
	}
	return $defaults[ $key ] ?? null;
}

/**
 * Accept historical HTTP(S) logo URLs as well as modern attachment IDs.
 *
 * @param string $key   Option or theme_mod key.
 * @param mixed  $value Candidate URL.
 * @return bool Whether this is a supported legacy logo URL.
 */
function gnn_is_legacy_logo_url( $key, $value ) {
	return in_array( $key, array( 'logo_light', 'logo_light_2x', 'logo_dark', 'logo_dark_2x' ), true )
		&& is_string( $value ) && preg_match( '#^https?://#i', $value )
		&& wp_parse_url( $value, PHP_URL_HOST ) && esc_url_raw( $value ) === $value
		&& ( gnn_is_site_asset_url( $value ) || gnn_is_stored_logo_url( $key, $value ) );
}

/**
 * Whether a URL is the logo value this site already stores, in the panel
 * option or the historical theme_mod. Existing off-site logos stay valid
 * through saves and the site's own export/import; only new ones are refused.
 *
 * @param string $key   Logo key.
 * @param string $value Candidate URL.
 * @return bool
 */
function gnn_is_stored_logo_url( $key, $value ) {
	$options = get_option( 'gnn_options', array() );
	if ( is_array( $options ) && isset( $options[ $key ] ) && $value === $options[ $key ] ) {
		return true;
	}
	return $value === get_theme_mod( $key );
}

/**
 * Whether a URL points into this site's uploads or a theme directory, so a
 * stored or imported URL can never load a third-party image on every page.
 * The scheme is ignored (http/https moves keep working).
 *
 * @param string $url Candidate URL.
 * @return bool
 */
function gnn_is_site_asset_url( $url ) {
	if ( ! is_string( $url ) || ! preg_match( '#^https?://#i', $url ) ) {
		return false;
	}
	$strip   = function ( $value ) {
		return strtolower( (string) preg_replace( '#^https?:#i', '', untrailingslashit( (string) $value ) ) ) . '/';
	};
	$uploads = wp_get_upload_dir();
	$bases   = array( get_template_directory_uri(), get_stylesheet_directory_uri() );
	if ( empty( $uploads['error'] ) && ! empty( $uploads['baseurl'] ) ) {
		$bases[] = $uploads['baseurl'];
	}
	$target = strtolower( (string) preg_replace( '#^https?:#i', '', $url ) );
	foreach ( $bases as $base ) {
		if ( 0 === strpos( $target, $strip( $base ) ) ) {
			return true;
		}
	}
	return false;
}

// ----- Branding: light/dark logo pair (falls back to core custom logo / title) ----------------------------------------------------------

/**
 * Render one retina-ready logo <img>.
 *
 * The 1x attachment sets the displayed dimensions; the optional 2x attachment
 * is added as a `2x` srcset descriptor so high-DPI screens fetch the sharper
 * file. CSS caps the height (see --gnn-logo-h), width stays auto.
 *
 * @param int    $id_1x     Attachment ID for the standard (1x) logo.
 * @param int    $id_2x     Attachment ID for the retina (2x) logo, or 0.
 * @param string $css_class Extra class (mode selector).
 * @param string $priority  'high' (eager, fetchpriority high), 'eager' or 'lazy'.
 * @return string HTML, or '' when no 1x id.
 */
function gnn_logo_img( $id_1x, $id_2x, $css_class, $priority = 'eager' ) {
	if ( ! $id_1x ) {
		return '';
	}
	// Off-site URLs are refused when saved or imported (gnn_is_legacy_logo_url());
	// values already stored keep rendering so existing sites keep their logo.
	$src = is_numeric( $id_1x ) ? wp_get_attachment_image_url( (int) $id_1x, 'full' ) : esc_url_raw( $id_1x, array( 'http', 'https' ) );
	if ( ! $src ) {
		return '';
	}
	$loading = 'lazy' === $priority ? ' loading="lazy"' : ' loading="eager"';
	if ( 'high' === $priority ) {
		$loading .= ' fetchpriority="high"';
	}
	$meta   = is_numeric( $id_1x ) ? wp_get_attachment_metadata( (int) $id_1x ) : array();
	$width  = ! empty( $meta['width'] ) ? (int) $meta['width'] : null;
	$height = ! empty( $meta['height'] ) ? (int) $meta['height'] : null;
	$alt    = get_bloginfo( 'name' );

	$srcset = '';
	$src_2x = $id_2x ? ( is_numeric( $id_2x ) ? wp_get_attachment_image_url( (int) $id_2x, 'full' ) : esc_url_raw( $id_2x, array( 'http', 'https' ) ) ) : '';
	if ( $src_2x ) {
		$srcset = ' srcset="' . esc_url( $src ) . ' 1x, ' . esc_url( $src_2x ) . ' 2x"';
	}

	return sprintf(
		'<img class="gnn-logo %1$s" src="%2$s"%3$s%4$s%5$s alt="%6$s" decoding="async"%7$s>',
		esc_attr( $css_class ),
		esc_url( $src ),
		$srcset, // Already escaped above.
		$width ? ' width="' . esc_attr( $width ) . '"' : '',
		$height ? ' height="' . esc_attr( $height ) . '"' : '',
		esc_attr( $alt ),
		$loading // Fixed attribute strings.
	);
}

/**
 * Output the site logo / title, honoring the panel's light+dark logo pair
 * with retina (2x) variants.
 */
function gnn_the_logo() {
	$light    = gnn_option( 'logo_light' );
	$light_2x = gnn_option( 'logo_light_2x' );
	$dark     = gnn_option( 'logo_dark' );
	$dark_2x  = gnn_option( 'logo_dark_2x' );

	$markup = gnn_logo_pair( $light, $light_2x, $dark, $dark_2x, '', true );
	if ( '' !== $markup ) {
		$height        = max( 12, (int) gnn_option( 'logo_max_height' ) );
		$height_mobile = max( 12, (int) gnn_option( 'logo_max_height_mobile' ) );
		echo '<a class="site-logo" href="' . esc_url( home_url( '/' ) ) . '" rel="home" aria-label="' . esc_attr( get_bloginfo( 'name' ) ) . '" style="--gnn-logo-h:' . esc_attr( $height ) . 'px;--gnn-logo-h-mobile:' . esc_attr( $height_mobile ) . 'px">';
		echo $markup; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in gnn_logo_pair/gnn_logo_img.
		echo '</a>';
		return;
	}

	// No panel logo set: fall back to the site title (core custom-logo is not
	// registered — logos live in GNN Theme Panel → Brand).
	?>
	<a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
		<?php bloginfo( 'name' ); ?>
		<?php
		if ( gnn_option( 'brand_dot' ) ) :
			?>
			<span class="site-title__dot">.</span><?php endif; ?>
	</a>
	<?php
}

/**
 * Render valid mode logos, or one shared image visible in both color modes.
 *
 * @param int|string $light    Light-mode logo.
 * @param int|string $light_2x Light-mode retina logo.
 * @param int|string $dark     Dark-mode logo.
 * @param int|string $dark_2x  Dark-mode retina logo.
 * @param string     $extra    Additional image classes.
 * @param bool       $lead     Above-the-fold logo: the default mode's image (or
 *                             the shared one) loads eagerly with high priority.
 *                             The other mode's image is lazy, so it downloads
 *                             only once a visitor switches to that mode.
 * @return string Escaped markup, or empty when no source resolves.
 */
function gnn_logo_pair( $light, $light_2x, $dark, $dark_2x, $extra = '', $lead = false ) {
	$default      = 'light' === get_theme_mod( 'gnn_default_theme', 'dark' ) ? 'light' : 'dark';
	$first        = $lead ? 'high' : 'lazy';
	$light_markup = gnn_logo_img( $light, $light_2x, 'gnn-logo--on-light ' . $extra, 'light' === $default ? $first : 'lazy' );
	$dark_markup  = gnn_logo_img( $dark, $dark_2x, 'gnn-logo--on-dark ' . $extra, 'dark' === $default ? $first : 'lazy' );
	if ( '' === $light_markup || '' === $dark_markup || ( $light === $dark && $light_2x === $dark_2x ) ) {
		// One image serves both modes, so it takes the default mode's priority.
		$shared = $dark_markup ? gnn_logo_img( $dark, $dark_2x, 'gnn-logo--shared ' . $extra, $first ) : gnn_logo_img( $light, $light_2x, 'gnn-logo--shared ' . $extra, $first );
		return $shared;
	}
	return $dark_markup . $light_markup;
}

/**
 * Output the footer logo pair (light/dark, retina) at the panel-set width.
 * Returns false when no footer logo is configured.
 *
 * @return bool True if a logo was output.
 */
function gnn_the_footer_logo() {
	$light    = (int) gnn_option( 'footer_logo_light' );
	$light_2x = (int) gnn_option( 'footer_logo_light_2x' );
	$dark     = (int) gnn_option( 'footer_logo_dark' );
	$dark_2x  = (int) gnn_option( 'footer_logo_dark_2x' );

	$markup = gnn_logo_pair( $light, $light_2x, $dark, $dark_2x, 'gnn-footer-logo' );
	if ( '' === $markup ) {
		return false;
	}

	$height = max( 12, (int) gnn_option( 'footer_logo_height' ) );

	echo '<a class="footer-logo-link" href="' . esc_url( home_url( '/' ) ) . '" rel="home" style="--gnn-footer-logo-h:' . esc_attr( $height ) . 'px;">';
	echo $markup; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in gnn_logo_pair/gnn_logo_img.
	echo '</a>';
	return true;
}

// ----- Color tokens (Dark/Light pairs, GNN Panel → Colors) ----------------------------------------------------------

/**
 * Map of {option key prefix => CSS custom property} for the surface and
 * text Dark/Light color pairs exposed in the panel (see gnn_field_color_pair()
 * calls in inc/admin-panel.php's Colors tab).
 *
 * The accent pair (gnn_accent_color / accent_light) and accent_ink_dark /
 * accent_ink_light are emitted only by gnn_brand_design_tokens() in
 * inc/design-system.php, so --accent, --accent-ink and --accent-text each
 * have exactly one source.
 *
 * @return array<string,string>
 */
function gnn_color_token_map() {
	return array(
		'color_bg'   => '--bg',
		'color_bg2'  => '--bg2',
		'color_bg3'  => '--bg3',
		'color_fg'   => '--fg',
		'color_fg2'  => '--fg2',
		'color_line' => '--line',
	);
}

/**
 * Inline CSS for any Dark/Light color pair an admin has actually set.
 * Unset pairs are omitted entirely, so main.css's own hardcoded :root /
 * [data-theme="light"] defaults keep applying untouched.
 */
function gnn_color_tokens_inline_css() {
	$dark_css  = '';
	$light_css = '';
	foreach ( gnn_color_token_map() as $key => $var ) {
		$dark = trim( (string) gnn_option( "{$key}_dark" ) );
		if ( '' !== $dark ) {
			$dark_css .= $var . ':' . esc_html( $dark ) . ';';
		}
		$light = trim( (string) gnn_option( "{$key}_light" ) );
		if ( '' !== $light ) {
			$light_css .= $var . ':' . esc_html( $light ) . ';';
		}
	}
	$css = '';
	if ( '' !== $dark_css ) {
		$css .= ':root{' . $dark_css . '}';
	}
	if ( '' !== $light_css ) {
		$css .= '[data-theme="light"]{' . $light_css . '}';
	}
	if ( '' !== $css ) {
		wp_add_inline_style( 'gnn-main', $css );
	}
}
add_action( 'wp_enqueue_scripts', 'gnn_color_tokens_inline_css', 15 );

// ----- Layout switches → body classes ----------------------------------------------------------

/**
 * Panel layout switches as body classes.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function gnn_option_body_classes( $classes ) {
	if ( ! gnn_option( 'sticky_header' ) ) {
		$classes[] = 'gnn-no-sticky';
	}
	if ( 'left' === gnn_option( 'sidebar_position' ) ) {
		$classes[] = 'gnn-sidebar-left';
	}
	if ( 'legacy' === gnn_layout_value( 'header_preset' ) ) {
		$classes[] = 'gnn-header-' . sanitize_html_class( (string) gnn_option( 'header_layout' ) ); }
	$classes[] = 'gnn-menu-' . sanitize_html_class( (string) gnn_option( 'header_menu_align' ) );
	$classes[] = 'gnn-fmenu-' . sanitize_html_class( (string) gnn_option( 'footer_menu_align' ) );
	if ( gnn_option( 'mobile_dock' ) ) {
		$classes[] = 'gnn-has-dock';
	}
	if ( gnn_option( 'topbar_enable' ) ) {
		$classes[] = 'gnn-has-topbar';
	}
	return $classes;
}
add_filter( 'body_class', 'gnn_option_body_classes' );

/**
 * The site's contact page URL, used by the mobile dock's Contact item
 * (and anywhere else a "get in touch" link is needed). Panel selection
 * wins; otherwise falls back to a page slugged "contact" or "iletisim",
 * then to a bare /contact/ guess.
 *
 * @return string
 */
function gnn_contact_url() {
	$page_id = gnn_translated_post_id( (int) gnn_option( 'contact_page_id' ) );
	if ( $page_id && 'publish' === get_post_status( $page_id ) ) {
		return get_permalink( $page_id );
	}
	foreach ( array( 'contact', 'iletisim' ) as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page ) {
			return get_permalink( $page );
		}
	}
	return home_url( '/contact/' );
}

/**
 * Whether the theme sidebar should render at all (panel can turn it off).
 *
 * @return bool
 */
function gnn_show_sidebar() {
	if ( ! is_admin() && -2 === (int) gnn_layout_value( 'sidebar_template' ) ) {
		return false; }
	if ( ! is_admin() && gnn_layout_value( 'sidebar_template' ) > 0 ) {
		return true; }
	return 'none' !== gnn_option( 'sidebar_position' ) && is_active_sidebar( 'sidebar-1' );
}

/**
 * Excerpt length from the panel (front-end only).
 *
 * @param int $length Default length.
 * @return int
 */
function gnn_excerpt_length( $length ) {
	return is_admin() ? $length : max( 5, (int) gnn_option( 'excerpt_length' ) );
}
add_filter( 'excerpt_length', 'gnn_excerpt_length', 20 );

// ----- Custom code + analytics output ----------------------------------------------------------

/**
 * Print the panel's custom CSS (tags stripped on save and output).
 */
function gnn_output_custom_css() {
	$css = trim( (string) gnn_option( 'custom_css' ) );
	if ( '' !== $css ) {
		wp_add_inline_style( 'gnn-style', wp_strip_all_tags( $css ) );
	}
}
add_action( 'wp_enqueue_scripts', 'gnn_output_custom_css', 20 );

/**
 * GA4 / GTM / extra head HTML / head JS output.
 */
function gnn_output_head_code() {
	$head_html = (string) gnn_option( 'head_html' );
	if ( '' !== trim( $head_html ) ) {
		echo $head_html . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- gated by unfiltered_html on save.
	}

	$js = trim( (string) gnn_option( 'custom_js_head' ) );
	if ( '' !== $js ) {
		echo '<script id="gnn-custom-js-head">' . $js . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- gated by unfiltered_html on save.
	}
}
add_action( 'wp_head', 'gnn_output_head_code', 20 );

/**
 * GTM noscript + after-<body> HTML output.
 */
function gnn_output_body_open_code() {
	$body_html = (string) gnn_option( 'body_html' );
	if ( '' !== trim( $body_html ) ) {
		echo $body_html . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- gated by unfiltered_html on save.
	}
}
add_action( 'wp_body_open', 'gnn_output_body_open_code', 20 );

/**
 * Footer custom JS output.
 */
function gnn_output_footer_code() {
	$js = trim( (string) gnn_option( 'custom_js_footer' ) );
	if ( '' !== $js ) {
		echo '<script id="gnn-custom-js-footer">' . $js . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- gated by unfiltered_html on save.
	}
}
add_action( 'wp_footer', 'gnn_output_footer_code', 99 );

// ----- Performance switches (all default ON) ----------------------------------------------------------

/**
 * Performance switches: emoji, oEmbed, Heartbeat.
 */
function gnn_perf_boot() {
	if ( gnn_option( 'disable_emoji' ) ) {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		add_filter( 'emoji_svg_url', '__return_false' );
	}

	if ( gnn_option( 'disable_oembed' ) && ! gnn_option( 'compatibility_mode' ) ) {
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	}

	if ( gnn_option( 'heartbeat_slow' ) ) {
		add_filter(
			'heartbeat_settings',
			function ( $settings ) {
				$settings['interval'] = 60;
				return $settings;
			}
		);
	}
}
add_action( 'init', 'gnn_perf_boot' );

/**
 * Front-end script diet: drop jQuery Migrate when enabled.
 */
function gnn_perf_scripts() {
	if ( is_admin() ) {
		return;
	}

	// jQuery Migrate off (front-end only).
	if ( gnn_option( 'disable_migrate' ) && ! gnn_option( 'compatibility_mode' ) ) {
		global $wp_scripts;
		if ( isset( $wp_scripts->registered['jquery'] ) ) {
			$deps = &$wp_scripts->registered['jquery']->deps;
			$deps = array_diff( $deps, array( 'jquery-migrate' ) );
		}
	}
}
add_action( 'wp_default_scripts', 'gnn_perf_scripts' );

/**
 * Keep WooCommerce assets off pages that don't use them.
 * A page "uses" Woo when it's a Woo page or its content contains a Woo
 * shortcode / add-to-cart markup (e.g. the front page's [products] grid).
 */
function gnn_scope_woo_assets() {
	if ( ! class_exists( 'WooCommerce' ) || ! gnn_option( 'woo_scope' ) || is_admin() ) {
		return;
	}
	if ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) {
		return;
	}
	global $wp_query;
	$posts = isset( $wp_query->posts ) ? $wp_query->posts : array( get_post() );
	$uses  = gnn_woocommerce_widgets_active();
	foreach ( $posts as $post ) {
		if ( $post instanceof WP_Post && ( gnn_content_uses_woocommerce( $post->post_content )
			|| 'builder' === get_post_meta( $post->ID, '_elementor_edit_mode', true ) ) ) {
			$uses = true;
			break;
		}
	}
	if ( $uses || apply_filters( 'gnn_page_uses_woocommerce', false ) ) {
		return;
	}

	foreach ( array( 'woocommerce-general', 'woocommerce-layout', 'woocommerce-smallscreen', 'wc-blocks-style', 'brands-styles', 'gnn-woocommerce' ) as $style ) {
		wp_dequeue_style( $style );
	}
	// The header cart count is rendered server-side, but full-page caches can
	// serve a stale count, so cart fragments stay wherever the count is shown.
	foreach ( array( 'wc-cart-fragments', 'woocommerce', 'wc-add-to-cart', 'sourcebuster-js', 'wc-order-attribution' ) as $script ) {
		if ( 'wc-cart-fragments' === $script && ( gnn_option( 'show_cart' ) || gnn_option( 'mobile_dock' ) ) ) {
			wp_enqueue_script( $script );
			continue;
		}
		wp_dequeue_script( $script );
	}
}
add_action( 'wp_enqueue_scripts', 'gnn_scope_woo_assets', 99 );

/**
 * Detect Woo content, including referenced blocks, without rendering it.
 *
 * @param string $content Raw content.
 * @param array  $seen Referenced block IDs already inspected.
 * @return bool
 */
function gnn_content_uses_woocommerce( $content, $seen = array() ) {
	if ( false !== strpos( $content, 'wp:woocommerce/' ) || false !== strpos( $content, 'add-to-cart' ) ) {
		return true;
	}
	$shortcodes = array(
		'products',
		'product',
		'product_page',
		'product_category',
		'product_categories',
		'add_to_cart',
		'add_to_cart_url',
		'recent_products',
		'featured_products',
		'sale_products',
		'best_selling_products',
		'top_rated_products',
		'product_attribute',
		'related_products',
		'shop_messages',
		'woocommerce_messages',
		'woocommerce_cart',
		'woocommerce_checkout',
		'woocommerce_my_account',
		'woocommerce_order_tracking',
	);
	foreach ( $shortcodes as $shortcode ) {
		// Detection must also work before a plugin registers its shortcode callback.
		if ( preg_match( '/\[(?!\[)\s*' . preg_quote( $shortcode, '/' ) . '(?=[\s\]\/])/', $content ) ) {
			return true;
		}
	}
	$blocks = parse_blocks( $content );
	while ( $blocks ) {
		$block = array_pop( $blocks );
		if ( in_array( $block['blockName'], array( 'core/template-part', 'core/pattern', 'core/post-content', 'core/query' ), true ) ) {
			return true; // Dynamic content cannot be ruled out before it is rendered.
		}
		if ( 'core/block' === $block['blockName'] && ! empty( $block['attrs']['ref'] ) ) {
			$id = absint( $block['attrs']['ref'] );
			if ( ! in_array( $id, $seen, true ) ) {
				$reference = get_post( $id );
				if ( ! $reference || count( $seen ) >= 20 ) {
					return true;
				}
				if ( gnn_content_uses_woocommerce( $reference->post_content, array_merge( $seen, array( $id ) ) ) ) {
					return true;
				}
			}
		}
		$blocks = array_merge( $blocks, $block['innerBlocks'] );
	}
	return false;
}

/**
 * Keep assets for active Woo widgets and widget-hosted product content.
 *
 * @return bool
 */
function gnn_woocommerce_widgets_active() {
	global $wp_registered_widgets;
	foreach ( wp_get_sidebars_widgets() as $sidebar => $widgets ) {
		if ( 'wp_inactive_widgets' === $sidebar || ! is_array( $widgets ) ) {
			continue;
		}
		foreach ( $widgets as $id ) {
			$callback = $wp_registered_widgets[ $id ]['callback'] ?? null;
			if ( is_array( $callback ) && is_object( $callback[0] ) ) {
				$class = get_class( $callback[0] );
				if ( 0 === strpos( $class, 'WC_Widget_' ) || false !== strpos( $class, 'WooCommerce' ) || false !== strpos( $class, 'Elementor' ) ) {
					return true;
				}
			}
			if ( preg_match( '/^(block|text|custom_html)-(\d+)$/', $id, $matches ) ) {
				$settings = get_option( 'widget_' . $matches[1], array() );
				$row      = $settings[ (int) $matches[2] ] ?? array();
				if ( gnn_content_uses_woocommerce( (string) ( $row['content'] ?? $row['text'] ?? '' ) ) ) {
					return true;
				}
			}
		}
	}
	return false;
}

/**
 * Self-hosted font files (latin subsets) that a configured role still uses.
 *
 * The body slot (body_font) is replaced when the paragraph role uses a Google
 * font; the heading slot (heading_font) when every heading role does.
 *
 * @return string[] File names in assets/fonts/.
 */
function gnn_preload_font_files() {
	$files = array(
		'manrope' => 'manrope-latin.woff2',
		'space'   => 'space-grotesk-latin.woff2',
	);
	$slots = array();
	if ( '' === gnn_typography_role_family( 'p' ) ) {
		$slots[] = (string) gnn_option( 'body_font' );
	}
	foreach ( array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ) as $role ) {
		if ( '' === gnn_typography_role_family( $role ) ) {
			$slots[] = (string) gnn_option( 'heading_font' );
			break;
		}
	}
	$used = array();
	foreach ( $files as $font => $file ) {
		if ( in_array( $font, $slots, true ) ) {
			$used[] = $file;
		}
	}
	return $used;
}

/**
 * Preload the self-hosted font files the page will actually use.
 */
function gnn_font_preload() {
	if ( ! gnn_option( 'font_preload' ) ) {
		return;
	}
	$base = get_template_directory_uri() . '/assets/fonts/';
	foreach ( gnn_preload_font_files() as $font ) {
		echo '<link rel="preload" href="' . esc_url( $base . $font ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
	}
}
add_action( 'wp_head', 'gnn_font_preload', 2 );

// ----- Google Material Symbols / Icons ----------------------------------------------------------

/**
 * Map a panel icon style to its Google font family. "Filled" isn't a
 * separate Material Symbols family — it's the Outlined family with the
 * FILL variable axis set to 1 (see the .material-symbols-filled CSS rule).
 *
 * @param string $style One of: outlined, rounded, sharp, filled.
 * @return string Google Fonts family slug (URL-encoded).
 */
function gnn_material_icons_family( $style ) {
	$map = array(
		'outlined' => 'Material+Symbols+Outlined',
		'rounded'  => 'Material+Symbols+Rounded',
		'sharp'    => 'Material+Symbols+Sharp',
		'filled'   => 'Material+Symbols+Outlined',
	);
	return $map[ $style ] ?? $map['outlined'];
}

/**
 * Enqueue the Google Material Symbols stylesheet when enabled.
 */
function gnn_material_icons_assets() {
	if ( ! gnn_option( 'google_material_icons' ) ) {
		return;
	}
	$style  = (string) gnn_option( 'material_icons_style' );
	$style  = in_array( $style, array( 'outlined', 'rounded', 'sharp', 'filled' ), true ) ? $style : 'outlined';
	$family = gnn_material_icons_family( $style );
	$fill   = 'filled' === $style ? '1' : '0';

	wp_enqueue_style(
		'gnn-material-symbols',
		esc_url_raw( "https://fonts.googleapis.com/css2?family={$family}:opsz,wght,FILL,GRAD@20..48,400,{$fill},0&display=swap" ),
		array(),
		null // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- external CDN URL; a ?ver= query string must not be appended to it.
	);
}
add_action( 'wp_enqueue_scripts', 'gnn_material_icons_assets' );

/**
 * Preconnect to the Google Fonts hosts, once, whenever anything actually
 * requests them (Material Symbols and/or Typography's per-element Google
 * Fonts) — never printed when both features are off.
 */
function gnn_google_fonts_preconnect() {
	$needs_it = gnn_option( 'google_material_icons' )
		|| '' !== gnn_typography_google_fonts_url();
	if ( ! $needs_it ) {
		return;
	}
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
add_action( 'wp_head', 'gnn_google_fonts_preconnect', 1 );
