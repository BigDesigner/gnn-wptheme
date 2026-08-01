<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package GNN
 */

if ( ! gnn_show_sidebar() ) { // Widgets present AND panel position ≠ "none".
	return;
}
?>

<aside id="secondary" class="widget-area">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside><!-- #secondary -->
