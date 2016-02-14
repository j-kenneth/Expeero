<?php
class TdStorageOptions extends TdComponent implements TdStorage
{
	public $optionName = 'td_storage_options';

	public function getData( $dataId ) {
		$data = get_option( $this->getOptionName() );
		return isset( $data[$dataId] ) ? $data[$dataId] : false;
	}

	public function setData( $dataId, $dataValue ) {
		$optionName = $this->getOptionName();
		$data = get_option( $optionName );

		if ( ! $data ) {
			$data = array();
		}

		$data[$dataId] = $dataValue;
		update_option( $optionName, $data );
	}

	public function deleteData( $dataId ) {
		$optionName = $this->getOptionName();
		$data = get_option( $optionName );

		if ( isset( $data[$dataId] ) ) {
			unset( $data[$dataId] );

			if ( count( $data ) > 0 ) {
				update_option( $optionName, $data );
			} else {
				delete_option( $optionName );
			}
		}
	}

	public function getOptionName() {
		return $this->optionName;
	}
}
