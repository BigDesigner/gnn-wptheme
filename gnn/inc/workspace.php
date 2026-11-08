<?php
/**
 * Task/role-based administration, recovery and diagnostics.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

/** Server-side allowlist, independent of visible controls. */
function gnn_delegated_keys() {
	$brand = array( 'company_name', 'company_address', 'company_hours', 'company_phone', 'company_email', 'company_social_url', 'header_cta_label', 'header_cta_url', 'topbar_text', 'topbar_email', 'topbar_phone', 'footer_tagline', 'footer_copyright', 'logo_light', 'logo_light_2x', 'logo_dark', 'logo_dark_2x', 'footer_logo_light', 'footer_logo_dark' );
	if ( current_user_can( 'gnn_manage_design' ) ) {
		foreach ( gnn_platform_schema() as $key => $field ) {
			if ( ! in_array( $field[3], array( 'code', 'advanced' ), true ) ) {
				$brand[] = $key; }
		}
		$brand = array_merge( $brand, array( 'accent', 'accent_light', 'color_bg_dark', 'color_bg_light', 'color_bg2_dark', 'color_bg2_light', 'color_bg3_dark', 'color_bg3_light', 'color_fg_dark', 'color_fg_light', 'color_fg2_dark', 'color_fg2_light', 'color_line_dark', 'color_line_light', 'logo_max_height', 'logo_max_height_mobile', 'footer_logo_height' ) );
	}
	return array_unique( $brand );
}

/** Operational controls are not part of an ordinary design save. */
function gnn_operation_keys() {
	return array( 'maintenance_mode', 'maintenance_message', 'disable_emoji', 'disable_oembed', 'disable_migrate', 'heartbeat_slow', 'woo_scope', 'font_preload', 'compatibility_mode' );
}

/** High-impact operations require an explicit confirmation and revision. */
function gnn_render_operation_controls() {
	echo '<h2>' . esc_html__( 'Maintenance and compatibility', 'gnn' ) . '</h2><p>' . esc_html__( 'These settings affect visitor access or third-party plugins. Test them on staging first. Ordinary design saves do not change these controls.', 'gnn' ) . '</p><form class="gnn-operations-form" method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="gnn_workspace"><input type="hidden" name="operation" value="operations">';
	wp_nonce_field( 'gnn_workspace' );
	gnn_configuration_revision_field();
	$labels = array(
		'maintenance_mode'   => __( 'Enable maintenance mode for visitors', 'gnn' ),
		'disable_emoji'      => __( 'Disable the emoji script', 'gnn' ),
		'disable_oembed'     => __( 'Remove oEmbed discovery links', 'gnn' ),
		'disable_migrate'    => __( 'Remove jQuery Migrate (front-end)', 'gnn' ),
		'heartbeat_slow'     => __( 'Slow the Heartbeat API to 60s', 'gnn' ),
		'woo_scope'          => __( 'Scope WooCommerce assets to pages that need them', 'gnn' ),
		'font_preload'       => __( 'Preload theme fonts', 'gnn' ),
		'compatibility_mode' => __( 'Prefer third-party plugin compatibility', 'gnn' ),
	);
	foreach ( $labels as $key => $label ) {
		gnn_field_checkbox( $key, $label ); }
	gnn_field_textarea( 'maintenance_message', __( 'Maintenance message', 'gnn' ), (string) gnn_option( 'maintenance_message' ), 3 );
	echo '<p><label><input type="checkbox" name="confirmed" value="1" required> ' . esc_html__( 'I understand the impact. Save a recovery snapshot and apply these operational settings.', 'gnn' ) . '</label></p>';
	submit_button( __( 'Apply operational settings', 'gnn' ) );
	echo '</form>';
}

/** Register delegated administration without exposing the full settings panel. */
function gnn_workspace_menu() {
	if ( current_user_can( 'manage_options' ) ) {
		add_submenu_page( 'gnn-panel', __( 'Site workspace', 'gnn' ), __( 'Workspace', 'gnn' ), 'manage_options', 'gnn-workspace', 'gnn_workspace_page' );
	} elseif ( current_user_can( 'gnn_manage_design' ) || current_user_can( 'gnn_manage_brand' ) || current_user_can( 'gnn_view_diagnostics' ) ) {
		$cap = current_user_can( 'gnn_manage_design' ) ? 'gnn_manage_design' : ( current_user_can( 'gnn_manage_brand' ) ? 'gnn_manage_brand' : 'gnn_view_diagnostics' );
		add_menu_page( __( 'Site workspace', 'gnn' ), 'GNN', $cap, 'gnn-panel', 'gnn_workspace_page', 'dashicons-admin-site-alt3', '59.100' );
	}
}
add_action( 'admin_menu', 'gnn_workspace_menu', 20 );

/** Diagnostic export contains versions/counts, never user/site/contact/code values. */
function gnn_support_summary() {
	return array(
		'theme'                  => GNN_VERSION,
		'wordpress'              => get_bloginfo( 'version' ),
		'php'                    => PHP_VERSION,
		'multisite'              => is_multisite(),
		'integrations'           => gnn_integration_status(),
		'history_entries'        => count( (array) get_option( 'gnn_configuration_history', array() ) ),
		'maintenance'            => (bool) gnn_option( 'maintenance_mode' ),
		'updates_enabled'        => gnn_updates_enabled(),
		'update_check'           => (array) get_option( 'gnn_update_status', array() ),
		'configuration_revision' => gnn_configuration_revision(),
	);
}

/**
 * Admin action forms are separate from the main settings form.
 *
 * @param string $action Operation identifier.
 * @param string $label Control label.
 * @param array  $extra Additional form fields.
 */
function gnn_workspace_action_form( $action, $label, $extra = array() ) {
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" class="gnn-workspace-action"><input type="hidden" name="action" value="gnn_workspace"><input type="hidden" name="operation" value="' . esc_attr( $action ) . '">';
	wp_nonce_field( 'gnn_workspace' );
	gnn_configuration_revision_field();
	foreach ( $extra as $key => $value ) {
		echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '">'; }
	if ( 'preset' === $action ) {
		echo '<label><input type="checkbox" name="reset_design" value="1"> ' . esc_html__( 'Reset presentation defaults before applying this profile. Keep identity, integrations and content.', 'gnn' ) . '</label><br>';
	}
	if ( 'starter' === $action ) {
		echo '<input type="hidden" name="editor" value="elementor"><label><input type="checkbox" name="apply_design" value="1" checked> ' . esc_html__( 'Apply this design profile after successful import', 'gnn' ) . '</label><br><label><input type="checkbox" name="reset_design" value="1"> ' . esc_html__( 'Reset presentation defaults first', 'gnn' ) . '</label><br>';
	}
	if ( in_array( $action, array( 'restore', 'starter', 'preset', 'clear_history', 'unlock' ), true ) ) {
		echo '<label><input type="checkbox" name="confirmed" value="1" required> ' . esc_html__( 'I reviewed the scope. A recovery snapshot will be created before configuration changes.', 'gnn' ) . '</label> '; }
	submit_button( $label, 'secondary', 'submit', false );
	echo '</form>';
}

/** A task-oriented workspace complements the detailed design controls. */
function gnn_workspace_page() {
	$admin    = current_user_can( 'manage_options' );
	$delegate = current_user_can( 'gnn_manage_design' ) || current_user_can( 'gnn_manage_brand' );
	if ( ! $admin && ! $delegate && ! current_user_can( 'gnn_view_diagnostics' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'gnn' ), '', array( 'response' => 403 ) );
	}
	$sections = array();
	if ( $admin ) {
		$sections['launch']   = __( 'Launch checklist', 'gnn' );
		$sections['profiles'] = __( 'Design profiles', 'gnn' );
	}
	if ( $delegate && ! $admin ) {
		$sections['brand'] = __( 'Assigned site settings', 'gnn' ); }
	if ( $admin || current_user_can( 'gnn_view_diagnostics' ) ) {
		$sections['diagnostics'] = __( 'Diagnostics', 'gnn' ); }
	if ( $admin ) {
		$sections['operations'] = __( 'Maintenance and compatibility', 'gnn' );
		$sections['recovery']   = __( 'Recovery snapshots', 'gnn' );
		$sections['history']    = __( 'Change history', 'gnn' );
	}
	echo '<div class="wrap gnn-panel gnn-workspace"><header class="gnn-panel__header"><div class="gnn-panel__heading"><span class="gnn-panel__icon dashicons dashicons-dashboard" aria-hidden="true"></span><div><h1>' . esc_html__( 'Site workspace', 'gnn' ) . '</h1><p class="gnn-panel__subtitle">' . esc_html__( 'Site operations, recovery and the settings assigned to your role.', 'gnn' ) . '</p></div></div><div class="gnn-panel__header-actions"><span class="gnn-panel__badge">v' . esc_html( GNN_VERSION ) . '</span>';
	if ( $admin ) {
		echo '<a class="button" href="' . esc_url( admin_url( 'admin.php?page=gnn-panel' ) ) . '">' . esc_html__( 'Theme settings', 'gnn' ) . '</a>'; }
	echo '</div></header><hr class="wp-header-end"><div class="gnn-admin-layout"><aside class="gnn-sidebar"><p class="gnn-sidebar__title">' . esc_html__( 'Site management', 'gnn' ) . '</p><nav class="gnn-workspace-links" aria-label="' . esc_attr__( 'Workspace sections', 'gnn' ) . '">';
	foreach ( $sections as $key => $label ) {
		echo '<a href="#gnn-workspace-' . esc_attr( $key ) . '">' . esc_html( $label ) . '</a>'; }
	echo '</nav></aside><div class="gnn-workspace-content">';
	if ( $admin ) {
		echo '<section id="gnn-workspace-launch" class="gnn-settings-card"><h2>' . esc_html__( 'Launch checklist', 'gnn' ) . '</h2><div class="gnn-workspace-section"><ol class="gnn-launch-list">';
		foreach ( array( __( 'Replace draft/demo content, choose logos and verify contact links.', 'gnn' ), __( 'Configure and test form delivery, consent, SEO and language integrations.', 'gnn' ), __( 'Review mobile navigation, keyboard operation and brand contrast.', 'gnn' ), __( 'Create an off-site database/files backup, rehearse upgrade and recovery on staging.', 'gnn' ), __( 'Assign the client or designer role instead of sharing an administrator account.', 'gnn' ) ) as $item ) {
			echo '<li>' . esc_html( $item ) . '</li>'; }
		echo '</ol></div></section><section id="gnn-workspace-profiles"><h2 class="gnn-workspace-section-title">' . esc_html__( 'Design profiles and draft starter pages', 'gnn' ) . '</h2><p class="description">' . esc_html__( 'Design changes preserve identity and content. Starter installation only adds missing draft pages; it does not publish them or replace your homepage or menus.', 'gnn' ) . '</p><div class="gnn-workspace-grid">';
		foreach ( gnn_starter_profiles() as $slug => $profile ) {
			echo '<article class="gnn-settings-card gnn-profile-card" data-profile="' . esc_attr( $slug ) . '"><span class="gnn-profile-swatch" style="background:' . esc_attr( $profile['accent'] ) . '" aria-hidden="true"></span><h3>' . esc_html( $profile['label'] ) . '</h3><p class="gnn-profile-meta">' . esc_html( $profile['accent'] ) . '</p>';
			if ( ! empty( $profile['description'] ) ) {
				echo '<p class="description">' . esc_html( $profile['description'] ) . '</p>';
				if ( 'architecture' === $slug ) {
					echo '<img class="gnn-profile-preview" src="' . esc_url( get_template_directory_uri() . '/assets/images/architecture/courtyard.svg' ) . '" alt="' . esc_attr__( 'Architecture concept preview', 'gnn' ) . '" width="1600" height="1100">';
				}
			}
			$installed = gnn_starter_owned_page( $slug, 'elementor' );
			if ( $installed && 'trash' !== $installed->post_status && current_user_can( 'edit_post', $installed->ID ) ) {
				echo '<p><a class="button" href="' . esc_url( get_preview_post_link( $installed->ID ) ) . '" target="_blank" rel="noopener">' . esc_html__( 'Preview installed design', 'gnn' ) . '</a> <a href="' . esc_url( admin_url( 'post.php?post=' . $installed->ID . '&action=elementor' ) ) . '">' . esc_html__( 'Edit with Elementor', 'gnn' ) . '</a></p>';
			}
			echo '<p class="description">' . esc_html__( 'Elementor Free edition. Only this design’s page documents are imported; no second editor edition or automatic saved-template library is created.', 'gnn' ) . '</p><details><summary>' . esc_html__( 'Profile actions', 'gnn' ) . '</summary>';
			gnn_workspace_action_form( 'preset', __( 'Apply design profile', 'gnn' ), array( 'profile' => $slug ) );
			if ( gnn_starter_elementor_available() && ( 'commerce' !== $slug || class_exists( 'WooCommerce' ) ) ) {
				gnn_workspace_action_form( 'starter', __( 'Import Elementor starter pages', 'gnn' ), array( 'profile' => $slug ) );
			} else {
				echo '<p>' . esc_html__( 'Activate Elementor to import pages. GNNcommerce also requires WooCommerce. Plugins are never installed automatically.', 'gnn' ) . '</p>';
			}
			echo '</details></article>';
		}
		echo '</div></section>';
		gnn_render_starter_maintenance();
	}
	$legacy_labels = array(
		'topbar_text'            => __( 'Announcement text', 'gnn' ),
		'topbar_phone'           => __( 'Phone', 'gnn' ),
		'topbar_email'           => __( 'Email', 'gnn' ),
		'footer_tagline'         => __( 'Footer tagline', 'gnn' ),
		'footer_copyright'       => __( 'Copyright text', 'gnn' ),
		'logo_light'             => __( 'Logo — light mode (1×)', 'gnn' ),
		'logo_light_2x'          => __( 'Logo — light mode (2× retina)', 'gnn' ),
		'logo_dark'              => __( 'Logo — dark mode (1×)', 'gnn' ),
		'logo_dark_2x'           => __( 'Logo — dark mode (2× retina)', 'gnn' ),
		'footer_logo_light'      => __( 'Footer logo — light mode (1×)', 'gnn' ),
		'footer_logo_dark'       => __( 'Footer logo — dark mode (1×)', 'gnn' ),
		'accent'                 => __( 'Accent color', 'gnn' ),
		'accent_light'           => __( 'Accent — light mode', 'gnn' ),
		'logo_max_height'        => __( 'Logo max height — desktop (px)', 'gnn' ),
		'logo_max_height_mobile' => __( 'Logo max height — mobile (px)', 'gnn' ),
		'footer_logo_height'     => __( 'Footer logo max height (px)', 'gnn' ),
	);
	foreach ( array(
		'color_bg'   => __( 'Background', 'gnn' ),
		'color_bg2'  => __( 'Surface', 'gnn' ),
		'color_bg3'  => __( 'Elevated surface', 'gnn' ),
		'color_fg'   => __( 'Primary text', 'gnn' ),
		'color_fg2'  => __( 'Secondary text', 'gnn' ),
		'color_line' => __( 'Borders', 'gnn' ),
	) as $key => $label ) {
		$legacy_labels[ $key . '_dark' ]  = $label . ' — ' . __( 'Dark', 'gnn' );
		$legacy_labels[ $key . '_light' ] = $label . ' — ' . __( 'Light', 'gnn' );
	}
	if ( $delegate && ! $admin ) {
		echo '<section id="gnn-workspace-brand" class="gnn-settings-card"><h2>' . esc_html__( 'Assigned site settings', 'gnn' ) . '</h2><p class="gnn-workspace-section">' . esc_html__( 'Only the fields assigned to your role can be changed here. Code, imports, resets and update controls are restricted to the site operator.', 'gnn' ) . '</p><form id="gnn-brand-form" class="gnn-card-content" method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="gnn_workspace"><input type="hidden" name="operation" value="brand">';
		wp_nonce_field( 'gnn_workspace' );
		gnn_configuration_revision_field();
		$schema = gnn_platform_schema();
		foreach ( gnn_delegated_keys() as $key ) {
			$field = $schema[ $key ] ?? array( $legacy_labels[ $key ] ?? ucwords( str_replace( '_', ' ', $key ) ), 'text' );
			if ( isset( $schema[ $key ] ) && 'select' === $field[1] ) {
				gnn_field_select( $key, $field[0], $field[4] ); } elseif ( 'bool' === $field[1] ) {
				gnn_field_checkbox( $key, $field[0] ); } elseif ( isset( $field[4], $field[5] ) && 'number' === $field[1] ) {
					gnn_field_number( $key, $field[0], $field[4], $field[5] ); } elseif ( 'actions' === $field[1] ) {
					gnn_mobile_action_fields(); } elseif ( 'layout' === $field[1] ) {
							$choices = array( 0 => __( 'Inherit', 'gnn' ) );
						foreach ( get_posts(
							array(
								'post_type'   => 'gnn_layout',
								'numberposts' => 100,
							)
						) as $layout ) {
							$choices[ $layout->ID ] = $layout->post_title;
						} gnn_field_select( $key, $field[0], $choices ); } elseif ( false !== strpos( $key, 'logo' ) && false === strpos( $key, 'height' ) ) {
								gnn_field_media( $key, $field[0], gnn_option( $key ) ); } else {
							gnn_field_text( $key, $field[0], 'accent' === $key ? get_theme_mod( 'gnn_accent_color', '#34d399' ) : (string) gnn_option( $key ) ); }
		}
		echo '<div class="gnn-savebar"><p class="gnn-save-status" role="status">' . esc_html__( 'No pending changes', 'gnn' ) . '</p>';
		submit_button( __( 'Save assigned settings', 'gnn' ), 'primary', 'submit', false );
		echo '</div>';
		echo '</form></section>';
	}
	if ( $admin || current_user_can( 'gnn_view_diagnostics' ) ) {
		echo '<section id="gnn-workspace-diagnostics" class="gnn-settings-card"><h2>' . esc_html__( 'Diagnostics and integration status', 'gnn' ) . '</h2><div class="gnn-workspace-section"><table class="gnn-diagnostic-table"><tbody>';
		$labels = array(
			'theme'                  => __( 'Theme version', 'gnn' ),
			'wordpress'              => __( 'WordPress version', 'gnn' ),
			'php'                    => __( 'PHP version', 'gnn' ),
			'multisite'              => __( 'Multisite', 'gnn' ),
			'integrations'           => __( 'Integrations', 'gnn' ),
			'history_entries'        => __( 'Recovery snapshots', 'gnn' ),
			'maintenance'            => __( 'Maintenance mode', 'gnn' ),
			'updates_enabled'        => __( 'Theme updates', 'gnn' ),
			'update_check'           => __( 'Last update check', 'gnn' ),
			'configuration_revision' => __( 'Configuration revision', 'gnn' ),
		);
		foreach ( gnn_support_summary() as $key => $value ) {
			if ( 'configuration_revision' === $key || 'update_check' === $key ) {
				continue; }
			$display = is_bool( $value ) ? ( $value ? __( 'Enabled', 'gnn' ) : __( 'Disabled', 'gnn' ) ) : ( is_array( $value ) ? implode( ' · ', $value ) : (string) $value );
			echo '<tr><th scope="row">' . esc_html( $labels[ $key ] ?? $key ) . '</th><td>' . esc_html( $display ) . '</td></tr>';
		}
		echo '</tbody></table><details><summary>' . esc_html__( 'Technical details', 'gnn' ) . '</summary>';
		$summary = gnn_support_summary();
		echo '<p class="description">' . esc_html( $labels['configuration_revision'] ) . '</p><code class="gnn-revision">' . esc_html( $summary['configuration_revision'] ) . '</code><p class="description">' . esc_html( $labels['update_check'] ) . '</p><code class="gnn-revision">' . esc_html( wp_json_encode( $summary['update_check'] ) ) . '</code></details>';
		gnn_workspace_action_form( 'support', __( 'Download masked support summary', 'gnn' ) );
		echo '</div></section>';
	}
	if ( $admin ) {
		echo '<section id="gnn-workspace-operations" class="gnn-settings-card"><div class="gnn-workspace-section">';
		gnn_render_operation_controls();
		echo '</div></section><section id="gnn-workspace-recovery" class="gnn-settings-card"><h2>' . esc_html__( 'Recovery snapshots', 'gnn' ) . '</h2><div class="gnn-workspace-section"><p class="description">' . esc_html__( 'The last ten local configuration snapshots are retained. This does not replace a database/files backup. Recovery does not delete or roll back customer pages.', 'gnn' ) . '</p>';
		gnn_workspace_action_form( 'snapshot', __( 'Create configuration snapshot', 'gnn' ) );
		foreach ( (array) get_option( 'gnn_configuration_history', array() ) as $item ) {
			echo '<div class="gnn-recovery-item"><p><strong>' . esc_html( $item['at'] ) . '</strong><br>' . esc_html( $item['action'] . ' · #' . $item['user'] ) . '</p><details><summary>' . esc_html__( 'Recovery actions', 'gnn' ) . '</summary>';
			gnn_workspace_action_form( 'restore', __( 'Restore this configuration', 'gnn' ), array( 'snapshot' => $item['id'] ) );
			echo '</details></div>';
		}
		echo '<details><summary>' . esc_html__( 'History management', 'gnn' ) . '</summary>';
		gnn_workspace_action_form( 'clear_history', __( 'Delete local recovery history', 'gnn' ) );
		$lock = get_option( 'gnn_configuration_lock' );
		if ( is_array( $lock ) && $lock['time'] < time() - 600 ) {
			gnn_workspace_action_form( 'unlock', __( 'Release abandoned settings lock', 'gnn' ) ); }
		echo '</details></div></section><section id="gnn-workspace-history" class="gnn-settings-card"><h2>' . esc_html__( 'Recent configuration changes', 'gnn' ) . '</h2><div class="gnn-workspace-section"><ul class="gnn-event-list">';
		foreach ( array_slice( (array) get_option( 'gnn_configuration_events', array() ), 0, 20 ) as $event ) {
			/* translators: %d: Number of changed configuration fields. */
			$changed = sprintf( __( 'Changed fields (%d)', 'gnn' ), count( $event['keys'] ) );
			echo '<li><strong>' . esc_html( $event['at'] . ' · #' . $event['user'] ) . '</strong><details><summary>' . esc_html( $changed ) . '</summary><p>' . esc_html( implode( ', ', $event['keys'] ) ) . '</p></details></li>';
		}
		echo '</ul><p class="description">' . esc_html__( 'Sensitive field values are never included in the change log or support summary.', 'gnn' ) . '</p></div></section>';
	}
	echo '</div></div></div>';
}

/** Every operation checks its own server-side capability and nonce. */
function gnn_handle_workspace() {
	check_admin_referer( 'gnn_workspace' );
	$operation = isset( $_POST['operation'] ) && is_string( $_POST['operation'] ) ? sanitize_key( wp_unslash( $_POST['operation'] ) ) : '';
	if ( 'support' === $operation && ( current_user_can( 'manage_options' ) || current_user_can( 'gnn_view_diagnostics' ) ) ) {
		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=gnn-support.json' );
		echo wp_json_encode( gnn_support_summary(), JSON_PRETTY_PRINT );
		exit;
	}
	$result = new WP_Error( 'gnn_permission', __( 'Permission denied.', 'gnn' ) );
	if ( 'brand' === $operation && ( current_user_can( 'gnn_manage_brand' ) || current_user_can( 'gnn_manage_design' ) ) ) {
		$input   = isset( $_POST['gnn_options'] ) && is_array( $_POST['gnn_options'] ) ? wp_unslash( $_POST['gnn_options'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- allowlist and native sanitizers below.
		$allowed = gnn_delegated_keys();
		if ( array_diff( array_keys( $input ), $allowed ) ) {
			wp_die( esc_html__( 'This role cannot change the submitted fields.', 'gnn' ), '', array( 'response' => 403 ) ); }
		$merged = wp_parse_args( (array) get_option( 'gnn_options', array() ), gnn_option_defaults() );
		foreach ( gnn_platform_schema() as $key => $field ) {
			if ( 'bool' === $field[1] && in_array( $key, $allowed, true ) && ! isset( $input[ $key ] ) ) {
				$input[ $key ] = 0; }
		}
		$result = gnn_validate_platform_input( $input );
		if ( is_wp_error( $result ) ) {
			wp_die( esc_html( $result->get_error_message() ), '', array( 'response' => 400 ) ); }
		$result = gnn_configuration_begin( 'delegated-save', gnn_configuration_form_revision() );
		if ( ! is_wp_error( $result ) ) {
			update_option( 'gnn_options', gnn_options_sanitize( array_replace( $merged, $input ) ) ); }
	} elseif ( current_user_can( 'manage_options' ) ) {
		if ( in_array( $operation, array( 'restore', 'preset', 'starter', 'starter_cleanup', 'operations', 'clear_history', 'unlock' ), true ) && empty( $_POST['confirmed'] ) ) {
			wp_die( esc_html__( 'Review and confirm the operation first.', 'gnn' ), '', array( 'response' => 400 ) ); }
		$profile = isset( $_POST['profile'] ) && is_string( $_POST['profile'] ) ? sanitize_key( wp_unslash( $_POST['profile'] ) ) : '';
		if ( 'operations' === $operation ) {
			$input = isset( $_POST['gnn_options'] ) && is_array( $_POST['gnn_options'] ) ? wp_unslash( $_POST['gnn_options'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- allowlist below.
			if ( array_diff( array_keys( $input ), gnn_operation_keys() ) ) {
				wp_die( esc_html__( 'Invalid operational field.', 'gnn' ), '', array( 'response' => 400 ) ); }
			$merged = wp_parse_args( (array) get_option( 'gnn_options', array() ), gnn_option_defaults() );
			foreach ( gnn_operation_keys() as $key ) {
				if ( 'maintenance_message' !== $key && ! isset( $input[ $key ] ) ) {
					$input[ $key ] = 0; }
			}
			$result = gnn_configuration_begin( 'operations', gnn_configuration_form_revision() );
			if ( ! is_wp_error( $result ) ) {
				update_option( 'gnn_options', gnn_options_sanitize( array_replace( $merged, $input ) ) ); }
		}
		if ( 'clear_history' === $operation && current_user_can( 'unfiltered_html' ) ) {
			delete_option( 'gnn_configuration_history' );
			delete_option( 'gnn_configuration_events' );
			$result = true; }
		if ( 'unlock' === $operation ) {
			$lock = get_option( 'gnn_configuration_lock' );
			if ( is_array( $lock ) && $lock['time'] < time() - 600 ) {
				delete_option( 'gnn_configuration_lock' );
				$result = true; }
		}
		if ( 'snapshot' === $operation ) {
			$result = gnn_configuration_begin( 'manual', gnn_configuration_form_revision() ); }
		if ( 'preset' === $operation ) {
			$result = gnn_apply_design_preset( $profile, ! empty( $_POST['reset_design'] ) ); }
		if ( 'starter' === $operation ) {

			$editor = isset( $_POST['editor'] ) && is_string( $_POST['editor'] ) ? sanitize_key( wp_unslash( $_POST['editor'] ) ) : 'elementor';
			if ( 'elementor' !== $editor ) {
				$result = new WP_Error( 'gnn_starter_editor', __( 'New ready-made sites use Elementor.', 'gnn' ) );
			} else {
				$result = gnn_install_starter( $profile, $editor );
			}
			if ( ! is_wp_error( $result ) && ! empty( $_POST['apply_design'] ) ) {
				$result = gnn_apply_design_preset( $profile, ! empty( $_POST['reset_design'] ) );
			}
		}
		if ( 'starter_cleanup' === $operation ) {
			$selection = isset( $_POST['cleanup'] ) && is_array( $_POST['cleanup'] ) ? map_deep( wp_unslash( $_POST['cleanup'] ), 'sanitize_text_field' ) : array();
			$result    = gnn_starter_cleanup( $selection, ! empty( $_POST['permanent'] ) );
			if ( is_wp_error( $result ) && 'gnn_cleanup_empty' === $result->get_error_code() ) {
				wp_safe_redirect( admin_url( 'admin.php?page=gnn-workspace&gnn_cleanup_notice=selection#gnn-workspace-cleanup' ) );
				exit;
			}
		}
		if ( 'restore' === $operation ) {
			$id     = isset( $_POST['snapshot'] ) && is_string( $_POST['snapshot'] ) ? sanitize_text_field( wp_unslash( $_POST['snapshot'] ) ) : '';
			$result = gnn_restore_configuration( $id, gnn_configuration_form_revision() ); }
	}
	if ( is_wp_error( $result ) ) {
		wp_die(
			esc_html( $result->get_error_message() ),
			'',
			array(
				'response'  => 'gnn_permission' === $result->get_error_code() ? 403 : 409,
				'back_link' => true,
			)
		); }
	wp_safe_redirect( admin_url( 'admin.php?page=' . ( current_user_can( 'manage_options' ) ? 'gnn-workspace' : 'gnn-panel' ) ) );
	exit;
}
add_action( 'admin_post_gnn_workspace', 'gnn_handle_workspace' );
