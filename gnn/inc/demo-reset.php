<?php
/**
 * Confirmed content replacement. Accounts, active plugins and their settings survive.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Request-scoped permission to run the importer while its own maintenance job exists.
 *
 * @param bool|null $value Set only around the owned import call.
 * @return bool
 */
function gnn_demo_import_context( $value = null ) {
	static $active = false;
	if ( null !== $value ) {
		$active = (bool) $value;
	}
	$job = get_option( 'gnn_wipe_job' );
	return $active && is_array( $job ) && 'demo' === ( $job['kind'] ?? '' ) && 'import' === $job['phase'] && get_current_user_id() === (int) $job['owner'] && hash_equals( home_url( '/' ), $job['site'] );
}

/**
 * Fixed content-table allowlist. Unknown plugin tables are never guessed or dropped.
 *
 * @param bool $validate_columns Inspect row keys before any deletion.
 * @return array|WP_Error Table => primary row key.
 */
function gnn_demo_content_tables( $validate_columns = false ) {
	global $wpdb;
	if ( is_multisite() || ! preg_match( '/^[A-Za-z0-9_]+$/D', $wpdb->prefix ) ) {
		return new WP_Error( 'gnn_demo_scope', __( 'Content replacement is unavailable for this database.', 'gnn' ) );
	}
	$tables = array(
		$wpdb->postmeta           => 'meta_id',
		$wpdb->comments           => 'comment_ID',
		$wpdb->commentmeta        => 'meta_id',
		$wpdb->links              => 'link_id',
		$wpdb->term_relationships => 'object_id',
		$wpdb->termmeta           => 'meta_id',
		$wpdb->term_taxonomy      => 'term_taxonomy_id',
		$wpdb->terms              => 'term_id',
	);
	$woo    = array(
		'wc_orders'                                    => 'id',
		'wc_order_addresses'                           => 'id',
		'wc_order_operational_data'                    => 'id',
		'wc_orders_meta'                               => 'id',
		'woocommerce_order_items'                      => 'order_item_id',
		'woocommerce_order_itemmeta'                   => 'meta_id',
		'wc_order_stats'                               => 'order_id',
		'wc_order_product_lookup'                      => 'order_item_id',
		'wc_order_tax_lookup'                          => 'order_id',
		'wc_order_coupon_lookup'                       => 'order_id',
		'wc_product_meta_lookup'                       => 'product_id',
		'wc_product_attributes_lookup'                 => 'product_id',
		'wc_reserved_stock'                            => 'order_id',
		'woocommerce_downloadable_product_permissions' => 'permission_id',
		'wc_download_log'                              => 'download_log_id',
		'woocommerce_sessions'                         => 'session_id',
	);
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- live table inventory before destructive confirmation takes effect.
	$existing = $wpdb->get_col( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $wpdb->prefix ) . '%' ) );
	if ( ! is_array( $existing ) || ! in_array( $wpdb->posts, $existing, true ) ) {
		return new WP_Error( 'gnn_demo_scope', __( 'Content replacement is unavailable for this database.', 'gnn' ) );
	}
	foreach ( $woo as $table => $column ) {
		$table = $wpdb->prefix . $table;
		if ( in_array( $table, $existing, true ) ) {
			$tables[ $table ] = $column;
		}
	}
	$tables = array_intersect_key( $tables, array_flip( $existing ) );
	if ( $validate_columns ) {
		foreach ( $tables as $table => $column ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table identifiers are from the fixed, prefix-validated allowlist above.
			$columns = $wpdb->get_col( "SHOW COLUMNS FROM `$table`" );
			if ( ! in_array( $column, (array) $columns, true ) ) {
				return new WP_Error( 'gnn_demo_scope', __( 'Content replacement is unavailable for this database.', 'gnn' ) );
			}
		}
	}
	return $tables;
}

/**
 * Validate every prerequisite before deleting a byte or creating a maintenance job.
 *
 * @param string $profile Selected complete demo.
 * @param bool   $confirmed Explicit deletion consent.
 * @param string $revision Current settings revision.
 * @return array|WP_Error Durable job.
 */
function gnn_demo_replace_start( $profile, $confirmed, $revision ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return new WP_Error( 'gnn_demo_permission', __( 'Permission denied.', 'gnn' ) );
	}
	if ( true !== $confirmed ) {
		return new WP_Error( 'gnn_demo_confirm', __( 'Confirm content deletion to apply this demo.', 'gnn' ) );
	}
	if ( 'architecture' !== $profile || ! gnn_starter_elementor_available() ) {
		return new WP_Error( 'gnn_demo_editor', __( 'This demo requires Elementor.', 'gnn' ) );
	}
	foreach ( array( 'publish_pages', 'edit_pages', 'edit_posts', 'upload_files' ) as $capability ) {
		if ( ! current_user_can( $capability ) ) {
			return new WP_Error( 'gnn_demo_permission', __( 'Permission denied.', 'gnn' ) );
		}
	}
	foreach ( range( 0, 28 ) as $number ) {
		if ( 5 !== $number && ! is_readable( get_template_directory() . '/assets/images/demo/architecture/image-' . sprintf( '%02d', $number ) . '.jpg' ) ) {
			return new WP_Error( 'gnn_demo_image', __( 'A demo image is missing. Reinstall the theme.', 'gnn' ) );
		}
	}
	$existing = get_option( 'gnn_wipe_job' );
	if ( $existing ) {
		if ( 'demo' === ( $existing['kind'] ?? '' ) && get_current_user_id() === (int) $existing['owner'] && $profile === $existing['profile'] && hash_equals( home_url( '/' ), $existing['site'] ) ) {
			return $existing;
		}
		return new WP_Error( 'gnn_demo_busy', __( 'Another operation is running.', 'gnn' ) );
	}
	if ( ! is_string( $revision ) || ! hash_equals( gnn_configuration_revision(), $revision ) ) {
		return new WP_Error( 'gnn_conflict', __( 'Settings changed. Reload and try again.', 'gnn' ) );
	}
	if ( get_option( 'gnn_configuration_lock' ) ) {
		return new WP_Error( 'gnn_demo_busy', __( 'Another operation is running.', 'gnn' ) );
	}
	$boundary = gnn_reset_upload_boundary();
	$tables   = gnn_demo_content_tables( true );
	if ( is_wp_error( $boundary ) || is_wp_error( $tables ) ) {
		return is_wp_error( $boundary ) ? $boundary : $tables;
	}
	$directory = $boundary['uploads'];
	while ( ! is_dir( $directory ) && dirname( $directory ) !== $directory ) {
		$directory = dirname( $directory );
	}
	if ( ! wp_is_writable( $directory ) ) {
		return new WP_Error( 'gnn_demo_upload', __( 'Uploads could not be read. Check file permissions.', 'gnn' ) );
	}
	$kit = (int) get_option( 'elementor_active_kit' );
	$kit = 'elementor_library' === get_post_type( $kit ) && 'kit' === get_post_meta( $kit, '_elementor_template_type', true ) ? $kit : 0;
	$job = array(
		'id'                 => wp_generate_uuid4(),
		'kind'               => 'demo',
		'owner'              => get_current_user_id(),
		'site'               => home_url( '/' ),
		'profile'            => $profile,
		'phase'              => 'files',
		'done'               => 0,
		'started'            => time(),
		'stack'              => array( '' ),
		'uploads'            => $boundary['uploads'],
		'root_real'          => $boundary['root_real'],
		'tables'             => $tables,
		'theme'              => get_stylesheet(),
		'template'           => get_template(),
		'protected_post_ids' => $kit ? array( $kit ) : array(),
	);
	if ( ! add_option( 'gnn_wipe_job', $job, '', false ) ) {
		return new WP_Error( 'gnn_demo_busy', __( 'Another operation is running.', 'gnn' ) );
	}
	return $job;
}

/** Remove only references to content that has just been deleted. */
function gnn_demo_clear_references() {
	wp_cache_set( 'last_changed', microtime(), 'terms' );
	foreach ( array( 'gnn_demo_architecture_complete', 'gnn_elementor_templates_installed', 'gnn_usage_index_pending' ) as $option ) {
		delete_option( $option );
	}
	foreach ( array( 'page_on_front', 'page_for_posts', 'site_icon', 'wp_page_for_privacy_policy', 'woocommerce_shop_page_id', 'woocommerce_cart_page_id', 'woocommerce_checkout_page_id', 'woocommerce_myaccount_page_id', 'woocommerce_terms_page_id' ) as $option ) {
		update_option( $option, 0 );
	}
	set_theme_mod( 'nav_menu_locations', array() );
	set_theme_mod( 'custom_logo', 0 );
	$uploads = wp_upload_dir( null, false );
	foreach ( array( 'logo_light', 'logo_light_2x', 'logo_dark', 'logo_dark_2x' ) as $key ) {
		$logo = get_theme_mod( $key );
		if ( is_numeric( $logo ) || ( is_string( $logo ) && 0 === strpos( $logo, trailingslashit( $uploads['baseurl'] ) ) ) ) {
			remove_theme_mod( $key );
		}
	}
	$options  = (array) get_option( 'gnn_options', array() );
	$defaults = gnn_option_defaults();
	foreach ( array( 'logo_light', 'logo_light_2x', 'logo_dark', 'logo_dark_2x', 'footer_logo_light', 'footer_logo_light_2x', 'footer_logo_dark', 'footer_logo_dark_2x', 'error404_image' ) as $key ) {
		$options[ $key ] = $defaults[ $key ] ?? 0;
	}
	foreach ( gnn_platform_schema() as $key => $field ) {
		if ( in_array( $field[1], array( 'region', 'media' ), true ) ) {
			$options[ $key ] = $field[2];
		}
	}
	$options['contact_page_id'] = 0;
	update_option( 'gnn_options', $options );
	$category = wp_insert_term( __( 'Uncategorized', 'gnn' ), 'category' );
	if ( ! is_wp_error( $category ) ) {
		update_option( 'default_category', (int) $category['term_id'] );
	}
	if ( function_exists( 'wc_delete_product_transients' ) ) {
		wc_delete_product_transients();
	}
	if ( function_exists( 'wc_delete_shop_order_transients' ) ) {
		wc_delete_shop_order_transients();
	}
	if ( is_callable( array( 'WC_Cache_Helper', 'invalidate_cache_group' ) ) ) {
		foreach ( array( 'orders', 'products' ) as $group ) {
			WC_Cache_Helper::invalidate_cache_group( $group );
		}
	}
}

/**
 * Advance one bounded destructive batch; import can be retried without repeating deletion.
 *
 * @param string $job_id Durable job ID.
 * @return array|WP_Error
 */
function gnn_demo_replace_step( $job_id ) {
	global $wpdb;
	$job = get_option( 'gnn_wipe_job' );
	if ( ! $job ) {
		$last = get_option( 'gnn_demo_replace_last', array() );
		return current_user_can( 'manage_options' ) && (int) ( $last['owner'] ?? 0 ) === get_current_user_id() && hash_equals( (string) ( $last['id'] ?? '' ), $job_id ) && hash_equals( (string) ( $last['site'] ?? '' ), home_url( '/' ) ) ? $last : new WP_Error( 'gnn_demo_job', __( 'This installation is not available.', 'gnn' ) );
	}
	if ( ! current_user_can( 'manage_options' ) || 'demo' !== ( $job['kind'] ?? '' ) || get_current_user_id() !== (int) $job['owner'] || ! hash_equals( $job['id'], $job_id ) || ! hash_equals( $job['site'], home_url( '/' ) ) || get_stylesheet() !== $job['theme'] || get_template() !== $job['template'] ) {
		return new WP_Error( 'gnn_demo_owner', __( 'Only the administrator who started this installation can continue.', 'gnn' ) );
	}
	if ( ! array_key_exists( 'protected_post_ids', $job ) ) {
		$kit                       = (int) get_option( 'elementor_active_kit' );
		$job['protected_post_ids'] = 'elementor_library' === get_post_type( $kit ) && 'kit' === get_post_meta( $kit, '_elementor_template_type', true ) ? array( $kit ) : array();
	}
	// The shared file walker persists this transition before returning to its caller.
	if ( 'tables' === $job['phase'] ) {
		$job['phase'] = 'posts';
	}
	if ( 'files' === $job['phase'] ) {
		$next = gnn_wipe_step( $job );
		if ( is_wp_error( $next ) ) {
			return $next;
		}
		$job = $next;
		if ( 'tables' === $job['phase'] ) {
			$job['phase'] = 'posts';
		}
	} elseif ( 'posts' === $job['phase'] ) {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- bounded live inventory, all post types including unknown CPTs and revisions.
		$posts = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_type FROM {$wpdb->posts} WHERE ID <> %d ORDER BY ID LIMIT %d", (int) ( $job['protected_post_ids'][0] ?? 0 ), 20 ) );
		foreach ( $posts as $post ) {
			// Deleting a parent also removes revisions already present in this batch.
			if ( ! get_post( (int) $post->ID ) ) {
				continue;
			}
			$deleted = 'attachment' === $post->post_type ? wp_delete_attachment( (int) $post->ID, true ) : wp_delete_post( (int) $post->ID, true );
			if ( ! $deleted ) {
				return new WP_Error( 'gnn_demo_delete', __( 'Content could not be deleted. Check the server and retry.', 'gnn' ) );
			}
			++$job['done'];
		}
		if ( count( $posts ) < 20 ) {
			$job['phase'] = 'content';
		}
	} elseif ( 'content' === $job['phase'] ) {
		$table = key( $job['tables'] );
		if ( $table ) {
			$column  = $job['tables'][ $table ];
			$allowed = gnn_demo_content_tables();
			if ( is_wp_error( $allowed ) || ! isset( $allowed[ $table ] ) || $allowed[ $table ] !== $column ) {
				return new WP_Error( 'gnn_demo_scope', __( 'Content replacement is unavailable for this database.', 'gnn' ) );
			}
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- identifiers from a fixed allowlist verified against the current database; values remain bound.
			$sql = $table === $wpdb->postmeta
				? $wpdb->prepare( "DELETE FROM `$table` WHERE `$column` IN (SELECT `$column` FROM (SELECT `$column` FROM `$table` WHERE post_id <> %d ORDER BY `$column` LIMIT %d) AS gnn_demo_batch)", (int) ( $job['protected_post_ids'][0] ?? 0 ), 500 )
				: $wpdb->prepare( "DELETE FROM `$table` WHERE `$column` IN (SELECT `$column` FROM (SELECT `$column` FROM `$table` ORDER BY `$column` LIMIT %d) AS gnn_demo_batch)", 500 );
			// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared -- bounded destructive batch with allowlisted identifiers and bound limit.
			$count = $wpdb->query( $sql );
			if ( false === $count ) {
				return new WP_Error( 'gnn_demo_delete', __( 'Content could not be deleted. Check the server and retry.', 'gnn' ) );
			}
			$job['done'] += $count;
			if ( $count < 500 ) {
				unset( $job['tables'][ $table ] );
			}
		} else {
			gnn_demo_clear_references();
			$job['phase'] = 'import';
		}
	} elseif ( 'import' === $job['phase'] ) {
		// Generate one attachment's derivatives per request before saving the page graph.
		$cursor = (int) ( $job['media_cursor'] ?? 0 );
		if ( 5 === $cursor ) {
			++$cursor;
		}
		if ( 28 >= $cursor ) {
			$media = gnn_demo_media( $cursor, __( 'Architecture / Selected projects', 'gnn' ) );
			if ( is_wp_error( $media ) ) {
				return $media;
			}
			$job['media_cursor'] = $cursor + 1;
			++$job['done'];
			update_option( 'gnn_wipe_job', $job, false );
			return $job;
		}
		gnn_demo_import_context( true );
		try {
			$result = gnn_import_demo_site( $job['profile'], true, gnn_configuration_revision() );
		} finally {
			gnn_demo_import_context( false );
		}
		if ( is_wp_error( $result ) ) {
			return $result;
		}
		$job['phase']    = 'complete';
		$job['redirect'] = admin_url( 'admin.php?page=gnn-workspace&path=ready' );
		update_option( 'gnn_demo_replace_last', $job, false );
		delete_option( 'gnn_wipe_job' );
		flush_rewrite_rules( false );
		return $job;
	}
	update_option( 'gnn_wipe_job', $job, false );
	return $job;
}

/** One nonce-protected endpoint for start, continuation and completed-response retries. */
function gnn_demo_replace_ajax() {
	check_ajax_referer( 'gnn_demo_replace', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied.', 'gnn' ) ), 403 );
	}
	$lock = get_option( 'gnn_wipe_batch_lock' );
	if ( $lock && (int) $lock < time() - 120 ) {
		delete_option( 'gnn_wipe_batch_lock' );
	}
	if ( ! add_option( 'gnn_wipe_batch_lock', time(), '', false ) ) {
		wp_send_json_error( array( 'message' => __( 'Another operation is running.', 'gnn' ) ), 409 );
	}
	try {
		if ( ! empty( $_POST['start'] ) ) {
			$profile  = isset( $_POST['profile'] ) && is_string( $_POST['profile'] ) ? sanitize_key( wp_unslash( $_POST['profile'] ) ) : '';
			$revision = isset( $_POST['revision'] ) && is_string( $_POST['revision'] ) ? sanitize_text_field( wp_unslash( $_POST['revision'] ) ) : '';
			$result   = gnn_demo_replace_start( $profile, isset( $_POST['confirm_delete'] ) && '1' === $_POST['confirm_delete'], $revision );
		} else {
			$id     = isset( $_POST['job'] ) && is_string( $_POST['job'] ) ? sanitize_text_field( wp_unslash( $_POST['job'] ) ) : '';
			$result = gnn_demo_replace_step( $id );
		}
	} catch ( Throwable $error ) {
		$result = new WP_Error( 'gnn_demo_failed', __( 'Installation stopped. Check the server and retry.', 'gnn' ) );
	} finally {
		delete_option( 'gnn_wipe_batch_lock' );
	}
	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => $result->get_error_message() ), 409 );
	}
	wp_send_json_success(
		array(
			'job'      => $result['id'],
			'phase'    => $result['phase'],
			'done'     => $result['done'],
			'redirect' => $result['redirect'] ?? '',
		)
	);
}
add_action( 'wp_ajax_gnn_demo_replace', 'gnn_demo_replace_ajax' );
