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
 * @param int     $id    Menu item ID.
 * @param WP_Post $item  Menu item.
 * @param int     $depth Menu depth.
 */
function gnn_menu_layout_fields( $id, $item, $depth ) {
	$classes = (array) get_post_meta( $id, '_menu_item_classes', true );
	$value   = get_post_meta( $id, '_gnn_menu_layout', true );
	if ( ! $value ) {
		$value = in_array( 'mega-menu-parent', $classes, true ) ? 'parent' : ( in_array( 'mega-featured', $classes, true ) ? 'featured' : '' );
	}
	$parent_classes = 1 === $depth ? (array) get_post_meta( (int) $item->menu_item_parent, '_menu_item_classes', true ) : array();
	$featured = 1 === $depth && in_array( 'mega-menu-parent', $parent_classes, true );
	$image = (int) get_post_meta( $id, '_gnn_menu_image', true );
	$src   = $image ? wp_get_attachment_image_url( $image, 'thumbnail' ) : '';
	?>
	<p class="description description-wide"<?php if ( 0 !== $depth && ! $featured ) : ?> hidden<?php endif; ?>>
		<label for="gnn-menu-layout-<?php echo esc_attr( $id ); ?>"><?php echo esc_html__( 'Menu layout', 'gnn' ); ?></label><br>
		<select data-gnn-menu-layout id="gnn-menu-layout-<?php echo esc_attr( $id ); ?>" name="gnn_menu_layout[<?php echo esc_attr( $id ); ?>]">
			<option value="" <?php selected( $value, '' ); ?>><?php echo esc_html__( 'Standard menu item', 'gnn' ); ?></option>
			<option value="parent" <?php selected( $value, 'parent' ); ?><?php if ( 0 !== $depth ) : ?> hidden<?php endif; ?>><?php echo esc_html__( 'Mega menu parent (top level)', 'gnn' ); ?></option>
			<option value="featured" <?php selected( $value, 'featured' ); ?><?php if ( ! $featured ) : ?> hidden<?php endif; ?>><?php echo esc_html__( 'Featured card (second level)', 'gnn' ); ?></option>
		</select>
	</p>
	<p class="description description-wide" data-gnn-featured-image<?php if ( ! $featured || 'featured' !== $value ) : ?> hidden<?php endif; ?>>
		<input type="hidden" name="gnn_menu_image[<?php echo esc_attr( $id ); ?>]" value="<?php echo $image ? esc_attr( $image ) : ''; ?>">
		<img width="72" height="72" alt=""<?php if ( $src ) : ?> src="<?php echo esc_url( $src ); ?>"<?php else : ?> hidden<?php endif; ?>>
		<button type="button" class="button" data-gnn-image-pick><?php echo esc_html__( 'Choose card image', 'gnn' ); ?></button>
		<button type="button" class="button" data-gnn-image-clear<?php if ( ! $image ) : ?> hidden<?php endif; ?>><?php echo esc_html__( 'Remove image', 'gnn' ); ?></button>
	</p>
	<?php
}
add_action( 'wp_nav_menu_item_custom_fields', 'gnn_menu_layout_fields', 10, 3 );

/** Load the core media picker on Appearance > Menus. */
function gnn_menu_layout_assets( $screen ) {
	if ( 'nav-menus.php' !== $screen || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script( 'gnn-header-menu-admin', get_template_directory_uri() . '/assets/js/header-menu-admin.js', array( 'media-editor' ), GNN_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'gnn_menu_layout_assets' );

/**
 * Save typed values under the native menu editor nonce and capability.
 *
 * @param int $menu Menu ID.
 * @param int $id Menu item ID.
 */
function gnn_save_menu_layout( $menu, $id ) {
	if ( ! current_user_can( 'edit_theme_options' ) || ! isset( $_POST['gnn_menu_layout'] ) || ! is_array( $_POST['gnn_menu_layout'] ) || ! isset( $_POST['gnn_menu_layout'][ $id ] ) ) {
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
	if ( 'featured' === $value && isset( $_POST['gnn_menu_image'] ) && is_array( $_POST['gnn_menu_image'] ) && isset( $_POST['gnn_menu_image'][ $id ] ) && is_scalar( $_POST['gnn_menu_image'][ $id ] ) ) {
		$image = absint( $_POST['gnn_menu_image'][ $id ] );
		if ( ! $image || wp_attachment_is_image( $image ) ) {
			update_post_meta( $id, '_gnn_menu_image', $image );
		}
	}
}
add_action( 'wp_update_nav_menu_item', 'gnn_save_menu_layout', 20, 2 );
