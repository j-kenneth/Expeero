<?php
/**
 * Notification email view for [faq_question_form] shortcode.
 * For more detailed list see list of shortcode attributes.
 *
 * @var string $name
 * @var string $email
 * @var string $question
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

?>
<h3><?php esc_html_e( 'New question for FAQs', 'adventure-tours' ); ?></h3>
<p><strong><?php esc_html_e( 'From', 'adventure-tours' ); ?>: </strong><?php echo esc_html( $name ); ?>, <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>.</p>
<p><strong><?php esc_html_e( 'Question', 'adventure-tours' ); ?>: </strong><?php echo nl2br( esc_html( $question ) ); ?></p>
