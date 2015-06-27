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

	static function stGetObjField($table) {

		$dbFields = DBObject::_stGetTableFields($table);

		$ret = array();
		foreach ($dbFields as $row) {

			$field = $row["Field"];
			$type = $row["Type"];

			$ret[DBObject::stDBFieldToObjField($field)]["type"] = DBObject::_stGetRegexDBType($type);

			if ($row["Key"] == "PRI") {
				$ret[DBObject::stDBFieldToObjField($field)]["key"] = "id";
			}

			if ($row["Null"] == "NO") {
				$ret[DBObject::stDBFieldToObjField($field)]["optional"] = false;
			} else {
				$ret[DBObject::stDBFieldToObjField($field)]["optional"] = true;
			}

			// Default
			$ret[DBObject::stDBFieldToObjField($field)]["default"] = $row["Default"];
			if ($row["Extra"] == "auto_increment") {
				$ret[DBObject::stDBFieldToObjField($field)]["default"] = "autoIncrement";
			}
		}
		return $ret;
	}

	static private function _stGetRegexDBType($type) {

		if (preg_match_all("/[a-z]{0,}([0-9]{0,})/", $type, $matches)) {

			// Primero nos llega el tipo
			switch ($matches[0][0]) {
			case "int":
				$regex = "[0-9]";
				break;
			case "varchar":
				$regex = ".";
				break;
			case "tinyint":
				// Se usa para bool como no se puede hacer un preg match, hasta que se tenga datatype nos jodemos
				return ".*";
				break;
			case "date":
				return "\d{4}-\d{2}-\d{2}";
				break;
			default:
				return ".*";
				break;
			}

			// Quizá nos llegue la longitud
			if (isset($matches[0][2])
			    && $matches[0][2] != "") {
				return $regex . "{1," . $matches[0][2] ."}";
			}
		}

		// Raro raro, aceptamos cualquier cosa, supongo
		return ".*";
	}

	private function _loadFields($params) {

		// El primer parámetro ha de ser el id, SIEMPRE
		$objId = array_slice($params, 0, 1);

		// Si no han pasado id no hay que cargar ningun dato
		if ($objId == array(array())) {
			return $params;
		}

		$class = $params["class"];

		if(!$class::stExist($objId)) {
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

		if (!preg_match_all('/((?:^|[A-Z])([a-z]|[0-9])+)/', $field, $matches)) {
			return $field;
		}

		$ret = "";
		foreach (reset($matches) as $piece) {
			$ret = $ret . "_" . lcfirst($piece);
		}
		return substr($ret, 1);
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

	static function stExist($objId) {

		// Para ello no se debe de llamar NUNCA a DBObject::stExist si no con la clase del objeto a crear
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
