<?php
/**
 *
 */
class DataTypeStringDT extends DataType {

	static $objField = array("type" => "is_string",
				 "DBType" => "VARCHAR",
				 "regex" => ".*",
				 "maxLength" => 250,
	);


	function isValidValue($value) {

		if (parent::isValidValue($value) === false) {
			return false;
		}

		if (strlen($value) > $this::$objField["maxLength"]) {
			return false;
		}

		return true;
	}

	protected function _getDBColumnType($params) {

		return "$params[DBType] ($params[maxLength]) NOT NULL";
	}
}
