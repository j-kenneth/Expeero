<?php
/**
 * Tour rating scheme rendering template part.
 *
 * @var object $product
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

if ( ! $product ) {
	return;
}

$rating_count = $product->get_review_count();
$rating_value = $product->get_average_rating();

if ( 'no' === get_option( 'woocommerce_enable_review_rating' ) || $rating_count < 1 || $rating_value < 1 ) {
	return;
}
?>

<span itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
	<meta itemprop="ratingValue" content="<?php echo esc_html( $rating_value ); ?>">
	<meta itemprop="reviewCount" content="<?php echo esc_html( $rating_count ); ?>">
</span>