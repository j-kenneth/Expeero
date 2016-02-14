<?php
/**
 * Basic class for custom fields implementation related to terms/taxonomies/posts.
 * Implements basic gui/storing behavior.
 *
 * @author    Themedelight
 * @package   Themedelight/Components
 * @version   1.0.0
 */

abstract class TdTaxonomyFieldManager extends TdComponent
{
	/**
	 * List taxonomies for use TdTaxonomyFieldManager.
	 * @var array
	 */
	public $taxonomies = array();

	/**
	 * Themedelight cmponent
	 */
	public $storage;

	/**
	 * Identificator column for html table, where show list categories.
	 * @var string
	 */
	public $tableColumnId = 'td_field';

	/**
	 * Label for field, where show list taxonomies, "Add new taxonomy" and "Edit taxonomy".
	 * @var string
	 */
	public $fieldLabel = 'Field';

	/**
	 * Post variable uses for save data.
	 * @see TdTaxonomyFieldManager::hookAddFormInsertField(), TdTaxonomyFieldManager::hookEditFormInsertField() To set variable.
	 * @see TdTaxonomyFieldManager::hookSaveData() variable processing.
	 * @var string
	 */
	public $postVariableFieldData = 'td_taxonomy_field_data';

	/**
	 * HTML template for field "Add new taxonomy".
	 */
	abstract public function hookAddFormInsertField();

	/**
	 * HTML template for field "Edit taxonomy".
	 * @param object $term
	 */
	abstract public function hookEditFormInsertField( $term );

	/**
	 * HTML template for field where show list taxonomies.
	 * @param stirn $deprecated
	 * @param string $columnName
	 * @param int $termId
	 */
	abstract public function hookAddTableColumnValue( $deprecated, $columnName, $termId );

	public function init() {
		if ( parent::init() ) {
			if ( is_admin() && $this->getTaxonomies() && $this->getStorage() ) {
				$this->initFields();
				$this->initTableColumn();
				$this->initSaveData();
				$this->initRemoveData();
			}
			return true;
		}

		return false;
	}

	/**
	 * Initialization field "Add new taxonomy" and "Edit taxonomy".
	 *
	 * @return void
	 */
	protected function initFields() {
		$taxonomies = $this->getTaxonomies();
		foreach ( $taxonomies as $taxonomy ) {
			add_action( $taxonomy . '_add_form_fields', array( $this, 'hookAddFormInsertField' ), 10 );
			add_action( $taxonomy . '_edit_form_fields', array( $this, 'hookEditFormInsertField' ), 10, 1 );
		}
	}

	/**
	 * Initialization table column where show list taxonomy.
	 *
	 * @return void
	 */
	protected function initTableColumn() {
		$taxonomies = $this->getTaxonomies();
		foreach ( $taxonomies as $taxonomy ) {
			// Column title.
			add_action( 'manage_edit-' . $taxonomy . '_columns', array( $this, 'hookAddTableColumnTitle' ), 10, 1 );

			// Column value.
			add_action( 'manage_' . $taxonomy . '_custom_column', array( $this, 'hookAddTableColumnValue' ), 10, 3 );
		}
	}

	/**
	 * Initialize method for save and edit data in database.
	 *
	 * @return void
	 */
	protected function initSaveData() {
		$taxonomies = $this->getTaxonomies();
		foreach ( $taxonomies as $taxonomy ) {
			add_action( 'created_' . $taxonomy, array( $this, 'hookSaveData' ), 10, 2 );
			add_action( 'edited_' . $taxonomy, array( $this, 'hookSaveData' ), 10, 2 );
		}
	}

	/**
	 * Initialize method for remove data from database.
	 *
	 * @return void
	 */
	protected function initRemoveData() {
		$taxonomies = $this->getTaxonomies();
		foreach ( $taxonomies as $taxonomy ) {
			add_action( 'delete_' . $taxonomy, array( $this, 'hookDeleteData' ), 10, 1 );
		}
	}

	/**
	 * Hook save data saves data in database or removes data from database if POST variables == none.
	 *
	 * @param int $termId
	 * @return void
	 */
	public function hookSaveData( $termId ) {
		$postVariable = $this->getPostVariableFieldData();
		$taxonomyData = isset( $_POST[$postVariable] ) ? $_POST[$postVariable] : false;
		if ( ! $taxonomyData ) {
			return;
		}

		if ( 'none' == $taxonomyData ) {
			$this->removeTaxonomyData( $termId );
		} else {
			$this->updateTaxonomyData( $termId, $taxonomyData );
		}
	}

	/**
	 * Hook delete data removes data from database.
	 *
	 * @param int $termId
	 * @return void
	 */
	public function hookDeleteData( $termId ) {
		$this->removeTaxonomyData( $termId );
	}

	/**
	 * Hook sets table label, where show list taxonomies.
	 * @param array $columns
	 * @return array
	 */
	public function hookAddTableColumnTitle( $columns ) {
		$columns[$this->getTableColumnId()] = $this->getFieldLabel();

		return $columns;
	}

	/**
	 * Function gets taxonomy data from storage.
	 *
	 * @param int $termId
	 * @return string | false
	 */
	protected function getTaxonomyData( $termId ) {
		if ( ! isset( $termId ) ) {
			return false;
		}

		return $this->getStorage()->getData( $termId );
	}

	/**
	 * Function removes data from storage.
	 *
	 * @param int $termId
	 * @return void
	 */
	protected function removeTaxonomyData( $termId ) {
		$this->getStorage()->deleteData( $termId );
	}

	/**
	 * Function updates data in storage.
	 *
	 * @param int $termId
	 * @param string $taxonomyData
	 * @return void
	 */
	protected function updateTaxonomyData( $termId, $taxonomyData ) {
		$this->getStorage()->setData( $termId, $taxonomyData );
	}

	public function getTaxonomies() {
		return $this->taxonomies;
	}

	public function getTableColumnId() {
		return $this->tableColumnId;
	}

	public function getFieldLabel() {
		return $this->fieldLabel;
	}

	public function getStorage() {
		return $this->storage;
	}

	public function getPostVariableFieldData() {
		return $this->postVariableFieldData;
	}
}
