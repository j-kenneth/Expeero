<?php
/**
 * Overriding woocommerce widget to replace view and exclude raviews related to the tour items.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

class Adventure_Tours_WC_Widget_Recent_Reviews extends WC_Widget_Recent_Reviews
{
	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {
		global $comments;

		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		$number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : $this->settings['number']['std'];
		$comments = get_comments( array(
			'number' => $number,
			'status' => 'approve',
			'post_status' => 'publish',
			'post_type' => 'product',

			// to exclude reviews related to tour items
			'meta_key' => 'is_tour_rating',
			'meta_compare' => 'NOT EXISTS',
		) );

		$content = adventure_tours_render_template_part( 'woocommerce/widgets/templates/recent_reviews', '', array(
			'args' => $args,
			'instance' => $instance,
			'comments' => $comments,
		), true );

		print $content;

		$this->cache_widget( $args, $content );
	}
}
