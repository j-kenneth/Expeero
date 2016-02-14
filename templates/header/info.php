<?php
/**
 * Page header template part for the site details rendering.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

?>
<div class="header__info">
	<div class="header__info__items-left">
	<?php
		$contact_phone = adventure_tours_get_option( 'contact_phone' );
		$contact_time = adventure_tours_get_option( 'contact_time' );
	?>
	<?php if ( $contact_phone ) { ?>
		<div class="header__info__item header__info__item--phone"><i class="fa fa-phone"></i><?php echo esc_html( $contact_phone ); ?></div>
	<?php } ?>
	<?php if ( $contact_time ) { ?>
		<div class="header__info__item header__info__item--clock"><i class="fa fa-clock-o"></i><?php echo esc_html( $contact_time ); ?></div>
	<?php } ?>
	</div>
	<div class="header__info__items-right">
		<?php get_template_part( 'templates/header/social-icons' ); ?>
		<?php get_template_part( 'templates/header/shop-cart' ); ?>
		<?php get_template_part( 'templates/header/search' ); ?>
	</div>
</div>
