<?php
/**
 * Special product type for the variable tour entity.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.8
 */

class WC_Product_Tour_Variable extends WC_Product_Variable
{
	/**
	 * Construct.
	 *
	 * @access public
	 * @param mixed $product
	 */
	public function __construct( $product )
	{
		parent::__construct( $product );
		$this->product_type = 'tour';
		$this->virtual = 'yes';
		$this->downloadable = 'no';
	}

	public function is_variable_tour() {
		return $this->variable_tour == 'yes';
	}

	public function __get( $key ) {
		if ( 'variable_tour' == $key ) {
			$value = get_post_meta( $this->id, '_' . $key, true );
			return $this->variable_tour = $value ? $value : 'no';
		} else {
			return parent::__get( $key );
		}
	}

	/**
	 * Returns array that conintains ids of related tours.
	 *
	 * @param  int   $limit
	 * @return array
	 */
	public function get_related( $limit = 5 ) {
		$transient_name = 'wc_related_' . $limit . '_' . $this->id . WC_Cache_Helper::get_transient_version( 'product' );

		if ( false === ( $related_posts = get_transient( $transient_name ) ) ) {
			global $wpdb;

			// Related products are found from category and tag
			$tags_array = $this->get_related_terms( 'product_tag' );
			$tour_cats_array = $this->get_related_terms( 'tour_category' );

			// Don't bother if none are set
			if ( sizeof( $tour_cats_array ) == 1 && sizeof( $tags_array ) == 1 ) {
				$related_posts = array();
			} else {
				// Sanitize
				$exclude_ids = array_map( 'absint', array_merge( array( 0, $this->id ), $this->get_upsells() ) );

				// Generate query
				$query = $this->build_related_tours_query( $tour_cats_array, $tags_array, $exclude_ids, $limit );

				// Get the posts
				$related_posts = $wpdb->get_col( implode( ' ', $query ) );
			}

			set_transient( $transient_name, $related_posts, DAY_IN_SECONDS * 30 );
		}

		shuffle( $related_posts );

		return $related_posts;
	}

	/**
	 * Builds the related posts query
	 *
	 * @param array $tour_cats_array
	 * @param array $tags_array
	 * @param array $exclude_ids
	 * @param int   $limit
	 * @return string
	 */
	protected function build_related_tours_query( $tour_cats_array, $tags_array, $exclude_ids, $limit ) {
		global $wpdb;

		$limit = absint( $limit );

		$query           = array();
		$query['fields'] = "SELECT DISTINCT ID FROM {$wpdb->posts} p";
		$query['join']   = " INNER JOIN {$wpdb->postmeta} pm ON ( pm.post_id = p.ID AND pm.meta_key='_visibility' )";
		$query['join']  .= " INNER JOIN {$wpdb->term_relationships} tr ON (p.ID = tr.object_id)";
		$query['join']  .= " INNER JOIN {$wpdb->term_taxonomy} tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)";
		$query['join']  .= " INNER JOIN {$wpdb->terms} t ON (t.term_id = tt.term_id)";

		if ( get_option( 'woocommerce_hide_out_of_stock_items' ) === 'yes' ) {
			$query['join'] .= " INNER JOIN {$wpdb->postmeta} pm2 ON ( pm2.post_id = p.ID AND pm2.meta_key='_stock_status' )";
		}

		$query['where']  = " WHERE 1=1";
		$query['where'] .= " AND p.post_status = 'publish'";
		$query['where'] .= " AND p.post_type = 'product'";
		$query['where'] .= " AND p.ID NOT IN ( " . implode( ',', $exclude_ids ) . " )";
		$query['where'] .= " AND pm.meta_value IN ( 'visible', 'catalog' )";

		if ( get_option( 'woocommerce_hide_out_of_stock_items' ) === 'yes' ) {
			$query['where'] .= " AND pm2.meta_value = 'instock'";
		}

		if ( apply_filters( 'woocommerce_product_related_posts_relate_by_tour_category', true, $this->id ) ) {
			$query['where'] .= " AND ( tt.taxonomy = 'tour_category' AND t.term_id IN ( " . implode( ',', $tour_cats_array ) . " ) )";
			$andor = 'OR';
		} else {
			$andor = 'AND';
		}

		// when query is OR - need to check against excluded ids again
		if ( apply_filters( 'woocommerce_product_related_posts_relate_by_tag', true, $this->id ) ) {
			$query['where'] .= " {$andor} ( ( tt.taxonomy = 'product_tag' AND t.term_id IN ( " . implode( ',', $tags_array ) . " ) )";
			$query['where'] .= " AND p.ID NOT IN ( " . implode( ',', $exclude_ids ) . " ) )";
		}

		$query['limits'] = " LIMIT {$limit} ";
		$query           = apply_filters( 'woocommerce_product_related_posts_query', $query, $this->id );

		return $query;
	}
}
