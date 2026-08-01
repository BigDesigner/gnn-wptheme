<?php
/**
 * GNN GitHub theme updater — bootstrap.
 *
 * Enable/disable and manual "Check now" / "Update now" controls live in
 * GNN Theme Panel -> Advanced (see admin-panel.php).
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require get_template_directory() . '/inc/class-gnn-github-updater.php';

/**
 * Whether GitHub update checks are enabled (GNN Panel -> Advanced).
 * Stored as its own autoloaded option rather than inside `gnn_options`,
 * since it's an updater switch, not themed content.
 *
 * @return bool
 */
function gnn_updates_enabled() {
	return (bool) get_option( 'gnn_github_updates_enable', 1 );
}

/**
 * Shared updater instance (also used by the Advanced-tab status display).
 *
 * @return GNN_GitHub_Updater
 */
function gnn_updater() {
	static $instance = null;
	if ( null === $instance ) {
		$instance = new GNN_GitHub_Updater();
	}
	return $instance;
}
add_action( 'init', 'gnn_updater' );
