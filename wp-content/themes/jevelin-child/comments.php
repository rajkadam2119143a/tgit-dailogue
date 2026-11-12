<?php
/**
 * Commentsaaa
 */
if ( post_password_required() || ( defined('FW') && jevelin_option('page_comments') == false ) ) { return; }
?>

<div class="sh-comments">
	<?php if ( have_comments() ) : ?>
		<h2 class="sh-comments-title">
			<?php printf( _nx( '1 Comment', '%1$s Comments', get_comments_number(), 'comments title', 'jevelin' ), number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' ); ?>
		</h2>

		<ol class="sh-comment-list">
			<?php

			function jevelin_light_comment($comment, $args, $depth) {
				$GLOBALS['comment'] = $comment;
				extract($args, EXTR_SKIP);

				if ( 'div' == $args['style'] ) {
					$tag = 'div';
					$add_below = 'comment';
				} else {
					$tag = 'li';
					$add_below = 'div-comment';
				}
			?>
				<<?php echo esc_attr( $tag ); ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">

				<div id="div-comment-<?php comment_ID() ?>" class="comment-body">
					<div class="comment-column-left">
						<div class="comment-thumb"><?php if ( $args['avatar_size'] != 0 ) echo get_avatar( $comment, $args['avatar_size'] ); ?></div>
					</div>
					<div class="comment-column-right">

						<?php printf( '<span class="sh-comment-author">%s</span>', get_comment_author_link() ); ?>
						<?php if ( $comment->comment_approved == '0' ) : ?>
							<em class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'jevelin' ); ?></em>
						<?php endif; ?>

						<span class="sh-comment-date">
							<a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ); ?>">
								<?php
									echo get_comment_date().' '.esc_html__('at', 'jevelin').' '.get_comment_time();
								?>
							</a>
						</span>

						<div class="sh-comment-content">
							<?php comment_text(); ?>
						</div>

						<div class="reply">
							<?php if( comments_open() ) : ?>
								<i class="icon-pencil sh-reply-link"></i>
								<span class="sh-reply-link-button sh-default-color">
									<?php comment_reply_link( array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
								</span>
							<?php endif; ?>

							<span class="sh-reply-edit">
								<?php edit_comment_link( esc_html__( 'Edit', 'jevelin' ), '  ', '' ); ?>
							</span>
						</div>

					</div>
				</div>

			<?php }
				wp_list_comments( array(
					'style'      => 'ol',
					'short_ping' => true,
					'avatar_size' => 54,
					'max_depth' => '5',
					'callback' => 'jevelin_light_comment',

				) );
			?>
		</ol><!-- .comment-list -->


		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
			<div class="comment-navigation grey-light"><?php paginate_comments_links(); ?></div>
		<?php endif; // check for comment navigation ?>

	<?php endif; ?>

	<?php if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'jevelin' ); ?></p>
	<?php endif; ?>


	<div class="sh-comment-form">
		<?php comment_form(array(
			'label_submit' => esc_html__( 'Send a Feedback', 'jevelin' ),
			'comment_notes_after' => '',
			'comment_notes_before' => '',
			'fields' => apply_filters( 'comment_form_default_fields', array(
				'author' =>
					'<label>' . esc_html__( 'Name ', 'jevelin' ) . ' <i class="icon-check sh-accent-color"></i></label>
					<p class="comment-form-author">
						<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" required />
					</p>',

				'email' =>
					'<label>' . esc_html__( 'Email ', 'jevelin' ) . ' <i class="icon-check sh-accent-color"></i></label>
					<p class="comment-form-email">
						<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) .'" required />
					</p>',

				'url' =>
					'<label>' . esc_html__( 'Website ', 'jevelin' ) . ' <i class="icon-check sh-accent-color"></i></label>
					<p class="comment-form-url">
						<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '"  />
					</p>'
				)
			),
			'comment_field' =>  '<label>' . esc_html__( 'Message ', 'jevelin' ) . ' <i class="icon-check sh-accent-color"></i></label>
			<p class="comment-form-comment">
				<textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" required></textarea>
			</p>',
			'submit_field' => '<div class="sh-comments-required-notice">' . esc_html__( 'Required fields are marked', 'jevelin' ) . ' <i class="icon-check sh-accent-color"></i></div><p class="form-submit">%1$s %2$s</a>',
		)); ?>
	</div>
</div>
