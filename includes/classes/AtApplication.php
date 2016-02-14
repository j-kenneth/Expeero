<?php
/**
 * Main theme component that contains different core functions related to theme.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

class AtApplication extends TdApp
{
	public $bodyClasses = array();

	protected $_bodyClassFilterSet = false;

	/* body classes management */
	/**
	 * Adds specefined class to body.
	 * 
	 * @param string $class
	 * @return  Theme
	 */
	public function addBodyClass($class)
	{
		$this->bodyClasses[] = $class;
		if (!$this->_bodyClassFilterSet) {
			add_filter('body_class',array($this, 'bodyClassFilter'));
			$this->_bodyClassFilterSet = true;
		}
		return $this;
	}

	/**
	 * Filter for wp 'body_class' function.
	 * @param  array $classes
	 * @return array
	 */
	public function bodyClassFilter($classes)
	{
		if ($this->bodyClasses) {
			foreach ($this->bodyClasses as $class) {
				$classes[] = $class;
			}
		}
		return $classes;
	}
	/* end body classes management */

	/* custom css generation */
	public function generateCustomCss($source, array $themeMods, $saveAs = true)
	{
		$result = array();

		//fonts including [start]
		$fontOptions = array();
		// filtergin all options that ends with "_font", this settings should contain font family settings
		foreach ($themeMods as $name => $modValue) {
			if (preg_match('`\_font$`', $name)) {
				$fontOptions[$name] = $modValue;
			}
		}
		if ($fontOptions) {
			$fontManager = new TdFontsManager(array(
				'font_set' => $this->getFontSet(),
			));

			if ($fontDefinitions = $fontManager->generateDefinitions($fontOptions)) {
				foreach ($fontDefinitions as $key => $value) {
					$result['theme-font-' . $key] = $value;
				}
			}
		}
		//fonts including [end]

		$generator = new TdLessProcessor(array(
			'lessFile' => $source,
			'themeBaseDir' => PARENT_DIR,
			'themeBaseUrl' => PARENT_URL,
			'saveAs' => $saveAs,
		));
		if ($generatedElements = $generator->generateCss($themeMods)) {
			$result = $result ? array_merge($result, $generatedElements) : $generatedElements;
		}
		return $result;
	}

	public function getStyleOptions($customizeMode = false)
	{
		//getting all theme mods
		$result = $themeMods = get_theme_mods();
		if ($customizeMode) {
			$updatedOptions = isset($_POST['customized']) ? json_decode( wp_unslash( $_POST['customized'] ), true ) : null;
			if ($updatedOptions) {
				foreach ($updatedOptions as $optionName => $value) {
					$result[$optionName] = get_theme_mod($optionName);
				}
			}
		}

		$theme_cusomizer = adventure_tours_di( 'theme_customizer' );
		$font_options = $theme_cusomizer->getFontFamilySettings();
		if ( $font_options ) {
			foreach ($font_options as $option_name ) {
				if ( ! isset( $result[$option_name] ) ) {
					$result[$option_name] = $theme_cusomizer->getFontSettingDefaults( $option_name );
				}
			}
		}

		return $result;
	}

	public function getFontSet()
	{
		static $includeCache = null;

		$fs = adventure_tours_di( 'register' )->getVar('font_set', null);
		if (!$fs) {
			if (null == $includeCache) {
				$includeCache = require PARENT_DIR . '/includes/data/font-set.php';
			}
			return $includeCache;
		}
		return $fs;
	}
	/* end custom css generation */
}
