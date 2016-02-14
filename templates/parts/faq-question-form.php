<?php
/**
 * FAQ question posting form themplate part.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

$attributes_text = '';
if ( 'custom_email' == adventure_tours_get_option( 'faq_question_form_receiver_type' ) ) {
	$attributes_text = ' email="' . esc_attr( adventure_tours_get_option( 'faq_question_form_custom_email' ) ) . '"';
}
?>
<div class="form-block form-block--style2 form-block--faq">
	<div class="form-block__content">
		<h3 class="form-block__title"><?php esc_html_e( 'Not found your question?', 'adventure-tours' ); ?></h3>
		<div class="form-block__description"><?php esc_html_e( 'Fill in the form below', 'adventure-tours' ); ?></div>
		<?php echo do_shortcode( '[faq_question_form' . $attributes_text . ']' ); ?>
	</div>
	<div class="form-block__validation-success"></div>
</div>
