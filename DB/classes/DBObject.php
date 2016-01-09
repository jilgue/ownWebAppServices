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

		if (!$this->ok) {
			return;
		}

		$table = DBObject::stGetTableName(get_class($this));

		$dbObj = DBMySQLConnection::stVirtualConstructor($table)->getObj(array($this->fieldId => $this->objectId));

		$fields = DBObject::stDBToObjFields($table);
		foreach ($dbObj as $dbField => $data) {
			$this->$fields[$dbField] = $data;
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

	/**
	 * Es publica porque también lo usan clases "hermanas" como DBObjectSearch
	 */
	static function _stGetMySQLParams() {

		$class = get_called_class();

		$fieldId = $class::stGetFieldConfigFiltered(array("identifier" => true));

		$table = DBObject::stGetTableName($class);

		return array("table" => $table,
			     "fieldId" => $fieldId);
	}

	protected static function _stExists($objId) {

		$mysqlParams = static::_stGetMySQLParams();

		return (bool) DBMySQLConnection::stVirtualConstructor($mysqlParams)->existObj($objId);
	}

	protected static function _stCreate($params) {

		$class = get_called_class();

		$dbObj = array();
		foreach ($params as $field => $value) {

			if (isset($params[$field])) {
				$dbObj[DBObject::stObjFieldToDBField($field)] = $value;
			}
		}

		$mysqlParams = static::_stGetMySQLParams();

		return DBMySQLConnection::stVirtualConstructor($mysqlParams)->createObj($dbObj);
	}

	protected function _save() {

		$dbObj = array();

		// TODO usar stObjToDBFields
		foreach ($this->_getStoredParams() as $field => $value) {
			$dbObj[DBObject::stObjFieldToDBField($field)] = $value;
		}

		$mysqlParams = static::_stGetMySQLParams();

		return (bool) DBMySQLConnection::stVirtualConstructor($mysqlParams)->updateObj($dbObj, $this->_getObjectId());
	}

	protected function _delete() {

		$mysqlParams = static::_stGetMySQLParams();

		return (bool) DBMySQLConnection::stVirtualConstructor($mysqlParams)->deleteObj($this->_getObjectId());
	}
}
