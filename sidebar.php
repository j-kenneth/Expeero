<?php
/**
 * Sidebar template file.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

$show_sidebar = is_active_sidebar( 'sidebar' );

$tour_search_form = adventure_tours_check( 'is_tour_search' ) ? adventure_tours_render_tour_search_form() : null;

if ( ! $show_sidebar && ! $tour_search_form ) {
	return;
}
?>
<aside class="col-md-3 sidebar" role="complementary">
<?php if ( $tour_search_form ) {
	print adventure_tours_render_tour_search_form();
} ?>
<?php dynamic_sidebar( 'sidebar' ); ?>
</aside>
