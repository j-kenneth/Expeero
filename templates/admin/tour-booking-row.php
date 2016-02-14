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

if ( ! isset( $rowIndex ) ) {
	$rowIndex = 0;
}

$daysList = array(
	'Mon' => esc_html__( 'Monday', 'adventure-tours' ),
	'Tue' => esc_html__( 'Tuesday', 'adventure-tours' ),
	'Wed' => esc_html__( 'Wednesday', 'adventure-tours' ),
	'Thu' => esc_html__( 'Thursday', 'adventure-tours' ),
	'Fri' => esc_html__( 'Friday', 'adventure-tours' ),
	'Sat' => esc_html__( 'Saturday', 'adventure-tours' ),
	'Sun' => esc_html__( 'Sunday', 'adventure-tours' ),
);

$yesNoList = array(
	'0' => esc_html__( 'No', 'adventure-tours' ),
	'1' => esc_html__( 'Yes', 'adventure-tours' ),
);

$modesList = array(
	'default' => esc_html__( 'Week days', 'adventure-tours' ),
	'exact-dates' => esc_html__( 'Exact dates', 'adventure-tours' ),
);

$exact_date_template = 'templates/admin/tour-booking-row-exact-date';
$time_template = 'templates/admin/tour-booking-row-time';

$row_field_name = "tour-booking-row[{$rowIndex}]";

$code_of_default_mode = 'default';

if ( empty( $row['mode'] ) ) {
	$row['mode'] = $code_of_default_mode;
}

$default_mode_date_from = $code_of_default_mode == $row['mode'] && ! empty( $row['from'] ) ? $row['from'] : '';
$default_mode_date_to = $code_of_default_mode == $row['mode'] && ! empty( $row['to'] ) ? $row['to'] : '';
?>
<tr class="tour-booking-row">
	<td class="sort">&nbsp;</td>
	<td>
		<div data-mode-box="default">
				<div class="tour-booking-row__date-wrapper">
					<span><?php esc_html_e( 'Start date', 'adventure-tours' ); ?></span>
					<input type="text" placeholder="YYYY-MM-DD" style="width:95%;border:1px solid #ddd;" class="dateselector" name="<?php echo esc_attr( $row_field_name ); ?>[from]" value="<?php if ( $default_mode_date_from ) { echo esc_attr( $default_mode_date_from ); } ?>" />
				</div>
				<div class="tour-booking-row__date-wrapper">
					<span><?php esc_html_e( 'End date', 'adventure-tours' ); ?></span>
					<input type="text" placeholder="YYYY-MM-DD" style="width:95%;border:1px solid #ddd;" class="dateselector" name="<?php echo esc_attr( $row_field_name ); ?>[to]" value="<?php if ( $default_mode_date_to ) { echo esc_attr( $default_mode_date_to ); } ?>" />
				</div>
				<div style="clear:both"></div>
				<div class="tour-booking-row__days">
					<?php
					$selectedDays = ! empty( $row['days'] ) ? $row['days'] : array();
					$dayColumns = array_chunk( $daysList, 4, true );
					foreach ( $dayColumns as $colDays ) {
						echo '<div class="tour-booking-row__days__column">';
						foreach ( $colDays as $val => $text ) {
							printf('<div class="tour-booking-row__days__item"><input type="checkbox" name="%s[days][]" value="%s"%s> %s</div>',
								esc_attr( $row_field_name ),
								esc_attr( $val ),
								$selectedDays && in_array( $val, $selectedDays ) ? ' checked="checked"' : '',
								esc_html( $text )
							);
						}
						echo '</div>';
					}
					?>
					<div style="clear:both"></div>
				</div>
		</div>

		<div data-mode-box="exact-dates">
			<?php
				$row_exact_dates = isset( $row['exact_dates'] ) ? $row['exact_dates'] : array();
				foreach ( $row_exact_dates as $_date ) {
					echo adventure_tours_render_template_part( $exact_date_template, '', array(
						'value' => $_date,
						'row_field_name' => $row_field_name,
					), true );
				}
			?>
			<a href="#" class="button button-primary add_exact_date_btn"><?php esc_html_e( 'Add Date', 'adventure-tours' ); ?></a>
			<script data-role="exact-date-template" type="text/template">
			<?php
				echo adventure_tours_render_template_part( $exact_date_template, '', array(
					'value' => '',
					'row_field_name' => $row_field_name,
				), true );
			?>
			</script>
		</div>
	</td>
	<td>
		<div>
			<?php
				$times = isset( $row['times'] ) ? $row['times'] : array();
				$prices = isset( $row['prices'] ) ? $row['prices'] : array();
				foreach ( $times as $_tindex => $_time ) {
					echo adventure_tours_render_template_part( $time_template, '', array(
						'value' => $_time,
						'price_value' => isset( $prices[ $_tindex ] ) ? $prices[ $_tindex ] : '',
						'row_field_name' => $row_field_name,
					), true );
				}
			?>
			<a href="#" class="button button-primary add_time_btn"><?php esc_html_e( 'Add Time', 'adventure-tours' ); ?></a>
			<script data-role="time-template" type="text/template">
			<?php
				echo adventure_tours_render_template_part( $time_template, '', array(
					'value' => '',
					'row_field_name' => $row_field_name,
				), true );
			?>
			</script>
		</div>
	</td>
	<td>
		<div class="tour-booking-row__cell">
			<div><?php esc_html_e( 'Number of tickets per tour', 'adventure-tours' ); ?></div>
			<input type="text" name="<?php echo esc_attr( $row_field_name ); ?>[limit]" style="width:60px;" value="<?php echo isset( $row['limit'] ) ? esc_attr( $row['limit'] ) : '1' ; ?>">
			<div style="clear:both"></div>
		</div>

		<div class="tour-booking-row__cell tour-booking-row__cell--next-row">
			<div><?php esc_html_e( 'Special price', 'adventure-tours' ); ?>&nbsp;<a class="tips" data-tip="<?php echo esc_attr( "accepted values:<br>200% - to make 2x from original price;<br>+20 - to increase original price;<br>-50 - to reduce original price;<br>60 - to replace original price." ); ?>">[?]</a></div>
			<input type="text" name="<?php echo esc_attr( $row_field_name ); ?>[spec_price]" placeholder="0%" style="width:60px;" value="<?php echo isset( $row['spec_price'] ) ? esc_attr( $row['spec_price'] ) : '' ; ?>">
			<div style="clear:both"></div>
		</div>

		<div class="tour-booking-row__cell tour-booking-row__cell--next-row">
			<div><?php esc_html_e( 'Mode', 'adventure-tours' ); ?></div>
			<select name="<?php echo esc_attr( $row_field_name ); ?>[mode]">
			<?php
				$mode_value = $row['mode'];
				foreach ( $modesList as $val => $text ) {
					printf('<option value="%s"%s>%s</option>',
						esc_attr( $val ),
						$mode_value == $val ? ' selected="selected"' : '',
						esc_html( $text )
					);
				}
			?>
			</select>
			<div style="clear:both"></div>
		</div>

		<div class="tour-booking-row__cell tour-booking-row__cell--next-row">
			<div><?php esc_html_e( 'Is active?', 'adventure-tours' ); ?></div>
			<select name="<?php echo esc_attr( $row_field_name ); ?>[type]">
			<?php
				$type_value = isset( $row['type'] ) ? $row['type'] : '1';
				foreach ( $yesNoList as $val => $text ) {
					printf('<option value="%s"%s>%s</option>',
						esc_attr( $val ),
						$type_value == $val ? ' selected="selected"' : '',
						esc_html( $text )
					);
				}
			?>
			</select>
			<div style="clear:both"></div>
		</div>
		<div class="tour-booking-row__actions-cnt">
			<a href="#" data-role="remove-row" title="<?php esc_attr_e( 'remove', 'adventure-tours' ); ?>" class="tour-booking-row__remove-btn"></a>
		</div>
	</td>
</tr>
