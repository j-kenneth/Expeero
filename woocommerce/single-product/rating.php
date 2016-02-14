<?php
/**
 * Single Product Rating
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     2.3.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
	return;
}

$rating_count = $product->get_rating_count();
$review_count = $product->get_review_count();
$average      = $product->get_average_rating();

if ( $rating_count > 0 ) : ?>

	<div class="woocommerce-product-rating">
		<div class="woocommerce-product-rating__stars" title="<?php printf( esc_html__( 'Rated %s out of 5', 'adventure-tours' ), $average ); ?>">
			<?php adventure_tours_renders_stars_rating( ceil( $average ) ); ?>
		</div>
		<?php if ( comments_open() ) : ?><a href="#shopreviews" class="woocommerce-review-link" rel="nofollow">(<?php printf( _n( '%s customer review', '%s customer reviews', $review_count, 'adventure-tours' ), '<span class="count">' . $review_count . '</span>' ); ?>)</a><?php endif ?>
	</div>

	<?php adventure_tours_render_template_part( 'templates/parts/scheme-rating', '', array( 'product' => $product ) ); ?>

<?php endif; ?>
