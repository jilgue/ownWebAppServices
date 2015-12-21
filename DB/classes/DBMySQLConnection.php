<?php

/**
 * Conexion con mysqli
 */
class DBMySQLConnection extends ObjectConfigurable {

	var $link;
	var $table;

	protected function __construct($params = array()) {

		$params = array_merge(array("link" => $this->_connect(LoadConfig::stGetConfigClass())), $params);
		parent::__construct($params);
	}

	private function _connect($config) {

		$port = isset($config["port"]) ? $config["port"] : null;

		$ret = new mysqli($config["host"], $config["user"], $config["password"], $config["database"], $port);

		if (!$ret) {
			Logs::stFatal("Can't connet to database");
		}

		return $ret;
	}

	static function stGetConnection() {
		return DBMySQLConnection::stVirtualConstructor()->getLink();
	}

	private function _nativeQuery($query, $link = false) {

		if (!$link) {
			$link = $this->link;
		}
		// TODO errores
		return $link->query($query);
	}

	function query($query) {

		return $this->_nativeQuery($query);
	}

	function getTables() {

		$resource = $this->_nativeQuery("SHOW TABLES");
		if ($resource === false) {
			return array();
		}

		$ret = array();
		while ($row = mysqli_fetch_assoc($resource)) {

			// MySQL devuelve cada row de SHOW TABLES en un array de un único elemento cuya clave es "Tables_in_xxx",
			// siendo xxx el nombre de la bbdd
			$ret[] = reset($row);
		}

		return $ret;
	}

	function describeTableFields($table = false) {

		if (!$table) {
			$table = $this->table;
		}

		$resource = $this->_nativeQuery("DESCRIBE $table");
		if ($resource === false) {
			return array();
		}

		$ret = array();
		while ($row = mysqli_fetch_assoc($resource)) {
			$ret[] = $row;
		}

		return $ret;
	}

	function count($query) {

		$resource = $this->_nativeQuery($query);
		if ($resource === false) {
			return 0;
		}

		$row = mysqli_fetch_assoc($resource);
		return reset($row);
	}

	function select($query) {

		$resource = $this->_nativeQuery($query);
		if ($resource === false) {
			return 0;
		}

		$ret = array();
		while ($row = $resource->fetch_assoc()) {
			$ret[] = $row;
		}

		if ($ret == array()) {
			return null;
		}
		return $ret[0];
	}

	function existObj($objId) {

		$field = key($objId);
		$id = current($objId);

		$query = "SELECT COUNT($field) FROM $this->table WHERE $field = '$id'";

		return $this->count($query);
	}

	function getObj($objId) {

		$field = key($objId);
		$id = current($objId);

		$query = "SELECT * FROM $this->table WHERE $field = $id";

		return $this->select($query);
	}

	function updateObj($dbObj, $objId) {

		// TRAPI
		$fieldId = key($objId);
		$id = current($objId);

		$query = "UPDATE $this->table SET";

		foreach ($dbObj as $field => $value) {

			$query = $query . " $field = '$value',";
		}

		$query = substr($query, 0, -1) . " WHERE $fieldId = $id";

		// Esto no me acaba de gustar
		return $this->_nativeQuery($query);
	}

	function createObj($dbObj) {

		$query = "INSERT INTO $this->table";

		$rows = "";
		$values = "";
		$select = "";
		foreach ($dbObj as $field => $value) {

			$rows = $rows . $field . ",";
			$values = $values . "'$value',";

			// Preparamos el select para luego
			$select = $select . "$field='$value' AND ";
		}

		$query = $query . " (" . substr($rows, 0, -1) . ") VALUES (" . substr($values, 0, -1) . ")";

		// A partir de aquí no me acaba de gustar
		if (!$this->_nativeQuery($query)) {
			return false;
		}

		return mysqli_insert_id($this->link);
	}
}
