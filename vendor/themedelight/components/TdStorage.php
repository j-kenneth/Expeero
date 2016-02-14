<?php
interface TdStorage
{
	public function getData( $dataId );

	public function setData( $dataId, $dataValue );

	public function deleteData( $dataId );
}
