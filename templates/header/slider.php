<?php
/**
 * Page header view for the slider mode.
 *
 * @var string $title
 * @var string $section_mode
 * @var string $slider_alias
 * @var string $banner_subtitle
 * @var string $banner_image
 * @var string $is_banner_image_parallax
 * @var string $banner_image_repeat
 * @var string $banner_mask
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

if ( empty( $slider_alias ) ) {
	return;
}
?>
<div class="slider"><?php echo do_shortcode( '[rev_slider ' . $slider_alias . ']' ); ?></div>
