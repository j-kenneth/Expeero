<?php
/**
 * Shortcode [icon_tick] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var boolean $state
 * @var string  $css_class
 * @var string  $view
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.3.0
 */

if ( $state ) {
	echo '<i class="fa fa-check icon-tick icon-tick--on' . ( empty( $css_class ) ? '' : ' ' . esc_attr( $css_class ) ) . '"></i>';
} else {
	echo '<i class="fa fa-times icon-tick icon-tick--off' . ( empty( $css_class ) ? '' : ' ' . esc_attr( $css_class ) ) . '"></i>';
}
