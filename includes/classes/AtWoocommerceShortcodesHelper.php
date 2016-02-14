<?php
/**
 * Redirects templates inside Woocommerce shortcodes to fix issues related on the loop rendering.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.2.1
 */

class AtWoocommerceShortcodesHelper
{
	/**
	 * List of the WooCommerce shortcodes that should be redefined to fix output.
	 *
	 * @var array
	 */
	public $revrite_shortcodes = array(
		// shortcode => function
		'product' => 'shortcode_product',
	);

	/**
	 * Array of hoocs that used in WooCommerce shortcodes related on the product loop function.
	 * Used to apply wrapper around product template (to implement right columns processing).
	 *
	 * @var array
	 */
	public $product_loop_shortcodes = array(
		'product_cat',     // used in 'product_category' shortcode
		'products',
		'recent_products',
		'sale_products',
		'best_selling_products',
		'top_rated_products',
		'featured_products',
		'product_attribute',
	);

	public $view_wrappers = array(
		'content-product' => 'woocommerce/shortcodes/content-product-wrapper.php'
	);

	private $wrappers_cache = array();

	private $wrapped_template;

	public function __construct() {
		if ( ! is_admin() ) {
			$this->init();
		}
	}

	protected function init() {
		if ( $this->product_loop_shortcodes ) {
			foreach ( $this->product_loop_shortcodes as $sc_name ) {
				add_action( 'woocommerce_shortcode_before_' . $sc_name . '_loop', array( $this, 'wc_shortcode_before_loop' ) );
				add_action( 'woocommerce_shortcode_after_' . $sc_name . '_loop', array( $this, 'wc_shortcode_after_loop' ) );
			}
		}

		$this->init_shortcodes();
	}

	protected function init_shortcodes() {
		$mapper = $this->revrite_shortcodes;
		if ( empty( $mapper ) || ! is_array( $mapper ) ) {
			return;
		}

		foreach( $mapper as $shortcode_name => $function ) {
			/*if ( ! method_exists( $this, $function ) ) {
				continue;
			}*/
			add_shortcode( $shortcode_name, array( $this, $function ) );
		}
	}

	public function wc_shortcode_before_loop() {
		$this->activate_wrapper();
	}

	public function wc_shortcode_after_loop() {
		$this->activate_wrapper( false );
	}

	protected function activate_wrapper( $add = true ) {
		$filter_name = 'wc_get_template_part';
		$filter = array( $this, 'filter_product_content_template' );
		if ( $add ) {
			add_filter( $filter_name, $filter, 10, 3 );
		} else {
			remove_filter( $filter_name, $filter, 10 );
			$this->set_wrapped_template( null );
		}
	}

	public function filter_product_content_template( $template, $slug, $name ) {
		$full_name = $slug . '-' . $name;

		if ( isset( $this->view_wrappers[ $full_name ] ) ) {
			if ( !isset( $this->wrappers_cache[ $full_name ] ) ) {
				$this->wrappers_cache[ $full_name ] = locate_template( $this->view_wrappers[ $full_name ], false, false );
			}

			$this->set_wrapped_template( $template );

			return $this->wrappers_cache[ $full_name ];
		}

		return $template;
	}

	protected function set_wrapped_template( $template ) {
		$this->wrapped_template = $template;
	}

	public function get_wrapped_template() {
		return $this->wrapped_template;
	}

	/**
	 * New code of the [product] shortcode.
	 *
	 * @param  assoc $atts
	 * @return string
	 */
	public function shortcode_product( $atts ) {
		$result = WC_Shortcodes::product( $atts );

		if ( $result ) {
			$new_result = preg_replace(
				'/^<div class="woocommerce">\s*<div class="row">/',
				'<div class="woocommerce"><div class="row"><div class="col-md-12">',
				trim( $result )
			);

			if ( $new_result != $result ) {
				return $new_result . '</div>';
			}
		}

		return $result;
	}
}
