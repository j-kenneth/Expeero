<?php
/**
 * View for rendering tour booking period settings.
 *
 * @var assoc $row
 * @var int   $rowIndex
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.2.3
 */

if ( ! isset( $value ) || ! isset( $row_field_name ) ) {
	return;
}
?>
<div class="tour-booking-row__time-row" data-role="row">
	<?php printf('<input type="text" placeholder="HH:ii" data-role="timepicker" class="tour-booking-row__time-row__time-field" name="%s[times][]" value="%s">',
		esc_attr( $row_field_name ),
		$value
	); ?>

	<?php /*printf('<input type="text" placeholder="%s" data-role="amountpicker" class="tour-booking-row__time-row__time-field" name="%s[prices][]" value="%s">',
		'0%',
		esc_attr( $row_field_name ),
		isset( $price_value ) ? $price_value : ''
	);*/ ?>

	<div class="tour-booking-row__time-row__actions-box">
		<a href="#" class="tour-booking-row__remove-btn remove_time_btn"></a>
	</div>
</div>
