<?php
/**
 * Reusable authoring tools and administrator diagnostics.
 *
 * @package GNN_Content
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Supported shared-block roles; untyped legacy blocks remain usable. */
function gnn_block_roles() {
	return array_merge(
		array( '' => __( 'General purpose', 'gnn-content' ) ),
		array(
			'header'   => __( 'Header', 'gnn-content' ),
			'footer'   => __( 'Footer', 'gnn-content' ),
			'titlebar' => __( 'Title bar', 'gnn-content' ),
			'sidebar'  => __( 'Sidebar', 'gnn-content' ),
			'content'  => __( 'Content', 'gnn-content' ),
		)
	);
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
	add_meta_box( 'gnn-block-role', __( 'Block role', 'gnn-content' ), 'gnn_block_role_fields', 'gnn_layout', 'side' );
}
add_action( 'add_meta_boxes', 'gnn_block_role_box' );

/**
 * Role selector uses the existing post permission boundary.
 *
 * @param WP_Post $post Shared block.
 */
function gnn_block_role_fields( $post ) {
	wp_nonce_field( 'gnn_block_role', 'gnn_block_role_nonce' );
	echo '<label class="screen-reader-text" for="gnn-block-role-select">' . esc_html__( 'Block role', 'gnn-content' ) . '</label><select id="gnn-block-role-select" name="gnn_block_role">';
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
	$columns['gnn_role'] = __( 'Block role', 'gnn-content' );
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
		echo esc_html( gnn_block_roles()[ get_post_meta( $id, '_gnn_block_role', true ) ] ?? __( 'General purpose', 'gnn-content' ) );
	}
}
add_action( 'manage_gnn_layout_posts_custom_column', 'gnn_block_role_column', 10, 2 );
