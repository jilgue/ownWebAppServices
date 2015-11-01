<?php
/**
 *
 */
class DataTypeString extends DataType {

	static $objField = array("type" => "is_string",
				 "DBType" => "VARCHAR",
				 "regex" => ".*",
	);

	protected function _getDBColumnType($fieldConfig) {

		return false;
	}
}
