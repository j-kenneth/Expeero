<?php
/**
 * Shortcode [mailchimp_form] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var string $form_id
 * @var string $title
 * @var string $css_class
 * @var string $width_mode
 * @var string $bg_url
 * @var string $bg_repeat
 * @var string $view
 * 
 * @var string $mailchimp_list_id // to support 5.4.X
 * @var string $button_text
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.1.1
 */

$is_modern_plugin = shortcode_exists( 'yikes-mailchimp' );

$is_missed_config = $is_modern_plugin ? empty( $form_id ) : empty( $mailchimp_list_id );
if ( $is_missed_config ) {
	printf( '<div class="form-subscribe"><div class="form-subscribe__shadow"></div>%s</div>',
		esc_html__( 'Please enter the MailChimp List ID settings in the MailChimp Form [mailchimp_form] shortcode.', 'adventure-tours' )
	);
	return;
}

$shortcode_id = adventure_tours_di( 'shortcodes_helper' )->generate_id();
$shortcode_full_id = 'adventure-tours-mainchimp-form-' . $shortcode_id;
TdJsClientScript::addScript( 'initMailChimpCustomValidtion' . $shortcode_id, 'Theme.FormValidationHelper.initMailChimpCustomValidtion("' .  $shortcode_full_id . '")' );

if ( $bg_url ) {
	wp_enqueue_script( 'parallax' );
	TdJsClientScript::addScript( 'initParallax', 'Theme.initParallax();' );
}

$form_mode_class = ( 'full-width' == $width_mode ) ? ' form-subscribe--full-width' : '';

?>
<div class="form-subscribe parallax-section <?php echo esc_attr( $css_class . $form_mode_class ); ?>">
<?php if ( $bg_url ) { ?>
	<div class="parallax-image" style="background-image:url(<?php echo esc_url( $bg_url ); ?>); background-repeat:<?php echo esc_attr( $bg_repeat ); ?>;"></div>
<?php } ?>
	<div class="form-subscribe__shadow"></div>
<?php
	if ( $title ) {
		printf( '<div class="form-subscribe__title">%s</div>', esc_html( $title ) );
	}
	if ( $content ) { 
		printf( '<div class="form-subscribe__description">%s</div>', adventure_tours_esc_text( $content ) );
	}

	if ( $is_modern_plugin ) { // version 6.0.X
		printf( '<div id="%s" class="form-subscribe__form-wrap">%s</div>',
			esc_attr( $shortcode_full_id ),
			do_shortcode( '[yikes-mailchimp form="' . $form_id . '" submit="' . esc_html( $button_text ) . '"]' )
		);
	} else { // version 5.4.X
		printf( '<div id="%s">%s</div>',
			esc_attr( $shortcode_full_id ),
			do_shortcode( '[yks-mailchimp-list id="' . $mailchimp_list_id . '" submit_text="' . esc_html( $button_text ) . '"] ' )
		);
	}
?>
</div>
