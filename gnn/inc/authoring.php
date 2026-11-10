<?php
/**
 * Reusable authoring tools and administrator diagnostics.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Supported shared-block roles; untyped legacy blocks remain usable. */
function gnn_block_roles() {
	return array_merge( array( '' => __( 'General purpose', 'gnn' ) ), gnn_site_regions() );
}

/**
 * Validate a block against a region without changing legacy records.
 *
 * @param int    $id Shared block ID.
 * @param string $region Region key.
 */
function gnn_block_matches_region( $id, $region ) {
	$role = get_post_meta( $id, '_gnn_block_role', true );
	return '' === $role || $role === $region;
}

/** Register the block-role control. */
function gnn_block_role_box() {
	add_meta_box( 'gnn-block-role', __( 'Block role', 'gnn' ), 'gnn_block_role_fields', 'gnn_layout', 'side' );
}
add_action( 'add_meta_boxes', 'gnn_block_role_box' );

/**
 * Role selector uses the existing post permission boundary.
 *
 * @param WP_Post $post Shared block.
 */
function gnn_block_role_fields( $post ) {
	wp_nonce_field( 'gnn_block_role', 'gnn_block_role_nonce' );
	echo '<label class="screen-reader-text" for="gnn-block-role-select">' . esc_html__( 'Block role', 'gnn' ) . '</label><select id="gnn-block-role-select" name="gnn_block_role">';
	foreach ( gnn_block_roles() as $key => $label ) {
		echo '<option value="' . esc_attr( $key ) . '" ' . selected( get_post_meta( $post->ID, '_gnn_block_role', true ), $key, false ) . '>' . esc_html( $label ) . '</option>';
	}
	echo '</select>';
}

/**
 * Save an allowlisted role only on an explicit authorized form submission.
 *
 * @param int $id Shared block ID.
 */
function gnn_save_block_role( $id ) {
	if ( wp_is_post_revision( $id ) || wp_is_post_autosave( $id ) || ! current_user_can( 'edit_post', $id ) || ! isset( $_POST['gnn_block_role_nonce'], $_POST['gnn_block_role'] ) || ! is_string( $_POST['gnn_block_role_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['gnn_block_role_nonce'] ) ), 'gnn_block_role' ) || ! is_string( $_POST['gnn_block_role'] ) ) {
		return;
	}
	$role = sanitize_key( wp_unslash( $_POST['gnn_block_role'] ) );
	if ( array_key_exists( $role, gnn_block_roles() ) ) {
		update_post_meta( $id, '_gnn_block_role', $role );
	}
}
add_action( 'save_post_gnn_layout', 'gnn_save_block_role' );

/**
 * Role column supplements the existing usage inventory.
 *
 * @param array $columns List columns.
 */
function gnn_block_role_columns( $columns ) {
	$columns['gnn_role'] = __( 'Block role', 'gnn' );
	return $columns;
}
add_filter( 'manage_gnn_layout_posts_columns', 'gnn_block_role_columns' );

/**
 * Display an escaped role name.
 *
 * @param string $column Column key.
 * @param int    $id Shared block ID.
 */
function gnn_block_role_column( $column, $id ) {
	if ( 'gnn_role' === $column ) {
		echo esc_html( gnn_block_roles()[ get_post_meta( $id, '_gnn_block_role', true ) ] ?? __( 'General purpose', 'gnn' ) );
	}
}
add_action( 'manage_gnn_layout_posts_custom_column', 'gnn_block_role_column', 10, 2 );

/** Small section catalog; nothing is inserted when listing it. */
function gnn_section_catalog() {
	return array(
		'hero'       => __( 'Hero', 'gnn' ),
		'services'   => __( 'Services', 'gnn' ),
		'team'       => __( 'Team', 'gnn' ),
		'references' => __( 'References', 'gnn' ),
		'pricing'    => __( 'Pricing', 'gnn' ),
		'contact'    => __( 'Contact', 'gnn' ),
		'callout'    => __( 'Call to action', 'gnn' ),
	);
}

/**
 * Native Elementor element, without HTML widgets or external assets.
 *
 * @param string $type Widget name or container.
 * @param array  $settings Native controls.
 * @param array  $children Nested elements.
 */
function gnn_section_element( $type, $settings, $children = array() ) {
	$node = array(
		'id'       => substr( md5( wp_generate_uuid4() ), 0, 7 ),
		'elType'   => 'container' === $type ? 'container' : 'widget',
		'settings' => $settings,
		'elements' => $children,
		'isInner'  => false,
	);
	if ( 'container' !== $type ) {
		$node['widgetType'] = $type;
	}
	return $node;
}

/**
 * Build selected sections with native responsive layout controls.
 *
 * @param string $key Section key.
 */
function gnn_section_elements( $key ) {
	$catalog = gnn_section_catalog();
	if ( ! isset( $catalog[ $key ] ) ) {
		return array();
	}
	$children    = array(
		gnn_section_element(
			'heading',
			array(
				'title'       => $catalog[ $key ],
				'header_size' => 'h2',
			)
		),
	);
	$children[]  = gnn_section_element( 'text-editor', array( 'editor' => '<p>' . esc_html__( 'Add your own content.', 'gnn' ) . '</p>' ) );
	$card_titles = array(
		'services'   => array( __( 'Strategy', 'gnn' ), __( 'Design', 'gnn' ), __( 'Delivery', 'gnn' ) ),
		'team'       => array( __( 'Team member', 'gnn' ), __( 'Team member', 'gnn' ), __( 'Team member', 'gnn' ) ),
		'references' => array( __( 'Client logo', 'gnn' ), __( 'Client logo', 'gnn' ), __( 'Client logo', 'gnn' ) ),
		'pricing'    => array( __( 'Essential', 'gnn' ), __( 'Professional', 'gnn' ), __( 'Custom', 'gnn' ) ),
	);
	if ( in_array( $key, array( 'services', 'team', 'references', 'pricing' ), true ) ) {
		$cards = array();
		for ( $index = 1; $index <= 3; ++$index ) {
			$cards[]  = gnn_section_element(
				'container',
				array(
					'content_width'  => 'full',
					'width'          => array(
						'unit' => '%',
						'size' => 33.333,
					),
					'width_mobile'   => array(
						'unit' => '%',
						'size' => 100,
					),
					'flex_direction' => 'column',
				),
				array(
					gnn_section_element(
						'heading',
						array(
							'title'       => $card_titles[ $key ][ $index - 1 ],
							'header_size' => 'h3',
						)
					),
					gnn_section_element( 'text-editor', array( 'editor' => '<p>' . esc_html__( 'Add your own content.', 'gnn' ) . '</p>' ) ),
				)
			);
			$position = count( $cards ) - 1;
			if ( in_array( $key, array( 'team', 'references' ), true ) ) {
				array_unshift(
					$cards[ $position ]['elements'],
					gnn_section_element(
						'image',
						array(
							'image'      => array(
								'url' => '',
								'id'  => 0,
							),
							'image_size' => 'large',
						)
					)
				);
			}
			if ( 'pricing' === $key ) {
				$cards[ $position ]['elements'][] = gnn_section_element(
					'icon-list',
					array(
						'icon_list' => array(
							array(
								'_id'  => 'scope',
								'text' => __( 'Scope', 'gnn' ),
							),
							array(
								'_id'  => 'support',
								'text' => __( 'Support', 'gnn' ),
							),
							array(
								'_id'  => 'schedule',
								'text' => __( 'Schedule', 'gnn' ),
							),
						),
					)
				);
				$cards[ $position ]['elements'][] = gnn_section_element( 'button', array( 'text' => __( 'Request a quote', 'gnn' ) ) );
			}
		}
		$children[] = gnn_section_element(
			'container',
			array(
				'content_width'         => 'full',
				'flex_direction'        => 'row',
				'flex_direction_mobile' => 'column',
				'flex_wrap'             => 'nowrap',
				'flex_gap'              => array(
					'unit'   => 'px',
					'size'   => 24,
					'column' => '24',
					'row'    => '24',
				),
			),
			$cards
		);
	} else {
		$children[] = gnn_section_element(
			'button',
			array(
				'text' => __( 'Contact', 'gnn' ),
				'link' => array( 'url' => (int) gnn_option( 'contact_page_id' ) ? get_permalink( (int) gnn_option( 'contact_page_id' ) ) : '' ),
			)
		);
	}
	if ( 'hero' === $key ) {
		$children[0]['settings']['title']       = get_bloginfo( 'name' );
		$children[0]['settings']['header_size'] = 'h1';
		$children[1]['settings']['editor']      = '<p>' . esc_html( get_bloginfo( 'description' ) ) . '</p>';
		$copy                                   = gnn_section_element(
			'container',
			array(
				'content_width'        => 'full',
				'width'                => array(
					'unit' => '%',
					'size' => 50,
				),
				'width_mobile'         => array(
					'unit' => '%',
					'size' => 100,
				),
				'flex_direction'       => 'column',
				'flex_justify_content' => 'center',
			),
			$children
		);
		$image                                  = gnn_section_element(
			'image',
			array(
				'image'                        => array(
					'url' => '',
					'id'  => 0,
				),
				'image_size'                   => 'large',
				'_element_width'               => 'initial',
				'_element_custom_width'        => array(
					'unit' => '%',
					'size' => 50,
				),
				'_element_custom_width_mobile' => array(
					'unit' => '%',
					'size' => 100,
				),
			)
		);
		return array(
			gnn_section_element(
				'container',
				array(
					'content_width'         => 'full',
					'flex_direction'        => 'row',
					'flex_direction_mobile' => 'column',
					'flex_gap'              => array(
						'unit'   => 'px',
						'size'   => 32,
						'column' => '32',
						'row'    => '32',
					),
				),
				array( $copy, $image )
			),
		);
	}
	if ( 'contact' === $key ) {
		$children[1]['settings']['editor'] = '<p>' . esc_html( (string) gnn_option( 'company_address' ) ) . '</p><p>' . esc_html( (string) gnn_option( 'company_phone' ) ) . '</p>';
		$email                             = sanitize_email( (string) gnn_option( 'company_email' ) );
		if ( $email ) {
			$children[2]['settings']['text'] = $email;
			$children[2]['settings']['link'] = array( 'url' => 'mailto:' . $email );
		}
	}
	if ( 'callout' === $key ) {
		$children[0]['settings']['align'] = 'center';
		$children[1]['settings']['align'] = 'center';
		$children[2]['settings']['align'] = 'center';
	}
	return array(
		gnn_section_element(
			'container',
			array(
				'content_width'  => 'full',
				'flex_direction' => 'column',
				'padding'        => array(
					'unit'     => 'px',
					'top'      => '48',
					'right'    => '24',
					'bottom'   => '48',
					'left'     => '24',
					'isLinked' => false,
				),
			),
			$children
		),
	);
}

/** Render visual catalog cards without auto-installing template records. */
function gnn_section_library() {
	echo '<section class="gnn-settings-card gnn-card-content"><h2>' . esc_html__( 'Section library', 'gnn' ) . '</h2><div class="gnn-section-library">';
	foreach ( gnn_section_catalog() as $key => $label ) {
		echo '<article class="gnn-section-card"><div aria-hidden="true" class="gnn-section-wireframe gnn-section-wireframe--' . esc_attr( $key ) . '"><span></span><span></span><span></span></div><h3>' . esc_html( $label ) . '</h3><form method="post" class="gnn-management-form" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="gnn_add_section"><input type="hidden" name="section" value="' . esc_attr( $key ) . '">';
		wp_nonce_field( 'gnn_add_section' );
		echo '<button class="button" type="submit">' . esc_html__( 'Add to saved templates', 'gnn' ) . '</button></form></article>';
	}
	echo '</div></section>';
}

/** Explicitly save only the selected section, then open its native editor. */
function gnn_add_section() {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'edit_posts' ) || ! current_user_can( 'publish_posts' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'gnn' ), '', array( 'response' => 403 ) );
	}
	check_admin_referer( 'gnn_add_section' );
	$key     = isset( $_POST['section'] ) && is_string( $_POST['section'] ) ? sanitize_key( wp_unslash( $_POST['section'] ) ) : '';
	$catalog = gnn_section_catalog();
	if ( ! isset( $catalog[ $key ] ) || ! gnn_starter_elementor_available() || get_option( 'gnn_wipe_job' ) ) {
		wp_die( esc_html__( 'This section is unavailable.', 'gnn' ) );
	}
	// phpcs:disable WordPress.DB.SlowDBQuery -- bounded explicit library lookup, never a frontend query.
	$existing = get_posts(
		array(
			'post_type'      => 'elementor_library',
			'post_status'    => array( 'draft', 'publish', 'private' ),
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_gnn_section_key',
			'meta_value'     => $key,
		)
	); // phpcs:ignore WordPress.DB.SlowDBQuery -- bounded explicit library action.
	// phpcs:enable WordPress.DB.SlowDBQuery
	$id = $existing ? (int) $existing[0] : wp_insert_post(
		array(
			'post_type'   => 'elementor_library',
			'post_status' => 'draft',
			'post_title'  => $catalog[ $key ],
			'meta_input'  => array(
				'_gnn_section_key'         => $key,
				'_elementor_template_type' => 'section',
			),
		),
		true
	);
	if ( is_wp_error( $id ) || ! $id ) {
		wp_die( esc_html__( 'The section could not be saved.', 'gnn' ) );
	}
	if ( ! get_post_meta( $id, '_gnn_section_ready', true ) ) {
		if ( taxonomy_exists( 'elementor_library_type' ) ) {
			$terms = wp_set_object_terms( $id, 'section', 'elementor_library_type' );
			if ( is_wp_error( $terms ) ) {
				wp_die( esc_html__( 'The section could not be saved.', 'gnn' ) );
			}
		}
		$result = gnn_starter_save_elementor( $id, gnn_section_elements( $key ) );
		if ( is_wp_error( $result ) ) {
			wp_die( esc_html( $result->get_error_message() ) );
		}
		update_post_meta( $id, '_gnn_section_ready', 1 );
		wp_publish_post( $id );
	}
	wp_safe_redirect( admin_url( 'post.php?post=' . $id . '&action=elementor' ) );
	exit;
}
add_action( 'admin_post_gnn_add_section', 'gnn_add_section' );

/** Explicit checks are never run on ordinary page loads. */
function gnn_run_authoring_checks() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'gnn' ), '', array( 'response' => 403 ) );
	}
	check_admin_referer( 'gnn_authoring_checks' );
	if ( get_option( 'gnn_wipe_job' ) || get_transient( 'gnn_authoring_check_' . get_current_user_id() ) ) {
		wp_safe_redirect( admin_url( 'admin.php?page=gnn-maintenance' ) );
		exit;
	}
	set_transient( 'gnn_authoring_check_' . get_current_user_id(), 1, 30 );
	$checks = array();
	foreach ( array(
		__( 'Site response', 'gnn' ) => home_url( '/' ),
		__( 'REST API', 'gnn' )      => rest_url(),
	) as $label => $url ) {
		$response = wp_remote_get(
			$url,
			array(
				'timeout'             => 5,
				'redirection'         => 0,
				'limit_response_size' => 2048,
			)
		);
		$code     = is_wp_error( $response ) ? 0 : wp_remote_retrieve_response_code( $response );
		$checks[] = array(
			'label'  => $label,
			'pass'   => $code >= 200 && $code < 300,
			'detail' => $code >= 200 && $code < 300 ? 'HTTP ' . $code : ( $code ? 'HTTP ' . $code . ' — ' . __( 'Check site URLs and server access rules.', 'gnn' ) : __( 'Connection failed. Check server access rules.', 'gnn' ) ),
		);
	}
	$upload   = wp_upload_bits( 'gnn-health-check.txt', null, 'GNN' );
	$checks[] = array(
		'label'  => __( 'Media write test', 'gnn' ),
		'pass'   => empty( $upload['error'] ),
		'detail' => empty( $upload['error'] ) ? __( 'Passed', 'gnn' ) : __( 'Check media directory permissions.', 'gnn' ),
	);
	if ( empty( $upload['error'] ) && ! empty( $upload['file'] ) ) {
		wp_delete_file( $upload['file'] );
	}
	$type       = get_post_type_object( 'gnn_layout' );
	$preview_ok = $type && $type->publicly_queryable && gnn_starter_elementor_available() && is_readable( get_template_directory() . '/page-templates/layout-preview.php' );
	$checks[]   = array(
		'label'  => __( 'Shared block preview', 'gnn' ),
		'pass'   => $preview_ok,
		'detail' => $preview_ok ? __( 'Passed', 'gnn' ) : __( 'Check Elementor activation and the shared block preview route.', 'gnn' ),
	);
	update_user_meta(
		get_current_user_id(),
		'_gnn_authoring_checks',
		array(
			'time'  => current_time( 'mysql' ),
			'items' => $checks,
		)
	);
	wp_safe_redirect( admin_url( 'admin.php?page=gnn-maintenance' ) );
	exit;
}
add_action( 'admin_post_gnn_authoring_checks', 'gnn_run_authoring_checks' );

/** Show actual check results, distinct from static environment information. */
function gnn_authoring_checks_screen() {
	echo '<section class="gnn-settings-card gnn-card-content"><h2>' . esc_html__( 'Connection and editor checks', 'gnn' ) . '</h2><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="gnn_authoring_checks">';
	wp_nonce_field( 'gnn_authoring_checks' );
	echo '<button class="button" type="submit">' . esc_html__( 'Run checks', 'gnn' ) . '</button></form>';
	$result = get_user_meta( get_current_user_id(), '_gnn_authoring_checks', true );
	if ( is_array( $result ) && ! empty( $result['items'] ) ) {
		echo '<p>' . esc_html( $result['time'] ) . '</p><table class="widefat striped"><tbody>';
		foreach ( $result['items'] as $item ) {
			echo '<tr><th scope="row">' . esc_html( $item['label'] ) . '</th><td>' . esc_html( $item['pass'] ? __( 'Passed', 'gnn' ) : __( 'Needs attention', 'gnn' ) ) . '</td><td>' . esc_html( $item['detail'] ) . '</td></tr>';
		}
		echo '</tbody></table>';
	}
	echo '</section>';
}

/** Visual selection writes the existing header layout setting. */
function gnn_visual_header_layout() {
	echo '<fieldset class="gnn-visual-options"><legend>' . esc_html__( 'Header layout', 'gnn' ) . '</legend>';
	foreach ( array(
		'standard' => __( 'Standard', 'gnn' ),
		'centered' => __( 'Centered', 'gnn' ),
	) as $value => $label ) {
		echo '<label><input type="radio" name="gnn_options[header_layout]" value="' . esc_attr( $value ) . '" ' . checked( gnn_option( 'header_layout' ), $value, false ) . '><span class="gnn-header-wireframe gnn-header-wireframe--' . esc_attr( $value ) . '" aria-hidden="true"><i></i><i></i><i></i></span><span>' . esc_html( $label ) . '</span></label>';
	}
	echo '</fieldset>';
}
