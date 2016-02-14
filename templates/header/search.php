<?php
/**
 * Page header template part for the search field rendering.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

if ( ! adventure_tours_get_option( 'show_header_search' ) ) {
	return '';
}
// as in this case link tag will be placed to body withou property='stylesheet' attribute
// that makes html validation error
// wp_enqueue_style( 'magnific-popup' );
// wp_enqueue_script( 'magnific-popup' );
TdJsClientScript::addScript( 'initSerchFormPopup', 'Theme.initSerchFormPopup('. wp_json_encode(array(
	'placeholder_text' => esc_html__( 'Type in your request...', 'adventure-tours' ),
)).');' );
?>
<div class="header__info__item header__info__item--delimiter header__info__item--search"><a href="#search-form-header" class="popup-search-form" data-effect="mfp-zoom-in"><i class="fa fa-search"></i></a></div>

<div id="search-form-header" class="search-form-popup search-form-popup--hide mfp-with-anim mfp-hide ">
	<?php get_search_form(); ?>
</div>
