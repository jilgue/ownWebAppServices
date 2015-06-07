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

		// Cargamos los datos en this para que no se queden guardados
		$this->objField = $this::$objField;

		foreach ($dbFields as $row) {

			$field = $row["Field"];
			$type = $row["Type"];
			$this->objField[DBObject::stDBFieldToObjField($field)]["type"] = $this->_getRegexDBType($type);
		}
	}

	private function _getRegexDBType($type) {

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

	static function stExist($objId, $class) {

		return (bool) DBMySQLConnection::stVirtualConstructor($class::$table)->existObj($objId);
	}

	function save() {

		$dbObj = array();
		foreach ($this->objField as $field => $_value) {
			$dbObj[DBObject::stObjFieldToDBField($field)] = $this->$field;
		}

		return (bool) DBMySQLConnection::stVirtualConstructor($this::$table)->updateObj($dbObj);
	}

	abstract static function stCreate();
}
