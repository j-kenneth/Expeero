<?php
/**
 * Page header template part for the logo rendering.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

?>
<div class="logo">
<?php if ( 'image' != adventure_tours_get_option( 'logo_type' ) ) {
	echo strtr(
		'<a id="logoLink" href="{homeUrl}">{name}</a>',
		array(
			'{homeUrl}' => esc_url( home_url( '/' ) ),
			'{name}' => esc_html( get_bloginfo( 'name' ) ),
		)
	);
} else {
	echo strtr(
		'<a id="logoLink" href="{homeUrl}">' .
			'<img id="normalImageLogo" src="{logoUrl}" alt="{blogNameAtr}" title="{blogDescriptionAtr}">' .
			'<img id="retinaImageLogo" src="{retinaLogoUrl}" alt="{blogNameAtr}" title="{blogDescriptionAtr}">' .
		'</a>',
		array(
			'{homeUrl}' => esc_url( home_url( '/' ) ),
			'{blogNameAtr}' => esc_attr( get_bloginfo( 'name' ) ),
			'{blogDescriptionAtr}' => esc_attr( get_bloginfo( 'description' ) ),
			'{logoUrl}' => esc_url( adventure_tours_get_option( 'logo_image' ) ),
			'{retinaLogoUrl}' => esc_url( adventure_tours_get_option( 'logo_image_retina' ) ),
		)
	);
} ?>
</div>
