<?php
/**
 * Single Product tabs
 *
 * @author   WooThemes
 * @package  WooCommerce/Templates
 * @version  2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Filter tabs and allow third parties to add their own
 *
 * Each tab is an array containing title, callback and priority.
 * @see woocommerce_default_product_tabs()
 */
$tabs = apply_filters( 'woocommerce_product_tabs', array() );

if ( empty($tabs) ) {
	return;
}
$tab_keys = array_keys( $tabs );
$active_tab_key = array_shift( $tab_keys );
?>
<div id="shopreviews" class="tours-tabs">

	<ul class="nav nav-tabs">
	<?php foreach ( $tabs as $key => $tab ) {
		printf( '<li%s><a href="#tab%s" data-toggle="tab">%s</a></li>',
			$key == $active_tab_key ? ' class="active"' : '',
			$key,
			apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key )
		);
	}; ?>
	</ul>

	<div class="tab-content">
		<?php foreach ( $tabs as $key => $tab ) {
			if ( empty( $tab['content'] ) && ! empty( $tab['callback'] ) ) { 
				ob_start();
				call_user_func( $tab['callback'], $key, $tab );
				$tab['content'] = ob_get_clean();
			}

			printf(
				'<div class="tab-pane %s" id="tab%s">' . 
					'<div class="tours-tabs__content padding-all">%s</div>' .
				'</div>',
				$key == $active_tab_key ? 'in active' : 'fade',
				$key,
				$tab['content']
			);
		} ?>
	</div>
</div>
