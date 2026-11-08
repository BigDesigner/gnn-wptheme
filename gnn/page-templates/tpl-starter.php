<?php
/**
 * Template Name: Design Profile Showcase
 *
 * Shared full-width canvas for native blocks or Elementor containers.
 *
 * @package GNN
 */

get_header();
?>
<main id="primary" class="gnn-starter-page">
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
