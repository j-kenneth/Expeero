<?php
/**
 * Page header section template.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

if ( is_404() ) {
	return;
}

$section_meta_service = adventure_tours_di( 'header_section' );
$section_meta = $section_meta_service ? $section_meta_service->get_section_meta() : array();

// $mode == 'hide' means "default" mode.
$mode = isset( $section_meta['section_mode'] ) ? $section_meta['section_mode'] : 'hide';

if ( 'banner' == $mode && empty( $section_meta['banner_image'] ) ) {
	$mode = 'hide';
}

if ('hide' == $mode) {
	// To hide default title for home page.
	if ( is_front_page() ) {
		return;
	}
}

switch ( $mode ) {
case 'banner':
	adventure_tours_render_template_part( 'templates/header/banner', '', $section_meta );
	break;

case 'slider':
	adventure_tours_render_template_part( 'templates/header/slider', '', $section_meta );
	break;

case 'hide':
default:
	adventure_tours_render_template_part( 'templates/header/title-block', '', $section_meta );
	break;
}
