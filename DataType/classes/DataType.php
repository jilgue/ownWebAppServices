<?php
/**
 *
 */
abstract class DataType extends ObjectConfigurable {

	var $optional = true;
	// Los campos declarados a null hay que sobreescribirlos
	var $type = null;
	var $DBType = null;
	var $regex = null;
	var $maxLength = null;

	var $validValues = array();

	public $objFields = array("type" => array("DT" => "DataTypeStringDT",
						  "DTParams" => array("optional" => false)),
				  "DBType" => array("DT" => "DataTypeStringDT",
						    "DTParams" => array("optional" => false)),
				  "regex" => array("DT" => "DataTypeStringDT",
						   "DTParams" => array("optional" => false)),
				  "maxLength" => array("DT" => "DataTypeIntDT",
						       "DTParams" => array("optional" => false)),
	);

	function isValidValue($value) {

		$type = $this->type;
		if (!$type($value)) {
			return false;
		}

		if (preg_match("#" . $this->regex . "#", $value, $match) !== 1) {
			return false;
		}

		if ($this->validValues
		    && !in_array($value, $this->validValues, true)) {
			return false;
		}

		if (strlen($value) > $this->maxLength) {
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
