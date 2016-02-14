<?php
/**
 * Class contains methods/helper functions related to WPML plugin integration.
 * Translates theme option settings in the admin's area.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.2.1
 */

class AtWPMLIntegrationHelper extends TdComponent
{
	/**
	 * Option name that store theme option settings.
	 *
	 * @var string
	 */
	protected $option_name = '';

	/**
	 * Keys from theme options that should be translated.
	 *
	 * @var array
	 */
	protected $settings_for_translation = array();

	/**
	 * Keys from theme options that contains page ids and should be translated.
	 *
	 * @var array
	 */
	protected $page_id_settings_for_translation = array();

	/**
	 * Get option filter call counter.
	 *
	 * @var integer
	 */
	private $get_filter_call_index = 0;

	/**
	 * Init function.
	 *
	 * @return void
	 */
	public function init() {
		if ( ! parent::init() ) {
			return false;
		}

		if ( is_admin() ) {
			$this->_initSettingsTranslation();
		} else {
			add_filter( 'icl_ls_languages', array( $this, 'filter_icl_ls_languages_fix_tours_link' ), 20 );
		}

		return true;
	}

	/**
	 * Fixes links to the tours archive page. Filter used by WPML plugin to disaply list of trnslated pages.
	 *
	 * @param  assoc $languages
	 * @return assoc
	 */
	public function filter_icl_ls_languages_fix_tours_link( $languages ) {
		static $first_call = true, $tour_page_id;
		if ( $first_call ) {
			// checking if current page is tours page, if no - removing this filter
			// need improve
			$first_call = false;
			if ( ! adventure_tours_check( 'is_tour_search' ) ) {
				remove_filter( 'icl_ls_languages', array( $this, 'filter_icl_ls_languages_fix_tours_link' ), 20 );
				return $languages;
			}
		}

		if ( null === $tour_page_id ) {
			$tour_page_id = adventure_tours_get_option( 'tours_page', '' );
		}

		global $sitepress;
		foreach ($languages as $code => $language) {
			// for WPML version prior 3.3.0
			// $languages[$code]['url'] = get_permalink( apply_filters( 'translate_object_id', $tour_page_id, 'page', true, $language['language_code'] ) );

			// for WPML version > 3.3.0
			$sitepress->switch_lang($language['language_code']);
			$languages[$code]['url'] = get_permalink( apply_filters( 'translate_object_id', $tour_page_id, 'page', true, $language['language_code']) );
			$sitepress->switch_lang();
		}

		return $languages;
	}

	/**
	 * Checks if instance has any option keys for translation.
	 *
	 * @return boolean
	 */
	protected function hasSettingsForTranslation() {
		return $this->settings_for_translation || $this->page_id_settings_for_translation;
	}

	/**
	 * Init option keys translations.
	 *
	 * @return boolean
	 */
	protected function _initSettingsTranslation() {
		if ( ! $this->option_name || ! $this->hasSettingsForTranslation() ) {
			return false;
		}

		// adding filters only for case when this is not default language
		if ( $this->getDefaultLang() != $this->getLang() ) {
			add_filter( 'option_' . $this->option_name, array( $this, 'filter_option_get' ) );
		}
		add_filter( 'pre_update_option_' . $this->option_name, array( $this, 'filter_option_update' ), 1, 2 );

		return true;
	}

	/**
	 * Filter that implements theme options translation in the admin's area during loading them from DB.
	 *
	 * @param  assoc $option_value option value loaded from DB.
	 * @return assoc
	 */
	public function filter_option_get( $option_value ) {
		// to allow call only once ( for init settings loading), improvements are required
		$this->get_filter_call_index++;
		if ( $this->get_filter_call_index > 1 ) {
			return $option_value;
		}

		$page_settings = $this->page_id_settings_for_translation;
		if ( $page_settings ) {
			foreach ( $page_settings as $setting_key ) {
				$new_value = ! empty( $option_value[ $setting_key ] ) ? $option_value[ $setting_key ] : null;
				if ( $new_value ) {
					$translated_value = apply_filters( 'translate_object_id', $new_value, 'page', true );
					if ( $translated_value && $translated_value != $new_value ) {
						$option_value[ $setting_key ] = $translated_value;
					}
				}
			}
		}

		$settings_list = $this->settings_for_translation;
		if ( $settings_list ) {
			$wpml_context = $this->getWpmlContext();
			$current_language = $this->getLang();

			foreach ( $settings_list as $setting_key ) {
				$value = isset( $option_value[ $setting_key ] ) ? $option_value[ $setting_key ] : '';

				$option_value[ $setting_key ] = apply_filters( 'wpml_translate_single_string',
					$value,
					$wpml_context,
					$this->getWpmlStringName( $setting_key ),
					$current_language
				);
			}
		}

		return $option_value;
	}

	/**
	 * Filter that implements theme options translation to default language before saving them to DB.
	 *
	 * @param  assoc $option_value
	 * @param  assoc $old_option_value
	 * @return assoc
	 */
	public function filter_option_update( $option_value, $old_option_value ) {
		$default_language = $this->getDefaultLang();
		$current_language = $this->getLang();

		$page_settings = $this->page_id_settings_for_translation;
		if ( $page_settings ) {
			$languages_list = apply_filters( 'wpml_active_languages', null, '' );
			foreach ( $page_settings as $setting_key ) {
				$new_value = ! empty( $option_value[ $setting_key ] ) ? $option_value[ $setting_key ] : null;
				// $old_value = ! empty( $old_option_value[ $setting_key ] ) ? $old_option_value[ $setting_key ] : null;

				if ( $new_value ) {
					foreach ($languages_list as $ln_code => $ln_details ) {
						//if ( $default_language != $ln_code ) {
						$tr_value = apply_filters( 'wpml_object_id', $new_value, 'page', true, $ln_code );
						icl_update_string_translation( $this->getWpmlStringName( $setting_key ), $ln_code, $tr_value, ICL_TM_COMPLETE, $translator_id = null, $rec_level = 0 );
					}

					//icl_update_string_translation( $this->getWpmlStringName( $setting_key ), $current_language, $new_value, ICL_TM_COMPLETE, $translator_id = null, $rec_level = 0 );
					$option_value[ $setting_key ] = apply_filters( 'wpml_object_id', $new_value, 'page', true, $default_language );
				}
			}
		}

		if ( $this->getDefaultLang() != $this->getLang() ) {
			$settings_list = $this->settings_for_translation;
			if ( $settings_list ) {
				foreach ( $settings_list as $setting_key ) {
					$value = isset( $option_value[ $setting_key ] ) ? $option_value[ $setting_key ] : '';

					icl_update_string_translation( $this->getWpmlStringName( $setting_key ), $current_language, $value, ICL_TM_COMPLETE, $translator_id = null, $rec_level = 0 );

					$option_value[ $setting_key ] = $old_option_value[ $setting_key ];
				}
			}
		}

		$option_value['_ts'] = time();

		return $option_value;
	}

	/**
	 * Returns WPML context value for current option name.
	 *
	 * @return string
	 */
	public function getWpmlContext() {
		return "admin_texts_{$this->option_name}";
	}

	/**
	 * Generates WPML string name attribute for specefied option key.
	 *
	 * @param  string $setting_key
	 * @return string
	 */
	public function getWpmlStringName( $setting_key ) {
		return "[{$this->option_name}]{$setting_key}";
	}

	/**
	 * Returns default language code.
	 *
	 * @return string
	 */
	protected function getDefaultLang() {
		static $def_lang;
		if ( null === $def_lang ) {
			$def_lang = apply_filters( 'wpml_default_language', '' );
		}
		return $def_lang;
	}

	/**
	 * Returns current language code.
	 *
	 * @return string
	 */
	protected function getLang() {
		return ICL_LANGUAGE_CODE;
	}
}
