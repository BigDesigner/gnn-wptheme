<?php
/**
 * Shared reset primitives used by site reset and demo replacement.
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

/**
 * Remove up to 100 upload entries from the job's directory stack.
 * Moves the job to the 'tables' phase once the stack is empty; the caller persists it.
 *
 * @param array $job Durable reset state in the 'files' phase.
 * @return array|WP_Error
 */
function gnn_reset_files_batch( $job ) {
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
	return $job;
}

/**
 * Take the shared batch lock; a lock older than two minutes is treated as abandoned.
 *
 * @return bool False when another batch holds the lock.
 */
function gnn_reset_batch_lock() {
	$lock = get_option( 'gnn_wipe_batch_lock' );
	if ( $lock && (int) $lock < time() - 120 ) {
		delete_option( 'gnn_wipe_batch_lock' );
	}
	return add_option( 'gnn_wipe_batch_lock', time(), '', false );
}

/** Release the shared batch lock. */
function gnn_reset_batch_unlock() {
	delete_option( 'gnn_wipe_batch_lock' );
}
