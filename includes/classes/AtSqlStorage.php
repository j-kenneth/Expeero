<?php
/**
 * Custom db table storage service.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

class AtSqlStorage extends TdSqlStorage
{
	public $table_name = 'adventure_tours_storage';

	// public $check_table_exists = WP_DEBUG;

	private static $_is_active_cache = array();

	public function is_active() {
		$table_name = $this->getTableName();

		if ( ! isset( self::$_is_active_cache[ $table_name ] ) ) {
			self::$_is_active_cache[ $table_name ] = $this->tableExists();
		}

		return self::$_is_active_cache[ $table_name ];
	}
}
