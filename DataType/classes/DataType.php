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
				 "validValues" => null,
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

	abstract protected function _getDBColumnType($params);

	function getDBColumnType($field) {

		$params = array_keys($this::$objField);
		$_params = array();
		foreach ($params as $param) {

			$_params[$param] = isset($this->$param) ? $this->$param : $this::$objField["$param"];

			// Si es un array puede ser que nos pasen un valor de los posibles
			if (is_array($this::$objField["$param"])
			    && isset($this::$objField["$param"][$_params[$param]])) {
				$_params[$param] = $this::$objField["$param"][$_params[$param]];
			}
		}

		return "`" . DBObject::stObjFieldToDBField($field) . "` " . $this->_getDBColumnType($_params);
	}
}
