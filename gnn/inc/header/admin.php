<?php
/**
 * Header tab renderer and helpers for the GNN Theme Panel.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Compute the legacy baseline values that empty header
 * fields resolve to, matching gnn_header_config().
 *
 * @param array $config Resolved header configuration from gnn_header_config().
 * @return array<string,mixed>
 */
function gnn_header_admin_baselines( $config ) {
	$preset           = $config['preset'];
	$legacy_structure = 'centered' === gnn_option( 'header_layout' ) ? 'centered' : 'classic';
	$preset_structure = in_array( $preset, array( 'classic', 'centered', 'stacked', 'shop' ), true ) ? $preset : 'classic';

	return array(
		'header_structure'   => '' === (string) gnn_option( 'header_structure' )
			? $config['header_structure']
			: ( 'legacy' === $preset ? $legacy_structure : $preset_structure ),
		'header_surface'     => '' === (string) gnn_option( 'header_surface' )
			? $config['header_surface']
			: ( 'transparent' === $preset ? 'transparent' : ( 'minimal' === $preset ? 'solid' : 'glass' ) ),
		'header_shape'       => '' === (string) gnn_option( 'header_shape' )
			? $config['header_shape']
			: ( 'floating' === $preset ? 'floating' : 'full' ),
		'header_width'       => '' === (string) gnn_option( 'header_width' )
			? $config['header_width']
			: ( 'minimal' === $preset ? 'wide' : 'contained' ),
		'header_behavior'    => '' === (string) gnn_option( 'header_behavior' )
			? $config['header_behavior']
			: ( gnn_option( 'sticky_header' ) ? 'sticky' : 'static' ),
		'header_overlay'     => '' === (string) gnn_option( 'header_overlay' )
			? (bool) $config['header_overlay']
			: ( 'transparent' === $preset ),
		'header_search_mode' => '' === (string) gnn_option( 'header_search_mode' )
			? $config['header_search_mode']
			: ( gnn_option( 'header_search_bar' ) ? 'field' : 'link' ),
	);
}

/**
 * Show the resolved default in the empty select option.
 *
 * @param array  $choices      Allowed value => label map.
 * @param string $resolved_key Key inside $choices that '' currently resolves to.
 * @return array
 */
function gnn_header_admin_inherit_choices( $choices, $resolved_key ) {
	if ( isset( $choices[''], $choices[ $resolved_key ] ) ) {
		/* translators: %s: resolved setting label. */
		$choices[''] = sprintf( __( 'Default (%s)', 'gnn' ), $choices[ $resolved_key ] );
	}
	return $choices;
}

/**
 * Build the choices array for a shared header region selector while preserving
 * any stored value (-2 hide, -1 inherit, 0 theme default, or published layout).
 *
 * @param string $key Option key.
 * @return array<int,string>
 */
function gnn_header_admin_region_choices( $key ) {
	$choices = array(
		-1 => __( 'Inherit', 'gnn' ),
		0  => __( 'Theme header', 'gnn' ),
		-2 => __( 'Hide header', 'gnn' ),
	);
	$layouts = get_posts(
		array(
			'post_type'   => 'gnn_layout',
			'numberposts' => 100,
			'post_status' => 'publish',
			'orderby'     => 'title',
			'order'       => 'ASC',
		)
	);
	foreach ( $layouts as $layout ) {
		$choices[ (int) $layout->ID ] = $layout->post_title;
	}
	$stored = (int) gnn_option( $key );
	if ( $stored > 0 && ! isset( $choices[ $stored ] ) ) {
		$choices[ $stored ] = __( 'Unavailable layout', 'gnn' );
	}
	return $choices;
}

/**
 * Prepare the ordered elements list for the Elements card editor.
 *
 * Omitted IDs from a non-empty stored list are appended with desktop/mobile
 * unchecked so every master toggle remains rendered in the form.
 *
 * @return array{raw:string,items:array<int,array<string,mixed>>,cart_meta:array<string,mixed>}
 */
function gnn_header_admin_elements_state() {
	$raw    = (string) gnn_option( 'header_elements' );
	$parsed = gnn_parse_header_elements( $raw );
	$defs   = gnn_header_default_elements();

	$meta = array(
		'theme'    => array(
			'label' => __( 'Theme switch', 'gnn' ),
			'opt'   => 'show_toggle',
		),
		'cart'     => array(
			'label' => __( 'Cart', 'gnn' ),
			'opt'   => 'show_cart',
		),
		'cta'      => array(
			'label' => __( 'Button', 'gnn' ),
			'opt'   => 'header_cta_show',
		),
		'search'   => array(
			'label' => __( 'Search', 'gnn' ),
			'opt'   => 'show_search',
		),
		'contact'  => array(
			'label' => __( 'Contact', 'gnn' ),
			'opt'   => 'header_contact',
		),
		'language' => array(
			'label' => __( 'Language', 'gnn' ),
			'opt'   => 'header_language',
		),
	);

	$ordered = is_array( $parsed ) ? $parsed : $defs;
	$seen    = array();
	$items   = array();

	foreach ( $ordered as $entry ) {
		$id = $entry['id'];
		if ( ! isset( $meta[ $id ] ) ) {
			continue;
		}
		$seen[ $id ] = true;
		$items[]     = array(
			'id'      => $id,
			'label'   => $meta[ $id ]['label'],
			'opt'     => $meta[ $id ]['opt'],
			'enabled' => (bool) gnn_option( $meta[ $id ]['opt'] ),
			'desktop' => (bool) $entry['desktop'],
			'mobile'  => (bool) $entry['mobile'],
			'present' => true,
		);
	}

	foreach ( $defs as $def ) {
		$id = $def['id'];
		if ( isset( $seen[ $id ] ) || ! isset( $meta[ $id ] ) ) {
			continue;
		}
		$items[] = array(
			'id'      => $id,
			'label'   => $meta[ $id ]['label'],
			'opt'     => $meta[ $id ]['opt'],
			'enabled' => (bool) gnn_option( $meta[ $id ]['opt'] ),
			'desktop' => false,
			'mobile'  => false,
			'present' => false,
		);
	}

	$cart_meta = array(
		'index'   => count( $items ) - 1,
		'desktop' => true,
		'mobile'  => true,
		'present' => ! is_array( $parsed ),
	);
	foreach ( $items as $index => $item ) {
		if ( 'cart' === $item['id'] ) {
			$cart_meta = array(
				'index'   => $index,
				'desktop' => $item['desktop'],
				'mobile'  => $item['mobile'],
				'present' => $item['present'],
			);
			break;
		}
	}

	return array(
		'raw'       => $raw,
		'items'     => $items,
		'cart_meta' => $cart_meta,
	);
}

/**
 * Render the structure visual radio cards (works with and without JavaScript).
 *
 * @param string $current        Stored header_structure value.
 * @param string $resolved_label Human-readable resolved structure name.
 */
function gnn_header_admin_structure_cards( $current, $resolved_label ) {
	$cards = array(
		''         => array(
			/* translators: %s: resolved structure label. */
			'label' => sprintf( __( 'Default (%s)', 'gnn' ), $resolved_label ),
			'wire'  => 'inherit',
		),
		'classic'  => array(
			'label' => __( 'Classic', 'gnn' ),
			'wire'  => 'classic',
		),
		'centered' => array(
			'label' => __( 'Centered', 'gnn' ),
			'wire'  => 'centered',
		),
		'stacked'  => array(
			'label' => __( 'Stacked', 'gnn' ),
			'wire'  => 'stacked',
		),
		'shop'     => array(
			'label' => __( 'Shop', 'gnn' ),
			'wire'  => 'shop',
		),
	);
	?>
	<div class="gnn-setting-row gnn-setting-row--cards" data-key="header_structure">
		<div class="gnn-setting-label-col">
			<span class="gnn-setting-label" id="label-gnn-header_structure"><?php esc_html_e( 'Structure', 'gnn' ); ?></span>
		</div>
		<div class="gnn-setting-control-col">
			<fieldset id="gnn-header_structure" class="gnn-header-structure-cards" aria-labelledby="label-gnn-header_structure">
				<legend class="screen-reader-text"><?php esc_html_e( 'Structure', 'gnn' ); ?></legend>
				<?php foreach ( $cards as $val => $card ) : ?>
					<?php
					$slug    = '' === $val ? 'inherit' : $val;
					$checked = ( (string) $current === (string) $val );
					?>
					<label class="gnn-hs-card<?php echo $checked ? ' is-selected' : ''; ?>" for="gnn-header_structure-<?php echo esc_attr( $slug ); ?>">
						<input
							type="radio"
							id="gnn-header_structure-<?php echo esc_attr( $slug ); ?>"
							name="gnn_options[header_structure]"
							value="<?php echo esc_attr( $val ); ?>"
							<?php checked( $checked ); ?>
						>
						<span class="gnn-hs-wireframe gnn-hs-wireframe--<?php echo esc_attr( $card['wire'] ); ?>" aria-hidden="true">
							<?php if ( 'classic' === $card['wire'] ) : ?>
								<span class="gnn-hs-row">
									<i class="gnn-hs-logo"></i>
									<i class="gnn-hs-nav"></i>
									<i class="gnn-hs-act"></i>
								</span>
							<?php elseif ( 'centered' === $card['wire'] ) : ?>
								<span class="gnn-hs-row">
									<i class="gnn-hs-nav gnn-hs-nav--sm"></i>
									<i class="gnn-hs-logo"></i>
									<i class="gnn-hs-nav gnn-hs-nav--sm"></i>
								</span>
							<?php elseif ( 'stacked' === $card['wire'] ) : ?>
								<span class="gnn-hs-row">
									<i class="gnn-hs-logo"></i>
									<i class="gnn-hs-act"></i>
								</span>
								<span class="gnn-hs-row gnn-hs-row--nav">
									<i class="gnn-hs-nav gnn-hs-nav--wide"></i>
								</span>
							<?php elseif ( 'shop' === $card['wire'] ) : ?>
								<span class="gnn-hs-row">
									<i class="gnn-hs-logo"></i>
									<i class="gnn-hs-search"></i>
									<i class="gnn-hs-act"></i>
								</span>
								<span class="gnn-hs-row gnn-hs-row--nav">
									<i class="gnn-hs-nav gnn-hs-nav--wide"></i>
								</span>
							<?php else : ?>
								<span class="gnn-hs-row gnn-hs-row--inherit">
									<i class="gnn-hs-logo"></i>
									<i class="gnn-hs-nav"></i>
								</span>
							<?php endif; ?>
						</span>
						<span class="gnn-hs-label"><?php echo esc_html( $card['label'] ); ?></span>
					</label>
				<?php endforeach; ?>
			</fieldset>
		</div>
	</div>
	<?php
}

/**
 * Render the schematic live preview card (HTML/CSS only, works without JS).
 *
 * @param array $config         Resolved header config from gnn_header_config().
 * @param array $elements_state Prepared elements state.
 * @param array $schema         Header setting definitions.
 */
function gnn_header_admin_live_preview( $config, $elements_state, $schema ) {
	$default_mode = 'light' === get_theme_mod( 'gnn_default_theme', 'dark' ) ? 'light' : 'dark';
	$woo_active   = class_exists( 'WooCommerce' );
	$site_name    = get_bloginfo( 'name' ) ?: __( 'Site title', 'gnn' );
	$labels       = array(
		'structure'    => $schema['header_structure'][4],
		'surface'      => $schema['header_surface'][4],
		'shape'        => $schema['header_shape'][4],
		'announcement' => __( 'Announcement bar', 'gnn' ),
		/* translators: %s: mobile breakpoint in pixels. */
		'mobile'       => __( 'Mobile < %s px', 'gnn' ),
	);
	?>
	<div class="gnn-hp" data-gnn-header-preview data-preview-labels="<?php echo esc_attr( wp_json_encode( $labels ) ); ?>" data-mode="<?php echo esc_attr( $default_mode ); ?>" data-device="desktop">
		<div class="gnn-hp__toolbar">
			<div class="gnn-hp__devices" role="group" aria-label="<?php esc_attr_e( 'Preview device', 'gnn' ); ?>">
				<button type="button" class="button gnn-hp__device is-active" data-hp-device="desktop" aria-pressed="true"><?php esc_html_e( 'Desktop', 'gnn' ); ?></button>
				<button type="button" class="button gnn-hp__device" data-hp-device="mobile" aria-pressed="false"><?php esc_html_e( 'Mobile', 'gnn' ); ?></button>
			</div>
			<span class="gnn-hp__meta" data-hp-meta>
				<?php
				echo esc_html(
					$labels['structure'][ $config['header_structure'] ] . ' · ' .
					$labels['surface'][ $config['header_surface'] ] . ' · ' .
					$labels['shape'][ $config['header_shape'] ] . ' · ' .
					(int) $config['header_height'] . ' px'
				);
				?>
			</span>
		</div>

		<div class="gnn-hp__stage" data-hp-stage
			data-structure="<?php echo esc_attr( $config['header_structure'] ); ?>"
			data-surface="<?php echo esc_attr( $config['header_surface'] ); ?>"
			data-shape="<?php echo esc_attr( $config['header_shape'] ); ?>"
			data-width="<?php echo esc_attr( $config['header_width'] ); ?>"
			data-align="<?php echo esc_attr( $config['header_menu_align'] ); ?>"
			data-overlay="<?php echo $config['header_overlay'] ? '1' : '0'; ?>"
			data-topbar="<?php echo gnn_option( 'topbar_enable' ) ? '1' : '0'; ?>"
			data-topbar-mobile="<?php echo $config['topbar_mobile'] ? '1' : '0'; ?>"
			data-dock="<?php echo gnn_option( 'mobile_dock' ) ? '1' : '0'; ?>"
		>
			<div class="gnn-hp__canvas">
				<div class="gnn-hp__topbar" data-hp-topbar<?php echo gnn_option( 'topbar_enable' ) ? '' : ' hidden'; ?>>
				<span class="gnn-hp__topbar-text" data-hp-topbar-text><?php echo esc_html( trim( (string) gnn_option( 'topbar_text' ) ) ?: $labels['announcement'] ); ?></span>
					<span class="gnn-hp__topbar-contact" data-hp-topbar-contact><?php esc_html_e( 'Contact', 'gnn' ); ?></span>
				</div>

				<div class="gnn-hp__hero-backdrop" data-hp-hero aria-hidden="true">
					<span><?php esc_html_e( 'First section (Hero)', 'gnn' ); ?></span>
				</div>

				<div class="gnn-hp__header" data-hp-header>
					<div class="gnn-hp__row gnn-hp__row--main" data-hp-main-row>
						<span class="gnn-hp__burger" aria-hidden="true"><i></i><i></i><i></i></span>
						<span class="gnn-hp__nav gnn-hp__nav--left" aria-hidden="true"><i></i><i></i></span>
						<span class="gnn-hp__brand">
							<span class="gnn-hp__brand-name"><?php echo esc_html( $site_name ); ?></span>
							<span class="gnn-hp__brand-dot" data-hp-dot<?php echo gnn_option( 'brand_dot' ) ? '' : ' hidden'; ?>>.</span>
						</span>
						<span class="gnn-hp__nav gnn-hp__nav--main" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
						<span class="gnn-hp__nav gnn-hp__nav--right" aria-hidden="true"><i></i><i></i></span>
						<div class="gnn-hp__actions" data-hp-actions-desktop>
							<?php
							foreach ( $elements_state['items'] as $item ) {
								if ( 'cart' === $item['id'] && ! $woo_active ) {
									continue;
								}
								if ( $item['enabled'] && $item['desktop'] ) {
									echo '<span class="gnn-hp__pill gnn-hp__pill--' . esc_attr( $item['id'] ) . '">' . esc_html( $item['label'] ) . '</span>';
								}
							}
							?>
						</div>
					</div>
					<div class="gnn-hp__row gnn-hp__row--nav" data-hp-nav-row<?php echo $config['two_rows'] ? '' : ' hidden'; ?>>
						<span class="gnn-hp__nav gnn-hp__nav--second" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i></span>
					</div>
				</div>

				<div class="gnn-hp__mobile-panel" data-hp-mobile-panel>
					<div class="gnn-hp__mobile-links" aria-hidden="true"><i></i><i></i><i></i></div>
					<div class="gnn-hp__actions gnn-hp__actions--mobile" data-hp-actions-mobile>
						<?php
						foreach ( $elements_state['items'] as $item ) {
							if ( 'cart' === $item['id'] && ! $woo_active ) {
								continue;
							}
							if ( $item['enabled'] && $item['mobile'] ) {
								echo '<span class="gnn-hp__pill gnn-hp__pill--' . esc_attr( $item['id'] ) . '">' . esc_html( $item['label'] ) . '</span>';
							}
						}
						?>
					</div>
				</div>

				<div class="gnn-hp__dock" data-hp-dock<?php echo gnn_option( 'mobile_dock' ) ? '' : ' hidden'; ?> aria-hidden="true">
					<span></span><span></span><span></span><span></span>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Render the Menus status card (Card 4).
 *
 * @param array $config Resolved header config from gnn_header_config().
 */
function gnn_header_admin_menus_card( $config ) {
	$assigned = gnn_header_menu_locations();
	$slots    = array(
		'primary'      => __( 'Primary menu', 'gnn' ),
		'mobile'       => __( 'Mobile menu', 'gnn' ),
		'header-left'  => __( 'Header left', 'gnn' ),
		'header-right' => __( 'Header right', 'gnn' ),
	);
	$names    = array();
	foreach ( $assigned as $slot => $menu_id ) {
		$menu           = wp_get_nav_menu_object( $menu_id );
		$names[ $slot ] = $menu ? $menu->name : __( 'Assigned', 'gnn' );
	}
	?>
	<?php if ( empty( $assigned ) ) : ?>
		<div class="gnn-header-notice gnn-header-notice--warn" role="alert">
			<p><?php esc_html_e( 'No menu is assigned to the header. Assign a menu in Appearance > Menus.', 'gnn' ); ?></p>
		</div>
	<?php endif; ?>

	<dl class="gnn-header-menu-list">
		<?php foreach ( $slots as $slot => $label ) : ?>
			<div class="gnn-header-menu-list__item">
				<dt><?php echo esc_html( $label ); ?></dt>
				<dd class="<?php echo isset( $names[ $slot ] ) ? 'is-assigned' : 'is-empty'; ?>">
					<?php echo esc_html( $names[ $slot ] ?? __( 'Not assigned', 'gnn' ) ); ?>
				</dd>
			</div>
		<?php endforeach; ?>
	</dl>

	<?php if ( ! empty( $assigned ) ) : ?>
		<?php
		if ( isset( $names['header-left'], $names['header-right'] ) ) {
			/* translators: 1: left menu name, 2: right menu name. */
			$centered_note = sprintf( __( 'Centered layout uses Header left (%1$s) and Header right (%2$s).', 'gnn' ), $names['header-left'], $names['header-right'] );
		} elseif ( isset( $names['header-left'] ) ) {
			/* translators: %s: left menu name. */
			$centered_note = sprintf( __( 'Only Header left (%s) is assigned. The right side will stay empty until Header right is assigned.', 'gnn' ), $names['header-left'] );
		} elseif ( isset( $names['header-right'] ) ) {
			/* translators: %s: right menu name. */
			$centered_note = sprintf( __( 'Only Header right (%s) is assigned. The left side will stay empty until Header left is assigned.', 'gnn' ), $names['header-right'] );
		} elseif ( isset( $names['primary'] ) ) {
			/* translators: %s: primary menu name. */
			$centered_note = sprintf( __( 'No side menus are assigned. Primary menu (%s) is split between left and right.', 'gnn' ), $names['primary'] );
		} else {
			/* translators: %s: mobile menu name. */
			$centered_note = sprintf( __( 'No side menus are assigned. Mobile menu (%s) is split between left and right.', 'gnn' ), $names['mobile'] );
		}
		?>
		<div class="gnn-header-notice gnn-header-notice--info" data-gnn-centered-menu-note<?php echo 'centered' === $config['header_structure'] ? '' : ' hidden'; ?>>
			<p><?php echo esc_html( $centered_note ); ?></p>
		</div>
	<?php endif; ?>

	<p class="gnn-header-menu-action">
		<a class="button" href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>"><?php esc_html_e( 'Open Appearance > Menus', 'gnn' ); ?></a>
	</p>
	<?php
}

/**
 * Render the complete Header tab inside #gnn-tab-header.
 */
function gnn_render_header_tab() {
	$schema         = gnn_header_schema();
	$config         = gnn_header_config();
	$baselines      = gnn_header_admin_baselines( $config );
	$elements_state = gnn_header_admin_elements_state();
	$woo_active     = class_exists( 'WooCommerce' );

	$structure_labels = $schema['header_structure'][4];
	$resolved_struct  = $structure_labels[ $baselines['header_structure'] ];

	$overlay_choices = gnn_header_overlay_choices();
	$overlay_resolved = $baselines['header_overlay'] ? __( 'On', 'gnn' ) : __( 'Off', 'gnn' );
	/* translators: %s: resolved On/Off label. */
	$overlay_choices[''] = sprintf( __( 'Default (%s)', 'gnn' ), $overlay_resolved );

	$shared_active = (int) gnn_option( 'header_template' ) > 0
		|| (int) gnn_option( 'archive_header_template' ) > 0
		|| (int) gnn_option( 'post_header_template' ) > 0;
	?>
	<div class="gnn-header-admin-data"
		hidden
		data-gnn-header-admin
		data-baseline-structure="<?php echo esc_attr( $baselines['header_structure'] ); ?>"
		data-baseline-surface="<?php echo esc_attr( $baselines['header_surface'] ); ?>"
		data-baseline-shape="<?php echo esc_attr( $baselines['header_shape'] ); ?>"
		data-baseline-width="<?php echo esc_attr( $baselines['header_width'] ); ?>"
		data-baseline-behavior="<?php echo esc_attr( $baselines['header_behavior'] ); ?>"
		data-baseline-overlay="<?php echo $baselines['header_overlay'] ? '1' : '0'; ?>"
		data-baseline-search-mode="<?php echo esc_attr( $baselines['header_search_mode'] ); ?>"
		data-woo-active="<?php echo $woo_active ? '1' : '0'; ?>"
	>
		<?php
		// Preserve legacy baseline inputs so saving never drops or resets them.
		?>
		<input type="hidden" id="gnn-header_preset" name="gnn_options[header_preset]" value="<?php echo esc_attr( (string) gnn_option( 'header_preset' ) ); ?>">
		<input type="hidden" id="gnn-header_layout" name="gnn_options[header_layout]" value="<?php echo esc_attr( (string) gnn_option( 'header_layout' ) ); ?>">
		<input type="hidden" id="gnn-sticky_header" name="gnn_options[sticky_header]" value="<?php echo esc_attr( (string) (int) (bool) gnn_option( 'sticky_header' ) ); ?>">
		<input type="hidden" id="gnn-header_search_bar" name="gnn_options[header_search_bar]" value="<?php echo esc_attr( (string) (int) (bool) gnn_option( 'header_search_bar' ) ); ?>">
	</div>

		<?php // 1. Layout ?>
		<?php gnn_card_open( __( 'Layout', 'gnn' ) ); ?>
		<?php gnn_header_admin_structure_cards( (string) gnn_option( 'header_structure' ), $resolved_struct ); ?>
		<?php
		gnn_field_select(
			'header_surface',
			__( 'Surface', 'gnn' ),
			gnn_header_admin_inherit_choices( $schema['header_surface'][4], $baselines['header_surface'] )
		);
		gnn_field_select(
			'header_shape',
			__( 'Shape', 'gnn' ),
			gnn_header_admin_inherit_choices( $schema['header_shape'][4], $baselines['header_shape'] )
		);
		gnn_field_select(
			'header_width',
			__( 'Width', 'gnn' ),
			gnn_header_admin_inherit_choices( $schema['header_width'][4], $baselines['header_width'] )
		);
		gnn_field_select(
			'header_menu_align',
			__( 'Menu alignment', 'gnn' ),
			array(
				'left'   => __( 'Left', 'gnn' ),
				'center' => __( 'Center', 'gnn' ),
				'right'  => __( 'Right', 'gnn' ),
			)
		);
		gnn_field_number( 'header_height', __( 'Main row height', 'gnn' ), 48, 160 );
		gnn_field_select(
			'header_behavior',
			__( 'Scroll behavior', 'gnn' ),
			gnn_header_admin_inherit_choices( $schema['header_behavior'][4], $baselines['header_behavior'] )
		);
		gnn_field_select(
			'header_overlay',
			__( 'Overlay first section', 'gnn' ),
			$overlay_choices,
			__( 'Overlays the header on the page only when the first section is a hero.', 'gnn' )
		);
		gnn_field_media(
			'logo_overlay',
			__( 'Overlay logo', 'gnn' ),
			gnn_option( 'logo_overlay' ),
			__( 'Shown over a hero before the page scrolls.', 'gnn' )
		);
		gnn_field_number( 'header_mobile_breakpoint', __( 'Mobile breakpoint', 'gnn' ), 600, 1280 );
		gnn_field_select( 'header_overflow', __( 'Menu overflow', 'gnn' ), $schema['header_overflow'][4] );
		?>
		<?php gnn_card_close(); ?>

		<?php // 2. Live preview ?>
		<?php gnn_card_open( __( 'Live preview', 'gnn' ) ); ?>
		<?php gnn_header_admin_live_preview( $config, $elements_state, $schema ); ?>
		<?php gnn_card_close(); ?>

		<?php // 3. Elements ?>
		<?php gnn_card_open( __( 'Elements', 'gnn' ) ); ?>
		<div class="gnn-he-editor"
			data-gnn-he-editor
			data-cart-index="<?php echo (int) $elements_state['cart_meta']['index']; ?>"
			data-cart-desktop="<?php echo $elements_state['cart_meta']['desktop'] ? '1' : '0'; ?>"
			data-cart-mobile="<?php echo $elements_state['cart_meta']['mobile'] ? '1' : '0'; ?>"
			data-cart-present="<?php echo $elements_state['cart_meta']['present'] ? '1' : '0'; ?>"
		>
			<input type="hidden" id="gnn-header_elements" name="gnn_options[header_elements]" value="<?php echo esc_attr( $elements_state['raw'] ); ?>">
			<?php if ( ! $woo_active ) : ?>
				<input type="hidden" id="gnn-show_cart" name="gnn_options[show_cart]" value="<?php echo esc_attr( (string) (int) (bool) gnn_option( 'show_cart' ) ); ?>">
			<?php endif; ?>

			<div class="gnn-he-list" role="list" data-he-list>
				<?php foreach ( $elements_state['items'] as $item ) : ?>
					<?php
					if ( 'cart' === $item['id'] && ! $woo_active ) {
						continue;
					}
					?>
					<div class="gnn-he-item gnn-setting-row" role="listitem" data-he-item="<?php echo esc_attr( $item['id'] ); ?>" data-key="<?php echo esc_attr( $item['opt'] ); ?>">
						<div class="gnn-he-item__main">
							<label class="gnn-switch" for="gnn-<?php echo esc_attr( $item['opt'] ); ?>">
								<input
									type="checkbox"
									id="gnn-<?php echo esc_attr( $item['opt'] ); ?>"
									name="gnn_options[<?php echo esc_attr( $item['opt'] ); ?>]"
									value="1"
									data-he-enable="<?php echo esc_attr( $item['id'] ); ?>"
									aria-label="<?php echo esc_attr( $item['label'] ); ?>"
									<?php checked( $item['enabled'], true ); ?>
								>
								<span class="gnn-switch__track"><span class="gnn-switch__thumb"></span></span>
							</label>
							<label class="gnn-he-item__title" for="gnn-<?php echo esc_attr( $item['opt'] ); ?>"><?php echo esc_html( $item['label'] ); ?></label>
						</div>

						<div class="gnn-he-item__controls">
							<label class="gnn-he-check">
								<input type="checkbox" data-he-device="desktop" <?php checked( $item['desktop'], true ); ?>>
								<span><?php esc_html_e( 'Desktop', 'gnn' ); ?></span>
							</label>
							<label class="gnn-he-check">
								<input type="checkbox" data-he-device="mobile" <?php checked( $item['mobile'], true ); ?>>
								<span><?php esc_html_e( 'Mobile', 'gnn' ); ?></span>
							</label>
							<div class="gnn-he-order">
								<button
									type="button"
									class="button gnn-he-move"
									data-he-move="up"
									aria-label="<?php /* translators: %s: header element name. */ echo esc_attr( sprintf( __( 'Move %s up', 'gnn' ), $item['label'] ) ); ?>"
								><span aria-hidden="true">&#8593;</span></button>
								<button
									type="button"
									class="button gnn-he-move"
									data-he-move="down"
									aria-label="<?php /* translators: %s: header element name. */ echo esc_attr( sprintf( __( 'Move %s down', 'gnn' ), $item['label'] ) ); ?>"
								><span aria-hidden="true">&#8595;</span></button>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="gnn-he-sub" data-he-sub="cta">
				<?php
				gnn_field_text( 'header_cta_label', __( 'Button label', 'gnn' ), (string) gnn_option( 'header_cta_label' ) );
				gnn_field_text( 'header_cta_url', __( 'Button URL', 'gnn' ), (string) gnn_option( 'header_cta_url' ), 'https://' );
				?>
			</div>

			<div class="gnn-he-sub" data-he-sub="search">
				<?php
				gnn_field_select(
					'header_search_mode',
					__( 'Search mode', 'gnn' ),
					gnn_header_admin_inherit_choices( $schema['header_search_mode'][4], $baselines['header_search_mode'] )
				);
				if ( $woo_active ) {
					gnn_field_select( 'header_search_scope', __( 'Search scope', 'gnn' ), $schema['header_search_scope'][4] );
				} else {
					echo '<input type="hidden" id="gnn-header_search_scope" name="gnn_options[header_search_scope]" value="' . esc_attr( (string) gnn_option( 'header_search_scope' ) ) . '">';
				}
				?>
			</div>
		</div>
		<?php gnn_card_close(); ?>

		<?php // 4. Menus ?>
		<?php gnn_card_open( __( 'Menus', 'gnn' ) ); ?>
		<?php gnn_header_admin_menus_card( $config ); ?>
		<?php gnn_card_close(); ?>

		<?php // 5. Logo ?>
		<?php gnn_card_open( __( 'Logo', 'gnn' ) ); ?>
		<?php
		// Stored legacy URLs stay in the form so an untouched save keeps them.
		gnn_field_media( 'logo_light', __( 'Light mode (1×)', 'gnn' ), gnn_option( 'logo_light' ) );
		gnn_field_media( 'logo_light_2x', __( 'Light mode (2×)', 'gnn' ), gnn_option( 'logo_light_2x' ) );
		gnn_field_media( 'logo_dark', __( 'Dark mode (1×)', 'gnn' ), gnn_option( 'logo_dark' ) );
		gnn_field_media( 'logo_dark_2x', __( 'Dark mode (2×)', 'gnn' ), gnn_option( 'logo_dark_2x' ) );
		gnn_field_number( 'logo_max_height', __( 'Desktop max height', 'gnn' ), 12, 200 );
		gnn_field_number( 'logo_max_height_mobile', __( 'Mobile max height', 'gnn' ), 12, 200 );
		?>
		<?php gnn_card_close(); ?>

		<?php // 6. Top bar ?>
		<?php gnn_card_open( __( 'Top bar', 'gnn' ) ); ?>
		<?php
		gnn_field_checkbox( 'topbar_enable', __( 'Enable top bar', 'gnn' ) );
		?>
		<div class="gnn-header-topbar-fields" data-gnn-topbar-fields>
			<?php
			gnn_field_text( 'topbar_text', __( 'Announcement text', 'gnn' ), (string) gnn_option( 'topbar_text' ) );
			gnn_field_color( 'topbar_bg', __( 'Background color', 'gnn' ), (string) gnn_option( 'topbar_bg' ), '#141416', '', true );
			gnn_field_color( 'topbar_text_color', __( 'Text color', 'gnn' ), (string) gnn_option( 'topbar_text_color' ), '#9d9da4', '', true );
			gnn_field_select(
				'topbar_mobile',
				__( 'Show on mobile', 'gnn' ),
				array(
					1 => __( 'Show', 'gnn' ),
					0 => __( 'Hide', 'gnn' ),
				)
			);
			gnn_field_text( 'topbar_phone', __( 'Top bar phone', 'gnn' ), (string) gnn_option( 'topbar_phone' ), '', __( 'Uses the public phone below when empty.', 'gnn' ) );
			gnn_field_text( 'topbar_email', __( 'Top bar email', 'gnn' ), (string) gnn_option( 'topbar_email' ), '', __( 'Uses the public email below when empty.', 'gnn' ) );
			?>
		</div>
		<?php
		gnn_field_text( 'company_name', __( 'Company name', 'gnn' ), (string) gnn_option( 'company_name' ) );
		gnn_field_text( 'company_phone', __( 'Public phone', 'gnn' ), (string) gnn_option( 'company_phone' ) );
		gnn_field_text( 'company_email', __( 'Public email', 'gnn' ), (string) gnn_option( 'company_email' ) );
		?>
		<?php gnn_card_close(); ?>

		<?php // 7. Colors ?>
		<?php gnn_card_open( __( 'Colors', 'gnn' ), __( 'Leave empty to use the design default.', 'gnn' ) ); ?>
		<?php
		gnn_field_color_pair( 'header_bg', __( 'Background', 'gnn' ), '#141416', '#fafafa' );
		gnn_field_color_pair( 'header_text', __( 'Text', 'gnn' ), '#f5f5f4', '#101013' );
		gnn_field_color_pair( 'header_accent', __( 'Accent', 'gnn' ), '#34d399', '#117865' );
		?>
		<?php gnn_card_close(); ?>

		<?php // 8. Mobile ?>
		<?php gnn_card_open( __( 'Mobile', 'gnn' ) ); ?>
		<?php gnn_field_checkbox( 'mobile_dock', __( 'Mobile bottom dock', 'gnn' ), __( 'Shown on screens up to 600 px.', 'gnn' ) ); ?>
		<div class="gnn-setting-row gnn-setting-row--actions" data-key="mobile_actions" data-gnn-mobile-actions>
			<div class="gnn-setting-label-col">
				<span class="gnn-setting-label"><?php esc_html_e( 'Mobile actions', 'gnn' ); ?></span>
			</div>
			<div class="gnn-setting-control-col">
				<?php gnn_mobile_action_fields(); ?>
			</div>
		</div>
		<?php gnn_card_close(); ?>

		<?php // 9. Advanced ?>
		<?php gnn_card_open( __( 'Advanced', 'gnn' ) ); ?>
		<div class="gnn-header-notice gnn-header-notice--warn" data-gnn-shared-header-notice<?php echo $shared_active ? '' : ' hidden'; ?>>
			<p><?php esc_html_e( 'A shared header is selected. Layout, row and element settings above do not apply to it. Use [gnn_header_menu] to place the menu inside a shared header.', 'gnn' ); ?></p>
		</div>
		<?php
		gnn_field_select( 'header_template', __( 'Site header', 'gnn' ), gnn_header_admin_region_choices( 'header_template' ) );
		gnn_field_select( 'archive_header_template', __( 'Archive header', 'gnn' ), gnn_header_admin_region_choices( 'archive_header_template' ) );
		gnn_field_select( 'post_header_template', __( 'Single post header', 'gnn' ), gnn_header_admin_region_choices( 'post_header_template' ) );
		gnn_field_checkbox( 'brand_dot', __( 'Brand dot', 'gnn' ), __( 'Shows a dot after the site title when no logo image is set.', 'gnn' ) );
		?>
		<?php gnn_card_close(); ?>
	<?php
}
