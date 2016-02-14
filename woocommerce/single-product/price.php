<?php
/**
 * Single Product Price, including microdata for SEO
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     2.4.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;
$price_html = $product->get_price_html();
?>
<div>
<?php
	if ( $price_html ) {
		printf( '<p class="price">%s</p>', $price_html );
	}

	adventure_tours_render_template_part( 'templates/parts/scheme-price', '', array( 'product' => $product ) );
?>
</div>
