<?php
/**
 * Theme demo data import component.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.1.1
 */

class AtDemoDataImport
{
	public $page_id = 'adventure_tours_import';

	public $capability = 'manage_options';

	/**
	 * Array used for posts filtering during the import.
	 *
	 * @see filter_post_for_import
	 * @var array
	 */
	protected $filter_types_only = array();

	protected $allow_product_terms = false;

	protected $allow_categories_import = false;

	/**
	 * Cache for import file authors.
	 * @see do_import_posts
	 * @var assoc
	 */
	private $file_authors_cache = array();

	private $types_allowed_for_import = array(
		'post',
		'page',
		'product',
		'faq',
		'configurate_woocommerce',
		'theme_options',
		'theme_widgets',
		//'menu',
	);

	/**
	 * Determines if demo site specific posts should be ignored.
	 *
	 * @var boolean
	 */
	private $ignore_demo_posts = true;

	public function __construct() {
		if ( is_admin() ) {
			$this->init();
		}
	}

	protected function init() {
		add_action( 'admin_menu', array( $this, 'action_admin_menu' ) );
	}

	public function get_import_settings( $with_full_check = false ) {
		$posts_xml_file = 'demo-data-full.xml';

		$cfg = array(
			'post' => array(
				'enabled' => true,
				'title' => 'Posts',
				'description' => 'Imports blog posts and categories from the demo site.',
				'file' => $this->locate_import_file( $posts_xml_file ), // 'demo-data-post.xml'
			),
			'page' => array(
				'enabled' => true,
				'title' => 'Pages',
				'description' => 'Imports pages and menus from the demo site.',
				'file' => $this->locate_import_file( $posts_xml_file ), // 'demo-data-page.xml'
			),
			'product' => array(
				'enabled' => true,
				'title' => 'Tours and Products',
				'description' => 'Imports tours and products from the demo site.',
				'file' => $this->locate_import_file( $posts_xml_file ), // 'demo-data-product.xml'
				'file_adventure_addons' => $this->locate_import_file( 'demo-data-adv-categories-addons.json' ),
			),
			'faq' => array(
				'enabled' => true,
				'title' => 'FAQs',
				'description' => 'Imports FAQs from the demo site.',
				'file' => $this->locate_import_file( $posts_xml_file ),
			),
			'configurate_woocommerce' => array(
				'enabled' => true,
				'title' => 'Configurate Woocommerce',
				'description' => 'Configurates right image sizes for shop section and the products in the widgets.',
				'file' => $this->locate_import_file( 'demo-data-woocommerce-options.json' ),
			),
			'theme_options' => array(
				'enabled' => true,
				'title' => 'Theme Options',
				'description' => 'Updates values in "Appearance" > "Theme Options" section.<br><b>NOTE:</b> will reset all your changes in Theme Options section.',
				'file' => $this->locate_import_file( 'demo-data-theme-options.json' ),
			),
			'theme_widgets' => array(
				'enabled' => true,
				'title' => 'Theme Widgets',
				'description' => 'Install widgets according the demo site.',
				'file' => $this->locate_import_file( 'demo-data-widgets.wie' ),
			),
			'menu' => array(
				'enabled' => false,
				'title' => 'Menus',
				'description' => 'Install widgets according the demo site.',
				'file' => $this->locate_import_file( $posts_xml_file ),
			)
		);

		foreach ($cfg as $type => $options) {
			$available_info = $this->check_import_type_requirements($type, $options, $with_full_check);
			if ( ! $available_info ) {
				$available_info['available'] = false;
			}

			foreach ($available_info as $key => $value) {
				$cfg[$type][$key] = $value;
			}
		}

		return $cfg;
	}

	public function action_admin_menu() {
		add_management_page(
			'Adventure Tours Import',
			'Adv. Tours Import',
			$this->capability,
			$this->page_id,
			array( $this, 'render_page' )
		);
	}

	public function render_page() {
		$results = array();
		$form_data = array();

		if ( !empty( $_GET['import'] ) ) {
			if ( !empty($_POST['import_data']) ) {
				$form_data = $_POST['import_data'];
			}

			$results = $this->do_imports( $form_data );

			$need_resave_permalinks = array('product', 'page', 'theme_options');

			foreach ($need_resave_permalinks as $key) {
				if ( isset($results[$key]) ) {
					$results[$key] .= '<br><b>NOTE:</b> Please resave your settings for section "Settings" > "Permalinks".';

					// tmp solution to fix permalinks in case if theme options have been updated
					//flush_rewrite_rules();
					break;
				}
			}
		}

		adventure_tours_render_template_part( 'includes/admin/views/import', '', array(
			'gateways' => $this->get_import_settings(),
			'form_data' => $form_data,
			'form_hidden_fields' => array(
				'adv-demo-import-nc' => wp_create_nonce( 'adv-demo-import' ),
			),
			'results' => $results,
			'form_action' => add_query_arg(
				array(
					'page' => $this->page_id,
					'import' => '1',
				),
				admin_url( 'tools.php' )
			),
		) );
	}

	// Import functions START
	protected function do_imports( $data, $check_nonce = true ) {
		if ( $check_nonce ) {
			check_admin_referer( 'adv-demo-import', 'adv-demo-import-nc' );
		}

		$checks_status = $this->get_import_settings(true);

		$result = array();

		$imports = isset( $data['types'] ) ? $data['types'] : array();
		$type_options = isset( $data['type_options'] ) ? $data['type_options'] : array();

		if ( $imports ) {
			foreach ($imports as $type ) {
				$type_cfg = !empty( $checks_status[$type] ) ? $checks_status[$type] : array();
				if ( !empty( $type_cfg['enabled'] ) ) {
					$current_type_options = !empty( $type_options[$type] ) ? $type_options[$type] : array();

					try {
						switch ($type) {
						case 'menu':
							$current_type_options['current_user'] = true;
							$current_type_options['allowed_types'] = array( 'nav_menu_item' );
							$r = $this->do_import_posts( $type_cfg, $current_type_options );
							break;

						case 'faq':
						case 'post':
						case 'page':
							$assign_menu_locations = false;
							$allowed_types = array( $type );
							if ( 'page' == $type ) {
								$allowed_types[] = 'wpcf7_contact_form';
								if ( !empty( $current_type_options['include_menus'] ) ) {
									$assign_menu_locations = true;
									$allowed_types[] = 'nav_menu_item';
								}
							}
							$current_type_options['allowed_types'] = $allowed_types;
							$r = $this->do_import_posts( $type_cfg, $current_type_options );

							// assigning menu to right locations
							if ( $assign_menu_locations ) {
								$menu_locations = get_theme_mod( 'nav_menu_locations' );
								$menu_assigns = array(
									'header-menu' => 'Main Menu',
									'footer-menu' => 'Footer Menu',
								);
								$locations_updated = false;
								$registered_locations = get_registered_nav_menus();
								foreach( $menu_assigns as $location_id => $imported_menu_name ) {
									if ( ! isset( $registered_locations[ $location_id ] ) || ! empty( $menu_locations[ $location_id ] ) ) {
										// locations is not defined or it already has menu, so skipping it
										continue;
									}

									$imported_menu_term = get_term_by('name', $imported_menu_name, 'nav_menu');
									if ( $imported_menu_term ) {
										$locations_updated = true;
										$menu_locations[ $location_id ] = $imported_menu_term->term_id;
									}
								}

								if ( $locations_updated ) {
									set_theme_mod( 'nav_menu_locations', $menu_locations );
								}
							}
							break;

						case 'product':
							$allowed_types = array($type);
							$allowed_types[] = 'product_variation';
							$current_type_options['allowed_types'] = $allowed_types;
							$r = $this->do_import_posts( $type_cfg, $current_type_options );
							break;

						case 'configurate_woocommerce':
							$r = $this->do_import_configurate_woocommerce( $type_cfg );
							break;

						case 'theme_options':
							$r = $this->do_import_theme_options( $type_cfg );
							break;

						case 'theme_widgets':
							$r = $this->do_import_widgets( $type_cfg );
							break;

						default:
							$r = esc_html__( 'System error. Please contact support', 'adventure-tours' );
							break;
						}

						$clear_regexps = $this->make_import_clearing_regexps();
						if ( $clear_regexps ) {
							foreach ( $clear_regexps as $regexp ) {
								$r = preg_replace( $regexp, '', $r);
							}
						}

						$result[$type] = $r;
					} catch (Exception $e) {
						$result[$type] = $e->getMessage();
						$result['errors'][$type] = true;
					}
				}
			}
		}

		return $result;
	}

	protected function do_import_posts( $cfg, $import_options = array( 'current_user' => true ) ) {
		$file = $cfg['file'];

		$import_adventure_data = false;

		// $GLOBALS['wp_import'] is required for vafpress
		if ( !empty( $GLOBALS['wp_import'] ) ) {
			$import = $GLOBALS['wp_import'];
		} else {
			$GLOBALS['wp_import'] = $import = new WP_Import();
		}

		// authors processing [start]
		$mapping_uid = get_current_user_id();
		if ( empty( $import_options['current_user'] ) ) {
			$mapping_uid = 0;

			if ( empty( $import_options['another_user'] ) ) {
				throw new Exception('Please enter username for an author.');
			} else {
				$another_username = $import_options['another_user'];

				$existing_user = get_user_by( 'login', $another_username );
				if ( $existing_user ) {
					$mapping_uid = $existing_user->ID;
				} else {
					$mapping_uid = wp_create_user( $another_username, wp_generate_password() );
					if ( is_wp_error( $mapping_uid ) ) {
						throw new Exception( sprintf( 'Can not create new user "%s": %s.', $another_username, $mapping_uid->get_error_message() ) );
					}
				}
			}
		}

		$file_authors = array();
		if ( isset( $this->file_authors_cache[ $file ] ) ) {
			$file_authors = $this->file_authors_cache[ $file ];
		} else {
			// retrivind author list from data file
			if ( isset( $_POST['imported_authors'] ) ) {
				unset( $_POST['imported_authors'] );
			}

			$import_data = $import->parse( $file );
			if ( is_wp_error( $import_data ) ) {
				throw new Exception(
					sprintf( 'Unexpected import error: %s.', esc_html( $import_data->get_error_message() ) )
				);
			}
			$import->get_authors_from_import( $import_data );
			$this->file_authors_cache[ $file ] = $file_authors = $import->authors;
		}

		$authors_mapping = array();
		if ( $file_authors ) {
			foreach ($file_authors as $_login => $_details) {
				$authors_mapping[ sanitize_user( $_login, true ) ] = $mapping_uid;
			}
		}
		$import->author_mapping = $authors_mapping;
		// authors processing [end]

		$this->filter_types_only = empty( $import_options['allowed_types'] ) ? array() : (array) $import_options['allowed_types'];
		$filter_posts_hook = array( $this, 'filter_wp_import_posts' );
		add_filter( 'wp_import_posts', $filter_posts_hook );

		if ( !$this->filter_types_only || in_array( 'post', $this->filter_types_only ) ) {
			$this->allow_categories_import = true;
		}
		$filter_categories_hook = array( $this, 'filter_wp_import_categories' );
		add_filter( 'wp_import_categories', $filter_categories_hook );

		if ( !$this->filter_types_only || in_array( 'product', $this->filter_types_only ) ) {
			$this->allow_product_terms = true;
			$import_adventure_data = !empty( $cfg['file_adventure_addons'] );
		}
		$filter_terms_hook = array( $this, 'filter_wp_import_terms' );
		add_filter( 'wp_import_terms', $filter_terms_hook );

		$filter_postmeta_key = array( $this, 'filter_wp_postmeta_key' );
		add_filter( 'import_post_meta_key', $filter_postmeta_key, 20, 3 );

		ob_start();
		$import->fetch_attachments = false;
		$import->import( $file );

		if ( $import_adventure_data ) {
			$this->do_import_adventure_data( $cfg['file_adventure_addons'] );
		}

		$output = ob_get_clean();

		remove_filter( 'wp_import_posts', $filter_posts_hook );
		remove_filter( 'wp_import_categories', $filter_categories_hook );
		remove_filter( 'wp_import_terms', $filter_terms_hook );
		remove_filter( 'import_post_meta_key', $filter_postmeta_key, 20 );

		return $output;
	}

	protected function do_import_adventure_data( $file ) {
		$import_data = array();

		$content = file_get_contents( $file );
		if ( ! $content ) {
			return;
		}
		$import_data = json_decode( $content );

		foreach( $import_data as $storage_key => $storage_data ) {
			if ( ! $storage_data ) {
				continue;
			}

			$ad_storages = array(
				'product_attribute_icon' => 'product_attribute_icons_storage',
				'tour_cat_display_type' => 'tour_category_display_type_storage',
				'tour_cat_thumb_id' => 'tour_category_images_storage',
				'tour_cat_icon' => 'tour_category_icons_storate',
			);

			$ab_storage = !empty( $ad_storages[ $storage_key ] ) ? adventure_tours_di( $ad_storages[ $storage_key ] ) : null;
			if ( ! $ab_storage ) {
				continue;
			}

			switch( $storage_key ) {
			case 'product_attribute_icon':
				delete_transient( 'wc_attribute_taxonomies' );
				$attributes = wc_get_attribute_taxonomies();
				if ( ! $attributes ) {
					continue;
				}

				foreach ($storage_data as $storage_data_item_slug => $storage_data_item_val) {
					foreach( $attributes as $attribute ) {
						if ( $storage_data_item_slug == $attribute->attribute_name ) {
							if ( ! $ab_storage->getData( $attribute->attribute_id ) ) {
								$ab_storage->setData( $attribute->attribute_id, $storage_data_item_val );
							}
						}
					}
				}
				break;

			//case 'tour_cat_thumb_id' :
			case 'tour_cat_display_type':
			case 'tour_cat_icon' :
				$tour_categories = get_terms( 'tour_category' );
				if ( ! $tour_categories ) {
					continue;
				}
				foreach( $storage_data as $storage_data_item_slug => $storage_data_item_val ) {
					foreach( $tour_categories as $category ) {
						if ( $storage_data_item_slug == $category->slug ) {
							if ( ! $ab_storage->getData( $category->term_id ) ) {
								$ab_storage->setData( $category->term_id, $storage_data_item_val );
							}
						}
					}
				}
				break;
			}
		}
	}

	/**
	 * Filter function for the 'wp_import_posts' filter.
	 * Filters posts during the import process.
	 *
	 * @param  array $posts
	 * @return array
	 */
	public function filter_wp_import_posts( $posts ) {
		foreach ($posts as $key => $post) {
			if ( ! $this->filter_post_for_import( $post ) ) {
				unset($posts[$key]);
			}
		}

		return $posts;
	}

	/**
	 * Desides if post can be imported or not.
	 *
	 * @see    filter_wp_import_posts
	 * @param  assoc   $post
	 * @return boolean
	 */
	protected function filter_post_for_import( $post ) {
		$type = $post['post_type'];
		if ( 'attachment' == $type ) {
			return false;
		}
		// nav_menu_item
		$status = $post['status'];
		if ( 'publish' != $status ) {
			return false;
		}

		if ( $this->filter_types_only && !in_array( $type, $this->filter_types_only ) ) {
			return false;
		}

		if ( $this->ignore_demo_posts && $this->is_demo_specific_post( $post ) ) {
			return false;
		}

		return true;
	}

	public function filter_wp_import_categories( $categories ) {
		if ( ! $this->allow_categories_import ) {
			return array();
		} else {
			return $categories;
		}
	}

	public function filter_wp_import_terms( $terms ) {
		$woocommerce_terms = array();

		foreach ($terms as $key => $term) {
			$remove = false;

			if ( 'faq_category' == $term['term_taxonomy'] ) {
				$remove = true;
			}

			if ( !$remove && ! $this->allow_product_terms ) {
				if ( in_array( $term['term_taxonomy'], array('tour_category', 'product_cat') ) ) {
					$remove = true;
				}

				if ( !$remove && preg_match('/^pa_/', $term['term_taxonomy']) ) {
					$remove = true;
				}
			}

			if ( $remove ) {
				unset($terms[$key]);
			} else {
				if ( strstr( $term['term_taxonomy'], 'pa_' ) ) {
					$woocommerce_terms[] = $term;
				}
			}
		}

		if ( $woocommerce_terms ) {
			$this->register_woocommerce_attributes( $woocommerce_terms );
		}

		return $terms;
	}

	public function filter_wp_postmeta_key( $key, $post_id, $post ) {
		$ignore_meta_keys = array(
			'_thumbnail_id', '_product_image_gallery', 'header_section_meta',
			'demometa_booking_hide_additional_fields'
		);
		if ( in_array( $key, $ignore_meta_keys ) ) {
			return false;
		}
		return $key;
	}

	protected function register_woocommerce_attributes( $woocommerce_terms ) {
		if ( ! $woocommerce_terms ) {
			return;
		}

		global $wpdb;

		foreach ( $woocommerce_terms as $key => $term ) {
			$domain = $term['term_taxonomy'];

			if ( ! taxonomy_exists( $domain ) ) {
				$nicename = strtolower( sanitize_title( str_replace( 'pa_', '', $domain ) ) );

				// no other api to check/add woocommerce attributes
				$exists_in_db = $wpdb->get_var( 
					$wpdb->prepare(
						"SELECT attribute_id FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies " .
							"WHERE attribute_name = %s;",
						$nicename
					)
				);

				// Create the taxonomy
				if ( ! $exists_in_db ) {
					$wpdb->insert( $wpdb->prefix . "woocommerce_attribute_taxonomies", 
						array(
							'attribute_name' => $nicename,
							'attribute_label' => ucwords( str_replace('-', '', $nicename) ),
							'attribute_type' => 'select', // 'text', 
							'attribute_orderby' => 'menu_order',
							'attribute_public' => 0,
						), array( '%s', '%s' , '%s', '%s' )
					);
				}

				// registering new taxonomy
				register_taxonomy(
					$domain,
					apply_filters( 'woocommerce_taxonomy_objects_' . $domain, array( 'product' ) ),
					apply_filters( 'woocommerce_taxonomy_args_' . $domain, array(
						'hierarchical' => true,
						'show_ui' => false,
						'query_var' => true,
						'rewrite' => false,
					) )
				);
			}
		}

		delete_transient( 'wc_attribute_taxonomies' );
	}

	protected function do_import_configurate_woocommerce( $cfg ) {
		$source_file = $cfg['file'];

		$action_config = json_decode( file_get_contents( $source_file ), true );
		ob_start();
		if ( !empty( $action_config['options'] ) ) {
			foreach ($action_config['options'] as $option_name => $option_value ) {
				update_option( $option_name, $option_value );
			}
		}

		return 'Woocommerce options have been updated';
	}

	protected function do_import_theme_options( $cfg ) {
		$theme_options_file = $cfg['file'];

		$theme_option_component = adventure_tours_di( 'register' )->getVar( '_vp_theme_option' );
		if ( ! $theme_option_component ) {
			throw new Exception( '[di500] Data import error. Please contact support.' );
		}
		$theme_option_values = json_decode( file_get_contents( $theme_options_file ), true );
		if ( ! $theme_option_values ) {
			throw new Exception( '[di501] Theme options parsing error. Please contact support.' );
		}

		$theme_option_component->init_options_set();
		$theme_option_component->init_options();

		$set = $theme_option_component->get_options_set();

		$tour_page_id = adventure_tours_get_option('tours_page');
		$tour_page_instance = $tour_page_id ? get_page( $tour_page_id ) : null;
		if ( ! $tour_page_instance ) {
			$tour_page_instance = get_page_by_path('tours', OBJECT, 'page');
			if ( $tour_page_instance ) {
				$tour_page_id = $tour_page_instance->ID;
			}
		}
		$theme_option_values['tours_page'] = $tour_page_id;

		// populate new values
		$theme_option_component->get_options_set()->populate_values( $theme_option_values, false );

		$theme_options_saving_result = $theme_option_component->save_and_reinit();

		if ( true != $theme_options_saving_result['status'] ) {
			throw new Exception( '[to503] ' . $theme_options_saving_result['message'] );
		} else {
			$saved_theme_values = $theme_option_component->get_options_set()->get_values();
		}

		return 'Settings have been imported.';
	}

	protected function do_import_widgets( $cfg ) {
		$file = $cfg['file'];

		// Get file contents and decode
		$data = json_decode( file_get_contents( $file ) );
		if ( empty( $data ) ) {
			throw new Exception( '[diw501] Widgets file data parsing error. Please contact support.' );
		}

		$import_results = wie_import_data( $data );

		$report_html = '';
		foreach( $import_results as $area_key => $area_details ) {
			if ( empty( $area_details['widgets'] ) ) {
				continue;
			}

			$widgets_info = array();
			foreach( $area_details['widgets'] as $widget_details ) {
				//if $widget_details['message_type'] == 'warning'
				$widgets_info[] = sprintf( "%s - %s",
					!empty( $widget_details['title'] ) && 'No Title' != $widget_details['title'] ? sprintf('%s [ %s ]', $widget_details['name'], $widget_details['title'] ) : $widget_details['name'],
					$widget_details['message']
				);
			}

			$report_html .= sprintf(
				'<div class="widgets-report"><div class="widgets-report__title">%s</div><div class="widgets-report__list">%s</div></div>',
				$area_details['name'],
				join( '<br>', $widgets_info )
			);
		}

		return $report_html;
	}

	/**
	 * Detects if postmeta contains '_demo_import_ignore' or 'demo_import_ignore' key, or if page uses template that stars with 'demo-' prefix.
	 *
	 * @param  assoc   $post
	 * @return boolean
	 */
	protected function is_demo_specific_post( $post ) {
		$postmeta = !empty( $post['postmeta'] ) ? $post['postmeta'] : array();
		$ignore_flags = array( '_demo_import_ignore', 'demo_import_ignore' );
		foreach ( $postmeta as $meta_item ) {
			$meta_key = ! empty( $meta_item['key'] ) ? $meta_item['key'] : '';
			if ( ! $meta_key ) {
				continue;
			}
			if ( in_array( $meta_key, $ignore_flags ) && ! empty( $meta_item['value'] ) ) {
				return true;
			}

			if( 'page' == $post['post_type'] && '_wp_page_template' == $meta_key ) {
				if ( ! empty( $meta_item['value'] ) && preg_match( '`^demo-`', $meta_item['value'] ) ) {
					return true;
				}
			}
		}

		return false;
	}

	protected function check_import_type_requirements( $type, $options, $full_check = false ) {
		$errors = array();

		if ( empty( $options['enabled'] ) ) {
			$errors[] = 'This option is disabled.';
		} elseif ( ! in_array( $type, $this->types_allowed_for_import ) ) {
			$errors[] = 'Unknown import type.';
		}

		if ( ! $errors ) {
			$check_keys = array( 'file', 'file_adventure_addons' );
			foreach ( $check_keys as $key_name ) {
				if ( !empty( $options[ $key_name ] ) && !file_exists( $options[ $key_name ] ) ) {
					$errors[] = 'Import file is missed. Please contact support.';
				}
			}
		}

		switch ($type) {
		case 'faq':
		case 'page':
		case 'post':
		case 'product':
			// wordpress-importer plugin check
			$import_plugin_file = 'wordpress-importer/wordpress-importer.php';
			if ( ! is_plugin_active( $import_plugin_file ) ) { 
				$errors[] = $this->get_missed_plugin_message( 'WordPress Importer', 'wordpress-importer' );
			}
			if ( $full_check ) {
				if ( ! class_exists('WP_Import') ) {
					/*if (!defined('WP_LOAD_IMPORTERS') ) {
						define('WP_LOAD_IMPORTERS', true);
						include ABSPATH . '/wp-content/plugins/' . $import_plugin_file;
					}*/
					if ( ! class_exists('WP_Import') ) {
						$errors[] = 'Can not load WP_Import class.';
					}
				}
			}

			if ( 'product' == $type ) {
				if ( ! class_exists( 'woocommerce' ) ) {
					$errors[] = $this->get_missed_plugin_message( 'Woocommerce', 'woocommerce' );
				}

				if ( ! adventure_tours_check( 'tour_category_taxonomy_exists' ) ) {
					$errors[] = $this->get_missed_plugin_message( 'Data types for Adventure Tours theme', '' );
				}
			}
			break;

		case 'configurate_woocommerce':
			if ( ! class_exists( 'woocommerce' ) ) {
				$errors[] = $this->get_missed_plugin_message( 'Woocommerce', 'woocommerce' );
			}
			break;

		case 'theme_widgets':
			// widget-importer-exporter plugin check
			$wie_plugin_file = 'widget-importer-exporter/widget-importer-exporter.php';
			if ( ! is_plugin_active( $wie_plugin_file ) ) { 
				$errors[] = $this->get_missed_plugin_message( 'Widget Importer & Exporter', 'widget-importer-exporter' );
			}
			break;
		}

		$result = array(
			'available' => empty( $errors )
		);

		if ( $errors ) {
			$result['errors'] = $errors;
		}

		return $result;
	}

	protected function get_missed_plugin_message( $name, $slug, $show_plugins_section_link = true ) {
		$result = '';
		if ( $slug ) {
			$result = sprintf( 'Please install and activate "%s" plugin.', $name);
		}

		if ( $show_plugins_section_link ) {
			$install_plugins_url = add_query_arg(
				array(
					'page' => 'install-required-plugins',
				),
				admin_url( 'themes.php' )
			);
			$result .= ' ' . sprintf( 'You can do this at <a href="%s">here</a>.', $install_plugins_url );
		}

		return $result;
	}

	protected function locate_import_file( $file ) {
		static $data_folder_path;
		if ( null === $data_folder_path) {
			$data_folder_path = get_template_directory() . '/includes/data/demo/';
		}
		return $file ? $data_folder_path . $file : null;
	}

	protected function make_import_clearing_regexps() {
		$result = array();

		$start_point_text = '{_START_POINT_}';
		$start_point_replace_regexp = sprintf( '`%s.*$`', $start_point_text );
		$start_point_real_regexp = '[^<]+<br\s?\/?>';
		// media
		$media_type = get_post_type_object('attachment');

		$context = join('-', array( 'wordpress' ,'importer' ) );

		if ( $media_type ) {
			$text = sprintf( 
				$this->txt_msg( 'Failed to import %s &#8220;%s&#8221;', $context ),
				$media_type->labels->singular_name,
				$start_point_text
			);
			$result[] = sprintf('`%s`', preg_replace( $start_point_replace_regexp, $start_point_real_regexp, $text ) );
		}

		// pa_*
		$text = sprintf(
			$this->txt_msg( 'Failed to import %s %s', $context ),
			'pa_' . $start_point_text,
			''
		);
		$result[] = sprintf('`%s`', preg_replace( $start_point_replace_regexp, $start_point_real_regexp, $text ) );

		return $result;
	}

	protected function txt_msg( $msg, $context ) {
		$proxy = join('', array( '_', '_', ) );
		if ( function_exists( $proxy ) ) {
			return call_user_func( $proxy, $msg, $context );
		} else {
			return $msg;
		}
	}
}

new AtDemoDataImport();
