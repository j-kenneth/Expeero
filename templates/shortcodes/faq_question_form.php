<?php
/**
 * Shortcode [faq_question_form] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var string $form_action       value for action attribute
 * @var assoc  $form_data         form values
 * @var assoc  $nonce_field       settings for nonce field
 * @var assoc  $state_hash_field
 * @var string $email
 * @var string $email_subject
 * @var string $email_view
 * @var string $css_class
 * @var string $view
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.3.0
 */

TdJsClientScript::addScript( 'initFaqQuestionForm', 'Theme.init_faq_question_form(".faq-question-form");' );

?>
<form class="faq-question-form<?php if ( ! empty( $css_class ) ) { echo ' ' . esc_attr( $css_class ); }; ?>" action="<?php echo esc_url( $form_action ); ?>" method="post">
	<div class="form-block__item">
		<input type="text" name="question[name]" class="form-validation-item" placeholder="<?php esc_attr_e( 'Name', 'adventure-tours' ); ?>" value="<?php echo isset( $form_data['name'] ) ? esc_attr( $form_data['name'] ) : ''; ?>">
	</div>
	<div class="form-block__item">
		<input type="email" name="question[email]" class="form-validation-item" placeholder="<?php esc_attr_e( 'Email', 'adventure-tours' ); ?>" value="<?php echo isset( $form_data['email'] ) ? esc_attr( $form_data['email'] ) : ''; ?>">
	</div>
	<div class="form-block__item">
		<textarea name="question[question]" class="form-validation-item" placeholder="<?php esc_attr_e( 'Question', 'adventure-tours' ); ?>"><?php echo isset( $form_data['question'] ) ? esc_textarea( $form_data['question'] ) : ''; ?></textarea>
	</div>
	<div class="form-block__item">
		<div class="form-block__validation-error"></div>
		<button type="submit" class="atbtn atbtn--primary atbtn--full-width"><?php esc_attr_e( 'Send Question', 'adventure-tours' ); ?></button>
	</div>
<?php if ( isset( $nonce_field ) && ! empty( $nonce_field['name'] ) && ! empty( $nonce_field['value'] ) ) { ?>
	<input type="hidden" name="<?php echo esc_attr( $nonce_field['name'] ); ?>" value="<?php echo esc_attr( $nonce_field['value'] ); ?>">
<?php } ?>
<?php if ( isset( $state_hash_field ) && ! empty( $state_hash_field['name'] ) && ! empty( $state_hash_field['value'] ) ) { ?>
	<input type="hidden" name="<?php echo esc_attr( $state_hash_field['name'] ); ?>" value="<?php echo esc_attr( $state_hash_field['value'] ); ?>">
<?php } ?>
</form>
