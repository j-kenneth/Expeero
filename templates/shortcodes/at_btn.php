<?php
/**
 * Shortcode [at_btn] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var string  $text
 * @var string  $url
 * @var string  $type
 * @var string  $style
 * @var string  $size
 * @var string  $corners
 * @var boolean $light
 * @var boolean $transparent
 * @var string  $icon_class
 * @var string  $icon_align
 * @var string  $css_class
 * @var string  $view
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.6
 */

if ( ! $type || ! in_array( $type, array( 'link','button','submit' ) ) ) {
	$type = !empty( $url ) ? 'link' : 'button';
}

$class_parts = array();
if ( $style ) {
	$class_parts[] = 'atbtn--' . $style;
}
if ( $size ) { //'large'
	$class_parts[] = 'atbtn--' . $size;
}
if ( $corners ) { //'square'
	$class_parts[] = 'atbtn--' . $corners;
}
if ( $light ) { // on/off
	$class_parts[] = 'atbtn--light';
}
if ( $transparent ) { // on/off
	$class_parts[] = 'atbtn--transparent';
}
if ( $class_parts ) {
	if ( $css_class ) {
		$class_parts[] = $css_class;
	}
	$css_class =  join( ' ', $class_parts );
}

$icon_html_left = '';
$icon_html_right = '';
if ( $icon_class ) {
	if ( 'right' == $icon_align ) {
		$icon_class .= ' atbtn__icon--right';
	}

	$icon_html = sprintf( '<i class="atbtn__icon %s"></i>', esc_attr( $icon_class ) );
	if ( 'right' == $icon_align ) {
		$icon_html_right = $icon_html;
	} else {
		$icon_html_left = $icon_html;
	}
}

if ( 'link' == $type ) {
	printf( '<a href="%s" class="atbtn%s">%s%s%s</a>',
		$url ? esc_url( $url ) : '',
		$css_class ? esc_attr( ' ' . $css_class ) : '',
		$icon_html_left,
		$content ? esc_html( $content ) : esc_html( $text ),
		$icon_html_right
	);
} else {
	printf( '<button class="atbtn%s"%s>%s%s%s</button>',
		$css_class ? esc_attr( ' ' . $css_class ) : '',
		'submit' == $type ? ' type="submit"' : '',
		$icon_html_left,
		$content ? esc_html( $content ) : esc_html( $text ),
		$icon_html_right
	);
}
