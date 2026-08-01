<?php
/**
 * Per-page display options: hide the page title and/or the breadcrumb
 * ("Home / Current"). Shown as a meta box on the page/post editor.
 *
 * Stored as post meta:
 *   _gnn_hide_title       '1' to hide the entry title.
 *   _gnn_hide_breadcrumb  '1' to hide the breadcrumb navigation.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the meta so it's revisioned and REST-exposed.
 */
function gnn_register_page_meta() {
	foreach ( array( '_gnn_hide_title', '_gnn_hide_breadcrumb' ) as $key ) {
		register_post_meta(
			'',
			$key,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'gnn_sanitize_bool_meta',
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}
add_action( 'init', 'gnn_register_page_meta' );

/**
 * Sanitize a boolean-ish meta value to '1' or ''.
 *
 * @param mixed $value Raw value.
 * @return string
 */
function gnn_sanitize_bool_meta( $value ) {
	return $value ? '1' : '';
}

/**
 * Add the meta box to pages and posts.
 */
function gnn_add_page_meta_box() {
	add_meta_box(
		'gnn_display_options',
		__( 'GNN Display Options', 'gnn' ),
		'gnn_render_page_meta_box',
		array( 'page', 'post' ),
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'gnn_add_page_meta_box' );

/**
 * Render the meta box.
 *
 * @param WP_Post $post Current post.
 */
function gnn_render_page_meta_box( $post ) {
	wp_nonce_field( 'gnn_page_meta', 'gnn_page_meta_nonce' );
	$hide_title = get_post_meta( $post->ID, '_gnn_hide_title', true );
	$hide_bc    = get_post_meta( $post->ID, '_gnn_hide_breadcrumb', true );
	?>
	<p>
		<label>
			<input type="checkbox" name="gnn_hide_title" value="1" <?php checked( $hide_title, '1' ); ?>>
			<?php esc_html_e( 'Hide the page title', 'gnn' ); ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="gnn_hide_breadcrumb" value="1" <?php checked( $hide_bc, '1' ); ?>>
			<?php esc_html_e( 'Hide the breadcrumb (Home / …)', 'gnn' ); ?>
		</label>
	</p>
	<?php
}

/**
 * Save the meta box values.
 *
 * @param int $post_id Post ID.
 */
function gnn_save_page_meta( $post_id ) {
	if ( ! isset( $_POST['gnn_page_meta_nonce'] )
		|| ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['gnn_page_meta_nonce'] ) ), 'gnn_page_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, '_gnn_hide_title', empty( $_POST['gnn_hide_title'] ) ? '' : '1' );
	update_post_meta( $post_id, '_gnn_hide_breadcrumb', empty( $_POST['gnn_hide_breadcrumb'] ) ? '' : '1' );
}
add_action( 'save_post', 'gnn_save_page_meta' );

/**
 * Whether the current singular entry hides its title.
 *
 * @return bool
 */
function gnn_hide_title() {
	return is_singular() && '1' === get_post_meta( get_the_ID(), '_gnn_hide_title', true );
}

/**
 * Whether the current singular entry hides its breadcrumb.
 *
 * @return bool
 */
function gnn_hide_breadcrumb() {
	return is_singular() && '1' === get_post_meta( get_the_ID(), '_gnn_hide_breadcrumb', true );
}
