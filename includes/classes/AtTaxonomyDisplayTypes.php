<?php
/**
 * Component for 'Display Type' field generaion.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.2.4
 */

class AtTaxonomyDisplayTypes extends TdTaxonomyFieldManager
{
	public $selectOptions = array(
		'Default',
		'Products',
		'Subcategories',
	);

	public function init() {
		if ( $this->getSelectOptions() && $this->getStorage()->is_active() && parent::init() ) {
			return true;
		}
		return false;
	}

	/**
	 * HTML template for field "Add new taxonomy".
	 */
	public function hookAddFormInsertField() {
		$selectOptions = '';
		foreach ( $this->getSelectOptions() as $value => $label ) {
			$selectOptions .= '<option value="' . esc_attr( $value ) . '">' . esc_html( $label ) . '</option>';
		}

		echo '<div class="form-field">' .
			'<label style="cursor:auto;">' . esc_html( $this->getFieldLabel() ) . '</label>' .
			'<select name="' . esc_attr( $this->getPostVariableFieldData() ) . '" class="postform">' .
				$selectOptions .
			'</select>' .
		'</div>';
	}

	/**
	 * HTML template for field "Edit taxonomy".
	 *
	 * @param object $term
	 */
	public function hookEditFormInsertField( $term ) {
		$taxonomyDisplayType = $this->getTaxonomyData( $term->term_id );
		$selectOptions = '';
		foreach ( $this->getSelectOptions() as $value => $label ) {
			$selectOptions .= '<option ' . ( $value == $taxonomyDisplayType ? ' selected="selected"' : '' ) . ' value="' . esc_attr( $value ) . '">' . esc_html( $label ) . '</option>';
		}

		echo '<tr class="form-field">' .
			'<th scope="row">' .
				'<label for="theme_taxonomy_icons">' . esc_html( $this->getFieldLabel() ) . '</label>' .
			'</th>' .
			'<td>' .
				'<select name="' . esc_attr( $this->getPostVariableFieldData() ) . '" class="postform">' .
					$selectOptions .
				'</select>' .
			'</td>' .
		'</tr>';
	}

	/**
	 * HTML template for table column value where show list taxonomy.
	 *
	 * @param stirng $deprecated
	 * @param string $columnId
	 * @param int    $termId
	 */
	public function hookAddTableColumnValue( $deprecated, $columnName, $termId ) {
		if ( $this->getTableColumnId() == $columnName ) {
			$taxonomyData = $this->getTaxonomyData( $termId );
			if ( $taxonomyData ) {
				$selectOptions = $this->getSelectOptions();
				print $selectOptions[$taxonomyData];
			}
		}
	}

	public function getSelectOptions() {
		return $this->selectOptions;
	}
}
