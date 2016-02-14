<?php
/**
 * Theme core file.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.3.2
 */

if ( ! defined( 'THEME_IS_DEV_MODE' ) ) {
	define( 'THEME_IS_DEV_MODE', false );
}

define( 'PARENT_DIR', get_template_directory() );
define( 'PARENT_URL', get_template_directory_uri() );

define( 'TOUR_POST_TYPE', 'product' );

// for page with sidebar
if ( ! isset( $content_width ) ) {
	$content_width = 748;
}

require PARENT_DIR . '/includes/loader.php';

/**
 * Returns dependency injection container/element from container by key.
 *
 * @param  string $key
 * @return mixed
 */
function &adventure_tours_di( $key = null ) {
	static $di;
	if ( ! $di ) {
		$di = new JuiceContainer();
	}
	if ( $key ) {
		$result = $di[ $key ];
		return $result;
	}
	return $di;
}

add_action( 'adventure_tours_init_di', 'adventure_tours_init_di_callback', 10, 2 );
function adventure_tours_init_di_callback( $di, $config ) {
	if ( $config ) {
		foreach ( $config as $key => $value ) {
			$instance = null;
			$class = '';
			$typeof = gettype( $value );
			switch ( $typeof ) {
			case 'string':
				$class = $value;
				break;

			case 'array':
				$class = array_shift( $value );
				break;

			default:
				$instance = $value;
				$class = get_class( $instance );
				break;
			}
			$diKey = is_string( $key ) ? $key : $class;
			if ( isset( $di[$diKey] ) ) {
				continue;
			}

			$di[$diKey] = $instance ? $instance : JuiceDefinition::create( $class, $value );
		}
	}
}

// -----------------------------------------------------------------#
// Theme settings functions
// -----------------------------------------------------------------#
/**
 * Option name used for storing theme settings.
 *
 * @see adventure_tours_get_option
 * @see adventure_tours_filter_after_theme_setup
 */
if ( ! defined( 'VP_OPTION_KEY' ) ) { define( 'VP_OPTION_KEY', 'adventure_tours_theme_options' ); }

// Vafpress framework integration.
if ( ! defined( 'VP_URL' ) ) {
	define( 'VP_URL', PARENT_URL . '/vendor/vafpress' );
}
require PARENT_DIR . '/vendor/vafpress/bootstrap.php';

// Additional vafpress fields implementation.
// VP_AutoLoader::add_directories(PARENT_DIR .'/includes/vafpress-addon/classes', 'VP_'); .
// VP_FileSystem::instance()->add_directories('views', PARENT_DIR .'/includes/vafpress-addon/views'); .
if ( ! function_exists( 'adventure_tours_get_option' ) ) {
	/**
	 * Returns theme option value.
	 *
	 * @param  string $name    option name.
	 * @param  mixed  $default default value.
	 * @return mixed
	 */
	function adventure_tours_get_option($name, $default = null) {
		return vp_option( VP_OPTION_KEY .'.'.$name, $default );
	}
}

if ( ! function_exists( 'adventure_tours_filter_after_theme_setup' ) ) {
	/**
	 * Init theme function.
	 *
	 * @return void
	 */
	function adventure_tours_filter_after_theme_setup() {
		load_theme_textdomain( 'adventure-tours', PARENT_DIR . '/languages' );

		do_action( 'adventure_tours_init_di', adventure_tours_di(), require PARENT_DIR . '/config.php' );
		$autoinit_services = adventure_tours_di( 'register' )->getVar( 'autoinit_services' );
		if ( $autoinit_services ) {
			foreach ( $autoinit_services as $service_id ) {
				adventure_tours_di( $service_id );
			}
		}

		// Initing Vafpress Framework theme options.
		$vp_theme_option = new VP_Option(array(
			// 'is_dev_mode'           => THEME_IS_DEV_MODE,
			'option_key'            => VP_OPTION_KEY,
			'page_slug'             => 'theme_options_page',
			'template'              => PARENT_DIR . '/includes/theme-options-config.php',
			'menu_page'             => 'themes.php',
			'use_auto_group_naming' => true,
			'use_exim_menu'         => true,
			'minimum_role'          => 'edit_theme_options',
			'layout'                => 'fixed',
			'page_title'            => esc_html__( 'Theme Options', 'adventure-tours' ),
			'menu_label'            => esc_html__( 'Theme Options', 'adventure-tours' ),
		));
		adventure_tours_di( 'register' )->setVar( '_vp_theme_option', $vp_theme_option );

		if ( adventure_tours_check( 'is_wpml_in_use' ) ) {
			new AtWPMLIntegrationHelper(array(
				'option_name' => VP_OPTION_KEY,
				'settings_for_translation' => array(
					'banner_default_subtitle',
					'contact_phone',
					'contact_time',
					'footer_text_note',
					'tour_badge_1_title',
					'tour_badge_2_title',
					'tour_badge_3_title',
					'tours_search_form_title',
					'tours_search_form_note',
					'excerpt_text',
					'faq_show_question_form',
				),
				'page_id_settings_for_translation' => array(
					'tours_page',
				)
			));
		}

		if ( is_super_admin() && !THEME_IS_DEV_MODE && adventure_tours_get_option( 'update_notifier' ) ) {
			adventure_tours_di( 'theme_updater' );
		}
	}

	add_action( 'after_setup_theme', 'adventure_tours_filter_after_theme_setup' );
}

if ( ! function_exists( 'adventure_tours_action_init' ) ) {
	/**
	 * Callback for 'init' action.
	 *
	 * @return void
	 */
	function adventure_tours_action_init() {
		if ( adventure_tours_check( 'tour_category_taxonomy_exists' ) ) {
			// Initing services related on images and icons processing for tour_category taxonomy.
			adventure_tours_di( 'taxonomy_display_data' );
			adventure_tours_di( 'taxonomy_images' );
			adventure_tours_di( 'taxonomy_icons' );
			adventure_tours_di( 'taxonomy_header_sections' );

			// Init for the tour booking form.
			adventure_tours_di( 'booking_form' );
		}

		if ( adventure_tours_check( 'woocommerce_active' ) ) {
			adventure_tours_di( 'wc_shortcodes_helper' );
		}

		if ( is_admin() ) {
			// To init shortcodes menu for tinyMCE.
			require 'shortcodes/menu.php';
		}
	}
	add_action( 'init', 'adventure_tours_action_init' );
}

if ( ! function_exists( 'adveture_tours_filter_tour_loop_settings' ) ) {
	/**
	 * Filter function that loads settings for the tour page from theme options.
	 *
	 * @param  assoc  $settings
	 * @param  string $view_type allowed values are: 'list' or 'grid'
	 * @return assoc
	 */
	function adveture_tours_filter_tour_loop_settings( $settings, $view_type = '' ) {
		$settings['show_categories'] = adventure_tours_get_option( 'tours_archive_tour_display_category', '1' );
		$settings['description_words_limit'] = adventure_tours_get_option( 'tours_archive_tour_description_words_limit', '13' );

		if ( 'grid' == $view_type ) {
			$settings['columns'] = adventure_tours_get_option( 'tours_archive_columns_number', '2' );
			$settings['price_style'] = adventure_tours_get_option( 'tours_archive_tour_price_style', 'default' );

			if ( 2 == $settings['columns'] ) {
				$settings['image_size'] = 'thumb_tour_listing';
			}
		}

		return $settings;
	}

	add_filter( 'adveture_tours_loop_settings', 'adveture_tours_filter_tour_loop_settings', 10, 2 );
}

if ( is_admin() ) {
	require 'admin/plugins.php';

	require 'admin/demo-data-import.php';
}

require 'theme-options-functions.php';

require 'template-functions.php';

if ( class_exists( 'woocommerce' ) ) {
	add_theme_support( 'woocommerce' );
	require_once PARENT_DIR . '/woocommerce/woocommerce.php';
}

// -----------------------------------------------------------------#
// Asserts registration
// -----------------------------------------------------------------#
if ( ! function_exists( 'adventure_tours_init_theme_asserts' ) ) {
	/**
	 * Defines theme assets.
	 *
	 * @return void
	 */
	function adventure_tours_init_theme_asserts() {
		$minExt = SCRIPT_DEBUG ? '' : '.min';

		$is_rtl = is_rtl();
		if ( THEME_IS_DEV_MODE ) {
			if ( $is_rtl ) {
				wp_enqueue_style( 'bootstrap-custom-rtl', PARENT_URL . '/assets/csslib/bootstrap-custom-rtl.css' );
			} else {
				wp_enqueue_style( 'bootstrap-custom', PARENT_URL . '/assets/csslib/bootstrap-custom.css' );
			}

			wp_enqueue_style( 'fontawesome', PARENT_URL . '/assets/csslib/font-awesome.min.css' );
			wp_enqueue_style( 'bootstrap-select', PARENT_URL . '/assets/csslib/bootstrap-select.min.css' );
			wp_register_style( 'magnific-popup', PARENT_URL . '/assets/csslib/magnific-popup.css', array(), '1.0.0' );

			wp_register_style( 'swipebox', PARENT_URL . '/assets/csslib/swipebox.css' );
			wp_register_style( 'swiper', PARENT_URL . '/assets/csslib/swiper.min.css' );

			wp_enqueue_script( 'bootstrap', PARENT_URL . '/assets/jslib/bootstrap.min.js',array( 'jquery' ), '',true );
			wp_enqueue_script( 'bootstrap-select', PARENT_URL . '/assets/jslib/bootstrap-select/bootstrap-select.min.js', array( 'jquery', 'bootstrap' ), '', true );
			wp_enqueue_script( 'slicknav', PARENT_URL . '/assets/jslib/jquery.slicknav.js',array( 'jquery' ), '',true );
			wp_enqueue_script( 'tabcollapse', PARENT_URL . '/assets/jslib/bootstrap-tabcollapse.js', array( 'jquery' ), '', true );
			wp_enqueue_script( 'theme', PARENT_URL . '/assets/js/Theme.js', array( 'jquery' ), '', true );
			wp_register_script( 'magnific-popup', PARENT_URL . '/assets/jslib/jquery.magnific-popup.min.js', array( 'jquery' ), '1.0.0', true );

			if ( adventure_tours_get_option( 'show_header_search' ) ) {
				wp_enqueue_style( 'magnific-popup' );
				wp_enqueue_script( 'magnific-popup' );
			}

			wp_register_script( 'swipebox', PARENT_URL . '/assets/jslib/jquery.swipebox.js', array( 'jquery' ), '1.3.0.2', true );
			wp_register_script( 'swiper', PARENT_URL . '/assets/jslib/swiper/swiper.jquery.min.js', array(), '', true );

			wp_register_script( 'parallax', PARENT_URL . '/assets/jslib/jquery.parallax-1.1.3.js', array( 'jquery' ), '1.1.3', true );

			wp_register_script( 'sharrre', PARENT_URL . '/assets/jslib/jquery.sharrre.js', array( 'jquery' ), '',true );
		} else {
			wp_enqueue_style( 'theme-addons', PARENT_URL . '/assets/csslib/theme-addons' . ( $is_rtl ? '-rtl' : '' ) . $minExt . '.css', array(), '1.3.0' );
			wp_enqueue_script( 'theme', PARENT_URL . '/assets/js/theme-full' . $minExt . '.js', array( 'jquery' ), '1.3.2', true );
		}

		$styleCollection = apply_filters('get-theme-styles', array(
			'style-css' => get_stylesheet_uri(),
		));

		if ( $styleCollection ) {
			foreach ( $styleCollection as $_itemKey => $resourceInfo ) {
				$_styleText = null;
				$_styleUrl = null;
				if ( ! is_array( $resourceInfo ) ) {
					$_styleUrl = $resourceInfo;
				} else {
					if ( isset( $resourceInfo['text'] ) ) {
						$_styleText = $resourceInfo['text'];
					} elseif ( isset( $resourceInfo['url'] ) ) {
						$_styleUrl = $resourceInfo['url'];
					}
				}
				if ( $_styleUrl ) {
					wp_enqueue_style( $_itemKey, $_styleUrl );
				} elseif ( $_styleText ) {
					adventure_tours_di( 'register' )->pushVar('header_inline_css_text', array(
						'id' => $_itemKey,
						'text' => $_styleText,
					));
				}
			}
		}

		wp_register_script( 'jPages', PARENT_URL . '/assets/jslib/jPages.js', array( 'jquery' ), '', true );

		// wp_register_style( 'jquery-ui-datepicker-custom', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css', array(), '1.8.2' );
		wp_register_style( 'jquery-ui-datepicker-custom', PARENT_URL . '/assets/csslib/jquery-ui-custom/jquery-ui.min.css', array(), '1.11.4' );
	}

	add_action( 'wp_enqueue_scripts', 'adventure_tours_init_theme_asserts' );
}

// -----------------------------------------------------------------#
// Widgets registration
// -----------------------------------------------------------------#
if ( ! function_exists( 'adventure_tours_register_widgets' ) ) {
	/**
	 * Hook for widgets registration.
	 *
	 * @return void
	 */
	function adventure_tours_register_widgets() {
		// Make a Wordpress built-in Text widget process shortcodes.
		add_filter( 'widget_text', 'shortcode_unautop');
		add_filter( 'widget_text', 'do_shortcode', 11);

		register_widget( 'AtWidgetLatestPosts' );

		register_widget( 'AtWidgetContactUs' );

		register_widget( 'AtWidgetTwitterTweets' );

		if ( class_exists( 'woocommerce' ) ) {
			register_widget( 'AtWidgetTours' );
		}

		register_sidebar(array(
			'id'            => 'sidebar',
			'name'          => esc_html__( 'Sidebar', 'adventure-tours' ),
			'description'   => esc_html__( 'Sidebar located on the right side of blog page.', 'adventure-tours' ),
			'before_widget' => '<div id="%1$s" class="widget block-after-indent %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget__title">',
			'after_title'   => '</h3>',
		));

		register_sidebar(array(
			'id'            => 'shop-sidebar',
			'name'          => esc_html__( 'Shop Sidebar', 'adventure-tours' ),
			'description'   => esc_html__( 'Sidebar located on the right side on pages related to shop.', 'adventure-tours' ),
			'before_widget' => '<div id="%1$s" class="widget block-after-indent %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget__title">',
			'after_title'   => '</h3>',
		));

		register_sidebar(array(
			'id'            => 'tour-sidebar',
			'name'          => esc_html__( 'Tour Sidebar', 'adventure-tours' ),
			'description'   => esc_html__( 'Sidebar located on the right side on pages related to tour.', 'adventure-tours' ),
			'before_widget' => '<div id="%1$s" class="widget block-after-indent %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget__title">',
			'after_title'   => '</h3>',
		));

		register_sidebar(array(
			'id'            => 'faq-sidebar',
			'name'          => esc_html__( 'FAQs', 'adventure-tours' ),
			'description'   => esc_html__( 'Sidebar located on the FAQ page.', 'adventure-tours' ),
			'before_widget' => '<div id="%1$s" class="widget block-after-indent %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget__title">',
			'after_title'   => '</h3>',
		));

		register_sidebar(array(
			'id'            => 'footer1',
			'name'          => sprintf( esc_html__( 'Footer %s', 'adventure-tours' ), 1 ),
			'description'   => esc_html__( 'Located in 1st column on 4-columns footer layout.', 'adventure-tours' ),
			'before_widget' => '<div id="%1$s" class="widget block-after-indent %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget__title">',
			'after_title'   => '</h3>',
		));

		$footerColumnsCount = adventure_tours_get_footer_columns();
		if ( $footerColumnsCount >= 2 ) {
			register_sidebar(array(
				'id'            => 'footer2',
				'name'          => sprintf( esc_html__( 'Footer %s', 'adventure-tours' ), 2 ),
				'description'   => esc_html__( 'Located in 2nd column on 4-columns footer layout.', 'adventure-tours' ),
				'before_widget' => '<div id="%1$s" class="widget block-after-indent %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget__title">',
				'after_title'   => '</h3>',
			));
		}

		if ( $footerColumnsCount >= 3 ) {
			register_sidebar(array(
				'id'            => 'footer3',
				'name'          =>sprintf( esc_html__( 'Footer %s', 'adventure-tours' ), 3 ),
				'description'   => esc_html__( 'Located in 3rd column on 4-columns footer layout.', 'adventure-tours' ),
				'before_widget' => '<div id="%1$s" class="widget block-after-indent %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget__title">',
				'after_title'   => '</h3>',
			));
		}

		if ( $footerColumnsCount >= 4 ) {
			register_sidebar(array(
				'id'            => 'footer4',
				'name'          => sprintf( esc_html__( 'Footer %s', 'adventure-tours' ), 4 ),
				'description'   => esc_html__( 'Located in 4th column on 4-columns footer layout.', 'adventure-tours' ),
				'before_widget' => '<div id="%1$s" class="widget block-after-indent %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget__title">',
				'after_title'   => '</h3>',
			));
		}
	}
	add_action( 'widgets_init', 'adventure_tours_register_widgets' );
}

add_theme_support( 'title-tag' );
add_theme_support( 'automatic-feed-links' );

add_theme_support( 'post-thumbnails' );

add_theme_support( 'menus' );
register_nav_menus(array(
	'header-menu' => esc_html__( 'Header Menu', 'adventure-tours' ),
	'footer-menu' => esc_html__( 'Footer Menu', 'adventure-tours' ),
));

add_theme_support( 'html5', array( 'gallery', 'caption', 'search-form' ) );

if ( ! function_exists( 'adventure_tours_action_after_theme_setup' ) ) {
	/**
	 * Callback for 'after_setup_theme' action.
	 * Creates metaboxes for for pages and tours.
	 *
	 * @return void
	 */
	function adventure_tours_action_after_theme_setup() {
		new VP_Metabox(array(
			'id'           => 'tour_tabs_meta',
			'types'        => array( TOUR_POST_TYPE ),
			'title'        => esc_html__( 'Tour Data', 'adventure-tours' ),
			'priority'     => 'high',
			'is_dev_mode'  => false,
			'template'     => PARENT_DIR . '/includes/metabox/tour-tabs-meta.php',
		));

		new VP_Metabox(array(
			'id'           => 'header_section_meta',
			'types'        => array( 'page', 'post', 'at_header_section', TOUR_POST_TYPE, ),
			'title'        => esc_html__( 'Header Section', 'adventure-tours' ),
			'priority'     => 'high',
			'is_dev_mode'  => false,
			'template'     => PARENT_DIR . '/includes/metabox/header-section-meta.php',
		));
	}
	add_action( 'after_setup_theme', 'adventure_tours_action_after_theme_setup' );
}

// -----------------------------------------------------------------#
// Rendering: filters & helpers
// -----------------------------------------------------------------#
if ( ! function_exists( 'adventure_tours_render_header_resources' ) ) {
	/**
	 * Renders theme header resources.
	 *
	 * @return void
	 */
	function adventure_tours_render_header_resources() {
		$inlinePices = adventure_tours_di( 'register' )->getVar( 'header_inline_css_text' );
		if ( $inlinePices ) {
			foreach ( $inlinePices as $inlinePiceInfo ) {
				if ( empty( $inlinePiceInfo['text'] ) ) {
					continue;
				}
				printf( "<style type=\"text/css\">%s</style>\n", $inlinePiceInfo['text'] );
			}
			adventure_tours_di( 'register' )->setVar( 'header_inline_css_text', array() );
		}

		$customCss = adventure_tours_get_option( 'custom_css_text' );
		if ( $customCss ) {
			printf( "<style type=\"text/css\">%s</style>\n", $customCss );
		}
	}
	add_action( 'wp_head', 'adventure_tours_render_header_resources' );
}

if ( ! function_exists( 'adventure_tours_filter_theme_styles' ) ) {
	/**
	 * Filter for theme style files list.
	 *
	 * @param  array $defaultSet list of default files that should be used.
	 * @return array
	 */
	function adventure_tours_filter_theme_styles(array $defaultSet) {
		$isCustomizeRequest = isset( $_POST['wp_customize'] ) && 'on' == $_POST['wp_customize'];

		$is_rtl = is_rtl();

		$cacheId = $isCustomizeRequest || THEME_IS_DEV_MODE ? '' : ( 'adventure_tours_generated_styles_list' . ( $is_rtl ? '_rtl' : '' ) );

		$cachedValue = $cacheId ? get_transient( $cacheId ) : false;
		if ( false == $cachedValue ) {
			$app = adventure_tours_di( 'app' );
			$styleOptions = $app->getStyleOptions( $isCustomizeRequest );
			// Special variable used to point url locations.
			if ( ! isset( $styleOptions['assetsUrl'] ) ) {
				$styleOptions['assetsUrl'] = '"' . PARENT_URL . '/assets/"';
			}

			if ( $is_rtl ) {
				$styleOptions['bi-app-left'] = 'right';
				$styleOptions['bi-app-right'] = 'left';
				$styleOptions['bi-app-direction'] = 'rtl';
				$styleOptions['bi-app-invert-direction'] = 'ltr';
			} else {
				$styleOptions['bi-app-left'] = 'left';
				$styleOptions['bi-app-right'] = 'right';
				$styleOptions['bi-app-direction'] = 'ltr';
				$styleOptions['bi-app-invert-direction'] = 'rtl';
			}

			$cachedValue = $app->generateCustomCss(
				adventure_tours_di( 'register' )->getVar( 'main_less_file' ),
				$styleOptions,
				$isCustomizeRequest ? 'preview-main' :  ( 'main-custom' . ( $is_rtl ? '-rtl' : '' ) )
			);

			$cachedValue = array_merge( $defaultSet, $cachedValue );
			if ( $cacheId ) {
				set_transient( $cacheId, $cachedValue );
			}
		}

		return $cachedValue ? $cachedValue : $defaultSet;
	}
	add_filter( 'get-theme-styles', 'adventure_tours_filter_theme_styles', 1, 1 );
}

if ( ! function_exists( 'adventure_tours_flush_style_cache' ) ) {
	/**
	 * Resets generated styles cache.
	 *
	 * @return void
	 */
	function adventure_tours_flush_style_cache() {
		delete_transient( 'adventure_tours_generated_styles_list' );
		delete_transient( 'adventure_tours_generated_styles_list_rtl' );
	}
	add_action( 'customize_save_after', 'adventure_tours_flush_style_cache' );
	add_action( 'after_switch_theme', 'adventure_tours_flush_style_cache' );
}

if ( ! function_exists( 'adventure_tours_get_tour_booking_range' ) ) {
	/**
	 * Returns range during that booking for specefied tour can be done.
	 *
	 * @param  int $tour_id
	 * @return assoc        contains 'start' and 'end' keys with dates during that booking is active
	 */
	function adventure_tours_get_tour_booking_range( $tour_id ) {
		static $start_days_in, $length;
		if ( null == $start_days_in ) {
			$start_days_in = (int) adventure_tours_get_option( 'tours_booking_start' );
			$length = (int) adventure_tours_get_option( 'tours_booking_length' );
			if ( $start_days_in < 0 ) {
				$start_days_in = 0;
			}
			if ( $length < 1 ) {
				$length = 1;
			}
		}

		$min_time = strtotime( '+' . $start_days_in . ' day' );
		$max_time = strtotime( '+' . $length . ' day', $min_time );

		return array(
			'start' => date( 'Y-m-d', $min_time ),
			'end' => date( 'Y-m-d', $max_time ),
		);
	}
}

if ( ! function_exists( 'adventure_touts_get_tour_booking_dates' ) ) {
	/**
	 * Returns assoc that contains dates and number of available tickets for a date.
	 *
	 * @param  integer $tour_id
	 * @param  boolean $allow_cache
	 * @return assoc   the date is used as a key, tickets number as a value
	 */
	function adventure_touts_get_tour_booking_dates( $tour_id, $allow_cache = true ) {
		static $cache = array();

		if ( $tour_id > 0 ) {
			if ( empty( $cache ) || ! $allow_cache || ! isset( $cache[ $tour_id ] ) ) {
				$booking_range = adventure_tours_get_tour_booking_range( $tour_id );
				$cache[ $tour_id ] = adventure_tours_di( 'tour_booking_service' )->get_expanded( $tour_id, $booking_range['start'], $booking_range['end'] );
			}

			return $cache[ $tour_id ];
		}
		return array();
	}
}

if ( ! function_exists( 'adventure_tours_get_shop_page_display_mode' ) ) {
	/**
	 * Returns display mode for shop pages (shop pages and product category pages).
	 *
	 * @return string possible values are: 'products','subcategories','both'
	 */
	function adventure_tours_get_shop_page_display_mode() {
		$display_type = get_option( 'woocommerce_shop_page_display' );

		if ( ! is_shop() && is_product_category() ) {
			$term = get_queried_object();
			$cat_mode = get_woocommerce_term_meta( $term->term_id, 'display_type', true );

			if ( ! $cat_mode ) {
				$cat_mode = get_option( 'woocommerce_category_archive_display' );
			}

			if ( $cat_mode ) {
				$display_type = $cat_mode;
			}

			/*if ( $display_type && $display_type != 'products' ) {
				$result = $display_type;
			}*/
		}

		return $display_type ? $display_type : 'products';
	}
}

if ( ! function_exists( 'adventure_tours_check' ) ) {
	function adventure_tours_check( $check_name, $ignore_cache = false ) {
		static $cache = array();

		if ( ! isset( $cache[ $check_name ] ) || $ignore_cache ) {
			$result = false;

			switch( $check_name ) {
			case 'is_single_tour':
				if ( is_single() && 'product' == get_post_type() && function_exists( 'wc_get_product' ) ) {
					$product = wc_get_product();
					$result = $product && $product->is_type( 'tour' );
				}
				break;

			case 'is_tour_search':
				return $GLOBALS['wp_query']->get( 'is_tour_query' );
				break;

			case 'tour_category_taxonomy_exists':
				$result = taxonomy_exists( 'tour_category' );
				break;

			case 'media_category_taxonomy_exists':
				$result = taxonomy_exists( 'media_category' );
				break;

			case 'faq_taxonomies':
				$result = taxonomy_exists( 'faq_category' ) && post_type_exists( 'faq' );
				break;

			case 'woocommerce_active':
			case 'tours_active':
				$result = class_exists( 'woocommerce' );
				break;

			case 'is_wpml_in_use':
				$result = defined( 'ICL_SITEPRESS_VERSION' ); // function_exists( 'icl_object_id' );
				break;
			}

			$cache[ $check_name ] = $result;
		}

		return $cache[ $check_name ];
	}
}
