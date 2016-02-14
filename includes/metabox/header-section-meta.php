<?php
/**
 * Config file for metabox fields defenition for header section block.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.2.1
 */

return array(
	array(
		'name' => 'section_mode',
		'label' => esc_html__( 'Display Mode','adventure-tours' ),
		'type' => 'radiobutton',
		'items' => array(
			array(
				'value' => 'hide',
				'label' => esc_html__( 'Default', 'adventure-tours' ),
			),
			array(
				'value' => 'banner',
				'label' => esc_html__( 'Image', 'adventure-tours' ),
			),
			array(
				'value' => 'slider',
				'label' => esc_html__( 'Slider', 'adventure-tours' ),
			),
			array(
				'value' => 'from_list',
				'label' => esc_html__( 'From list', 'adventure-tours' ),
			)
		),
		'default' => '{{first}}',
	),
	_adventure_tours_hsm_get_header_section_selector(),
	_adventure_tours_hsm_get_slider_selector(),
	array(
		'type' => 'textbox',
		'name' => 'banner_subtitle',
		'label' => esc_html__( 'Subtitle', 'adventure-tours' ),
		'dependency' => array(
			'field' => 'section_mode',
			'function' => 'adventure_tours_vp_header_section_is_banner',
		),
	),
	array(
		'name' => 'banner_image',
		'type' => 'upload',
		'label' => esc_html__( 'Image', 'adventure-tours' ),
		'default' => '',
		'dependency' => array(
			'field' => 'section_mode',
			'function' => 'adventure_tours_vp_header_section_is_banner',
		),
	),
	array(
		'name' => 'is_banner_image_parallax',
		'type' => 'toggle',
		'label' => esc_html__( 'Use Parallax', 'adventure-tours' ),
		'default' => '1',
		'dependency' => array(
			'field' => 'section_mode',
			'function' => 'adventure_tours_vp_header_section_is_banner',
		),
	),
	array(
		'name' => 'banner_image_repeat',
		'label' => esc_html__( 'Image repeat','adventure-tours' ),
		'type' => 'select',
		'items' => array(
			array(
				'value' => 'repeat',
				'label' => esc_html__( 'Repeat', 'adventure-tours' ),
			),
			array(
				'value' => 'no-repeat',
				'label' => esc_html__( 'No repeat', 'adventure-tours' ),
			),
			array(
				'value' => 'repeat-x',
				'label' => esc_html__( 'Repeat horizontally', 'adventure-tours' ),
			),
			array(
				'value' => 'repeat-y',
				'label' => esc_html__( 'Repeat vertically', 'adventure-tours' ),
			),
		),
		'default' => '{{first}}',
		'dependency' => array(
			'field' => 'section_mode',
			'function' => 'adventure_tours_vp_header_section_is_banner',
		),
	),
	array(
		'type' => 'select',
		'name' => 'banner_mask',
		'label' => esc_html__( 'Mask', 'adventure-tours' ),
		'dependency' => array(
			'field' => 'section_mode',
			'function' => 'adventure_tours_vp_header_section_is_banner',
		),
		'items' => array(
			'data' => array(
				array(
					'source' => 'function',
					'value' => 'adventure_tours_vp_header_section_masks_list',
				),
			),
		),
		'default' => '',
	),
);

function _adventure_tours_hsm_get_header_section_selector() {
	$list = array();

	$list[] = array(
		'value' => '',
		'label' => __( 'Default', 'adventure-tours' ),
	);

	$list[] = array(
		'value' => '-1',
		'label' => __( 'None', 'adventure-tours' ),
	);

	$exclude = array();
	// excluding current post to prevent looping (case when meta used for at_header_section post type)
	if ( !empty( $_GET['post'] ) && !empty( $_GET['action'] ) && 'edit' == $_GET['action'] ) {
		$exclude[] = (int) $_GET['post'];
	}

	$items = get_posts( array(
		'post_type' => 'at_header_section',
		'numberposts' => -1,
		'exclude' => $exclude,
	) );

	if ( $items ) {
		foreach ( $items as $item ) {
			if ( $exclude && $exclude == $item->ID ) {
				continue;
			}
			$list[] = array(
				'value' => $item->ID,
				'label' => $item->post_title,
			);
		}
	}

	return array(
		'label' => esc_html__( 'Header Section', 'adventure-tours' ),
		'type' => 'select',
		'name' => 'header_section_id',
		'items' => $list,
		'dependency' => array(
			'field' => 'section_mode',
			'function' => 'adventure_tours_vp_header_section_from_list',
		),
	);
}

/**
 * Local function that returns field that allows to select revolution slider.
 *
 * @return assoc
 */
function _adventure_tours_hsm_get_slider_selector() {

	$isRevoSliderInstalled = class_exists( 'RevSlider' );

	$revoSlidersList = array();
	if ( $isRevoSliderInstalled ) {
		$slider = new RevSlider();
		if ( $arrSliders = $slider->getArrSlidersShort() ) {
			foreach ( $arrSliders as $sid => $stitle ) {
				$revoSlidersList[] = array(
					'value' => $sid,
					'label' => $stitle,
				);
			}
		}
	}

	$descriptionNoticeText = '';
	if ( ! $isRevoSliderInstalled ) {
		$descriptionNoticeText = esc_html__( 'Please install and activate the Slider Revolution plugin.','adventure-tours' );
	} else if ( empty( $revoSlidersList ) ) {
		$descriptionNoticeText = esc_html__( 'Please go to Slider Revolution plugin and create a slider.','adventure-tours' );
	}

	return array(
		'label' => esc_html__( 'Choose Slider', 'adventure-tours' ),
		'type' => 'select',
		'name' => 'slider_alias',
		'description' => $descriptionNoticeText ? '<span style="color:#EE0000">' . $descriptionNoticeText . '</span>' : '',
		'items' => $revoSlidersList,
		'dependency' => array(
			'field' => 'section_mode',
			'function' => 'adventure_tours_vp_header_section_is_slider',
		),
	);
}
