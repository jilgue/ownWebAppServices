<?php
/**
 *
 */
abstract class DataType extends Object {

	// Campos que deben sobreescribir cada datatype
	static $objField = array("optional" => true,
				 "type" => null,
				 "DBType" => null,
				 "regex" => null,
				 "maxLength" => null,
	);

	function isValidValue($value) {

		$type = $this::$objField["type"];
		if (!$type($value)) {
			return false;
		}

		if (preg_match("#" . $this::$objField["regex"] . "#", $value, $match) !== 1) {
			return false;
		}

		return true;
	}

	function getDBColumnType($field) {

		$params = array_keys($this::$objField);
		foreach ($params as $param) {

			$_param = isset($this->$param) ? $this->$param : $this::$objField["$param"];
			${$param} = $_param;
		}

		return "`" . $field . "` " . $DBType . "(" . $maxLength . ") NOT NULL";
	}
}
