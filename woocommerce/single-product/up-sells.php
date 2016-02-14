<?php
/**
 * Single Product Up-Sells
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop;

$upsells = $product->get_upsells();

if ( sizeof( $upsells ) == 0 ) {
	return;
}

$meta_query = WC()->query->get_meta_query();

$args = array(
	'post_type'           => 'product',
	'ignore_sticky_posts' => 1,
	'no_found_rows'       => 1,
	'posts_per_page'      => $posts_per_page,
	'orderby'             => $orderby,
	'post__in'            => $upsells,
	'post__not_in'        => array( $product->id ),
	'meta_query'          => $meta_query
);

$products = new WP_Query( $args );

$woocommerce_loop['columns'] = $columns;
if ( sizeof( $upsells ) == 1 ) {
	$columns = 1;
} elseif ( sizeof( $upsells ) == 2 ) {
	$columns = 2;
}

$product_item_coll_size = 12 / $columns;

if ( $products->have_posts() ) : ?>

	<div class="upsells products margin-top atgrid">

		<h2><?php esc_html_e( 'You may also like', 'adventure-tours' ) . '&hellip;'; ?></h2>

		<?php woocommerce_product_loop_start(); ?>

			<?php $item_index = 0; ?>
			<?php while ( $products->have_posts() ) : $products->the_post(); ?>
				<?php
					if ( $item_index > 0 && $item_index % $columns == 0 ) {
						echo '<div class="atgrid__row-separator atgrid__row-separator--related-and-upsells clearfix"></div>';
					}
					$item_index++;
				?>
				<div class="atgrid__item-wrap atgrid__item-wrap--related-and-upsells <?php print 'col-md-' . $product_item_coll_size; ?>">
					<?php wc_get_template_part( 'content', 'product' ); ?>
				</div>
			<?php endwhile; // end of the loop. ?>
		<?php woocommerce_product_loop_end(); ?>

	</div>

<?php endif;

wp_reset_postdata();
