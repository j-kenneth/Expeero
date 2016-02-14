<?php
/**
 * Special price rules processing service.
 * Makes price recalculation based on defined price rule.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.2.4
 */

class AtSpecialPriceRule extends TdComponent
{
	const TYPE_CONST = 'percent';

	const TYPE_ADD = 'add';

	const TYPE_EXACT = 'exact';

	public $min_valid_price = 0;

	public $type_matches = array(
		'percent' => '`^\d+\%$`',
		'add' => '`^(\+|\-)\d+$`',
		'exact' => '`^\d+$`',
	);

	public function is_valid_rule( $rule ) {
		return $this->get_rule_type( $rule ) ? true : false;
	}

	public function get_rule_type( $rule ) {
		if ( $rule ) {
			$patters = $this->type_matches;
			foreach ($patters as $type => $pattern) {
				if ( preg_match( $pattern, $rule ) ) {
					return $type;
				}
			}
		}
		return null;
	}

	public function process_rule( $rule, $price ) {
		$type = $rule ? $this->get_rule_type( $rule ) : null;
		if ( $type ) {
			return $this->calculate_price_by_rule_type( $rule, $type, $price );
		}

		return $price;
	}

	protected function calculate_price_by_rule_type( $rule, $type, $price ) {
		$result = $price;
		switch ( $type ) {
		case self::TYPE_CONST:
			$result = (string) ( floatval( $rule ) / 100 * $result );
			break;
		case self::TYPE_ADD:
			$result += floatval( $rule );
			$result = (string) $result;
			break;
		case self::TYPE_EXACT:
			$result = $rule;
			break;
		}

		if ( is_numeric( $this->min_valid_price ) && $result < $this->min_valid_price ) {
			return $this->min_valid_price;
		}

		return $result;
	}
}
