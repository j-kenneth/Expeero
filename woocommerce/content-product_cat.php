<?php
/**
 * The template for displaying product category thumbnails within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product_cat.php
 *
 * @author   WooThemes
 * @package  WooCommerce/Templates
 * @version  2.5.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $woocommerce_loop;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) ) {
	$woocommerce_loop['loop'] = 0;
}

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) ) {
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
}

// Increase loop count
$woocommerce_loop['loop'] ++;

$category_link = get_term_link( $category->slug, 'product_cat' );

$columns = $woocommerce_loop['columns'];
if ( $columns < 2 ) {
	$columns = 2;
} else if ( $columns > 4 ) {
	$columns = 4;
}
$description = wc_format_content( $category->description );
$item_css_class = 'product-category-wrap col-xs-6 col-md-' . ( 12 / $columns );

$catIndex = adventure_tours_di( 'register' )->getVar( 'product_category_el_index', 0);
if ( $catIndex > 0 && 0 == ($catIndex % $columns) ) {
	echo '<div class="product-category__row-separator clearfix hidden-sm hidden-xs"></div>';
}
if ( $catIndex > 0 && 0 == ($catIndex % 2) ) {
	echo '<div class="product-category__row-separator clearfix visible-sm visible-xs"></div>';
}
$catIndex++;
adventure_tours_di( 'register' )->setVar( 'product_category_el_index', $catIndex );
?>
<div class="<?php echo esc_attr( $item_css_class ); ?>">
	<div class="product-category">
		<a href="<?php echo esc_url( $category_link ); ?>" class="product-category__image"><?php adventure_tours_render_category_thumbnail( $category ); ?></a>
		<div class="product-category__content">
			<?php printf('<h3 class="product-category__title"><a href="%s">%s</a></h3>',
					esc_url( $category_link ),
					esc_html( $category->name )
			); ?>
			<?php if ( $description ) {
				printf( '<div class="product-category__description">%s</div>', $description );
			} ?>
		</div>
		<div class="product-category__info">
			<?php if ( $category->count > 0 ) { ?>
				<div class="product-category__info__item"><?php echo esc_html( $category->count . ' ' . _n( 'product', 'products', $category->count, 'adventure-tours' ) ); ?></div>
			<?php } ?>
			<div class="product-category__info__item product-category__info__item--link">
				<a href="<?php echo esc_url( $category_link ); ?>"><?php esc_html_e('See products', 'adventure-tours'); ?><i class="fa fa-long-arrow-right"></i></a>
			</div>
		</div>
	</div>
</div>
