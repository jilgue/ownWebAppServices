<?php

/**
 * DB Object
 */
abstract class DBObject extends Object {

	static $objField = array();
	static $table;

	protected function __construct($params = array()) {

		$this->_loadObjField();
		$params = $this->_loadFields($params);
		parent::__construct($params);
	}

	private function _loadObjField() {

		$dbFields = DBObject::_stGetTableFields($this::$table);
		$this->objField = DBObject::stDBToObjFields($dbFields);
	}

	private function _loadFields($params) {

		// El primer parámetro ha de ser el id, SIEMPRE
		$objId = array_slice($params, 0, 1);
		$class = $params["class"];

		if(!DBObject::stExist($objId, $class)) {
			// No creo que esto sea bueno
			return $params;
		}

		$dbObj = DBMySQLConnection::stVirtualConstructor($class::$table)->getObj($objId);
		$ret = array();
		foreach ($dbObj as $field => $value) {
			$ret[DBObject::stDBFieldToObjField($field)] = $value;
		}

		return array_merge($ret, $params);
	}

	private static function _stGetTableFields($table) {

		$conn = DBMySQLConnection::stVirtualConstructor(array("table" => $table));
		return $conn->describeTableFields();
	}

	static function stDBFieldToObjField($field) {

		$pieces = explode("_", $field);

		$ret = "";
		foreach ($pieces as $piece) {
			$ret = $ret . ucfirst($piece);
		}
		return lcfirst($ret);
	}

	static function stObjFieldToDBField($field) {

		if (!preg_match_all('/((?:^|[A-Z])[a-z]+)/', $field, $matches)) {
			return $field;
		}

		$ret = "";
		foreach (reset($matches) as $piece) {
			$ret = $ret . "_" . lcfirst($piece);
		}
		return substr($ret, 1);
	}

	static function stDBToObjFields($fields) {

		$ret = array();
		foreach ($fields as $row) {

			$field = $row["Field"];
			$ret[DBObject::stDBFieldToObjField($field)] = array("type" => ".*");
		}
		return $ret;
	}

	static function stObjToDBFields($fields) {

		return array_flip(DBObject::stDBFieldToObjField($fields));
	}

	static function stExist($objId, $class) {

		return (bool) DBMySQLConnection::stVirtualConstructor($class::$table)->existObj($objId);
	}

	function save() {

		
	}
	abstract static function stCreate();
}
