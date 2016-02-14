<?php
/**
 * Class for building tour related queries.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.2.4
 */

class WC_Tour_WP_Query
{
	public function init() {
		if ( ! is_admin() ) {
			add_action( 'pre_get_posts', array( $this, 'filter_pre_get_posts' ) );
			add_filter( 'query_vars', array( $this, 'filter_query_vars' ) );
		}
	}

	public function filter_pre_get_posts( $q ) {
		if ( ! $q->is_main_query() ) {
			if ( 'product' == $q->get( 'post_type' ) ) {
				// For all queries that is not marked as tours query we excluding products with 'tour' product_type value.
				$is_tour_query = 'tours' == $q->get('wc_query'); // checking is this is tour query
				if ( ! $is_tour_query  ) {
					$cur_tax_query = $q->get( 'tax_query' );
					if ( ! $cur_tax_query ) {
						$cur_tax_query = array();
					}
					$cur_tax_query['relation'] = 'AND';
					$cur_tax_query[] = $this->get_tour_tax_query( true );

					$q->set( 'wc_query', 'tours' );
					$q->set( 'tax_query', $cur_tax_query );
				}
			}
			return;
		}

		$isTourArchivePage = false;
		if ( ! empty( $q->query_vars['toursearch'] ) ) {
			$isTourArchivePage = true;
		} elseif ( $tours_page_id = $this->get_tours_page_id() ) {
			/*if ( $q->get('page_id') == $tours_page_id || $q->get('pagename') == get_post($tours_page_id)->post_name ) {
				$isTourArchivePage = true;
			}*/
			if ( $GLOBALS['wp_rewrite']->use_verbose_page_rules && isset( $q->queried_object->ID ) && $q->queried_object->ID == $tours_page_id ) {
				$isTourArchivePage = true;
			}
		}

		if ( $isTourArchivePage ) {
			$q->set( 'is_tour_query', 1 );
			$q->set( 'wc_query', 'tours' );

			$q->set( 'post_type', 'product' );
			$q->set( 'page', '' );
			$q->set( 'pagename', '' );

			// items sorting options
			$ordering = $this->get_archive_ordering_args();
			$q->set( 'orderby', $ordering['orderby'] );
			$q->set( 'order', $ordering['order'] );
			if ( isset( $ordering['meta_key'] ) ) {
				$q->set( 'meta_key', $ordering['meta_key'] );
			}

			$q->is_singular          = false;
			$q->is_post_type_archive = true;
			$q->is_archive           = true;
			$q->is_page              = false;
			if ( $q->is_home ) {
				$q->is_home = false;
				/*if ( 'page' != get_option( 'show_on_front') ) {
					$q->is_home = false;
				} else {
					$tours_page_id = $this->get_tours_page_id();
					if ( ! $tours_page_id || $tours_page_id != get_option( 'page_on_front' ) ) {
						$q->is_home = false;
					}
				}*/
			}
		}

		if ( 'product' == $q->get( 'post_type' ) ) {
			$taxQuery = array(
				'relation' => 'AND',
				$this->get_tour_tax_query( !$isTourArchivePage ),
			);

			if ( $isTourArchivePage ) {
				$tourTaxonomies = ! empty( $q->query_vars['tourtax'] ) ? $q->query_vars['tourtax'] : array();
				$taxConditions = array(
					// Alternative logic: 'relation' => 'OR'
				);
				if ( $tourTaxonomies ) {
					foreach ( $tourTaxonomies as $taxName => $taxValue ) {
						if ( ! $taxValue ) {
							continue;
						}

						$_slugs = wp_unslash( (array) $taxValue );

						$need_expand_slugs = false;
						if ( ! $need_expand_slugs ) {
							$taxConditions[] = array(
								'taxonomy' => $taxName,
								'terms' => $_slugs,
								'field' => 'slug',
							);
						} else {
							$_slug_ids = array();
							foreach ( $_slugs as $cur_slug ) {
								$term = get_term_by( 'slug', $cur_slug, $taxName );
								if ( $term ) {
									$_slug_ids[] = $term->term_id;
								} else {
									$_slug_ids[] = 0;
								}
							}

							$taxConditions[] = array(
								'taxonomy' => $taxName,
								'terms' => $_slug_ids,
								'field' => 'term_id',
							);
						}
					}
				}

				if ( $taxConditions ) {
					$taxQuery[] = $taxConditions;
				}
			}

			if ( $taxQuery ) {
				$q->set( 'tax_query', $taxQuery );
			}
		} elseif ( $q->is_tax( 'tour_category' ) ) {
			// items sorting options
			$ordering = $this->get_archive_ordering_args();
			$q->set( 'orderby', $ordering['orderby'] );
			$q->set( 'order', $ordering['order'] );
			if ( isset( $ordering['meta_key'] ) ) {
				$q->set( 'meta_key', $ordering['meta_key'] );
			}
		}
		return $q;
	}

	/**
	 * Adds query vars used for tours filtering.
	 *
	 * @param  array $vars set of query vars.
	 * @return array
	 */
	public function filter_query_vars($vars) {
		$vars[] = 'toursearch';
		$vars[] = 'tourtax';
		return $vars;
	}

	public function get_tours_page_id() {
		static $result;
		if ( null == $result ) {
			$result = adventure_tours_get_option( 'tours_page', 0 );
		}
		return $result;
	}


	/**
	 * Returns an array of arguments for ordering tour based on the selected values.
	 *
	 * @param string $orderby
	 * @param string $order
	 * @return array
	 */
	public function get_archive_ordering_args( $orderby = '', $order = '' ) {
		global $wpdb;

		// Get ordering from query string unless defined
		if ( ! $orderby ) {
			$orderby_value = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'adventure_tours_default_archive_orderby', adventure_tours_get_option( 'tours_archive_orderby' ) );

			// Get order + orderby args from string
			$orderby_value = explode( '-', $orderby_value );
			$orderby       = esc_attr( $orderby_value[0] );
			$order         = ! empty( $orderby_value[1] ) ? $orderby_value[1] : $order;
		}

		$orderby = strtolower( $orderby );
		$order   = strtoupper( $order );
		$args    = array();

		// default - menu_order
		$args['orderby']  = 'menu_order title';
		$args['order']    = $order == 'DESC' ? 'DESC' : 'ASC';
		$args['meta_key'] = '';

		switch ( $orderby ) {
			case 'rand' :
				$args['orderby']  = 'rand';
			break;
			case 'date' :
				$args['orderby']  = 'date';
				$args['order']    = $order == 'ASC' ? 'ASC' : 'DESC';
			break;
			case 'price' :
				$args['orderby']  = "meta_value_num {$wpdb->posts}.ID";
				$args['order']    = $order == 'DESC' ? 'DESC' : 'ASC';
				$args['meta_key'] = '_price';
			break;
			case 'popularity' :
				$args['meta_key'] = 'total_sales';

				// Sorting handled later though a hook
				add_filter( 'posts_clauses', array( 'WC_Query', 'order_by_popularity_post_clauses' ) );
			break;
			case 'rating' :
				// Sorting handled later though a hook
				add_filter( 'posts_clauses', array( 'WC_Query', 'order_by_rating_post_clauses' ) );
			break;
			case 'title' :
				$args['orderby']  = 'title';
				$args['order']    = $order == 'DESC' ? 'DESC' : 'ASC';
			break;
		}

		return apply_filters( 'adventure_tours_get_archive_ordering_args', $args );
	}

	/**
	 * Builds tax query for filtering/excluding tours from WooCommerce product posts.
	 *
	 * @param boolean $invert
	 * @param boolean $rebuild
	 * @return assoc
	 */
	protected function get_tour_tax_query( $invert = false, $rebuild = false ) {
		static $cache;

		if ( null === $cache || $rebuild ) {
			$tax_name = 'product_type';
			$term_slug = 'tour';
			$tour_term = get_term_by( 'slug', $term_slug, $tax_name );

			if ( $tour_term ) {
				$cache = array(
					'taxonomy' => $tax_name,
					'field' => 'term_id',
					'terms' => array( $tour_term->term_id ),
					'operator' => 'IN',
				);
			} else {
				$cache = array(
					'taxonomy' => $tax_name,
					'field' => 'slug',
					'terms' => array( $term_slug ),
					'operator' => 'IN',
				);
			}
		}

		return !$invert ? $cache : array_merge( $cache, array(
			'operator' => 'NOT IN'
		) );
	}
}
