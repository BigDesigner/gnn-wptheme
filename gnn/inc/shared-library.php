<?php
/**
 * Shared blocks, usage inventory and reusable collection layouts.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Find exact shared references in native shortcode and builder data.
 *
 * @param string $content Post content.
 * @param array  $elements Builder elements.
 */
function gnn_shared_references( $content, $elements ) {
	$ids = array();
	if ( preg_match_all( '/\[gnn_block\s+[^\]]*\bid=["\']?([0-9]+)/', $content, $matches ) ) {
		$ids = array_map( 'absint', $matches[1] );
	}
	$scan = function ( $items, $depth = 0 ) use ( &$scan, &$ids ) {
		if ( ! is_array( $items ) || $depth > 16 ) {
			return;
		}
		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$settings = $item['settings'] ?? array();
			foreach ( array( 'shared_block', 'shared_block_id' ) as $key ) {
				if ( ! empty( $settings[ $key ] ) && is_scalar( $settings[ $key ] ) ) {
					$ids[] = absint( $settings[ $key ] );
				}
			}
			foreach ( $settings as $value ) {
				if ( is_string( $value ) && preg_match_all( '/\[gnn_block\s+[^\]]*\bid=["\']?([0-9]+)/', $value, $found ) ) {
					$ids = array_merge( $ids, array_map( 'absint', $found[1] ) );
				}
			}
			$scan( $item['elements'] ?? array(), $depth + 1 );
		}
	};
	$scan( $elements );
	return array_values( array_unique( array_filter( $ids ) ) );
}

/** Maintain per-document reference metadata on ordinary editor writes.
 *
 * @param int $id Content identifier.
 */
function gnn_shared_index_record( $id ) {
	if ( wp_is_post_revision( $id ) ) {
		return;
	}
	$record = get_post( $id );
	if ( ! $record || 'attachment' === $record->post_type ) {
		return;
	}
	$refs = gnn_shared_references( $record->post_content, json_decode( (string) get_post_meta( $id, '_elementor_data', true ), true ) );
	foreach ( (array) get_post_meta( $id, '_gnn_layout', true ) as $key => $value ) {
		if ( false !== strpos( $key, '_template' ) && (int) $value > 0 ) {
			$refs[] = absint( $value );
		}
	}
	$refs     = array_values( array_unique( $refs ) );
	$previous = (array) get_post_meta( $id, '_gnn_shared_refs', true );
	foreach ( array_diff( $previous, $refs ) as $ref ) {
		delete_post_meta( $id, '_gnn_ref_' . absint( $ref ) ); }
	foreach ( $refs as $ref ) {
		update_post_meta( $id, '_gnn_ref_' . absint( $ref ), '1' ); }
	update_post_meta( $id, '_gnn_shared_refs', $refs );
}
add_action( 'save_post', 'gnn_shared_index_record', 100 );

/** Builder/meta changes occur after the native post write.
 *
 * @param int    $meta_id Meta ID.
 * @param int    $post_id Content ID.
 * @param string $key Meta key.
 */
function gnn_shared_index_meta( $meta_id, $post_id, $key ) {
	if ( in_array( $key, array( '_elementor_data', '_gnn_layout' ), true ) ) {
		gnn_shared_index_record( $post_id );
	}
}
add_action( 'updated_post_meta', 'gnn_shared_index_meta', 10, 3 );
add_action( 'added_post_meta', 'gnn_shared_index_meta', 10, 3 );
add_action( 'deleted_post_meta', 'gnn_shared_index_meta', 10, 3 );

/** Usage columns list live assignments and direct content references.
 *
 * @param array $columns Native columns.
 */
function gnn_shared_columns( $columns ) {
	$columns['gnn_usage'] = __( 'Used in', 'gnn' );
	return $columns;
}
add_filter( 'manage_gnn_layout_posts_columns', 'gnn_shared_columns' );

/** Render access-checked usage links; scan native records in bounded batches once per request.
 *
 * @param string $column Column key.
 * @param int    $id Shared record ID.
 */
function gnn_shared_column( $column, $id ) {
	if ( 'gnn_usage' !== $column ) {
		return;
	}
	$index = array();
 // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- indexed reference inventory, bounded displayed consumers.
	$query = new WP_Query(
		array(
			'post_type'      => array_values( array_diff( get_post_types( array( 'show_ui' => true ) ), array( 'attachment' ) ) ),
			'post_status'    => array( 'publish', 'draft', 'private', 'pending', 'future' ),
			'posts_per_page' => 50,
			'no_found_rows'  => true,
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- bounded admin reference query.
			'meta_query'     => array(
				array(
					'key'     => '_gnn_ref_' . absint( $id ),
					'value'   => '1',
					'compare' => '=',
				),
			),
		)
	);
	foreach ( $query->posts as $record ) {
		if ( in_array( (int) $id, (array) get_post_meta( $record->ID, '_gnn_shared_refs', true ), true ) ) {
			$index[ $id ][ $record->ID ] = $record->post_title;
		}
	}
	$found = false;
	foreach ( (array) get_option( 'gnn_options', array() ) as $key => $value ) {
		if ( ( false !== strpos( $key, '_template' ) ) && (int) $value === (int) $id ) {
			echo '<p><a href="' . esc_url( admin_url( 'admin.php?page=gnn-assignments' ) ) . '">' . esc_html( str_replace( '_', ' ', $key ) ) . '</a></p>';
			$found = true;
		}
	}
	foreach ( $index[ $id ] ?? array() as $record_id => $title ) {
		if ( current_user_can( 'edit_post', $record_id ) ) {
			echo '<p><a href="' . esc_url( get_edit_post_link( $record_id ) ) . '">' . esc_html( $title ) . '</a></p>';
			$found = true;
		}
	}
	if ( count( $query->posts ) >= 50 ) {
		echo '<p><a href="' . esc_url( admin_url( 'admin.php?page=gnn-usage&ref=' . absint( $id ) ) ) . '">' . esc_html__( 'All uses', 'gnn' ) . '</a></p>';
	}
	if ( ! $found ) {
		esc_html_e( 'Not assigned', 'gnn' );
	}
}
add_action( 'manage_gnn_layout_posts_custom_column', 'gnn_shared_column', 10, 2 );

/** Backfill legacy records in small resumable batches; do not scan the whole site per row. */
function gnn_usage_index_batch() {
	if ( ! current_user_can( 'manage_options' ) || get_option( 'gnn_wipe_job' ) ) {
		return; }
 // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- one bounded upgrade batch, completed records excluded.
	$query = new WP_Query(
		array(
			'post_type'      => array_values( array_diff( get_post_types( array( 'show_ui' => true ) ), array( 'attachment' ) ) ),
			'post_status'    => array( 'publish', 'draft', 'private', 'pending', 'future' ),
			'posts_per_page' => 100,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- bounded admin reference query.
			'meta_query'     => array(
				array(
					'key'     => '_gnn_shared_refs',
					'compare' => 'NOT EXISTS',
				),
			),
		)
	);
	foreach ( $query->posts as $id ) {
		gnn_shared_index_record( $id ); }
	update_option( 'gnn_usage_index_pending', 100 === count( $query->posts ), false );
}
add_action( 'admin_init', 'gnn_usage_index_batch', 25 );

/** Explain incomplete legacy indexing instead of claiming an empty usage inventory is authoritative. */
function gnn_usage_index_notice() {
	$screen = get_current_screen();
	if ( $screen && in_array( $screen->post_type, array( 'gnn_layout' ), true ) && get_option( 'gnn_usage_index_pending' ) ) {
		echo '<div class="notice notice-info"><p>' . esc_html__( 'Usage indexing is in progress. Reload to continue.', 'gnn' ) . '</p></div>';
	}
}
add_action( 'admin_notices', 'gnn_usage_index_notice' );

/** Paged usage inventory for shared records with many consumers. */
function gnn_usage_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'gnn' ), '', array( 'response' => 403 ) ); }
	$id   = isset( $_GET['ref'] ) && is_scalar( $_GET['ref'] ) ? absint( $_GET['ref'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only inventory.
	$page = isset( $_GET['paged'] ) && is_scalar( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only pagination.
	gnn_management_open( __( 'Used in', 'gnn' ) );
 // phpcs:ignore WordPress.DB.SlowDBQuery -- explicit paginated inventory, not a public visitor query.
	$query = new WP_Query(
		array(
			'post_type'      => array_values( array_diff( get_post_types( array( 'show_ui' => true ) ), array( 'attachment' ) ) ),
			'post_status'    => array( 'publish', 'draft', 'private', 'pending', 'future' ),
			'posts_per_page' => 20,
			'paged'          => $page,
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- paginated exact reference index.
			'meta_key'       => '_gnn_ref_' . $id,
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- paginated exact reference index.
			'meta_value'     => '1',
		)
	);
	echo '<section class="gnn-settings-card gnn-card-content">';
	foreach ( $query->posts as $record ) {
		if ( current_user_can( 'edit_post', $record->ID ) ) {
			echo '<p><a href="' . esc_url( get_edit_post_link( $record->ID ) ) . '">' . esc_html( $record->post_title ) . '</a></p>'; }
	}
	echo wp_kses_post(
		paginate_links(
			array(
				'base'    => add_query_arg( 'paged', '%#%' ),
				'current' => $page,
				'total'   => $query->max_num_pages,
			)
		)
	);
	echo '</section></div>';
}
