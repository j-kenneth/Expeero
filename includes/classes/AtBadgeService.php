<?php
/**
 * Service for the tour badges rendering.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

class AtBadgeService extends TdComponent
{
	/**
	 * Number of the available badges.
	 *
	 * @var integer
	 */
	public $count = 3;

	/**
	 * Returns badge settings for specific tour.
	 *
	 * @param  integer $tour_id
	 * @param  boolean $ignore_inactive
	 * @return assoc
	 */
	public function get_tour_badge($tour_id, $ignore_inactive = true) {
		$bid = $tour_id ? vp_metabox('tour_tabs_meta.tour_badge', null, $tour_id) : null;

		if ( $this->is_active( $bid ) ) {
			return array(
				'title' => $this->get_title( $bid ),
				'color' => $this->get_color( $bid ),
			);
		}
		return null;
	}

	/**
	 * Returns title for specific badge.
	 *
	 * @param  integer $bid
	 * @return string
	 */
	public function get_title($bid) {
		return $this->get_field( $bid, 'title' );
	}

	/**
	 * Returns color for specific badge.
	 *
	 * @param  integer $bid
	 * @return string
	 */
	public function get_color($bid) {
		return $this->get_field( $bid, 'color' );
	}

	/**
	 * Checks if specific badge is active.
	 *
	 * @param  integer $bid
	 * @return string
	 */
	public function is_active($bid) {
		return $this->get_field( $bid, 'is_active' );
	}

	/**
	 * Returns badge field value.
	 *
	 * @param  integer $bid
	 * @param  string  $field
	 * @return string
	 */
	public function get_field($bid, $field) {
		if ( $bid > 0 && $bid <= $this->count && $field ) {
			$option = "tour_badge_{$bid}_{$field}";
			return adventure_tours_get_option( $option );
		}
		return null;
	}

	/**
	 * Returns count of badges available in system.
	 *
	 * @return integer
	 */
	public function get_count() {
		return $this->count;
	}

	/**
	 * Returns set of active badges.
	 *
	 * @return array
	 */
	public function get_list() {
		static $list;

		if ( null === $list ) {
			$list = array();

			for ( $i = 1; $i <= $this->count; $i++ ) {
				if ( $this->is_active( $i ) ) {
					$list[$i] = $this->get_title( $i );
				}
			}
		}

		return $list;
	}
}
