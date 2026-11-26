<?php
/**
 * Primary-menu walker: standard ul/li/.sub-menu output, plus mega-menu support.
 *
 * Assign the CSS class `mega-menu-parent` to a top-level menu item in
 * Appearance → Menus to turn its .sub-menu into a full-width mega menu:
 * - its second-level children render as columns (label = column heading,
 *   their children = the column's links);
 * - a second-level item with class `mega-featured` renders as a featured
 *   card (kicker = the item's Title Attribute, body = its Description).
 *
 * No menu item is hardcoded — everything comes from the assigned menu.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nav menu walker with mega-menu column/featured-card support.
 */
class GNN_Walker_Nav_Menu extends Walker_Nav_Menu {

	/**
	 * Whether the branch currently being walked is a mega menu.
	 *
	 * @var bool
	 */
	protected $in_mega = false;

	/** Accordion mode keeps every branch individually collapsible. */
	protected $accordion = false;

	/** The most recent parent ID at each depth. */
	private $submenus = array();

	/** Preserve post-safe label markup and basic inline SVG geometry. */
	private function safe_title( $title ) {
		$allowed = wp_kses_allowed_html( 'post' );
		$paint   = array_fill_keys( array( 'class', 'fill', 'fill-rule', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin', 'opacity', 'transform' ), true );
		$shapes  = array(
			'path'     => array( 'd' ),
			'circle'   => array( 'cx', 'cy', 'r' ),
			'ellipse'  => array( 'cx', 'cy', 'rx', 'ry' ),
			'rect'     => array( 'x', 'y', 'width', 'height', 'rx', 'ry' ),
			'line'     => array( 'x1', 'x2', 'y1', 'y2' ),
			'polyline' => array( 'points' ),
			'polygon'  => array( 'points' ),
			'g'        => array(),
		);
		foreach ( $shapes as $tag => $attributes ) {
			$allowed[ $tag ] = $paint + array_fill_keys( $attributes, true );
		}
		$allowed['svg']   = $paint + array_fill_keys( array( 'xmlns', 'viewbox', 'width', 'height', 'role', 'aria-hidden', 'aria-label', 'focusable', 'preserveaspectratio' ), true );
		$allowed['title'] = array();
		return wp_kses( $title, $allowed );
	}

	/**
	 * Starts the list before the elements are added.
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param int      $depth  Depth of menu item.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$indent  = str_repeat( "\t", $depth );
		$classes = 'sub-menu';

		if ( 0 === $depth && $this->in_mega ) {
			$classes .= ' mega-menu';
		} elseif ( $depth > 0 && $this->in_mega ) {
			$classes .= ' mega-menu__links';
		}

		$output .= "\n{$indent}<ul class=\"" . esc_attr( $classes ) . '" id="' . esc_attr( $this->submenus[ $depth ] ) . "\">\n";
	}

	/**
	 * Starts the element output.
	 *
	 * @param string   $output            Used to append additional content (passed by reference).
	 * @param WP_Post  $data_object       Menu item data object.
	 * @param int      $depth             Depth of menu item.
	 * @param stdClass $args              An object of wp_nav_menu() arguments.
	 * @param int      $current_object_id Optional. ID of the current menu item. Default 0.
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
		$item    = $data_object;
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		if ( 0 === $depth ) {
			$this->in_mega = in_array( 'mega-menu-parent', $classes, true );
		}

		$has_children = $this->has_children;
		$prefix = $args->gnn_header_menu_prefix ?? 'menu';
		$submenu_id = $prefix . '-submenu-' . $item->ID;
		$this->submenus[ $depth ] = $submenu_id;
		$is_featured  = $this->in_mega && 1 === $depth && in_array( 'mega-featured', $classes, true );
		$is_column    = $this->in_mega && 1 === $depth && $has_children && ! $is_featured;

		if ( $is_column ) {
			$classes[] = 'mega-menu__column';
		}
		if ( $is_featured ) {
			$classes[] = 'mega-menu__featured';
		}

		$class_names = implode( ' ', array_filter( array_map( 'sanitize_html_class', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) ) ) );
		$id          = apply_filters( 'nav_menu_item_id', $prefix . '-item-' . $item->ID, $item, $args, $depth );

		$output .= '<li id="' . esc_attr( $id ) . '" class="' . esc_attr( $class_names ) . '">';

		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']   = ! empty( $item->url ) ? $item->url : '';
		$atts['aria-current'] = $item->current ? 'page' : '';
		$atts           = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( is_scalar( $value ) && '' !== (string) $value && preg_match( '/^(?!on)[a-z][a-z0-9_:-]*$/iD', $attr ) ) {
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );
		$visible_title = $this->safe_title( $title );

		$badge = trim( (string) get_post_meta( $item->ID, '_gnn_menu_badge', true ) );
		$badge_markup = '' === $badge ? '' : '<span class="gnn-badge">' . esc_html( $badge ) . '</span>';
		if ( $is_column ) {
			// Column heading with links provided by the item's children.
			$item_output = '<span class="mega-menu__heading">' . $visible_title . $badge_markup . '</span>';
		} elseif ( $is_featured ) {
			$kicker       = ! empty( $item->attr_title ) ? $item->attr_title : __( 'New', 'gnn' );
			$description  = ! empty( $item->description ) ? $item->description : '';
			$item_output  = '<a class="mega-menu__card"' . $attributes . '>';
			$image        = (int) get_post_meta( $item->ID, '_gnn_menu_image', true );
			$item_output .= $image ? '<span class="mega-menu__card-media" aria-hidden="true">' . wp_get_attachment_image( $image, 'medium', false, array( 'alt' => '' ) ) . '</span>' : '';
			$item_output .= '<span class="mega-menu__card-body">';
			$item_output .= '<span class="mega-menu__card-kicker">' . esc_html( $kicker ) . '</span>';
			$item_output .= '<span class="mega-menu__card-title">' . $visible_title . $badge_markup . '</span>';
			if ( $description ) {
				$item_output .= '<span class="mega-menu__card-desc">' . esc_html( $description ) . '</span>';
			}
			$item_output .= '</span></a>';
		} else {
			$item_output  = wp_kses_post( $args->before ?? '' );
			$item_output .= '<a' . $attributes . '>';
			$item_output .= wp_kses_post( $args->link_before ?? '' ) . $visible_title . $badge_markup . wp_kses_post( $args->link_after ?? '' );
			$item_output .= '</a>';
			$item_output .= wp_kses_post( $args->after ?? '' );
		}

		if ( $has_children && ( ! $is_column || $this->accordion ) ) {
			/* translators: %s: menu item title. */
			$label = sprintf( __( 'Toggle submenu for %s', 'gnn' ), wp_strip_all_tags( $title ) );
			$item_output .= '<button type="button" class="gnn-submenu-toggle" aria-controls="' . esc_attr( $submenu_id ) . '" aria-expanded="false" aria-label="' . esc_attr( $label ) . '"><span class="dropdown-caret" aria-hidden="true"></span></button>';
		}

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}
