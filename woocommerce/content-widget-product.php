<?php
/**
 * The template for displaying product widget entries
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-widget-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	http://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.5.0
 */

global $product;
$product_permalink = get_permalink( $product->id );
?>
<li class="product_list_widget__item">
	<?php printf( '<div class="product_list_widget__item__image">%s</div>', $product->get_image() ); ?>
	<div class="product_list_widget__item__content">
		<div class="product_list_widget__item__title">
			<a href="<?php echo esc_url( $product_permalink ); ?>"><?php echo esc_html( $product->get_title() ); ?></a>
		</div>
		<?php printf( '<div class="product_list_widget__item__price">%s</div>', $product->get_price_html() ); ?>
		<?php if ( ! empty( $show_rating ) ) { ?>
			<?php
				$average = $product->get_average_rating();
				adventure_tours_renders_stars_rating( ceil( $average ), array(
					'before' => '<div class="product_list_widget__item__rating">',
					'after' => '</div>',
				) );
			?>
		<?php } else { ?>
			<a href="<?php echo esc_url( $product_permalink ); ?>" class="product_list_widget__item__button"><?php esc_html_e( 'View', 'adventure-tours' ); ?></a>
		<?php } ?>
	</div>
</li>