<?php
/**
 * Config file for tour tab metabox fields defenition.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

return array(
	array(
		'label' => esc_html__( 'Badge', 'adventure-tours' ),
		'name'  => 'tour_badge',
		'type' => 'select',
		'items' => array(
			'data' => array(
				array(
					'source' => 'function',
					'value' => 'adventure_tours_vp_badges_list',
				),
			),
		),
	),
	array(
		'type'      => 'group',
		'repeating' => true,
		'name'      => 'tabs',
		'title'     => esc_html__( 'Additional Tab', 'adventure-tours' ),
		'fields'    => array(
			array(
				'type'  => 'textbox',
				'label' => esc_html__( 'Title', 'adventure-tours' ),
				'name'  => 'title',
			),
			array(
				'type'  => 'wpeditor', // 'textarea',
				'label' => esc_html__( 'Content', 'adventure-tours' ),
				'name'  => 'content',
				'use_external_plugins' => 1,
			),
		),
	),
);
