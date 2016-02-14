<?php
/**
 * Template part used for tour price presentation.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

global $product;
$price_html = $product->get_price_html();
$class_without_price = $price_html ? '' : ' price-decoration--without-price';

$label_text = apply_filters( 'adventure_tours_price_decoration_label', esc_html__( 'One tour per person', 'adventure-tours' ), $product );
$class_without_label = $label_text ? '' : ' price-decoration--without-label';

printf( '<div class="price-decoration block-after-indent%s">', esc_attr( $class_without_price . $class_without_label ) );

if ( $price_html ) {
	printf( '<div class="price-decoration__value"><i class="td-price-tag"></i>%s</div>', $price_html );
}

if ( $label_text ) { 
	printf( '<div class="price-decoration__label">%s</div>', esc_html( $label_text ) );
}

if ( $product->is_type('tour') ) {
	adventure_tours_renders_tour_badge( array(
		'tour_id' => $product->id,
		'css_class' => 'price-decoration__label-round',
		'text_before' => '<span>',
		'text_after' => '</span>',
	) );
}

print( '</div>' );
