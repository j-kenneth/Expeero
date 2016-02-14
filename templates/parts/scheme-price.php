<?php
/**
 * Tour price scheme rendering template part.
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

?>

<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
	<meta itemprop="price" content="<?php echo esc_attr( $product->get_price() ); ?>" />
	<meta itemprop="priceCurrency" content="<?php echo esc_attr(get_woocommerce_currency() ); ?>" />
	<link itemprop="availability" href="http://schema.org/<?php printf( '%s', $product->is_in_stock() ? 'InStock' : 'OutOfStock' ); ?>" />
</span>