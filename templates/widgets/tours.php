<?php
/**
 * Widget Tours view.
 *
 * @var string  $widget_args
 * @var boolean $settings
 * @var string  $items
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.2.4
 */

if ( ! $items ) {
	return;
}

if ( isset( $widget_args ) ) {
	extract( $widget_args );
}

$badge_service = adventure_tours_di( 'tour_badge_service' );

// Display mode processing to decide what elements should be displayed for each tour item.
$display_flags = ! empty( $settings['display_mode'] ) ? explode( '_', $settings['display_mode'] ) : array();
$is_show_rating = $display_flags ? in_array( 'rating', $display_flags ) : false;
$is_show_badge = $display_flags ? in_array( 'badge', $display_flags ) : false;
$is_show_price = $display_flags ? in_array( 'price', $display_flags ) : false;
$is_show_alt_price = $display_flags ? in_array( 'alt-price', $display_flags ) : false;
if ( $is_show_alt_price && $is_show_badge ) {
	$is_show_badge = false;
}

$title = ! empty( $settings['title'] ) ? $settings['title'] : '';
?>
<div class="block-after-indent<?php if ( ! $title ) { print ' widget-atgrid-without-title'; }; ?>">
	<?php print $before_widget; ?>
	<?php if ( $title ) {
		print $before_title . esc_html( $title ) . $after_title;
	} ?>
	<div class="atgrid--widget">
		<div class="atgrid atgrid--small">
		<?php foreach ( $items as $product ) : ?>
			<?php
			$price_html = $is_show_price || $is_show_alt_price ? $product->get_price_html() : null;
			$item_url = get_permalink( $product->id );
			$escaped_item_url = esc_url( $item_url );
			$item_title = $product->get_title();
			?>
			<div class="atgrid__item">
				<div class="atgrid__item__top">
				<?php printf( '<a href="%s">%s</a>', $escaped_item_url, $product->get_image( 'thumb_tour_widget' ) ); ?>
				<?php if ( $is_show_badge ) {
						adventure_tours_renders_tour_badge( array(
						'tour_id' => $product->id,
						'wrap_css_class' => 'atgrid__item__angle-wrap',
						'css_class' => 'atgrid__item__angle',
					) );
				} ?>
				<?php if ( $price_html ) {
					if ( $is_show_alt_price ) {
						$badge = $badge_service->get_tour_badge( $product->id );
						printf('<a href="%s" class="price-round"%s><span class="price-round__content">%s</span></a>',
							$escaped_item_url,
							$badge && !empty( $badge['color'] ) ? ' style="background-color:' . esc_attr( $badge['color'] ) . '"' : '',
							$price_html
						);
					} else {
						printf(
							'<div class="atgrid__item__price"><a href="%s" class="atgrid__item__price__button">%s</a></div>',
							$escaped_item_url,
							$price_html
						);
					}
				} ?>
				<?php if ( $is_show_rating ) {
					adventure_tours_renders_stars_rating( $product->get_average_rating(), array(
						'before' => '<div class="atgrid__item__rating">',
						'after' => '</div>',
					) );
				} ?>
					<h4 class="atgrid__item__top__title<?php if ( $price_html && $is_show_alt_price ) { print ' atgrid__item__top__title--alt'; }; ?>"><a href="<?php echo esc_url( $item_url ); ?>"><?php echo esc_html( $item_title ); ?></a></h4>
				</div>
			</div>
		<?php endforeach; ?>
		</div><!-- .atgrid -->
	</div><!-- .atgrid widget -->
	<?php print $after_widget; ?>
</div>