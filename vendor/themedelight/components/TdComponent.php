<?php
/**
 * Component basic class.
 * Implements configuration functionality.
 *
 * @author    Themedelight
 * @package   Themedelight/Components
 * @version   1.0.0
 */

class TdComponent
{
	protected $inited;

	public function __construct(array $config = array()) {
		if ( $config ) {
			$this->setConfig( $config );
		}
		$this->init();
	}

	public function init() {
		if ( $this->inited ) {
			return false;
		}
		$this->inited = true;
		return true;
	}

	public function setConfig(array $config) {
		foreach ( $config as $option => $value ) {
			$this->$option = $value;
		}
	}
}
