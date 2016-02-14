<?php
/**
 * Shortcode [tabs] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var array  $items
 * @var string $style
 * @var string $css_class
 * @var string $view
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.2.4
 */

if ( ! $items ) {
	return '';
}

if ( $css_class ) {
	$css_class = ' ' . $css_class;
}
if ( 'with-border' == $style ) {
	$css_class .= ' tours-tabs--with-border';
}
?>
<div class="tours-tabs<?php if ( $css_class ) { echo esc_attr( $css_class ); }; ?>">
	<ul class="nav nav-tabs">
	<?php foreach ( $items as $item_id => $title_info ) {
		printf(
			'<li%s><a href="#%s" data-toggle="tab">%s</a></li>',
			$title_info['is_active'] ? ' class="active"' : '',
			esc_attr( $item_id ),
			esc_html( $title_info['title'] )
		);
	} ?>
	</ul>
	<div class="tab-content">
	<?php foreach ( $items as $item_id => $item_info ) {
		printf(
			'<div class="tab-pane%s" id="%s"><div class="tours-tabs__content padding-all">%s</div></div>',
			$item_info['is_active'] ? ' active' : '',
			esc_attr( $item_id ),
			do_shortcode( $item_info['content'] )
		);
	} ?>
	</div>
</div>