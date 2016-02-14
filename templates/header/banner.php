<?php
/**
 * Page header view for the banner mode.
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
 * @version   1.0.7
 */

$breadcrumbs_html = adventure_tours_render_template_part( 'templates/header/breadcrumbs', '', array(), true );

$is_use_parallax = isset( $is_banner_image_parallax ) && $is_banner_image_parallax;
$is_image = $banner_image && ! $is_use_parallax;
$is_banner_paralax = $is_use_parallax && $banner_image;

$mask_html = ! empty( $banner_mask )
	? sprintf( '<div class="header-section-mask %s"></div>', esc_attr( 'header-section-mask--' . $banner_mask ) ) 
	: '';

adventure_tours_di( 'register' )->setVar( 'is_banner', true );

$wrapper_additional_class = $is_banner_paralax ? ' parallax-section parallax-section--header' : '';
if ( $banner_mask ) {
	$wrapper_additional_class .= esc_attr( ' header-section--with-mask-' . $banner_mask );
}
?>

<div class="header-section header-section--with-banner<?php print $wrapper_additional_class; ?>">
<?php if ( $is_banner_paralax ) {
	wp_enqueue_script( 'parallax' );
	TdJsClientScript::addScript( 'initParallax', 'Theme.initParallax();' );

	printf( '%s<div class="parallax-image" style="background-image:url(%s);%s"></div>',
		$mask_html,
		esc_url($banner_image),
		$banner_image_repeat ? ' background-repeat:' . esc_attr( $banner_image_repeat ) . ';' : ''
	);
} ?>
	<div class="container">
		<?php print $breadcrumbs_html; ?>
		<div class="header-section__content">
			<h1 class="header-section__title"><?php echo esc_html( $title ); ?></h1>
		<?php if ( $banner_subtitle ) { ?>
			<p class="header-section__description"><?php echo esc_html( $banner_subtitle ); ?></p>
		<?php } ?>
		<?php
			// for single tour and single product
			if ( is_singular( 'product' ) && 'yes' === get_option( 'woocommerce_enable_review_rating' ) ) {
				$product = wc_get_product();
				$review_count = ( int ) $product->get_review_count();
				$rating_average = ceil ( $product->get_average_rating() );

				if ( $review_count && $rating_average ) {
					echo '<div class="header-section__rating">';
					adventure_tours_renders_stars_rating( $rating_average );
					printf( '(' . _n( '1 review', '%s reviews', $review_count, 'adventure-tours' ) . ')', $review_count );
					echo '</div>';
				}
			}
		?>
		</div>
	</div>
<?php if ( $is_image ) { 
	printf('<div class="header-section__simple-image%s">%s<img src="%s" alt="%s"></div>',
		$breadcrumbs_html ? ' header-section__simple-image--with-breadcrumbs' : '',
		$mask_html,
		esc_url( $banner_image ),
		esc_attr( $title )
	);
} ?>
</div>
