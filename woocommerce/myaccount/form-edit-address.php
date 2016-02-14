<?php
/**
 * Edit address form
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;

$page_title = ( $load_address === 'billing' ) ? esc_html__( 'Billing Address', 'adventure-tours' ) : esc_html__( 'Shipping Address', 'adventure-tours' );

get_currentuserinfo();

?>

<?php wc_print_notices(); ?>

<?php if ( ! $load_address ) : ?>

	<?php wc_get_template( 'myaccount/my-address.php' ); ?>

<?php else : ?>
	<div class="myaccount-box">
		<form method="post">
	
			<h3 class="myaccount-edit-address-title"><?php echo apply_filters( 'woocommerce_my_account_edit_address_title', $page_title ); ?></h3>
	
			<?php do_action( "woocommerce_before_edit_address_form_{$load_address}" ); ?>
	
			<?php foreach ( $address as $key => $field ) : ?>
	
				<?php woocommerce_form_field( $key, $field, ! empty( $_POST[ $key ] ) ? wc_clean( $_POST[ $key ] ) : $field['value'] ); ?>
	
			<?php endforeach; ?>
	
			<?php do_action( "woocommerce_after_edit_address_form_{$load_address}" ); ?>
	
			<p>
				<input type="submit" class="button myaccount-box-button" name="save_address" value="<?php esc_attr_e( 'Save Address', 'adventure-tours' ); ?>" />
				<?php wp_nonce_field( 'woocommerce-edit_address' ); ?>
				<input type="hidden" name="action" value="edit_address" />
			</p>
	
		</form>
	</div>
<?php endif; ?>
