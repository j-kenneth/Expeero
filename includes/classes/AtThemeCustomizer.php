<?php
/**
 * Theme customize options.
 * Component that uses wordpress customization API to implement customization options that allows configurate theme visual presentation.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.9
 */

class AtThemeCustomizer extends TdComponent
{
	/**
	 * Set of fonts available for selection of the theme text elements.
	 *
	 * @var array
	 */
	public $font_set = array();

	/**
	 * @var WP_Customize_Manager
	 */
	protected $wp_customizer;

	public $font_default_settings = array(
		'main_font' => array(
			'family' => 'Oxygen',
			'weight' => 'regular',
			'style' => 'normal',
		),
		'heading_font' => array(
			'family' => 'Oxygen',
			'weight' => 700,
			'style' => 'normal',
		),
		'subheading_font' => array(
			'family' => 'Kaushan Script',
			'weight' => 'regular',
			'style' => 'normal',
		),
		'alternative_text_font' => array(
			'family' => 'Oxygen',
			'weight' => 700,
			'style' => 'normal',
		),
	);

	/**
	 * Init method.
	 * @return void
	 */
	public function init() {
		if ( parent::init() ) {
			add_action( 'customize_register', array( $this, 'hook_onCustomizeRegister' ) );

			if ( is_admin() ) {
				add_action( 'customize_controls_print_scripts', array( $this, 'action_customize_controls_print_scripts' ) );
				add_action( 'wp_ajax_customizer_reset', array( $this, 'action_ajax_reset' ) );

				// add_action( 'customize_preview_init', array( $this, 'hook_registerPreviewAssets' ) );
				// add_action( 'customize_controls_enqueue_scripts', array( $this, 'hook_registerManageAssets' ) );
			}

			return true;
		}
		return false;
	}

	public function getCustomazerSettings() {

		return array(
			'typography' => array(
				'config' => array(
					'title' => esc_html__( 'Typography', 'adventure-tours' ),
				),
				'fields' => array(
					'main_font' => array(
						'label' => esc_html__( 'Main Font', 'adventure-tours' ),
						'field_type' => 'themedelight_font',
						'default' => $this->getFontSettingDefaults( 'main_font' ),
					),
					'main_font_size' => array(
						'label' => esc_html__( 'Size', 'adventure-tours' ),
						'as_subfield' => true,
						'field_type' => 'themedelight_font_size',
						'default' => array(
							'size' => '15',
							'unit' => 'px',
						),
					),
					'main_font_color' => array(
						'label' => esc_html__( 'Main Font Color', 'adventure-tours' ),
						'field_type' => 'color',
						'default' => '#333333',
					),
					'subheading_font' => array(
						'label' => esc_html__( 'Subheading Font', 'adventure-tours' ),
						'field_type' => 'themedelight_font',
						'default' => $this->getFontSettingDefaults( 'subheading_font' ),
					),
					'alternative_text_font' => array(
						'label' => esc_html__( 'Alternative Text Font', 'adventure-tours' ),
						'field_type' => 'themedelight_font',
						'default' => $this->getFontSettingDefaults( 'alternative_text_font' ),
					),
					'heading_font' => array(
						'label' => esc_html__( 'Heading Font', 'adventure-tours' ),
						'field_type' => 'themedelight_font',
						'default' => $this->getFontSettingDefaults( 'heading_font' ),
					),
					'links_color' => array(
						'label' => esc_html__( 'Link Color', 'adventure-tours' ),
						'field_type' => 'color',
						'default' => '#4090e5',
					),
				),
			), // end of Typography
			'general' => array(
				'config' => array(
					'title' => esc_html__( 'General Colors', 'adventure-tours' ),
				),
				'fields' => array(
					'main_color' => array(
						'label' => esc_html__( 'Main color', 'adventure-tours' ),
						'field_type' => 'color',
						'default' => '#47a0ff',
					),
					'accent_color1' => array(
						'label' => esc_html__( 'Accent color 1', 'adventure-tours' ),
						'field_type' => 'color',
						'default' => '#ff47a0',
					),
					'accent_color2' => array(
						'label' => esc_html__( 'Accent color 2', 'adventure-tours' ),
						'field_type' => 'color',
						'default' => '#01cb68',
					),
					'forms_bg' => array(
						'label' => esc_html__( 'Form background', 'adventure-tours' ),
						'field_type' => 'color',
						'default' => '#47a0ff',
					),
					'forms_button_bg' => array(
						'label' => esc_html__( 'Form button background', 'adventure-tours' ),
						'field_type' => 'color',
						'default' => '#006fe6',
					),
				),
			),// end of General
			'header' => array(
				'config' => array(
					'title' => esc_html__( 'Header Colors', 'adventure-tours' ),
				),
				'fields' => array(
					'header_bg' => array(
						'label' => esc_html__( 'Background color', 'adventure-tours' ),
						'field_type' => 'color',
						'default' => '#334960',
					),
					'header_text_color' => array(
						'label' => esc_html__( 'Text color', 'adventure-tours' ),
						'field_type' => 'color',
						'default' => '#ffffff',
					),
				),
			),// end of Header
			'footer' => array(
				'config' => array(
					'title' => esc_html__( 'Footer Colors', 'adventure-tours' ),
				),
				'fields' => array(
					'footer_bg' => array(
						'label' => esc_html__( 'Background color', 'adventure-tours' ),
						'field_type' => 'color',
						'default' => '#334960',
					),
					'footer_heading_color' => array(
						'label' => esc_html__( 'Heading color', 'adventure-tours' ),
						'field_type' => 'color',
						'default' => '#ffffff',
					),
					'footer_text_color' => array(
						'label' => esc_html__( 'Text color', 'adventure-tours' ),
						'field_type' => 'color',
						'default' => '#959da7',
					),
					'footer_links_color' => array(
						'label' => esc_html__( 'Link color', 'adventure-tours' ),
						'field_type' => 'color',
						'default' => '#ffffff',
					),
				),
			),// end of Footer
		);
	}

	/**
	 * @return assoc
	 */
	public function getFontSet() {
		if ( ! $this->font_set ) {
			return adventure_tours_di( 'app' )->getFontSet();
		}
		return $this->font_set;
	}

	/**
	 * Returns list of options that contain font family settings used during the css generation process.
	 *
	 * @return array
	 */
	public function getFontFamilySettings() {
		return array_keys( $this->font_default_settings );
	}

	/**
	 * Returns default value for option with font family settings.
	 * @param  string $optionName
	 * @return assoc
	 */
	public function getFontSettingDefaults( $optionName ) {
		return isset($this->font_default_settings[$optionName]) ? $this->font_default_settings[$optionName] : array(
			'family' => 'Oxygen',
			'weight' => 'regular',
			'style' => 'normal',
		);
	}

	public function hook_onCustomizeRegister($customizer) {
		$this->wp_customizer = $customizer;

		$fontSet = $this->getFontSet();

		$sectionsConfig = $this->getCustomazerSettings();

		foreach ( $sectionsConfig as $sectionKey => $sectionOptions ) {
			$sectionConfig = ! empty( $sectionOptions['config'] ) ? $sectionOptions['config'] : array();
			$sectionFields = ! empty( $sectionOptions['fields'] ) ? $sectionOptions['fields'] : array();

			if ( ! $sectionFields ) {
				continue;
			}

			$customizer->add_section( $sectionKey, $sectionConfig );

			foreach ( $sectionFields as $fieldKey => $fieldOptions ) {
				if ( isset( $fieldOptions['field_type'] ) ) {
					$fieldType = $fieldOptions['field_type'];
					unset( $fieldOptions['field_type'] );
				} else {
					$fieldType = 'text';
				}

				if ( isset( $fieldOptions['default'] ) ) {
					$defaultValue = $fieldOptions['default'];
					unset( $fieldOptions['default'] );
				} else {
					$defaultValue = '';
				}

				$customizer->add_setting( $fieldKey, array( 'default' => $defaultValue ) );

				$fieldOptions['section'] = $sectionKey;
				switch ( $fieldType ) {
				case 'themedelight_font':
					$fieldOptions['font_set'] = $fontSet;

					$customizer->add_control(
						new TdCustomizeFontControl( $customizer, $fieldKey, $fieldOptions )
					);
					break;

				case 'themedelight_font_size':
					$customizer->add_control(
						new TdCustomizeFontSizeControl( $customizer, $fieldKey, $fieldOptions )
					);
					break;

				case 'themedelight_color':
					$customizer->add_control(
						new TdCustomizeColorControl( $customizer, $fieldKey, $fieldOptions )
					);
					break;

				case 'color':
					$customizer->add_control(
						new WP_Customize_Color_Control( $customizer, $fieldKey, $fieldOptions )
					);
					break;

				default:
					$customizer->add_control( $fieldKey, $fieldOptions );
					break;
				}
			}
		}
	}

	public function action_ajax_reset() {
		if ( ! check_ajax_referer( 'customizer-reset', 'nonce', false ) ) {
			wp_send_json_error( 'invalid_nonce' );
		}

		if ( $this->wp_customizer ) {
			$settings = $this->wp_customizer->settings();
			foreach ( $settings as $setting ) {
				if ( 'theme_mod' == $setting->type ) {
					remove_theme_mod( $setting->id );
				}
			}
		}

		wp_send_json_success();
	}

	public function action_customize_controls_print_scripts(){
		wp_enqueue_script( 'theme-customizer', PARENT_URL . '/assets/td/js/ThemeCustomizer.js', array( 'jquery' ), '20150704' );
		wp_localize_script( 'theme-customizer', 'ThemeCustomizerConfig', array(
			'resetBtn' => array(
				'text'   => esc_html__( 'Reset', 'adventure-tours' ),
				'confirm' => esc_html__( 'This will reset all customizations made via customizer to this theme! Are you sure?', 'adventure-tours' ),
				'nonce'   => wp_create_nonce( 'customizer-reset' ),
			),
		) );
	}

	/*public function hook_registerPreviewAssets()
	{
		wp_enqueue_script(
			'personal-brand-themecustomizer-preview',
			PARENT_URL . '/assets/td/js/ThemeCustomizerPreview.js',
			array( 'jquery', 'customize-preview' ),
			time(),
			true
		);
	}

	public function hook_registerManageAssets()
	{
		$config = array(
			'adminAjaxUrl' => admin_url('admin_ajax.php')
		);
		wp_localize_script( 'personal-brand-themecustomizer-preview', 'ThemeCustomizerConfig', $config );
	}*/
}
