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
	// No todos van a tener un input
	var $inputType = false;

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

	protected function _getDBColumnType($params) {

		$ret = "$params[DBType]";

		if ($params["maxLength"]) {
			$ret = $ret . " ($params[maxLength])";
		}

		if (!$this->optional) {
			$ret = $ret . " NOT NULL";
		}

		if (isset($this->default)) {
			$ret = $ret . " DEFAULT '$this->default'";
		}

		return $ret;
	}


	function getDBColumnType($field) {

		$params = array_merge(array_keys($this->objFields), array_keys($this->params));

		$_params = array();
		foreach ($params as $param) {

			$_params[$param] = isset($this->$param) ? $this->$param : $this->objFields["$param"];
		}

		return "`" . DBObject::stObjFieldToDBField($field) . "` " . $this->_getDBColumnType($_params);
	}
}
