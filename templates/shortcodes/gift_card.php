<?php
/**
 * Shortcode [gift_card] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var string $title
 * @var string $content
 * @var string $button_title
 * @var srting $button_link
 * @var string $css_class
 * @var string $view
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.3.0
 */

$is_button = $button_title && $button_link ? true : false;

$element_class = 'gift-cart';
if ( $is_button ) {
	$element_class .= ' gift-cart--button'; 
}
if ( !empty( $css_class ) ) {
	$element_class .= ' ' . $css_class;
}
?>
<div class="<?php echo esc_attr( $element_class ); ?>">
	<div class="gift-cart__box">
		<div class="gift-cart__bow"></div>
<?php
	if ( $title ) {
		printf( '<h3 class="gift-cart__title">%s</h3>', esc_html( $title ) );
	}
	if ( $content ) {
		printf( '<div class="gift-cart__description">%s</div>', esc_html( $content ) );
	}
	if ( $is_button ) {
		printf('<a href="%s" class="gift-cart__button">%s</a>',
			esc_url( $button_link ),
			esc_html( $button_title )
		);
	} 
?>
	</div>
</div>
