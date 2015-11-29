<?php
/**
 *
 */
class DataTypeIdDT extends DataType {

	static $objField = array("optional" => false,
				 "type" => "is_int",
				 "DBType" => "INT",
				 "regex" => "\d+",
				 "maxLength" => 5,
				 "identifier" => array(false => "",
						       true => "UNSIGNED NOT NULL AUTO_INCREMENT"),
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

		return "$params[DBType] ($params[maxLength]) $params[identifier]";
	}
}
