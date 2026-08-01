<?php
/**
 * Custom search form: pill input matching the design.
 *
 * @package GNN
 */

?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="search-form__label">
		<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'gnn' ); ?></span>
		<input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Search products, posts, pages…', 'gnn' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
	</label>
	<button type="submit" class="search-submit screen-reader-text"><?php esc_html_e( 'Search', 'gnn' ); ?></button>
</form>
