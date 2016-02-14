<?php
/**
 * Helper that contains functions related to shortcodes.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

class AtShortcodesHelperService extends TdComponent
{
	/**
	 * Get shortcode identifier.
	 *
	 * @return integer
	 */
	public function generate_id(){
		static $id = 0;
		$id++;

		return $id;
	}

	/**
	 * Checks if values of the boolean attribute is true.
	 *
	 * @param  string $value
	 * @return boolean
	 */
	public function attribute_is_true( $value ) {
		if ( ! $value || in_array( $value, array( 'no','false', 'off' ) ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Returns collection of WC_Product_Tour instances based on attribute values used in shortcodes
	 * related to the tours rendering.
	 *
	 * @param  assoc $atts shorcode attributes.
	 * @return array
	 */
	public function get_tours_collection( $atts ) {
		$result = array();
		$items = $this->get_tours_query( $atts )->get_posts();

		foreach ( $items as $item ) {
			$result[] = wc_get_product( $item );
		}

		return $result;
	}

	/**
	 * Returns WP_Query instance based on attribute values used in shortcodes related to the tours rendering.
	 *
	 * @param  assoc $atts shorcode attributes.
	 * @return array
	 */
	public function get_tours_query( $atts ) {
		$number  = ! empty( $atts['number'] ) ? absint( $atts['number'] ) : '-1';
		$show    = ! empty( $atts['show'] ) ? sanitize_title( $atts['show'] ) : '';
		$orderby = ! empty( $atts['orderby'] ) ? sanitize_title( $atts['orderby'] ) : '';
		$order   = ! empty( $atts['order'] ) ? sanitize_title( $atts['order'] ) : 'ASC';

		$is_wc_loaded = $this->check( 'is_wc_loaded' );

		$query_args = array(
			'wc_query'       => 'tours', // tours query marker
			'posts_per_page' => $number,
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'no_found_rows'  => 1,
			'order'          => $order,
			'meta_query'     => array(),
			'tax_query'      => array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'product_type',
					'terms' => 'tour',
					'field' => 'slug',
					'operator' => 'IN',
				),
			),
		);

		if ( ! empty( $atts['tour_ids'] ) ) {
			$query_args['post__in'] = explode(',', $atts['tour_ids']);
		}

		if ( empty( $atts['show_hidden'] ) ) {
			if ( $is_wc_loaded ) {
				$query_args['meta_query'][] = WC()->query->visibility_meta_query();
			}
			$query_args['post_parent']  = 0;
		}

		if ( ! empty( $atts['hide_free'] ) ) {
			$query_args['meta_query'][] = array(
				'key'     => '_price',
				'value'   => 0,
				'compare' => '>',
				'type'    => 'DECIMAL',
			);
		}

		if ( ! empty( $atts['tour_category'] ) && $this->check( 'tour_category_taxonomy_exists' ) ) {
			$query_args['tax_query'][] = array(
				'taxonomy' => 'tour_category',
				'terms' => array_map( 'sanitize_title', explode( ',', $atts['tour_category'] ) ),
				'field' => 'slug',
				'operator' => 'IN',
			);
		}

		if ( $is_wc_loaded ) {
			$query_args['meta_query'][] = WC()->query->stock_status_meta_query();
			$query_args['meta_query']   = array_filter( $query_args['meta_query'] );
		}

		switch ( $show ) {
		case 'featured' :
			$query_args['meta_query'][] = array(
				'key'   => '_featured',
				'value' => 'yes'
			);
			break;

		case 'onsale' :
			if ( empty( $atts['tour_ids'] ) ) {
				$product_ids_on_sale    = $is_wc_loaded ? wc_get_product_ids_on_sale() : array();
				$product_ids_on_sale[]  = 0;
				$query_args['post__in'] = $product_ids_on_sale;
			}
			break;
		}

		switch ( $orderby ) {
		case 'price' :
			$query_args['meta_key'] = '_price';
			$query_args['orderby']  = 'meta_value_num';
			break;

		case 'rand' :
			$query_args['orderby']  = 'rand';
			break;

		case 'sales' :
			$query_args['meta_key'] = 'total_sales';
			$query_args['orderby']  = 'meta_value_num';
			break;

		default :
			$query_args['orderby']  = 'date';
		}

		$is_most_popular_query = $is_wc_loaded && $orderby == 'most_popular';
		if ( $is_most_popular_query ) {
			add_filter( 'posts_clauses',  array( WC()->query, 'order_by_rating_post_clauses' ) );
		}

		$result_query = new WP_Query( $query_args );

		if ( $is_most_popular_query ) {
			remove_filter( 'posts_clauses', array( WC()->query, 'order_by_rating_post_clauses' ) );
		}

		return $result_query;
	}

	/**
	 * Makes different checks required for correct plugin working.
	 *
	 * @param  string $check_name check uniq. code.
	 * @return boolean
	 */
	protected function check( $check_name ) {
		$result = false;

		switch ( $check_name ) {
		case 'is_wc_loaded':
			$result = function_exists( 'WC' );
			break;
		case 'tour_category_taxonomy_exists':
			$result = taxonomy_exists( 'tour_category' );
			break;
		}

		return $result;
	}
}
