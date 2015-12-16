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

	static function stExists($objId) {

		// Para ello no se debe de llamar NUNCA a DBObject::stExists si no con la clase del objeto a crear
		$class = get_called_class();

		return (bool) DBMySQLConnection::stVirtualConstructor($class::$table)->existObj($objId);
	}

	function save() {

		$dbObj = array();
		foreach ($this->objField as $field => $_value) {
			$dbObj[DBObject::stObjFieldToDBField($field)] = $this->$field;
		}
		// TODO revisar que this table fijo que miente
		return (bool) DBMySQLConnection::stVirtualConstructor($this::$table)->updateObj($dbObj);
	}

	static function stCreate($params) {

		// Para ello no se debe de llamar NUNCA a DBObject::stCreate si no con la clase del objeto a crear
		$class = get_called_class();

		$objField = $class::$objField;

		// Intanciamos la clase vacia para obtener los campos que no tengamos
		$obj = $class::stVirtualConstructor();

		$dbObj = array();
		foreach ($objField as $field => $options) {

			// Si no nos han pasado el valor, no tiene por defecto y no es auto increment
			if (!isset($params[$field])
			    && $options["default"] != "autoIncrement"
			    && is_null($options["default"])
			    ) {
				$func = "get" . ucfirst($field).  "DefaultValue";

				if (method_exists($obj, $func)) {

					// Pasamos todos los parametros y ya el sabra que hacer y que no
					$dbObj[DBObject::stObjFieldToDBField($field)] = $obj->$func($params);
				} else {
					return "Missing params $field";
				}
			}

			if (isset($params[$field])) {
				$dbObj[DBObject::stObjFieldToDBField($field)] = $params[$field];
			}
		}

		return array(DBObject::stGetObjIdField($class) => DBMySQLConnection::stVirtualConstructor($class::$table)->createObj($dbObj));
	}

	static function stUpdate($params) {

		// Para ello no se debe de llamar NUNCA a DBObject::stUpdate si no con la clase del objeto a crear
		$class = get_called_class();

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

	static function stGetObjIdField($class) {

		$objField = $class::$objField;

		return array_search("id", DBObject::_array_column($objField, "key"));
	}

	/**
	 * Este método es de php 5.5
	 */
	static private function _array_column($array, $columnKey) {

		$ret = array();
		foreach ($array as $key => $value) {

			if (isset($value[$columnKey])) {
				$ret[$key] = $value[$columnKey];
			}
		}

		return $ret;
	}
}
