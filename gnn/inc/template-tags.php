<?php
/**
 * Custom template tags for the GNN theme.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'gnn_is_wc_utility_page' ) ) :
	/**
	 * Whether the current view is a WooCommerce utility page
	 * (cart, checkout, my account) that ships its own layout.
	 *
	 * @return bool
	 */
	function gnn_is_wc_utility_page() {
		return class_exists( 'WooCommerce' )
			&& ( is_cart() || is_checkout() || is_account_page() );
	}
endif;

if ( ! function_exists( 'gnn_breadcrumb' ) ) :
	/**
	 * Minimal breadcrumb trail: Home / [section /] current.
	 */
	function gnn_breadcrumb() {
		if ( is_front_page() ) {
			return;
		}
		// Per-page toggle (GNN Display Options meta box).
		if ( function_exists( 'gnn_hide_breadcrumb' ) && gnn_hide_breadcrumb() ) {
			return;
		}

		echo '<nav class="gnn-breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'gnn' ) . '">';
		echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'gnn' ) . '</a>';

		if ( is_singular( 'post' ) || is_category() || is_tag() || is_author() || is_home() ) {
			$blog_page = get_option( 'page_for_posts' );
			if ( $blog_page && ! is_home() ) {
				echo '<span class="gnn-breadcrumb__sep">/</span><a href="' . esc_url( get_permalink( $blog_page ) ) . '">' . esc_html( get_the_title( $blog_page ) ) . '</a>';
			}
		} elseif ( is_page() ) {
			$parent_id = wp_get_post_parent_id( get_the_ID() );
			if ( $parent_id ) {
				echo '<span class="gnn-breadcrumb__sep">/</span><a href="' . esc_url( get_permalink( $parent_id ) ) . '">' . esc_html( get_the_title( $parent_id ) ) . '</a>';
			}
		}

		echo '<span class="gnn-breadcrumb__sep">/</span><span class="gnn-breadcrumb__current">';
		if ( is_home() ) {
			$gnn_blog_title = get_the_title( get_option( 'page_for_posts' ) );
			echo esc_html( '' !== $gnn_blog_title ? $gnn_blog_title : __( 'Blog', 'gnn' ) );
		} elseif ( is_category() || is_tag() ) {
			echo esc_html( single_term_title( '', false ) );
		} elseif ( is_author() ) {
			echo esc_html( get_the_author() );
		} elseif ( is_search() ) {
			esc_html_e( 'Search', 'gnn' );
		} elseif ( is_404() ) {
			esc_html_e( 'Not found', 'gnn' );
		} else {
			echo esc_html( get_the_title() );
		}
		echo '</span></nav>';
	}
endif;

if ( ! function_exists( 'gnn_entry_meta' ) ) :
	/**
	 * Post-card meta line: date — reading time.
	 */
	function gnn_entry_meta() {
		// str_word_count() is byte-based: it treats the bytes of multibyte
		// letters (ğ ı ş ö ü ç, Cyrillic, CJK, …) as word separators and badly
		// over-counts non-ASCII content. Count Unicode letter/number runs
		// instead so the estimate holds in any language.
		$gnn_text  = wp_strip_all_tags( (string) get_post_field( 'post_content', get_the_ID() ) );
		$gnn_words = preg_match_all( '/[\p{L}\p{N}]+/u', $gnn_text );
		if ( false === $gnn_words ) {
			$gnn_words = str_word_count( $gnn_text ); // PCRE without UTF-8 support.
		}
		$minutes = max( 1, (int) ceil( $gnn_words / 220 ) );
		printf(
			'<div class="entry-meta"><time class="entry-date published" datetime="%1$s">%2$s</time> — %3$s</div>',
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			/* translators: %d: estimated reading time in minutes. */
			esc_html( sprintf( _n( '%d min read', '%d min read', $minutes, 'gnn' ), $minutes ) )
		);
	}
endif;

if ( ! function_exists( 'gnn_post_thumbnail' ) ) :
	/**
	 * Featured image output.
	 *
	 * On cards ($placeholder = true) a neutral block keeps the grid tidy when
	 * a post has no image. On single posts/pages ($placeholder = false) nothing
	 * is printed when there is no image, so no empty grey band appears.
	 *
	 * @param string $size        Registered image size.
	 * @param bool   $placeholder Whether to output the fallback block. Default true.
	 */
	function gnn_post_thumbnail( $size = 'gnn-wide', $placeholder = true ) {
		if ( post_password_required() || is_attachment() ) {
			return;
		}
		if ( has_post_thumbnail() ) {
			echo '<div class="post-thumbnail">';
			the_post_thumbnail( $size );
			// Singular views only (the helper checks is_singular() itself), so
			// card grids never get an overlay — and a single post whose title
			// the overlay suppressed always gets it rendered back here.
			if ( function_exists( 'gnn_title_overlay_html' ) ) {
				echo gnn_title_overlay_html(); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the helper.
			}
			echo '</div>';
		} elseif ( $placeholder && ! gnn_is_wc_utility_page() ) {
			// Card grids only: a placeholder keeps card heights consistent.
			echo '<div class="post-thumbnail post-thumbnail--empty" aria-hidden="true"></div>';
		}
	}
endif;

if ( ! function_exists( 'gnn_pagination' ) ) :
	/**
	 * Numbered pagination matching the design.
	 */
	function gnn_pagination() {
		the_posts_pagination(
			array(
				'mid_size'  => 2,
				'prev_text' => __( '← Prev', 'gnn' ),
				'next_text' => __( 'Next →', 'gnn' ),
			)
		);
	}
endif;
