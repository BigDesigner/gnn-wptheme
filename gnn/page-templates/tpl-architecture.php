<?php
/**
 * Template Name: Architecture Showcase
 * Template Post Type: page
 *
 * @package GNN
 */

get_header();
?>
<main id="primary" class="gnn-architecture-page">
	<?php
	while ( have_posts() ) :
		the_post();
		the_content();
		wp_link_pages();
	endwhile;
	?>
</main>
<?php
get_footer();
