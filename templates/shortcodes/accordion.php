<?php
/**
 * Shortcode [accordion] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var string $content
 * @var string $style
 * @var string $accordion_id
 * @var string $css_class
 * @var string $view
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.3.0
 */

if ( ! $content ) {
	return;
}

if ( $css_class ) {
	$css_class = ' ' . $css_class;
}
if ( 'with-border' == $style ) {
	$css_class .= ' accordion--with-border';
}

printf(
	'<div class="panel-group accordion%s" id="%s">%s</div>',
	$css_class ? esc_attr( $css_class ) : '',
	esc_attr( $accordion_id ),
	do_shortcode( $content )
);
