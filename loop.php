<?php
/**
 * Partial template used for looping through query results.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		$postType = get_post_type();
		switch ( $postType ) {
		case 'post':
			get_template_part( 'content', get_post_format() );
			break;

		case 'product':
			wc_get_template_part( 'content-product', get_post_format() );
			break;

		default:
			get_template_part( 'content', $postType );
			break;
		}
	}
	if ( ! is_single() ) {
		adventure_tours_render_pagination();
	}
} else {
	get_template_part( 'content', 'none' );
}
