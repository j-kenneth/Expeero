<?php
/**
 * Basic class for services creation (component that should be created only once per app).
 *
 * @author    Themedelight
 * @package   Themedelight/Components
 * @version   1.0.0
 */

abstract class TdService
{
	abstract public function getServiceId();

	private static $instances = array();

	private $inited = false;

	protected function __construct(array $config = array()) {
		if ( $config ) {
			$this->setConfig( $config );
		}
		$this->init();
	}

	private function __clone() {

	}

	/**
	 * @return TdService
	 */
	public static function getInstance($class = __CLASS__) {
		if ( ! isset( self::$instances[$class] ) ) {
			self::$instances[$class] = new $class();
		}

		return self::$instances[$class];
	}

	public function setConfig(array $config) {
		foreach ( $config as $option => $value ) {
			$this->$option = $value;
		}
	}

	/**
	 * Init method.
	 *
	 * @return void
	 */
	protected function init() {
		if ( $this->inited ) {
			return false;
		}
		$this->inited = true;

		do_action( 'td_service_init', $this, $this->getServiceId() );

		return true;
	}
}
