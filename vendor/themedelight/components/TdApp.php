<?php
/**
 * Application component class.
 *
 * @author    Themedelight
 * @package   Themedelight/Components
 * @version   1.0.0
 */

class TdApp extends TdComponent
{
	/**
	 * Analog for the get_template_part.
	 * Allows render view with possibility to passing some params for rendering.
	 *
	 * @param  string  $templateName    view name.
	 * @param  string  $templatePostfix optional postfix.
	 * @param  array   $data            assoc array with variables that should be passed to view.
	 * @param  boolean $return          if result should be returned instead of outputting.
	 * @return string
	 */
	public function renderTemplatePart($templateName, $templatePostfix = '', array $data = array(), $return = false) {
		static $__rfCache;
		if ( null === $__rfCache ) {
			$__rfCache = array();
		}
		$__cacheKey = $templateName . $templatePostfix;
		if ( isset( $__rfCache[ $__cacheKey ] ) ) {
			$__viewFilePath = $__rfCache[ $__cacheKey ];
		} else {
			$__templateVariations = array();
			if ( $templatePostfix ) {
				$__templateVariations[] = $templateName . '-' . $templatePostfix . '.php';
			}
			$__templateVariations[] = $templateName . '.php';
			$__rfCache[ $__cacheKey ] = $__viewFilePath = locate_template( $__templateVariations );
		}

		if ( ! $__viewFilePath ) {
			return '';
		}

		if ( $data ) {
			extract( $data );
		}

		$__rfData = $data;
		$__rfReturn = $return;

		unset( $templateName );
		unset( $templatePostfix );
		unset( $data );
		unset( $return );

		if ( $__rfData ) {
			extract( $__rfData );
		}

		if ( $__rfReturn ) {
			ob_start();
			include $__viewFilePath;
			return ob_get_clean();
		} else {
			include $__viewFilePath;
		}
	}
}
