<?php
/**
 * Assigned shared body; selected by the theme, not a duplicate page document.
 *
 * @package GNN
 */

get_header();
$gnn_shared_assignment = gnn_region_assignment( 'content' );
echo '<main id="primary" class="gnn-container gnn-shared-body">' . gnn_shared_content( $gnn_shared_assignment['id'] ) . '</main>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- publication-checked shared renderer.
get_footer();
