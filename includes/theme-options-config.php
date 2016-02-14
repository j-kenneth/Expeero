<?php
/**
 * Defition for Theme Options section fields.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.9
 */

$full_options_config = array(
	'title' => esc_html__( 'Theme Settings', 'adventure-tours' ),
	'logo' => PARENT_URL . '/assets/images/logo.png',
	'menus' => array(
		array(
			'title' => esc_html__( 'General', 'adventure-tours' ),
			'name' => 'general',
			'icon' => 'font-awesome:fa-cogs',
			'controls' => array(
				array(
					'name' => 'update_notifier',
					'label' => esc_html__( 'Update Notifier', 'adventure-tours' ),
					'description' => esc_html__( 'Switch on if you would like to receive update noticies.', 'adventure-tours' ),
					'type' => 'toggle',
					'default' => '1',
				),
				array(
					'name' => 'custom_css_text',
					'label' => esc_html__( 'Custom CSS', 'adventure-tours' ),
					'type' => 'textarea',
				),
				array(
					'name' => 'placeholder_image',
					'label' => esc_html__( 'Placeholder Image', 'adventure-tours' ),
					'description' => esc_html__( 'Recommended size is 1140x760px.', 'adventure-tours' ),
					'type' => 'upload',
				),
			),
		),
		array(
			'name' => 'header',
			'title' => esc_html__( 'Header','adventure-tours' ),
			'type' => 'section',
			'icon' => 'font-awesome:fa-credit-card',
			'controls' => array(
				array(
					'type' => 'section',
					'title' => esc_html__( 'Logo', 'adventure-tours' ),
					'fields' => array(
						array(
							'name' => 'logo_type',
							'label' => esc_html__( 'Logo Type', 'adventure-tours' ),
							'type' => 'radiobutton',
							'items' => array(
								array(
									'value' => 'image',
									'label' => esc_html__( 'Image', 'adventure-tours' ),
								),
								array(
									'value' => 'text',
									'label' => esc_html__( 'Text', 'adventure-tours' ),
								),
							),
							'default' => array( 'text' ),
							'validation' => 'required',
						),
						array(
							'name' => 'logo_image',
							'label' => esc_html__( 'Logo Image', 'adventure-tours' ),
							'description' => esc_html__( 'Recommended size is 180x30px.', 'adventure-tours' ),
							'type' => 'upload',
							'validation' => 'required',
							'dependency' => array(
								'field' => 'logo_type',
								'function' => 'adventure_tours_vp_dep_value_equal_image',
							),
						),
						array(
							'name' => 'logo_image_retina',
							'label' => esc_html__( 'Logo Image for Retina', 'adventure-tours' ),
							'description' => esc_html__( 'Recommended size is 360x60px.', 'adventure-tours' ),
							'type' => 'upload',
							'validation' => 'required',
							'dependency' => array(
								'field' => 'logo_type',
								'function' => 'adventure_tours_vp_dep_value_equal_image',
							),
						),
					),
				),// logo section end
				array(
					'name' => 'banner',
					'title' => esc_html__( 'Default Header Image', 'adventure-tours' ),
					'type' => 'section',
					'fields' => array(
						array(
							'name' => 'banner_is_show',
							'label' => esc_html__( 'Use Default Image', 'adventure-tours' ),
							'description' => esc_html__( 'For archive and search pages.', 'adventure-tours' ),
							'type' => 'toggle',
							'default' => '0',
						),
						array(
							'name' => 'banner_default_subtitle',
							'label' => esc_html__( 'Subtitle', 'adventure-tours' ),
							'type' => 'textbox',
							'dependency' => array(
								'field' => 'banner_is_show',
								'function' => 'vp_dep_boolean',
							),
						),
						array(
							'name' => 'banner_default_image',
							'label' => esc_html__( 'Image', 'adventure-tours' ),
							'type' => 'upload',
							'dependency' => array(
								'field' => 'banner_is_show',
								'function' => 'vp_dep_boolean',
							),
						),
						array(
							'name' => 'banner_default_image_repeat',
							'label' => esc_html__( 'Image Repeat', 'adventure-tours' ),
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
								'field' => 'banner_is_show',
								'function' => 'vp_dep_boolean',
							),
						),
						array(
							'name' => 'is_banner_default_image_parallax',
							'label' => esc_html__( 'Use Parallax', 'adventure-tours' ),
							'type' => 'toggle',
							'default' => '0',
							'dependency' => array(
								'field' => 'banner_is_show',
								'function' => 'vp_dep_boolean',
							),
						),
						array(
							'type' => 'select',
							'name' => 'banner_default_mask',
							'label' => esc_html__( 'Mask', 'adventure-tours' ),
							'dependency' => array(
								'field' => 'banner_is_show',
								'function' => 'vp_dep_boolean',
							),
							'items' => array(
								array(
									'label' => esc_html__( 'None', 'adventure-tours' ),
									'value' => '',
								),
								array(
									'label' => esc_html__( 'Default', 'adventure-tours' ),
									'value' => 'default',
								),
							),
							'default' => '',
						),
					),
				),// banner section end
				array(
					'name' => 'contact_phone',
					'label' => esc_html__( 'Phone', 'adventure-tours' ),
					'type' => 'textbox',
				),
				array(
					'name' => 'contact_time',
					'label' => esc_html__( 'Working Hours', 'adventure-tours' ),
					'type' => 'textbox',
				),
				_adventure_tours_shop_cart_option(),
				array(
					'name' => 'show_header_search',
					'label' => esc_html__( 'Show Search', 'adventure-tours' ),
					'type' => 'toggle',
					'default' => '1',
				),
				array(
					'name' => 'breadcrumbs_is_show',
					'label' => esc_html__( 'Show Breadcrumbs', 'adventure-tours' ),
					'type' => 'toggle',
					'default' => '1',
				),
				array(
					'name' => 'sticky-header',
					'label' => esc_html__( 'Sticky Header', 'adventure-tours' ),
					'type' => 'toggle',
					'default' => '0',
				)
			),
		),
		array(
			'name' => 'footer',
			'title' => esc_html__( 'Footer','adventure-tours' ),
			'type' => 'section',
			'icon' => 'font-awesome:fa-columns',
			'controls' => array(
				array(
					'name' => 'footer_layout',
					'label' => esc_html__( 'Layout', 'adventure-tours' ),
					'type' => 'select',
					'items' => array(
						array(
							'value' => '2columns',
							'label' => sprintf( esc_html__( '%s Columns', 'adventure-tours' ), 2 ),
						),
						array(
							'value' => '3columns',
							'label' => sprintf( esc_html__( '%s Columns', 'adventure-tours' ), 3 ),
						),
						array(
							'value' => '4columns',
							'label' => sprintf( esc_html__( '%s Columns', 'adventure-tours' ), 4 ),
						),
					),
					'default' => '4columns',
				),
				array(
					'name' => 'footer_text_note',
					'label' => esc_html__( 'Text Note', 'adventure-tours' ),
					'type' => 'textarea',
					'default' => '&copy; Adventure Tours 2015 All Rights Reserved Site Map Disclaimer',
				),
			),
		),
		'tours_section' => array(
			'name' => 'tour',
			'title' => esc_html__( 'Tours', 'adventure-tours' ),
			'type' => 'section',
			'icon' => 'font-awesome:fa-th-list',
			'controls' => array(
				array(
					'type' => 'section',
					'title' => esc_html__( 'Tours Page', 'adventure-tours' ),
					'fields' => array(
						array(
							'name' => 'tours_page',
							'label' => esc_html__( 'Page', 'adventure-tours' ),
							'description' => esc_html__( 'Tours archive/search page.', 'adventure-tours' ),
							'type' => 'select',
							'items' => array(
								'data' => array(
									array(
										'source' => 'function',
										'value' => 'vp_get_pages',
									),
								),
							),
						),
						array(
							'name' => 'tours_archive_display_mode',
							'label' => esc_html__( 'Display', 'adventure-tours' ),
							'type' => 'select',
							'default' => 'products',
							'items' => array(
								'data' => array(
									array(
										'source' => 'function',
										'value' => 'adventure_tours_vp_archive_tour_display_modes_list',
									),
								),
							),
						),
						array(
							'name' => 'tours_archive_show_sidebar',
							'label' => esc_html__( 'Show Sidebar', 'adventure-tours' ),
							'type' => 'toggle',
							'default' => '1',
						),
						array(
							'name' => 'tours_archive_show_search_form',
							'label' => esc_html__( 'Show Search Form', 'adventure-tours' ),
							'type' => 'toggle',
							'default' => '1',
							'dependency' => array(
								'field' => 'tours_archive_show_sidebar',
								'function' => 'vp_dep_boolean',
							),
						),
						array(
							'name' => 'tours_archive_display_style',
							'label' => esc_html__( 'Display Style', 'adventure-tours' ),
							'type' => 'select',
							'items' => array(
								array(
									'value' => 'list',
									'label' => esc_html__( 'List', 'adventure-tours' ),
								),
								array(
									'value' => 'grid',
									'label' => esc_html__( 'Grid', 'adventure-tours' ),
								),
							),
							'default' => array(
								'{{first}}',
							),
						),
						array(
							'name' => 'tours_archive_columns_number',
							'label' => esc_html__( 'Columns Number', 'adventure-tours' ),
							'type' => 'select',
							'items' => array(
								array(
									'value' => '2',
									'label' => '2',
								),
								array(
									'value' => '3',
									'label' => '3',
								),
								array(
									'value' => '4',
									'label' => '4',
								),
							),
							'default' => array(
								'{{first}}',
							),
							'dependency' => array(
								'field' => 'tours_archive_display_style',
								'function' => 'adventure_tours_vp_tour_page_style_is_grid',
							),
						),
						array(
							'name' => 'tours_archive_tour_price_style',
							'label' => esc_html__( 'Item Price Style', 'adventure-tours' ),
							'description' => esc_html__( 'Item style display mode.', 'adventure-tours' ),
							'type' => 'select',
							'items' => array(
								array(
									'value' => 'default',
									'label' => esc_html__( 'Default', 'adventure-tours' ),
								),
								array(
									'value' => 'highlighted',
									'label' => esc_html__( 'Highlighted', 'adventure-tours' ),
								),
							),
							'default' => array(
								'{{first}}',
							),
							'dependency' => array(
								'field' => 'tours_archive_display_style',
								'function' => 'adventure_tours_vp_tour_page_style_is_grid',
							),
						),
						array(
							'name' => 'tours_archive_tour_description_words_limit',
							'label' => esc_html__( 'Description Words Limit', 'adventure-tours' ),
							'description' => esc_html__( 'Limits the length of the item description.', 'adventure-tours' ),
							'type' => 'Slider',
							'min' => '0',
							'max' => '500',
							'step' => '1',
							'default' => '13',
						),
						array(
							'name' => 'tours_archive_tour_display_category',
							'label' => esc_html__( 'Show Tour Categories', 'adventure-tours' ),
							'description' => esc_html__( 'Shows category icons for each item.', 'adventure-tours' ),
							'type' => 'toggle',
							'default' => '1',
						),
						array(
							'name' => 'tours_archive_orderby',
							'label' => esc_html__( 'Default Tour Sorting', 'adventure-tours' ),
							'type' => 'select',
							'items' => array(
								array(
									'value' => 'menu_order',
									'label' => esc_html__( 'Default sorting (custom ordering + name)', 'adventure-tours' ),
								),
								array(
									'value' => 'popularity',
									'label' => esc_html__( 'Popularity (sales)', 'adventure-tours' ),
								),
								array(
									'value' => 'rating',
									'label' => esc_html__( 'Average Rating', 'adventure-tours' ),
								),
								array(
									'value' => 'date',
									'label' => esc_html__( 'Sort by most recent', 'adventure-tours' ),
								),
								array(
									'value' => 'price',
									'label' => esc_html__( 'Sort by price (asc)', 'adventure-tours' ),
								),
								array(
									'value' => 'price-desc',
									'label' => esc_html__( 'Sort by price (desc)', 'adventure-tours' ),
								),
							),
							'default' => array(
								'{{first}}',
							),
						),
					),
				),
				array(
					'type' => 'section',
					'title' => esc_html__( 'Booking Form', 'adventure-tours' ),
					'fields' => array(
						array(
							'name' => 'tours_booking_length',
							'label' => esc_html__( 'Earliest Booking Time', 'adventure-tours' ),
							'description' => esc_html__( 'Number of days before the tour the booking starts.', 'adventure-tours' ),
							'type' => 'textbox',
							'default' => '90',
							'validation' => 'required|numeric',
							'type' => 'slider',
							'min' => '1',
							'max' => '365',
							'step' => '1',
						),
						array(
							'name' => 'tours_booking_start',
							'label' => esc_html__( 'Latest Booking Time', 'adventure-tours' ),
							'description' => esc_html__( 'Number of days before the tour the booking stops.', 'adventure-tours' ),
							'type' => 'textbox',
							'default' => '1',
							'validation' => 'required|numeric',
							'type' => 'slider',
							'min' => '0',
							'max' => '365',
							'step' => '1',
						),
						array(
							'name' => 'tours_booking_redirect',
							'label' => esc_html__( 'Redirect to', 'adventure-tours' ),
							'description' => esc_html__( 'Redirect user after successful booking.', 'adventure-tours' ),
							'type' => 'select',
							'items' => array(
								array(
									'value' => 'checkout_page',
									'label' => esc_html__( 'Send to checkout page', 'adventure-tours' ),
								),
								array(
									'value' => 'cart_page',
									'label' => esc_html__( 'Send to cart page', 'adventure-tours' ),
								),
								array(
									'value' => 'same_as_product',
									'label' => esc_html__( 'Use product settings', 'adventure-tours' ),
								),
								array(
									'value' => 'stay_on_same_page',
									'label' => esc_html__( 'Stay on the same page', 'adventure-tours' ),
								)
							),
							'default' => array(
								'checkout_page',
							),
						),
					),
				),
				array(
					'type' => 'section',
					'title' => esc_html__( 'Tour Details Page', 'adventure-tours' ),
					'fields' => array(
						array(
							'name' => 'social_sharing_tour',
							'label' => esc_html__( 'Social Sharing', 'adventure-tours' ),
							'description' => esc_html__( 'Turn on to show social media buttons on the tour details page.', 'adventure-tours' ),
							'type' => 'toggle',
							'default' => '1',
						),
						array(
							'name' => 'tours_page_show_related_tours',
							'label' => esc_html__( 'Show Related Tours', 'adventure-tours' ),
							'description' => esc_html__( 'Turn on to show related tours on the tour details page.', 'adventure-tours' ),
							'type' => 'toggle',
							'default' => '1',
						),
						array(
							'name' => 'tours_page_top_attributes',
							'label' => esc_html__( 'Top Section Attributes', 'adventure-tours' ),
							'type' => 'sorter',
							'items' => array(
								'data' => array(
									array(
										'source' => 'function',
										'value' => 'adventure_tours_vp_get_tour_attributes_list',
									),
								),
							),
						),
					),
				),
				array(
					'type' => 'section',
					'title' => esc_html__( 'Tour Badges', 'adventure-tours' ),
					'fields' => _adventure_tours_generate_badge_controls(),
				),
				array(
					'type' => 'section',
					'title' => esc_html__( 'Search Form', 'adventure-tours' ),
					'fields' => array(
						array(
							'name' => 'tours_search_form_title',
							'label' => esc_html__( 'Title', 'adventure-tours' ),
							'type' => 'textbox',
							'default' => esc_attr__( 'Search Tour', 'adventure-tours' ),
						),
						array(
							'name' => 'tours_search_form_note',
							'label' => esc_html__( 'Subtitle', 'adventure-tours' ),
							'type' => 'textbox',
							'default' => esc_attr__( 'Find your dream tour today!','adventure-tours' ),
						),
						array(
							'name' => 'tours_search_form_attributes',
							'label' => esc_html__( 'Additional Fields', 'adventure-tours' ),
							'type' => 'sorter',
							'items' => array(
								array(
									'value' => '__tour_categories_filter',
									'label' => esc_html__( 'Tour Categories', 'adventure-tours' ),
								),
								'data' => array(
									array(
										'source' => 'function',
										'value' => 'adventure_tours_vp_get_tour_attributes_list',
									),
								),
							),
						),
						array(
							'name' => 'tours_search_form_start_category',
							'label' => esc_html__( 'Tour Parent Category', 'adventure-tours' ),
							'description' => esc_html__( 'Select a parent if you would like to refine the list of searchable categories.', 'adventure-tours' ),
							'type' => 'select',
							'items' => array(
								array(
									'value' => '',
									'label' => esc_html__( 'All', 'adventure-tours' ),
								),
								'data' => array(
									array(
										'source' => 'function',
										'value' => 'adventure_tours_vp_get_tour_start_category_list',
									),
								),
							),
							'dependency' => array(
								'field' => 'tours_search_form_attributes',
								'function' => 'adventure_tours_vp_is_tour_categories_visible_on_search',
							),
							'default' => '',
						),
					),
				), // End of Tours > Search Form section.
			),
		),
		array(
			'name' => 'blog',
			'title' => esc_html__( 'Blog', 'adventure-tours' ),
			'type' => 'section',
			'icon' => 'font-awesome:fa-th-list',
			'controls' => array(
				array(
					'name' => 'blog_settings',
					'type' => 'section',
					'title' => esc_html__( 'Blog Page','adventure-tours' ),
					'fields' => array(
						array(
							'name' => 'excerpt_text',
							'label' => esc_html__( 'Read More Link Text', 'adventure-tours' ),
							'type' => 'textbox',
							'default' => esc_attr__( 'Read more', 'adventure-tours' ),
						),
						array(
							'name' => 'is_excerpt',
							'label' => esc_html__( 'Excerpt', 'adventure-tours' ),
							'description' => esc_html__( 'Turn on to automatically shorten the posts on the blog page', 'adventure-tours' ),
							'type' => 'toggle',
						),
						array(
							'name' => 'excerpt_length',
							'label' => esc_html__( 'Excerpt Length', 'adventure-tours' ),
							'type' => 'textbox',
							'validation' => 'numeric',
							'default' => '55',
							'dependency' => array(
								'field' => 'is_excerpt',
								'function' => 'vp_dep_boolean',
							),
						),
						array(
							'name' => 'social_sharing_blog',
							'label' => esc_html__( 'Social Sharing', 'adventure-tours' ),
							'description' => esc_html__( 'Turn on to show social media buttons under the post.', 'adventure-tours' ),
							'type' => 'toggle',
							'default' => '1',
						),
					),
				),
				array(
					'name' => 'single_post',
					'type' => 'section',
					'title' => esc_html__( 'Single Post Page','adventure-tours' ),
					'fields' => array(
						array(
							'name' => 'post_tags',
							'label' => esc_html__( 'Show Post Tags', 'adventure-tours' ),
							'type' => 'toggle',
							'default' => '1',
						),
						array(
							'name' => 'social_sharing_blog_single',
							'label' => esc_html__( 'Social Sharing', 'adventure-tours' ),
							'description' => esc_html__( 'Turn on to show social media buttons under the post.', 'adventure-tours' ),
							'type' => 'toggle',
							'default' => '1',
						),
						array(
							'name' => 'about_author',
							'label' => esc_html__( 'Show "About Author" Section', 'adventure-tours' ),
							'type' => 'toggle',
							'default' => '1',
						),
					),
				),
			),
		),
		'faq_section' => array(
			'name' => 'other_faq_page',
			'title' => esc_html__( 'FAQs Page', 'adventure-tours' ),
			'type' => 'section',
			'icon' => 'font-awesome: fa-question',
			'controls' => array(
				array(
					'name' => 'faq_show_question_form',
					'label' => esc_html__( 'Show Question Form', 'adventure-tours' ),
					'type' => 'toggle',
					'default' => '0',
				),
				array(
					'name' => 'faq_notification_settings',
					'title' => esc_html__( 'New Question Notification', 'adventure-tours' ),
					'type' => 'section',
					'dependency' => array(
						'field' => 'faq_show_question_form',
						'function' => 'vp_dep_boolean',
					),
					'fields' => array(
						array(
							'type' => 'radiobutton',
							'name' => 'faq_question_form_receiver_type',
							'label' => esc_html__( 'Email Receiver', 'adventure-tours' ),
							'items' => array(
								array(
									'value' => 'admin_email',
									'label' => esc_html__( 'Admin email', 'adventure-tours' ),
								),
								array(
									'value' => 'custom_email',
									'label' => esc_html__( 'Custom email', 'adventure-tours' ),
								),
							),
							'default' => array(
								'{{first}}',
							),
						),
						array(
							'name' => 'faq_question_form_custom_email',
							'label' => esc_html__( 'Custom email', 'adventure-tours' ),
							'type' => 'textbox',
							'validation' => 'email',
							'dependency' => array(
								'field' => 'faq_question_form_receiver_type',
								'function' => 'adventure_tours_vp_faq_is_custom_email',
							),
						),
					),
				),
			),
		),
		array(
			'name' => 'social_media',
			'title' => esc_html__( 'Social Media', 'adventure-tours' ),
			'type' => 'section',
			'icon' => 'font-awesome:fa-facebook-square',
			'controls' => array(
				array(
					'name' => 'social_link_facebook',
					'type' => 'textbox',
					'label' => esc_html__( 'Facebook URL', 'adventure-tours' ),
					'validation' => 'url',
				),
				array(
					'name' => 'social_link_twitter',
					'type' => 'textbox',
					'label' => esc_html__( 'Twitter URL', 'adventure-tours' ),
					'validation' => 'url',
				),
				array(
					'name' => 'social_link_googleplus',
					'type' => 'textbox',
					'label' => esc_html__( 'Google+ URL', 'adventure-tours' ),
					'validation' => 'url',
				),
				array(
					'name' => 'social_link_pinterest',
					'type' => 'textbox',
					'label' => esc_html__( 'Pinterest URL', 'adventure-tours' ),
					'validation' => 'url',
				),
				array(
					'name' => 'social_link_linkedin',
					'type' => 'textbox',
					'label' => esc_html__( 'Linkedin URL', 'adventure-tours' ),
					'validation' => 'url',
				),
				array(
					'name' => 'social_link_instagram',
					'type' => 'textbox',
					'label' => esc_html__( 'Instagram URL', 'adventure-tours' ),
					'validation' => 'url',
				),
				array(
					'name' => 'social_link_dribbble',
					'type' => 'textbox',
					'label' => esc_html__( 'Dribbble URL', 'adventure-tours' ),
					'validation' => 'url',
				),
				array(
					'name' => 'social_link_tumblr',
					'type' => 'textbox',
					'label' => esc_html__( 'Tumblr URL', 'adventure-tours' ),
					'validation' => 'url',
				),
				array(
					'name' => 'social_link_vk',
					'type' => 'textbox',
					'label' => esc_html__( 'Vkontakte URL', 'adventure-tours' ),
					'validation' => 'url',
				),
				array(
					'name' => 'social_media_additional_links',
					'type' => 'section',
					'title' => esc_html__( 'Additional URLs', 'adventure-tours' ),
					'fields' => _adventure_tours_additional_social_icons( ),
				)
			),
		),
		array(
			'name' => 'social_sharing',
			'title' => esc_html__( 'Social Sharing', 'adventure-tours' ),
			'type' => 'section',
			'icon' => 'font-awesome:fa-facebook-square',
			'controls' => array(
				array(
					'name' => 'social_sharing_googleplus',
					'label' => esc_html__( 'Google+', 'adventure-tours' ),
					'type' => 'toggle',
					'default' => '1',
				),
				array(
					'name' => 'social_sharing_facebook',
					'label' => esc_html__( 'Facebook', 'adventure-tours' ),
					'type' => 'toggle',
					'default' => '1',
				),
				array(
					'name' => 'social_sharing_twitter',
					'label' => esc_html__( 'Twitter', 'adventure-tours' ),
					'type' => 'toggle',
					'default' => '1',
				),
				array(
					'name' => 'social_sharing_stumbleupon',
					'label' => esc_html__( 'Stumbleupon', 'adventure-tours' ),
					'type' => 'toggle',
					'default' => '1',
				),
				array(
					'name' => 'social_sharing_linkedin',
					'label' => esc_html__( 'Linkedin', 'adventure-tours' ),
					'type' => 'toggle',
					'default' => '1',
				),
				array(
					'name' => 'social_sharing_pinterest',
					'label' => esc_html__( 'Pinterest', 'adventure-tours' ),
					'type' => 'toggle',
					'default' => '1',
				),
				array(
					'name' => 'social_sharing_vk',
					'label' => esc_html__( 'Vkontakte', 'adventure-tours' ),
					'type' => 'toggle',
					'default' => '0',
				),
			),
		),
		/*
		array(
			'name' => 'data_import',
			'title' => esc_html__('Data Import', 'adventure-tours'),
			'type' => 'section',
			'icon' => 'font-awesome: fa-question',
			'controls' => array(
				array(

				)
			),
		),*/
	),
);

/**
 * Generates inputs for badges lit management.
 *
 * @return array
 */
function _adventure_tours_generate_badge_controls() {
	$result = array();

	$count = adventure_tours_di('tour_badge_service')->get_count();
	for($bid = 1; $bid <= $count; $bid++ ) {
		$result[] = array(
			'name' => "tour_badge_{$bid}_is_active",
			'label' => sprintf( esc_html__( 'Is Active Badge #%d', 'adventure-tours' ), $bid ),
			'type' => 'toggle',
			'default' => '0',
		);

		$result[] = array(
			'name' => "tour_badge_{$bid}_title",
			'label' => sprintf( esc_html__( 'Title #%d', 'adventure-tours' ), $bid ),
			'type' => 'textbox',
			'default' => '',
			'dependency' => array(
				'field' => "tour_badge_{$bid}_is_active",
				'function' => 'vp_dep_boolean',
			),
		);

		$result[] = array(
			'name' => "tour_badge_{$bid}_color",
			'label' => sprintf( esc_html__( 'Color #%d', 'adventure-tours' ), $bid ),
			'type' => 'color',
			'default' => '',
			'dependency' => array(
				'field' => "tour_badge_{$bid}_is_active",
				'function' => 'vp_dep_boolean',
			),
		);
	}

	return $result;
}

function _adventure_tours_shop_cart_option() {
	$descriptionNoticeText = '';
	if ( ! class_exists( 'WooCommerce' ) ) {
		$descriptionNoticeText = esc_html__( 'Please install and activate the WooCommerce plugin.','adventure-tours' );
	}

	return array(
		'name' => 'show_header_shop_cart',
		'label' => esc_html__( 'Show Shopping Cart', 'adventure-tours' ),
		'description' => $descriptionNoticeText ? '<span style="color:#EE0000">' . $descriptionNoticeText . '</span>' : '',
		'type' => 'toggle',
		'default' => '1',
	);
}

function _adventure_tours_additional_social_icons( $limit = 5 ) {
	$result = array();

	for ( $cur_index = 1; $cur_index <= $limit; $cur_index++ ) {
		$result[] = array(
			'name' => "social_link_{$cur_index}_is_active",
			'label' => sprintf( esc_html__( 'Link #%d', 'adventure-tours' ), $cur_index ),
			'type' => 'toggle',
			'default' => '0',
		);

		$result[] = array(
			'name' => "social_link_{$cur_index}_icon",
			'label' => sprintf( esc_html__( 'Icon #%d', 'adventure-tours' ), $cur_index ),
			'type' => 'fontawesome',
			'default' => '',
			'validation' => 'required',
			'dependency' => array(
				'field' => "social_link_{$cur_index}_is_active",
				'function' => 'vp_dep_boolean',
			),
		);

		$result[] = array(
			'name' => "social_link_{$cur_index}_url",
			'label' => sprintf( esc_html__( 'Url #%d', 'adventure-tours' ), $cur_index ),
			'type' => 'textbox',
			'default' => '',
			'validation' => 'required|url',
			'dependency' => array(
				'field' => "social_link_{$cur_index}_is_active",
				'function' => 'vp_dep_boolean',
			),
		);
	}
	return $result;
}

if ( ! adventure_tours_check( 'faq_taxonomies' ) ) {
	unset($full_options_config['menus']['faq_section']);
}
if ( ! adventure_tours_check( 'tours_active' ) ) {
	unset($full_options_config['menus']['tours_section']);
}

return $full_options_config;
