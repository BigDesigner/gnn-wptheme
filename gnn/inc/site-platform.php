<?php
/**
 * Shared presentation settings and reusable content.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Additional typed settings retain all existing keys. */
function gnn_site_schema() {
	$schema = array( 'region_contract' => array( __( 'Layout settings version', 'gnn' ), 'number', 0, 'internal', 0, 1 ) );
	foreach ( gnn_site_contexts() as $context => $label ) {
		foreach ( gnn_site_regions() as $region => $name ) {
			$key = ( 'site' === $context ? '' : $context . '_' ) . $region . '_template';
			if ( in_array( $key, array( 'header_template', 'footer_template', 'post_header_template', 'post_footer_template', 'archive_header_template', 'archive_footer_template' ), true ) ) {
				continue;
			}
			$schema[ $key ] = array( $label . ' / ' . $name, 'region', -1, 'assignments' );
		}
	}
	foreach ( array(
		'page'        => __( 'Pages', 'gnn' ),
		'post'        => __( 'Posts', 'gnn' ),
		'gnn_project' => __( 'Projects', 'gnn' ),
	) as $type => $label ) {
		$schema[ 'search_exclude_' . $type ] = array( $label, 'bool', 0, 'search' );
	}
	$schema['site_frame']        = array(
		__( 'Site layout', 'gnn' ),
		'select',
		'full',
		'geometry',
		array(
			'full'  => __( 'Full width', 'gnn' ),
			'boxed' => __( 'Boxed', 'gnn' ),
		),
	);
	$schema['site_padding']      = array( __( 'Side padding', 'gnn' ), 'number', 24, 'geometry', 8, 100 );
	$schema['mobile_padding']    = array( __( 'Mobile padding', 'gnn' ), 'number', 20, 'geometry', 8, 48 );
	$schema['content_gap']       = array( __( 'Column gap', 'gnn' ), 'number', 32, 'geometry', 8, 120 );
	$schema['tablet_breakpoint'] = array( __( 'Tablet breakpoint', 'gnn' ), 'number', 1024, 'geometry', 800, 1280 );
	$schema['mobile_breakpoint'] = array( __( 'Mobile breakpoint', 'gnn' ), 'number', 767, 'geometry', 480, 799 );
	$schema['field_radius']      = array( __( 'Corner radius', 'gnn' ), 'number', 8, 'fields', 0, 64 );
	$schema['field_padding']     = array( __( 'Padding', 'gnn' ), 'number', 12, 'fields', 4, 32 );
	$schema['field_border']      = array( __( 'Border width', 'gnn' ), 'number', 1, 'fields', 0, 4 );
	$schema['field_size']        = array( __( 'Font size', 'gnn' ), 'number', 16, 'fields', 12, 24 );
	$schema['focus_width']       = array( __( 'Focus width', 'gnn' ), 'number', 2, 'fields', 2, 6 );
	foreach ( array_keys( gnn_typography_roles() ) as $role ) {
		foreach ( array(
			'size'   => __( 'Desktop size', 'gnn' ),
			'tablet' => __( 'Tablet size', 'gnn' ),
			'mobile' => __( 'Mobile size', 'gnn' ),
		) as $prop => $label ) {
			$schema[ 'type_' . $role . '_' . $prop ] = array( strtoupper( $role ) . ' / ' . $label, 'number', 0, 'type_scale', 0, 160 );
		}
		$schema[ 'type_' . $role . '_leading' ]   = array( strtoupper( $role ) . ' / ' . __( 'Line height (%)', 'gnn' ), 'number', 0, 'type_scale', 0, 250 );
		$schema[ 'type_' . $role . '_weight' ]    = array(
			strtoupper( $role ) . ' / ' . __( 'Weight', 'gnn' ),
			'select',
			'',
			'type_scale',
			array(
				''    => __( 'Inherit', 'gnn' ),
				'300' => '300',
				'400' => '400',
				'500' => '500',
				'600' => '600',
				'700' => '700',
				'800' => '800',
				'900' => '900',
			),
		);
		$schema[ 'type_' . $role . '_tracking' ]  = array( strtoupper( $role ) . ' / ' . __( 'Letter spacing (1/100 em)', 'gnn' ), 'optional_number', '', 'type_scale', -10, 50 );
		$schema[ 'type_' . $role . '_transform' ] = array(
			strtoupper( $role ) . ' / ' . __( 'Text case', 'gnn' ),
			'select',
			'',
			'type_scale',
			array(
				''          => __( 'Inherit', 'gnn' ),
				'none'      => __( 'Normal', 'gnn' ),
				'uppercase' => __( 'Uppercase', 'gnn' ),
				'lowercase' => __( 'Lowercase', 'gnn' ),
			),
		);
	}
	for ( $slot = 1; $slot <= 6; $slot++ ) {
		$schema[ 'btn_style_' . $slot . '_label' ] = array( __( 'Button style', 'gnn' ) . ' ' . $slot, 'text', '', 'button_names' );
		foreach ( array(
			'bg'         => __( 'Background', 'gnn' ),
			'text'       => __( 'Text', 'gnn' ),
			'border'     => __( 'Border', 'gnn' ),
			'hover_bg'   => __( 'Hover background', 'gnn' ),
			'hover_text' => __( 'Hover text', 'gnn' ),
		) as $part => $label ) {
			$schema[ 'btn_style_' . $slot . '_' . $part ] = array( $slot . ' / ' . $label, 'hex', '', 'button_names' );
		}
		$schema[ 'global_color_' . $slot . '_name' ] = array( __( 'Color name', 'gnn' ) . ' ' . $slot, 'text', '', 'global_colors' );
		$schema[ 'global_color_' . $slot ]           = array( __( 'Color', 'gnn' ) . ' ' . $slot, 'hex', '', 'global_colors' );
	}
	$schema['image_large_threshold'] = array( __( 'Large image threshold', 'gnn' ), 'number', 2560, 'images', 1024, 8192 );
	for ( $slot = 1; $slot <= 4; $slot++ ) {
		$schema[ 'image_' . $slot . '_width' ]  = array( __( 'Width', 'gnn' ) . ' ' . $slot, 'number', 0, 'images', 0, 4096 );
		$schema[ 'image_' . $slot . '_height' ] = array( __( 'Height', 'gnn' ) . ' ' . $slot, 'number', 0, 'images', 0, 4096 );
		$schema[ 'image_' . $slot . '_crop' ]   = array( __( 'Crop', 'gnn' ) . ' ' . $slot, 'bool', 0, 'images' );
	}
	return $schema;
}

/** Assignment contexts, from general to specific. */
function gnn_site_contexts() {
	$contexts = array(
		'site'     => __( 'Site', 'gnn' ),
		'page'     => __( 'Pages', 'gnn' ),
		'post'     => __( 'Posts', 'gnn' ),
		'project'  => __( 'Projects', 'gnn' ),
		'archive'  => __( 'Archives', 'gnn' ),
		'category' => __( 'Categories', 'gnn' ),
		'post_tag' => __( 'Tags', 'gnn' ),
		'author'   => __( 'Authors', 'gnn' ),
		'search'   => __( 'Search', 'gnn' ),
		'notfound' => __( '404 page', 'gnn' ),
	);
	foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $taxonomy ) {
		if ( ! isset( $contexts[ $taxonomy->name ] ) && 'post_format' !== $taxonomy->name ) {
			$contexts[ $taxonomy->name ] = $taxonomy->label;
		}
	}
	return $contexts;
}

/** Shared block regions. */
function gnn_site_regions() {
	return array(
		'header'   => __( 'Header', 'gnn' ),
		'titlebar' => __( 'Title bar', 'gnn' ),
		'sidebar'  => __( 'Sidebar', 'gnn' ),
		'footer'   => __( 'Footer', 'gnn' ),
		'content'  => __( 'Page template', 'gnn' ),
	);
}

/** Context hierarchy used by frontend and assignment controls. */
function gnn_site_context_chain() {
	$chain = array();
	if ( is_search() ) {
		$chain[] = 'search';
	} elseif ( is_404() ) {
		$chain[] = 'notfound';
	} elseif ( is_singular( 'gnn_project' ) ) {
		$chain[] = 'project';
	} elseif ( is_singular( 'post' ) ) {
		$chain[] = 'post';
	} elseif ( is_page() ) {
		$chain[] = 'page';
	} elseif ( is_archive() || is_home() ) {
		$object = get_queried_object();
		if ( $object instanceof WP_Term ) {
			$chain[] = $object->taxonomy;
		} elseif ( is_author() ) {
			$chain[] = 'author';
		}
		$chain[] = 'archive';
	}
	$chain[] = 'site';
	return $chain;
}

/** Resolve region plus its source; -1 inherits, zero explicitly uses theme markup.
 *
 * @param string $region Region identifier.
 */
function gnn_region_assignment( $region ) {
	$key  = $region . '_template';
	$meta = is_singular() ? (array) get_post_meta( get_queried_object_id(), '_gnn_layout', true ) : array();
	if ( isset( $meta[ $key ] ) && '' !== $meta[ $key ] && -1 !== (int) $meta[ $key ] && (int) $meta[ $key ] >= -2 ) {
		return array(
			'id'     => (int) $meta[ $key ],
			'source' => 'page',
		);
	}
	$stored = (array) get_option( 'gnn_options', array() );
	foreach ( gnn_site_context_chain() as $context ) {
		$option = ( 'site' === $context ? '' : $context . '_' ) . $key;
		// Legacy zero meant inheritance; new region controls retain an explicit zero.
		if ( isset( $stored[ $option ] ) && -1 !== (int) $stored[ $option ] && (int) $stored[ $option ] >= -2 ) {
			if ( 0 === (int) $stored[ $option ] && in_array( $option, array( 'post_header_template', 'post_footer_template', 'archive_header_template', 'archive_footer_template' ), true ) && empty( $stored['region_contract'] ) ) {
				continue;
			}
			return array(
				'id'     => (int) $stored[ $option ],
				'source' => $context,
			);
		}
	}
	return array(
		'id'     => 0,
		'source' => 'site',
	);
}

/** Render shared content without exposing drafts, password content or recursive references.
 *
 * @param int $id Shared record identifier.
 * @return string
 */
function gnn_shared_content( $id ) {
	if ( (int) $id <= 0 ) {
		return ''; }
	static $stack = array();
	$id           = gnn_translated_post_id( absint( $id ) );
	$record       = get_post( $id );
	if ( ! $record || ! in_array( $record->post_type, array( 'gnn_layout', 'elementor_library' ), true ) || 'publish' !== $record->post_status || $record->post_password || isset( $stack[ $id ] ) || count( $stack ) >= 8 ) {
		return '';
	}
	$stack[ $id ] = true;
	try {
		if ( 'builder' === get_post_meta( $id, '_elementor_edit_mode', true ) && class_exists( '\Elementor\Plugin' ) ) {
			ob_start();
			try {
				$content = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $id, true );
				return ob_get_contents() . $content;
			} finally {
				ob_end_clean(); }
		}
		return do_shortcode( do_blocks( $record->post_content ) );
	} finally {
		unset( $stack[ $id ] );
	}
}

/** Shared content shortcode can be used in native blocks or builder text.
 *
 * @param array $attrs Attributes.
 */
function gnn_shared_shortcode( $attrs ) {
	return gnn_shared_content( absint( $attrs['id'] ?? 0 ) );
}
add_shortcode( 'gnn_block', 'gnn_shared_shortcode' );

/** Enable Elementor Free on shared regions without duplicating content. */
function gnn_shared_editor_support() {
	add_post_type_support( 'gnn_layout', 'elementor' );
	$types = get_option( 'elementor_cpt_support', array( 'page', 'post' ) );
	if ( is_array( $types ) && ! in_array( 'gnn_layout', $types, true ) ) {
		$types[] = 'gnn_layout';
		update_option( 'elementor_cpt_support', $types );
	}
}
add_action( 'init', 'gnn_shared_editor_support', 30 );

/** Use an assigned shared body while leaving the content loop intact.
 *
 * @param string $content Current content.
 */
function gnn_shared_body( $content ) {
	static $rendering = false;
	if ( $rendering || is_admin() || ! is_main_query() || ! in_the_loop() || ! is_singular() || is_singular( 'gnn_layout' ) || post_password_required() ) {
		return $content;
	}
	$assignment = gnn_region_assignment( 'content' );
	if ( $assignment['id'] <= 0 || ! gnn_block_matches_region( $assignment['id'], 'content' ) ) {
		return $content;
	}
	$rendering = true;
	try {
		$template = gnn_shared_content( $assignment['id'] );
		return $template ? $template : $content;
	} finally {
		$rendering = false;
	}
}
add_filter( 'the_content', 'gnn_shared_body', 8 );

/** Register extra sizes only for future media; original files are untouched. */
function gnn_extra_image_sizes() {
	for ( $slot = 1; $slot <= 4; $slot++ ) {
		$width  = (int) gnn_option( 'image_' . $slot . '_width' );
		$height = (int) gnn_option( 'image_' . $slot . '_height' );
		if ( $width || $height ) {
			add_image_size( 'gnn-custom-' . $slot, $width, $height, (bool) gnn_option( 'image_' . $slot . '_crop' ) );
		}
	}
}
add_action( 'after_setup_theme', 'gnn_extra_image_sizes', 30 );
add_filter(
	'big_image_size_threshold',
	function () {
		return (int) gnn_option( 'image_large_threshold' );
	}
);

/** Typed geometry, role typography, color tokens and form states. */
function gnn_site_styles() {
	$css = ':root{--gnn-column-gap:' . (int) gnn_option( 'content_gap' ) . 'px;}';
	if ( 'boxed' === gnn_option( 'site_frame' ) ) {
		$css .= '#page{max-width:' . (int) gnn_option( 'container_width' ) . 'px;margin-inline:auto;}';
	}
	$css .= '.gnn-container{padding-inline:' . (int) gnn_option( 'site_padding' ) . 'px;}';
	$css .= '@media(max-width:' . (int) gnn_option( 'mobile_breakpoint' ) . 'px){.gnn-container{padding-inline:' . (int) gnn_option( 'mobile_padding' ) . 'px;}}';
	for ( $slot = 1; $slot <= 6; $slot++ ) {
		$color = sanitize_hex_color( gnn_option( 'global_color_' . $slot ) );
		if ( $color ) {
			$css .= ':root{--gnn-global-' . $slot . ':' . $color . ';}.gnn-color-' . $slot . '{color:var(--gnn-global-' . $slot . ');}';
		}
	}
	foreach ( array_keys( gnn_typography_roles() ) as $role ) {
		$selector = 'p' === $role ? '.entry-content p,.elementor-widget-text-editor' : $role . ',.elementor-widget-heading ' . $role . '.elementor-heading-title';
		$rules    = '';
		$base     = 'type_' . $role . '_';
		if ( gnn_option( $base . 'size' ) ) {
			$rules .= 'font-size:' . (int) gnn_option( $base . 'size' ) . 'px;';
		}
		if ( gnn_option( $base . 'leading' ) ) {
			$rules .= 'line-height:' . ( (int) gnn_option( $base . 'leading' ) / 100 ) . ';';
		}
		if ( gnn_option( $base . 'weight' ) ) {
			$rules .= 'font-weight:' . (int) gnn_option( $base . 'weight' ) . ';';
		}
		if ( '' !== gnn_option( $base . 'tracking' ) ) {
			$rules .= 'letter-spacing:' . ( (int) gnn_option( $base . 'tracking' ) / 100 ) . 'em;';
		}
		$transform = gnn_option( $base . 'transform' );
		if ( in_array( $transform, array( 'none', 'uppercase', 'lowercase' ), true ) ) {
			$rules .= 'text-transform:' . $transform . ';';
		}
		$css .= $selector . '{' . $rules . '}';
		foreach ( array(
			'tablet' => 'tablet_breakpoint',
			'mobile' => 'mobile_breakpoint',
		) as $device => $breakpoint ) {
			if ( gnn_option( $base . $device ) ) {
				$css .= '@media(max-width:' . (int) gnn_option( $breakpoint ) . 'px){' . $selector . '{font-size:' . (int) gnn_option( $base . $device ) . 'px;}}';
			}
		}
	}
	$css .= '.entry-content input:not([type=checkbox]):not([type=radio]),.entry-content select,.entry-content textarea,.woocommerce input.input-text,.woocommerce select{border-radius:' . (int) gnn_option( 'field_radius' ) . 'px;padding:' . (int) gnn_option( 'field_padding' ) . 'px;border-width:' . (int) gnn_option( 'field_border' ) . 'px;font-size:' . (int) gnn_option( 'field_size' ) . 'px;background:var(--bg2);color:var(--fg);border-color:var(--line);}';
	$css .= ':where(a,button,input,select,textarea):focus-visible{outline:' . (int) gnn_option( 'focus_width' ) . 'px solid var(--accent);outline-offset:3px;}input:disabled,select:disabled,textarea:disabled{opacity:.6;}input[aria-invalid=true],textarea[aria-invalid=true]{border-color:#c93535;}';
	wp_add_inline_style( 'gnn-main', $css );
}
add_action( 'wp_enqueue_scripts', 'gnn_site_styles', 35 );

/** Keep visitor requests out of a partially reset site. */
function gnn_reset_visitor_gate() {
	if ( get_option( 'gnn_wipe_job' ) && ! current_user_can( 'manage_options' ) ) {
		status_header( 503 );
		nocache_headers();
		header( 'Retry-After: 300' );
		wp_die( esc_html__( 'Maintenance in progress.', 'gnn' ), '', array( 'response' => 503 ) );
	}
}
add_action( 'template_redirect', 'gnn_reset_visitor_gate', 0 );

/** Pause API mutations while the database is being reset.
 *
 * @param mixed $result Existing REST result.
 */
function gnn_reset_rest_gate( $result ) {
	return get_option( 'gnn_wipe_job' ) ? new WP_Error( 'gnn_reset_busy', __( 'Maintenance in progress.', 'gnn' ), array( 'status' => 503 ) ) : $result;
}
add_filter( 'rest_pre_dispatch', 'gnn_reset_rest_gate' );

/** Use a shared body template for archives/search/404 as well as singular content.
 *
 * @param string $template Selected theme template.
 */
function gnn_shared_template_route( $template ) {
	if ( is_singular() && post_password_required() ) {
		return $template; }
	$assignment = gnn_region_assignment( 'content' );
	if ( $assignment['id'] <= 0 ) {
		return $template; }
	$record = get_post( $assignment['id'] );
	return $record && 'gnn_layout' === $record->post_type && 'publish' === $record->post_status && ! $record->post_password && ( trim( $record->post_content ) || get_post_meta( $record->ID, '_elementor_data', true ) ) ? get_template_directory() . '/page-templates/tpl-shared.php' : $template;
}
add_filter( 'template_include', 'gnn_shared_template_route', 40 );

/** Search exclusions affect only the public main search query.
 *
 * @param WP_Query $query Main query.
 */
function gnn_search_types( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
		return; }
	$types = get_post_types( array( 'exclude_from_search' => false ) );
	foreach ( array( 'page', 'post', 'gnn_project' ) as $type ) {
		if ( gnn_option( 'search_exclude_' . $type ) ) {
			unset( $types[ $type ] ); }
	}
	$query->set( 'post_type', $types ? array_values( $types ) : array( 'gnn_no_search_results' ) );
}
add_action( 'pre_get_posts', 'gnn_search_types' );
