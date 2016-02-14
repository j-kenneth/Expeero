<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php do_action( 'woocommerce_before_mini_cart' ); ?>

<div class="product_list_widget product_list_widget--cart">
	<ul class="<?php echo esc_attr( $args['list_class'] ); ?>">
		<?php if ( ! WC()->cart->is_empty() ) : ?>
			<?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) : ?>
				<?php
					$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
					$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
				?>
				<?php if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) : ?>
					<?php
						$product_name = apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
						$thumbnail = str_replace( array( 'http:', 'https:' ), '', apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key ) );
						$product_price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
					?>
					<li class="product_list_widget__item<?php echo ' ' . esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>">
						<?php printf( '<div class="product_list_widget__item__image">%s</div>', $thumbnail ); ?>
						<div class="product_list_widget__item__content">
							<div class="product_list_widget__item__title">
								<?php if ( ! $_product->is_visible() ) {
									print $product_name;
								} else {
									printf( '<a href="%s">%s</a>', esc_url( $_product->get_permalink( $cart_item ) ), $product_name );
								} ?>
							</div>
							<div class="product_list_widget__item__price">
								<?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); ?>
							</div>
							<?php
								echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
									'<a href="%s" class="product_list_widget__item__button" data-product_id="%s" data-product_sku="%s">%s</a>',
									esc_url( WC()->cart->get_remove_url( $cart_item_key ) ),
									esc_attr( $product_id ),
									esc_attr( $_product->get_sku() ),
									esc_html__( 'Remove', 'adventure-tours' )
								), $cart_item_key );
							?>
						</div>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php else : ?>
			<li class="empty"><?php esc_html_e( 'No products in the cart.', 'adventure-tours' ); ?></li>
		<?php endif; ?>
	</ul><!-- end product list -->
	
	<?php if ( ! WC()->cart->is_empty() ) : ?>
		<div class="product_list_widget__total">
			<?php esc_html_e( 'Subtotal', 'adventure-tours' ); ?>: <span class="product_list_widget__total__value"><?php echo WC()->cart->get_cart_subtotal(); ?></span>
		</div>
	
		<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>
	
		<div class="product_list_widget__buttons">
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="button"><?php esc_html_e( 'View Cart', 'adventure-tours' ); ?></a>
			<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="button"><?php esc_html_e( 'Checkout', 'adventure-tours' ); ?></a>
		</div>
	<?php endif; ?>
</div>

<?php do_action( 'woocommerce_after_mini_cart' ); ?> 
