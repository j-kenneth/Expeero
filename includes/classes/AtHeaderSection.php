<?php
/**
 * Component for handling page header section settings.
 * Require vaffpress framework.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.3.1
 */

class AtHeaderSection extends TdComponent
{
	public $page_meta_key = 'header_section_meta';

	private $mode_none = 'hide';

	public $title_separator = '';

	public $use_wp_title_function = false;

	public function get_section_meta() {
		$section_meta = array();

		$is_single = is_singular();

		$section_post_id = $is_single ? get_the_ID() : $this->get_section_id_for_archive_page();
		if ( $section_post_id ) {
			$section_meta = $this->get_section_meta_by_post_id( $section_post_id );
		}

		if ( ! $is_single && empty( $section_meta ) ) {
			$default_image_url = adventure_tours_get_option( 'banner_default_image' );
			$section_meta['section_mode'] = $default_image_url ? 'banner' : $this->mode_none;
			$section_meta['banner_image'] = $default_image_url;
			$section_meta['banner_subtitle'] = adventure_tours_get_option( 'banner_default_subtitle' );
			$section_meta['is_banner_image_parallax'] = adventure_tours_get_option( 'is_banner_default_image_parallax' );
			$section_meta['banner_image_repeat'] = adventure_tours_get_option( 'banner_default_image_repeat' );
			$section_meta['banner_mask'] = adventure_tours_get_option( 'banner_default_mask' );
		}

		$section_meta['title'] = $this->get_title();
		return $section_meta;
	}

	public function get_title() {
		$separator = $this->title_separator;
		$use_wp_title = $this->use_wp_title_function;

		// Disabling 'title-tag' feature.
		$activate_title_tag_back = false;
		if ( $use_wp_title && get_theme_support( 'title-tag' ) ) {
			remove_theme_support( 'title-tag' );
			$activate_title_tag_back = true;
		}

		$q = $GLOBALS['wp_query'];
		if ( $q->get( 'wc_query' ) && function_exists( 'woocommerce_page_title' ) ) {
			if ( $separator ) { 
				$separator = ''; 
			}
			$title = woocommerce_page_title( false );
		} else {
			$is_home = is_home();
			$is_front_page = is_front_page();
			if ( $is_home || $is_front_page ) {
				if ( $is_home && $is_front_page ) {
					$title = get_bloginfo( 'name' );
				} elseif ( $is_home ) {
					$title = get_the_title( get_option( 'page_for_posts' ) );
				} elseif ( $is_front_page ) {
					$title = get_the_title( get_option( 'page_on_front' ) );
				}
			} else {
				if ( $use_wp_title ) {
					$title = wp_title( $separator, false );
				} else {
					$title = is_singular() ? get_the_title( get_queried_object() ) : strip_tags( get_the_archive_title() );
				}
			}
		}

		// Restoring 'title-tag' feature.
		if ( $activate_title_tag_back ) {
			// add_theme_support( 'title-tag' );
			$GLOBALS['_wp_theme_features']['title-tag'] = true;
		}

		if ( $title ) {
			if ( $separator ) {
				$title = substr( $title, strlen( $separator ) + 1 );
			}
			$title = trim( $title );
		}

		return $title;
	}

	protected function get_section_id_for_archive_page() {
		$result = 0;
		if ( adventure_tours_check( 'is_tour_search' ) ) {
			// static page for tours
			$result = adventure_tours_get_option( 'tours_page' );
		} elseif ( is_home() ) {
			$result = get_option( 'page_for_posts' );
		} elseif ( is_post_type_archive('product') ) {
			$result = wc_get_page_id( 'shop' );
		} elseif ( is_tax( 'tour_category' ) ) {
			$tour_category = get_queried_object();
			if ( $tour_category && isset( $tour_category->term_id ) ) {
				$storage = adventure_tours_di( 'tour_category_header_sections_storage' );
				if ( $storage && $storage->is_active() ) {
					$result = $storage->getData( $tour_category->term_id );
				}
			}
		}
		return $result;
	}

	public function get_section_meta_by_post_id( $post_id, $max_depth = 5 ) {
		$section_meta = array();

		if ( '-1' === $post_id ) {
			$section_meta['section_mode'] = $this->mode_none;
		} else {
			$metaObject = $post_id > 0 && $this->page_meta_key ? vp_metabox( $this->page_meta_key, null, $post_id ) : null;
			if ( $metaObject && $metaObject->meta ) {
				$section_meta = $metaObject->meta;

				if ( $section_meta && ! empty( $section_meta['section_mode'] ) && 'from_list' == $section_meta['section_mode'] ) {
					if ( $max_depth > 0 && ! empty( $section_meta['header_section_id'] ) ) {
						return $this->get_section_meta_by_post_id( $section_meta['header_section_id'], $max_depth - 1 );
					} else {
						$section_meta = array();
					}
				}
			}
		}

		return $section_meta;
	}
}
