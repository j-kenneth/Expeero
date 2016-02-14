<?php
/**
 * Extends tour booking form component with additional fields (name, email, phone).
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.1.0
 */

class AtBookingForm extends AtBookingFormBase
{
	public $disable_additional_fields = false;

	public $default_checkout_fields = array();

	private $_default_checkout_fields_inited = false;

	public function init() {
		if ( ! parent::init() ) {
			return false;
		}

		if ( ! $this->disable_additional_fields ) {
			add_action( 'woocommerce_before_checkout_form', array( $this, 'action_init_default_checkout_fileds' ), 1 );
		}

		return true;
	}

	public function get_fields_config( $product ) {
		$state_key = $this->make_product_state_key( 'general_fields_config', $product );
		$config = $this->get_state( $state_key, false );

		if ( false === $config ) {
			$config = parent::get_fields_config( $product );

			if ( $this->disable_additional_fields ) {
				return $config;
			}

			static $additional_fileds;

			if ( null === $additional_fileds ) {
				$name_label = esc_html__( 'Name', 'adventure-tours' );
				$email_label = esc_html__( 'Email address', 'adventure-tours' );
				$phone_label = esc_html__( 'Phone number', 'adventure-tours' );
				$additional_fileds = array(
					'name' => array(
						'label' => $name_label,
						'placeholder' => $name_label,
						'default' => $this->additional_field_default_value( 'name' ),
						'rules' => array( 'required' ),
						'icon_class' => 'td-user',
					),
					'email' => array(
						'label' => $email_label,
						'placeholder' => $email_label,
						'default' => $this->additional_field_default_value( 'email' ),
						'rules' => array( 'required', 'email' ),
						'icon_class' => 'td-email-2',
					),
					'phone' => array(
						'label' => $phone_label,
						'placeholder' => $phone_label,
						'default' => $this->additional_field_default_value( 'phone' ),
						'icon_class' => 'td-phone-1',
					)
				);
			}

			if ( $additional_fileds ) {
				$config = array_merge( $additional_fileds, $config );
			}

			$this->set_state( $state_key, $config );
		}

		return $config;
	}

	protected function added_to_cart( $product ) {
		parent::added_to_cart( $product );

		if ( ! $this->disable_additional_fields ) {
			$this->save_default_checkout_fields( $this->get_field_values( $product ) );
		}
	}

	protected function additional_field_default_value( $field_key, $default = '' ) {
		static $cache;
		if ( null == $cache ) {

			$this->action_init_default_checkout_fileds();

			$customer_id = get_current_user_id();
			$fname = $this->filter_default_checkout_field( null, 'billing_first_name' );
			$lname = $this->filter_default_checkout_field( null, 'billing_last_name' );
			if ( ! $fname && $customer_id ) {
				$fname = get_user_meta( $customer_id, 'billing_first_name', true );
				if ( ! $fname ) {
					$fname = get_user_meta( $customer_id, 'first_name', true );
				}
			}
			if ( ! $lname && $customer_id ) {
				$lname = get_user_meta( $customer_id, 'billing_last_name', true );
				if ( ! $lname ) {
					$lname = get_user_meta( $customer_id, 'last_name', true );
				}
			}

			if ( $fname || $lname ) {
				$cache['name'] = trim(
					join( ' ', array( $fname, $lname ) )
				);
			}

			$email = $this->filter_default_checkout_field( null, 'billing_email' );
			if ( ! $email && $customer_id ) {
				$email = get_user_meta( $customer_id, 'billing_email', true );
				
				if ( ! $email ) {
					$current_user = wp_get_current_user();
					$email = $current_user->user_email;
				}
			}
			if ( $email ) {
				$cache['email'] = $email;
			}

			$phone = $this->filter_default_checkout_field( null, 'billing_phone' );
			if ( ! $phone && $customer_id ) {
				$phone = get_user_meta( $customer_id, 'billing_phone', true );
			}
			if ( $phone ) {
				$cache['phone'] = $phone;
			}
		}
		return isset( $cache[ $field_key ] ) ? $cache[ $field_key ] : $default;
	}

	/**
	 * Loads values filled on booking form to local cache.
	 * Adds 'woocommerce_checkout_get_value' filter.
	 * Subscribed on 'woocommerce_before_checkout_form' action.
	 *
	 * @return void
	 */
	public function action_init_default_checkout_fileds() {

		if ( $this->_default_checkout_fields_inited ) {
			return;
		}
		$this->_default_checkout_fields_inited = true;

		$default_checkout_fields = WC()->session->get( 'tour_order_data' );
		if ( $default_checkout_fields ) {
			$this->default_checkout_fields = $this->default_checkout_fields
				? array_merge( $this->default_checkout_fields, $default_checkout_fields )
				: $default_checkout_fields;
		}

		if ( $this->default_checkout_fields ) {
			add_filter( 'woocommerce_checkout_get_value', array( $this, 'filter_default_checkout_field' ), 20, 2 );
		}
	}

	protected function save_default_checkout_fields( $data ) {
		$convertedOrderData = array();
		if ( !empty( $data['name'] ) ) {
			$nameParts = explode( ' ', $data['name'] );
			if ( ! empty( $nameParts[0] ) ) {
				$convertedOrderData['billing_first_name'] = $nameParts[0];
			}
			if ( ! empty( $nameParts[1] ) ) {
				$convertedOrderData['billing_last_name'] = $nameParts[1];
			}
		}

		if ( ! empty( $data['email'] ) ) {
			$convertedOrderData['billing_email'] = $data['email'];
		}

		if ( ! empty( $data['phone'] ) ) {
			$convertedOrderData['billing_phone'] = $data['phone'];
		}

		if ( $convertedOrderData ) {
			WC()->session->set( 'tour_order_data', $convertedOrderData );
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Filter that returns values defined for checkout durring booking form submission.
	 *
	 * @param  string $value current value
	 * @param  string $field field name
	 * @return mixed
	 */
	public function filter_default_checkout_field( $value, $field ) {
		if ( ! empty( $this->default_checkout_fields[$field] ) ) {
			return $this->default_checkout_fields[$field];
		}
		return $value;
	}
}
