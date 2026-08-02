<?php
/**
 * GNN Slider — Custom Post Type (unlimited slides, managed like Posts/Pages).
 *
 * Each slide is a `gnn_slide` post: title = slide title, featured image =
 * 1x background, meta box holds the 2x retina image, kicker, text, CTA and
 * image fit mode. Order follows the CPT's menu_order (drag in the admin
 * list, or set "Order" on each slide).
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the gnn_slide CPT. Menu stays "GNN Slider" for continuity.
 */
function gnn_register_slide_cpt() {
	register_post_type(
		'gnn_slide',
		array(
			'labels'              => array(
				'name'               => __( 'GNN Slider', 'gnn' ),
				'singular_name'      => __( 'Slide', 'gnn' ),
				'add_new'            => __( 'Add Slide', 'gnn' ),
				'add_new_item'       => __( 'Add New Slide', 'gnn' ),
				'edit_item'          => __( 'Edit Slide', 'gnn' ),
				'all_items'          => __( 'All Slides', 'gnn' ),
				'menu_name'          => __( 'GNN Slider', 'gnn' ),
				'not_found'          => __( 'No slides yet.', 'gnn' ),
				'featured_image'     => __( 'Background image (1×)', 'gnn' ),
				'set_featured_image' => __( 'Set background image', 'gnn' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_icon'           => 'dashicons-images-alt2',
			'menu_position'       => 60,
			'supports'            => array( 'title', 'thumbnail', 'page-attributes' ),
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'show_in_rest'        => true,
			'capability_type'     => 'page',
		)
	);
}
add_action( 'init', 'gnn_register_slide_cpt' );

/**
 * Slide meta fields: key => sanitize callback.
 *
 * @return array<string,callable>
 */
function gnn_slide_meta_fields() {
	return array(
		'_gnn_slide_image_2x'  => 'absint',
		'_gnn_slide_kicker'    => 'sanitize_text_field',
		'_gnn_slide_text'      => 'sanitize_textarea_field',
		'_gnn_slide_cta_label' => 'sanitize_text_field',
		'_gnn_slide_cta_url'   => 'esc_url_raw',
		'_gnn_slide_fit'       => 'gnn_sanitize_slide_fit',
	);
}

/**
 * Sanitize the image-fit choice.
 *
 * @param string $value Raw value.
 * @return string
 */
function gnn_sanitize_slide_fit( $value ) {
	$allowed = array( 'cover', 'contain', 'top', 'bottom', 'center' );
	return in_array( $value, $allowed, true ) ? $value : 'cover';
}

/**
 * Register slide meta for REST + revisions.
 */
function gnn_register_slide_meta() {
	foreach ( gnn_slide_meta_fields() as $key => $cb ) {
		register_post_meta(
			'gnn_slide',
			$key,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => $cb,
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}
add_action( 'init', 'gnn_register_slide_meta' );

/**
 * Slide details meta box.
 */
function gnn_add_slide_meta_box() {
	add_meta_box( 'gnn_slide_details', __( 'Slide Details', 'gnn' ), 'gnn_render_slide_meta_box', 'gnn_slide', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'gnn_add_slide_meta_box' );

/**
 * Render the slide details meta box.
 *
 * @param WP_Post $post Current slide post.
 */
function gnn_render_slide_meta_box( $post ) {
	wp_nonce_field( 'gnn_slide_meta', 'gnn_slide_meta_nonce' );
	?>
	<div class="notice notice-warning inline" style="margin:0 0 16px;">
		<p>
			<strong><?php esc_html_e( 'Gutenberg only.', 'gnn' ); ?></strong>
			<?php esc_html_e( 'This slider renders on the default (Gutenberg-driven) front page template only. If the front page has been built or replaced with Elementor — including the "GNN Ana Sayfa" Elementor template — the slider will not appear there, since Elementor content takes over the entire front page.', 'gnn' ); ?>
		</p>
	</div>
	<?php
	$image_2x = (int) get_post_meta( $post->ID, '_gnn_slide_image_2x', true );
	$kicker   = (string) get_post_meta( $post->ID, '_gnn_slide_kicker', true );
	$text     = (string) get_post_meta( $post->ID, '_gnn_slide_text', true );
	$cta_l    = (string) get_post_meta( $post->ID, '_gnn_slide_cta_label', true );
	$cta_u    = (string) get_post_meta( $post->ID, '_gnn_slide_cta_url', true );
	$fit      = (string) get_post_meta( $post->ID, '_gnn_slide_fit', true );
	$fit      = $fit ? $fit : 'cover';
	$img_html = $image_2x ? wp_get_attachment_image( $image_2x, 'medium', false, array( 'class' => 'gnn-media-preview' ) ) : '';
	?>
	<p class="description"><?php esc_html_e( 'Set the standard background via the Featured Image box. The 2× image below is optional (used on Retina/high-DPI screens).', 'gnn' ); ?></p>

	<div class="gnn-media gnn-field">
		<span class="gnn-field__label"><?php esc_html_e( 'Background image (2× retina)', 'gnn' ); ?></span>
		<div class="gnn-media-box"><?php echo $img_html; // phpcs:ignore WordPress.Security.EscapeOutput -- wp_get_attachment_image(). ?></div>
		<input type="hidden" class="gnn-media-input" name="gnn_slide_image_2x" value="<?php echo esc_attr( $image_2x ); ?>">
		<button type="button" class="button gnn-media-pick"><?php esc_html_e( 'Select image', 'gnn' ); ?></button>
		<button type="button" class="button gnn-media-clear"><?php esc_html_e( 'Remove', 'gnn' ); ?></button>
	</div>

	<p>
		<label class="gnn-field__label" for="gnn_slide_kicker"><?php esc_html_e( 'Kicker', 'gnn' ); ?></label><br>
		<input type="text" id="gnn_slide_kicker" name="gnn_slide_kicker" class="widefat" value="<?php echo esc_attr( $kicker ); ?>">
	</p>
	<p>
		<label class="gnn-field__label" for="gnn_slide_text"><?php esc_html_e( 'Text', 'gnn' ); ?></label><br>
		<textarea id="gnn_slide_text" name="gnn_slide_text" class="widefat" rows="3"><?php echo esc_textarea( $text ); ?></textarea>
	</p>
	<p>
		<label class="gnn-field__label" for="gnn_slide_cta_label"><?php esc_html_e( 'Button label', 'gnn' ); ?></label><br>
		<input type="text" id="gnn_slide_cta_label" name="gnn_slide_cta_label" class="widefat" value="<?php echo esc_attr( $cta_l ); ?>">
	</p>
	<p>
		<label class="gnn-field__label" for="gnn_slide_cta_url"><?php esc_html_e( 'Button URL', 'gnn' ); ?></label><br>
		<input type="url" id="gnn_slide_cta_url" name="gnn_slide_cta_url" class="widefat" value="<?php echo esc_attr( $cta_u ); ?>">
	</p>
	<p>
		<label class="gnn-field__label" for="gnn_slide_fit"><?php esc_html_e( 'Image fit', 'gnn' ); ?></label><br>
		<select id="gnn_slide_fit" name="gnn_slide_fit">
			<?php
			$choices = array(
				'cover'   => __( 'Cover (fill, cropped)', 'gnn' ),
				'contain' => __( 'Contain (fit, no crop)', 'gnn' ),
				'top'     => __( 'Cover — top', 'gnn' ),
				'bottom'  => __( 'Cover — bottom', 'gnn' ),
				'center'  => __( 'Cover — centered', 'gnn' ),
			);
			foreach ( $choices as $val => $label ) {
				printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $val ), selected( $fit, $val, false ), esc_html( $label ) );
			}
			?>
		</select>
	</p>
	<p class="description"><?php esc_html_e( 'Slide order: set the "Order" field under Page Attributes (lower = earlier). An empty title hides the slide from the front page.', 'gnn' ); ?></p>
	<?php
}

/**
 * Save the slide meta box.
 *
 * @param int $post_id Post ID.
 */
function gnn_save_slide_meta( $post_id ) {
	if ( ! isset( $_POST['gnn_slide_meta_nonce'] )
		|| ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['gnn_slide_meta_nonce'] ) ), 'gnn_slide_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$map        = array(
		'gnn_slide_image_2x'  => '_gnn_slide_image_2x',
		'gnn_slide_kicker'    => '_gnn_slide_kicker',
		'gnn_slide_text'      => '_gnn_slide_text',
		'gnn_slide_cta_label' => '_gnn_slide_cta_label',
		'gnn_slide_cta_url'   => '_gnn_slide_cta_url',
		'gnn_slide_fit'       => '_gnn_slide_fit',
	);
	$sanitizers = gnn_slide_meta_fields();
	foreach ( $map as $field => $meta_key ) {
		if ( ! isset( $_POST[ $field ] ) ) {
			continue;
		}
		$raw   = wp_unslash( $_POST[ $field ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized dynamically via $sanitizers on the next line.
		$clean = call_user_func( $sanitizers[ $meta_key ], $raw );
		update_post_meta( $post_id, $meta_key, $clean );
	}
}
add_action( 'save_post_gnn_slide', 'gnn_save_slide_meta' );

/**
 * Admin assets for the slide editor (media picker, same UI as the panel).
 *
 * @param string $hook Current admin page hook.
 */
function gnn_slide_admin_assets( $hook ) {
	global $post_type;
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || 'gnn_slide' !== $post_type ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_style( 'gnn-admin', get_template_directory_uri() . '/assets/css/admin.css', array(), GNN_VERSION );
	wp_enqueue_script( 'gnn-slider-admin', get_template_directory_uri() . '/assets/js/slider-admin.js', array(), GNN_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'gnn_slide_admin_assets' );

/**
 * Fetch published slides in display order, each as a ready-to-render array.
 *
 * @return array[]
 */
function gnn_get_slides() {
	$query = new WP_Query(
		array(
			'post_type'      => 'gnn_slide',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'no_found_rows'  => true,
		)
	);

	$slides = array();
	foreach ( $query->posts as $post ) {
		$title = get_the_title( $post );
		if ( '' === trim( (string) $title ) ) {
			continue; // Empty title hides the slide.
		}
		$slides[] = array(
			'title'     => $title,
			'image'     => (int) get_post_thumbnail_id( $post ),
			'image_2x'  => (int) get_post_meta( $post->ID, '_gnn_slide_image_2x', true ),
			'kicker'    => (string) get_post_meta( $post->ID, '_gnn_slide_kicker', true ),
			'text'      => (string) get_post_meta( $post->ID, '_gnn_slide_text', true ),
			'cta_label' => (string) get_post_meta( $post->ID, '_gnn_slide_cta_label', true ),
			'cta_url'   => (string) get_post_meta( $post->ID, '_gnn_slide_cta_url', true ),
			'fit'       => gnn_sanitize_slide_fit( (string) get_post_meta( $post->ID, '_gnn_slide_fit', true ) ),
		);
	}
	return $slides;
}
