<?php
/**
 * Wrapper for product loop used in WooCoomerce shortcodes for products rendering.
 *
 * @see       AtWoocommerceShortcodesHelper
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.2.1
 */

global $woocommerce_loop;

$template_file = adventure_tours_di( 'wc_shortcodes_helper' )->get_wrapped_template();
if ( ! $template_file ) {
	return;
}

$columns = isset( $woocommerce_loop['columns']) ? $woocommerce_loop['columns'] : 3;
$loop = isset( $woocommerce_loop['loop'] ) ? $woocommerce_loop['loop'] : 0;

if ( $columns < 1 || $columns > 4 ) {
	$columns = 3;
}

$item_class = '';
if ( ! $item_class ) {
	$item_class = 'atgrid__item-wrap atgrid__item-wrap--product col-xs-6 col-md-' . round(12 / $columns);
}

// $loop increases in content-product.php
if ( $loop > 0 && $loop % $columns == 0 ) {
	echo '<div class="atgrid__row-separator atgrid__row-separator--product clearfix hidden-sm hidden-xs"></div>';
}
if ( $loop > 0 && $loop % 2 == 0 ) {
	echo '<div class="atgrid__row-separator atgrid__row-separator--product clearfix visible-sm visible-xs"></div>';
}
?>

<div class="<?php echo esc_attr( $item_class ) ?>">
	<?php include $template_file; ?>
</div>