<?php
/**
 * Comments generation template part.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

if ( ! comments_open() && get_comments_number() < 1 ) {
	return;
}

if ( ! function_exists( 'adventure_tours_comment_form_renderer' ) ) {
	/**
	 * Comment form renderer function.
	 *
	 * @return void
	 */
	function adventure_tours_comment_form_renderer() {
		$commenter = wp_get_current_commenter();
		$fields = array(
			'author' => '<div class="row"><div class="col-sm-4">' .
				'<label for="author">' . esc_html__( 'Name', 'adventure-tours' ) . '*</label>' .
				'<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" />' .
			'</div>',
			'email' => '<div class="col-sm-4">' .
				'<label for="email">' . esc_html__( 'Email', 'adventure-tours' ) . '*</label>' .
				'<input id="email" name="email" type="text" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" />' .
			'</div>',
			'url' => '<div class="col-sm-4">' .
				'<label for="url">' . esc_html__( 'Website', 'adventure-tours' ) . '</label>' .
				'<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" />' .
			'</div></div>',
		);
		$args = array(
			'fields' => apply_filters( 'comment_form_default_fields', $fields ),
			'comment_notes_before' => '',
			'comment_notes_after' => '',
			'comment_field' => '<label for="comment">' . esc_html__( 'Comment', 'adventure-tours' ) . '</label><textarea id="comment" name="comment"></textarea>',
			'label_submit' => '',
			'cancel_reply_link' => '<i class="fa fa-times"></i>',
		);
		ob_start();
		comment_form( $args );
		$formHtml = '<div class="comments__form">' . ob_get_clean() . '</div>';
		wp_enqueue_script( 'comment-reply' );
		echo str_replace( '<input name="submit" type="submit" id="submit" class="submit" value="" />','<button type="submit" class="atbtn"><i class="atbtn__icon fa fa-comment"></i>' . esc_html__( 'Post Comment', 'adventure-tours' ) . '</button>', $formHtml );
	}
}

if ( ! function_exists( 'adventure_tours_comment_renderer' ) ) {
	/**
	 * Comment renderer function.
	 *
	 * @param  Comment $comment comment instance.
	 * @param  array   $args    array of options.
	 * @param  int     $depth   current depth level.
	 * @return void
	 */
	function adventure_tours_comment_renderer($comment, $args, $depth) {
		$commentHtml = get_avatar( $comment, 90 ) .
			'<div class="comments__item__info">' .
				'<div class="comments__item__name">' . get_comment_author_link() . '</div>' .
				'<div class="comments__item__reply-link">' .
					get_comment_reply_link(array(
						'depth' => $depth,
						'max_depth' => $args['max_depth'],
						'reply_text' => esc_html__( 'Reply', 'adventure-tours' ),
						'login_text' => '',
					)) .
				'</div>' .
			'</div>' .
			'<div class="comments__item__date">' . get_comment_date() . '</div>' .
			'<div class="comments__item__text">' . get_comment_text() . '</div>';

		printf( '<div class="%s" id="comment-%s">%s%s',
			implode( ' ', get_comment_class( 'comments__item' ) ),
			get_comment_ID(),
			$commentHtml,
			! empty( $args['has_children'] ) ? '</div><div class="comments__item__reply">' : ''
		);
	}
}

echo '<div class="comments margin-top" id="comments">';
echo '<div class="section-title title title--small title--center title--decoration-bottom-center title--underline">' .
	'<h3 class="title__primary">' . esc_html__( 'Comments', 'adventure-tours' ) . '</h3>' .
'</div>';
if ( have_comments() ) {
	echo '<div class="padding-all comments-wrap">';
	wp_list_comments( array(
		'style' => 'div',
		'callback' => 'adventure_tours_comment_renderer',
		'type' => 'all',
		'avatar_size' => 60,
	) );
	echo '</div>';
	adventure_tours_comments_pagination();
};
adventure_tours_comment_form_renderer();
echo '</div><!-- end .comments -->';
