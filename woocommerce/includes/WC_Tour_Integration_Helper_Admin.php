<?php
/**
 * Implements hooks for integration the tour entity with woocommerce plugin.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.3.2
 */

class WC_Tour_Integration_Helper_Admin
{
	public static $booking_form_nonce_field_name = 'ncs';
	public static $booking_form_nonce_key = 'save_tour_booking';

	protected $_report_path_filter_is_added = false;

	/**
	 * Cache option used for detection events when rewrite rules should be flushed.
	 *
	 * @var string
	 */
	private $cached_tours_base_rewrite_rule;

	public function __construct() {
		$this->init();
	}

	protected function init() {
		add_action( 'init', array( $this, 'action_init' ) );

		add_filter( 'product_type_selector', array( $this, 'filter_product_type' ) );
		add_filter( 'product_type_options', array( $this, 'filter_product_type_options' ) );
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'filter_product_data_tabs' ), 20 );
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'action_general_product_data_tab' ) );

		// tour booking periods management implementation
		add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'filter_woocommerce_product_write_panel_tabs' ), 6 );
		add_action( 'woocommerce_product_write_panels', array( $this, 'filter_woocommerce_product_write_panels' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'filter_woocommerce_process_product_meta' ), 20 );
		add_action( 'wp_ajax_save_tour_booking_periods', array( $this, 'ajax_action_save_tour_booking_periods'), 20 );
		add_action( 'wp_ajax_preview_booking_periods', array( $this, 'ajax_action_preview_booking_periods'), 20 );

		add_filter( 'custom_menu_order', array( $this, 'filter_custom_menu_order' ), 20 );
		add_filter( 'product_rewrite_rules', array( $this, 'filter_product_rewrite_rules' ), 20 );
		add_filter( 'rewrite_rules_array', array( $this, 'filter_rewrite_rules_array' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'filter_admin_enqueue_scripts' ) );

		add_action( 'woocommerce_process_product_meta', array( $this, 'action_woocommerce_process_product_meta' ), 1, 2 );

		add_filter( 'woocommerce_admin_reports', array( $this, 'filter_woocommerce_admin_reports' ) );
	}

	public function action_init() {
		if ( '' != get_option( 'permalink_structure' ) ) {
			$this->cached_tours_base_rewrite_rule =  AtTourHelper::get_tour_base_rewrite_rule();

			add_action( 'save_post', array( $this, 'action_save_post_tour_page' ) );

			add_action( 'vp_option_save_and_reinit', array( $this, 'check_tours_base_change' ), 20 );
		}
	}

	/**
	 * Hook for 'save_post' action.
	 *
	 * @param  int  $post_id
	 * @return void
	 */
	public function action_save_post_tour_page( $post_id ) {
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		// if saved post is 'Tours' page - maybe we need flush rewrite rules (slug may be updated)
		if ( adventure_tours_get_option( 'tours_page' ) == $post_id ) {
			$this->check_tours_base_change();
		}
	}

	/**
	 * Flushs rewrite rules if 'Tours' page slug has been updated.
	 *
	 * @return void
	 */
	public function check_tours_base_change() {
		if ( $this->cached_tours_base_rewrite_rule != AtTourHelper::get_tour_base_rewrite_rule( false, true ) ) {
			flush_rewrite_rules();
		}
	}

	public function filter_product_type( $types ) {
		$types['tour'] = esc_html__( 'Tour', 'adventure-tours' );
		return $types;
	}

	public function filter_product_data_tabs( $tabs ) {
		array_push( $tabs['shipping']['class'], 'hide_if_tour' );
		array_push( $tabs['inventory']['class'], 'show_if_tour' );
		return $tabs;
	}

	/**
	 * Used to make available price and inventory inputs.
	 *
	 * @return void
	 */
	public function action_general_product_data_tab() {
		$disable_tour_variable_checkbox = $this->is_product_translation();

		echo <<<SCRIPT
<script>
	//woocommerce_added_attribute - event should be processed as well
	var cont = jQuery('#woocommerce-product-data'),
		tour_variable_switcher = cont.find("#_variable_tour"),
		allSimplePanels = cont.find(".show_if_simple:not(.tips)"),
		allVariablePanels = cont.find(".show_if_variable");

		allSimplePanels.addClass("show_if_tour");

	tour_variable_switcher.on("change", function(){
		var product_type = jQuery( "select#product-type" ).val();
		if ( "tour" != product_type ) {
			return;
		}

		var isVariableTour = jQuery(this).is(":checked"),
			pricePanels = allSimplePanels.filter(".options_group.pricing"),
			attributesPanel = jQuery("#product_attributes .product_attributes");

		if ( isVariableTour ) {
			allVariablePanels.addClass("show_if_tour").show();
			pricePanels.removeClass("show_if_tour").hide();

			attributesPanel.find(".enable_variation")
				.removeClass("enable_variation")
				.addClass("enable_variation_veriable_tour")
				.show();
		} else {
			allVariablePanels.removeClass("show_if_tour").hide();
			pricePanels.addClass("show_if_tour").show();

			attributesPanel.find(".enable_variation_veriable_tour")
				.removeClass("enable_variation_veriable_tour")
				.addClass("enable_variation")
				.hide();
		}
	}).trigger("change");

	cont.find(".product_data_tabs .attribute_options a").on("click",function(){
		tour_variable_switcher.trigger("change");
	});
	jQuery(function(){
		cont.find(".add_attribute").on( "click", function(){
			setTimeout(function(){
				tour_variable_switcher.trigger("change");
			},100);
		});
	});

	var disableVariableTourCheckbox = function(disabled){
		jQuery('#_variable_tour').prop('disabled', disabled ? true : false);
	};
	disableVariableTourCheckbox({$disable_tour_variable_checkbox});
</script>
SCRIPT;
	}

	/**
	 * Adds 'variable_tour' option for tours.
	 *
	 * @param  assoc $options
	 * @return assoc
	 */
	public function filter_product_type_options( $options ) {
		$options['variable_tour'] = array(
			'id'            => '_variable_tour',
			'wrapper_class' => 'show_if_tour',
			'label'         => esc_html__( 'Variable Tour', 'adventure-tours' ),
			'description'   => esc_html__( 'Check this is your tour has different options.', 'adventure-tours' ),
			'default'       => 'no',
		);

		return $options;
	}

	/**
	 * Saves tour related meta.
	 * Changes product type for variable tours.
	 *
	 * @param  sting  $post_id
	 * @param  object $post
	 * @return void
	 */
	public function action_woocommerce_process_product_meta( $post_id, $post ) {
		$product_type = empty( $_POST['product-type'] ) ? 'simple' : sanitize_title( stripslashes( $_POST['product-type'] ) );

		if ( 'tour' == $product_type ) {
			$is_variable = isset( $_POST['_variable_tour'] ) ? 'yes' : 'no';
			update_post_meta( $post_id, '_variable_tour', $is_variable );

			if ( 'yes' == $is_variable ) {
				// HACK, to process product as variable and will return it back via 'fix_tour_meta' method
				$_POST['product-type'] = 'variable';
				add_action( 'woocommerce_process_product_meta', array( $this, 'fix_tour_meta' ), 11, 2 );
			}
		}
	}

	public function fix_tour_meta( $post_id, $post ) {
		$set_type_to = 'tour';
		wp_set_object_terms( $post_id, $set_type_to, 'product_type' );

		do_action( 'woocommerce_process_product_meta_' . $set_type_to, $post_id );
	}

	/**
	 * Filter function for 'custom_menu_order' filter.
	 * Used for adding new items to 'Products' section and making custom order for them.
	 *
	 * @param  boolean $order flag that indicates that custom order should be used.
	 * @return boolean
	 */
	public function filter_custom_menu_order( $order ) {
		$icons_storage = adventure_tours_di( 'product_attribute_icons_storage' );
		if ( $icons_storage && $icons_storage->is_active() ) {
			include_once dirname( __FILE__ ) . '/WC_Admin_Attributes_Extended.php';
			$extender = new WC_Admin_Attributes_Extended(array(
				'storage' => $icons_storage,
			));
			$extender->hook();
		}

		global $submenu;

		if ( ! empty( $submenu['edit.php?post_type=product'] ) ) {
			$productsMenu = &$submenu['edit.php?post_type=product'];
			array_unshift($productsMenu, array(
				esc_html__( 'Tours', 'adventure-tours' ),
				'edit_products',
				'edit.php?post_type=product&product_type=tour&is_tours_management=1',
			));
		}

		// if currently loaded page is tours management section - adding js that highlight it as active menu item
		// as WP does not provide any other way to have few edit section for same custom post type
		// need improve this
		if ( ! empty( $_GET['is_tours_management'] ) ) {
			TdJsClientScript::addScript( 'activateTourItemMenu', $this->generate_tour_activation_js() );
		} else {
			add_filter('admin_footer-post.php', array( $this, 'filter_admin_footer_for_menu_activation' ) );
		}

		return $order;
	}

	public function filter_admin_footer_for_menu_activation(){
		if ( !empty($_GET['action']) && 'edit' == $_GET['action'] && 'product' == get_post_type() ) {
			$p = wc_get_product( get_post() );
			if ( $p && $p->is_type( 'tour' ) ) {
				echo '<script>jQuery(function(){'. $this->generate_tour_activation_js() .'});</script>';
			}
		}
	}

	protected function generate_tour_activation_js(){
		return <<<SCRIPT
		var activeLi = jQuery("#adminmenu").find("li.current"),
			newActiveLi = activeLi.parent().find("a[href$=\'is_tours_management=1\']").parent();
		if (newActiveLi.length) {
			activeLi.removeClass("current")
				.find("a.current").removeClass("current");
			newActiveLi.addClass("current")
				.find("a").addClass("current");
		}
SCRIPT;
	}

	/**
	 * Tour special permalink filter.
	 * Add spec rule for tour items.
	 *
	 * @param  assoc $rules wp defined urls.
	 * @return assoc
	 */
	public function filter_product_rewrite_rules( $rules ) {
		$tour_base_url = ltrim( AtTourHelper::get_tour_base_rewrite_rule( true, true ), '/' );
		if ( $tour_base_url ) {
			$new_rules = array(
				$tour_base_url . '([^/]+)/comment-page-([0-9]{1,})/?' => 'index.php?product=$matches[1]&cpage=$matches[2]',
				$tour_base_url . '(.+)' => 'index.php?product=$matches[1]',
			);
			return array_merge( $new_rules, $rules );
		}
		return $rules;
	}

	/**
	 * Creates special rewrite url for tours archive section.
	 *
	 * @param  assoc $rules
	 * @return void
	 */
	public function filter_rewrite_rules_array( $rules ) {
		$tour_base_url = AtTourHelper::get_tour_base_rewrite_rule( false, true );
		if ( $tour_base_url ) {
			$new_rules = array();
			$new_rules[ $tour_base_url . 'page/([0-9]{1,})/?' ] = 'index.php?toursearch=1&paged=$matches[1]';
			$new_rules[ $tour_base_url . '?$' ] = 'index.php?toursearch=1';// &post_type=product&product_type=tour
			return array_merge( $new_rules, $rules );
		}

		return $rules;
	}

/*** Tour Booking tab management implementation [start] ***/
	/**
	 * Renders tab name to list of tabs in on the product management page.
	 *
	 * @return void
	 */
	public function filter_woocommerce_product_write_panel_tabs() {
		echo '<li class="advanced_options show_if_tour"><a href="#tour_booking_tab">' . esc_html__( 'Tour Booking', 'adventure-tours' ) . '</a></li>';
	}

	public function is_product_translation() {
		static $result;

		if ( null === $result ) {
			if ( adventure_tours_check( 'is_wpml_in_use' ) ) {
				$result = ! defined( 'ICL_LANGUAGE_CODE') || ICL_LANGUAGE_CODE == apply_filters( 'wpml_default_language', '' ) ? false : true;
			} else {
				$result = false;
			}
		}

		return $result;
	}

	/**
	 * Renders Tour Booking management tab on the product management page.
	 *
	 * @return void
	 */
	public function filter_woocommerce_product_write_panels() {
		wp_enqueue_script( 'theme-tools', PARENT_URL . '/assets/js/ThemeTools.js', array('jquery'), '1.0.0' );
		wp_enqueue_script( 'tour-booking-tab', PARENT_URL . '/assets/js/AdminTourBookingTab.js', array('jquery'), '1.0.0' );

		global $post;
		adventure_tours_render_template_part( 'templates/admin/tour-booking-tab', '', array(
			'periods' => adventure_tours_di( 'tour_booking_service' )->get_rows( $post->ID ),
			'product_translation' => $this->is_product_translation(),
			'disable_ajax_saving' => adventure_tours_check( 'is_wpml_in_use' ),
			'nonce_field' => array(
				'name' => self::$booking_form_nonce_field_name,
				'value' => self::$booking_form_nonce_key,
			),
		) );
	}

	/**
	 * Filter called by woocommerce on the product data saving event.
	 * Saves tour booking periods.
	 *
	 * @param  int $post_id
	 * @return void
	 */
	public function filter_woocommerce_process_product_meta( $post_id ) {
		if ( ! isset( $_POST['tour-booking-row'] ) ) {
			return;
		}
		$this->save_booking_rows( $post_id, $_POST['tour-booking-row'] );
	}

	/**
	 * Ajax action used for saving tour booking periods data.
	 *
	 * @return void
	 */
	public function ajax_action_save_tour_booking_periods() {
		//need implement nonce field
		$post_id = isset( $_POST['booking_tour_id'] ) ? $_POST['booking_tour_id'] : null;
		$rows = isset( $_POST['tour-booking-row'] ) ? $_POST['tour-booking-row'] : array();
		$nonce = isset( $_POST[self::$booking_form_nonce_field_name] ) ? $_POST[self::$booking_form_nonce_field_name] : null;

		$response = array(
			'success' => false,
		);

		if ( $post_id && wp_verify_nonce( $nonce, self::$booking_form_nonce_key ) ) {
			$saving_errors = $this->save_booking_rows( $post_id, $rows );
			if ( empty( $saving_errors ) ) {
				$response['success'] = true;
			} else {
				$response['errors'] = $saving_errors;
			}
		} else {
			$response['errors'] = array(
				'general' => array(
					esc_html__( 'Parameters error. Please contact support.', 'adventure-tours' ),
				)
			);
		}

		wp_send_json( $response );
	}

	/**
	 * Ajax action used by 'Preview Calendar' functionality on the tour booking management tab.
	 *
	 * @return void
	 */
	public function ajax_action_preview_booking_periods() {
		//need implement nonce field
		$post_id = isset( $_POST['booking_tour_id'] ) ? $_POST['booking_tour_id'] : null;
		$rows = isset( $_POST['tour-booking-row'] ) ? $_POST['tour-booking-row'] : null;

		$result = adventure_tours_di( 'tour_booking_service' )->expand_periods( $rows, $post_id );

		$response = array(
			'success' => true,
			'data' => $result
		);

		wp_send_json( $response );
	}

	/**
	 * Saves booking periods for specefied post.
	 *
	 * @param  int   $post_id
	 * @param  array $rows
	 * @return assoc
	 */
	protected function save_booking_rows( $post_id, $rows ) {
		return adventure_tours_di( 'tour_booking_service' )->set_rows( $post_id, $rows );
	}

/*** Tour Booking tab management implementation [end] ***/

	/**
	 * Adds tour reports tab to the reports section.
	 *
	 * @param  assoc $reports
	 * @return assoc
	 */
	public function filter_woocommerce_admin_reports( $reports ) {
		if ( ! $this->_report_path_filter_is_added ) {
			$this->_report_path_filter_is_added = true;
			add_filter( 'wc_admin_reports_path', array( $this, 'filter_wc_admin_reports_path' ), 20, 3 );
		}

		$reports['tour_reports'] = array(
			'title' => __( 'Tours', 'adventure-tours' ),
			'reports' => array(
				'adt-general' => array(
					'title' => __( 'General', 'adventure-tours' ),
					'description' => '',
					'hide_title' => true,
					'callback' => array(
						'WC_Admin_Reports',
						'get_report'
					)
				)
			)
		);

		return $reports;
	}

	/**
	 * Filter for loading tour report classes in the reports section.
	 *
	 * @param  string $path  path to included file
	 * @param  string $name  report name
	 * @param  string $class class name
	 * @return string
	 */
	public function filter_wc_admin_reports_path( $path, $name, $class ) {
		$adventure_report_prefix = 'adt-';
		if ( strrpos( $name, $adventure_report_prefix ) === 0 ) {
			return dirname( __FILE__ ) . '/reports/WC_Report_ADT_' . ucfirst( str_replace( $adventure_report_prefix, '', $name ) ) .'.php';
		}

		return $path;
	}

	/**
	 * Filter for admin enqueue scripts.
	 *
	 * @return void
	 */
	public function filter_admin_enqueue_scripts() {
		$screen = get_current_screen();
		if ( in_array( $screen->id, array( 'product', 'edit-product' ) ) ) {
			wp_enqueue_style( 'tour_admin_style', PARENT_URL . '/assets/admin/manage-product.css', array(), '1.0' );
		}
	}
}
