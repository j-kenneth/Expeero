<?php
/**
 * Shortcode [timeline_item] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var string $item_number
 * @var string $title
 * @var string $content
 * @var string $view
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

?>
<div class="timeline__item">
	<div class="timeline__item__icon-wrap">
		<div class="timeline__item__icon">
			<div class="timeline__item__icon__text"><?php echo esc_html( $item_number ); ?></div>
		</div>
	</div>
	<div class="timeline__item__content padding-left">
		<?php if ( $title ) {
			printf( '<h3 class="timeline__item__title">%s</h3>', esc_html( $title ) );
		} ?>
		<?php if ( $content ) {
			printf( '<div class="timeline__item__description">%s</div>', do_shortcode( $content ) );
		} ?>
	</div>
</div>
