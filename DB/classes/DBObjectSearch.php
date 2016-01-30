<?php

/**
 *
 */
abstract class DBObjectSearch extends ObjectPersistentSearch {


	protected function _processSearch() {

		$class = $this->_getSearchClass();

		$table = DBObject::stGetTableName($class);

		$fieldId = $class::stGetFieldConfigFiltered(array("identifier" => true));

		$select = "SELECT $this->fields FROM $table";

		$where = "";
		foreach ($this->filters as $field => $filter) {

			$field = DBObject::stObjFieldToDBField($field);

			$op = $filter[0];

			if (is_array($filter[1])) {
				$whereField = "(";
				foreach ($filter[1] as $value) {
					// TODO soporte para OR ?
					$whereField = $whereField . "'" . $value . "'" . " AND ";
				}
				$whereField = substr($whereField, 0, -5) . ")";
			} else {
				$whereField = "'" . $filter[1] . "'";
			}

			$where = $where . $field . " " . $op . " " . $whereField . " AND ";
		}

		if ($where != "") {
			$select = $select . " WHERE " .	substr($where, 0, -5);
		}

		$offset = ($this->page - 1) * $this->limit;

		$select = $select . " LIMIT $offset,$this->limit";

		$mysqlParams = static::_stGetMySQLParams();

		$search = DBMySQLConnection::stVirtualConstructor($mysqlParams)->query($select);

		// Anotamos los resultados
		$this->count = $search->num_rows;

		// Guardamos la búsqueda
		$this->search = $search;
	}

	function hasResults() {

		return (bool) $this->count;
	}

	function getResults() {

		$ret = array();

		// Si no hay nada que buscar porque hay error, devolvemos vacio y como esta setado el error nos enteramos de que algo va mal
		if (!is_object($this->search)
		    && !method_exists($this->search, "fetch_assoc")) {
			return $ret;
		}

		while ($row = $this->search->fetch_assoc()) {
			$ret[] = $row;
		}

		if ($ret == array()) {
			return false;
		}

		return $ret;
	}

	/**
	 * Solo devuelve un resultado
	 */
	function getResult() {

		// Si no hay nada que buscar porque hay error, devolvemos vacio y como esta setado el error nos enteramos de que algo va mal
		if (!is_object($this->search)
		    && !method_exists($this->search, "fetch_assoc")) {
			return $ret;
		}

		while ($row = $this->search->fetch_assoc()) {
			return $row;
		}

		return false;
	}
}
