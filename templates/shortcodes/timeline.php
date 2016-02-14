<?php
/**
 * Shortcode [timeline] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var string $content
 * @var string $css_class
 * @var string $view
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

if ( ! $content ) {
	return;
}

printf(
	'<div class="timeline%s">%s</div>',
	$css_class ? ' ' . esc_attr( $css_class ) : '',
	do_shortcode( $content )
);
