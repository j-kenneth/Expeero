<?php
/**
 * Sidebar template used for woocommerce related page.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.9
 */

$is_tour_query = adventure_tours_check( 'is_tour_search' );
$current_product = is_single() ? wc_get_product() : null;
$is_single_tour = $current_product && $current_product->is_type('tour');
$is_tour_category = is_tax('tour_category');
$show_booking_form = $is_single_tour;

$sidebar_id = $is_tour_query || $is_single_tour || $is_tour_category ? 'tour-sidebar' : 'shop-sidebar';
$show_sidebar = is_active_sidebar( $sidebar_id );

$tour_search_form = $is_tour_query && adventure_tours_get_option( 'tours_archive_show_search_form', 1 ) ? adventure_tours_render_tour_search_form() : null;

if ( ! $show_sidebar && ! $show_booking_form && ! $tour_search_form ) {
	return;
}
?>
<aside class="col-md-3 sidebar" role="complementary">
<?php if ( $tour_search_form ) {
	print adventure_tours_render_tour_search_form();
} ?>
<?php if ( $show_booking_form ) {
	get_template_part( 'templates/tour/price-decoration' );
	echo adventure_tours_render_tour_booking_form( $current_product );
} ?>
<?php if ( $show_sidebar ) {
	dynamic_sidebar( $sidebar_id );
} ?>
</aside>
