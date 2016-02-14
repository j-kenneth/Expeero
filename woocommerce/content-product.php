<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author   WooThemes
 * @package  WooCommerce/Templates
 * @version  2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! AtTourHelper::beforeWCTemplateRender( __FILE__ ) ) {
	return;
}

global $product, $woocommerce_loop;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) ) {
	$woocommerce_loop['loop'] = 0;
}

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) ) {
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
}

// Ensure visibility
if ( ! $product || ! $product->is_visible() ) {
	return;
}

// Increase loop count
$woocommerce_loop['loop']++;

$price_html = $product->get_price_html();
?>

<div class="atgrid__item">
	<div class="atgrid__item__top">
		<a href="<?php the_permalink(); ?>" class="atgrid__item__top__image"><?php echo woocommerce_get_product_thumbnail(); ?></a>
	<?php if ( $product->is_on_sale() ) { ?>
		<div class="atgrid__item__angle-wrap"><div class="atgrid__item__angle"><?php esc_html_e( 'On Sale', 'adventure-tours' ); ?></div></div>
	<?php } ?>
		<div class="atgrid__item__price">
		<?php if ( $price_html ) {
			printf( '<a href="%s" class="atgrid__item__price__button">%s</a>',
				esc_url( get_permalink() ),
				$price_html
			);
		} ?>
		</div>
	</div>
	<div class="atgrid__item__content">
		<h3 class="atgrid__item__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<div class="atgrid__item__description"><?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ) ?></div>
	</div>
	<div class="item-attributes">
		<?php adventure_tours_render_product_attributes(array(
			'before_each' => '<div class="item-attributes__item">',
			'after_each' => '</div>',
			'limit' => 3,
		)) ?>
		<div class="item-attributes__item"><a href="<?php the_permalink(); ?>" class="item-attributes__link"><?php esc_html_e('view', 'adventure-tours'); ?><i class="fa fa-long-arrow-right"></i></a></div>
	</div>
</div>
