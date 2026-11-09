<?php
/**
 * GNN Panel — the theme's admin page (WP Admin → GNN).
 *
 * Pure WP Settings API + one scoped admin.css/admin.js pair loaded ONLY on
 * this screen. No third-party options framework.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the shared GNN menu and theme settings screen.
 *
 * Position '59.100' is this theme's reserved slot in the GNN product
 * family's shared menu-position registry.
 */
function gnn_panel_menu() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	add_menu_page(
		__( 'GNN Theme Panel', 'gnn' ),
		'GNN',
		'manage_options',
		'gnn-panel',
		'gnn_panel_render',
		'dashicons-shield-alt',
		'59.100'
	);
	add_submenu_page(
		'gnn-panel',
		__( 'GNN Theme Panel', 'gnn' ),
		__( 'Theme settings', 'gnn' ),
		'manage_options',
		'gnn-panel',
		'gnn_panel_render'
	);
}
add_action( 'admin_menu', 'gnn_panel_menu' );

/**
 * Keep setup, configuration and maintenance in a consistent menu order.
 */
function gnn_panel_menu_reorder() {
	global $submenu;
	if ( empty( $submenu['gnn-panel'] ) ) {
		return;
	}
	$order = array_flip( array( 'gnn-panel', 'gnn-templates', 'gnn-assignments', 'gnn-design', 'edit.php?post_type=gnn_layout', 'edit.php?post_type=gnn_project', 'edit.php?post_type=gnn_album', 'edit.php?post_type=gnn_slide', 'gnn-workspace', 'gnn-addons', 'gnn-maintenance' ) );
	foreach ( $submenu['gnn-panel'] as $item ) {
		if ( ! isset( $order[ $item[2] ] ) ) {
			$order[ $item[2] ] = count( $order );
		}
	}
	usort(
		$submenu['gnn-panel'],
		function ( $a, $b ) use ( $order ) {
			return $order[ $a[2] ] <=> $order[ $b[2] ];
		}
	);
}
add_action( 'admin_menu', 'gnn_panel_menu_reorder', 999 );

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
	$saved = (array) get_option( 'gnn_options', array() );
	$out   = wp_parse_args( $saved, gnn_option_defaults() );
	if ( ! array_key_exists( 'post_meta', $saved ) && array_key_exists( 'blog_meta', $saved ) ) {
		$out['post_meta'] = (int) (bool) $saved['blog_meta'];
	}
	if ( ! is_array( $input ) ) {
		return $saved;
	}
	foreach ( $input as $value ) {
		if ( ! is_scalar( $value ) ) {
			add_settings_error( 'gnn_options', 'gnn_value_type', __( 'Settings were not saved: a field has an invalid value type.', 'gnn' ) );
			return $saved;
		}
	}

	$validation = gnn_validate_platform_input( $input );
	if ( is_wp_error( $validation ) ) {
		add_settings_error( 'gnn_options', $validation->get_error_code(), $validation->get_error_message() );
		return $saved;
	}
	$started = gnn_configuration_begin( 'save', gnn_configuration_form_revision() );
	if ( is_wp_error( $started ) ) {
		add_settings_error( 'gnn_options', $started->get_error_code(), $started->get_error_message() );
		return $saved;
	}

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
			$out[ $key ] = gnn_is_legacy_logo_url( $key, $input[ $key ] ) ? $input[ $key ] : absint( $input[ $key ] );
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

	// --- Optional colors. ---
	foreach ( array(
		'topbar_bg',
		'topbar_text_color',
		'title_overlay_bg',
		'accent_light',
		'accent_ink_dark',
		'accent_ink_light',
		'color_bg_dark',
		'color_bg_light',
		'color_bg2_dark',
		'color_bg2_light',
		'color_bg3_dark',
		'color_bg3_light',
		'color_fg_dark',
		'color_fg_light',
		'color_fg2_dark',
		'color_fg2_light',
		'color_line_dark',
		'color_line_light',
	) as $key ) {
		if ( ! isset( $input[ $key ] ) ) {
			continue;
		}
		$val         = trim( (string) $input[ $key ] );
		$out[ $key ] = ( '' === $val ) ? '' : (string) sanitize_hex_color( $val );
	}

	// --- Textareas. ---
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
		'title_overlay_font_size'    => array( 16, 120 ),
		'title_overlay_bg_opacity'   => array( 0, 100 ),
		'sidebar_width'              => array( 200, 500 ),
		'boxed_width'                => array( 600, 1600 ),
		'full_width'                 => array( 900, 1920 ),
	) as $key => $range ) {
		if ( isset( $input[ $key ] ) ) {
			$out[ $key ] = min( $range[1], max( $range[0], absint( $input[ $key ] ) ) );
		}
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Settings API checks the form nonce.
	if ( isset( $_POST['gnn_form_scope'] ) && 'design' === $_POST['gnn_form_scope'] ) {
		foreach ( gnn_operation_keys() as $key ) {
			$input[ $key ] = $saved[ $key ] ?? gnn_option_defaults()[ $key ];
		}
	}
	// --- Checkboxes. ---
	foreach ( array( 'show_toggle', 'remember_mode', 'sticky_header', 'show_search', 'show_cart', 'disable_emoji', 'disable_oembed', 'disable_migrate', 'heartbeat_slow', 'woo_scope', 'font_preload', 'mobile_dock', 'topbar_enable', 'smooth_scroll', 'scroll_top', 'scroll_anim', 'preloader', 'loading_screen', 'maintenance_mode', 'error404_search', 'slider_autoplay', 'slider_full_height', 'google_material_icons', 'typography_google_enable', 'title_overlay_enable' ) as $key ) {
		$out[ $key ] = empty( $input[ $key ] ) ? 0 : 1;
	}

	// --- Typography. ---
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

	// --- Button styles. ---
	foreach ( gnn_button_style_slots() as $gnn_btn_slot ) {
		$label_key = "btn_style_{$gnn_btn_slot}_label";
		if ( isset( $input[ $label_key ] ) ) {
			$out[ $label_key ] = wp_html_excerpt( sanitize_text_field( (string) $input[ $label_key ] ), 40, '' );
		}
		foreach ( array( 'bg', 'text', 'border' ) as $gnn_btn_part ) {
			$color_key = "btn_style_{$gnn_btn_slot}_{$gnn_btn_part}";
			if ( ! isset( $input[ $color_key ] ) ) {
				continue;
			}
			$val               = trim( (string) $input[ $color_key ] );
			$out[ $color_key ] = ( '' === $val ) ? '' : (string) sanitize_hex_color( $val );
		}
	}

	// --- Custom CSS. ---
	if ( isset( $input['custom_css'] ) ) {
		$out['custom_css'] = wp_strip_all_tags( (string) $input['custom_css'] );
	}

	// --- Script fields. ---
	foreach ( array( 'custom_js_head', 'custom_js_footer', 'head_html', 'body_html' ) as $key ) {
		if ( isset( $input[ $key ] ) ) {
			if ( current_user_can( 'unfiltered_html' ) ) {
				$out[ $key ] = (string) $input[ $key ];
			} else {
				add_settings_error( 'gnn_options', 'gnn_js_cap', __( 'Custom script fields were not saved: your account lacks the unfiltered_html capability.', 'gnn' ) );
			}
		}
	}

	return gnn_sanitize_platform_options( $out, $input );
}

/**
 * Admin assets — only on settings and management screens.
 *
 * @param string $hook Current admin page hook.
 */
function gnn_panel_assets( $hook ) {
	if ( ! in_array( $hook, array( 'toplevel_page_gnn-panel', 'gnn_page_gnn-workspace', 'gnn_page_gnn-design' ), true ) ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_style( 'gnn-admin', get_template_directory_uri() . '/assets/css/admin.css', array( 'wp-color-picker' ), GNN_VERSION );
	wp_enqueue_script( 'gnn-admin', get_template_directory_uri() . '/assets/js/admin.js', array( 'wp-color-picker' ), GNN_VERSION, true );
	gnn_panel_code_editors();
	$invalid = '';
	foreach ( get_settings_errors( 'gnn_options' ) as $error ) {
		if ( 0 === strpos( $error['code'], 'gnn_field_' ) ) {
			$invalid = substr( $error['code'], 10 );
			break;
		}
	}
	wp_localize_script(
		'gnn-admin',
		'gnnAdminText',
		array(
			'close'           => __( 'Close', 'gnn' ),
			'savedSuccess'    => __( 'Settings saved.', 'gnn' ),
			'settingsUpdated' => isset( $_GET['settings-updated'] ) && is_string( $_GET['settings-updated'] ) && in_array( wp_unslash( $_GET['settings-updated'] ), array( 'true', '1' ), true ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only WordPress save notice.
			'unsaved'         => __( 'Unsaved changes', 'gnn' ),
			'invalidField'    => $invalid,
			'chooseColor'     => __( 'Choose color', 'gnn' ),
			'ready'           => __( 'No pending changes', 'gnn' ),
			'saving'          => __( 'Saving settings…', 'gnn' ),
			'allGroups'       => __( 'All groups', 'gnn' ),
			'groupsLabel'     => __( 'Setting groups', 'gnn' ),
			/* translators: %d: Number of matching setting groups. */
			'searchResults'   => __( '%d setting groups found', 'gnn' ),
			'noResults'       => __( 'No settings match your search. Try another keyword.', 'gnn' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'gnn_panel_assets' );

/**
 * Initialize core CodeMirror editors.
 */
function gnn_panel_code_editors() {
	$fields = array(
		'gnn-custom_css'       => 'text/css',
		'gnn-custom_js_head'   => 'text/javascript',
		'gnn-custom_js_footer' => 'text/javascript',
		'gnn-head_html'        => 'text/html',
		'gnn-body_html'        => 'text/html',
	);
	$init   = '';
	foreach ( $fields as $id => $mime ) {
		$settings = wp_enqueue_code_editor( array( 'type' => $mime ) );
		if ( false === $settings ) {
			continue;
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

// ----- Visual Component and Field Helpers -------------------------------------

/**
 * Render a section divider bar.
 *
 * @param string $title Section bar title.
 */
function gnn_section_bar( $title ) {
	?>
	<div class="gnn-section-bar" aria-hidden="true">
		<span><?php echo esc_html( $title ); ?></span>
	</div>
	<?php
}

/**
 * Render visual diagram wireframe inside an option card.
 *
 * @param string $type Diagram type slug.
 */
function gnn_render_card_diagram( $type ) {
	switch ( $type ) {
		case 'header-standard':
			echo '<div class="gnn-diagram-header gnn-diagram-header--standard"><span class="gnn-diagram-logo"></span><span class="gnn-diagram-menu"><i></i><i></i><i></i></span></div>';
			break;
		case 'header-centered':
			echo '<div class="gnn-diagram-header gnn-diagram-header--centered"><span class="gnn-diagram-menu"><i></i><i></i></span><span class="gnn-diagram-logo"></span><span class="gnn-diagram-menu"><i></i><i></i></span></div>';
			break;
		case 'sidebar-right':
			echo '<div class="gnn-diagram-layout gnn-diagram-layout--sidebar-right"><span class="gnn-diagram-main"></span><span class="gnn-diagram-sidebar"></span></div>';
			break;
		case 'sidebar-left':
			echo '<div class="gnn-diagram-layout gnn-diagram-layout--sidebar-left"><span class="gnn-diagram-sidebar"></span><span class="gnn-diagram-main"></span></div>';
			break;
		case 'sidebar-none':
			echo '<div class="gnn-diagram-layout gnn-diagram-layout--sidebar-none"><span class="gnn-diagram-main gnn-diagram-main--full"></span></div>';
			break;
		case 'align-left':
			echo '<div class="gnn-diagram-align gnn-diagram-align--left"><i></i><i></i><i></i></div>';
			break;
		case 'align-center':
			echo '<div class="gnn-diagram-align gnn-diagram-align--center"><i></i><i></i><i></i></div>';
			break;
		case 'align-right':
			echo '<div class="gnn-diagram-align gnn-diagram-align--right"><i></i><i></i><i></i></div>';
			break;
		case 'brand-text':
			echo '<div class="gnn-diagram-brand gnn-diagram-brand--text"><span class="dashicons dashicons-editor-textcolor"></span></div>';
			break;
		case 'brand-image':
			echo '<div class="gnn-diagram-brand gnn-diagram-brand--image"><span class="dashicons dashicons-format-image"></span></div>';
			break;
		case 'brand-both':
			echo '<div class="gnn-diagram-brand gnn-diagram-brand--both"><span class="dashicons dashicons-format-image"></span></div>';
			break;
		case 'brand-none':
			echo '<div class="gnn-diagram-brand gnn-diagram-brand--none"><span class="dashicons dashicons-hidden"></span></div>';
			break;
		case 'fit-cover':
			echo '<div class="gnn-diagram-fit gnn-diagram-fit--cover"><span class="dashicons dashicons-editor-expand"></span></div>';
			break;
		case 'fit-contain':
			echo '<div class="gnn-diagram-fit gnn-diagram-fit--contain"><span class="dashicons dashicons-editor-contract"></span></div>';
			break;
		case 'fit-fill':
			echo '<div class="gnn-diagram-fit gnn-diagram-fit--fill"><span class="dashicons dashicons-image-rotate"></span></div>';
			break;
		case 'mode-dark':
			echo '<span class="dashicons dashicons-moon"></span>';
			break;
		case 'mode-light':
			echo '<span class="dashicons dashicons-lightbulb"></span>';
			break;
		default:
			echo '<span class="dashicons dashicons-admin-generic"></span>';
	}
}

/**
 * Render visual diagram option cards.
 *
 * @param string $key     Option key.
 * @param string $label   Label text.
 * @param array  $cards   Card presets.
 * @param string $current Current stored value.
 * @param string $hint    Optional description.
 */
function gnn_field_cards( $key, $label, $cards, $current, $hint = '' ) {
	?>
	<div class="gnn-setting-row gnn-setting-row--cards" data-key="<?php echo esc_attr( $key ); ?>">
		<div class="gnn-setting-label-col">
			<span class="gnn-setting-label" id="label-gnn-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></span>
			<?php if ( $hint ) : ?>
				<span class="gnn-setting-hint"><?php echo esc_html( $hint ); ?></span>
			<?php endif; ?>
		</div>
		<div class="gnn-setting-control-col">
			<div class="gnn-option-cards" role="radiogroup" aria-labelledby="label-gnn-<?php echo esc_attr( $key ); ?>" data-target="gnn-<?php echo esc_attr( $key ); ?>">
				<input type="hidden" id="gnn-<?php echo esc_attr( $key ); ?>" name="gnn_options[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $current ); ?>">
				<?php
				foreach ( $cards as $val => $data ) :
					$is_active = ( (string) $current === (string) $val );
					$type      = $data['type'] ?? 'default';
					?>
					<div class="gnn-option-card <?php echo $is_active ? 'is-selected' : ''; ?>" role="radio" aria-checked="<?php echo $is_active ? 'true' : 'false'; ?>" data-value="<?php echo esc_attr( $val ); ?>" tabindex="0">
						<div class="gnn-card-box">
							<div class="gnn-diagram gnn-diagram--<?php echo esc_attr( $type ); ?>" aria-hidden="true">
								<?php gnn_render_card_diagram( $type ); ?>
							</div>
						</div>
						<span class="gnn-card-label"><?php echo esc_html( $data['label'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Render a checkbox switch field.
 *
 * @param string $key   Option key.
 * @param string $label Label text.
 * @param string $hint  Optional description.
 */
function gnn_field_checkbox( $key, $label, $hint = '' ) {
	?>
	<div class="gnn-setting-row gnn-setting-row--switch" data-key="<?php echo esc_attr( $key ); ?>">
		<div class="gnn-setting-label-col">
			<label class="gnn-setting-label" for="gnn-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
			<?php if ( $hint ) : ?>
				<span class="gnn-setting-hint"><?php echo esc_html( $hint ); ?></span>
			<?php endif; ?>
		</div>
		<div class="gnn-setting-control-col">
			<label class="gnn-switch" for="gnn-<?php echo esc_attr( $key ); ?>">
				<input type="checkbox" id="gnn-<?php echo esc_attr( $key ); ?>" aria-label="<?php echo esc_attr( $label ); ?>" name="gnn_options[<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( (bool) gnn_option( $key ), true ); ?>>
				<span class="gnn-switch__track"><span class="gnn-switch__thumb"></span></span>
			</label>
		</div>
	</div>
	<?php
}

/**
 * Render a media-picker field.
 *
 * @param string     $key   Option key.
 * @param string     $label Label text.
 * @param int|string $value Attachment ID or legacy logo URL.
 * @param string     $hint  Optional description.
 */
function gnn_field_media( $key, $label, $value, $hint = '' ) {
	$img = $value ? wp_get_attachment_image( $value, 'medium', false, array( 'class' => 'gnn-media-preview' ) ) : '';
	if ( gnn_is_legacy_logo_url( $key, $value ) ) {
		$img = gnn_logo_img( $value, 0, 'gnn-media-preview' );
	}
	?>
	<div class="gnn-setting-row gnn-setting-row--media" data-key="<?php echo esc_attr( $key ); ?>">
		<div class="gnn-setting-label-col">
			<span class="gnn-setting-label"><?php echo esc_html( $label ); ?></span>
			<?php if ( $hint ) : ?>
				<span class="gnn-setting-hint"><?php echo esc_html( $hint ); ?></span>
			<?php endif; ?>
		</div>
		<div class="gnn-setting-control-col">
			<div class="gnn-media-field">
				<div class="gnn-media-box" data-target="<?php echo esc_attr( $key ); ?>"><?php echo $img; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
				<input type="hidden" id="gnn-media-<?php echo esc_attr( $key ); ?>" name="gnn_options[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $value ); ?>">
				<div class="gnn-media-actions">
					<button type="button" class="button gnn-btn-media gnn-media-pick" data-target="<?php echo esc_attr( $key ); ?>">
						<span class="dashicons dashicons-format-image" aria-hidden="true"></span>
						<span><?php esc_html_e( 'Select image', 'gnn' ); ?></span>
					</button>
					<button type="button" class="button gnn-btn-media gnn-btn-media--clear gnn-media-clear" data-target="<?php echo esc_attr( $key ); ?>">
						<span class="dashicons dashicons-trash" aria-hidden="true"></span>
						<span><?php esc_html_e( 'Remove', 'gnn' ); ?></span>
					</button>
				</div>
			</div>
		</div>
	</div>
	<?php
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
	?>
	<div class="gnn-setting-row gnn-setting-row--text" data-key="<?php echo esc_attr( $key ); ?>">
		<div class="gnn-setting-label-col">
			<label class="gnn-setting-label" for="gnn-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
			<?php if ( $hint ) : ?>
				<span class="gnn-setting-hint"><?php echo esc_html( $hint ); ?></span>
			<?php endif; ?>
		</div>
		<div class="gnn-setting-control-col">
			<input type="text" id="gnn-<?php echo esc_attr( $key ); ?>" name="gnn_options[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>" class="gnn-input regular-text">
		</div>
	</div>
	<?php
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
function gnn_field_textarea( $key, $label, $value, $rows = 5, $hint = '' ) {
	?>
	<div class="gnn-setting-row gnn-setting-row--textarea" data-key="<?php echo esc_attr( $key ); ?>">
		<div class="gnn-setting-label-col">
			<label class="gnn-setting-label" for="gnn-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
			<?php if ( $hint ) : ?>
				<span class="gnn-setting-hint"><?php echo esc_html( $hint ); ?></span>
			<?php endif; ?>
		</div>
		<div class="gnn-setting-control-col">
			<textarea id="gnn-<?php echo esc_attr( $key ); ?>" name="gnn_options[<?php echo esc_attr( $key ); ?>]" rows="<?php echo (int) $rows; ?>" class="gnn-textarea large-text code"><?php echo esc_textarea( $value ); ?></textarea>
		</div>
	</div>
	<?php
}

/**
 * Render a select field or visual option cards.
 *
 * @param string   $key     Option key.
 * @param string   $label   Label text.
 * @param string[] $options value => label map.
 * @param string   $hint    Optional description.
 */
function gnn_field_select( $key, $label, $options, $hint = '' ) {
	$current = (string) gnn_option( $key );
	if ( 'site_segment' === $key ) {
		$current = gnn_starter_profile_key( $current );
		$options = wp_list_pluck( gnn_starter_profiles(), 'label' );
	}

	$card_presets = array(
		'header_layout'           => array(
			'standard' => array(
				'label' => __( 'Standard (logo left, menu right)', 'gnn' ),
				'type'  => 'header-standard',
			),
			'centered' => array(
				'label' => __( 'Centered (logo center, split menus)', 'gnn' ),
				'type'  => 'header-centered',
			),
		),
		'sidebar_position'        => array(
			'right' => array(
				'label' => __( 'Right', 'gnn' ),
				'type'  => 'sidebar-right',
			),
			'left'  => array(
				'label' => __( 'Left', 'gnn' ),
				'type'  => 'sidebar-left',
			),
			'none'  => array(
				'label' => __( 'No sidebar', 'gnn' ),
				'type'  => 'sidebar-none',
			),
		),
		'header_menu_align'       => array(
			'left'   => array(
				'label' => __( 'Left', 'gnn' ),
				'type'  => 'align-left',
			),
			'center' => array(
				'label' => __( 'Center', 'gnn' ),
				'type'  => 'align-center',
			),
			'right'  => array(
				'label' => __( 'Right', 'gnn' ),
				'type'  => 'align-right',
			),
		),
		'footer_menu_align'       => array(
			'left'   => array(
				'label' => __( 'Left', 'gnn' ),
				'type'  => 'align-left',
			),
			'center' => array(
				'label' => __( 'Center', 'gnn' ),
				'type'  => 'align-center',
			),
			'right'  => array(
				'label' => __( 'Right', 'gnn' ),
				'type'  => 'align-right',
			),
		),
		'footer_brand_type'       => array(
			'text'  => array(
				'label' => __( 'Text (site title)', 'gnn' ),
				'type'  => 'brand-text',
			),
			'image' => array(
				'label' => __( 'Image logo', 'gnn' ),
				'type'  => 'brand-image',
			),
			'both'  => array(
				'label' => __( 'Image + text', 'gnn' ),
				'type'  => 'brand-both',
			),
			'none'  => array(
				'label' => __( 'Hide', 'gnn' ),
				'type'  => 'brand-none',
			),
		),
		'page_featured_image_fit' => array(
			'cover'   => array(
				'label' => __( 'Cover (fill and crop)', 'gnn' ),
				'type'  => 'fit-cover',
			),
			'contain' => array(
				'label' => __( 'Contain (show the whole image)', 'gnn' ),
				'type'  => 'fit-contain',
			),
			'fill'    => array(
				'label' => __( 'Fill (stretch)', 'gnn' ),
				'type'  => 'fit-fill',
			),
		),
		'post_featured_image_fit' => array(
			'cover'   => array(
				'label' => __( 'Cover (fill and crop)', 'gnn' ),
				'type'  => 'fit-cover',
			),
			'contain' => array(
				'label' => __( 'Contain (show the whole image)', 'gnn' ),
				'type'  => 'fit-contain',
			),
			'fill'    => array(
				'label' => __( 'Fill (stretch)', 'gnn' ),
				'type'  => 'fit-fill',
			),
		),
	);

	if ( isset( $card_presets[ $key ] ) ) {
		gnn_field_cards( $key, $label, $card_presets[ $key ], $current, $hint );
		return;
	}

	echo '<div class="gnn-setting-row gnn-setting-row--select" data-key="' . esc_attr( $key ) . '">';
	echo '<div class="gnn-setting-label-col">';
	echo '<label class="gnn-setting-label" for="gnn-' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label>';
	if ( $hint ) {
		echo '<span class="gnn-setting-hint">' . esc_html( $hint ) . '</span>';
	}
	echo '</div>';
	echo '<div class="gnn-setting-control-col">';
	echo '<select id="gnn-' . esc_attr( $key ) . '" name="gnn_options[' . esc_attr( $key ) . ']" class="gnn-select">';
	foreach ( $options as $value => $text ) {
		echo '<option value="' . esc_attr( $value ) . '" ' . selected( $current, (string) $value, false ) . '>' . esc_html( $text ) . '</option>';
	}
	echo '</select>';
	echo '</div></div>';
}

/**
 * Render a number field as a range slider + number input pair.
 *
 * @param string $key   Option key.
 * @param string $label Label text.
 * @param int    $min   Minimum.
 * @param int    $max   Maximum.
 * @param string $hint  Optional description.
 */
function gnn_field_number( $key, $label, $min = 0, $max = 9999, $hint = '' ) {
	$val = gnn_option( $key );
	if ( null === $val || '' === $val ) {
		$val = $min;
	}
	$val  = (int) $val;
	$unit = 'px';
	if ( false !== strpos( $key, 'interval' ) ) {
		$unit = 's';
	} elseif ( false !== strpos( $key, 'opacity' ) ) {
		$unit = '%';
	} elseif ( false !== strpos( $key, 'columns' ) || false !== strpos( $key, 'per_page' ) || false !== strpos( $key, 'excerpt' ) ) {
		$unit = '';
	}
	?>
	<div class="gnn-setting-row gnn-setting-row--range" data-key="<?php echo esc_attr( $key ); ?>">
		<div class="gnn-setting-label-col">
			<label class="gnn-setting-label" for="gnn-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
			<?php if ( $hint ) : ?>
				<span class="gnn-setting-hint"><?php echo esc_html( $hint ); ?></span>
			<?php endif; ?>
		</div>
		<div class="gnn-setting-control-col">
			<div class="gnn-range-group">
				<div class="gnn-range-slider-wrap">
					<input type="range" id="gnn-slider-<?php echo esc_attr( $key ); ?>" class="gnn-range-slider" min="<?php echo (int) $min; ?>" max="<?php echo (int) $max; ?>" value="<?php echo (int) $val; ?>" data-target="gnn-<?php echo esc_attr( $key ); ?>" aria-label="<?php echo esc_attr( $label ); ?>">
					<div class="gnn-range-scale" aria-hidden="true">
						<span><?php echo (int) $min; ?></span>
						<span><?php echo (int) $max; ?></span>
					</div>
				</div>
				<div class="gnn-range-input-wrap">
					<input type="number" id="gnn-<?php echo esc_attr( $key ); ?>" name="gnn_options[<?php echo esc_attr( $key ); ?>]" value="<?php echo (int) $val; ?>" min="<?php echo (int) $min; ?>" max="<?php echo (int) $max; ?>" class="gnn-range-number" data-slider="gnn-slider-<?php echo esc_attr( $key ); ?>">
					<?php if ( $unit ) : ?>
						<span class="gnn-range-unit"><?php echo esc_html( $unit ); ?></span>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Render a color field using Iris color picker.
 *
 * @param string $key            Option key.
 * @param string $label          Label text.
 * @param string $value          Current stored value.
 * @param string $default_swatch Swatch color shown when empty.
 * @param string $hint           Optional description.
 * @param bool   $allow_empty    Whether an empty value is allowed.
 */
function gnn_field_color( $key, $label, $value, $default_swatch = '#000000', $hint = '', $allow_empty = false ) {
	$value = (string) $value;
	?>
	<div class="gnn-setting-row gnn-setting-row--color" data-key="<?php echo esc_attr( $key ); ?>">
		<div class="gnn-setting-label-col">
			<label class="gnn-setting-label" for="gnn-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
			<?php if ( $hint ) : ?>
				<span class="gnn-setting-hint"><?php echo esc_html( $hint ); ?></span>
			<?php endif; ?>
		</div>
		<div class="gnn-setting-control-col">
			<div class="gnn-color-field">
				<input type="text" id="gnn-<?php echo esc_attr( $key ); ?>" name="gnn_options[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $value ); ?>" class="gnn-color-picker" data-default-color="<?php echo esc_attr( $default_swatch ); ?>" maxlength="7" pattern="^#[0-9a-fA-F]{6}$">
				<?php if ( $allow_empty ) : ?>
					<button type="button" class="button gnn-color-clear" data-target="gnn-<?php echo esc_attr( $key ); ?>"><?php esc_html_e( 'Clear', 'gnn' ); ?></button>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Render a Dark/Light color pair.
 *
 * @param string $key           Option key prefix.
 * @param string $label         Label text.
 * @param string $dark_default  Dark swatch default.
 * @param string $light_default Light swatch default.
 * @param string $hint          Optional description.
 */
function gnn_field_color_pair( $key, $label, $dark_default, $light_default, $hint = '' ) {
	$dark  = (string) gnn_option( "{$key}_dark" );
	$light = (string) gnn_option( "{$key}_light" );
	?>
	<div class="gnn-setting-row gnn-setting-row--color-pair" data-key="<?php echo esc_attr( $key ); ?>">
		<div class="gnn-setting-label-col">
			<span class="gnn-setting-label"><?php echo esc_html( $label ); ?></span>
			<?php if ( $hint ) : ?>
				<span class="gnn-setting-hint"><?php echo esc_html( $hint ); ?></span>
			<?php endif; ?>
		</div>
		<div class="gnn-setting-control-col">
			<div class="gnn-color-pair__row">
				<div class="gnn-color-pair__item">
					<span class="gnn-color-pair__tag"><?php esc_html_e( 'Dark', 'gnn' ); ?></span>
					<div class="gnn-color-field">
						<input type="text" id="gnn-<?php echo esc_attr( "{$key}_dark" ); ?>" aria-label="<?php echo esc_attr( $label . ' — ' . __( 'Dark', 'gnn' ) ); ?>" name="gnn_options[<?php echo esc_attr( "{$key}_dark" ); ?>]" value="<?php echo esc_attr( $dark ); ?>" class="gnn-color-picker" data-default-color="<?php echo esc_attr( $dark_default ); ?>" maxlength="7" pattern="^#[0-9a-fA-F]{6}$">
						<button type="button" class="button gnn-color-clear" data-target="gnn-<?php echo esc_attr( "{$key}_dark" ); ?>"><?php esc_html_e( 'Clear', 'gnn' ); ?></button>
					</div>
				</div>
				<div class="gnn-color-pair__item">
					<span class="gnn-color-pair__tag"><?php esc_html_e( 'Light', 'gnn' ); ?></span>
					<div class="gnn-color-field">
						<input type="text" id="gnn-<?php echo esc_attr( "{$key}_light" ); ?>" aria-label="<?php echo esc_attr( $label . ' — ' . __( 'Light', 'gnn' ) ); ?>" name="gnn_options[<?php echo esc_attr( "{$key}_light" ); ?>]" value="<?php echo esc_attr( $light ); ?>" class="gnn-color-picker" data-default-color="<?php echo esc_attr( $light_default ); ?>" maxlength="7" pattern="^#[0-9a-fA-F]{6}$">
						<button type="button" class="button gnn-color-clear" data-target="gnn-<?php echo esc_attr( "{$key}_light" ); ?>"><?php esc_html_e( 'Clear', 'gnn' ); ?></button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
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
		'blog'       => __( 'Blog', 'gnn' ),
		'styles'     => __( 'Styles', 'gnn' ),
		'typography' => __( 'Typography', 'gnn' ),
		'code'       => __( 'Custom Code', 'gnn' ),
		'advanced'   => __( 'Advanced', 'gnn' ),
	);

	$tab_icons = array(
		'global'     => 'admin-generic',
		'header'     => 'align-center',
		'footer'     => 'arrow-down-alt2',
		'pages'      => 'admin-page',
		'blog'       => 'admin-post',
		'styles'     => 'art',
		'typography' => 'editor-textcolor',
		'code'       => 'editor-code',
		'advanced'   => 'admin-tools',
	);
	?>
	<div class="wrap gnn-panel" id="gnn-theme-panel">
		<header class="gnn-panel-header">
			<div class="gnn-panel-branding">
				<span class="gnn-brand-icon dashicons dashicons-shield-alt" aria-hidden="true"></span>
				<span class="gnn-brand-title">GNN</span>
			</div>
			<div class="gnn-panel-header-center">
				<h1 id="gnn-page-heading" class="gnn-panel-title"><?php echo esc_html( $tabs['global'] ); ?></h1>
			</div>
			<div class="gnn-panel-header-actions">
				<button type="submit" form="gnn-settings-form" class="gnn-save-btn" id="gnn-save-header">
					<span class="dashicons dashicons-saved" aria-hidden="true"></span>
					<span class="btn-text"><?php esc_html_e( 'Save Settings', 'gnn' ); ?></span>
				</button>
				<a class="gnn-header-tool-btn" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'View site', 'gnn' ); ?>" title="<?php esc_attr_e( 'View site', 'gnn' ); ?>">
					<span class="dashicons dashicons-external" aria-hidden="true"></span>
				</a>
				<span class="gnn-header-badge">v<?php echo esc_html( GNN_VERSION ); ?></span>
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

		<?php settings_errors( 'gnn_options' ); ?>

		<div class="gnn-panel-layout">
			<aside class="gnn-panel-sidebar">
				<nav class="gnn-sidebar-nav gnn-tabs" aria-label="<?php esc_attr_e( 'Theme sections', 'gnn' ); ?>">
					<?php foreach ( $tabs as $id => $label ) : ?>
						<?php if ( 'global' === $id ) : ?>
							<div class="gnn-nav-group-title"><?php esc_html_e( 'Site structure', 'gnn' ); ?></div>
						<?php elseif ( 'styles' === $id ) : ?>
							<div class="gnn-nav-group-title"><?php esc_html_e( 'Brand and appearance', 'gnn' ); ?></div>
						<?php elseif ( 'code' === $id ) : ?>
							<div class="gnn-nav-group-title"><?php esc_html_e( 'Administration', 'gnn' ); ?></div>
						<?php endif; ?>
						<a id="gnn-nav-<?php echo esc_attr( $id ); ?>" href="#gnn-tab-<?php echo esc_attr( $id ); ?>" class="nav-tab gnn-nav-item" data-tab="<?php echo esc_attr( $id ); ?>" data-title="<?php echo esc_attr( $label ); ?>">
							<span class="dashicons dashicons-<?php echo esc_attr( $tab_icons[ $id ] ); ?>" aria-hidden="true"></span>
							<span><?php echo esc_html( $label ); ?></span>
						</a>
					<?php endforeach; ?>
				</nav>
				<div class="gnn-sidebar-secondary">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=gnn-workspace' ) ); ?>"><span class="dashicons dashicons-dashboard" aria-hidden="true"></span><?php esc_html_e( 'Setup wizard', 'gnn' ); ?></a>
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=gnn_layout' ) ); ?>"><span class="dashicons dashicons-layout" aria-hidden="true"></span><?php esc_html_e( 'Reusable blocks', 'gnn' ); ?></a>
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=gnn_slide' ) ); ?>"><span class="dashicons dashicons-images-alt2" aria-hidden="true"></span><?php esc_html_e( 'Slider', 'gnn' ); ?></a>
				</div>
				<div class="gnn-sidebar-footer">
					<?php esc_html_e( 'Theme version', 'gnn' ); ?> <?php echo esc_html( GNN_VERSION ); ?>
				</div>
			</aside>

			<div class="gnn-panel-content">
				<div class="gnn-panel-toolbar">
					<div class="gnn-search-box">
						<span class="dashicons dashicons-search" aria-hidden="true"></span>
						<input type="search" id="gnn-settings-search" placeholder="<?php esc_attr_e( 'Search settings…', 'gnn' ); ?>" autocomplete="off" aria-label="<?php esc_attr_e( 'Search settings…', 'gnn' ); ?>">
					</div>
				</div>
				<p class="gnn-search-feedback" role="status" hidden></p>

				<form id="gnn-settings-form" method="post" action="options.php">
					<?php settings_fields( 'gnn_options_group' ); ?>
					<input type="hidden" name="gnn_form_scope" value="design">
					<?php gnn_configuration_revision_field(); ?>
					<input type="hidden" name="gnn_options[platform_fields_present]" value="1">

					<section id="gnn-tab-global" class="gnn-tab">
						<?php gnn_section_bar( __( 'Appearance and behavior', 'gnn' ) ); ?>
						<?php
						gnn_field_cards(
							'default_mode',
							__( 'Default mode', 'gnn' ),
							array(
								'dark'  => array(
									'label' => __( 'Dark', 'gnn' ),
									'type'  => 'mode-dark',
								),
								'light' => array(
									'label' => __( 'Light', 'gnn' ),
									'type'  => 'mode-light',
								),
							),
							get_theme_mod( 'gnn_default_theme', 'dark' )
						);
						?>
						<?php gnn_field_checkbox( 'show_toggle', __( 'Show the dark/light toggle in the header', 'gnn' ), __( 'Turn this off to lock the whole site to a single style — the Default mode selected above becomes the only style every visitor sees, with no way to switch (this also removes any previously remembered per-visitor choice).', 'gnn' ) ); ?>
						<?php gnn_field_checkbox( 'remember_mode', __( 'Remember each visitor\'s choice (localStorage)', 'gnn' ), __( 'Only applies while the toggle above is visible.', 'gnn' ) ); ?>
						<?php gnn_field_media( 'site_icon', __( 'Favicon (site icon, 512×512)', 'gnn' ), (int) get_option( 'site_icon', 0 ) ); ?>

						<?php gnn_section_bar( __( 'Interactions', 'gnn' ) ); ?>
						<?php gnn_field_checkbox( 'smooth_scroll', __( 'Smooth scroll for anchor links', 'gnn' ) ); ?>
						<?php gnn_field_checkbox( 'scroll_top', __( 'Show the “Scroll to top” button', 'gnn' ) ); ?>
						<?php gnn_field_checkbox( 'scroll_anim', __( 'Scroll-in animations for blocks/images', 'gnn' ), __( 'Adds reveal-on-scroll to elements with the gnn-anim class (respects reduced-motion).', 'gnn' ) ); ?>

						<?php gnn_section_bar( __( 'Hero slider', 'gnn' ) ); ?>
						<?php gnn_field_checkbox( 'slider_autoplay', __( 'Autoplay', 'gnn' ) ); ?>
						<?php gnn_field_number( 'slider_interval', __( 'Autoplay interval (seconds)', 'gnn' ), 2, 30 ); ?>
						<?php gnn_field_checkbox( 'slider_full_height', __( 'Full-height slider (fills the screen below the header)', 'gnn' ) ); ?>
						<?php gnn_field_checkbox( 'preloader', __( 'Top loading progress bar', 'gnn' ) ); ?>
						<?php gnn_field_checkbox( 'loading_screen', __( 'Full-screen loading overlay', 'gnn' ) ); ?>

						<?php gnn_platform_fields( 'global' ); ?>
					</section>

					<section id="gnn-tab-header" class="gnn-tab">
						<?php gnn_section_bar( __( 'Brand assets', 'gnn' ) ); ?>
						<div class="gnn-logo-grid">
							<?php gnn_field_media( 'logo_light', __( 'Logo — light mode (1×)', 'gnn' ), (int) gnn_option( 'logo_light' ) ); ?>
							<?php gnn_field_media( 'logo_light_2x', __( 'Logo — light mode (2× retina)', 'gnn' ), (int) gnn_option( 'logo_light_2x' ) ); ?>
							<?php gnn_field_media( 'logo_dark', __( 'Logo — dark mode (1×)', 'gnn' ), (int) gnn_option( 'logo_dark' ) ); ?>
							<?php gnn_field_media( 'logo_dark_2x', __( 'Logo — dark mode (2× retina)', 'gnn' ), (int) gnn_option( 'logo_dark_2x' ) ); ?>
						</div>
						<?php gnn_field_number( 'logo_max_height', __( 'Logo max height — desktop (px)', 'gnn' ), 12, 200 ); ?>
						<?php gnn_field_number( 'logo_max_height_mobile', __( 'Logo max height — mobile (px)', 'gnn' ), 12, 200 ); ?>

						<?php gnn_section_bar( __( 'Layout', 'gnn' ) ); ?>
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

						<?php gnn_section_bar( __( 'Top bar', 'gnn' ) ); ?>
						<?php gnn_field_checkbox( 'topbar_enable', __( 'Enable the top announcement bar', 'gnn' ) ); ?>
						<?php gnn_field_text( 'topbar_text', __( 'Announcement text', 'gnn' ), (string) gnn_option( 'topbar_text' ) ); ?>
						<?php gnn_field_text( 'topbar_email', __( 'Email', 'gnn' ), (string) gnn_option( 'topbar_email' ) ); ?>
						<?php gnn_field_text( 'topbar_phone', __( 'Phone', 'gnn' ), (string) gnn_option( 'topbar_phone' ) ); ?>

						<?php gnn_platform_fields( 'header' ); ?>
					</section>

					<section id="gnn-tab-footer" class="gnn-tab">
						<?php gnn_section_bar( __( 'Footer branding', 'gnn' ) ); ?>
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

						<?php gnn_platform_fields( 'footer' ); ?>
					</section>

					<section id="gnn-tab-pages" class="gnn-tab">
						<?php gnn_section_bar( __( 'Page structure', 'gnn' ) ); ?>
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

						<?php gnn_section_bar( __( 'Layout widths', 'gnn' ) ); ?>
						<?php gnn_field_number( 'sidebar_width', __( 'Sidebar width (px)', 'gnn' ), 200, 500, __( 'Sidebar column width.', 'gnn' ) ); ?>
						<?php gnn_field_number( 'boxed_width', __( 'Boxed template max width (px)', 'gnn' ), 600, 1600 ); ?>
						<?php gnn_field_number( 'full_width', __( 'Full Width template max width (px)', 'gnn' ), 900, 1920 ); ?>

						<?php gnn_section_bar( __( 'Featured image', 'gnn' ) ); ?>
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
							__( 'Visible area when cropped.', 'gnn' )
						);
						?>

						<?php gnn_section_bar( __( 'Title over featured image', 'gnn' ) ); ?>
						<?php
						gnn_field_checkbox(
							'title_overlay_enable',
							__( 'Show the page title centered inside the featured image', 'gnn' ),
							__( 'Off by default. Replaces the normal title above the content with a centered title overlaid on the featured image itself. Any page can override this individually — see "Title over featured image" in its GNN Display Options box.', 'gnn' )
						);
						gnn_field_number( 'title_overlay_font_size', __( 'Overlay title font size (px)', 'gnn' ), 16, 120 );
						gnn_field_color( 'title_overlay_bg', __( 'Overlay title background color', 'gnn' ), (string) gnn_option( 'title_overlay_bg' ), '#000000' );
						gnn_field_number( 'title_overlay_bg_opacity', __( 'Overlay title background opacity (%)', 'gnn' ), 0, 100 );
						?>

						<?php if ( class_exists( 'WooCommerce' ) ) : ?>
							<?php gnn_field_number( 'shop_columns', __( 'Shop columns', 'gnn' ), 2, 6 ); ?>
							<?php gnn_field_number( 'shop_per_page', __( 'Products per page', 'gnn' ), 2, 48 ); ?>
						<?php endif; ?>

						<?php gnn_section_bar( __( '404 page', 'gnn' ) ); ?>
						<?php gnn_field_text( 'error404_title', __( '404 title', 'gnn' ), (string) gnn_option( 'error404_title' ), __( 'Page not found', 'gnn' ) ); ?>
						<?php gnn_field_textarea( 'error404_text', __( '404 description', 'gnn' ), (string) gnn_option( 'error404_text' ), 2 ); ?>
						<?php gnn_field_checkbox( 'error404_search', __( 'Show a search box on the 404 page', 'gnn' ) ); ?>
						<?php gnn_field_text( 'error404_button', __( '“Back to home” button text', 'gnn' ), (string) gnn_option( 'error404_button' ), __( 'Back to Home', 'gnn' ) ); ?>
						<?php gnn_field_media( 'error404_image', __( '404 image', 'gnn' ), (int) gnn_option( 'error404_image' ) ); ?>

						<?php gnn_platform_fields( 'pages' ); ?>
					</section>

					<section id="gnn-tab-blog" class="gnn-tab">
						<?php
						gnn_platform_fields( 'blog_archive' );
						gnn_platform_fields( 'blog_cards' );
						gnn_platform_fields( 'blog_single' );
						gnn_platform_fields( 'blog_media' );
						?>
					</section>

					<section id="gnn-tab-styles" class="gnn-tab">
						<?php gnn_section_bar( __( 'Accent', 'gnn' ) ); ?>
						<?php
						$gnn_accent_dark  = (string) get_theme_mod( 'gnn_accent_color', '#34d399' );
						$gnn_accent_light = (string) gnn_option( 'accent_light' );
						?>
						<div class="gnn-setting-row gnn-setting-row--color-pair" data-key="accent">
							<div class="gnn-setting-label-col">
								<span class="gnn-setting-label"><?php esc_html_e( 'Accent color', 'gnn' ); ?></span>
							</div>
							<div class="gnn-setting-control-col">
								<div class="gnn-color-pair__row">
									<div class="gnn-color-pair__item">
										<span class="gnn-color-pair__tag"><?php esc_html_e( 'Dark', 'gnn' ); ?></span>
										<div class="gnn-color-field">
											<input type="text" id="gnn-accent" name="gnn_options[accent]" value="<?php echo esc_attr( $gnn_accent_dark ); ?>" class="gnn-color-picker" data-default-color="#34d399" maxlength="7" pattern="^#[0-9a-fA-F]{6}$">
										</div>
									</div>
									<div class="gnn-color-pair__item">
										<span class="gnn-color-pair__tag"><?php esc_html_e( 'Light', 'gnn' ); ?></span>
										<div class="gnn-color-field">
											<input type="text" id="gnn-accent_light" name="gnn_options[accent_light]" value="<?php echo esc_attr( '' !== $gnn_accent_light ? $gnn_accent_light : $gnn_accent_dark ); ?>" class="gnn-color-picker" data-default-color="<?php echo esc_attr( $gnn_accent_dark ); ?>" maxlength="7" pattern="^#[0-9a-fA-F]{6}$">
											<button type="button" class="button gnn-color-clear" data-target="gnn-accent_light"><?php esc_html_e( 'Clear', 'gnn' ); ?></button>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php
						gnn_field_color_pair(
							'accent_ink',
							__( 'Text on accent', 'gnn' ),
							'#062416',
							'#04150c',
							__( 'The text/icon color painted on top of the accent color (e.g. button labels). If you pick a bright custom accent, set this for readable contrast.', 'gnn' )
						);
						?>

						<?php gnn_section_bar( __( 'Surfaces', 'gnn' ) ); ?>
						<?php
						gnn_field_color_pair( 'color_bg', __( 'Page background', 'gnn' ), '#0a0a0b', '#fafafa' );
						gnn_field_color_pair( 'color_bg2', __( 'Card / panel background', 'gnn' ), '#121214', '#ffffff' );
						gnn_field_color_pair( 'color_bg3', __( 'Alternate surface background', 'gnn' ), '#1b1b1f', '#efeff1' );
						?>

						<?php gnn_section_bar( __( 'Text & Borders', 'gnn' ) ); ?>
						<?php
						gnn_field_color_pair( 'color_fg', __( 'Primary text', 'gnn' ), '#f5f5f4', '#101013' );
						gnn_field_color_pair( 'color_fg2', __( 'Muted text', 'gnn' ), '#9d9da4', '#5c5c64' );
						gnn_field_color_pair( 'color_line', __( 'Borders / dividers', 'gnn' ), '#232328', '#e5e5e9' );
						?>

						<?php gnn_section_bar( __( 'Top bar', 'gnn' ) ); ?>
						<?php
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

						<?php gnn_section_bar( __( 'Button Styles', 'gnn' ) ); ?>
						<div class="gnn-btn-styles">
							<?php foreach ( gnn_button_style_slots() as $gnn_btn_slot ) : ?>
								<?php
								$gnn_btn_label         = (string) gnn_option( "btn_style_{$gnn_btn_slot}_label" );
								$gnn_btn_class         = gnn_button_style_class( $gnn_btn_slot );
								$gnn_btn_bg            = (string) gnn_option( "btn_style_{$gnn_btn_slot}_bg" );
								$gnn_btn_text          = (string) gnn_option( "btn_style_{$gnn_btn_slot}_text" );
								$gnn_btn_border        = (string) gnn_option( "btn_style_{$gnn_btn_slot}_border" );
								$gnn_btn_preview_style = '';
								if ( '' !== $gnn_btn_bg ) {
									$gnn_btn_preview_style .= 'background:' . $gnn_btn_bg . ';';
								}
								if ( '' !== $gnn_btn_text ) {
									$gnn_btn_preview_style .= 'color:' . $gnn_btn_text . ';';
								}
								if ( '' !== $gnn_btn_border ) {
									$gnn_btn_preview_style .= 'border:1px solid ' . $gnn_btn_border . ';';
								}
								?>
								<div class="gnn-btn-style-card">
									<div class="gnn-btn-style-card__head">
										<strong>
										<?php
										/* translators: %d: slot number (1-6). */
										printf( esc_html__( 'Style %d', 'gnn' ), (int) $gnn_btn_slot );
										?>
										</strong>
										<code class="gnn-btn-style-card__class"><?php echo esc_html( '.' . $gnn_btn_class ); ?></code>
									</div>
									<input type="text" name="gnn_options[btn_style_<?php echo esc_attr( $gnn_btn_slot ); ?>_label]" value="<?php echo esc_attr( $gnn_btn_label ); ?>" placeholder="<?php esc_attr_e( 'Name (e.g. Primary) — leave empty to skip', 'gnn' ); ?>" class="gnn-input regular-text">
									<div class="gnn-btn-style-card__colors">
										<?php gnn_field_color( "btn_style_{$gnn_btn_slot}_bg", __( 'Background', 'gnn' ), $gnn_btn_bg, '#34d399', '', true ); ?>
										<?php gnn_field_color( "btn_style_{$gnn_btn_slot}_text", __( 'Text', 'gnn' ), $gnn_btn_text, '#062416', '', true ); ?>
										<?php gnn_field_color( "btn_style_{$gnn_btn_slot}_border", __( 'Border', 'gnn' ), $gnn_btn_border, '#232328', '', true ); ?>
									</div>
									<?php if ( '' !== $gnn_btn_label ) : ?>
										<div class="gnn-btn-style-card__preview">
											<button type="button" class="gnn-btn <?php echo esc_attr( $gnn_btn_class ); ?>" style="<?php echo esc_attr( $gnn_btn_preview_style ); ?>"><?php echo esc_html( $gnn_btn_label ); ?></button>
										</div>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						</div>

						<?php gnn_platform_fields( 'styles' ); ?>
					</section>

					<section id="gnn-tab-typography" class="gnn-tab">
						<?php gnn_section_bar( __( 'Font loading', 'gnn' ) ); ?>
						<?php
						gnn_field_checkbox(
							'typography_google_enable',
							__( 'Use Google Fonts instead', 'gnn' ),
							__( 'Off by default. When on, any element below with a font name set loads that Google Font; elements left empty keep the fast self-hosted default. Every font you pick is combined into a single stylesheet request, never one per font.', 'gnn' )
						);
						?>

						<?php gnn_section_bar( __( 'Fonts by element', 'gnn' ) ); ?>
						<div class="gnn-typo-grid">
							<?php foreach ( gnn_typography_roles() as $gnn_typo_role => $gnn_typo_label ) : ?>
								<div class="gnn-typo-row">
									<span class="gnn-typo-row__label"><?php echo esc_html( $gnn_typo_label ); ?></span>
									<input type="text" aria-label="<?php echo esc_attr( $gnn_typo_label . ' — ' . __( 'Font family', 'gnn' ) ); ?>" name="gnn_options[typo_<?php echo esc_attr( $gnn_typo_role ); ?>_family]" value="<?php echo esc_attr( (string) gnn_option( "typo_{$gnn_typo_role}_family" ) ); ?>" placeholder="<?php esc_attr_e( 'Theme default', 'gnn' ); ?>" class="gnn-input regular-text">
									<select aria-label="<?php echo esc_attr( $gnn_typo_label . ' — ' . __( 'Font weight', 'gnn' ) ); ?>" name="gnn_options[typo_<?php echo esc_attr( $gnn_typo_role ); ?>_weight]" class="gnn-select">
										<?php foreach ( gnn_typography_weights() as $gnn_typo_weight ) : ?>
											<option value="<?php echo esc_attr( $gnn_typo_weight ); ?>" <?php selected( (string) gnn_option( "typo_{$gnn_typo_role}_weight" ), $gnn_typo_weight ); ?>><?php echo esc_html( $gnn_typo_weight ); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							<?php endforeach; ?>
						</div>

						<?php gnn_section_bar( __( 'Icons', 'gnn' ) ); ?>
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

						<?php gnn_platform_fields( 'typography' ); ?>
					</section>

					<section id="gnn-tab-code" class="gnn-tab">
						<?php gnn_section_bar( __( 'Custom CSS', 'gnn' ) ); ?>
						<?php gnn_field_textarea( 'custom_css', __( 'Custom CSS', 'gnn' ), (string) gnn_option( 'custom_css' ), 8 ); ?>

						<?php gnn_section_bar( __( 'Analytics and consent', 'gnn' ) ); ?>
						<?php gnn_field_text( 'ga4_id', __( 'Google Analytics 4 ID', 'gnn' ), (string) gnn_option( 'ga4_id' ), 'G-XXXXXXXXXX' ); ?>
						<?php gnn_field_text( 'gtm_id', __( 'Google Tag Manager ID', 'gnn' ), (string) gnn_option( 'gtm_id' ), 'GTM-XXXXXXX' ); ?>

						<?php gnn_section_bar( __( 'JavaScript', 'gnn' ) ); ?>
						<?php gnn_field_textarea( 'custom_js_head', __( 'Custom JavaScript — head', 'gnn' ), (string) gnn_option( 'custom_js_head' ), 5, __( 'Raw JS, printed inside a <script> tag in <head>. Requires the unfiltered_html capability.', 'gnn' ) ); ?>
						<?php gnn_field_textarea( 'custom_js_footer', __( 'Custom JavaScript — footer', 'gnn' ), (string) gnn_option( 'custom_js_footer' ), 5 ); ?>

						<?php gnn_section_bar( __( 'Verification and custom markup', 'gnn' ) ); ?>
						<?php gnn_field_textarea( 'head_html', __( 'Extra <head> HTML (meta/verification tags)', 'gnn' ), (string) gnn_option( 'head_html' ), 4 ); ?>
						<?php gnn_field_textarea( 'body_html', __( 'After <body> HTML', 'gnn' ), (string) gnn_option( 'body_html' ), 4 ); ?>

						<?php gnn_platform_fields( 'code' ); ?>
					</section>

					<div class="gnn-savebar">
						<p class="gnn-save-status" role="status"><?php esc_html_e( 'No pending changes', 'gnn' ); ?></p>
						<?php submit_button( __( 'Save Settings', 'gnn' ), 'primary gnn-btn-save', 'submit', false ); ?>
					</div>
				</form>

				<?php gnn_panel_advanced_tab(); ?>
			</div>
		</div>

		<div id="gnn-toast-container" class="gnn-toast-container" aria-live="polite"></div>
	</div>
	<?php
}

// ----- Advanced tab: reset / export / import ----------------------------------

/**
 * Render the Advanced tab.
 */
function gnn_panel_advanced_tab() {
	?>
	<section id="gnn-tab-advanced" class="gnn-tab">
		<?php gnn_section_bar( __( 'Export settings', 'gnn' ) ); ?>
		<div class="gnn-setting-row">
			<div class="gnn-setting-label-col">
				<span class="gnn-setting-label"><?php esc_html_e( 'Export settings', 'gnn' ); ?></span>
				<span class="gnn-setting-hint"><?php esc_html_e( 'Download all GNN Theme settings as a JSON file for backup or transfer.', 'gnn' ); ?></span>
			</div>
			<div class="gnn-setting-control-col">
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="gnn-inline-form">
					<input type="hidden" name="action" value="gnn_export_settings">
					<label>
						<?php esc_html_e( 'Export scope', 'gnn' ); ?>
						<select name="gnn_export_scope" class="gnn-select">
							<option value="design"><?php esc_html_e( 'Portable design profile (no site identity or code)', 'gnn' ); ?></option>
							<option value="site"><?php esc_html_e( 'Site settings (includes private configuration)', 'gnn' ); ?></option>
						</select>
					</label>
					<?php wp_nonce_field( 'gnn_export_settings' ); ?>
					<?php submit_button( __( 'Export JSON', 'gnn' ), 'secondary', 'submit', false ); ?>
				</form>
			</div>
		</div>

		<?php gnn_section_bar( __( 'Import settings', 'gnn' ) ); ?>
		<div class="gnn-setting-row">
			<div class="gnn-setting-label-col">
				<span class="gnn-setting-label"><?php esc_html_e( 'Import settings', 'gnn' ); ?></span>
				<span class="gnn-setting-hint"><?php esc_html_e( 'Upload a GNN settings JSON file (maximum 1 MB). Fields absent from the file keep their current values.', 'gnn' ); ?></span>
			</div>
			<div class="gnn-setting-control-col">
				<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="gnn-inline-form">
					<input type="hidden" name="action" value="gnn_import_settings">
					<?php
					wp_nonce_field( 'gnn_import_settings' );
					gnn_configuration_revision_field();
					?>
					<input type="file" aria-label="<?php esc_attr_e( 'Settings JSON file', 'gnn' ); ?>" name="gnn_import_file" accept="application/json,.json" required>
					<button type="submit" class="button button-secondary" name="gnn_preview" value="1"><?php esc_html_e( 'Preview import', 'gnn' ); ?></button>
				</form>
			</div>
		</div>

		<?php gnn_section_bar( __( 'Reset settings', 'gnn' ) ); ?>
		<div class="gnn-setting-row">
			<div class="gnn-setting-label-col">
				<span class="gnn-setting-label"><?php esc_html_e( 'Reset settings', 'gnn' ); ?></span>
				<span class="gnn-setting-hint"><?php esc_html_e( 'Restore panel settings, accent color, default mode and footer legal text. Site icon and update preferences are preserved. This cannot be undone.', 'gnn' ); ?></span>
			</div>
			<div class="gnn-setting-control-col">
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('<?php echo esc_js( __( 'Reset all GNN Theme settings to defaults?', 'gnn' ) ); ?>');" class="gnn-inline-form">
					<input type="hidden" name="action" value="gnn_reset_settings">
					<?php
					wp_nonce_field( 'gnn_reset_settings' );
					gnn_configuration_revision_field();
					?>
					<?php submit_button( __( 'Reset to defaults', 'gnn' ), 'delete', 'submit', false ); ?>
				</form>
			</div>
		</div>

		<?php if ( defined( 'ELEMENTOR_VERSION' ) ) : ?>
			<?php gnn_section_bar( __( 'Elementor templates', 'gnn' ) ); ?>
			<div class="gnn-setting-row">
				<div class="gnn-setting-label-col">
					<span class="gnn-setting-label"><?php esc_html_e( 'Elementor templates', 'gnn' ); ?></span>
					<span class="gnn-setting-hint"><?php esc_html_e( 'Refresh untouched GNN Saved Templates and add missing sections. Your edited templates and their IDs are preserved. Legacy templates without a baseline are preserved.', 'gnn' ); ?></span>
				</div>
				<div class="gnn-setting-control-col">
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="gnn-inline-form">
						<input type="hidden" name="action" value="gnn_rebuild_elementor_templates">
						<?php wp_nonce_field( 'gnn_rebuild_elementor_templates' ); ?>
						<?php submit_button( __( 'Refresh Elementor templates', 'gnn' ), 'secondary', 'submit', false ); ?>
					</form>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( function_exists( 'gnn_updates_enabled' ) ) : ?>
			<?php
			$gnn_updates_on = gnn_updates_enabled();
			$gnn_release    = ( $gnn_updates_on && function_exists( 'gnn_updater' ) ) ? gnn_updater()->get_remote_release() : false;
			$gnn_has_update = $gnn_release && version_compare( $gnn_release->version, GNN_VERSION, '>' );
			?>
			<?php gnn_section_bar( __( 'Theme updates (GitHub)', 'gnn' ) ); ?>
			<div class="gnn-setting-row">
				<div class="gnn-setting-label-col">
					<span class="gnn-setting-label"><?php esc_html_e( 'Theme updates (GitHub)', 'gnn' ); ?></span>
					<span class="gnn-setting-hint">
						<?php
						/* translators: %s: installed theme version. */
						printf( esc_html__( 'Installed version: %s', 'gnn' ), esc_html( GNN_VERSION ) );
						?>
					</span>
				</div>
				<div class="gnn-setting-control-col">
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
						<label class="gnn-switch">
							<input type="checkbox" aria-label="<?php esc_attr_e( 'Enable theme updates', 'gnn' ); ?>" name="gnn_updates_enable" value="1" <?php checked( $gnn_updates_on ); ?>>
							<span class="gnn-switch__track"><span class="gnn-switch__thumb"></span></span>
						</label>
						<span class="gnn-switch__label"><?php esc_html_e( 'Check GitHub for new releases', 'gnn' ); ?></span>
						<?php submit_button( __( 'Save', 'gnn' ), 'secondary', 'submit', false ); ?>
					</form>
				</div>
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
	return array( 'gnn_accent_color', 'gnn_default_theme', 'gnn_footer_legal', 'logo_light', 'logo_light_2x', 'logo_dark', 'logo_dark_2x' );
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
		wp_die( esc_html__( 'Permission denied.', 'gnn' ), '', array( 'response' => 403 ) );
	}
	$scope   = isset( $_POST['gnn_export_scope'] ) && 'site' === $_POST['gnn_export_scope'] ? 'site' : 'design'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$payload = gnn_settings_export_payload( $scope );

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
		wp_die( esc_html__( 'Permission denied.', 'gnn' ), '', array( 'response' => 403 ) );
	}
	if ( ! isset( $_FILES['gnn_import_file'] ) || ! is_array( $_FILES['gnn_import_file'] ) ) {
		gnn_panel_redirect( 'import_error' );
	}
	$file = $_FILES['gnn_import_file']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ! isset( $file['tmp_name'], $file['name'], $file['error'], $file['size'] )
		|| ! is_string( $file['tmp_name'] ) || ! is_string( $file['name'] )
		|| UPLOAD_ERR_OK !== (int) $file['error'] || (int) $file['size'] > 1048576
		|| 'json' !== strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) )
		|| ! is_uploaded_file( $file['tmp_name'] ) ) {
		gnn_panel_redirect( 'import_error' );
	}
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$raw = file_get_contents( $file['tmp_name'], false, null, 0, 1048577 );
	if ( false !== $raw && ! empty( $_POST['gnn_preview'] ) ) {
		gnn_render_import_preview( $raw );
	}
	if ( false === $raw || is_wp_error( gnn_import_panel_settings( $raw ) ) ) {
		gnn_panel_redirect( 'import_error' );
	}
	gnn_panel_redirect( 'import_ok' );
}
add_action( 'admin_post_gnn_import_settings', 'gnn_handle_import_settings' );

require_once get_template_directory() . '/inc/settings-transfer.php';

/**
 * Handle: reset settings to defaults.
 */
function gnn_handle_reset_settings() {
	if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'gnn_reset_settings' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'gnn' ), '', array( 'response' => 403 ) );
	}
	$started = gnn_configuration_begin( 'reset', gnn_configuration_form_revision() );
	if ( is_wp_error( $started ) ) {
		wp_die( esc_html( $started->get_error_message() ), '', array( 'response' => 409 ) );
	}
	gnn_reset_panel_settings();
	gnn_panel_redirect( 'reset_ok' );
}
add_action( 'admin_post_gnn_reset_settings', 'gnn_handle_reset_settings' );

/**
 * Reset settings owned by the panel.
 */
function gnn_reset_panel_settings() {
	delete_option( 'gnn_options' );
	foreach ( gnn_exportable_theme_mods() as $mod ) {
		remove_theme_mod( $mod );
	}
}

/**
 * Handle: rebuild the Elementor Saved Templates library.
 */
function gnn_handle_rebuild_elementor_templates() {
	if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'gnn_rebuild_elementor_templates' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'gnn' ), '', array( 'response' => 403 ) );
	}
	if ( function_exists( 'gnn_install_elementor_templates' ) ) {
		if ( ! gnn_install_elementor_templates() ) {
			gnn_panel_redirect( 'elementor_error' );
		}
	}
	gnn_panel_redirect( 'elementor_rebuilt' );
}
add_action( 'admin_post_gnn_rebuild_elementor_templates', 'gnn_handle_rebuild_elementor_templates' );

/**
 * Handle: save the GitHub-updates enable/disable toggle.
 */
function gnn_handle_save_updater_settings() {
	if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'gnn_save_updater_settings' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'gnn' ), '', array( 'response' => 403 ) );
	}
	update_option( 'gnn_github_updates_enable', empty( $_POST['gnn_updates_enable'] ) ? 0 : 1 );
	delete_site_transient( 'update_themes' );
	gnn_panel_redirect( 'updater_saved' );
}
add_action( 'admin_post_gnn_save_updater_settings', 'gnn_handle_save_updater_settings' );

/**
 * Handle: manual update check.
 */
function gnn_handle_check_theme_update() {
	if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'gnn_check_theme_update' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'gnn' ), '', array( 'response' => 403 ) );
	}
	delete_transient( 'gnn_github_update_check_v2' );
	delete_site_transient( 'update_themes' );
	gnn_panel_redirect( 'updater_checked' );
}
add_action( 'admin_post_gnn_check_theme_update', 'gnn_handle_check_theme_update' );

/**
 * Show admin notices after an Advanced-tab action.
 */
function gnn_panel_admin_notices() {
	if ( empty( $_GET['gnn_notice'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || 'toplevel_page_gnn-panel' !== $screen->id ) {
		return;
	}
	$notice   = sanitize_key( wp_unslash( $_GET['gnn_notice'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$messages = array(
		'import_ok'         => array( 'success', __( 'Settings imported successfully.', 'gnn' ) ),
		'import_error'      => array( 'error', __( 'Import failed: the file is not a valid GNN settings export.', 'gnn' ) ),
		'reset_ok'          => array( 'success', __( 'Settings reset to defaults.', 'gnn' ) ),
		'elementor_rebuilt' => array( 'success', __( 'Elementor templates refreshed. Edited templates were preserved.', 'gnn' ) ),
		'elementor_error'   => array( 'error', __( 'Some Elementor templates could not be saved. Please retry.', 'gnn' ) ),
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
