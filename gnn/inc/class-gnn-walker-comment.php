<?php
/**
 * Comment walker matching the GNN nested comment design.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * HTML5 comment walker with author badge + design markup.
 */
class GNN_Walker_Comment extends Walker_Comment {

	/**
	 * Outputs a single comment (HTML5 format).
	 *
	 * @param WP_Comment $comment Comment to display.
	 * @param int        $depth   Depth of the current comment.
	 * @param array      $args    An array of arguments.
	 */
	protected function html5_comment( $comment, $depth, $args ) {
		$tag       = ( 'div' === $args['style'] ) ? 'div' : 'li';
		$post      = get_post( $comment->comment_post_ID );
		$is_author = $post && (int) $comment->user_id === (int) $post->post_author && 0 !== (int) $comment->user_id;
		?>
		<<?php echo esc_html( $tag ); ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( $this->has_children ? 'parent' : '', $comment ); ?>>
			<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
				<footer class="comment-meta">
					<div class="comment-author vcard">
						<?php echo get_avatar( $comment, $args['avatar_size'], '', '', array( 'class' => 'comment-avatar' ) ); ?>
					</div>
					<div class="comment-metadata-row">
						<b class="fn"><?php echo esc_html( get_comment_author( $comment ) ); ?></b>
						<?php if ( $is_author ) : ?>
							<span class="comment-author-badge"><?php esc_html_e( 'Author', 'gnn' ); ?></span>
						<?php endif; ?>
						<span class="comment-metadata">
							<a href="<?php echo esc_url( get_comment_link( $comment, $args ) ); ?>">
								<time datetime="<?php comment_time( 'c' ); ?>"><?php echo esc_html( get_comment_date( '', $comment ) ); ?></time>
							</a>
						</span>
					</div>
				</footer>

				<div class="comment-content">
					<?php if ( '0' === $comment->comment_approved ) : ?>
						<p class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'gnn' ); ?></p>
					<?php endif; ?>
					<?php comment_text(); ?>
				</div>

				<div class="reply">
					<?php
					comment_reply_link(
						array_merge(
							$args,
							array(
								'add_below' => 'div-comment',
								'depth'     => $depth,
								'max_depth' => $args['max_depth'],
							)
						)
					);
					?>
				</div>
			</article>
		<?php
		// Closing tag is printed by end_el() in the parent class.
	}
}
