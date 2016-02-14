<?php
/**
 * Shortcode [contact_info] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var string $address
 * @var string $phone
 * @var string $email
 * @var string $skype
 * @var string $css_class
 * @var string $view
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.3.0
 */

?>
<div class="contact-info<?php if ( ! empty( $css_class ) ) { echo ' ' . esc_attr( $css_class ); }; ?>">
	<?php if ( $address ) { ?>
		<div class="contact-info__item">
			<div class="contact-info__item__icon"><i class="fa fa-map-marker"></i></div>
			<div class="contact-info__item__text"><?php echo esc_html( $address ); ?></div>
		</div>
	<?php } ?>
	<?php if ( $phone ) {
		$item_template = '<span class="contact-info__item__icon"><i class="fa fa-phone"></i></span>' .
			'<span class="contact-info__item__text">%s</span>';

		if ( '+' == $phone[0] ) {
			$call_url = 'call:' . preg_replace('/ |-|\(|\)/', '', $phone);

			$item_html = sprintf( '<a href="%s">' . $item_template . '</a>',
				esc_attr( $call_url ),
				esc_html( $phone )
			);
		} else {
			$item_html = sprintf( $item_template,
				esc_html( $phone )
			);
		}
		printf('<div class="contact-info__item">%s</div>', $item_html );
	} ?>
	<?php if ( $email ) { ?>
		<div class="contact-info__item">
			<a href="mailto:<?php echo esc_attr( $email ); ?>">
				<span class="contact-info__item__icon"><i class="fa fa-envelope"></i></span>
				<span class="contact-info__item__text"><?php echo esc_html( $email ); ?></span>
			</a>
		</div>
	<?php } ?>
	<?php if ( $skype ) { ?>
		<div class="contact-info__item">
			<a href="skype:<?php echo esc_attr( $skype ); ?>?call">
				<span class="contact-info__item__icon"><i class="fa fa-skype"></i></span>
				<span class="contact-info__item__text"><?php echo esc_html( $skype ); ?></span>
			</a>
		</div>
	<?php } ?>
</div>
