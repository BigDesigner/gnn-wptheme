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

		$output .= "\n{$indent}<ul class=\"" . esc_attr( $classes ) . "\">\n";
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

		$has_children = in_array( 'menu-item-has-children', $classes, true );
		$is_featured  = $this->in_mega && 1 === $depth && in_array( 'mega-featured', $classes, true );
		$is_column    = $this->in_mega && 1 === $depth && $has_children && ! $is_featured;

		if ( $is_column ) {
			$classes[] = 'mega-menu__column';
		}
		if ( $is_featured ) {
			$classes[] = 'mega-menu__featured';
		}

		$class_names = implode( ' ', array_filter( array_map( 'sanitize_html_class', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) ) ) );
		$id          = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );

		$output .= '<li id="' . esc_attr( $id ) . '" class="' . esc_attr( $class_names ) . '">';

		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']   = ! empty( $item->url ) ? $item->url : '';
		$atts           = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( strlen( $value ) ) {
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		if ( $is_column ) {
			// Column heading: plain text, links come from the item's children.
			$item_output = '<span class="mega-menu__heading">' . esc_html( $title ) . '</span>';
		} elseif ( $is_featured ) {
			$kicker       = ! empty( $item->attr_title ) ? $item->attr_title : __( 'New', 'gnn' );
			$description  = ! empty( $item->description ) ? $item->description : '';
			$item_output  = '<a class="mega-menu__card"' . $attributes . '>';
			$item_output .= '<span class="mega-menu__card-media" aria-hidden="true"></span>';
			$item_output .= '<span class="mega-menu__card-body">';
			$item_output .= '<span class="mega-menu__card-kicker">' . esc_html( $kicker ) . '</span>';
			$item_output .= '<span class="mega-menu__card-title">' . esc_html( $title ) . '</span>';
			if ( $description ) {
				$item_output .= '<span class="mega-menu__card-desc">' . esc_html( $description ) . '</span>';
			}
			$item_output .= '</span></a>';
		} else {
			$badge        = trim( (string) get_post_meta( $item->ID, '_gnn_menu_badge', true ) );
			$item_output  = $args->before ?? '';
			$item_output .= '<a' . $attributes . '>';
			$item_output .= ( $args->link_before ?? '' ) . $title . ( $args->link_after ?? '' );
			if ( '' !== $badge ) {
				$item_output .= '<span class="gnn-badge">' . esc_html( $badge ) . '</span>';
			}
			if ( 0 === $depth && $has_children ) {
				$item_output .= '<span class="dropdown-caret" aria-hidden="true">&#9662;</span>';
			}
			$item_output .= '</a>';
			$item_output .= $args->after ?? '';
		}

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}
