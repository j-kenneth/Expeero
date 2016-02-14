<?php
/**
 * Theme bootrap file that defines classes loaders.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

$loaderPath = dirname( __FILE__ );
if (!defined('_THEME_VENDOR_PATH_')) {
	define('_THEME_VENDOR_PATH_', $loaderPath . '/../vendor/');
}

set_include_path(
	get_include_path() . 
	PATH_SEPARATOR . $loaderPath
);

if ( ! function_exists( 'adventure_tours_autoloader' ) ) {
	/**
	 * Vendor components loading function.
	 *
	 * @param  string $class class name that should be loaded.
	 * @return void
	 */
	function adventure_tours_autoloader( $class ) {
		static $map, $includesPath;
		if ( ! $map ) {
			$map = array(
				'lessc' => _THEME_VENDOR_PATH_ . 'lessphp/lessc.inc.php',
				'wp_bootstrap_navwalker' => _THEME_VENDOR_PATH_ . 'twittem/wp_bootstrap_navwalker.php',
				'JuiceContainer' => _THEME_VENDOR_PATH_ . 'juice/JuiceContainer.php',
				'tmhOAuth' => _THEME_VENDOR_PATH_ . 'tmhOAuth/tmhOAuth.php',
			);
			$includesPath = dirname(__FILE__);
		}

		if ( isset( $map[ $class ] ) ) {
			$fileName = $map[ $class ];
			if ( $fileName ) {
				require $fileName;
			}
		} elseif ( 0 === strpos( $class, 'At' ) ) {
			$themeClassFile = "{$includesPath}/classes/{$class}.php";
			if ( file_exists( $themeClassFile ) ) {
				require $themeClassFile;
			}
		}
	}

	spl_autoload_register( 'adventure_tours_autoloader' );
}

require _THEME_VENDOR_PATH_ . 'themedelight/bootstrap.php';

AtTourHelper::init();
