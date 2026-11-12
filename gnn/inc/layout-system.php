<?php
/**
 * Reusable native-block regions and predictable page-level overrides.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

/** Published regions are editable through the native block editor. */
function gnn_register_layout_content() {
	if ( ! post_type_exists( 'gnn_layout' ) ) {
		register_post_type(
			'gnn_layout',
			array(
				'labels'          => array(
					'name'          => __( 'Reusable blocks', 'gnn' ),
					'singular_name' => __( 'Layout', 'gnn' ),
					'add_new_item'  => __( 'Add reusable block', 'gnn' ),
				),
				'public'          => false,
				'show_ui'         => true,
				'show_in_rest'    => true,
				'show_in_menu'    => 'gnn-panel',
				'supports'        => array( 'title', 'editor', 'revisions' ),
				'capability_type' => array( 'gnn_layout', 'gnn_layouts' ),
				'map_meta_cap'    => true,
			)
		);
	}

	if ( ! taxonomy_exists( 'gnn_slide_group' ) ) {
		register_taxonomy(
			'gnn_slide_group',
			'gnn_slide',
			array(
				'label'        => __( 'Slider groups', 'gnn' ),
				'public'       => false,
				'show_ui'      => true,
				'show_in_rest' => true,
				'hierarchical' => true,
			)
		);
	}
}
add_action( 'init', 'gnn_register_layout_content' );

/**
 * Allow builder preview queries while keeping shared records out of public browsing.
 *
 * @param array  $args Registration arguments.
 * @param string $type Post type.
 */
function gnn_layout_preview_args( $args, $type ) {
	if ( 'gnn_layout' === $type ) {
		$args['publicly_queryable']  = true;
		$args['exclude_from_search'] = true;
		$args['rewrite']             = false;
	}
	return $args;
}
add_filter( 'register_post_type_args', 'gnn_layout_preview_args', 10, 2 );

/** Only an editor of the shared record may open its standalone preview. */
function gnn_layout_preview_access() {
	if ( is_singular( 'gnn_layout' ) && ! current_user_can( 'edit_post', get_queried_object_id() ) ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();
	}
}
add_action( 'template_redirect', 'gnn_layout_preview_access', 0 );

/**
 * A shared region preview must not embed itself through the site's assigned footer.
 *
 * @param string $template Selected template.
 */
function gnn_layout_preview_template( $template ) {
	if ( is_singular( 'gnn_layout' ) && current_user_can( 'edit_post', get_queried_object_id() ) ) {
		return get_template_directory() . '/page-templates/layout-preview.php';
	}
	return $template;
}
add_filter( 'template_include', 'gnn_layout_preview_template', 99 );

/**
 * Grant layout editing to administrators/designers, never just because a user can edit pages.
 *
 * @param array $allcaps Effective user capabilities.
 */
function gnn_layout_capabilities( $allcaps ) {
	if ( ! empty( $allcaps['manage_options'] ) || ! empty( $allcaps['gnn_manage_design'] ) ) {
		foreach ( array( 'edit_gnn_layouts', 'edit_others_gnn_layouts', 'edit_published_gnn_layouts', 'edit_private_gnn_layouts', 'publish_gnn_layouts', 'read_private_gnn_layouts', 'delete_gnn_layouts', 'delete_others_gnn_layouts', 'delete_published_gnn_layouts', 'delete_private_gnn_layouts' ) as $cap ) {
			$allcaps[ $cap ] = true; }
	}
	return $allcaps;
}
add_filter( 'user_has_cap', 'gnn_layout_capabilities' );

/** Supported override keys have the same type/bounds as the global schema. */
function gnn_layout_override_keys() {
	return array( 'header_template', 'footer_template', 'titlebar_template', 'sidebar_template', 'content_template', 'header_preset', 'hero_style', 'hero_group', 'content_top_padding', 'content_bottom_padding', 'post_content_width', 'post_image_layout', 'post_image_width', 'post_image_height_mode', 'post_image_ratio', 'post_featured_image_height', 'post_featured_image_fit', 'post_featured_image_position' );
}

/**
 * Explicit page choice > content context > site default.
 *
 * @param string $key Option key.
 */
function gnn_layout_value( $key ) {
	if ( in_array( $key, array( 'header_template', 'footer_template', 'titlebar_template', 'sidebar_template', 'content_template' ), true ) ) {
		$assignment = gnn_region_assignment( str_replace( '_template', '', $key ) );
		return $assignment['id'];
	}
	$id   = is_singular() ? get_queried_object_id() : 0;
	$meta = $id ? (array) get_post_meta( $id, '_gnn_layout', true ) : array();
	if ( isset( $meta[ $key ] ) && '' !== $meta[ $key ] ) {
		return $meta[ $key ]; }
	if ( in_array( $key, array( 'header_template', 'footer_template' ), true ) ) {
		$context = is_archive() || is_home() || is_search() ? 'archive_' : ( is_singular( 'post' ) ? 'post_' : '' );
		$value   = $context ? gnn_option( $context . $key ) : 0;
		if ( $value ) {
			return $value; }
	}
	return gnn_option( $key );
}

/**
 * Render only published, non-password-protected layout content; guard recursion.
 *
 * @param string $region Header or footer region.
 */
function gnn_render_layout_region( $region ) {
	static $rendering = false;
	if ( $rendering || ! isset( gnn_site_regions()[ $region ] ) ) {
		return false; }
	if ( -2 === (int) gnn_layout_value( $region . '_template' ) ) {
		return true; }
	$id = absint( apply_filters( 'gnn_layout_region_id', gnn_layout_value( $region . '_template' ), $region ) );
	if ( ! $id ) {
		return false; }
	$post = get_post( gnn_translated_post_id( $id ) );
	if ( ! $post || 'gnn_layout' !== $post->post_type || 'publish' !== $post->post_status || '' !== $post->post_password || ! gnn_block_matches_region( $id, $region ) ) {
		return false; }
	$rendering = true;
	$content   = gnn_shared_content( $id );
	$rendering = false;
	if ( '' === trim( $content ) ) {
		return false;
	}
	$tag = in_array( $region, array( 'header', 'footer' ), true ) ? $region : 'div';
	echo '<' . esc_html( $tag ) . ' class="gnn-layout-region gnn-layout-region--' . esc_attr( $region ) . '">' . $content . '</' . esc_html( $tag ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput -- native rendered blocks, authored under layout capabilities.
	return true;
}

/** Page controls do not require Elementor Pro. */
function gnn_layout_meta_box() {
	add_meta_box( 'gnn-layout', __( 'Site layout overrides', 'gnn' ), 'gnn_render_layout_meta_box', array( 'page', 'post', 'gnn_project', 'gnn_product' ), 'side' );
}
add_action( 'add_meta_boxes', 'gnn_layout_meta_box' );

/**
 * Accessible inheritance controls with explicit zero-template fallback.
 *
 * @param WP_Post $post Current content record.
 */
function gnn_render_layout_meta_box( $post ) {
	wp_nonce_field( 'gnn_layout', 'gnn_layout_nonce' );
	$meta = (array) get_post_meta( $post->ID, '_gnn_layout', true );
	foreach ( gnn_layout_override_keys() as $key ) {
		if ( 0 === strpos( $key, 'post_' ) && 'post' !== $post->post_type ) {
			continue; }
		$schema = gnn_platform_schema();
		$field  = $schema[ $key ] ?? array( ucwords( str_replace( '_', ' ', $key ) ), 'number', '', 'pages', 0, 300 );
		echo '<p><label for="gnn-layout-' . esc_attr( $key ) . '">' . esc_html( $field[0] ) . '</label>';
		if ( in_array( $field[1], array( 'layout', 'region', 'select' ), true ) ) {
			$choices = array( '' => __( 'Inherit', 'gnn' ) );
			if ( in_array( $field[1], array( 'layout', 'region' ), true ) ) {
				$choices[0]  = __( 'Native theme / builder fallback', 'gnn' );
				$choices[-2] = __( 'Hide', 'gnn' );
				foreach ( get_posts(
					array(
						'post_type'   => 'gnn_layout',
						'numberposts' => 100,
					)
				) as $layout ) {
					$choices[ $layout->ID ] = $layout->post_title; }
			} else {
				$choices += $field[4]; }
			echo '<select id="gnn-layout-' . esc_attr( $key ) . '" name="gnn_layout[' . esc_attr( $key ) . ']">';
			foreach ( $choices as $value => $label ) {
				echo '<option value="' . esc_attr( $value ) . '" ' . selected( (string) ( $meta[ $key ] ?? '' ), (string) $value, false ) . '>' . esc_html( $label ) . '</option>'; }
			echo '</select>';
		} else {
			echo '<input type="number" min="' . esc_attr( $field[4] ?? 0 ) . '" max="' . esc_attr( $field[5] ?? 300 ) . '" id="gnn-layout-' . esc_attr( $key ) . '" name="gnn_layout[' . esc_attr( $key ) . ']" placeholder="' . esc_attr__( 'Inherit', 'gnn' ) . '" value="' . esc_attr( $meta[ $key ] ?? '' ) . '">'; }
		echo '</p>';
	}
}

/**
 * Meta writes require both a nonce and permission on the actual post.
 *
 * @param int|string $id Record identifier.
 */
function gnn_save_layout_meta( $id ) {
	if ( wp_is_post_revision( $id ) || wp_is_post_autosave( $id ) || ! current_user_can( 'edit_post', $id ) || ! isset( $_POST['gnn_layout_nonce'] ) || ! is_string( $_POST['gnn_layout_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['gnn_layout_nonce'] ) ), 'gnn_layout' ) ) {
		return; }
	$input  = isset( $_POST['gnn_layout'] ) && is_array( $_POST['gnn_layout'] ) ? wp_unslash( $_POST['gnn_layout'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- per-field validation below.
	$valid  = array();
	$schema = gnn_platform_schema();
	foreach ( gnn_layout_override_keys() as $key ) {
		if ( 0 === strpos( $key, 'post_' ) && 'post' !== get_post_type( $id ) ) {
			continue; }
		if ( ! isset( $input[ $key ] ) || ! is_scalar( $input[ $key ] ) || '' === $input[ $key ] ) {
			continue; }
		if ( isset( $schema[ $key ] ) && 'select' === $schema[ $key ][1] ) {
			if ( isset( $schema[ $key ][4][ $input[ $key ] ] ) ) {
				$valid[ $key ] = $input[ $key ]; }
		} else {
			$value = false !== strpos( $key, '_template' ) ? max( -2, (int) $input[ $key ] ) : absint( $input[ $key ] );
			if ( false !== strpos( $key, '_template' ) && $value > 0 && 'gnn_layout' !== get_post_type( $value ) ) {
				continue; }
			$valid[ $key ] = isset( $schema[ $key ] ) && 'number' === $schema[ $key ][1] ? max( $schema[ $key ][4], min( $schema[ $key ][5], $value ) ) : $value;
		}
	}
	update_post_meta( $id, '_gnn_layout', $valid );
}
add_action( 'save_post', 'gnn_save_layout_meta' );

/** Header contact, action and language providers use escaped values. */
function gnn_header_components() {
	if ( gnn_option( 'header_contact' ) ) {
		echo '<div class="header-contact">';
		if ( gnn_option( 'company_phone' ) ) {
			echo '<a href="tel:' . esc_attr( preg_replace( '/[^0-9+]/', '', gnn_option( 'company_phone' ) ) ) . '">' . esc_html( gnn_translated_option( 'company_phone' ) ) . '</a>'; }
		if ( gnn_option( 'company_email' ) ) {
			echo '<a href="mailto:' . esc_attr( gnn_option( 'company_email' ) ) . '">' . esc_html( gnn_translated_option( 'company_email' ) ) . '</a>'; }
		echo '</div>';
	}
	if ( gnn_option( 'header_language' ) ) {
		if ( function_exists( 'pll_the_languages' ) ) {
			echo '<ul class="gnn-languages">';
			pll_the_languages(
				array(
					'show_flags' => 0,
					'show_names' => 1,
				)
			);
			echo '</ul>'; } elseif ( has_action( 'wpml_add_language_selector' ) ) {
			do_action( 'wpml_add_language_selector' ); }
	}
	if ( gnn_option( 'header_cta_label' ) && gnn_option( 'header_cta_url' ) ) {
		echo '<a class="gnn-btn header-cta" href="' . esc_url( gnn_option( 'header_cta_url' ) ) . '">' . esc_html( gnn_translated_option( 'header_cta_label' ) ) . '</a>'; }
}
