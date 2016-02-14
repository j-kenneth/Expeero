<?php
/**
 * Shortcodes menu definition.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.3.0
 */

$shortcodes_register = adventure_tours_di( 'shortcodes_register' );
if ( ! adventure_tours_di( 'shortcodes_tiny_mce_integrator' ) || ! $shortcodes_register ) {
	return;
}

$toursMenu = esc_html__( 'Tours', 'adventure-tours' ) . '.';

$typographyMenu = esc_html__( 'Typography', 'adventure-tours' ) . '.';

$tablesMenu = esc_html__( 'Tables', 'adventure-tours' ) . '.';

$otherMenu = esc_html__( 'Other', 'adventure-tours' ) . '.';

$contactMenu = esc_html__( 'Contact', 'adventure-tours' ) . '.';

$externalApiMenu = esc_html__( 'External Services', 'adventure-tours' ) . '.';

$category_order_mode = array(
	'ASC',
	'DESC',
);

$category_orderby_mode = array(
	'name',
	'id',
	'slug',
	'count',
	'term_group',
	'category__in',
);

$article_order_mode = array(
	'DESC',
	'ASC',
);

$article_orderby_mode = array(
	'date',
	'title',
	'name',
	'modified',
	'rand',
	'comment_count',
	'post__in',
);

$article_product_orderby_mode = $article_orderby_mode;
$article_product_orderby_mode[] = 'price';
$article_product_orderby_mode[] = 'sales';
$article_product_orderby_mode[] = 'most_popular';

$shortcodes_register
	->add( '_edit_', esc_html__( 'Edit', 'adventure-tours' ) )
	->add( 'row', esc_html__( 'Columns', 'adventure-tours' ), array(
		'columns' => '2',
		'css_class' => '',
	))

	->add( 'title', $typographyMenu . esc_html__( 'Title', 'adventure-tours' ), array(
		'text' => '',
		'subtitle' => '',
		'size' => array(
			'type' => 'select',
			'values' => array(
				'big',
				'small',
			),
		),
		'position' => array(
			'type' => 'select',
			'values' => array(
				'left',
				'center',
			),
		),
		'decoration' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'underline' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'style' => array(
			'type' => 'select',
			'values' => array(
				'dark',
				'light',
			),
		),
		'css_class' => '',
	))

	->add( 'icon_tick', $typographyMenu . esc_html__( 'Icon Tick', 'adventure-tours' ), array(
		'state' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'css_class' => '',
	))

	->add( 'at_btn', $typographyMenu . esc_html__( 'Button', 'adventure-tours' ), array(
		'text' => '',
		'url' => '',
		'type' => array(
			'type' => 'select',
			'values' => array(
				'link',
				'button',
				'submit',
			),
		),
		'css_class' => '',
		'style' => array(
			'type' => 'select',
			'values' => array(
				'',
				'primary',
				'secondary1',
				'secondary2',
			),
		),
		'size' => array(
			'type' => 'select',
			'values' => array(
				'',
				'medium',
				'small',
			),
		),
		'corners' => array(
			'type' => 'select',
			'values' => array(
				'',
				'rounded',
			),
		),
		'light' => array(
			'type' => 'boolean',
			'default' => 'off',
		),
		'transparent' => array(
			'type' => 'boolean',
			'default' => 'off',
		),
		'icon_class' => '',
		'icon_align' => array(
			'type' => 'select',
			'values' => array(
				'left',
				'right',
			),
		),
	))

	->add( 'table', $tablesMenu . esc_html__( 'Table', 'adventure-tours' ), array(
		'rows' => '',
		'cols' => '',
		'css_class' => '',
	))
	->add( 'tour_table', $tablesMenu . esc_html__( 'Tour Table', 'adventure-tours' ), array(
		'rows' => '',
		'cols' => '',
		'css_class' => '',
	))

	->add( 'tour_search_form', $toursMenu . esc_html__( 'Tour Search Form', 'adventure-tours' ), array(
		'title' => '',
		'note' => '',
		'css_class' => '',
		'hide_text_field' => array(
			'type' => 'boolean',
			'default' => 'off',
		)
	))
	->add( 'tour_search_form_horizontal', $toursMenu . esc_html__( 'Tour Search Form Horizontal', 'adventure-tours' ), array(
		'title' => '',
		'note' => '',
		'style' => array(
			'type' => 'select',
			'values' => array(
				'default',
				'style1',
				'style2',
				'style3',
				'style4',
			),
		),
		'css_class' => '',
		'hide_text_field' => array(
			'type' => 'boolean',
			'default' => 'off',
		)
	))
	->add( 'tour_category_images', $toursMenu . esc_html__( 'Tour Category Images', 'adventure-tours' ), array(
		'title' => '',
		'title_underline' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'sub_title' => '',
		'parent_id' => '',
		'ignore_empty' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'category_ids' => array(
			'help' => esc_html__( 'Specify exact ids of items that should be displayed separated by comma.', 'adventure-tours' ),
			'default' => '',
		),
		'number' => '',
		'slides_number' => '4',
		'css_class' => '',
		'order' => array(
			'type' => 'select',
			'values' => $category_order_mode,
		),
		'orderby' => array(
			'type' => 'select',
			'values' => $category_orderby_mode,
		),
	))
	->add( 'tour_category_icons', $toursMenu . esc_html__( 'Tour Category Icons', 'adventure-tours' ), array(
		'title' => '',
		'title_underline' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'sub_title' => '',
		'parent_id' => '',
		'bg_url' => array(
			'type' => 'image_url',
			'help' => esc_html__( 'Select image that should be used as background.', 'adventure-tours' ),
			'default' => '',
		),
		'ignore_empty' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'category_ids' => array(
			'help' => esc_html__( 'Specify exact ids of items that should be displayed separated by comma.', 'adventure-tours' ),
			'default' => '',
		),
		'number' => '',
		'slides_number' => '5',
		'css_class' => '',
		'order' => array(
			'type' => 'select',
			'values' => $category_order_mode,
		),
		'orderby' => array(
			'type' => 'select',
			'values' => $category_orderby_mode,
		),
	))
	->add( 'tour_carousel', $toursMenu . esc_html__( 'Tours Carousel', 'adventure-tours' ), array(
		'title' => '',
		'title_underline' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'sub_title' => '',
		'description_words_limit' => '20',
		'tour_category' => array(
			'help' => esc_html__( 'Filter items from specific tour category (enter category slug).', 'adventure-tours' ),
			'default' => '',
		),
		'tour_category_ids' => array(
			'help' => esc_html__( 'Specify tour categories ID\'s (separated by comma) of items that you want to display.', 'adventure-tours' ),
			'default' => '',
		),
		'tour_ids' => array(
			'help' => esc_html__( 'Specify exact ids of items that should be displayed separated by comma.', 'adventure-tours' ),
			'default' => '',
		),
		'slides_number' => '3',
		'number' => '',
		'css_class' => '',
		'bg_url' => array(
			'type' => 'image_url',
			'help' => esc_html__( 'Select image that should be used as background.', 'adventure-tours' ),
			'default' => '',
		),
		'arrow_style' => array(
			'type' => 'select',
			'values' => array(
				'light',
				'dark',
			),
		),
		'order' => array(
			'type' => 'select',
			'values' => $article_order_mode,
		),
		'orderby' => array(
			'type' => 'select',
			'values' => $article_product_orderby_mode,
		),
	))
	->add( 'tours_grid', $toursMenu . esc_html__( 'Tours Grid', 'adventure-tours' ), array(
		'title' => '',
		'title_underline' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'sub_title' => '',
		'description_words_limit' => '20',
		'tour_category' => array(
			'help' => esc_html__( 'Filter items from specific tour category (enter category slug).', 'adventure-tours' ),
			'default' => '',
		),
		'tour_category_ids' => array(
			'help' => esc_html__( 'Specify tour categories ID\'s (separated by comma) of items that you want to display.', 'adventure-tours' ),
			'default' => '',
		),
		'tour_ids' => array(
			'help' => esc_html__( 'Specify exact ids of items that should be displayed separated by comma.', 'adventure-tours' ),
			'default' => '',
		),
		'number' => '4',
		'columns' => '',
		'css_class' => '',
		'price_style' => array(
			'type' => 'select',
			'values' => array(
				'default',
				'highlighted',
			),
		),
		'show_categories' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'btn_more_text' => esc_html__( 'View more', 'adventure-tours' ),
		'btn_more_link' => '',
		'order' => array(
			'type' => 'select',
			'values' => $article_order_mode,
		),
		'orderby' => array(
			'type' => 'select',
			'values' => $article_product_orderby_mode,
		),
	))
	->add( 'tours_list', $toursMenu . esc_html__( 'Tours List', 'adventure-tours' ), array(
		'title' => '',
		'title_underline' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'sub_title' => '',
		'description_words_limit' => '20',
		'tour_category' => array(
			'help' => esc_html__( 'Filter items from specific tour category (enter category slug).', 'adventure-tours' ),
			'default' => '',
		),
		'tour_category_ids' => array(
			'help' => esc_html__( 'Specify tour categories ID\'s (separated by comma) of items that you want to display.', 'adventure-tours' ),
			'default' => '',
		),
		'tour_ids' => array(
			'help' => esc_html__( 'Specify exact ids of items that should be displayed separated by comma.', 'adventure-tours' ),
			'default' => '',
		),
		'number' => '4',
		'css_class' => '',
		'show_categories' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'btn_more_text' => esc_html__( 'View more', 'adventure-tours' ),
		'btn_more_link' => '',
		'order' => array(
			'type' => 'select',
			'values' => $article_order_mode,
		),
		'orderby' => array(
			'type' => 'select',
			'values' => $article_product_orderby_mode,
		),
	))
	->add( 'tour_reviews', $toursMenu . esc_html__( 'Tour Reviews', 'adventure-tours' ), array(
		'title' => '',
		'title_underline' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'number' => '2',
		'css_class' => '',
		'order' => array(
			'type' => 'select',
			'values' => array(
				'DESC',
				'ASC',
			),
		),
		'orderby' => array(
			'type' => 'select',
			'values' => array(
				'comment_date_gmt',
				'comment_author',
				'comment_post_ID',
			),
		),
	))

	->add( 'contact_info', $contactMenu . esc_html__( 'Contact Info', 'adventure-tours' ), array(
		'address' => '',
		'phone' => '',
		'email' => '',
		'skype' => '',
		'css_class' => '',
	))
	->add( 'social_icons', $contactMenu . esc_html__( 'Social Icons', 'adventure-tours' ), array(
		'title' => esc_html__( 'We are social', 'adventure-tours' ),
		'facebook_url' => '',
		'twitter_url' => '',
		'googleplus_url' => '',
		'pinterest_url' => '',
		'linkedin_url' => '',
		'instagram_url' => '',
		'dribbble_url' => '',
		'tumblr_url' => '',
		'vk_url' => '',
		'css_class' => '',
	))

	->add( 'mailchimp_form', $externalApiMenu . esc_html__( 'MailChimp Form', 'adventure-tours' ), array(
		'form_id' => array(
			'required' => true,
		),
		/*'mailchimp_list_id' => array(
			'required' => true,
		),*/
		'button_text' => esc_html__( 'Submit', 'adventure-tours' ),
		'title' => '',
		'content' => '',
		'css_class' => '',
		'width_mode' => array(
			'type' => 'select',
			'values' => array(
				'box-width',
				'full-width',
			),
		),
		'bg_url' => array(
			'type' => 'image_url',
			'help' => esc_html__( 'Select image that should be used as background.', 'adventure-tours' ),
			'default' => '',
		),
		'bg_repeat' => array(
			'type' => 'select',
			'values' => array(
				'repeat',
				'no-repeat',
				'repeat-x',
				'repeat-y',
			),
		),
	))
	->add( 'google_map', $externalApiMenu . esc_html__( 'Google Map', 'adventure-tours' ), array(
		'address' => array(
			'help' => esc_html__( 'The address will show up when clicking on the map marker.', 'adventure-tours' ),
		),
		'coordinates' => array(
			'help' => esc_html__( 'Coordinates separated by comma.', 'adventure-tours' ),
			'default' => '40.764324,-73.973057',
			'required' => true,
		),
		'zoom' => array(
			'help' => esc_html__( 'Number in range from 1 up to 21.', 'adventure-tours' ),
			'default' => '10',
			'required' => true,
		),
		'height' => array(
			'default' => '400',
		),
		'width_mode' => array(
			'type' => 'select',
			'values' => array(
				'box-width',
				'full-width',
			),
		),
		'css_class' => '',
	))

	->add( 'latest_posts', $otherMenu . esc_html__( 'Latest Posts', 'adventure-tours' ), array(
		'title' => esc_html__( 'Latest Posts', 'adventure-tours' ),
		'title_underline' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'category' => array(
			'help' => esc_html__( 'Filter items from specific category (enter category slug).', 'adventure-tours' ),
			'default' => '',
		),
		'post_ids' => array(
			'help' => esc_html__( 'Specify exact ids of items that should be displayed separated by comma.', 'adventure-tours' ),
			'default' => '',
		),
		'number' => '1',
		'read_more_text' => esc_html__( 'Read more', 'adventure-tours' ),
		'words_limit' => '25',
		'ignore_sticky_posts' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'translate' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'order' => array(
			'type' => 'select',
			'values' => $article_order_mode,
		),
		'orderby' => array(
			'type' => 'select',
			'values' => $article_orderby_mode,
		),
		'css_class' => '',
	))
	->add( 'timeline', $otherMenu . esc_html__( 'Timeline', 'adventure-tours' ), array(
		'content' => '[timeline_item item_number="1" title="Day 1"]Lorem ipsum 1[/timeline_item][timeline_item item_number="2" title="Day 2"]Lorem ipsum 2[/timeline_item]',
		'css_class' => '',
	))
	->add( 'icons_set', $otherMenu . esc_html__( 'Icons Set', 'adventure-tours' ), array(
		'row_size' => array(
			'type' => 'select',
			'values' => array( '2', '3', '4' ),
			'default' => 3,
		),
		'content' => join( PHP_EOL, array(
			'[icon_item icon="td-earth" title="Item1"]text[/icon_item]',
			'[icon_item icon="td-heart" title="Item2"]text[/icon_item]',
			'[icon_item icon="td-lifebuoy" title="Item3"]text[/icon_item]',
		)),
		'css_class' => '',
	))
	->add( 'product_carousel', $otherMenu . esc_html__( 'Products Carousel', 'adventure-tours' ), array(
		'title' => '',
		'title_underline' => array(
			'type' => 'boolean',
			'default' => 'on',
		),
		'sub_title' => '',
		'description_words_limit' => '20',
		'product_category' => array(
			'help' => esc_html__( 'Filter items from specific tour category (enter category slug).', 'adventure-tours' ),
			'default' => '',
		),
		'product_category_ids' => array(
			'help' => esc_html__( 'Specify product categories ID\'s (separated by comma) of items that you want to display.', 'adventure-tours' ),
			'default' => '',
		),
		'product_ids' => array(
			'help' => esc_html__( 'Specify exact ids of items that should be displayed separated by comma.', 'adventure-tours' ),
			'default' => '',
		),
		'slides_number' => '3',
		'number' => '',
		'css_class' => '',
		'bg_url' => array(
			'type' => 'image_url',
			'help' => esc_html__( 'Select image that should be used as background.', 'adventure-tours' ),
			'default' => '',
		),
		'arrow_style' => array(
			'type' => 'select',
			'values' => array(
				'light',
				'dark',
			),
		),
		'order' => array(
			'type' => 'select',
			'values' => $article_order_mode,
		),
		'orderby' => array(
			'type' => 'select',
			'values' => $article_product_orderby_mode,
		),
	))
	->add( 'accordion', $otherMenu . esc_html__( 'Accordion', 'adventure-tours' ), array(
		'content' => join( PHP_EOL, array(
			'[accordion_item title="Title 1" is_active="on"]Lorem ipsum 1[/accordion_item]',
			'[accordion_item title="Title 2"]Lorem ipsum 2[/accordion_item]',
			'[accordion_item title="Title 3"]Lorem ipsum 3[/accordion_item]',
		)),
		'style' => array(
			'type' => 'select',
			'values' => array(
				'with-shadow',
				'with-border',
			),
		),
		'css_class' => '',
	))
	->add( 'tabs', $otherMenu . esc_html__( 'Tabs', 'adventure-tours' ), array(
		'content' => join( PHP_EOL, array(
			'[tab_item title="Title 1" is_active="on"]Lorem ipsum 1[/tab_item]',
			'[tab_item title="Title 2"]Lorem ipsum 2[/tab_item]',
			'[tab_item title="Title 3"]Lorem ipsum 3[/tab_item]',
		)),
		'style' => array(
			'type' => 'select',
			'values' => array(
				'with-shadow',
				'with-border',
			),
		),
		'css_class' => '',
	));
