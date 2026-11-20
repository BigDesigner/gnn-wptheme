<?php
/**
 * Reusable native-block regions and predictable page-level overrides.
 *
 * @package GNN_Content
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
					'name'          => __( 'Reusable blocks', 'gnn-content' ),
					'singular_name' => __( 'Layout', 'gnn-content' ),
					'add_new_item'  => __( 'Add reusable block', 'gnn-content' ),
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
				'label'        => __( 'Slider groups', 'gnn-content' ),
				'public'       => false,
				'show_ui'      => true,
				'show_in_rest' => true,
				'hierarchical' => true,
			)
		);
	}
}
add_action( 'init', 'gnn_register_layout_content', 8 );

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

/**
 * Reusable blocks are rendered through their assigned regions; the REST collection is for editors only.
 *
 * @param mixed           $response Earlier short-circuit result.
 * @param array           $handler Matched route handler.
 * @param WP_REST_Request $request Current request.
 * @return mixed
 */
function gnn_layout_rest_access( $response, $handler, $request ) {
	$type  = get_post_type_object( 'gnn_layout' );
	$base  = '/' . ( $type && ! empty( $type->rest_namespace ) ? $type->rest_namespace : 'wp/v2' ) . '/' . ( $type && ! empty( $type->rest_base ) ? $type->rest_base : 'gnn_layout' );
	$route = untrailingslashit( (string) $request->get_route() );
	if ( $base !== $route && 0 !== strpos( $route, $base . '/' ) ) {
		return $response;
	}
	if ( ! is_wp_error( $response ) && ! current_user_can( 'edit_posts' ) ) {
		return new WP_Error( 'rest_forbidden', __( 'Sorry, you are not allowed to do that.', 'gnn-content' ), array( 'status' => rest_authorization_required_code() ) );
	}
	return $response;
}
add_filter( 'rest_request_before_callbacks', 'gnn_layout_rest_access', 10, 3 );

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
		return GNN_CONTENT_PATH . 'templates/layout-preview.php';
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
