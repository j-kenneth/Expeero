<?php
/**
 * Partial template used for looping through search results.
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

			case 'page':
			case 'product':
				get_template_part( 'templates/parts/search-result-block' );
			break;

			default:
				// Alrernative is: get_template_part( 'content', $postType ); .
				echo strtr('<div><h2><a href="{url}">{title}</a></h2></div>', array(
					'{url}' => get_permalink(),
					'{title}' => get_the_title(),
				));
			break;
		}
	}
	if ( ! is_single() ) {
		adventure_tours_render_pagination();
	}
} else {
	get_template_part( 'content', 'none' );
}
