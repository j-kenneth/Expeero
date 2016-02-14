<?php
/**
 * Shortcode [google_map] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var string $address
 * @var string $coordinates
 * @var string $zoom
 * @var string $height
 * @var string $width_mode
 * @var string $css_class
 * @var string $view
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

$instance_id = adventure_tours_di( 'shortcodes_helper' )->generate_id();
$element_id = 'googleMapCanvas' . $instance_id;

$config_json = wp_json_encode( array(
	'coordinates' => explode( ',', $coordinates ),
	'zoom' => (int) $zoom,
	'address' => $address,
	'height' => $height,
	'element_id' => $element_id,
	'full_width' => 'full-width' == $width_mode,
	'is_reset_map_fix_for_bootstrap_tabs_accrodion' => true,
) );

TdJsClientScript::addScriptScriptFile( 'googleMapScript', 'https://maps.google.com/maps/api/js?sensor=true' );
TdJsClientScript::addScript( 'initGoogleMap' . $instance_id, 'Theme.initGoogleMap(' . $config_json . ');', TdJsClientScript::POS_FOOTER );

printf( '<div id="%s" class="google-map%s"></div>',
	esc_attr( $element_id ),
	$css_class ? esc_attr( ' ' . $css_class ) : ''
);
