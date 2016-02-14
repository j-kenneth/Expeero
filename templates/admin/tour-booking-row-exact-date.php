<?php
/**
 * View for rendering tour booking period settings.
 *
 * @var assoc $row
 * @var int   $rowIndex
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.1.0
 */

if ( ! isset( $value ) || ! isset( $row_field_name ) ) {
	return;
}
?>
<div class="tour-booking-row__exact-date-row" data-role="row">
	<?php printf('<input type="text" placeholder="YYYY-MM-DD" data-role="datepicker" class="tour-booking-row__exact-date-row__date-field" name="%s[exact_dates][]" value="%s">',
		esc_attr( $row_field_name ),
		$value
	); ?>
	<div class="tour-booking-row__exact-date-row__actions-box">
		<a href="#" class="tour-booking-row__remove-btn remove_exact_date_btn"></a>
	</div>
</div>
