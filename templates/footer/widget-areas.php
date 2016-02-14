<?php
/**
 * Footer widgets area template.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

$columnsCount = adventure_tours_get_footer_columns();

$hasActiveArea = false;
$curIndex = $columnsCount;
while ( $curIndex >= 1 ) {
	if ( is_active_sidebar( 'footer' . $curIndex ) ) {
		$hasActiveArea = true;
		break;
	}
	$curIndex--;
}

if ( ! $hasActiveArea ) {
	return '';
}

$col_class = $columnsCount <= 4 ? 'col-md-' . (12 / $columnsCount) : 'col-md-3';
?>
<div class="container">
	<div class="row margin-top margin-bottom footer__widgets-areas">
<?php 
	for ( $i = 1; $i <= $columnsCount; $i++ ) {
		echo '<div class="' . esc_attr( $col_class ) . '">';
		dynamic_sidebar( 'footer' . $i );
		echo '</div>';
	}
?>
	</div>
</div>
