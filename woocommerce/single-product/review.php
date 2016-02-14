<?php
/**
 * Review Comments Template
 *
 * @author   WooThemes
 * @package  WooCommerce/Templates
 * @version  2.5.0
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;
$rating = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );
?>

<div id="comment-<?php comment_ID(); ?>" class="tour-reviews__item margin-left margin-right padding-top padding-bottom" itemscope itemtype="http://schema.org/Review">
	<span itemprop="itemReviewed" itemscope itemtype="http://schema.org/Product">
		<meta itemprop="name" content="<?php echo esc_html( get_the_title( $product->ID ) ); ?>">
	</span>
	<div class="tour-reviews__item__container">
		<div class="tour-reviews__item__info">
			<?php echo get_avatar( $comment, apply_filters( 'woocommerce_review_gravatar_size', '122' ), '' ); ?>
			<div class="tour-reviews__item__name" itemprop="author" itemscope itemtype="http://schema.org/Person"><span itemprop="name"><?php comment_author(); ?></span></div>
		</div>
		<div class="tour-reviews__item__content">
			<div class="tour-reviews__item__content__top">
				<?php do_action( 'woocommerce_review_before_comment_meta', $comment ); ?>
				<?php if ( $rating && get_option( 'woocommerce_enable_review_rating' ) == 'yes' ) : ?>
					<?php adventure_tours_renders_stars_rating( $rating, array(
						'before' => '<div class="tour-reviews__item__rating">',
						'after' => '</div>',
					) ); ?>
					<span itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
						<meta itemprop="ratingValue" content="<?php echo esc_html( $rating ); ?>">
					</span>
				<?php endif; ?>
				<div class="tour-reviews__item__date"><?php echo get_comment_date( wc_date_format() ); ?></div>
			</div>
			<?php do_action( 'woocommerce_review_before_comment_text', $comment ); ?>
			<div class="tour-reviews__item__text" itemprop="reviewBody"><?php comment_text(); ?></div>
			<?php do_action( 'woocommerce_review_after_comment_text', $comment ); ?>
		</div>
	</div>
<?php //echo '</div>'; // commented as closing tag will be added by Walker_Comment class ?>