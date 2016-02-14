<?php
/**
 * Orders report for orders related to the tours.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.3.2
 */

class WC_Report_ADT_General extends WC_Admin_Report {

	public $default_range = 'last_month';

	/**
	 * Get the legend for the main chart sidebar.
	 *
	 * @return array
	 */
	public function get_chart_legend() {
		return array();
	}

	public function get_current_range_code() {
		$result = ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : null;

		if ( $result && ! in_array( $result, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
			$current_range = null;
		}

		return $result ? $result : $this->default_range;
	}

	/**
	 * Output an export link.
	 */
	public function get_export_button() {
		$export_file_name = sprintf( 'tickets-report-%s-%s.csv',
			esc_attr( $this->get_current_range_code() ),
			date_i18n( 'Y-m-d', current_time('timestamp') )
		);

		printf( '<a href="#" download="%s" class="export_csv" data-export="table">%s</a>',
			esc_attr( $export_file_name ),
			esc_html__( 'Export CSV', 'adventure-tours' )
		);
	}

	/**
	 * Output the report.
	 */
	public function output_report() {

		$ranges = array(
			'year'         => __( 'Year', 'adventure-tours' ),
			'last_month'   => __( 'Last Month', 'adventure-tours' ),
			'month'        => __( 'This Month', 'adventure-tours' ),
			'7day'         => __( '7 Day', 'adventure-tours' ),
		);

		$current_range = $this->get_current_range_code();

		$this->calculate_current_range( $current_range );

		$hide_sidebar = true;

		include( WC()->plugin_path() . '/includes/admin/views/html-report-by-date.php');
	}

	/**
	 * Output the main chart.
	 */
	public function get_main_chart() {
		global $wpdb;

		$query_data = array(
			'_product_id' => array(
				'type' => 'order_item_meta',
				'order_item_type' => 'line_item',
				'function' => '',
				'name' => 'product_id'
			),
			'_qty' => array(
				'type' => 'order_item_meta',
				'order_item_type' => 'line_item',
				'function' => 'SUM',
				'name' => 'quantity'
			),
			'order_id' => array(
				'type' => 'order_item',
				'order_item_type' => 'line_item',
				'function' => 'GROUP_CONCAT',
				'name' => 'order_ids',
			),
			'post_status' => array(
				'type' => 'post_data',
				'order_item_type' => 'line_item',
				'function' => '',
				'name' => 'order_status',
			),
			'tour_date' => array(
				'type' => 'order_item_meta',
				'order_item_type' => 'line_item',
				'function' => '',
				'name' => 'date',
			),
		);

		$where_meta = array();

		$product_ids = array();
		if ( isset( $_GET['item_ids'] ) && !empty( $_GET['item_ids'] ) ) {
			$product_ids = array_map( 'absint', (array) $_GET['item_ids'] );
		}

		if ( $product_ids ) {
			$where_meta[] = array(
				'type' => 'order_item_meta',
				'meta_key' => '_product_id',
				'operator' => 'in',
				'meta_value' => $product_ids
			);
		}

		$wpdb->query('SET SQL_BIG_SELECTS=1');

		$raw_rows = $this->get_order_report_data( array(
			'data'                => $query_data,
			'where_meta'          => $where_meta,

			'order_by'            => 'date, product_id DESC',
			'group_by'            => 'product_id, date, order_status',
			'query_type'          => 'get_results',
			'filter_range'        => true,

			'order_types'         => array_merge( wc_get_order_types( 'sales-reports' ), array( 'shop_order_refund' ) ),
			'order_status'        => array( 'completed', 'processing', 'on-hold' ),
			'parent_order_status' => array( 'completed', 'processing', 'on-hold' ) // Partial refunds inside refunded orders should be ignored
		) );

		$records = array();
		$stuses_list = wc_get_order_statuses();
		$booking_form = adventure_tours_di( 'booking_form' );

		foreach ( $raw_rows as $row ) {
			$product = wc_get_product( $row->product_id );
			$row->item_title = $product->get_title();
			$row->item_permalink = get_permalink( $row->product_id );
			$row->order_status_label = isset( $stuses_list[ $row->order_status ] ) ? $stuses_list[ $row->order_status ] : $row->order_status;
			$row->booking_date_formatted = $booking_form ? $booking_form->convert_date_for_human( $row->date ) : $row->date;

			$records[] = $row;
		}
		?>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php _e( 'Tour', 'adventure-tours' ); ?></th>
					<th><?php _e( 'Date', 'adventure-tours' ); ?></th>
					<th><?php _e( 'Tickets', 'adventure-tours' ); ?></th>
					<th><?php _e( 'Status', 'adventure-tours' ); ?></th>
					<th><?php _e( 'Orders', 'adventure-tours' ); ?></th>
					<th><?php _e( 'URL', 'adventure-tours' ); ?></th>
				</tr>
			</thead>
			<?php if ( $records ) : ?>
				<tbody>
					<?php foreach ( $records as $row ) { ?>
						<?php 
							
						?>
						<tr>
							<th scope="row"><?php printf( '<a href="%s">%s</a>', esc_url( $row->item_permalink ), esc_html( $row->item_title ) ); ?></th>
							<td><?php echo esc_html( $row->booking_date_formatted ); ?></td>
							<td class="total_row"><?php echo esc_html( $row->quantity ); ?></td>
							<td><?php echo esc_html( $row->order_status_label ); ?></td>
							<td><?php echo '#' . join(', #', explode( ',', $row->order_ids ) ); ?></td>
							<td><?php echo esc_url( $row->item_permalink ); ?></td>
						</tr>
						<?php
					}
					?>
				</tbody>
			<?php else : ?>
				<tbody>
					<tr>
						<td><?php _e( 'No records found in this period', 'adventure-tours' ); ?></td>
					</tr>
				</tbody>
			<?php endif; ?>
		</table>
		<?php
	}
}
