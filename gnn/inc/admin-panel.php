<?php
/**
 * GNN Panel — the theme's admin page (WP Admin → GNN).
 *
 * Pure WP Settings API + one tiny admin.css/admin.js pair loaded ONLY on
 * this screen. No options framework.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the admin menu: a single top-level "GNN" parent with "Theme"
 * (this panel) and "Slider" (gnn_slide CPT, see inc/slider.php) as its two
 * submenu items, instead of two separate top-level menus.
 *
 * Position '59' is this theme's reserved slot in the GNN product family's
 * shared menu-position registry (see .specs/constitution.md rule 7 and
 * .memory-bank/adr/0009-gnn-product-family-menu-position-registry.md).
 * Always a quoted string — WordPress keys the $menu array off "$position",
 * and a bare float literal can silently lose trailing zeros at parse time.
 */
function gnn_panel_menu() {
	add_menu_page(
		__( 'GNN Theme Panel', 'gnn' ),
		'GNN',
		'manage_options',
		'gnn-panel',
		'gnn_panel_render',
		'dashicons-shield-alt',
		'59'
	);
	// Renames the auto-added first submenu item (same slug as the parent)
	// from "GNN" to "Theme" instead of adding a duplicate entry.
	add_submenu_page(
		'gnn-panel',
		__( 'GNN Theme Panel', 'gnn' ),
		__( 'Theme', 'gnn' ),
		'manage_options',
		'gnn-panel',
		'gnn_panel_render'
	);
}
add_action( 'admin_menu', 'gnn_panel_menu' );

/**
 * Register the setting.
 */
function gnn_panel_settings() {
	register_setting(
		'gnn_options_group',
		'gnn_options',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'gnn_options_sanitize',
		)
	);
}
add_action( 'admin_init', 'gnn_panel_settings' );

/**
 * Sanitize everything; route shared brand values into theme_mods so the
 * Customizer live preview stays in sync (single source of truth).
 *
 * @param array $input Raw POST values.
 * @return array Stored option array.
 */
function gnn_options_sanitize( $input ) {
	$input = (array) $input;
	$out   = wp_parse_args( (array) get_option( 'gnn_options', array() ), gnn_option_defaults() );

	// --- Shared theme_mods (not stored in the option). ---
	if ( isset( $input['accent'] ) && preg_match( '/^#[0-9a-fA-F]{6}$/', $input['accent'] ) ) {
		set_theme_mod( 'gnn_accent_color', $input['accent'] );
	}
	if ( isset( $input['default_mode'] ) ) {
		set_theme_mod( 'gnn_default_theme', 'light' === $input['default_mode'] ? 'light' : 'dark' );
	}
	if ( isset( $input['footer_legal'] ) ) {
		set_theme_mod( 'gnn_footer_legal', sanitize_text_field( $input['footer_legal'] ) );
	}
	if ( ! empty( $input['site_icon'] ) ) {
		update_option( 'site_icon', absint( $input['site_icon'] ) );
	} elseif ( isset( $input['site_icon'] ) ) {
		delete_option( 'site_icon' );
	}

	// --- Media ids. ---
	foreach ( array( 'logo_light', 'logo_light_2x', 'logo_dark', 'logo_dark_2x', 'footer_logo_light', 'footer_logo_light_2x', 'footer_logo_dark', 'footer_logo_dark_2x', 'error404_image' ) as $key ) {
		if ( isset( $input[ $key ] ) ) {
			$out[ $key ] = absint( $input[ $key ] );
		}
	}

	// --- Post ids (page pickers). ---
	if ( isset( $input['contact_page_id'] ) ) {
		$out['contact_page_id'] = absint( $input['contact_page_id'] );
	}

	// --- Plain text. ---
	foreach ( array( 'footer_copyright', 'ga4_id', 'gtm_id', 'topbar_text', 'topbar_phone', 'error404_title', 'error404_button' ) as $key ) {
		if ( isset( $input[ $key ] ) ) {
			$out[ $key ] = sanitize_text_field( $input[ $key ] );
		}
	}
	if ( isset( $input['footer_tagline'] ) ) {
		$out['footer_tagline'] = wp_kses_post( $input['footer_tagline'] );
	}
	if ( isset( $input['topbar_email'] ) ) {
		$out['topbar_email'] = sanitize_email( $input['topbar_email'] );
	}

	// --- Optional colors (empty = fall back to the theme's own tokens). ---
	foreach ( array( 'topbar_bg', 'topbar_text_color' ) as $key ) {
		if ( ! isset( $input[ $key ] ) ) {
			continue;
		}
		$val         = trim( (string) $input[ $key ] );
		$out[ $key ] = ( '' === $val ) ? '' : (string) sanitize_hex_color( $val );
	}

	// --- Textareas (multi-line). ---
	foreach ( array( 'maintenance_message', 'error404_text' ) as $key ) {
		if ( isset( $input[ $key ] ) ) {
			$out[ $key ] = sanitize_textarea_field( $input[ $key ] );
		}
	}

	// --- Choices. ---
	$choices = array(
		'sidebar_position'             => array( 'right', 'left', 'none' ),
		'header_layout'                => array( 'standard', 'centered' ),
		'header_menu_align'            => array( 'left', 'center', 'right' ),
		'footer_menu_align'            => array( 'left', 'center', 'right' ),
		'footer_brand_type'            => array( 'text', 'image', 'both', 'none' ),
		'material_icons_style'         => array( 'outlined', 'rounded', 'sharp', 'filled' ),
		'page_featured_image_fit'      => array( 'cover', 'contain', 'fill' ),
		'page_featured_image_position' => array( 'top', 'center', 'bottom' ),
		'post_featured_image_fit'      => array( 'cover', 'contain', 'fill' ),
		'post_featured_image_position' => array( 'top', 'center', 'bottom' ),
	);
	foreach ( $choices as $key => $allowed ) {
		if ( isset( $input[ $key ] ) && in_array( $input[ $key ], $allowed, true ) ) {
			$out[ $key ] = $input[ $key ];
		}
	}

	// --- Numbers (clamped). ---
	foreach ( array(
		'content_top_padding'        => array( 0, 200 ),
		'content_bottom_padding'     => array( 0, 300 ),
		'page_featured_image_height' => array( 80, 600 ),
		'post_featured_image_height' => array( 80, 800 ),
		'excerpt_length'             => array( 5, 100 ),
		'shop_columns'               => array( 2, 6 ),
		'shop_per_page'              => array( 2, 48 ),
		'logo_max_height'            => array( 12, 200 ),
		'logo_max_height_mobile'     => array( 12, 200 ),
		'footer_logo_height'         => array( 12, 300 ),
		'slider_interval'            => array( 2, 30 ),
	) as $key => $range ) {
		if ( isset( $input[ $key ] ) ) {
			$out[ $key ] = min( $range[1], max( $range[0], absint( $input[ $key ] ) ) );
		}
	}

	// --- Checkboxes (a submitted form turns unchecked boxes off). ---
	foreach ( array( 'show_toggle', 'remember_mode', 'sticky_header', 'show_search', 'show_cart', 'disable_emoji', 'disable_oembed', 'disable_migrate', 'heartbeat_slow', 'woo_scope', 'font_preload', 'mobile_dock', 'topbar_enable', 'smooth_scroll', 'scroll_top', 'scroll_anim', 'preloader', 'loading_screen', 'maintenance_mode', 'error404_search', 'slider_autoplay', 'slider_full_height', 'google_material_icons', 'typography_google_enable' ) as $key ) {
		$out[ $key ] = empty( $input[ $key ] ) ? 0 : 1;
	}

	// --- Typography: a Google Font name + weight per element. ---
	foreach ( array_keys( gnn_typography_roles() ) as $gnn_typo_role ) {
		$fam_key = "typo_{$gnn_typo_role}_family";
		if ( isset( $input[ $fam_key ] ) ) {
			$out[ $fam_key ] = substr( trim( (string) preg_replace( '/[^A-Za-z0-9 ]/', '', $input[ $fam_key ] ) ), 0, 60 );
		}
		$wt_key = "typo_{$gnn_typo_role}_weight";
		if ( isset( $input[ $wt_key ] ) && in_array( $input[ $wt_key ], gnn_typography_weights(), true ) ) {
			$out[ $wt_key ] = $input[ $wt_key ];
		}
	}

	// --- Custom CSS (tags stripped). ---
	if ( isset( $input['custom_css'] ) ) {
		$out['custom_css'] = wp_strip_all_tags( (string) $input['custom_css'] );
	}

	// --- Script fields: only users with unfiltered_html may change them. ---
	foreach ( array( 'custom_js_head', 'custom_js_footer', 'head_html', 'body_html' ) as $key ) {
		if ( isset( $input[ $key ] ) ) {
			if ( current_user_can( 'unfiltered_html' ) ) {
				$out[ $key ] = (string) $input[ $key ];
			} else {
				add_settings_error( 'gnn_options', 'gnn_js_cap', __( 'Custom script fields were not saved: your account lacks the unfiltered_html capability.', 'gnn' ) );
			}
		}
	}

	return $out;
}

/**
 * Admin assets — only on the panel screen.
 *
 * @param string $hook Current admin page hook.
 */
function gnn_panel_assets( $hook ) {
	if ( 'toplevel_page_gnn-panel' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_style( 'gnn-admin', get_template_directory_uri() . '/assets/css/admin.css', array(), GNN_VERSION );
	wp_enqueue_script( 'gnn-admin', get_template_directory_uri() . '/assets/js/admin.js', array(), GNN_VERSION, true );
	gnn_panel_code_editors();
}
add_action( 'admin_enqueue_scripts', 'gnn_panel_assets' );

/**
 * Turn the Custom Code tab's plain textareas into syntax-highlighted
 * CodeMirror editors using WordPress core's own bundled editor (the same
 * one behind Customizer → Additional CSS) — no third-party library added.
 */
function gnn_panel_code_editors() {
	$fields = array(
		'gnn-custom_css'       => 'text/css',
		'gnn-custom_js_head'   => 'text/javascript',
		'gnn-custom_js_footer' => 'text/javascript',
		'gnn-head_html'        => 'text/html',
		'gnn-body_html'        => 'text/html',
	);
	$init = '';
	foreach ( $fields as $id => $mime ) {
		$settings = wp_enqueue_code_editor( array( 'type' => $mime ) );
		if ( false === $settings ) {
			continue; // The user disabled the code editor in their WP profile.
		}
		$init .= sprintf(
			'if(document.getElementById(%1$s)){wp.codeEditor.initialize(%1$s,%2$s);}',
			wp_json_encode( $id ),
			wp_json_encode( $settings )
		);
	}
	if ( '' !== $init ) {
		wp_add_inline_script( 'code-editor', $init );
	}
}

// ----- Field helpers ----------------------------------------------------------

/**
 * Render a checkbox field.
 *
 * @param string $key   Option key.
 * @param string $label Label text.
 * @param string $hint  Optional description.
 */
function gnn_field_checkbox( $key, $label, $hint = '' ) {
	printf(
		'<div class="gnn-field gnn-field--switch"><label class="gnn-switch"><input type="checkbox" name="gnn_options[%1$s]" value="1" %2$s><span class="gnn-switch__track"><span class="gnn-switch__thumb"></span></span></label><span class="gnn-switch__label">%3$s</span></div>%4$s',
		esc_attr( $key ),
		checked( (bool) gnn_option( $key ), true, false ),
		esc_html( $label ),
		$hint ? '<p class="description">' . esc_html( $hint ) . '</p>' : ''
	);
}

/**
 * Render a media-picker field.
 *
 * @param string $key   Option key.
 * @param string $label Label text.
 * @param int    $value Attachment ID.
 */
function gnn_field_media( $key, $label, $value ) {
	$img = $value ? wp_get_attachment_image( $value, 'medium', false, array( 'class' => 'gnn-media-preview' ) ) : '';
	printf(
		'<div class="gnn-field gnn-field--media"><span class="gnn-field__label">%1$s</span>' .
		'<div class="gnn-media-box" data-target="%2$s">%3$s</div>' .
		'<input type="hidden" id="gnn-media-%2$s" name="gnn_options[%2$s]" value="%4$d">' .
		'<button type="button" class="button gnn-media-pick" data-target="%2$s">%5$s</button> ' .
		'<button type="button" class="button gnn-media-clear" data-target="%2$s">%6$s</button></div>',
		esc_html( $label ),
		esc_attr( $key ),
		$img, // phpcs:ignore WordPress.Security.EscapeOutput -- wp_get_attachment_image().
		(int) $value,
		esc_html__( 'Select image', 'gnn' ),
		esc_html__( 'Remove', 'gnn' )
	);
}

/**
 * Render a text field.
 *
 * @param string $key         Option key.
 * @param string $label       Label text.
 * @param string $value       Current value.
 * @param string $placeholder Placeholder.
 * @param string $hint        Optional description.
 */
function gnn_field_text( $key, $label, $value, $placeholder = '', $hint = '' ) {
	printf(
		'<div class="gnn-field"><label class="gnn-field__label" for="gnn-%1$s">%2$s</label>' .
		'<input type="text" id="gnn-%1$s" name="gnn_options[%1$s]" value="%3$s" placeholder="%4$s" class="regular-text">%5$s</div>',
		esc_attr( $key ),
		esc_html( $label ),
		esc_attr( $value ),
		esc_attr( $placeholder ),
		$hint ? '<p class="description">' . esc_html( $hint ) . '</p>' : ''
	);
}

/**
 * Render a textarea field.
 *
 * @param string $key   Option key.
 * @param string $label Label text.
 * @param string $value Current value.
 * @param int    $rows  Rows.
 * @param string $hint  Optional description.
 */
function gnn_field_textarea( $key, $label, $value, $rows = 6, $hint = '' ) {
	printf(
		'<div class="gnn-field"><label class="gnn-field__label" for="gnn-%1$s">%2$s</label>' .
		'<textarea id="gnn-%1$s" name="gnn_options[%1$s]" rows="%3$d" class="large-text code">%4$s</textarea>%5$s</div>',
		esc_attr( $key ),
		esc_html( $label ),
		(int) $rows,
		esc_textarea( $value ),
		$hint ? '<p class="description">' . esc_html( $hint ) . '</p>' : ''
	);
}

/**
 * Render a select field.
 *
 * @param string   $key     Option key.
 * @param string   $label   Label text.
 * @param string[] $options value => label map.
 * @param string   $hint    Optional description.
 */
function gnn_field_select( $key, $label, $options, $hint = '' ) {
	echo '<div class="gnn-field"><label class="gnn-field__label" for="gnn-' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label>';
	echo '<select id="gnn-' . esc_attr( $key ) . '" name="gnn_options[' . esc_attr( $key ) . ']">';
	$current = (string) gnn_option( $key );
	foreach ( $options as $value => $text ) {
		echo '<option value="' . esc_attr( $value ) . '" ' . selected( $current, (string) $value, false ) . '>' . esc_html( $text ) . '</option>';
	}
	echo '</select>';
	if ( $hint ) {
		echo '<p class="description">' . esc_html( $hint ) . '</p>';
	}
	echo '</div>';
}

/**
 * Render a number field.
 *
 * @param string $key   Option key.
 * @param string $label Label text.
 * @param int    $min   Minimum.
 * @param int    $max   Maximum.
 * @param string $hint  Optional description.
 */
function gnn_field_number( $key, $label, $min = 0, $max = 9999, $hint = '' ) {
	printf(
		'<div class="gnn-field"><label class="gnn-field__label" for="gnn-%1$s">%2$s</label>' .
		'<input type="number" id="gnn-%1$s" name="gnn_options[%1$s]" value="%3$s" min="%4$d" max="%5$d">%6$s</div>',
		esc_attr( $key ),
		esc_html( $label ),
		esc_attr( (string) gnn_option( $key ) ),
		(int) $min,
		(int) $max,
		$hint ? '<p class="description">' . esc_html( $hint ) . '</p>' : ''
	);
}

/**
 * Render a color field: a text input for the hex value (the primary,
 * readable control) paired with a small native color-picker swatch as a
 * visual shortcut. Avoids relying solely on the browser's native color
 * dialog for reading/typing a value.
 *
 * @param string $key            Option key.
 * @param string $label          Label text.
 * @param string $value          Current stored value ('' allowed when $allow_empty).
 * @param string $default_swatch Fallback color for the picker when $value is empty.
 * @param string $hint           Optional description.
 * @param bool   $allow_empty    Whether an empty value (theme default) is valid.
 */
function gnn_field_color( $key, $label, $value, $default_swatch = '#000000', $hint = '', $allow_empty = false ) {
	$value = (string) $value;
	printf(
		'<div class="gnn-field"><label class="gnn-field__label" for="gnn-%1$s">%2$s</label>' .
		'<div class="gnn-color-field">' .
		'<input type="color" class="gnn-color-swatch" value="%3$s" aria-hidden="true" tabindex="-1">' .
		'<input type="text" id="gnn-%1$s" name="gnn_options[%1$s]" value="%4$s" class="gnn-hex-input" maxlength="7" pattern="^#[0-9a-fA-F]{6}$" placeholder="%5$s">' .
		'</div>%6$s</div>',
		esc_attr( $key ),
		esc_html( $label ),
		esc_attr( $value ? $value : $default_swatch ),
		esc_attr( $value ),
		esc_attr( $allow_empty ? __( 'Theme default', 'gnn' ) : $default_swatch ),
		$hint ? '<p class="description">' . esc_html( $hint ) . '</p>' : ''
	);
}

// ----- Page render ----------------------------------------------------------

/**
 * Render the panel page (tabs + Settings API form).
 */
function gnn_panel_render() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$tabs = array(
		'global'     => __( 'Global', 'gnn' ),
		'header'     => __( 'Header', 'gnn' ),
		'footer'     => __( 'Footer', 'gnn' ),
		'pages'      => __( 'Pages Layout', 'gnn' ),
		'colors'     => __( 'Colors', 'gnn' ),
		'typography' => __( 'Typography', 'gnn' ),
		'icons'      => __( 'Icons', 'gnn' ),
		'code'       => __( 'Custom Code', 'gnn' ),
		'advanced'   => __( 'Advanced', 'gnn' ),
	);
	// Purely decorative — same icon used in the tab pill and its section header.
	$tab_icons = array(
		'global'     => 'admin-generic',
		'header'     => 'align-center',
		'footer'     => 'arrow-down-alt2',
		'pages'      => 'admin-page',
		'colors'     => 'art',
		'typography' => 'editor-textcolor',
		'icons'      => 'star-filled',
		'code'       => 'editor-code',
		'advanced'   => 'admin-tools',
	);
	?>
	<div class="wrap gnn-panel">
		<header class="gnn-panel__header">
			<div class="gnn-panel__heading">
				<span class="gnn-panel__icon dashicons dashicons-shield-alt" aria-hidden="true"></span>
				<div>
					<h1><?php esc_html_e( 'GNN Theme Panel', 'gnn' ); ?></h1>
					<p class="gnn-panel__subtitle"><?php esc_html_e( 'Panel-controlled theme settings — no code required.', 'gnn' ); ?></p>
				</div>
			</div>
			<div class="gnn-panel__header-actions">
				<span class="gnn-panel__badge">v<?php echo esc_html( GNN_VERSION ); ?></span>
				<?php if ( function_exists( 'gnn_updates_enabled' ) && gnn_updates_enabled() ) : ?>
					<?php
					$gnn_hdr_release    = function_exists( 'gnn_updater' ) ? gnn_updater()->get_remote_release() : false;
					$gnn_hdr_has_update = $gnn_hdr_release && version_compare( $gnn_hdr_release->version, GNN_VERSION, '>' );
					?>
					<?php if ( $gnn_hdr_has_update ) : ?>
						<a class="button button-primary gnn-update-btn" href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'update.php?action=upgrade-theme&theme=' . rawurlencode( get_template() ) ), 'upgrade-theme_' . get_template() ) ); ?>">
							<?php
							/* translators: %s: latest available version. */
							printf( esc_html__( 'Update to %s', 'gnn' ), esc_html( $gnn_hdr_release->version ) );
							?>
						</a>
					<?php else : ?>
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="gnn-update-form">
							<input type="hidden" name="action" value="gnn_check_theme_update">
							<?php wp_nonce_field( 'gnn_check_theme_update' ); ?>
							<button type="submit" class="button gnn-update-btn"><?php esc_html_e( 'Check for updates', 'gnn' ); ?></button>
						</form>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</header>
		<hr class="wp-header-end">

		<div class="gnn-stats">
			<div class="gnn-stat">
				<span class="dashicons dashicons-art gnn-stat__icon" aria-hidden="true"></span>
				<div>
					<span class="gnn-stat__value"><span class="gnn-swatch" style="background:<?php echo esc_attr( get_theme_mod( 'gnn_accent_color', '#34d399' ) ); ?>"></span><?php echo esc_html( get_theme_mod( 'gnn_accent_color', '#34d399' ) ); ?></span>
					<span class="gnn-stat__label"><?php esc_html_e( 'Accent color', 'gnn' ); ?></span>
				</div>
			</div>
			<div class="gnn-stat">
				<span class="dashicons dashicons-lightbulb gnn-stat__icon" aria-hidden="true"></span>
				<div>
					<span class="gnn-stat__value"><?php echo esc_html( 'light' === get_theme_mod( 'gnn_default_theme', 'dark' ) ? __( 'Light', 'gnn' ) : __( 'Dark', 'gnn' ) ); ?></span>
					<span class="gnn-stat__label"><?php esc_html_e( 'Default mode', 'gnn' ); ?></span>
				</div>
			</div>
			<div class="gnn-stat">
				<span class="dashicons dashicons-admin-tools gnn-stat__icon" aria-hidden="true"></span>
				<div>
					<span class="gnn-stat__value"><?php echo esc_html( defined( 'ELEMENTOR_VERSION' ) ? __( 'Elementor', 'gnn' ) : __( 'Gutenberg', 'gnn' ) ); ?></span>
					<span class="gnn-stat__label"><?php esc_html_e( 'Page builder', 'gnn' ); ?></span>
				</div>
			</div>
		</div>

		<?php settings_errors( 'gnn_options' ); ?>

		<nav class="nav-tab-wrapper gnn-tabs">
			<?php foreach ( $tabs as $id => $label ) : ?>
				<a href="#gnn-tab-<?php echo esc_attr( $id ); ?>" class="nav-tab" data-tab="<?php echo esc_attr( $id ); ?>"><span class="dashicons dashicons-<?php echo esc_attr( $tab_icons[ $id ] ); ?>" aria-hidden="true"></span><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</nav>

		<form method="post" action="options.php">
			<?php settings_fields( 'gnn_options_group' ); ?>

			<section id="gnn-tab-global" class="gnn-tab">
				<h2><span class="dashicons dashicons-admin-generic" aria-hidden="true"></span><?php esc_html_e( 'Global', 'gnn' ); ?></h2>
				<div class="gnn-field">
					<span class="gnn-field__label"><?php esc_html_e( 'Default mode', 'gnn' ); ?></span>
					<label><input type="radio" name="gnn_options[default_mode]" value="dark" <?php checked( get_theme_mod( 'gnn_default_theme', 'dark' ), 'dark' ); ?>> <?php esc_html_e( 'Dark', 'gnn' ); ?></label>
					<label><input type="radio" name="gnn_options[default_mode]" value="light" <?php checked( get_theme_mod( 'gnn_default_theme', 'dark' ), 'light' ); ?>> <?php esc_html_e( 'Light', 'gnn' ); ?></label>
				</div>
				<?php gnn_field_checkbox( 'show_toggle', __( 'Show the dark/light toggle in the header', 'gnn' ) ); ?>
				<?php gnn_field_checkbox( 'remember_mode', __( 'Remember each visitor\'s choice (localStorage)', 'gnn' ) ); ?>
				<?php gnn_field_media( 'site_icon', __( 'Favicon (site icon, 512×512)', 'gnn' ), (int) get_option( 'site_icon', 0 ) ); ?>

				<h3><?php esc_html_e( 'Interactions', 'gnn' ); ?></h3>
				<?php gnn_field_checkbox( 'smooth_scroll', __( 'Smooth scroll for anchor links', 'gnn' ) ); ?>
				<?php gnn_field_checkbox( 'scroll_top', __( 'Show the “Scroll to top” button', 'gnn' ) ); ?>
				<?php gnn_field_checkbox( 'scroll_anim', __( 'Scroll-in animations for blocks/images', 'gnn' ), __( 'Adds reveal-on-scroll to elements with the gnn-anim class (respects reduced-motion).', 'gnn' ) ); ?>

				<h3><?php esc_html_e( 'Hero slider', 'gnn' ); ?></h3>
				<p class="description"><?php esc_html_e( 'Slides are managed under GNN Slider in the admin menu (unlimited slides).', 'gnn' ); ?></p>
				<?php gnn_field_checkbox( 'slider_autoplay', __( 'Autoplay', 'gnn' ) ); ?>
				<?php gnn_field_number( 'slider_interval', __( 'Autoplay interval (seconds)', 'gnn' ), 2, 30 ); ?>
				<?php gnn_field_checkbox( 'slider_full_height', __( 'Full-height slider (fills the screen below the header)', 'gnn' ) ); ?>
				<?php gnn_field_checkbox( 'preloader', __( 'Top loading progress bar', 'gnn' ) ); ?>
				<?php gnn_field_checkbox( 'loading_screen', __( 'Full-screen loading overlay', 'gnn' ) ); ?>

				<h3><?php esc_html_e( 'Maintenance / Coming soon', 'gnn' ); ?></h3>
				<?php gnn_field_checkbox( 'maintenance_mode', __( 'Enable maintenance mode (logged-out visitors only)', 'gnn' ) ); ?>
				<?php gnn_field_textarea( 'maintenance_message', __( 'Maintenance message', 'gnn' ), (string) gnn_option( 'maintenance_message' ), 3 ); ?>

				<h3><?php esc_html_e( 'Performance', 'gnn' ); ?></h3>
				<?php gnn_field_checkbox( 'disable_emoji', __( 'Disable the emoji script', 'gnn' ) ); ?>
				<?php gnn_field_checkbox( 'disable_oembed', __( 'Remove oEmbed discovery links', 'gnn' ) ); ?>
				<?php gnn_field_checkbox( 'disable_migrate', __( 'Remove jQuery Migrate (front-end)', 'gnn' ) ); ?>
				<?php gnn_field_checkbox( 'heartbeat_slow', __( 'Slow the Heartbeat API to 60s', 'gnn' ) ); ?>
				<?php gnn_field_checkbox( 'woo_scope', __( 'Load WooCommerce assets only on shop pages', 'gnn' ), __( 'Pages using [products] or Woo blocks are detected automatically.', 'gnn' ) ); ?>
				<?php gnn_field_checkbox( 'font_preload', __( 'Preload theme fonts', 'gnn' ) ); ?>
			</section>

			<section id="gnn-tab-header" class="gnn-tab">
				<h2><span class="dashicons dashicons-align-center" aria-hidden="true"></span><?php esc_html_e( 'Header', 'gnn' ); ?></h2>

				<p class="description gnn-group-note"><?php esc_html_e( 'Light mode uses dark artwork; dark mode uses light artwork. Upload a 2× version of each for crisp logos on Retina / high-DPI screens. If you only fill one mode, it is used for both.', 'gnn' ); ?></p>
				<div class="gnn-logo-grid">
					<?php gnn_field_media( 'logo_light', __( 'Logo — light mode (1×)', 'gnn' ), (int) gnn_option( 'logo_light' ) ); ?>
					<?php gnn_field_media( 'logo_light_2x', __( 'Logo — light mode (2× retina)', 'gnn' ), (int) gnn_option( 'logo_light_2x' ) ); ?>
					<?php gnn_field_media( 'logo_dark', __( 'Logo — dark mode (1×)', 'gnn' ), (int) gnn_option( 'logo_dark' ) ); ?>
					<?php gnn_field_media( 'logo_dark_2x', __( 'Logo — dark mode (2× retina)', 'gnn' ), (int) gnn_option( 'logo_dark_2x' ) ); ?>
				</div>
				<?php gnn_field_number( 'logo_max_height', __( 'Logo max height — desktop (px)', 'gnn' ), 12, 200 ); ?>
				<?php gnn_field_number( 'logo_max_height_mobile', __( 'Logo max height — mobile (px)', 'gnn' ), 12, 200 ); ?>

				<h3><?php esc_html_e( 'Layout', 'gnn' ); ?></h3>
				<?php
				gnn_field_select(
					'header_layout',
					__( 'Header layout', 'gnn' ),
					array(
						'standard' => __( 'Standard (logo left, menu right)', 'gnn' ),
						'centered' => __( 'Centered (logo center, split menus)', 'gnn' ),
					),
					__( 'Centered layout uses the Header Left and Header Right menu locations.', 'gnn' )
				);
				gnn_field_select(
					'header_menu_align',
					__( 'Menu alignment', 'gnn' ),
					array(
						'left'   => __( 'Left', 'gnn' ),
						'center' => __( 'Center', 'gnn' ),
						'right'  => __( 'Right', 'gnn' ),
					)
				);
				?>
				<?php gnn_field_checkbox( 'sticky_header', __( 'Sticky header', 'gnn' ) ); ?>
				<?php gnn_field_checkbox( 'show_search', __( 'Show the search icon', 'gnn' ) ); ?>
				<?php gnn_field_checkbox( 'show_cart', __( 'Show the cart link (WooCommerce)', 'gnn' ) ); ?>
				<?php gnn_field_checkbox( 'mobile_dock', __( 'Mobile bottom app dock (≤600px)', 'gnn' ) ); ?>

				<h3><?php esc_html_e( 'Top bar', 'gnn' ); ?></h3>
				<?php gnn_field_checkbox( 'topbar_enable', __( 'Enable the top announcement bar', 'gnn' ) ); ?>
				<?php gnn_field_text( 'topbar_text', __( 'Announcement text', 'gnn' ), (string) gnn_option( 'topbar_text' ) ); ?>
				<?php gnn_field_text( 'topbar_email', __( 'Email', 'gnn' ), (string) gnn_option( 'topbar_email' ) ); ?>
				<?php gnn_field_text( 'topbar_phone', __( 'Phone', 'gnn' ), (string) gnn_option( 'topbar_phone' ) ); ?>
				<p class="description"><?php esc_html_e( 'Top bar colors have moved to the Colors tab, alongside every other theme color.', 'gnn' ); ?></p>
			</section>

			<section id="gnn-tab-footer" class="gnn-tab">
				<h2><span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span><?php esc_html_e( 'Footer', 'gnn' ); ?></h2>
				<?php
				gnn_field_select(
					'footer_brand_type',
					__( 'Footer brand', 'gnn' ),
					array(
						'text'  => __( 'Text (site title)', 'gnn' ),
						'image' => __( 'Image logo', 'gnn' ),
						'both'  => __( 'Image + text', 'gnn' ),
						'none'  => __( 'Hide', 'gnn' ),
					)
				);
				?>
				<div class="gnn-logo-grid">
					<?php gnn_field_media( 'footer_logo_light', __( 'Footer logo — light mode (1×)', 'gnn' ), (int) gnn_option( 'footer_logo_light' ) ); ?>
					<?php gnn_field_media( 'footer_logo_light_2x', __( 'Footer logo — light mode (2× retina)', 'gnn' ), (int) gnn_option( 'footer_logo_light_2x' ) ); ?>
					<?php gnn_field_media( 'footer_logo_dark', __( 'Footer logo — dark mode (1×)', 'gnn' ), (int) gnn_option( 'footer_logo_dark' ) ); ?>
					<?php gnn_field_media( 'footer_logo_dark_2x', __( 'Footer logo — dark mode (2× retina)', 'gnn' ), (int) gnn_option( 'footer_logo_dark_2x' ) ); ?>
				</div>
				<?php gnn_field_number( 'footer_logo_height', __( 'Footer logo max height (px)', 'gnn' ), 12, 200, __( 'Controls logo height while preserving aspect ratio (default: 40px). Very wide banner-style logos may render slightly shorter if the footer column is narrow.', 'gnn' ) ); ?>
				<?php gnn_field_textarea( 'footer_tagline', __( 'Footer tagline', 'gnn' ), (string) gnn_option( 'footer_tagline' ), 3, __( 'HTML tags allowed (e.g. <a>, <br>, <strong>, <span>). Leave empty to use the site tagline.', 'gnn' ) ); ?>
				<?php
				gnn_field_select(
					'footer_menu_align',
					__( 'Footer menu alignment', 'gnn' ),
					array(
						'left'   => __( 'Left', 'gnn' ),
						'center' => __( 'Center', 'gnn' ),
						'right'  => __( 'Right', 'gnn' ),
					)
				);
				/* translators: %year% and %site% are literal replacement tokens shown to the admin, not printf placeholders. */
				gnn_field_text( 'footer_copyright', __( 'Footer copyright line', 'gnn' ), (string) gnn_option( 'footer_copyright' ), '© %year% %site%. All rights reserved.', __( 'Placeholders: %year% and %site%. Leave empty for the default.', 'gnn' ) );
				gnn_field_text( 'footer_legal', __( 'Footer legal text', 'gnn' ), (string) get_theme_mod( 'gnn_footer_legal', '' ), 'Privacy — Terms', __( 'Shown bottom-right in the footer. Leave empty to hide.', 'gnn' ) );
				?>
			</section>

			<section id="gnn-tab-pages" class="gnn-tab">
				<h2><span class="dashicons dashicons-admin-page" aria-hidden="true"></span><?php esc_html_e( 'Pages Layout', 'gnn' ); ?></h2>
				<?php
				gnn_field_select(
					'sidebar_position',
					__( 'Default sidebar position', 'gnn' ),
					array(
						'right' => __( 'Right', 'gnn' ),
						'left'  => __( 'Left', 'gnn' ),
						'none'  => __( 'No sidebar', 'gnn' ),
					)
				);
				$gnn_contact_options = array( 0 => __( 'Auto-detect (default)', 'gnn' ) );
				foreach ( get_pages( array( 'sort_column' => 'post_title' ) ) as $gnn_contact_page ) {
					$gnn_contact_options[ $gnn_contact_page->ID ] = $gnn_contact_page->post_title;
				}
				gnn_field_select(
					'contact_page_id',
					__( 'Contact page', 'gnn' ),
					$gnn_contact_options,
					__( 'Used by the mobile dock\'s Contact link (and anywhere else the theme links to "get in touch"). Auto-detect looks for a page slugged "contact" or "iletisim".', 'gnn' )
				);
				?>
				<?php gnn_field_number( 'content_top_padding', __( 'Content top spacing (px)', 'gnn' ), 0, 200, __( 'Gap between the header and the page content — or between the featured image and the content, when the page has one.', 'gnn' ) ); ?>
				<?php gnn_field_number( 'content_bottom_padding', __( 'Content bottom spacing (px)', 'gnn' ), 0, 300, __( 'Gap between the page content and the footer.', 'gnn' ) ); ?>

				<h3><?php esc_html_e( 'Featured image', 'gnn' ); ?></h3>
				<p class="description"><?php esc_html_e( 'Pages: Default, Boxed, Left Sidebar and Right Sidebar templates.', 'gnn' ); ?></p>
				<?php gnn_field_number( 'page_featured_image_height', __( 'Page image height (px)', 'gnn' ), 80, 600 ); ?>
				<?php
				gnn_field_select(
					'page_featured_image_fit',
					__( 'Page image fit', 'gnn' ),
					array(
						'cover'   => __( 'Cover (fill and crop)', 'gnn' ),
						'contain' => __( 'Contain (show the whole image)', 'gnn' ),
						'fill'    => __( 'Fill (stretch)', 'gnn' ),
					)
				);
				gnn_field_select(
					'page_featured_image_position',
					__( 'Page image alignment', 'gnn' ),
					array(
						'top'    => __( 'Top', 'gnn' ),
						'center' => __( 'Center', 'gnn' ),
						'bottom' => __( 'Bottom', 'gnn' ),
					),
					__( 'Which part of the image stays visible when it gets cropped.', 'gnn' )
				);
				?>

				<p class="description"><?php esc_html_e( 'Blog: single post pages.', 'gnn' ); ?></p>
				<?php gnn_field_number( 'post_featured_image_height', __( 'Blog image height (px)', 'gnn' ), 80, 800 ); ?>
				<?php
				gnn_field_select(
					'post_featured_image_fit',
					__( 'Blog image fit', 'gnn' ),
					array(
						'cover'   => __( 'Cover (fill and crop)', 'gnn' ),
						'contain' => __( 'Contain (show the whole image)', 'gnn' ),
						'fill'    => __( 'Fill (stretch)', 'gnn' ),
					)
				);
				gnn_field_select(
					'post_featured_image_position',
					__( 'Blog image alignment', 'gnn' ),
					array(
						'top'    => __( 'Top', 'gnn' ),
						'center' => __( 'Center', 'gnn' ),
						'bottom' => __( 'Bottom', 'gnn' ),
					),
					__( 'Which part of the image stays visible when it gets cropped.', 'gnn' )
				);
				?>

				<?php gnn_field_number( 'excerpt_length', __( 'Excerpt length (words)', 'gnn' ), 5, 100 ); ?>
				<?php if ( class_exists( 'WooCommerce' ) ) : ?>
					<?php gnn_field_number( 'shop_columns', __( 'Shop columns', 'gnn' ), 2, 6 ); ?>
					<?php gnn_field_number( 'shop_per_page', __( 'Products per page', 'gnn' ), 2, 48 ); ?>
				<?php endif; ?>

				<h3><?php esc_html_e( '404 page', 'gnn' ); ?></h3>
				<?php gnn_field_text( 'error404_title', __( '404 title', 'gnn' ), (string) gnn_option( 'error404_title' ), __( 'Page not found', 'gnn' ) ); ?>
				<?php gnn_field_textarea( 'error404_text', __( '404 description', 'gnn' ), (string) gnn_option( 'error404_text' ), 2 ); ?>
				<?php gnn_field_checkbox( 'error404_search', __( 'Show a search box on the 404 page', 'gnn' ) ); ?>
				<?php gnn_field_text( 'error404_button', __( '“Back to home” button text', 'gnn' ), (string) gnn_option( 'error404_button' ), __( 'Back to Home', 'gnn' ) ); ?>
				<?php gnn_field_media( 'error404_image', __( '404 image', 'gnn' ), (int) gnn_option( 'error404_image' ) ); ?>
			</section>

			<section id="gnn-tab-colors" class="gnn-tab">
				<h2><span class="dashicons dashicons-art" aria-hidden="true"></span><?php esc_html_e( 'Colors', 'gnn' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Every theme color lives here. Type a hex code directly, or use the small swatch to pick one visually.', 'gnn' ); ?></p>
				<?php
				gnn_field_color(
					'accent',
					__( 'Accent color', 'gnn' ),
					get_theme_mod( 'gnn_accent_color', '#34d399' ),
					'#34d399',
					__( 'Used for buttons, links and highlights across the theme (and the Elementor color palette).', 'gnn' )
				);
				gnn_field_color(
					'topbar_bg',
					__( 'Top bar background color', 'gnn' ),
					(string) gnn_option( 'topbar_bg' ),
					'#141416',
					__( 'Leave empty to match the theme automatically.', 'gnn' ),
					true
				);
				gnn_field_color(
					'topbar_text_color',
					__( 'Top bar text color', 'gnn' ),
					(string) gnn_option( 'topbar_text_color' ),
					'#9d9da4',
					__( 'Leave empty to match the theme automatically.', 'gnn' ),
					true
				);
				?>
			</section>

			<section id="gnn-tab-typography" class="gnn-tab">
				<h2><span class="dashicons dashicons-editor-textcolor" aria-hidden="true"></span><?php esc_html_e( 'Typography', 'gnn' ); ?></h2>
				<p class="description"><?php esc_html_e( 'The theme ships self-hosted Space Grotesk (headings) and Manrope (body) — fast, and used by default with zero extra requests.', 'gnn' ); ?></p>
				<?php
				gnn_field_checkbox(
					'typography_google_enable',
					__( 'Use Google Fonts instead', 'gnn' ),
					__( 'Off by default. When on, any element below with a font name set loads that Google Font; elements left empty keep the fast self-hosted default. Every font you pick is combined into a single stylesheet request, never one per font.', 'gnn' )
				);
				?>
				<h3><?php esc_html_e( 'Fonts by element', 'gnn' ); ?></h3>
				<p class="description"><?php esc_html_e( 'Type the exact name from fonts.google.com (e.g. "Poppins", "IBM Plex Sans"). Leave empty to keep the theme default for that element. Turkish characters (ğ ı ş ö ü ç) need no separate setting — Google Fonts and the browser fetch the extended character set automatically, only when the page actually needs it.', 'gnn' ); ?></p>
				<div class="gnn-typo-grid">
					<?php foreach ( gnn_typography_roles() as $gnn_typo_role => $gnn_typo_label ) : ?>
						<div class="gnn-typo-row">
							<span class="gnn-typo-row__label"><?php echo esc_html( $gnn_typo_label ); ?></span>
							<input type="text" name="gnn_options[typo_<?php echo esc_attr( $gnn_typo_role ); ?>_family]" value="<?php echo esc_attr( (string) gnn_option( "typo_{$gnn_typo_role}_family" ) ); ?>" placeholder="<?php esc_attr_e( 'Theme default', 'gnn' ); ?>" class="regular-text">
							<select name="gnn_options[typo_<?php echo esc_attr( $gnn_typo_role ); ?>_weight]">
								<?php foreach ( gnn_typography_weights() as $gnn_typo_weight ) : ?>
									<option value="<?php echo esc_attr( $gnn_typo_weight ); ?>" <?php selected( (string) gnn_option( "typo_{$gnn_typo_role}_weight" ), $gnn_typo_weight ); ?>><?php echo esc_html( $gnn_typo_weight ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					<?php endforeach; ?>
				</div>
			</section>

			<section id="gnn-tab-icons" class="gnn-tab">
				<h2><span class="dashicons dashicons-star-filled" aria-hidden="true"></span><?php esc_html_e( 'Icons', 'gnn' ); ?></h2>
				<p class="description"><?php esc_html_e( 'The theme uses inline SVG icons for its own UI. Google Material Symbols can additionally be loaded for use in page content and menus.', 'gnn' ); ?></p>
				<?php gnn_field_checkbox( 'google_material_icons', __( 'Load Google Material Symbols', 'gnn' ), __( 'Adds the font stylesheet site-wide so .material-symbols-* classes render.', 'gnn' ) ); ?>
				<?php
				gnn_field_select(
					'material_icons_style',
					__( 'Icon style', 'gnn' ),
					array(
						'outlined' => __( 'Outlined', 'gnn' ),
						'rounded'  => __( 'Rounded', 'gnn' ),
						'sharp'    => __( 'Sharp', 'gnn' ),
						'filled'   => __( 'Filled', 'gnn' ),
					),
					/* translators: %s: example HTML markup, shown literally. */
					sprintf( __( 'Use it in content with e.g. %s', 'gnn' ), '<code>&lt;span class="material-symbols-outlined"&gt;search&lt;/span&gt;</code>' )
				);
				?>
			</section>

			<section id="gnn-tab-code" class="gnn-tab">
				<h2><span class="dashicons dashicons-editor-code" aria-hidden="true"></span><?php esc_html_e( 'Custom Code', 'gnn' ); ?></h2>
				<?php gnn_field_textarea( 'custom_css', __( 'Custom CSS', 'gnn' ), (string) gnn_option( 'custom_css' ), 8 ); ?>
				<?php gnn_field_text( 'ga4_id', __( 'Google Analytics 4 ID', 'gnn' ), (string) gnn_option( 'ga4_id' ), 'G-XXXXXXXXXX' ); ?>
				<?php gnn_field_text( 'gtm_id', __( 'Google Tag Manager ID', 'gnn' ), (string) gnn_option( 'gtm_id' ), 'GTM-XXXXXXX' ); ?>
				<?php gnn_field_textarea( 'custom_js_head', __( 'Custom JavaScript — head', 'gnn' ), (string) gnn_option( 'custom_js_head' ), 5, __( 'Raw JS, printed inside a <script> tag in <head>. Requires the unfiltered_html capability.', 'gnn' ) ); ?>
				<?php gnn_field_textarea( 'custom_js_footer', __( 'Custom JavaScript — footer', 'gnn' ), (string) gnn_option( 'custom_js_footer' ), 5 ); ?>
				<?php gnn_field_textarea( 'head_html', __( 'Extra <head> HTML (meta/verification tags)', 'gnn' ), (string) gnn_option( 'head_html' ), 4 ); ?>
				<?php gnn_field_textarea( 'body_html', __( 'After <body> HTML', 'gnn' ), (string) gnn_option( 'body_html' ), 4 ); ?>
			</section>

			<?php submit_button( __( 'Save Settings', 'gnn' ) ); ?>
		</form>

		<?php gnn_panel_advanced_tab(); ?>
	</div>
	<?php
}

// ----- Advanced tab: reset / export / import ----------------------------------

/**
 * Render the Advanced tab. Rendered OUTSIDE the settings form (its tools use
 * their own forms + nonces), toggled by the same tab navigation.
 */
function gnn_panel_advanced_tab() {
	?>
	<section id="gnn-tab-advanced" class="gnn-tab">
		<h2><span class="dashicons dashicons-admin-tools" aria-hidden="true"></span><?php esc_html_e( 'Advanced', 'gnn' ); ?></h2>

		<div class="gnn-field">
			<span class="gnn-field__label"><?php esc_html_e( 'Export settings', 'gnn' ); ?></span>
			<p class="description"><?php esc_html_e( 'Download all GNN Theme settings as a JSON file for backup or transfer.', 'gnn' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="gnn-inline-form">
				<input type="hidden" name="action" value="gnn_export_settings">
				<?php wp_nonce_field( 'gnn_export_settings' ); ?>
				<?php submit_button( __( 'Export JSON', 'gnn' ), 'secondary', 'submit', false ); ?>
			</form>
		</div>

		<div class="gnn-field">
			<span class="gnn-field__label"><?php esc_html_e( 'Import settings', 'gnn' ); ?></span>
			<p class="description"><?php esc_html_e( 'Upload a previously exported GNN settings JSON file.', 'gnn' ); ?></p>
			<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="gnn-inline-form">
				<input type="hidden" name="action" value="gnn_import_settings">
				<?php wp_nonce_field( 'gnn_import_settings' ); ?>
				<input type="file" name="gnn_import_file" accept="application/json,.json" required>
				<?php submit_button( __( 'Import JSON', 'gnn' ), 'secondary', 'submit', false ); ?>
			</form>
		</div>

		<div class="gnn-field">
			<span class="gnn-field__label"><?php esc_html_e( 'Reset settings', 'gnn' ); ?></span>
			<p class="description"><?php esc_html_e( 'Restore all GNN Theme settings to their defaults. This cannot be undone.', 'gnn' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('<?php echo esc_js( __( 'Reset all GNN Theme settings to defaults?', 'gnn' ) ); ?>');" class="gnn-inline-form">
				<input type="hidden" name="action" value="gnn_reset_settings">
				<?php wp_nonce_field( 'gnn_reset_settings' ); ?>
				<?php submit_button( __( 'Reset to defaults', 'gnn' ), 'delete', 'submit', false ); ?>
			</form>
		</div>

		<?php if ( defined( 'ELEMENTOR_VERSION' ) ) : ?>
			<div class="gnn-field">
				<span class="gnn-field__label"><?php esc_html_e( 'Elementor templates', 'gnn' ); ?></span>
				<p class="description"><?php esc_html_e( 'GNN\'s designed sections as Elementor Saved Templates (Templates → Insert Template in the editor). Rebuild after changing the accent color so the templates match.', 'gnn' ); ?></p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="gnn-inline-form">
					<input type="hidden" name="action" value="gnn_rebuild_elementor_templates">
					<?php wp_nonce_field( 'gnn_rebuild_elementor_templates' ); ?>
					<?php submit_button( __( 'Rebuild Elementor templates', 'gnn' ), 'secondary', 'submit', false ); ?>
				</form>
			</div>
		<?php endif; ?>

		<?php if ( function_exists( 'gnn_updates_enabled' ) ) : ?>
			<?php
			$gnn_updates_on = gnn_updates_enabled();
			$gnn_release    = ( $gnn_updates_on && function_exists( 'gnn_updater' ) ) ? gnn_updater()->get_remote_release() : false;
			$gnn_has_update = $gnn_release && version_compare( $gnn_release->version, GNN_VERSION, '>' );
			?>
			<div class="gnn-field">
				<span class="gnn-field__label"><?php esc_html_e( 'Theme updates (GitHub)', 'gnn' ); ?></span>
				<p class="description">
					<?php
					/* translators: %s: installed theme version. */
					printf( esc_html__( 'Installed version: %s', 'gnn' ), esc_html( GNN_VERSION ) );
					?>
				</p>
				<?php if ( $gnn_updates_on ) : ?>
					<p class="description">
						<?php if ( $gnn_has_update ) : ?>
							<strong>
							<?php
							/* translators: %s: latest available version. */
							printf( esc_html__( 'Update available: version %s.', 'gnn' ), esc_html( $gnn_release->version ) );
							?>
							</strong>
						<?php elseif ( $gnn_release ) : ?>
							<?php esc_html_e( 'You are running the latest version.', 'gnn' ); ?>
						<?php else : ?>
							<?php esc_html_e( 'Could not reach GitHub to check for updates.', 'gnn' ); ?>
						<?php endif; ?>
					</p>
				<?php endif; ?>

				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="gnn-inline-form">
					<input type="hidden" name="action" value="gnn_save_updater_settings">
					<?php wp_nonce_field( 'gnn_save_updater_settings' ); ?>
					<label class="gnn-switch"><input type="checkbox" name="gnn_updates_enable" value="1" <?php checked( $gnn_updates_on ); ?>><span class="gnn-switch__track"><span class="gnn-switch__thumb"></span></span></label> <span class="gnn-switch__label"><?php esc_html_e( 'Check GitHub for new releases', 'gnn' ); ?></span>
					<?php submit_button( __( 'Save', 'gnn' ), 'secondary', 'submit', false ); ?>
				</form>
				<p class="description"><?php esc_html_e( 'The check-for-updates / update button now lives at the top of this panel, under the version number.', 'gnn' ); ?></p>
			</div>
		<?php endif; ?>
	</section>
	<?php
}

/**
 * Theme_mods that travel with the panel export/import.
 *
 * @return string[]
 */
function gnn_exportable_theme_mods() {
	return array( 'gnn_accent_color', 'gnn_default_theme', 'gnn_footer_legal' );
}

/**
 * Redirect back to the panel with a status flag.
 *
 * @param string $status Status slug.
 */
function gnn_panel_redirect( $status ) {
	wp_safe_redirect( add_query_arg( array( 'gnn_notice' => $status ), admin_url( 'admin.php?page=gnn-panel' ) ) );
	exit;
}

/**
 * Handle: export settings as a JSON download.
 */
function gnn_handle_export_settings() {
	if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'gnn_export_settings' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'gnn' ) );
	}
	$mods = array();
	foreach ( gnn_exportable_theme_mods() as $mod ) {
		$mods[ $mod ] = get_theme_mod( $mod );
	}
	$payload = array(
		'_gnn_export' => GNN_VERSION,
		'options'     => (array) get_option( 'gnn_options', array() ),
		'theme_mods'  => $mods,
	);

	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=gnn-settings-' . gmdate( 'Ymd' ) . '.json' );
	echo wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	exit;
}
add_action( 'admin_post_gnn_export_settings', 'gnn_handle_export_settings' );

/**
 * Handle: import settings from an uploaded JSON file.
 */
function gnn_handle_import_settings() {
	if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'gnn_import_settings' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'gnn' ) );
	}
	if ( empty( $_FILES['gnn_import_file']['tmp_name'] ) || ! is_uploaded_file( sanitize_text_field( wp_unslash( $_FILES['gnn_import_file']['tmp_name'] ) ) ) ) {
		gnn_panel_redirect( 'import_error' );
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- reading an uploaded temp file.
	$raw  = file_get_contents( sanitize_text_field( wp_unslash( $_FILES['gnn_import_file']['tmp_name'] ) ) );
	$data = json_decode( (string) $raw, true );
	if ( ! is_array( $data ) || empty( $data['_gnn_export'] ) ) {
		gnn_panel_redirect( 'import_error' );
	}

	if ( isset( $data['options'] ) && is_array( $data['options'] ) ) {
		// Run through the same sanitizer as the settings form.
		update_option( 'gnn_options', gnn_options_sanitize( $data['options'] ) );
	}
	if ( isset( $data['theme_mods'] ) && is_array( $data['theme_mods'] ) ) {
		foreach ( gnn_exportable_theme_mods() as $mod ) {
			if ( isset( $data['theme_mods'][ $mod ] ) ) {
				set_theme_mod( $mod, sanitize_text_field( (string) $data['theme_mods'][ $mod ] ) );
			}
		}
	}
	gnn_panel_redirect( 'import_ok' );
}
add_action( 'admin_post_gnn_import_settings', 'gnn_handle_import_settings' );

/**
 * Handle: reset settings to defaults.
 */
function gnn_handle_reset_settings() {
	if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'gnn_reset_settings' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'gnn' ) );
	}
	delete_option( 'gnn_options' );
	gnn_panel_redirect( 'reset_ok' );
}
add_action( 'admin_post_gnn_reset_settings', 'gnn_handle_reset_settings' );

/**
 * Handle: rebuild the Elementor Saved Templates library.
 */
function gnn_handle_rebuild_elementor_templates() {
	if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'gnn_rebuild_elementor_templates' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'gnn' ) );
	}
	if ( function_exists( 'gnn_install_elementor_templates' ) ) {
		gnn_install_elementor_templates();
	}
	gnn_panel_redirect( 'elementor_rebuilt' );
}
add_action( 'admin_post_gnn_rebuild_elementor_templates', 'gnn_handle_rebuild_elementor_templates' );

/**
 * Handle: save the GitHub-updates enable/disable toggle.
 */
function gnn_handle_save_updater_settings() {
	if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'gnn_save_updater_settings' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'gnn' ) );
	}
	update_option( 'gnn_github_updates_enable', empty( $_POST['gnn_updates_enable'] ) ? 0 : 1 );
	gnn_panel_redirect( 'updater_saved' );
}
add_action( 'admin_post_gnn_save_updater_settings', 'gnn_handle_save_updater_settings' );

/**
 * Handle: manual "Check for updates now" — clears the cached GitHub
 * response and WP's own theme-update transient so both re-check fresh.
 */
function gnn_handle_check_theme_update() {
	if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'gnn_check_theme_update' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'gnn' ) );
	}
	delete_transient( 'gnn_github_update_check' );
	delete_site_transient( 'update_themes' );
	gnn_panel_redirect( 'updater_checked' );
}
add_action( 'admin_post_gnn_check_theme_update', 'gnn_handle_check_theme_update' );

/**
 * Show admin notices after an Advanced-tab action.
 */
function gnn_panel_admin_notices() {
	if ( empty( $_GET['gnn_notice'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only flash flag.
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || 'toplevel_page_gnn-panel' !== $screen->id ) {
		return;
	}
	$notice   = sanitize_key( wp_unslash( $_GET['gnn_notice'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only flash flag.
	$messages = array(
		'import_ok'         => array( 'success', __( 'Settings imported successfully.', 'gnn' ) ),
		'import_error'      => array( 'error', __( 'Import failed: the file is not a valid GNN settings export.', 'gnn' ) ),
		'reset_ok'          => array( 'success', __( 'Settings reset to defaults.', 'gnn' ) ),
		'elementor_rebuilt' => array( 'success', __( 'Elementor templates rebuilt.', 'gnn' ) ),
		'updater_saved'     => array( 'success', __( 'Theme update settings saved.', 'gnn' ) ),
		'updater_checked'   => array( 'success', __( 'Update check complete.', 'gnn' ) ),
	);
	if ( isset( $messages[ $notice ] ) ) {
		printf(
			'<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
			esc_attr( $messages[ $notice ][0] ),
			esc_html( $messages[ $notice ][1] )
		);
	}
}
add_action( 'admin_notices', 'gnn_panel_admin_notices' );
