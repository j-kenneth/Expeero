<?php
/**
 * Post sharing buttons rendering template part.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.9
 */

$sharrePluginConfig = array(
	// 'urlCurl' => admin_url( 'admin-ajax.php?action=sharrre_curl' ),
	'itemsSelector' => '.share-buttons__item[data-btntype]',
);

wp_enqueue_script( 'sharrre' );
TdJsClientScript::addScript( 'sharreInit', 'Theme.initSharrres(' . wp_json_encode( $sharrePluginConfig ) . ');' );
?>
<div class="share-buttons" data-urlshare="<?php echo esc_url( get_permalink() ); ?>">
<?php if ( adventure_tours_get_option( 'social_sharing_googleplus' ) ) { ?>
	<div class="share-buttons__item share-buttons__item--googleplus" data-btntype="googlePlus"></div>
<?php } ?>
<?php if ( adventure_tours_get_option( 'social_sharing_facebook' ) ) { ?>
	<div class="share-buttons__item share-buttons__item--facebook" data-btntype="facebook"></div>
<?php } ?>
<?php if ( adventure_tours_get_option( 'social_sharing_twitter' ) ) { ?>
	<div class="share-buttons__item share-buttons__item--twitter" data-btntype="twitter"></div>
<?php } ?>
<?php if ( adventure_tours_get_option( 'social_sharing_stumbleupon' ) ) { ?>
	<div class="share-buttons__item share-buttons__item--stumbleupon" data-btntype="stumbleupon"></div>
<?php } ?>
<?php if ( adventure_tours_get_option( 'social_sharing_linkedin' ) ) { ?>
	<div class="share-buttons__item share-buttons__item--linkedin" data-btntype="linkedin"></div>
<?php } ?>
<?php if ( adventure_tours_get_option( 'social_sharing_pinterest' ) ) { ?>
	<div class="share-buttons__item share-buttons__item--pinterest" data-btntype="pinterest"></div>
<?php } ?>
<?php if ( adventure_tours_get_option( 'social_sharing_vk' ) ) { ?>
	<div class="share-buttons__item share-buttons__item--vk" data-btntype="vk"></div>
<?php } ?>
</div>
