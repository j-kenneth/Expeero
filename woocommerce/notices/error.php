<?php
/**
 * Show error messages
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! $messages ){
	return;
}

?>
<ul class="woocommerce-error">
	<?php foreach ( $messages as $message ) : ?>
		<li><i class="fa fa-exclamation-triangle woocommerce-error-icon"></i><?php echo wp_kses_post( $message ); ?></li>
	<?php endforeach; ?>
</ul>
