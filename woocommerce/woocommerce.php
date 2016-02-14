<?php
$wcIncludesFolder = dirname(__FILE__) . '/includes/';

require $wcIncludesFolder . 'WC_Product_Tour.php';
require $wcIncludesFolder . 'WC_Product_Tour_Variable.php';

require $wcIncludesFolder . 'WC_Tour_Integration_Helper.php';

// To init integration helper.
WC_Tour_Integration_Helper::getInstance();

function adventure_tours_init_select2() {
	wp_enqueue_script( 'select2' );
	wp_enqueue_style( 'select2', str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/' . 'css/select2.css' );
	// .shipping_method, #calc_shipping_state selectors for selects shipping methods
	// if elements not dom, select2 throw exception "Uncaught query function not defined for Select2" and rendering stopped for next elements in jQuery collections
	TdJsClientScript::addScript( 'initSelect2', 'jQuery(".country_to_state, .select2-selector").select2();' );
}

function woocommerce_cart_totals() {
	adventure_tours_init_select2();
	wc_get_template( 'cart/cart-totals.php' );
}

function adventure_tours_register_woocommerce_widgets() {
	require_once PARENT_DIR . '/woocommerce/widgets/adventure_tours_wc_widget_recent_reviews.php';

	register_widget( 'Adventure_Tours_WC_Widget_Recent_Reviews' );
}
add_action( 'widgets_init', 'adventure_tours_register_woocommerce_widgets' );
