<?php
/**
 * Functions related to Theme Options section.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.2.0
 */

// checks if user selected header section slider
// @see metabox/header-section-meta.php
function adventure_tours_vp_header_section_is_slider($value) {
	return $value == 'slider';
}
VP_Security::instance()->whitelist_function( 'adventure_tours_vp_header_section_is_slider' );

// checks if user selected header section banner
// @see metabox/header-section-meta.php
function adventure_tours_vp_header_section_is_banner($value) {
	return $value == 'banner';
}
VP_Security::instance()->whitelist_function( 'adventure_tours_vp_header_section_is_banner' );

// checks if user selected header section from list
// @see metabox/header-section-meta.php
function adventure_tours_vp_header_section_from_list($value) {
	return $value == 'from_list';
}
VP_Security::instance()->whitelist_function( 'adventure_tours_vp_header_section_from_list' );

// checks if user selected custom email in section faq
// @see theme-options.php
function adventure_tours_vp_faq_is_custom_email($value) {
	return $value == 'custom_email';
}
VP_Security::instance()->whitelist_function( 'adventure_tours_vp_faq_is_custom_email' );

// dependency function used for the logo management
// @see theme-options.php
function adventure_tours_vp_dep_value_equal_image($value) {
	return $value == 'image';
}
VP_Security::instance()->whitelist_function( 'adventure_tours_vp_dep_value_equal_image' );

// dependency function used to determine if selector for start category should be displayed/hidden
// @see theme-options.php
function adventure_tours_vp_is_tour_categories_visible_on_search($values) {
	return $values && is_array($values) && in_array('__tour_categories_filter', $values);
}
VP_Security::instance()->whitelist_function( 'adventure_tours_vp_is_tour_categories_visible_on_search' );

/**
 * Checks if user selected tours page style grid.
 * 
 * @return boolean
 */
function adventure_tours_vp_tour_page_style_is_grid( $value ) {
	return $value == 'grid';
}
VP_Security::instance()->whitelist_function( 'adventure_tours_vp_tour_page_style_is_grid' );

/**
 * Theme options helper function.
 * Returns list of available attributes (attributes that have few values saved for a tours) for tour entities.
 *
 * @return array
 */
function adventure_tours_vp_get_tour_attributes_list() {
	static $cache;

	$ignore_empty_lists = false;
	if ( null != $cache ) {
		return $cache;
	}

	$result = array();

	if ( adventure_tours_check( 'tour_category_taxonomy_exists' ) ) {
		$result[] = array(
			'value' => '__tour_categories_filter',
			'label' => esc_html__( 'Tour Categories', 'adventure-tours' ),
		);
	}

	$list = AtTourHelper::get_available_attributes(true, true);

	if ( $list ) {
		foreach ($list as $attributeName => $attributeValues) {
			if ( $ignore_empty_lists && count($attributeValues) < 2 ) { 
				// Checking if list contains more than 1 value, as 1-st one is field label.
				continue;
			}
			$result[] = array(
				'value' => $attributeName,
				'label' => array_shift($attributeValues)
			);
		}
	}

	$cache = $result;

	return $result;
}

/**
 * Return list for tour_category selector.
 *
 * @return array
 */
function adventure_tours_vp_get_tour_start_category_list() {
	$result = array();

	if ( ! adventure_tours_check( 'tour_category_taxonomy_exists' ) ) {
		return $result;
	}

	$list = get_terms( 'tour_category', array( 'hierarchical' => true ) );

	if ( $list ) {

		$top = array();
		$children = array();

		foreach ( $list as $item ) {
			$el = array(
				'value' => $item->term_id,
				'label' => $item->name . "({$item->count})",
			);

			if ( $item->parent ) {
				$children[$item->parent][$item->term_id] = $el;
			} else {
				$top[$item->term_id] = $el;
			}
		}

		foreach ($top as $top_id => $el) {
			__at_vp_cat_list_walker($result, $children, $top_id, $el, '');
		}
	}

	return $result;
}

/**
 * Walker function for categories list making function.
 *
 * @param  array  &$set     link to current items set.
 * @param  assoc  $children mapper that contains set of items for each parent.
 * @param  string $cur_id   current element id.
 * @param  assoc  $el       element that should be added to set.
 * @param  string $pad
 * @return void
 */
function __at_vp_cat_list_walker( &$set, $children, $cur_id, $el, $pad ) {
	if ( $pad ) {
		$el['label'] = $pad . $el['label'];
	}

	$set[] = $el;

	if ( isset( $children[$cur_id] ) ) {
		foreach ( $children[$cur_id] as $child_id => $child_el ) {
			__at_vp_cat_list_walker( $set, $children, $child_id, $child_el, $pad . '&nbsp;&nbsp;&nbsp;' );
		}
	}
}

/**
 * Returns options for the tour badge selector.
 *
 * @return array
 */
function adventure_tours_vp_badges_list() {
	$list = adventure_tours_di( 'tour_badge_service' )->get_list();

	$result = array(
		array(
			'value' => '',
			'label' => esc_html__( 'None', 'adventure-tours' ),
		)
	);

	foreach ($list as $bid => $title) {
		$result[] = array(
			'value' => $bid,
			'label' => $title,
		);
	}

	return $result;
}

/**
 * Returns options for tour display mode.
 *
 * @return array
 */
function adventure_tours_vp_archive_tour_display_modes_list() {
	$list = array(
		'products' => esc_html__( 'Tours', 'adventure-tours' ),
		'subcategories' => esc_html__( 'Categories', 'adventure-tours' ),
		'both' => esc_html__( 'Both', 'adventure-tours' ),
	);

	$result = array();
	foreach ($list as $val => $label) {
		$result[] = array(
			'value' => $val,
			'label' => $label
		);
	};
	return $result;
}

if ( ! function_exists( 'adventure_tours_vp_header_section_masks_list' ) ) {
	/**
	 * Returns set of masks available for header section image.
	 *
	 * @return array
	 */
	function adventure_tours_vp_header_section_masks_list() {
		$list = apply_filters( 'adventure_tours_get_header_section_masks', array(
			'' => esc_html__( 'None', 'adventure-tours' ),
			'default' => esc_html__( 'Default', 'adventure-tours' )
		) );

		$result = array();

		if ( $list ) {
			foreach ($list as $value => $label) {
				$result[] = array(
					'label' => $label,
					'value' => $value,
				);
			}
		}

		return $result;
	}
}
