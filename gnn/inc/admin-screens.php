<?php
/**
 * GNN setup and management screens.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Register management pages under one product menu. */
function gnn_management_menu() {
	add_submenu_page( null, __( 'Used in', 'gnn' ), __( 'Used in', 'gnn' ), 'manage_options', 'gnn-usage', 'gnn_usage_page' );
	$pages = array(
		'gnn-templates'   => __( 'Page templates', 'gnn' ),
		'gnn-assignments' => __( 'Layouts', 'gnn' ),
		'gnn-design'      => __( 'Design system', 'gnn' ),
		'gnn-addons'      => __( 'Plugins', 'gnn' ),
		'gnn-maintenance' => __( 'Maintenance', 'gnn' ),
	);
	foreach ( $pages as $slug => $label ) {
		add_submenu_page( 'gnn-panel', $label, $label, 'manage_options', $slug, 'gnn_management_page' );
	}
}
add_action( 'admin_menu', 'gnn_management_menu', 25 );

/** Shared management page header.
 *
 * @param string $title Page title.
 */
function gnn_management_open( $title ) {
	echo '<div class="wrap gnn-panel gnn-management"><header class="gnn-panel-header"><div class="gnn-panel-header-center"><h1 class="gnn-panel-title">' . esc_html( $title ) . '</h1></div><span class="gnn-header-badge">v' . esc_html( GNN_VERSION ) . '</span></header><hr class="wp-header-end">';
	$notice = get_user_meta( get_current_user_id(), '_gnn_management_notice', true );
	if ( is_array( $notice ) ) {
		delete_user_meta( get_current_user_id(), '_gnn_management_notice' );
		echo '<div role="status" class="notice ' . ( ! empty( $notice['error'] ) ? 'notice-error' : 'notice-success' ) . '"><p>' . esc_html( $notice['text'] ) . '</p></div>';
	}
}

/** Asset scope includes product pages and shared content editors.
 *
 * @param string $hook Current screen.
 */
function gnn_management_assets( $hook ) {
	$screen = get_current_screen();
	if ( false === strpos( $hook, 'gnn' ) && ( ! $screen || ! in_array( $screen->post_type, array( 'gnn_layout' ), true ) ) ) {
		return;
	}
	wp_enqueue_style( 'gnn-admin', get_template_directory_uri() . '/assets/css/admin.css', array(), GNN_VERSION );
	wp_enqueue_style( 'gnn-management', get_template_directory_uri() . '/assets/css/management.css', array( 'gnn-admin' ), GNN_VERSION );
	wp_enqueue_script( 'gnn-management', get_template_directory_uri() . '/assets/js/management.js', array(), GNN_VERSION, true );
	wp_localize_script(
		'gnn-management',
		'gnnManagement',
		array(
			'ajax'              => admin_url( 'admin-ajax.php' ),
			'nonce'             => wp_create_nonce( 'gnn_wipe' ),
			'demoNonce'         => wp_create_nonce( 'gnn_demo_replace' ),
			'close'             => __( 'Close', 'gnn' ),
			'setup'             => admin_url( 'admin.php?page=gnn-workspace' ),
			'deleting'          => __( 'Deleting site content…', 'gnn' ),
			'installing'        => __( 'Installing demo…', 'gnn' ),
			'busy'              => __( 'Working…', 'gnn' ),
			'retry'             => __( 'Retry', 'gnn' ),
			'done'              => __( 'Completed.', 'gnn' ),
			'failure'           => __( 'The operation stopped. Retry to continue.', 'gnn' ),
			'waiting'           => __( 'Server busy. Waiting to continue…', 'gnn' ),
			'connectionFailure' => __( 'Server connection interrupted. Retry to continue.', 'gnn' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'gnn_management_assets' );

/** Setup entry preserves delegated workspace access. */
function gnn_workspace_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		gnn_workspace_legacy();
		return;
	}
	gnn_management_open( __( 'Setup wizard', 'gnn' ) );
	$job = get_option( 'gnn_wipe_job' );
	if ( is_array( $job ) && 'demo' === ( $job['kind'] ?? '' ) ) {
		gnn_demo_resume_screen( $job );
		echo '</div>';
		return;
	}
	$path = isset( $_GET['path'] ) && is_string( $_GET['path'] ) ? sanitize_key( wp_unslash( $_GET['path'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only step.
	if ( ! in_array( $path, array( 'ready', 'scratch' ), true ) ) {
		echo '<div class="gnn-choice-grid">';
		foreach ( array(
			'ready'   => __( 'Ready-made site', 'gnn' ),
			'scratch' => __( 'Start from scratch', 'gnn' ),
		) as $key => $label ) {
			echo '<a class="gnn-choice-card" href="' . esc_url( admin_url( 'admin.php?page=gnn-workspace&path=' . $key ) ) . '"><span class="dashicons ' . ( 'ready' === $key ? 'dashicons-layout' : 'dashicons-edit-page' ) . '" aria-hidden="true"></span><h2>' . esc_html( $label ) . '</h2></a>';
		}
		echo '</div></div>';
		return;
	}

	if ( 'scratch' === $path ) {
		gnn_management_form_open( 'scratch' );
		echo '<div class="gnn-settings-card gnn-card-content">';
		foreach ( array( 'header_preset', 'footer_preset', 'body_font', 'heading_font' ) as $key ) {
			$field = gnn_platform_schema()[ $key ];
			gnn_field_select( $key, $field[0], $field[4] );
		}
		gnn_field_text( 'accent', __( 'Accent color', 'gnn' ), get_theme_mod( 'gnn_accent_color', '#34d399' ) );
		echo '<p>' . esc_html__( 'No sample content will be added.', 'gnn' ) . '</p>';
		submit_button( __( 'Apply', 'gnn' ) );
		echo '</div></form></div>';
		return;
	}
	$profiles = gnn_starter_profiles();
	$ready    = gnn_starter_elementor_available();
	if ( ! $ready ) {
		gnn_plugin_cards();
	}
	echo '<div class="gnn-choice-grid">';
	foreach ( $profiles as $slug => $item ) {
		$preview = 'architecture' === $slug ? 'demo/architecture/image-18.jpg' : 'profiles/' . $slug . '.svg';
		echo '<article class="gnn-settings-card gnn-profile-card"><img class="gnn-profile-preview" src="' . esc_url( get_template_directory_uri() . '/assets/images/' . $preview ) . '" alt="' . esc_attr( $item['label'] ) . '" width="1600" height="1100"><h2>' . esc_html( $item['label'] ) . '</h2>';
		if ( 'architecture' === $slug ) {
			echo '<button type="button" class="button button-primary gnn-demo-open" data-profile="' . esc_attr( $slug ) . '" data-label="' . esc_attr( $item['label'] ) . '"' . disabled( ! $ready, true, false ) . '>' . esc_html__( 'Install demo site', 'gnn' ) . '</button>';
		} else {
			gnn_management_form_open( 'import' );
			echo '<input type="hidden" name="profile" value="' . esc_attr( $slug ) . '">';
			submit_button( __( 'Add draft pages', 'gnn' ), 'secondary', 'submit', true, $ready && ( 'commerce' !== $slug || class_exists( 'WooCommerce' ) ) ? array() : array( 'disabled' => 'disabled' ) );
			echo '<p class="gnn-operation-status" role="status"></p></form>';
		}
		echo '</article>';
	}
	echo '</div>';
	gnn_demo_confirmation();
	echo '</div>';
}

/** One explicit confirmation for replacing site content with a demo. */
function gnn_demo_confirmation() {
	echo '<dialog id="gnn-demo-confirm" class="gnn-demo-dialog" aria-labelledby="gnn-demo-title" aria-describedby="gnn-demo-scope"><h2 id="gnn-demo-title">' . esc_html__( 'Install demo site', 'gnn' ) . '</h2><p id="gnn-demo-scope">' . esc_html__( 'All pages, posts, projects, galleries, slides, menus, comments, orders and media files will be permanently deleted. Your active theme, plugins and user accounts will remain. The selected demo will be installed.', 'gnn' ) . '</p><form class="gnn-demo-form"><input type="hidden" name="profile" value=""><input type="hidden" name="confirm_delete" value="1">';
	gnn_configuration_revision_field();
	echo '<p class="gnn-demo-status" role="status" aria-live="polite"></p><div class="gnn-demo-actions"><button type="button" class="button gnn-demo-cancel" autofocus>' . esc_html__( 'Cancel', 'gnn' ) . '</button><button type="submit" class="button button-primary gnn-demo-submit">' . esc_html__( 'Delete and apply', 'gnn' ) . '</button></div></form></dialog><noscript><p>' . esc_html__( 'Enable JavaScript to install a demo.', 'gnn' ) . '</p></noscript>';
}

/**
 * Resume the existing installation without a second deletion confirmation.
 *
 * @param array $job Existing content replacement job.
 */
function gnn_demo_resume_screen( $job ) {
	echo '<section class="gnn-settings-card gnn-card-content"><h2>' . esc_html__( 'Demo installation', 'gnn' ) . '</h2><p>' . esc_html__( 'The installation has not finished. Continue from where it stopped.', 'gnn' ) . '</p><button type="button" class="button button-primary gnn-demo-resume" data-job="' . esc_attr( $job['id'] ) . '">' . esc_html__( 'Continue installation', 'gnn' ) . '</button><p class="gnn-demo-status" role="status"></p></section>';
}

/** Compact plugin cards use WordPress permission-checked actions.
 *
 * @param bool $commerce Whether WooCommerce is needed for this setup.
 */
function gnn_plugin_cards( $commerce = false ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	$plugins = array(
		'elementor'   => array( 'Elementor', 'elementor/elementor.php', __( 'Required for ready-made sites', 'gnn' ) ),
		'woocommerce' => array( 'WooCommerce', 'woocommerce/woocommerce.php', $commerce ? __( 'Required for this design', 'gnn' ) : __( 'Optional store', 'gnn' ) ),
	);
	echo '<div class="gnn-choice-grid">';
	foreach ( $plugins as $slug => $plugin ) {
		$installed = file_exists( WP_PLUGIN_DIR . '/' . $plugin[1] );
		$active    = is_plugin_active( $plugin[1] );
		echo '<article class="gnn-plugin-card"><span class="dashicons dashicons-admin-plugins" aria-hidden="true"></span><h3>' . esc_html( $plugin[0] ) . '</h3><p>' . esc_html( $plugin[2] ) . '</p>';
		if ( $active ) {
			echo '<strong>' . esc_html__( 'Active', 'gnn' ) . '</strong>';
		} elseif ( $installed && current_user_can( 'activate_plugins' ) ) {
			echo '<a class="button" href="' . esc_url( wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . rawurlencode( $plugin[1] ) ), 'activate-plugin_' . $plugin[1] ) ) . '">' . esc_html__( 'Activate', 'gnn' ) . '</a>';
		} elseif ( ! $installed && current_user_can( 'install_plugins' ) ) {
			echo '<a class="button" href="' . esc_url( self_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $slug ) ) . '">' . esc_html__( 'Install', 'gnn' ) . '</a>';
		} else {
			echo '<span>' . esc_html__( 'Administrator access required.', 'gnn' ) . '</span>';
		}
		echo '</article>';
	}
	echo '</div>';
}

/** Start an authenticated operation form.
 *
 * @param string $operation Allowlisted action.
 */
function gnn_management_form_open( $operation ) {
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" class="gnn-management-form"><input type="hidden" name="action" value="gnn_management"><input type="hidden" name="operation" value="' . esc_attr( $operation ) . '">';
	wp_nonce_field( 'gnn_management' );
	gnn_configuration_revision_field();
}

/** Render layout assignments without hiding inheritance semantics. */
function gnn_assignment_fields() {
	$records = get_posts(
		array(
			'post_type'   => 'gnn_layout',
			'post_status' => 'publish',
			'numberposts' => -1,
			'orderby'     => 'title',
			'order'       => 'ASC',
		)
	);
	$stored  = (array) get_option( 'gnn_options', array() );
	gnn_management_form_open( 'assignments' );
	echo '<div class="gnn-choice-grid">';
	foreach ( gnn_site_contexts() as $context => $label ) {
		echo '<section class="gnn-settings-card gnn-card-content"><h2>' . esc_html( $label ) . '</h2>';
		foreach ( gnn_site_regions() as $region => $name ) {
			$key   = ( 'site' === $context ? '' : $context . '_' ) . $region . '_template';
			$value = isset( $stored[ $key ] ) ? (int) $stored[ $key ] : -1;
			if ( empty( $stored['region_contract'] ) && 0 === $value && 'site' !== $context ) {
				$value = -1;
			}
			echo '<div class="gnn-field"><label for="gnn-region-' . esc_attr( $key ) . '">' . esc_html( $name ) . '</label><select id="gnn-region-' . esc_attr( $key ) . '" name="regions[' . esc_attr( $key ) . ']">';
			foreach ( array(
				-1 => __( 'Inherit', 'gnn' ),
				0  => __( 'Theme default', 'gnn' ),
				-2 => __( 'Hide', 'gnn' ),
			) as $id => $title ) {
				echo '<option value="' . esc_attr( $id ) . '" ' . selected( $id, $value, false ) . '>' . esc_html( $title ) . '</option>';
			}
			foreach ( $records as $record ) {
				if ( ! gnn_block_matches_region( $record->ID, $region ) && (int) $record->ID !== $value ) {
					continue;
				}
				echo '<option value="' . esc_attr( $record->ID ) . '" ' . selected( $record->ID, $value, false ) . '>' . esc_html( $record->post_title ) . '</option>';
			}
			if ( $value > 0 && ! in_array( $value, array_map( 'intval', wp_list_pluck( $records, 'ID' ) ), true ) ) {
				echo '<option value="' . esc_attr( $value ) . '" selected disabled>' . esc_html__( 'Unavailable block', 'gnn' ) . '</option>';
			}
			echo '</select>';
			echo '<a class="button gnn-region-edit"' . ( $value > 0 && current_user_can( 'edit_post', $value ) ? '' : ' hidden' ) . ' href="' . esc_url( admin_url( 'post.php?post=' . max( 0, $value ) . '&action=elementor' ) ) . '">' . esc_html__( 'Edit selected block', 'gnn' ) . '</a>';
			if ( -1 === $value && 'site' !== $context ) {
				$parents = ! in_array( $context, array( 'page', 'post', 'project', 'archive', 'search', 'notfound' ), true ) ? array( 'archive', 'site' ) : array( 'site' );
				foreach ( $parents as $parent ) {
					$parent_key = ( 'site' === $parent ? '' : $parent . '_' ) . $region . '_template';
					$inherited  = isset( $stored[ $parent_key ] ) ? (int) $stored[ $parent_key ] : -1;
					if ( -1 === $inherited || ( empty( $stored['region_contract'] ) && 0 === $inherited && 'site' !== $parent ) ) {
						continue; }
					$inherited_label = $inherited > 0 ? get_the_title( $inherited ) : ( -2 === $inherited ? __( 'Hide', 'gnn' ) : __( 'Theme default', 'gnn' ) );
					$contexts        = gnn_site_contexts();
					echo '<small>' . esc_html( $contexts[ $parent ] . ' / ' . $inherited_label ) . '</small>';
					break;
				}
			}
			if ( $value > 0 && current_user_can( 'edit_post', $value ) ) {
				echo '<a href="' . esc_url( get_edit_post_link( $value ) ) . '">' . esc_html__( 'Edit selected', 'gnn' ) . '</a>';
			}
			echo '</div>';
		}
		echo '</section>';
	}
	echo '</div>';
	submit_button( __( 'Save', 'gnn' ) );
	echo '</form>';
}

/** Render extended design settings by topic. */
function gnn_design_fields() {
	$draft          = get_user_meta( get_current_user_id(), '_gnn_design_draft', true );
	$saved          = (array) get_option( 'gnn_options', array() );
	$display_values = is_array( $draft ) ? array_replace( $saved, $draft ) : $saved;
	$display        = function () use ( $display_values ) {
		return $display_values;
	};
	add_filter( 'pre_option_gnn_options', $display );
	delete_user_meta( get_current_user_id(), '_gnn_design_draft' );

	$groups = array(
		'geometry'      => __( 'Layout', 'gnn' ),
		'type_scale'    => __( 'Typography', 'gnn' ),
		'fields'        => __( 'Form fields', 'gnn' ),
		'button_names'  => __( 'Button styles', 'gnn' ),
		'global_colors' => __( 'Global colors', 'gnn' ),
		'images'        => __( 'Image sizes', 'gnn' ),
		'search'        => __( 'Exclude from search', 'gnn' ),
	);
	echo '<div class="gnn-design-topics">';
	foreach ( $groups as $group => $label ) {
		echo '<a class="button" href="#gnn-design-' . esc_attr( $group ) . '">' . esc_html( $label ) . '</a>';
	}
	echo '</div>';
	gnn_management_form_open( 'design' );
	foreach ( $groups as $group => $label ) {
		echo '<details id="gnn-design-' . esc_attr( $group ) . '" class="gnn-settings-card gnn-design-group"><summary>' . esc_html( $label ) . '</summary><div class="gnn-card-content gnn-design-fields">';
		if ( 'type_scale' === $group ) {
			echo '<p>' . esc_html__( 'Size 0 inherits. Empty letter spacing inherits.', 'gnn' ) . '</p>'; }
		$last_set = '';
		foreach ( gnn_site_schema() as $key => $field ) {
			if ( $field[3] !== $group ) {
				continue;
			}
			if ( in_array( $group, array( 'type_scale', 'button_names' ), true ) && preg_match( '/^(type_[a-z0-9]+|btn_style_[0-9]+)_/', $key, $match ) ) {
				$set = $match[1];
				if ( $set !== $last_set ) {
					if ( $last_set ) {
						echo '</div></details>'; }
					$set_title = 'type_scale' === $group ? strtoupper( str_replace( 'type_', '', $set ) ) : ( gnn_option( $set . '_label' ) ? gnn_option( $set . '_label' ) : str_replace( 'btn_style_', '', $set ) );
					echo '<details class="gnn-design-subgroup"><summary>' . esc_html( $set_title ) . '</summary><div class="gnn-design-fields">';
					if ( 'type_scale' === $group ) {
						echo '<p class="gnn-type-preview" data-type-preview="' . esc_attr( str_replace( 'type_', '', $set ) ) . '">Aa Bb Cc 0123</p>'; }
					$last_set = $set;
				}
			}
			if ( in_array( $field[1], array( 'number', 'optional_number' ), true ) ) {
				gnn_field_number( $key, $field[0], $field[4], $field[5] );
			} elseif ( 'bool' === $field[1] ) {
				gnn_field_checkbox( $key, $field[0] );
			} elseif ( 'select' === $field[1] ) {
				gnn_field_select( $key, $field[0], $field[4] );
			} elseif ( 'hex' === $field[1] ) {
				gnn_field_color( $key, $field[0], (string) gnn_option( $key ), '#000000', '', true );
			} else {
				gnn_field_text( $key, $field[0], (string) gnn_option( $key ) );
			}
		}
		if ( $last_set ) {
			echo '</div></details>'; }
		if ( 'button_names' === $group ) {
			foreach ( gnn_button_style_slots() as $slot ) {
				$style = '';
				foreach ( array(
					'bg'     => 'background',
					'text'   => 'color',
					'border' => 'border-color',
				) as $part => $property ) {
					$color = sanitize_hex_color( gnn_option( 'btn_style_' . $slot . '_' . $part ) );
					if ( $color ) {
						$style .= $property . ':' . $color . ';'; }
				}
				echo '<button type="button" class="button gnn-button-preview" style="' . esc_attr( $style ) . '">' . esc_html( ( gnn_option( 'btn_style_' . $slot . '_label' ) ? gnn_option( 'btn_style_' . $slot . '_label' ) : (string) $slot ) ) . '</button>';
			}
		}
		if ( 'images' === $group ) {
			echo '<p>' . esc_html__( 'New sizes apply to future uploads. Original files are kept.', 'gnn' ) . '</p>';
		}
		echo '</div></details>';
	}
	submit_button( __( 'Save', 'gnn' ) );
	echo '</form>';
	remove_filter( 'pre_option_gnn_options', $display );
}

/** Catalog links existing documents and creates nothing on a read request. */
function gnn_template_catalog() {
	if ( ! gnn_starter_elementor_available() ) {
		gnn_plugin_cards();
		return; }
	gnn_section_library();
	echo '<p><a class="button" href="' . esc_url( admin_url( 'edit.php?post_type=elementor_library' ) ) . '">' . esc_html__( 'Saved templates', 'gnn' ) . '</a> <a class="button" href="' . esc_url( admin_url( 'post-new.php?post_type=elementor_library' ) ) . '">' . esc_html__( 'Add template', 'gnn' ) . '</a></p>';
	foreach ( gnn_starter_profiles() as $profile => $details ) {
		$home = gnn_starter_owned_page( $profile, 'elementor' );
		$ids  = $home ? (array) get_post_meta( $home->ID, '_gnn_starter_pages', true ) : array();
		if ( ! $ids ) {
			continue;
		}
		echo '<section class="gnn-settings-card gnn-card-content"><h2>' . esc_html( $details['label'] ) . '</h2>';
		foreach ( $ids as $id ) {
			if ( ! current_user_can( 'edit_post', $id ) || ! get_post( $id ) ) {
				continue;
			}
			echo '<div class="gnn-catalog-row"><strong>' . esc_html( get_the_title( $id ) ) . '</strong><span>' . esc_html( get_post_status( $id ) ) . '</span><a href="' . esc_url( get_preview_post_link( $id ) ) . '">' . esc_html__( 'Preview', 'gnn' ) . '</a><a href="' . esc_url( admin_url( 'post.php?post=' . absint( $id ) . '&action=elementor' ) ) . '">' . esc_html__( 'Edit with Elementor', 'gnn' ) . '</a></div>';
		}
		echo '</section>';
	}
}

/** Role-checked page dispatcher. */
function gnn_management_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'gnn' ), '', array( 'response' => 403 ) );
	}
	$page   = isset( $_GET['page'] ) && is_string( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only page route.
	$titles = array(
		'gnn-addons'      => __( 'Plugins', 'gnn' ),
		'gnn-templates'   => __( 'Page templates', 'gnn' ),
		'gnn-assignments' => __( 'Layouts', 'gnn' ),
		'gnn-design'      => __( 'Design system', 'gnn' ),
		'gnn-maintenance' => __( 'Maintenance', 'gnn' ),
	);
	gnn_management_open( $titles[ $page ] ?? __( 'GNN', 'gnn' ) );
	if ( 'gnn-addons' === $page ) {
		gnn_plugin_cards( 'commerce' === gnn_option( 'site_segment' ) );
	} elseif ( 'gnn-templates' === $page ) {
		gnn_template_catalog();
	} elseif ( 'gnn-assignments' === $page ) {
		gnn_assignment_fields();
	} elseif ( 'gnn-design' === $page ) {
		gnn_design_fields();
	} elseif ( 'gnn-maintenance' === $page ) {
		gnn_system_table();
		gnn_authoring_checks_screen();
		echo '<section class="gnn-settings-card gnn-card-content">';
		gnn_render_operation_controls();
		echo '</section><section class="gnn-settings-card gnn-card-content"><h2>' . esc_html__( 'Settings backup', 'gnn' ) . '</h2><p>' . esc_html__( 'Settings snapshots do not include content or files.', 'gnn' ) . '</p>';
		gnn_workspace_action_form( 'snapshot', __( 'Create snapshot', 'gnn' ) );
		foreach ( (array) get_option( 'gnn_configuration_history', array() ) as $item ) {
			echo '<p>' . esc_html( $item['at'] ) . '</p>';
			gnn_workspace_action_form( 'restore', __( 'Restore', 'gnn' ), array( 'snapshot' => $item['id'] ) );
		}
		echo '</section>';
		gnn_wipe_screen();
	}
	echo '</div>';
}

/** Environment facts contain no credentials or filesystem paths. */
function gnn_system_table() {
	$facts = array(
		__( 'Theme', 'gnn' )           => GNN_VERSION,
		__( 'WordPress', 'gnn' )       => get_bloginfo( 'version' ),
		'PHP'                          => PHP_VERSION,
		__( 'Memory limit', 'gnn' )    => ini_get( 'memory_limit' ),
		__( 'Time limit', 'gnn' )      => ini_get( 'max_execution_time' ),
		__( 'Input variables', 'gnn' ) => ini_get( 'max_input_vars' ),
		__( 'Upload limit', 'gnn' )    => size_format( wp_max_upload_size() ),
		__( 'Multisite', 'gnn' )       => is_multisite() ? __( 'Yes', 'gnn' ) : __( 'No', 'gnn' ),
		__( 'Debug', 'gnn' )           => defined( 'WP_DEBUG' ) && WP_DEBUG ? __( 'Enabled', 'gnn' ) : __( 'Disabled', 'gnn' ),
	);
	foreach ( array( 'curl', 'dom', 'zip', 'mbstring' ) as $extension ) {
		$facts[ $extension ] = extension_loaded( $extension ) ? __( 'Available', 'gnn' ) : __( 'Unavailable', 'gnn' );
	}
	echo '<section class="gnn-settings-card gnn-card-content"><h2>' . esc_html__( 'System status', 'gnn' ) . '</h2><table class="widefat striped"><tbody>';
	foreach ( $facts as $label => $value ) {
		echo '<tr><th scope="row">' . esc_html( $label ) . '</th><td>' . esc_html( $value ) . '</td></tr>';
	}
	echo '</tbody></table>';
	gnn_workspace_action_form( 'support', __( 'Download support summary', 'gnn' ) );
	echo '</section>';
}

/** Apply small allowlisted operations and preserve omitted settings. */
function gnn_management_handle() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'gnn' ), '', array( 'response' => 403 ) );
	}
	check_admin_referer( 'gnn_management' );
	$operation = isset( $_POST['operation'] ) && is_string( $_POST['operation'] ) ? sanitize_key( wp_unslash( $_POST['operation'] ) ) : '';
	$result    = new WP_Error( 'gnn_operation', __( 'Invalid operation.', 'gnn' ) );
	$target    = 'gnn-workspace';
	if ( 'import' === $operation ) {
		$profile = isset( $_POST['profile'] ) && is_string( $_POST['profile'] ) ? sanitize_key( wp_unslash( $_POST['profile'] ) ) : '';
		$result  = gnn_configuration_begin( 'setup', gnn_configuration_form_revision() );
		if ( ! is_wp_error( $result ) ) {
			$result = gnn_install_starter( $profile, 'elementor' );
			if ( ! is_wp_error( $result ) ) {
				$result = gnn_apply_design_preset( $profile, ! empty( $_POST['reset_design'] ) );
			}
		}
		$target = 'gnn-templates';
	} elseif ( 'assignments' === $operation ) {
		$raw    = isset( $_POST['regions'] ) && is_array( $_POST['regions'] ) ? wp_unslash( $_POST['regions'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- exact keys and typed values below.
		$values = array();
		$result = true;
		foreach ( gnn_site_contexts() as $context => $label ) {
			foreach ( gnn_site_regions() as $region => $name ) {
				$key   = ( 'site' === $context ? '' : $context . '_' ) . $region . '_template';
				$value = $raw[ $key ] ?? null;
				if ( ! is_scalar( $value ) || ! preg_match( '/^-?[0-9]+$/D', (string) $value ) || (int) $value < -2 || ( (int) $value > 0 && ( 'gnn_layout' !== get_post_type( $value ) || 'publish' !== get_post_status( $value ) ) ) ) {
					$result = new WP_Error( 'gnn_region', __( 'Choose a published block.', 'gnn' ) );
					break 2;
				}
				$values[ $key ] = (int) $value;
				if ( (int) $value > 0 && ! gnn_block_matches_region( (int) $value, $region ) ) {
					$result = new WP_Error( 'gnn_role', __( 'The block role does not match this region.', 'gnn' ) );
					break 2;
				}
			}
		}
		if ( ! is_wp_error( $result ) ) {
			$result = gnn_configuration_begin( 'assignments', gnn_configuration_form_revision() );
			if ( ! is_wp_error( $result ) ) {
				$values['region_contract'] = 1;
				update_option( 'gnn_options', array_replace( wp_parse_args( (array) get_option( 'gnn_options', array() ), gnn_option_defaults() ), $values ) );
			}
		}
		$target = 'gnn-assignments';
	} elseif ( in_array( $operation, array( 'scratch', 'design' ), true ) ) {
		$raw     = isset( $_POST['gnn_options'] ) && is_array( $_POST['gnn_options'] ) ? wp_unslash( $_POST['gnn_options'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- allowlist and typed schema below.
		$allowed = 'scratch' === $operation ? array( 'header_preset', 'footer_preset', 'body_font', 'heading_font', 'accent' ) : array_keys(
			array_filter(
				gnn_site_schema(),
				function ( $field ) {
					return ! in_array( $field[3], array( 'assignments', 'internal' ), true );
				}
			)
		);
		if ( ! array_diff( array_keys( $raw ), $allowed ) ) {
			foreach ( gnn_site_schema() as $key => $field ) {
				if ( 'bool' === $field[1] && in_array( $key, $allowed, true ) && ! isset( $raw[ $key ] ) ) {
					$raw[ $key ] = 0;
				}
			}
			$result = gnn_validate_platform_input( $raw );
			if ( isset( $raw['accent'] ) && ! sanitize_hex_color( $raw['accent'] ) ) {
				$result = new WP_Error( 'gnn_color', __( 'Enter a valid color.', 'gnn' ) );
			}
			if ( ! is_wp_error( $result ) ) {
				$result = gnn_configuration_begin( $operation, gnn_configuration_form_revision() );
				if ( ! is_wp_error( $result ) ) {
					$saved = gnn_sanitize_platform_options( wp_parse_args( (array) get_option( 'gnn_options', array() ), gnn_option_defaults() ), $raw );
					update_option( 'gnn_options', $saved );
					if ( isset( $raw['accent'] ) ) {
						set_theme_mod( 'gnn_accent_color', sanitize_hex_color( $raw['accent'] ) );
					}
				}
			}
		}
		$target = 'design' === $operation ? 'gnn-design' : 'gnn-maintenance';
	}
	if ( 'import' === $operation && is_wp_error( $result ) && isset( $profile ) ) {
		$target = 'gnn-workspace&path=ready&profile=' . rawurlencode( $profile ); }
	if ( ! is_wp_error( $result ) && in_array( $operation, array( 'scratch', 'import' ), true ) ) {
		update_option( 'gnn_setup_completed', true, false );
	}
	if ( 'design' === $operation ) {
		if ( is_wp_error( $result ) && isset( $raw ) && is_array( $raw ) ) {
			$draft = array();
			foreach ( array_intersect_key( $raw, gnn_site_schema() ) as $key => $value ) {
				if ( is_scalar( $value ) ) {
					$draft[ $key ] = sanitize_text_field( (string) $value ); }
			}
			update_user_meta( get_current_user_id(), '_gnn_design_draft', $draft );
		} else {
			delete_user_meta( get_current_user_id(), '_gnn_design_draft' ); }
	}
	update_user_meta(
		get_current_user_id(),
		'_gnn_management_notice',
		array(
			'error' => is_wp_error( $result ),
			'text'  => is_wp_error( $result ) ? $result->get_error_message() : __( 'Changes saved.', 'gnn' ),
		)
	);
	wp_safe_redirect( admin_url( 'admin.php?page=' . $target ) );
	exit;
}
add_action( 'admin_post_gnn_management', 'gnn_management_handle' );
