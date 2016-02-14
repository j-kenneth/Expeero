<?php
/**
 * Shortcode [tour_reviews] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var string  $title
 * @var boolean $title_underline
 * @var string  $number
 * @var array   $reviews
 * @var string  $view
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.2.3
 */

if ( ! $reviews ) {
	return;
}
?>
<div class="shortcode-tour-reviews<?php if ( $css_class ) { echo esc_attr( ' ' . $css_class ); } ?>">
<?php if ( $title ) {
	echo do_shortcode( '[title text="' . $title . '" subtitle="" size="small" position="center" decoration="on" underline="' . $title_underline . '" style="dark"]' );
} ?>
<?php foreach ( $reviews as $review ) { ?>
	<div class="shortcode-tour-reviews__item">
		<div class="shortcode-tour-reviews__item__info">
			<?php echo get_avatar( $review->user_id > 0 ? $review->user_id : $review->comment_author_email, 95 ); ?>
			<div class="shortcode-tour-reviews__item__info__name"><?php echo esc_html( $review->comment_author ); ?></div>
		</div>
		<div class="shortcode-tour-reviews__item__content">
			<h3 class="shortcode-tour-reviews__item__title"><a href="<?php echo esc_url( get_permalink( $review->comment_post_ID ) ); ?>"><?php echo esc_html( $review->post_title ); ?></a></h3>
			<div class="shortcode-tour-reviews__item__description"><?php echo esc_html( $review->comment_content ); ?></div>
			<?php adventure_tours_renders_stars_rating( get_comment_meta( $review->comment_ID, 'rating', true ), array(
				'before' => '<div class="shortcode-tour-reviews__item__rating">',
				'after' => '</div>',
			) ); ?>
		</div>
	</div>
<?php } ?>
</div>
