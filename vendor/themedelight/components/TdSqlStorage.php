<?php
class TdSqlStorage extends TdComponent implements TdStorage
{
	public $table_name;

	public $storage_key;

	public $is_cache_allowed;

	public $check_table_exists = false;

	protected $_cache_storage = false;

	public function init() {
		if ( parent::init() ) {
			if ( ! $this->table_name ) {
				throw new Exception( 'Option "table_name" can not be empty.' );
			}
			if ( ! $this->storage_key ) {
				throw new Exception( 'Option "storage_key" can not be empty.' );
			}
			if ( $this->check_table_exists && ! $this->tableExists() ) {
				throw new Exception(
					sprintf( 'Table "%s" does not exist.', $this->getTableName() )
				);
			}
			return true;
		}
		return false;
	}

	public function getData( $key_id ) {
		$row = $this->getRow( $key_id );

		return $row ? $row['value'] : null;
	}

	public function setData( $key_id, $value ) {
		if ( $key_id < 1 ) {
			return;
		}

		global $wpdb;

		$row = $this->getRow( $key_id );

		if ( $row ) {
			if ( $row['value'] != $value ) {
				$wpdb->update(
					$this->getTableName(),
					array(
						'value' => $value,
					),
					array(
						'storage_key' => $this->storage_key,
						'key_id' => $key_id,
					),
					array(
						'%s',
					),
					array(
						'%s',
						'%d',
					)
				);
			} else {
				return;
			}
		} else {
			$wpdb->insert(
				$this->getTableName(),
				array(
					'storage_key' => $this->storage_key,
					'key_id' => $key_id,
					'value' => $value,
				),
				array(
					'%s',
					'%d',
					'%s',
				)
			);
		}

		if ( $this->is_cache_allowed ) {
			$this->resesetCache();
		}
	}

	public function deleteData( $key_id ) {
		if ( $key_id < 1 ) {
			return;
		}

		$row = $this->getRow( $key_id );

		if ( $row ) {
			global $wpdb;

			$wpdb->delete(
				$this->getTableName(),
				array(
					'storage_key' => $this->storage_key,
					'key_id' => $key_id,
				),
				array(
					'%s',
					'%d',
				)
			);

			if ( $this->is_cache_allowed ) {
				$this->resesetCache();
			}
		}
	}

	public function clearAll( ) {
		global $wpdb;

		$wpdb->delete(
			$this->getTableName(),
			array(
				'storage_key' => $this->storage_key,
			),
			array(
				'%s'
			)
		);

		if ( $this->is_cache_allowed ) {
			$this->resesetCache();
		}
	}

	public function getAll( ) {
		$cached = $this->is_cache_allowed ? $this->getCache() : false;
		if ( false !== $cached ) {
			return $cached;
		}

		$result = array();
		global $wpdb;

		$rows = $wpdb->get_results( $wpdb->prepare('SELECT `key_id`, `value` FROM `' . $this->getTableName() .'` ' .
			'WHERE `storage_key` = %s',
			$this->storage_key
		), ARRAY_A );

		if ( $rows ) {
			foreach ( $rows as $record ) {
				$result[ $record['key_id'] ] = $record['value'];
			}
		}

		if ( $this->is_cache_allowed ) {
			$this->setCache( $result );
		}

		return $result;
	}

	protected function getTableName() {
		global $wpdb;
		return $wpdb->prefix . $this->table_name;
	}

	protected function getRow( $key_id ) {
		if ( $key_id < 1 ) {
			return null;
		}

		if ( $this->is_cache_allowed ) {
			$allRecords = $this->getAll();
			$resultRow = null;
			if ( isset( $allRecords[$key_id] ) ) {
				return array(
					'key_id' => $key_id,
					'value' => $allRecords[$key_id]['value'],
				);
			}
			return $resultRow;
		} else {
			global $wpdb;

			$row = $wpdb->get_row( $wpdb->prepare('SELECT `key_id`, `value` FROM `' . $this->getTableName() .'` ' .
				'WHERE `storage_key` = %s AND `key_id` = %d',
				$this->storage_key,
				$key_id
			), ARRAY_A );

			return $row;
		}
	}

	protected function tableExists(){
		global $wpdb;
		$name = $this->getTableName();
		return $wpdb->get_var("SHOW TABLES LIKE '$name'") == $name;
	}

	public function getCache( ) {
		return $this->_cache_storage;
	}

	public function setCache( $value ) {
		$this->_cache_storage = $value;
	}

	public function resesetCache() {
		if ( false !== $this->_cache_storage ) {
			$this->_cache_storage = false;
		}
	}
}
