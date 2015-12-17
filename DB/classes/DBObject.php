<?php

/**
 * DB Object
 */
abstract class DBObject extends ObjectPersistent {

	static $table;

	protected function __construct($params = array()) {

		parent::__construct($params);
	}

	/**
	 * Convert the format a DB field (separation with low bar) to Obj field (camelcase)
	 * Inverse to stObjFieldToDBField
	 */
	static function stDBFieldToObjField($field) {

		$pieces = explode("_", $field);

		$ret = "";
		foreach ($pieces as $piece) {
			$ret = $ret . ucfirst($piece);
		}
		return lcfirst($ret);
	}

	/**
	 * Convert the format a Obj field to DB field
	 * Inverse to stDBFieldToObjField
	 */
	static function stObjFieldToDBField($field) {

		if (!preg_match_all('/((?:^|[A-Z])([a-z]|[0-9])+)/', $field, $matches)) {
			return $field;
		}

		$ret = "";
		foreach (reset($matches) as $piece) {
			$ret = $ret . "_" . lcfirst($piece);
		}
		return substr($ret, 1);
	}

	private static function _stGetTableFields($table) {

		$conn = DBMySQLConnection::stVirtualConstructor(array("table" => $table));
		return $conn->describeTableFields();
	}

	static function stDBToObjFields($table) {

		$fields = DBObject::_stGetTableFields($table);
		$ret = array();
		foreach ($fields as $row) {

			$field = $row["Field"];
			$ret[$field] = DBObject::stDBFieldToObjField($field);
		}
		return $ret;
	}

	static function stObjToDBFields($table) {

		return array_flip(DBObject::stDBToObjFields($table));
	}

	static function stGetTableName($class) {

		// TODO mirar si se hace de otra manera
		return strtolower($class);
	}

	protected static function _stExists($objId) {

		// Para ello no se debe de llamar NUNCA a DBObject::stExists si no con la clase del objeto a crear
		$class = get_called_class();

		return (bool) DBMySQLConnection::stVirtualConstructor($class::$table)->existObj($objId);
	}

	protected static function _stCreate($params) {

		$class = get_called_class();

		$dbObj = array();
		foreach ($params as $field => $value) {

			if (isset($params[$field])) {
				$dbObj[DBObject::stObjFieldToDBField($field)] = $value;
			}
		}

		$id = $class::stGetFieldFilteredConfig(array("identifier" => true));
		$table = DBObject::stGetTableName($class);
		return array($id => DBMySQLConnection::stVirtualConstructor($table)->createObj($dbObj));
	}

	protected static function _stUpdate($params) {

		$id = DBObject::stGetObjIdField($class);

		$obj = $class::stVirtualConstructor($params[$id]);

		// Quitamos el id
		// No podemos quitarlo en la clase de objeto porque no tiene dateTypes
		unset($params[$id]);

		if (!$obj->multiSetter($params)
		    || !$obj->save()) {
			return false;
		}

		return true;
	}

	function save() {

		$dbObj = array();
		foreach ($this->objField as $field => $_value) {
			$dbObj[DBObject::stObjFieldToDBField($field)] = $this->$field;
		}
		// TODO revisar que this table fijo que miente
		return (bool) DBMySQLConnection::stVirtualConstructor($this::$table)->updateObj($dbObj);
	}

	static function stGetObjIdField($class) {

		$objField = $class::$objField;

		return array_search("id", array_column($objField, "key"));
	}
}
