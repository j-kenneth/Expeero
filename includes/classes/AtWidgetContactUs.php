<?php
/**
 * Contact us widget component.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.1.1
 */

class AtWidgetContactUs extends WP_Widget
{
	public $allow_use_links = true;

	public function __construct() {
		parent::__construct(
			'contact_us_adventure_tours',
			'AdventureTours: ' . esc_html__( 'Contact Us', 'adventure-tours' ),
			array(
				'description' => esc_html__( 'Contact Us Widget', 'adventure-tours' ),
			)
		);
	}

	public function widget( $args, $instance ) {
		$allow_use_links = $this->allow_use_links;

		extract( $args );
		extract( $instance );

		$elements_html = '';

		if ( $address ) {
			$elements_html .= '<div class="widget-contact-info__item">' .
				'<div class="widget-contact-info__item__icon"><i class="fa fa-map-marker"></i></div>' .
				'<div class="widget-contact-info__item__text"><span>' . esc_html( $address ) . '</span></div>' .
			'</div>';
		}

		if ( $phone ) {
			if ( $allow_use_links && '+' == $phone[0] ) {
				$phone_html = sprintf( '<a href="%s">%s</a>',
					esc_html( 'call:' . preg_replace('/ |-|\(|\)/', '', $phone) ),
					esc_html( $phone )
				);
			} else {
				$phone_html = esc_html( $phone );
			}

			$elements_html .= '<div class="widget-contact-info__item">' .
				'<div class="widget-contact-info__item__icon"><i class="fa fa-phone"></i></div>' .
				'<div class="widget-contact-info__item__text">' . $phone_html . '</div>' .
			'</div>';
		}

		if ( $email ) {
			if ( $allow_use_links ) {
				$email_html = sprintf( '<a href="%s">%s</a>',
					esc_html( 'mailto:' . $email ),
					esc_html( $email )
				);
			} else {
				$email_html = esc_html( $email );
			}

			$elements_html .= '<div class="widget-contact-info__item">' .
				'<div class="widget-contact-info__item__icon"><i class="fa fa-envelope"></i></div>' .
				'<div class="widget-contact-info__item__text">' . $email_html . '</div>' .
			'</div>';
		}

		if ( $skype ) {
			if ( $allow_use_links ) {
				$skype_html = sprintf( '<a href="%s">%s</a>',
					esc_attr( 'skype:' . $skype . '?call' ),
					esc_html( $skype )
				);
			} else {
				$skype_html = esc_html( $skype );
			}
			$elements_html .= '<div class="widget-contact-info__item">' .
				'<div class="widget-contact-info__item__icon"><i class="fa fa-skype"></i></div>' .
				'<div class="widget-contact-info__item__text">' . $skype_html . '</div>' .
			'</div>';
		}

		if ( $elements_html ) {
			printf(
				'%s<div class="widget-contact-info">%s%s</div>%s',
				$before_widget,
				$title ? $before_title . esc_html( $title ) . $after_title : '',
				$elements_html,
				$after_widget
			);
		}
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $new_instance;
		return $instance;
	}

	public function form( $instance ) {
		$default = array(
			'title' => '',
			'address' => '',
			'phone' => '',
			'email' => '',
			'skype' => '',
		);

		$itemTitles = array(
			'title' => esc_html__( 'Title', 'adventure-tours' ),
			'address' => esc_html__( 'Address', 'adventure-tours' ),
			'phone' => esc_html__( 'Phone', 'adventure-tours' ),
			'email' => esc_html__( 'Email', 'adventure-tours' ),
			'skype' => esc_html__( 'Skype', 'adventure-tours' ),
		);

		$instance = wp_parse_args( (array) $instance, $default );

		foreach ( $instance as $key => $val ) {
			$itemTitle = isset( $itemTitles[$key] ) ? $itemTitles[$key] : '';

			echo '<p>' .
				'<label for="' . esc_attr( $this->get_field_id( $key ) ) . '">' . esc_html( $itemTitle ) . ':</label>' .
				'<input class="widefat" id="' . esc_attr( $this->get_field_id( $key ) ) . '" name="' . esc_attr( $this->get_field_name( $key ) ) . '" type="text" value="' . esc_attr( $val ) . '">' .
			'</p>';
		}
	}
}
