<?php
/**
 * Shortcodes register component used for shorcodes menu generation (for wp rich text editor).
 *
 * @author    Themedelight
 * @package   Themedelight/Components
 * @version   1.0.0
 */

class TdShortcodesRegister extends TdComponent
{
	protected $list = array();

	protected $menu = array();

	protected $titles = array();

	protected $isDisabled = false;

	public function setDisabledState($value) {
		$this->isDisabled = $value;
	}

	public function add($name, $menuPosition, array $attributesConfig = array()) {
		if ( $this->isDisabled ) {
			return $this;
		}

		$scTitle = '';
		if ( is_array( $name ) ) {
			$scName = array_shift( $name );
			if ( $name ) {
				$scTitle = array_shift( $name );
			}
		} else {
			$scName = $name;
		}

		$this->menu[$scName] = $menuPosition;

		if ( $attributesConfig ) {
			$this->list[$scName] = $attributesConfig;
		}

		if ( $scTitle ) {
			$this->titles[$scName] = $scTitle;
		}
		return $this;
	}

	public function getMenuConfig() {
		$list = array();

		foreach ( $this->menu as $shName => $fullPath ) {
			$parts = explode( '.',$fullPath );
			$cp = &$list;
			foreach ( $parts as $level ) {
				if ( ! isset( $cp[$level] ) ) {
					$cp[$level] = array();
				}
				$cp = &$cp[$level];
			}

			if ( ! empty( $cp ) ) {
				if ( is_string( $cp ) ) {
					$x = $cp;
					$cp = array();
					$cp[$this->getSchorcodeTitle( $x )] = $x;
					$cp[$this->getSchorcodeTitle( $shName )] = $shName;
				} else {
					$cp[$this->getSchorcodeTitle( $shName )] = $shName;
				}
			} else {
				$cp = $shName;
			}
		}
		return $list;
	}

	public function getDialogsConfig() {
		return $this->list;
	}

	public function getSchorcodeTitle($name) {
		if ( isset( $this->titles[$name] ) ) {
			return $this->titles[$name];
		} else {
			return ucfirst( str_replace( '_', ' ', $name ) );
		}
	}
}
