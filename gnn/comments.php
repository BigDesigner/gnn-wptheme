<?php
/**
 * The template for displaying comments (nested list + form).
 *
 * @package GNN
 */

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$gnn_comment_count = get_comments_number();
			/* translators: %s: number of comments. */
			printf( esc_html( _n( '%s comment', '%s comments', $gnn_comment_count, 'gnn' ) ), esc_html( number_format_i18n( $gnn_comment_count ) ) );
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'avatar_size' => 44,
					'walker'      => new GNN_Walker_Comment(),
				)
			);
			?>
		</ol>

		<?php
		the_comments_navigation(
			array(
				'prev_text' => __( '← Older comments', 'gnn' ),
				'next_text' => __( 'Newer comments →', 'gnn' ),
			)
		);
		?>

		<?php if ( ! comments_open() ) : ?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'gnn' ); ?></p>
		<?php endif; ?>

	<?php endif; ?>

	<?php
	comment_form(
		array(
			'title_reply'        => __( 'Leave a comment', 'gnn' ),
			'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title">',
			'title_reply_after'  => '</h3>',
			'class_submit'       => 'submit gnn-btn',
		)
	);
	?>

</div><!-- #comments -->
