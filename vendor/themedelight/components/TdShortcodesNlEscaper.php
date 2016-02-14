<?php
/**
 * Class allows escape unexpected <br /> & <p> tags between nested theme shortcodes.
 * For example we have following structute of the shortcodes:
 * <pre>
 * [table]
 * 	[tr]
 * 		[td]cell 1[td]
 * 		[td]cell 2[/td]
 * 	[/tr]
 * [/table]
 * </pre>
 *
 * So to prevent tags P or BR between table, tr, td tags we should register this structure via following call:
 * <pre>
 * $escaper = new TdShortcodesNlEscaper();
 * $escaper->registerNestedShortcodes('table','tr','td');
 *
 * //or alternative way:
 * $escaper->addRelation('table','tr');
 * $escaper->addRelation('tr','td');
 * </pre>
 *
 * @author    Themedelight
 * @package   Themedelight/Components
 * @version   1.0.0
 */

class TdShortcodesNlEscaper extends TdComponent
{
	/**
	 * Stores list of possible parent & child tags combinations.
	 * @see is_nested
	 * @var array
	 */
	protected $relation_variations = array();

	protected $delimiter = '|';

	/**
	 * Parts of the regexp.
	 * @var array
	 */
	protected $regPartsOpens = array();

	/**
	 * Parts of the regexp.
	 * @var array
	 */
	protected $regPartsCloses = array();

	/**
	 * Component init method
	 * @return [type] [description]
	 */
	public function init() {
		if ( parent::init() ) {
			add_filter( 'the_content', array( $this, 'removeWhitespaces' ), 2 );
			return true;
		}
		return false;
	}

	/**
	 * Registers relations between all arguments passed to the function.
	 *
	 * @example
	 * <pre>
	 * $nlEscaper->registerNestedShortcodes('table','tr','td');
	 * </pre>
	 * @return void
	 */
	public function registerNestedShortcodes() {
		$items = func_get_args();

		if ( count( $items ) < 2 ) {
			return;
		}
		$parent = array_shift( $items );
		foreach ( $items as $child ) {
			$this->pushRelation( $parent, $child );
			$parent = $child;
		}
	}

	/**
	 * Registers relation between parent and child shortcodes.
	 *
	 * @example
	 * <pre>
	 * $escaper->addRelation('table','tr');
	 * $escaper->addRelation('tr','td');
	 * </pre>
	 *
	 * @param string $parent name of the parent shortcode
	 * @param string $child  name of the child shortcode
	 * @return void
	 */
	public function addRelation($parent, $child) {
		$this->pushRelation( $parent, $child );
	}

	public function removeWhitespaces($content) {
		return preg_replace_callback( $this->getRegexp(),array( $this, '_parse_callback' ), $content );
	}

	public function pushRelation($parent, $child) {
		$this->relation_variations[] = $parent . $this->delimiter . $child;
		$this->relation_variations[] = '/' . $child . $this->delimiter . '/' . $parent;
		$this->relation_variations[] = '/' . $child . $this->delimiter . $child;

		$this->pushToRegexpParts( 'open', $parent );
		$this->pushToRegexpParts( 'open', '\/' . $child );

		$this->pushToRegexpParts( 'close', '\/' . $parent );
		$this->pushToRegexpParts( 'close', '\/?' . $child );
	}

	protected function getRegexp() {
		// $result = '`\[\/?(\w+)[^\]]*\](\s)+\[\/?(\w+)[^\]]*\]`';
		$attributesPattern = '[^\]]*';
		return '`\[('.join( '|', $this->regPartsOpens ).')'.$attributesPattern.'\](\s)+\[('.join( '|', $this->regPartsCloses ).')'.$attributesPattern.'\]`';
	}

	public function _parse_callback($res) {
		$fullText = $res[0];
		if ( $this->isNested( $res[1], $res[3] ) ) {
			return preg_replace( '`(\])\s+(\[)`', '$1$2', $fullText );
		}
		return $fullText;
	}

	protected function pushToRegexpParts($type, $regexp) {
		if ( 'open' == $type ) {
			$targetList = &$this->regPartsOpens;
		} else {
			$targetList = &$this->regPartsCloses;
		}
		if ( ! isset( $targetList[$regexp] ) ) {
			$targetList[$regexp] = $regexp;
		}
	}

	protected function isNested($tag1, $tag2) {
		return in_array( $tag1 . $this->delimiter . $tag2, $this->relation_variations );
	}
}
