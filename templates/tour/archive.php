<?php
/**
 * Tour archive template.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.1.2
 */

get_header();

$sidebar_content = '';
if ( adventure_tours_get_option( 'tours_archive_show_sidebar', 1 ) ) {
	ob_start();
	do_action( 'woocommerce_sidebar' );
	$sidebar_content = ob_get_clean();
}

$is_first_page = get_query_var( 'paged' ) < 2;
$cat_term = is_tax() ? get_queried_object() : null;
$display_mode = AtTourHelper::get_tour_archive_page_display_mode( $cat_term ? $cat_term->term_id : 0 );
?>

<?php ob_start(); ?>
	<?php if ( $is_first_page ) {
		if ( $cat_term ) {
			echo wc_format_content( $cat_term->description );
		} elseif ( is_archive() ) {
			$tours_page_id = adventure_tours_get_option( 'tours_page' );
			$tours_page = $tours_page_id ? get_post( $tours_page_id ) : null;
			if ( $tours_page && $tours_page->post_content ) {
				echo wc_format_content( $tours_page->post_content );
			}
		}
	} ?>

	<?php if ( have_posts() ) : ?>

		<?php
			$need_show_categories = $is_first_page && in_array( $display_mode, array( 'both', 'subcategories' ) );
			$need_show_tours = in_array( $display_mode, array( 'products', 'both' ) );
			$tours_display_style = $need_show_tours
				? apply_filters( 'adventure_tours_get_tours_page_display_style', adventure_tours_get_option( 'tours_archive_display_style' ) )
				: '';
		?>

		<?php if ( $need_show_categories ) {
			if ( 'grid' == $tours_display_style ) {
				$categories_col_size = adventure_tours_get_option( 'tours_archive_columns_number', '2' );
			} else {
				$categories_col_size = $sidebar_content ? 2 : 3;
			}
			adventure_tours_di( 'register' )->setVar( 'tour_cat_columns', apply_filters( 'adventure_tours_tour_categories_columns', $categories_col_size, $sidebar_content ? true : false ) );

			adventure_tours_render_tour_categories(array(
				'before' => '<div class="row product-rategories">',
				'after' => '</div>',
			));
		} ?>

		<?php if ( $need_show_tours ) {
			if ( 'grid' == $tours_display_style ) {
				get_template_part( 'templates/tour/loop-grid' );
			} else {
				get_template_part( 'templates/tour/loop-list' );
			}
			adventure_tours_render_pagination( '<div class="margin-top">', '</div>' );
		} ?>
	<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>
		<?php wc_get_template( 'loop/no-products-found.php' ); ?>
	<?php endif; ?>
<?php $primary_content = ob_get_clean();  ?>

<?php adventure_tours_render_template_part('templates/layout', '', array(
	'content' => $primary_content,
	'sidebar' => $sidebar_content,
)); ?>

<?php get_footer(); ?>
