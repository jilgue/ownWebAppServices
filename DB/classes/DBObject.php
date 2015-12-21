<?php

/**
 * DB Object
 */
abstract class DBObject extends ObjectPersistent {

	static $table;

	protected function __construct($params = array()) {

		parent::__construct($params);

		$this->_loadObj();
	}

	private function _loadObj() {

		$fieldId = static::stGetFieldFilteredConfig(array("identifier" => true));

		// Si tenemos el id cargado y existe cargamos el resto de sus datos
		if (isset($this->$fieldId)
		    && static::stExists($this->$fieldId)) {

			$table = DBObject::stGetTableName(get_class($this));

			$dbObj = DBMySQLConnection::stVirtualConstructor($table)->getObj(array($fieldId => $this->$fieldId));

			$fields = DBObject::stDBToObjFields($table);
			foreach ($dbObj as $dbField => $data) {
				$this->$fields[$dbField] = $data;
			}
		}
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

		$fieldId = $class::stGetFieldFilteredConfig(array("identifier" => true));

		$table = DBObject::stGetTableName($class);

		return (bool) DBMySQLConnection::stVirtualConstructor($table)->existObj(array($fieldId => $objId));
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

	function _save() {

		$class = get_called_class();

		$id = $class::stGetFieldFilteredConfig(array("identifier" => true));
		$objId = array($id => $this->$id);

		$dbObj = array();

		// TODO usar stObjToDBFields
		foreach ($this->_getStoredParams() as $field => $value) {
			$dbObj[DBObject::stObjFieldToDBField($field)] = $value;
		}

		$table = DBObject::stGetTableName($class);
		return (bool) DBMySQLConnection::stVirtualConstructor($table)->updateObj($dbObj, $objId);
	}

	static function stGetObjIdField($class) {

		// TODO mal !! $class::$objField; miente
		$objField = $class::$objField;

		return array_search("id", array_column($objField, "key"));
	}
}
