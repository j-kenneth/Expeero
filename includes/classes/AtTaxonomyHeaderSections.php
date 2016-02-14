<?php
/**
 * Component for header section selection field generation.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.2.4
 */

class AtTaxonomyHeaderSections extends TdTaxonomyFieldManager
{
	public function init() {
		if ( $this->getStorage()->is_active() && parent::init() ) {
			return true;
		}
		return false;
	}

	/**
	 * HTML template for field "Add new taxonomy".
	 */
	public function hookAddFormInsertField() {
		$options = $this->getOptions();
		$selectOptions = '';
		foreach ( $options as $option ) {
			$selectOptions .= '<option value="' . esc_attr( $option['value'] ) . '">' . esc_html( $option['label'] ) . '</option>';
		}

		echo '<div class="form-field">' .
			'<label style="cursor:auto;">' . esc_html( $this->getFieldLabel() ) . '</label>' .
			'<select name="' . esc_attr( $this->getPostVariableFieldData() ) . '" class="postform td-js-font-icons">' .
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
		$taxonomyValue = $this->getTaxonomyData( $term->term_id );
		$options = $this->getOptions();
		$selectOptions = '';
		foreach ( $options as $option ) {
			$value = $option['value'];
			$selectOptions .= '<option' . ( $value == $taxonomyValue ? ' selected="selected"' : '' ) . ' value="' . esc_attr( $value ) . '">' . esc_html( $option['label'] ) . '</option>';
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
	 * @param int $termId
	 */
	public function hookAddTableColumnValue( $deprecated, $columnId, $termId ) {
		/*if ( $this->getTableColumnId() == $columnId ) {
			$header_section_id = $this->getTaxonomyData( $termId );
			print '#' . $header_section_id;
		}*/
	}

	public function getOptions() {
		$list = array();

		$list[] = array(
			'value' => '',
			'label' => __( 'Default', 'adventure_tours' ),
		);

		$list[] = array(
			'value' => '-1',
			'label' => __( 'None', 'adventure_tours' ) . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
		);

		$items = get_posts( array(
			'post_type' => 'at_header_section',
			'numberposts' => -1,
		) );

		if ( $items ) {
			foreach ( $items as $item ) {
				$list[] = array(
					'value' => $item->ID,
					'label' => $item->post_title,
				);
			}
		}

		return $list;
	}
}
