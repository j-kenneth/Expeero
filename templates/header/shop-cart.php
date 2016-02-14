<?php
/**
 * Shopping cart icon rendring templated.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

if ( ! class_exists( 'WooCommerce' ) || ! adventure_tours_get_option( 'show_header_shop_cart' ) ) {
	return;
}

$cart_qty = WC()->cart->get_cart_contents_count()
?>
<div class="header__info__item header__info__item--delimiter header__info__item--shoping-cart">
	<a href="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>">
		<i class="fa fa-shopping-cart"></i>
		<?php if ( $cart_qty > 0 ) {
			echo '(' . esc_html( $cart_qty ) . ')';
		} ?>
	</a>
</div>
