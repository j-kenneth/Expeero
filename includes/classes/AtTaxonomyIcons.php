<?php
/**
 * Component for 'Icon' selection field generaion.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.2.4
 */

class AtTaxonomyIcons extends TdTaxonomyFieldManager
{
	public $iconSize = '40px';

	public $selectOptionNoneLabel = 'None';

	public $icons_manager_service_id = 'icons_manager';

	public function init() {
		if ( $this->getStorage()->is_active() && parent::init() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'hookLoadAdminScripts' ) );
			return true;
		}
		return false;
	}

	public function hookLoadAdminScripts( $hook ) {
		if ( 'edit-tags.php' != $hook ) {
			return;
		}

		wp_enqueue_style( 'td-taxonomy-fiels-icons-select2', PARENT_URL . '/assets/td/css/TdTaxonomyIconsSelect2.min.css' );
		wp_enqueue_script( 'td-taxonomy-fiels-icons-select2', PARENT_URL . '/assets/td/js/TdTaxonomyIconsSelect2.full.min.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'td-taxonomy-fiels-icons-chooser', PARENT_URL . '/assets/td/js/TdTaxonomyIconsChooser.js', array( 'jquery', 'td-taxonomy-fiels-icons-select2' ), '', true );
		wp_localize_script( 'td-taxonomy-fiels-icons-chooser', 'td_icon_placeholder', $this->getSelectOptionNoneLabel() );
	}

	/**
	 * HTML template for field "Add new taxonomy".
	 */
	public function hookAddFormInsertField() {
		$icons = $this->getFontIcons();
		$selectOptions = '';
		foreach ( $icons as $icon ) {
			$selectOptions .= '<option value="' . esc_attr( $icon['value'] ) . '">' . esc_html( $icon['label'] ) . '</option>';
		}

		echo '<div class="form-field">' .
			'<label style="cursor:auto;">' . esc_html( $this->getFieldLabel() ) . '</label>' .
			'<select name="' . esc_attr( $this->getPostVariableFieldData() ) . '" class="postform td-js-font-icons">' .
				'<option value="none">' . esc_html( $this->getSelectOptionNoneLabel() ) . '</option>' .
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
		$taxonomyIconClass = $this->getTaxonomyData( $term->term_id );
		$icons = $this->getFontIcons();
		$selectOptions = '';
		foreach ( $icons as $icon ) {
			$iconValue = $icon['value'];
			$selectOptions .= '<option ' . ( $iconValue == $taxonomyIconClass ? ' selected="selected"' : '' ) . ' value="' . esc_attr( $iconValue ) . '">' . esc_html( $icon['label'] ) . '</option>';
		}

		echo '<tr class="form-field">' .
			'<th scope="row">' .
				'<label for="theme_taxonomy_icons">' . esc_html( $this->getFieldLabel() ) . '</label>' .
			'</th>' .
			'<td>' .
				'<select name="' . esc_attr( $this->getPostVariableFieldData() ) . '" class="postform td-js-font-icons">' .
					'<option value="none">' . esc_html( $this->getSelectOptionNoneLabel() ) . '</option>' .
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
	public function hookAddTableColumnValue( $deprecated, $columnName, $termId ) {
		if ( $this->getTableColumnId() == $columnName ) {
			$taxonomyIconClass = $this->getTaxonomyData( $termId );
			echo '<i style="font-size:' . esc_attr( $this->getIconSize() ) . '" class="' . esc_attr( $taxonomyIconClass ) . '"></i>';
		}
	}

	public function getFontIcons() {
		$service = $this->icons_manager_service_id ? adventure_tours_di( $this->icons_manager_service_id ) : null;
		$result = $service ? $service->get_list() : null;
		return $result ? $result : array();
	}

	public function getFontFile() {
		return $this->fontFile;
	}

	public function getIconSize() {
		return $this->iconSize;
	}

	public function getSelectOptionNoneLabel() {
		return $this->selectOptionNoneLabel;
	}
}
