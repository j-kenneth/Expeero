<?php
/**
 * Breadcrumbs template.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

if ( ! adventure_tours_get_option( 'breadcrumbs_is_show' ) ) {
	return;
}
$breadcrumbs_html = adventure_tours_di( 'breadcrumbs' )->get_html();
if ( $breadcrumbs_html ) {
	printf( '<div class="breadcrumbs">%s</div>', $breadcrumbs_html );
}
