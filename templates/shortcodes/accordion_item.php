<?php
/**
 * Shortcode [accordion_item] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var string  $title
 * @var boolean $is_active
 * @var string  $content
 * @var string  $accordion_id
 * @var string  $item_id
 * @var string  $css_class
 * @var string  $view
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.2.4
 */

?>

<div class="panel panel-default accordion__item<?php if ( $css_class ) { echo esc_attr( ' ' . $css_class ); } ?>">
	<div class="panel-heading accordion__item__title-wrap">
		<h4 class="panel-title">
			<a data-toggle="collapse" data-parent="#<?php echo esc_attr( $accordion_id ); ?>" href="#<?php echo esc_attr( $item_id ); ?>" class="accordion__item__link<?php if ( ! $is_active ) { echo ' collapsed'; } ?>"><?php echo esc_html( $title ); ?></a>
		</h4>
	</div>
	<div id="<?php echo esc_attr( $item_id ); ?>" class="panel-collapse<?php echo $is_active ? ' in' : ' collapse'; ?>">
		<div class="panel-body accordion__item__content"><?php echo do_shortcode( $content ); ?></div>
	</div>
</div>