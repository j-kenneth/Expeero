<?php
/**
 * Social icons rendering template part.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.2.2
 */

$social_icons = array(
	'facebook' => 'facebook',
	'twitter' => 'twitter',
	'googleplus' => 'google-plus',
	'pinterest' => 'pinterest',
	'linkedin' => 'linkedin',
	'instagram' => 'instagram',
	'dribbble' => 'dribbble',
	'tumblr' => 'tumblr',
	'vk' => 'vk',
);

$links_set = array();
foreach ( $social_icons as $key => $icon_class ) {
	$url = adventure_tours_get_option( 'social_link_' . $key );
	if ( $url ) {
		$links_set[] = array(
			'icon_class' => 'fa fa-' . $icon_class,
			'url' => $url
		);
	}
}

for( $i = 1; $i <= 5; $i++ ) {
	$url = adventure_tours_get_option( "social_link_{$i}_is_active" ) ? adventure_tours_get_option( "social_link_{$i}_url" ) : null;
	if ( ! $url ) {
		continue;
	}
	$icon_class = adventure_tours_get_option( "social_link_{$i}_icon" );
	if ( $icon_class ) {
		$links_set[] = array(
			'icon_class' => 'fa ' . $icon_class,
			'url' => $url
		);
	}
}

$social_icons_html = '';
foreach ( $links_set as $link_info ) {
	$social_icons_html .= '<a href="' . esc_url( $link_info['url'] ) . '"><i class="' . esc_attr( $link_info['icon_class'] ) . '"></i></a>';
}
if ( $social_icons_html ) {
	printf( '<div class="header__info__item header__info__item--delimiter header__info__item--social-icons">%s</div>',
		$social_icons_html
	);
}
