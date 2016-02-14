<?php
/**
 * Post navigation template part.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

$prev_link = get_previous_post_link('%link',
	'<i class="fa fa-chevron-left"></i>' .
	'<span class="post-navigation__title">' . esc_html__( 'Previous post', 'adventure-tours' ) . '</span>' .
	'<span class="post-navigation__post-name">%title</span>'
);

$next_link = get_next_post_link('%link',
	'<i class="fa fa-chevron-right"></i>' .
	'<span class="post-navigation__title">' . esc_html__( 'Next post', 'adventure-tours' ) . '</span>' .
	'<span class="post-navigation__post-name">%title</span>'
);

$has_any_link = $prev_link || $next_link;
if ( ! $has_any_link ) {
	return;
}
?>
<div class="post-navigation margin-top">
<?php
	printf('<div class="post-navigation__prev%s">%s</div>',
		$next_link ? '' : ' post-navigation__full-item',
		$prev_link
	);

	printf('<div class="post-navigation__next%s">%s</div>',
		$prev_link ? '' : ' post-navigation__full-item',
		$next_link
	);
?>
</div>
