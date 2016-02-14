<?php
/**
 * Tour single content template part.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

global $product;
$is_banner = adventure_tours_di( 'register' )->getVar( 'is_banner' );
$tour_permalink = get_the_permalink();
$tour_thumbnail_url = wp_get_attachment_url( get_post_thumbnail_id() );
?>
<div class="row<?php if ( $is_banner ) { echo ' tour-single-rise'; } ?>">
	<main class="col-md-9" role="main" itemscope itemtype="http://schema.org/Product">
		<?php get_template_part( 'templates/tour/tabs' ); ?>

		<meta itemprop="name" content="<?php the_title(); ?>">
		<meta itemprop="description" content="<?php echo esc_attr( adventure_tours_get_short_description( null, 300 ) ); ?>">
		<meta itemprop="url" content="<?php echo esc_url( $tour_permalink ); ?>">
		<?php
			adventure_tours_render_template_part( 'templates/parts/scheme-price', '', array( 'product' => $product ) );
			adventure_tours_render_template_part( 'templates/parts/scheme-rating', '', array( 'product' => $product ) );
		?>
		<?php if ( $tour_thumbnail_url ) { ?>
			<meta itemprop="image" content="<?php echo esc_url( $tour_thumbnail_url ) ?>">
		<?php } ?>
		<?php if ( comments_open() ) {
			comments_template();
		} ?>

		<?php if ( adventure_tours_get_option( 'tours_page_show_related_tours' ) ) {
			get_template_part( 'templates/tour/related-tours' );
		} ?>
	</main>
	<?php get_sidebar( 'shop' ); ?>
</div>
