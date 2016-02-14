<?php
/**
 * Class contains methods/helper functions related to tours.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.2.2
 */

class AtTourHelper
{
	/**
	 * Map for woocommerce templates replacement functionality.
	 *
	 * @see filter_wc_template_rendering
	 * @var array
	 */
	public static $wcTemplatesMap = array(
		'single-product' => 'templates/tour/single',
		// 'content-single-product' => 'templates/tour/content',
		// 'content-product' => 'templates/tour/content',
	);

	public static function init() {
		if ( self::$wcTemplatesMap ) {
			add_action( 'adventure_tours_allow_wc_template_render', array( __CLASS__, 'filter_wc_template_rendering' ), 20 );
		}
	}

	/**
	 * Checks if current post is a product and has tour type.
	 *
	 * @param  mixed $product product id/instance.
	 * @return boolean
	 */
	public static function isTourProduct( $product = null ) {
		if ( ! $product ) {
			$product = wc_get_product();
		}
		if ( $product ) {
			$curProduct = is_string( $product ) ? wc_get_product( false ) : $product;
			if ( $curProduct && $curProduct->is_type( 'tour' ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Filter that called before any woocommerce template rendering.
	 * If filter returns false - rendering should be stopped, filter function should take care about rendering.
	 *
	 * @param  string $file full path to template file that should be rendered.
	 * @return string|false
	 */
	public static function beforeWCTemplateRender($file) {
		return apply_filters( 'adventure_tours_allow_wc_template_render', $file );
	}

	/**
	 * Filter that replaces current woocommerce template with template defined in settings.
	 *
	 * @see beforeWCTemplateRender
	 * @param  string $file full path to currently rendered template
	 * @return mixed
	 */
	public static function filter_wc_template_rendering($file) {
		if ( $file && self::isTourProduct() ) {
			$anotherTemplate = '';
			$map = self::$wcTemplatesMap;
			$baseName = basename( $file, '.php' );
			if ( isset( $map[$baseName] ) ) {
				wc_get_template_part( $map[$baseName] ); // get_template_part($map[$baseName]);
				return false;
			}
		}
		return $file;
	}

	/**
	 * Returns list of attributes available for tour posts.
	 *
	 * @param  boolean $withLists if set to true each element will contains list of values.
	 * @param  boolean $putLabelAsEmptyValue if set to true -
	 *                                       each list will contains label as empty element for each list.
	 * @return array
	 */
	public static function get_available_attributes($withLists = false, $putLabelAsEmptyValue = false) {
		$result = array();

		$taxonomies = get_object_taxonomies( 'product', 'objects' );
		if ( empty( $taxonomies ) ) {
			return $result;
		}

		foreach ( $taxonomies as $tax ) {
			$taxName = $tax->name;
			if ( 0 !== strpos( $taxName, 'pa_' ) ) {
				continue;
			}
			if ( $withLists ) {
				if ( $putLabelAsEmptyValue ) {
					$result[$taxName] = array(
						'' => wc_attribute_label( $tax->label ),
					);
				} else {
					$result[$taxName] = array();
				}
			} else {
				$result[$taxName] = wc_attribute_label( $tax->label );
			}
		}
		if ( $withLists && $result ) {
			foreach( $result as $term_name => $term_label ) {
				$values = get_terms( $term_name );
				foreach ( $values as $term ) {
					$result[$term->taxonomy][$term->slug] = wc_attribute_label( $term->name );
				}
			}
		}

		return $result;
	}

	/**
	 * Returns set of taxonomies/tour attributes that should be used as additional fields for tour search form.
	 *
	 * @param  boolean $only_allowed_in_settings if set to true only fields allwed in Tours > Search Form > Additional Fields option.
	 * @return array
	 */
	public static function get_search_form_fields( $only_allowed_in_settings = true ) {
		$result = array();
		$allowedList = adventure_tours_get_option( 'tours_search_form_attributes' );
		if ( $allowedList || ! $only_allowed_in_settings ) {
			$fullList = self::get_available_attributes( true, true );
			if ( ! $only_allowed_in_settings ) {
				$result = $fullList;
			} else {
				foreach ( $allowedList as $attributeName ) {
					if ( ! empty( $fullList[$attributeName] ) ) {
						$result[$attributeName] = $fullList[$attributeName];
					} elseif ( '__tour_categories_filter' == $attributeName ) {
						$result[$attributeName] = array();
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Returns field configs for tour search form.
	 *
	 * @param  boolean $only_allowed_in_settings if set to true only fields allwed in Tours > Search Form > Additional Fields option.
	 * @param  boolean $clear_empty_values
	 * @return array
	 */
	public static function get_search_form_fields_html( $only_allowed_in_settings = true, $clear_empty_values = false ) {
		$result = array();

		$form_taxonomies = self::get_search_form_fields( $only_allowed_in_settings );
		if ( ! $form_taxonomies ) {
			return $result;
		}

		$tour_tax_request = isset( $_REQUEST['tourtax'] ) ? $_REQUEST['tourtax'] : array();

		foreach ( $form_taxonomies as $name => $list ) {
			if ( '__tour_categories_filter' === $name ) {
				if ( ! adventure_tours_check( 'tour_category_taxonomy_exists' ) ) {
					continue;
				}

				$current_term_id = ! empty( $_REQUEST['tour_category'] ) ? $_REQUEST['tour_category'] : '';
				/*
				// For WP < 4.3
				$current_term_id = 0;
				$tour_cat_slug = ! empty( $_REQUEST['tour_category'] ) ? $_REQUEST['tour_category'] : '';
				if ( $tour_cat_slug ) {
					$cur_cat_term = get_term_by( 'slug', $tour_cat_slug, 'tour_category' );
					if ( $cur_cat_term ) {
						$current_term_id = $cur_cat_term->term_id;
					}
				}*/

				$use_parent_cat_name_as_title = true;
				$parent_term_id = adventure_tours_get_option( 'tours_search_form_start_category' );
				$show_all_title = esc_html__( 'Category', 'adventure-tours' );
				if ( $use_parent_cat_name_as_title && $parent_term_id ) {
					$parent_term_obj = get_term( $parent_term_id, 'tour_category' );
					if ( $parent_term_obj ) {
						$show_all_title = $parent_term_obj->name;
					}
				}

				$drop_down_html = wp_dropdown_categories( array(
					'show_option_all' => $clear_empty_values ? ' ' : $show_all_title,
					'hide_if_empty' => true,
					'taxonomy' => 'tour_category',
					'hierarchical' => true,
					'echo' => false,
					'name' => 'tour_category',
					'value_field' => 'slug',
					'hide_if_empty' => true,
					'class' => 'selectpicker',
					'show_count' => true,
					'selected' => $current_term_id,
					'child_of' => $parent_term_id,
				) );

				if ( $drop_down_html ) {
					// to replace value='0' with value='' - as options with empty string value are hidhlighted with placeholder color only
					$drop_down_html = preg_replace('`(\s+value=(?:\"|\'))0(\"|\')`', '$1$2', $drop_down_html);

					$icon_class = $parent_term_id ? AtTourHelper::get_tour_category_icon_class( $parent_term_id ) : '';
					$result[] = array(
						'icon' => $icon_class ? $icon_class : 'td-network',
						'html' => $drop_down_html,
						'label' => $show_all_title,
					);
				}
			} else {
				$selected_value = isset( $tour_tax_request[ $name ] ) ? $tour_tax_request[ $name ] : '';
				$attribute_title = wc_attribute_label( $name );

				$list_options = array();
				foreach ( $list as $value => $title ) {
					/*if ( $is_show_label ) {
						if ( $attribute_title == $title ) {
							continue;
						}
					}*/
					if ( $clear_empty_values && ! $value ) {
						$title = ' ';
					}

					$list_options[] = sprintf(
						'<option value="%s"%s>%s</option>',
						esc_attr( $value ),
						$selected_value == $value ? ' selected="selected"' : '',
						esc_html( $title )
					);
				}

				$result[] = array(
					'icon' => AtTourHelper::get_product_attribute_icon_class( $name ),
					'html' => '<select name="tourtax[' . esc_attr( $name ) . ']" class="selectpicker">' . join( '', $list_options ) . '</select>',
					'label' => $attribute_title,
				);
			}
		}

		return $result;
	}

	/**
	 * Returns modified tour attributes where each element contains information about attribute label,
	 * value and icon class.
	 *
	 * @param  WC_Product  $product               product for that attributes should be retrived.
	 * @param  boolean     $onlyAllowedInSettings if attributes should be filtered with values allowed in theme options.
	 * @return array
	 */
	public static function get_tour_details_attributes($product, $onlyAllowedInSettings = true) {
		$result = array();
		$list = $product->get_attributes();
		$allowedList = adventure_tours_get_option( 'tours_page_top_attributes' );
		if ( ! $list || ( $onlyAllowedInSettings && ! $allowedList ) ) {
			return $result;
		}

		foreach ( $list as $name => $attribute ) {
			$attrib_name = $attribute['name'];

			if ( empty( $attribute['is_visible'] ) || ( $attribute['is_taxonomy'] && ! taxonomy_exists( $attrib_name ) ) ) {
				continue;
			}

			if ( false === $onlyAllowedInSettings &&  in_array( $attrib_name, $allowedList ) ) {
				continue;
			}

			if ( $attribute['is_taxonomy'] ) {
				$values = wc_get_product_terms( $product->id, $attrib_name, array( 'fields' => 'names' ) );
				$text = apply_filters( 'woocommerce_attribute', wptexturize( implode( ', ', $values ) ), $attribute, $values );
			} else {
				// Convert pipes to commas and display values
				$values = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
				$text = apply_filters( 'woocommerce_attribute', wptexturize( implode( ', ', $values ) ), $attribute, $values );
			}

			$result[$attrib_name] = array(
				'name' => $attrib_name,
				'label' => wc_attribute_label( $attrib_name ),
				'values' => $values,
				'text' => $text,
				'icon_class' => self::get_product_attribute_icon_class( $attribute ),
			);
		}

		// We need reorder items according order in settings.
		if ( $onlyAllowedInSettings && $result ) {
			$orderedList = array();

			foreach ( $allowedList as $attribKey ) {
				if ( ! empty( $result[$attribKey] ) ) {
					$orderedList[$attribKey] = $result[$attribKey];
				}
			}

			return $orderedList;
		}

		return $result;
	}

	/**
	 * Retrives icon class related to the tour category term.
	 *
	 * @param  mixed $tour_category term object or term id.
	 * @return string
	 */
	public static function get_tour_category_icon_class( $tour_category ) {
		$term_id = is_scalar( $tour_category ) ? $tour_category : (
			isset( $tour_category->term_id ) ? $tour_category->term_id : ''
		);
		if ( $term_id > 0 ) {
			$storage = adventure_tours_di( 'tour_category_icons_storate' );
			if ( $storage && $storage->is_active() ) {
				return $storage->getData( $term_id );
			}
		}
		// return default tour category ison class
		return '';
	}

	/**
	 * Retrives thumbnail id related to the tour category term.
	 *
	 * @param  mixed $tour_category term object or term id.
	 * @return string
	 */
	public static function get_tour_category_thumbnail( $tour_category ) {
		$term_id = is_scalar( $tour_category ) ? $tour_category : (
			isset( $tour_category->term_id ) ? $tour_category->term_id : ''
		);
		if ( $term_id > 0 ) {
			$storage = adventure_tours_di( 'tour_category_images_storage' );
			if ( $storage && $storage->is_active() ) {
				return $storage->getData( $term_id );
			}
		}

		return null;
	}

	/**
	 * Return tour attribute icon class.
	 *
	 * @param  string $product_attribute
	 * @return string
	 */
	public static function get_product_attribute_icon_class( $product_attribute ) {
		$result = '';

		$icons_storage = adventure_tours_di( 'product_attribute_icons_storage' );
		if ( ! $icons_storage || ! $icons_storage->is_active() ) {
			return $result;
		}

		$name = is_string( $product_attribute ) ? $product_attribute : $product_attribute['name'];

		static $attrMap;
		if ( null == $attrMap ) {
			$attrMap = array();

			$paTaxonomies = wc_get_attribute_taxonomies();
			if ( $paTaxonomies ) {
				foreach ( $paTaxonomies as $taxInfo ) {
					$attrMap[ 'pa_' . $taxInfo->attribute_name ] = $taxInfo->attribute_id;
				}
			}
		}

		if ( isset( $attrMap[$name] ) ) {
			if ( $savedValue = $icons_storage->getData( $attrMap[$name] ) ) {
				$result = $savedValue;
			}
		}

		return $result;
	}

	/**
	 * Returns display mode value for tour archive page.
	 * If $tour_category_id has been specefied - category specific value, otherwise value will be taken from the theme options.
	 *
	 * @param  int $tour_category_id
	 * @return string                possible values are: 'products', 'subcategories', 'both'.
	 */
	public static function get_tour_archive_page_display_mode ( $tour_category_id = null ) {
		$result = 'default';

		if ( $tour_category_id > 0 ) {
			$cat_display_storage = adventure_tours_di( 'tour_category_display_type_storage' );
			if ( $cat_display_storage && $cat_display_storage->is_active() ) {
				$result = $cat_display_storage->getData( $tour_category_id );
			}
		}

		if ( 'default' == $result ) {
			$result = adventure_tours_get_option( 'tours_archive_display_mode' );
		}

		return !$result || 'default' == $result ? 'both' : $result;
	}

	public static function get_tour_base_rewrite_rule( $with_front = false, $reset_cache = true ) {
		static $base_url, $base_full, $is_front_page, $default_lang, $cur_lang;

		if ( null === $base_url || $reset_cache ) {
			$tours_page_id = adventure_tours_get_option( 'tours_page' );
			// if WPML in use we need ensure that we have page_id in default language
			if ( adventure_tours_check( 'is_wpml_in_use' ) && !is_admin() ) {
				if ( null === $cur_lang ) {
					$default_lang = apply_filters( 'wpml_default_language', '' );
					$cur_lang = defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : $cur_lang;
				}

				if ( $default_lang != $cur_lang ) {
					$page_id_in_default_lang = icl_object_id( $tours_page_id, 'page', true, $default_lang );
					if ( $page_id_in_default_lang ) {
						$tours_page_id = $page_id_in_default_lang;
					}
				}
			}
			if ( $tours_page_id && 'page' == get_option( 'show_on_front' ) && $tours_page_id == get_option( 'page_on_front' ) ) {
				$base_url = '';
				$is_front_page = true;
			} else {
				$base_url = $tours_page_id ? get_page_uri( $tours_page_id ) : 'tours';
				if ( $base_url ) {
					$base_url .= '/';
				}
			}
		}

		if ( $with_front ) {
			if ( null === $base_full || $reset_cache ) {
				$base_full = $base_url || $is_front_page ? $GLOBALS['wp_rewrite']->front . $base_url : '';
			}
			return $base_full;
		}
		return $base_url;
	}
}
