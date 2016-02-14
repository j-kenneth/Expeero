<?php
class TdStorageWoocommerceTermMeta extends TdComponent implements TdStorage
{
	public $optionName = 'td_storage_wc_ter_meta';

	public function getData( $dataId ) {
		return get_woocommerce_term_meta( $dataId, $this->getOptionName(), true );
	}

	public function setData( $dataId, $dataValue ) {
		update_woocommerce_term_meta( $dataId, $this->getOptionName(), $dataValue );
	}

	public function deleteData( $dataId ) {
		delete_woocommerce_term_meta( $dataId, $this->getOptionName() );
	}

	public function getOptionName() {
		return $this->optionName;
	}
}
