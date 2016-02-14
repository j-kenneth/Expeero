<?php
/**
 * Display single product reviews (comments)
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     2.3.2
 */
global $product;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! comments_open() ) {
	return;
}
?>

<div id="comments" class="tour-reviews margin-top">
	<div class="section-title title title--small title--center title--decoration-bottom-center title--underline">
		<h3 class="title__primary"><?php esc_html_e( 'Tour Reviews', 'adventure-tours' ); ?></h3>
	</div>
	<?php
		if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' ) {
			$rating_count = $product->get_rating_count();
			$review_count = $product->get_review_count();
			$average = $product->get_average_rating();

			if ( $rating_count > 0 ) {
				echo '<div class="margin-left margin-right padding-top padding-bottom tour-reviews__rating-total">';
				adventure_tours_renders_stars_rating( ceil( $average ), array(
					'before' => '<div class="tour-reviews__rating-total__stars">',
					'after' => '</div>',
				) );
				echo '<div class="tour-reviews__rating-total__text">' .
					$average . ' ' .
					esc_html__( 'based on', 'adventure-tours' ) . ' ' .
					sprintf( _n( '1 review', '%s reviews', $review_count, 'adventure-tours' ), $review_count ) .
				'</div>';
				echo '</div>';
			}
		}
	?>
	<div class="tour-reviews__items">
	<?php if ( have_comments() ) : ?>
		<?php wp_list_comments( apply_filters( 'woocommerce_product_review_list_args', array( 'callback' => 'woocommerce_comments' ) ) ); ?>
	<?php else : ?>
		<p class="woocommerce-noreviews padding-all"><?php esc_html_e( 'There are no reviews yet.', 'adventure-tours' ); ?></p>
	<?php endif; ?>
	</div>

	<?php adventure_tours_comments_pagination(); ?>

	<?php if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->id ) ) : ?>
		<?php
			//$idWrapFormTourLeaveReview = 'tour-leave-review';
			//TdJsClientScript::addScript( 'initValidationTourLeaveReview', 'Theme.FormValidationHelper.initValidationForm("' . $idWrapFormTourLeaveReview . '");' );
			wp_enqueue_script( 'comment-reply' );
		?>
		<div id="tour-leave-review" class="tour-reviews__form padding-all">
			<h3 class="tour-reviews__form__title"><?php esc_html_e( 'Leave a Review', 'adventure-tours' ); ?></h3>
			<?php
				$commenter = wp_get_current_commenter();
	
				$comment_form = array(
					'title_reply' => '',
					'title_reply_to' => '',
					'comment_notes_before' => '',
					'comment_notes_after' => '',
					'fields' => array(
						'author' => '<div class="tour-reviews__form__item">' .
							'<input id="author" name="author" type="text" placeholder="' . esc_attr__( 'Name', 'adventure-tours' ) . '" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" class="form-validation-item">' .
						'</div>',
						'email' => '<div class="tour-reviews__form__item">' .
							'<input id="email" name="email" type="text" placeholder="' . esc_attr__( 'Email', 'adventure-tours' ) . '" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" class="form-validation-item">' .
						'</div>',
					),
					'class_submit' => 'atbtn',
					'label_submit' => esc_html__( 'Submit', 'adventure-tours' ),
					'comment_field' => ''
				);
	
				if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' ) {
					$comment_form['comment_field'] = '<div class="tour-reviews__form__rating">' .
						'<div class="tour-reviews__form__rating__label">' . esc_html__( 'Rating', 'adventure-tours' ) . '</div>' .
						'<select name="rating" id="rating">' .
							'<option value="">' . esc_html__( 'Rate&hellip;', 'adventure-tours' ) . '</option>' .
							'<option value="5">' . esc_html__( 'Perfect', 'adventure-tours' ) . '</option>' .
							'<option value="4">' . esc_html__( 'Good', 'adventure-tours' ) . '</option>' .
							'<option value="3">' . esc_html__( 'Average', 'adventure-tours' ) . '</option>' .
							'<option value="2">' . esc_html__( 'Not that bad', 'adventure-tours' ) . '</option>' .
							'<option value="1">' . esc_html__( 'Very Poor', 'adventure-tours' ) . '</option>' .
						'</select>' .
					'</div>';
				}
	
				$comment_form['comment_field'] .= '<div class="tour-reviews__form__item">' .
					'<textarea id="comment" name="comment" placeholder="' . esc_attr__( 'Your Review', 'adventure-tours' ) . '" class="form-validation-item"></textarea>' .
				'</div>';
	
				comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
			?>
		</div>
	<?php else : ?>
		<p class="woocommerce-verification-required padding-left padding-right padding-bottom"><?php esc_html_e( 'Only logged in customers who have purchased this product may leave a review.', 'adventure-tours' ); ?></p>
	<?php endif; ?>
</div>
