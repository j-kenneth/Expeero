<?php
/**
 * Class adapter to allow use some protected methods from lessc class.
 * Requires lessphp/lessc.inc.php package.
 *
 * @author    Themedelight
 * @package   Themedelight/Components
 * @version   1.0.0
 */

class TdLesscPlugin extends lessc
{
	public function inject(lessc $instance) {
		$instance->registerFunction(
			'hsvsaturation', array( $this,'function_hsvsaturation' )
		);
	}

	public function convert_color_toHSL($color) {
		return $this->toHSL( $this->coerceColor( $color ) );
	}

	public static function getInstance() {
		static $instance;
		if ( ! $instance ) {
			$instance = new self();
		}
		return $instance;
	}

	public static function function_hsvsaturation($color) {
		$hsv = self::getInstance()->convert_color_toHSL( $color );
		return round( $hsv[2] * 100 ) . '%';
	}
}
