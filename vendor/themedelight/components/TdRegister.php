<?php
/**
 * Register component. Used for sharing some values through components/services.
 *
 * @author    Themedelight
 * @package   Themedelight/Components
 * @version   1.0.0
 */

class TdRegister extends TdComponent
{
	/**
	 * Registers storage property.
	 * @var array
	 */
	protected $data = array(
		// 'key' => 'value'
	);

	protected $stateHistory = array(
		// 'key' => array()
	);

	/**
	 * Saves value into register.
	 * @param string $name  register name
	 * @param mixed  $value
	 */
	public function setVar($name, $value) {
		$this->data[$name] = $value;
	}

	public function setVarIfEmpty($name, $value) {
		if ( empty( $this->data[$name] ) ) {
			$this->setVar( $name, $value );
		}
	}

	public function pushState($name, $value) {
		if ( ! isset( $this->stateHistory[$name] ) ) {
			$this->stateHistory[$name] = array();
		}
		$this->stateHistory[$name][] = $this->getVar( $name );
		$this->setVar( $name, $value );
	}

	public function popState($name) {
		if ( ! empty( $this->stateHistory[$name] ) ) {
			$this->setVar( $name, array_pop( $this->stateHistory[$name] ) );
		}
	}

	/**
	 * Appends value into register key.
	 *
	 * @param  string $name  register name
	 * @param  mixed  $value
	 * @return void
	 */
	public function pushVar($name, $value) {
		if ( ! isset( $this->data[$name] ) ) {
			$this->data[$name] = array();
		} elseif ( ! is_array( $this->data[$name] ) ) {
			$this->data[$name] = array( $this->data[$name] );
		}
		$this->data[$name][] = $value;
	}

	/**
	 * Returns value stored in register.
	 *
	 * @param  string $name
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function getVar($name, $default = null) {

		if ( isset( $this->data[$name] ) ) {
			return $this->data[$name];
		}

		return $default;
	}
}
