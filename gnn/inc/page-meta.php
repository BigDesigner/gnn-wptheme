<?php
/**
 * Per-page display options: hide the page title and/or the breadcrumb
 * ("Home / Current"), and override the title-overlay-on-featured-image
 * setting. Shown as a meta box on the page/post editor.
 *
 * Stored as post meta:
 *   _gnn_hide_title       '1' to hide the entry title.
 *   _gnn_hide_breadcrumb  '1' to hide the breadcrumb navigation.
 *   _gnn_title_overlay    '' theme default, '1' force on, '0' force off.
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
	register_post_meta(
		'',
		'_gnn_title_overlay',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'gnn_sanitize_tristate_meta',
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
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
 * Sanitize a "Theme default / Force on / Force off" meta value.
 *
 * @param mixed $value Raw value.
 * @return string '', '1', or '0'.
 */
function gnn_sanitize_tristate_meta( $value ) {
	$value = (string) $value;
	return in_array( $value, array( '1', '0' ), true ) ? $value : '';
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
	$overlay    = get_post_meta( $post->ID, '_gnn_title_overlay', true );
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
	<p>
		<label for="gnn_title_overlay"><?php esc_html_e( 'Title over featured image', 'gnn' ); ?></label><br>
		<select id="gnn_title_overlay" name="gnn_title_overlay" style="width:100%;">
			<?php
			$gnn_overlay_default_state = gnn_option( 'title_overlay_enable' ) ? __( 'on', 'gnn' ) : __( 'off', 'gnn' );
			$gnn_overlay_choices       = array(
				/* translators: %s: "on" or "off", the current theme-wide default. */
				'' => sprintf( __( 'Theme default (currently %s)', 'gnn' ), $gnn_overlay_default_state ),
				'1' => __( 'Force on for this page', 'gnn' ),
				'0' => __( 'Force off for this page', 'gnn' ),
			);
			foreach ( $gnn_overlay_choices as $gnn_overlay_value => $gnn_overlay_label ) {
				printf(
					'<option value="%1$s" %2$s>%3$s</option>',
					esc_attr( $gnn_overlay_value ),
					selected( $overlay, $gnn_overlay_value, false ),
					esc_html( $gnn_overlay_label )
				);
			}
			?>
		</select>
		<span class="description"><?php esc_html_e( 'When on, the title renders centered inside the featured image instead of above the content. Set the look (size/background) in GNN Panel → Pages Layout.', 'gnn' ); ?></span>
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
	if ( isset( $_POST['gnn_title_overlay'] ) ) {
		update_post_meta( $post_id, '_gnn_title_overlay', gnn_sanitize_tristate_meta( wp_unslash( $_POST['gnn_title_overlay'] ) ) );
	}
}
add_action( 'save_post', 'gnn_save_page_meta' );

/**
 * Whether this entry's title has been explicitly hidden: the "Hide the
 * page title" checkbox, or Elementor's own Document Settings → Hide Title
 * toggle (`_elementor_page_settings['hide_title']`). This is the hard
 * override both the normal in-flow title AND the featured-image overlay
 * must respect — hiding the title means hiding it everywhere, not just
 * repositioning it.
 *
 * @return bool
 */
function gnn_title_explicitly_hidden() {
	if ( ! is_singular() ) {
		return false;
	}
	$post_id = get_the_ID();
	if ( '1' === get_post_meta( $post_id, '_gnn_hide_title', true ) ) {
		return true;
	}
	$elementor_settings = get_post_meta( $post_id, '_elementor_page_settings', true );
	return is_array( $elementor_settings ) && ! empty( $elementor_settings['hide_title'] ) && 'yes' === $elementor_settings['hide_title'];
}

/**
 * Whether the current singular entry renders its title centered inside the
 * featured image instead of in the normal content flow. Never true when
 * the title is explicitly hidden (see gnn_title_explicitly_hidden()) —
 * that always wins. Otherwise the per-page override (`_gnn_title_overlay`:
 * '' = theme default, '1' = force on, '0' = force off) wins over the
 * theme-wide `title_overlay_enable` panel setting.
 *
 * @return bool
 */
function gnn_title_overlay_active() {
	if ( ! is_singular() || gnn_title_explicitly_hidden() ) {
		return false;
	}
	// The overlay needs a featured image to sit on. Without one there is
	// nothing to render it into — and since an active overlay suppresses the
	// normal in-flow title, saying "yes" here would leave the entry with no
	// title at all. These are the same guards the two renderers use
	// (gnn_page_featured_image() and gnn_post_thumbnail()).
	if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
		return false;
	}
	$override = get_post_meta( get_the_ID(), '_gnn_title_overlay', true );
	if ( '1' === $override ) {
		return true;
	}
	if ( '0' === $override ) {
		return false;
	}
	return (bool) gnn_option( 'title_overlay_enable' );
}

/**
 * Ready-to-echo markup for the title overlay, or '' when it isn't active.
 *
 * Shared by both featured-image renderers — gnn_page_featured_image()
 * (page templates) and gnn_post_thumbnail() (single posts) — so the
 * overlay can never be suppressed-but-not-rendered in one of them.
 *
 * @return string Escaped HTML, or '' when the overlay is off.
 */
function gnn_title_overlay_html() {
	if ( ! gnn_title_overlay_active() ) {
		return '';
	}
	$size    = max( 16, min( 120, (int) gnn_option( 'title_overlay_font_size' ) ) );
	$bg      = sanitize_hex_color( (string) gnn_option( 'title_overlay_bg' ) );
	$bg      = $bg ? $bg : '#000000';
	$opacity = max( 0, min( 100, (int) gnn_option( 'title_overlay_bg_opacity' ) ) );

	return sprintf(
		'<div class="gnn-title-overlay"><h1 class="entry-title gnn-title-overlay__title" style="font-size:%1$dpx;background:color-mix(in srgb, %2$s %3$d%%, transparent);">%4$s</h1></div>',
		$size,
		esc_attr( $bg ),
		$opacity,
		esc_html( get_the_title() )
	);
}

/**
 * Whether the current singular entry hides its title from the normal
 * in-flow position above the content — either because it's explicitly
 * hidden everywhere, or because the featured-image overlay is showing it
 * there instead.
 *
 * @return bool
 */
function gnn_hide_title() {
	return gnn_title_explicitly_hidden() || gnn_title_overlay_active();
}

/**
 * Whether the current singular entry hides its breadcrumb.
 *
 * Also honors the custom "Hide Breadcrumb" toggle this theme adds to
 * Elementor's own Page Settings panel (see gnn_elementor_register_
 * breadcrumb_control() in inc/elementor.php), stored the same way
 * Elementor stores its native settings.
 *
 * @return bool
 */
function gnn_hide_breadcrumb() {
	if ( ! is_singular() ) {
		return false;
	}
	$post_id = get_the_ID();
	if ( '1' === get_post_meta( $post_id, '_gnn_hide_breadcrumb', true ) ) {
		return true;
	}
	$elementor_settings = get_post_meta( $post_id, '_elementor_page_settings', true );
	return is_array( $elementor_settings ) && ! empty( $elementor_settings['gnn_hide_breadcrumb'] ) && 'yes' === $elementor_settings['gnn_hide_breadcrumb'];
}
