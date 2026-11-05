<?php
/**
 * Native menu controls; legacy CSS class assignments remain supported.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Show discoverable mega-menu controls in the native menu editor.
 *
 * @param int $id Menu item ID.
 */
function gnn_menu_layout_fields( $id ) {
	$classes = (array) get_post_meta( $id, '_menu_item_classes', true );
	$choices = array(
		''         => __( 'Standard menu item', 'gnn' ),
		'parent'   => __( 'Mega menu parent (top level)', 'gnn' ),
		'featured' => __( 'Featured card (second level)', 'gnn' ),
	);
	$value   = get_post_meta( $id, '_gnn_menu_layout', true );
	if ( ! $value ) {
		$value = in_array( 'mega-menu-parent', $classes, true ) ? 'parent' : ( in_array( 'mega-featured', $classes, true ) ? 'featured' : '' );
	}
	echo '<p class="description description-wide"><label for="gnn-menu-layout-' . esc_attr( $id ) . '">' . esc_html__( 'Menu layout', 'gnn' ) . '<br><select id="gnn-menu-layout-' . esc_attr( $id ) . '" name="gnn_menu_layout[' . esc_attr( $id ) . ']">';
	foreach ( $choices as $key => $label ) {
		echo '<option value="' . esc_attr( $key ) . '" ' . selected( $value, $key, false ) . '>' . esc_html( $label ) . '</option>';
	}
	echo '</select></label></p><p class="description description-wide"><label for="gnn-menu-image-' . esc_attr( $id ) . '">' . esc_html__( 'Featured card image attachment ID', 'gnn' ) . '<br><input type="number" min="0" id="gnn-menu-image-' . esc_attr( $id ) . '" name="gnn_menu_image[' . esc_attr( $id ) . ']" value="' . esc_attr( (int) get_post_meta( $id, '_gnn_menu_image', true ) ) . '"></label></p>';
}
add_action( 'wp_nav_menu_item_custom_fields', 'gnn_menu_layout_fields' );

/**
 * Save typed values under the native menu editor nonce and capability.
 *
 * @param int $menu Menu ID.
 * @param int $id Menu item ID.
 */
function gnn_save_menu_layout( $menu, $id ) {
	if ( ! current_user_can( 'edit_theme_options' ) || ! isset( $_POST['gnn_menu_layout'][ $id ] ) ) {
		return;
	}
	check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );
	$value = is_string( $_POST['gnn_menu_layout'][ $id ] ) ? sanitize_key( wp_unslash( $_POST['gnn_menu_layout'][ $id ] ) ) : '';
	if ( ! in_array( $value, array( '', 'parent', 'featured' ), true ) ) {
		return;
	}
	update_post_meta( $id, '_gnn_menu_layout', $value );
	$classes = (array) get_post_meta( $id, '_menu_item_classes', true );
	$classes = array_values( array_diff( $classes, array( 'mega-menu-parent', 'mega-featured' ) ) );
	if ( $value ) {
		$classes[] = 'parent' === $value ? 'mega-menu-parent' : 'mega-featured';
	}
	update_post_meta( $id, '_menu_item_classes', $classes );
	$image = isset( $_POST['gnn_menu_image'][ $id ] ) && is_scalar( $_POST['gnn_menu_image'][ $id ] ) ? absint( $_POST['gnn_menu_image'][ $id ] ) : 0;
	update_post_meta( $id, '_gnn_menu_image', wp_attachment_is_image( $image ) ? $image : 0 );
}
add_action( 'wp_update_nav_menu_item', 'gnn_save_menu_layout', 20, 2 );
