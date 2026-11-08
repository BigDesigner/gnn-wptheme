<?php
/**
 * Explicit, resumable single-site reset.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve the configured local media directory without including application files.
 *
 * @return array|WP_Error Approved upload boundary.
 */
function gnn_reset_upload_boundary() {
	$uploads = wp_upload_dir( null, false );
	$actual  = wp_normalize_path( $uploads['basedir'] );
	$content = wp_normalize_path( realpath( WP_CONTENT_DIR ) );
	if ( $uploads['error'] || ! $content || ( file_exists( $actual ) && ! is_dir( $actual ) ) || is_link( $actual ) || 0 !== strpos( trailingslashit( $actual ), trailingslashit( $content ) ) || $actual === $content ) {
		return new WP_Error( 'gnn_wipe_uploads', __( 'The media folder must be a local directory inside wp-content.', 'gnn' ) );
	}
	$protected = array( WP_PLUGIN_DIR, WPMU_PLUGIN_DIR, WP_CONTENT_DIR . '/themes', WP_CONTENT_DIR . '/languages', get_theme_root() );
	foreach ( $protected as $directory ) {
		$directory = wp_normalize_path( $directory );
		if ( 0 === strpos( trailingslashit( $actual ), trailingslashit( $directory ) ) || 0 === strpos( trailingslashit( $directory ), trailingslashit( $actual ) ) ) {
			return new WP_Error( 'gnn_wipe_uploads', __( 'The media folder overlaps theme or plugin files.', 'gnn' ) );
		}
	}
	$ancestor = $actual;
	while ( $ancestor !== $content && dirname( $ancestor ) !== $ancestor ) {
		if ( is_link( $ancestor ) ) {
			return new WP_Error( 'gnn_wipe_link', __( 'Remove upload folder symlinks before resetting.', 'gnn' ) );
		}
		$ancestor = dirname( $ancestor );
	}
	$parent = $actual;
	while ( ! file_exists( $parent ) && dirname( $parent ) !== $parent ) {
		$parent = dirname( $parent );
	}
	$resolved = wp_normalize_path( realpath( $parent ) );
	if ( ! $resolved || ( $resolved !== $content && 0 !== strpos( trailingslashit( $resolved ), trailingslashit( $content ) ) ) ) {
		return new WP_Error( 'gnn_wipe_uploads', __( 'The media folder must be a local directory inside wp-content.', 'gnn' ) );
	}
	try {
		if ( is_dir( $actual ) ) {
			$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $actual, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::SELF_FIRST );
			foreach ( $iterator as $entry ) {
				if ( $entry->isLink() ) {
					return new WP_Error( 'gnn_wipe_link', __( 'Remove upload folder symlinks before resetting.', 'gnn' ) );
				}
			}
		}
	} catch ( UnexpectedValueException $error ) {
		return new WP_Error( 'gnn_wipe_read', __( 'Uploads could not be read. Check file permissions.', 'gnn' ) );
	}
	return array(
		'uploads'   => $actual,
		'root_real' => realpath( $actual ),
	);
}

/** Inspect the exact database/upload boundary before offering a reset.
 *
 * @return array|WP_Error
 */
function gnn_wipe_plan() {
	global $wpdb;
	if ( is_multisite() || defined( 'CUSTOM_USER_TABLE' ) || defined( 'CUSTOM_USER_META_TABLE' ) || $wpdb->users !== $wpdb->prefix . 'users' || $wpdb->usermeta !== $wpdb->prefix . 'usermeta' ) {
		return new WP_Error( 'gnn_wipe_shared', __( 'Reset is unavailable for shared or multisite databases.', 'gnn' ) );
	}
	if ( ! preg_match( '/^[A-Za-z0-9_]+$/D', $wpdb->prefix ) ) {
		return new WP_Error( 'gnn_wipe_prefix', __( 'Unsupported database prefix.', 'gnn' ) );
	}
	if ( file_exists( WP_CONTENT_DIR . '/object-cache.php' ) || ( defined( 'WP_CACHE' ) && WP_CACHE && file_exists( WP_CONTENT_DIR . '/advanced-cache.php' ) ) ) {
		return new WP_Error( 'gnn_wipe_cache', __( 'Disable persistent caching before resetting.', 'gnn' ) );
	}
	$boundary = gnn_reset_upload_boundary();
	if ( is_wp_error( $boundary ) ) {
		return $boundary;
	}
	$actual = $boundary['uploads'];
 // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- reset inventory must reflect the live database.
	$tables = $wpdb->get_col( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $wpdb->prefix ) . '%' ) );
	if ( ! is_array( $tables ) || ! in_array( $wpdb->options, $tables, true ) ) {
		return new WP_Error( 'gnn_wipe_tables', __( 'Database inventory is unavailable.', 'gnn' ) );
	}
	foreach ( $tables as $table ) {
		if ( ! preg_match( '/^[A-Za-z0-9_]+$/D', $table ) || 0 !== strpos( $table, $wpdb->prefix ) || ( substr( $table, -8 ) === '_options' && $table !== $wpdb->options ) || preg_match( '/^' . preg_quote( $wpdb->prefix, '/' ) . '[0-9]+_/', $table ) ) {
			return new WP_Error( 'gnn_wipe_scope', __( 'Another site may share this database prefix. Reset is unavailable.', 'gnn' ) );
		}
	}
	return array(
		'tables'   => $tables,
		'uploads'  => $actual,
		'site'     => home_url( '/' ),
		'theme'    => get_stylesheet(),
		'template' => get_template(),
		'owner'    => get_current_user_id(),
	);
}

/** Present destructive scope at the decision point, not in ordinary settings. */
function gnn_wipe_screen() {
	echo '<section class="gnn-settings-card gnn-card-content gnn-danger"><h2>' . esc_html__( 'Reset WordPress', 'gnn' ) . '</h2>';
	$job = get_option( 'gnn_wipe_job' );
	if ( is_array( $job ) && 'demo' === ( $job['kind'] ?? '' ) ) {
		gnn_demo_resume_screen( $job );
		echo '</section>';
		return;
	}
	if ( is_array( $job ) ) {
		echo '<p>' . esc_html__( 'A reset is in progress. Continue to finish it.', 'gnn' ) . '</p><button type="button" class="button button-primary gnn-wipe-resume">' . esc_html__( 'Continue reset', 'gnn' ) . '</button><p class="gnn-wipe-status" role="status"></p></section>';
		return;
	}
	$plan = gnn_wipe_plan();
	if ( is_wp_error( $plan ) ) {
		echo '<p>' . esc_html( $plan->get_error_message() ) . '</p></section>';
		return;
	}
	echo '<p>' . esc_html__( 'All content, orders, media files, other users and plugin settings will be deleted. Your administrator account and theme/plugin files will remain. Plugins will be deactivated.', 'gnn' ) . '</p><p><strong>' . esc_html__( 'This cannot be undone. Take a database and files backup first.', 'gnn' ) . '</strong></p><form class="gnn-wipe-form"><p><label><input type="checkbox" name="backup" value="1" required> ' . esc_html__( 'I have a database and files backup.', 'gnn' ) . '</label></p><p><label for="gnn-wipe-site">' . esc_html__( 'Type the site address', 'gnn' ) . '</label><br><code>' . esc_html( $plan['site'] ) . '</code><br><input id="gnn-wipe-site" type="url" name="site" required autocomplete="off"></p><p><label><input type="checkbox" name="confirmed" value="1" required> ' . esc_html__( 'Delete the listed data and reset WordPress.', 'gnn' ) . '</label></p><button class="button gnn-wipe-start" type="submit">' . esc_html__( 'Wipe', 'gnn' ) . '</button><p class="gnn-wipe-status" role="status"></p></form></section>';
}

/** Start requires a separate destructive confirmation, permissions and exact site identity. */
function gnn_wipe_start() {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'delete_users' ) || ! current_user_can( 'activate_plugins' ) ) {
		return new WP_Error( 'gnn_wipe_permission', __( 'Permission denied.', 'gnn' ) );
	}
	if ( get_option( 'gnn_wipe_job' ) ) {
		return new WP_Error( 'gnn_wipe_busy', __( 'A reset is already running.', 'gnn' ) );
	}
	$site = isset( $_POST['site'] ) && is_string( $_POST['site'] ) ? esc_url_raw( wp_unslash( $_POST['site'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- AJAX entry verifies gnn_wipe nonce.
	if ( empty( $_POST['backup'] ) || empty( $_POST['confirmed'] ) || ! hash_equals( home_url( '/' ), $site ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified at AJAX boundary.
		return new WP_Error( 'gnn_wipe_confirm', __( 'Confirm the backup, site address and deletion scope.', 'gnn' ) );
	}
	$plan = gnn_wipe_plan();
	if ( is_wp_error( $plan ) ) {
		return $plan;
	}
	if ( get_option( 'gnn_configuration_lock' ) ) {
		return new WP_Error( 'gnn_wipe_lock', __( 'Another operation is running.', 'gnn' ) );
	}
	$plan['locale']    = get_locale();
	$plan['root_real'] = realpath( $plan['uploads'] );
	$job               = $plan + array(
		'phase'   => 'files',
		'stack'   => array( '' ),
		'done'    => 0,
		'started' => time(),
	);
	if ( ! add_option( 'gnn_wipe_job', $job, '', false ) ) {
		return new WP_Error( 'gnn_wipe_busy', __( 'A reset is already running.', 'gnn' ) );
	}
	update_option( 'active_plugins', array() );
	return $job;
}

/** Remove only a path whose resolved parent is inside the approved root.
 *
 * @param string $root Canonical upload root.
 * @param string $relative Relative path.
 * @return string|WP_Error
 */
function gnn_wipe_path( $root, $relative ) {
	$path     = $root . ( '' === $relative ? '' : '/' . $relative );
	$resolved = realpath( $path );
	$base     = realpath( $root );
	if ( is_link( $root ) || is_link( $path ) || ! $base || ! $resolved || ( $resolved !== $base && 0 !== strpos( wp_normalize_path( $resolved ), trailingslashit( wp_normalize_path( $base ) ) ) ) ) {
		return new WP_Error( 'gnn_wipe_path', __( 'Upload boundary changed. Reset stopped.', 'gnn' ) );
	}
	return $path;
}

/** One short batch can be retried without replaying completed work.
 *
 * @param array $job Durable reset state.
 * @return array|WP_Error
 */
function gnn_wipe_step( $job ) {
	global $wpdb;
	if ( ! is_array( $job ) || get_current_user_id() !== (int) $job['owner'] || ! current_user_can( 'manage_options' ) || ! hash_equals( home_url( '/' ), $job['site'] ) ) {
		return new WP_Error( 'gnn_wipe_owner', __( 'Only the administrator who started this reset can continue.', 'gnn' ) );
	}
	if ( 'files' === $job['phase'] ) {
		if ( is_link( $job['uploads'] ) || ( file_exists( $job['uploads'] ) && isset( $job['root_real'] ) && realpath( $job['uploads'] ) !== $job['root_real'] ) ) {
			return new WP_Error( 'gnn_wipe_root', __( 'Upload boundary changed. Reset stopped.', 'gnn' ) ); }
		$listings = array();
		$cursors  = array();
		for ( $batch = 0; 100 > $batch && $job['stack']; ++$batch ) {
			if ( ! file_exists( $job['uploads'] ) ) {
				$job['stack'] = array();
				break;
			}
			$relative = end( $job['stack'] );
			if ( '' !== $relative && ! file_exists( $job['uploads'] . '/' . $relative ) && ! is_link( $job['uploads'] . '/' . $relative ) ) {
				array_pop( $job['stack'] );
				continue;
			}
			$path = gnn_wipe_path( $job['uploads'], $relative );
			if ( is_wp_error( $path ) ) {
				return $path;
			}
			if ( ! isset( $listings[ $path ] ) ) {
				$entries = scandir( $path );
				if ( false === $entries ) {
					return new WP_Error( 'gnn_wipe_read', __( 'Uploads could not be read. Check file permissions.', 'gnn' ) ); }
				$listings[ $path ] = array_values( array_diff( $entries, array( '.', '..' ) ) );
				$cursors[ $path ]  = 0;
			}
			$entry = $listings[ $path ][ $cursors[ $path ] ] ?? null;
			if ( null === $entry ) {
				if ( '' !== $relative && ! rmdir( $path ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_rmdir -- exact validated local upload directory.
					return new WP_Error( 'gnn_wipe_directory', __( 'An upload directory could not be removed.', 'gnn' ) );
				}
				array_pop( $job['stack'] );
			} else {
				$child = ltrim( $relative . '/' . $entry, '/' );
				++$cursors[ $path ];
				$target = gnn_wipe_path( $job['uploads'], $child );
				if ( is_wp_error( $target ) ) {
					return $target;
				}
				if ( is_dir( $target ) ) {
					$job['stack'][] = $child;
				} elseif ( ! unlink( $target ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink -- exact validated local upload file; errors stop the job.
					return new WP_Error( 'gnn_wipe_file', __( 'An upload file could not be removed. Check file permissions.', 'gnn' ) );
				} else {
					++$job['done'];
				}
			}
		}
		if ( ! $job['stack'] ) {
			$job['phase'] = 'tables';
		}
	} elseif ( 'tables' === $job['phase'] ) {
		$table = array_shift( $job['tables'] );
		if ( $table ) {
			if ( ! preg_match( '/^[A-Za-z0-9_]+$/D', $table ) || 0 !== strpos( $table, $wpdb->prefix ) ) {
				return new WP_Error( 'gnn_wipe_table', __( 'Database boundary changed. Reset stopped.', 'gnn' ) );
			}
			if ( ! in_array( $table, array( $wpdb->options, $wpdb->users, $wpdb->usermeta ), true ) ) {
				$columns = array(
					$wpdb->posts              => 'ID',
					$wpdb->postmeta           => 'meta_id',
					$wpdb->comments           => 'comment_ID',
					$wpdb->commentmeta        => 'meta_id',
					$wpdb->links              => 'link_id',
					$wpdb->terms              => 'term_id',
					$wpdb->term_taxonomy      => 'term_taxonomy_id',
					$wpdb->term_relationships => 'object_id',
					$wpdb->termmeta           => 'meta_id',
				);
				$column  = $columns[ $table ] ?? '';
				if ( $column ) {
				 // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- validated installation identifier plus a column from the fixed map above.
					$sql = $wpdb->prepare( "DELETE FROM `$table` WHERE `$column` IN (SELECT `$column` FROM (SELECT `$column` FROM `$table` ORDER BY `$column` LIMIT %d) AS gnn_reset_batch)", 500 );
				} else {
					$sql = 'DROP TABLE IF EXISTS `' . $table . '`'; }
			 // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared -- identifier is from the captured live inventory, validated to the installation prefix and identifier alphabet above; no values interpolated.
				$deleted = $wpdb->query( $sql );
				if ( false === $deleted ) {
					return new WP_Error( 'gnn_wipe_db', __( 'A database table could not be reset.', 'gnn' ) );
				}
				if ( $column && $deleted >= 500 ) {
					array_unshift( $job['tables'], $table ); }
			}
		}
		if ( ! $job['tables'] ) {
			$job['phase'] = 'finish';
		}
	} elseif ( 'finish' === $job['phase'] ) {
		$owner = (int) $job['owner'];
		$user  = get_userdata( $owner );
		if ( ! $user ) {
			return new WP_Error( 'gnn_wipe_admin', __( 'The administrator account is unavailable.', 'gnn' ) );
		}
		$email    = $user->user_email;
		$url      = get_option( 'siteurl' );
		$home     = get_option( 'home' );
		$locale   = $job['locale'] ?? get_locale();
		$sessions = get_user_meta( $owner, 'session_tokens', true );
	 // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- deletion scope is the verified local users table and retained owner.
		if ( false === $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->users} WHERE ID <> %d", $owner ) ) ) {
			return new WP_Error( 'gnn_wipe_users', __( 'Users could not be reset.', 'gnn' ) );
		}
	 // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- reset every local user setting; owner session restored below.
		if ( false === $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->usermeta} WHERE user_id <> %d OR meta_key NOT IN (%s,%s,%s)", $owner, $wpdb->prefix . 'capabilities', $wpdb->prefix . 'user_level', 'session_tokens' ) ) ) {
			return new WP_Error( 'gnn_wipe_meta', __( 'User settings could not be reset.', 'gnn' ) ); }
		// Keep the durable job until final initialization succeeds.
	 // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- exact local options table and fixed preserved keys.
		$preserve     = array( 'gnn_wipe_job', 'siteurl', 'home', 'stylesheet', 'template', $wpdb->prefix . 'user_roles', 'auth_key', 'auth_salt', 'secure_auth_key', 'secure_auth_salt', 'logged_in_key', 'logged_in_salt', 'nonce_key', 'nonce_salt' );
		$placeholders = implode( ',', array_fill( 0, count( $preserve ), '%s' ) );
  // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- placeholder list is generated only from fixed preserved option keys.
		if ( false === $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name NOT IN ($placeholders)", $preserve ) ) ) {
			return new WP_Error( 'gnn_wipe_options', __( 'Site settings could not be reset.', 'gnn' ) ); }
		wp_cache_flush();
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		make_db_current_silent();
		populate_options();
		// Construct core roles in memory and replace them atomically, retaining login access on interruption.
		$roles               = wp_roles();
		$roles->use_db       = false;
		$roles->roles        = array();
		$roles->role_objects = array();
		$roles->role_names   = array();
		populate_roles();
		$roles->use_db = true;
		update_option( $roles->role_key, $roles->roles );
		update_option( 'siteurl', $url );
		update_option( 'home', $home );
		update_option( 'admin_email', $email );
		update_option( 'WPLANG', $locale );
		update_option( 'template', $job['template'] );
		update_option( 'stylesheet', $job['theme'] );
		update_option( 'current_theme', wp_get_theme( $job['theme'] )->get( 'Name' ) );
		update_option( 'active_plugins', array() );
		update_user_meta( $owner, 'session_tokens', $sessions );
		clean_user_cache( $owner );
		$user = new WP_User( $owner );
		$user->set_role( 'administrator' );
		wp_set_current_user( $owner );
		if ( ! current_user_can( 'manage_options' ) || get_option( 'stylesheet' ) !== $job['theme'] ) {
			return new WP_Error( 'gnn_wipe_finish', __( 'Reset initialization failed. Continue after checking the database.', 'gnn' ) );
		}
		delete_option( 'gnn_wipe_job' );
		$category = wp_insert_term( __( 'Uncategorized', 'gnn' ), 'category' );
		if ( ! is_wp_error( $category ) ) {
			update_option( 'default_category', $category['term_id'] ); }
		flush_rewrite_rules( false );
		return array(
			'phase'    => 'complete',
			'done'     => $job['done'],
			'redirect' => admin_url( 'admin.php?page=gnn-workspace' ),
		);
	}
	update_option( 'gnn_wipe_job', $job, false );
	return $job;
}

/** Serialize reset batches; a second tab cannot advance the same job concurrently. */
function gnn_wipe_ajax() {
	check_ajax_referer( 'gnn_wipe', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied.', 'gnn' ) ), 403 );
	}
	$job = get_option( 'gnn_wipe_job' );
	if ( is_array( $job ) && 'demo' === ( $job['kind'] ?? '' ) ) {
		wp_send_json_error( array( 'message' => __( 'Continue the demo installation from Setup wizard.', 'gnn' ) ), 409 );
	}
	$lock = get_option( 'gnn_wipe_batch_lock' );
	if ( $lock && (int) $lock < time() - 120 ) {
		delete_option( 'gnn_wipe_batch_lock' );
	}
	if ( ! add_option( 'gnn_wipe_batch_lock', time(), '', false ) ) {
		wp_send_json_error( array( 'message' => __( 'Another reset batch is running.', 'gnn' ) ), 409 );
	}
	try {
		$start  = ! empty( $_POST['start'] );
		$result = $start ? gnn_wipe_start() : gnn_wipe_step( get_option( 'gnn_wipe_job' ) );
	} catch ( Throwable $error ) {
		$result = new WP_Error( 'gnn_wipe_failure', __( 'Reset stopped. Check the server and retry.', 'gnn' ) );
	} finally {
		delete_option( 'gnn_wipe_batch_lock' );
	}
	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => $result->get_error_message() ), 409 );
	}
	wp_send_json_success(
		array(
			'phase'    => $result['phase'],
			'done'     => $result['done'],
			'redirect' => $result['redirect'] ?? '',
		)
	);
}
add_action( 'wp_ajax_gnn_wipe', 'gnn_wipe_ajax' );

/** Keep ordinary admin writes away from a partially reset installation. */
function gnn_wipe_admin_gate() {
	$job = get_option( 'gnn_wipe_job' );
	if ( ! $job ) {
		return; }
	if ( wp_doing_ajax() ) {
		$action = isset( $_POST['action'] ) && is_string( $_POST['action'] ) ? sanitize_key( wp_unslash( $_POST['action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- read-only maintenance routing; reset endpoint verifies its own nonce.
		if ( ( 'demo' === ( $job['kind'] ?? '' ) && 'gnn_demo_replace' === $action ) || ( 'demo' !== ( $job['kind'] ?? '' ) && 'gnn_wipe' === $action ) ) {
			return; }
		wp_send_json_error( array( 'message' => __( 'Maintenance in progress.', 'gnn' ) ), 503 );
	}
	$page    = isset( $_GET['page'] ) && is_string( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only routing.
	$allowed = 'demo' === ( $job['kind'] ?? '' ) ? 'gnn-workspace' : 'gnn-maintenance';
	if ( $allowed !== $page ) {
		wp_safe_redirect( admin_url( 'admin.php?page=' . $allowed ) );
		exit;
	}
}
add_action( 'admin_init', 'gnn_wipe_admin_gate', 1 );
