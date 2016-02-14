<?php
/**
 * Shortcode [tour_category_icons] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var string  $title
 * @var boolean $title_underline
 * @var string  $sub_title
 * @var string  $bg_url
 * @var boolean $ignore_empty
 * @var string  $category_ids
 * @var string  $css_class
 * @var string  $parent_id
 * @var string  $slides_number
 * @var string  $number
 * @var string  $order
 * @var string  $orderby
 * @var string  $view
 * @var string  $items
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.2.4
 */

if ( ! $items ) {
	return;
}

$slider_id = 'swiper' . adventure_tours_di( 'shortcodes_helper' )->generate_id();
wp_enqueue_style( 'swiper' );
wp_enqueue_script( 'swiper' );

$cfg_var_name = '_ticCfg' . $slider_id;

if ( ! isset( $slides_number ) || $slides_number < 1 ) {
	$slides_number = 5;
} elseif ( $slides_number > 6 ) {
	$slides_number = 6;
}

TdJsClientScript::addScript(
	'toursTypeIconsSliderInit' . $slider_id,
	'var ' . $cfg_var_name . ' = '. wp_json_encode( array(
		'containerSelector' => '#' . $slider_id,
		'slidesNumber' => $slides_number,
		'navPrevSelector' => '.tours-type-icons__slider__prev',
		'navNextSelector' => '.tours-type-icons__slider__next',
	) ). ';' .
	$cfg_var_name . '.widthToSlidesNumber = function(windowWidth, slidesPerView){ if (windowWidth < 390) return 1; else if (windowWidth < 581) return 2; else if (windowWidth < 768) return 3; return slidesPerView; };' .
	'Theme.makeSwiper(' . $cfg_var_name . ');'
);
?>
<div id="<?php echo esc_attr( $slider_id ); ?>" class="tours-type-icons padding-top-large padding-bottom-large<?php if ( $css_class ) { echo esc_attr( ' ' . $css_class ); } ?>">
<?php if ( $bg_url ) { ?>
	<div class="tours-type-icons__bg" style="background:url(<?php echo esc_url( $bg_url ); ?>) no-repeat center"></div>
<?php } ?>
	<div class="tours-type-icons__shadow"></div>
	<div class="tours-type-icons__content">
		<?php if ( $title || $sub_title ) {
			echo do_shortcode( '[title text="' . addslashes( $title ) . '" subtitle="' . addslashes( $sub_title ) . '" size="big" position="center" decoration="on" underline="' . addslashes( $title_underline ) . '" style="light"]' );
		} ?>
		<div class="tours-type-icons__slider">
			<div class="tours-type-icons__slider__controls">
				<a class="tours-type-icons__slider__prev" href="#"><i class="fa fa-chevron-left"></i></a>
				<a class="tours-type-icons__slider__next" href="#"><i class="fa fa-chevron-right"></i></a>
			</div>
			<div class="swiper-container swiper-slider">
				<div class="swiper-wrapper">
				<?php foreach ( $items as $item ) { ?>
					<?php
					$icon_class = AtTourHelper::get_tour_category_icon_class( $item->term_id );
					$detail_url = get_term_link( $item->slug, 'tour_category' );
					?>
					<div class="swiper-slide tours-type-icons__item">
						<a href="<?php echo esc_url( $detail_url ); ?>" class="tours-type-icons__item__container">
							<span class="tours-type-icons__item__content">
								<i class="<?php echo esc_attr( $icon_class ); ?>"></i><?php echo esc_html( $item->name ); ?>
							</span>
						</a>
					</div>
				<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
