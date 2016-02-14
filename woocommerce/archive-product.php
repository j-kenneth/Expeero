<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive.
 *
 * Override this template by copying it to yourtheme/woocommerce/archive-product.php
 *
 * @author   WooThemes
 * @package  WooCommerce/Templates
 * @version  2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! AtTourHelper::beforeWCTemplateRender( __FILE__ ) ) {
	return;
}

global $woocommerce_loop;

get_header( 'shop' );

ob_start();
do_action( 'woocommerce_sidebar' );
$sidebar_content = ob_get_clean();
?>

<?php ob_start(); ?>
	<?php
		/**
		 * woocommerce_before_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked woocommerce_breadcrumb - 20
		 */
		do_action( 'woocommerce_before_main_content' );

		$columns = $sidebar_content ? 2 : 3;
		$woocommerce_loop['columns'] = $columns;
		$coll_class = 'atgrid__item-wrap atgrid__item-wrap--product col-xs-6 col-md-' . round(12 / $columns);
		$counter = 0;
	?>

	<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
		<h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
	<?php endif; ?>

	<?php do_action( 'woocommerce_archive_description' ); ?>

	<?php if ( have_posts() ) : ?>

	<?php
		woocommerce_product_subcategories(array(
			'before' => '<div class="row product-rategories">',
			'after' => '</div>',
		));
	?>
		<div class="row<?php echo 'both' == adventure_tours_get_shop_page_display_mode() ? ' atgrid-sorting': ''; ?>"><div class="col-xs-12"><?php do_action( 'woocommerce_before_shop_loop' ); ?></div></div>
		<?php woocommerce_product_loop_start(); ?>
		<div class="atgrid">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php
					if ( $counter > 0 && $counter % $columns == 0 ) {
						echo '<div class="atgrid__row-separator atgrid__row-separator--product clearfix hidden-sm hidden-xs"></div>';
					}
					if ( $counter > 0 && $counter % 2 == 0 ) {
						echo '<div class="atgrid__row-separator atgrid__row-separator--product clearfix visible-sm visible-xs"></div>';
					}
					$counter++;
				?>
				<div class="<?php echo esc_attr( $coll_class ); ?>">
					<?php wc_get_template_part( 'content', 'product' ); ?>
				</div>
			<?php endwhile; // end of the loop. ?>
		</div>
		<?php woocommerce_product_loop_end(); ?>
		<?php do_action( 'woocommerce_after_shop_loop' ); ?>
	<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>
		<?php wc_get_template( 'loop/no-products-found.php' ); ?>
	<?php endif; ?>
	<?php
		/**
		 * woocommerce_after_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
	?>
<?php $primary_content = ob_get_clean();  ?>

<?php adventure_tours_render_template_part('templates/layout', '', array(
	'content' => $primary_content,
	'sidebar' => $sidebar_content,
)); ?>

<?php get_footer( 'shop' ); ?>
