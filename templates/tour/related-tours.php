<?php
/**
 * View that renders related tours.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.2.1
 */

$related_limit = 3;
$related_ids = $GLOBALS['product']->get_related( $related_limit );
if ( ! $related_ids ) {
	return;
}
?>
<div class="related-tours padding-top">
	<h2 style="margin:0 0 25px 0"><?php esc_html_e( 'You May Also Like', 'adventure-tours' ); ?></h2>
	<?php echo do_shortcode( '[tours_grid show_categories="" css_class="atgrid--small" description_words_limit="7" number="' . $related_limit . '" tour_ids="'.join( ',', $related_ids ).'"]' ); ?>
</div>
