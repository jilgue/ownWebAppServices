<?php

/**
 *
 */
abstract class ObjectPersistentSearch extends Object {

	const ERROR_CODE_INVALID_OPERATOR = "ObjectPersistentSearch::ERROR_CODE_INVALID_OPERATOR";
	const ERROR_CODE_INVALID_FILTER = "ObjectPersistentSearch::ERROR_CODE_INVALID_FILTER";

	public $objFields = array("filters" => array(),
				  "fields" => "*",
				  "order" => false,
				  "page" => 1,
				  "limit" => 10);

	var $operators = array("=",
			       "!=",
			       "<",
			       ">",
			       "<=",
			       ">=",
			       "RANGE",
			       "LIKE",
			       "REGEXP",
	);

	var $count = 0;
	var $search = array();

	protected function __construct($params = array()) {

		parent::__construct($params);

		// Nos han pasado el constructor vacio, no hay nada mas que hacer
		if ($params === array()) {
			return;
		}

		if ($this->_processFieldFilter()) {
			$this->_processSearch();
		}
	}

	abstract protected function _processSearch();

	abstract protected function _getSearchClass();

	private function _processFieldFilter() {

		$class = $this->_getSearchClass();
		$classFieldsConfig = $class::stGetFieldsConfig();

		if (!is_array($this->filters)) {
			// TODO error
			return false;
		}

		foreach ($this->filters as $filter => & $value) {

			// Si todavia no es un array ponemos el operador por defecto
			if (!is_array($value)) {
				$value = array("=", $value);
			} else {
				// Si es un array pero de un elemento suponemos que es el valor a buscar, no debería ser así pero lo admitimos porque puede ser cómodo
				if (count($value) == 1) {
					$value = array("=", $value[0]);
				}

				if (!in_array($value[0], $this->operators)) {
					LogsErrors::stCreate(array("errorCode" => ObjectPersistentSearch::ERROR_CODE_INVALID_OPERATOR,
								   "object" => $this,
								   "degree" => "fatal",
								   "param" => $filter,
								   "value" => $value[0]));

					return false;
				}
			}

			if (!in_array($filter, array_keys($classFieldsConfig))) {
				LogsErrors::stCreate(array("errorCode" => ObjectPersistentSearch::ERROR_CODE_INVALID_FILTER,
							   "object" => $this,
							   "degree" => "fatal",
							   "value" => $filter));
				return false;
			}


		}

		return true;
	}

	abstract protected function _getResults();

	function getResults() {

		$ret = array();
		foreach ($this->_getResults() as $key => $result) {
			foreach ($result as $field => $value) {
				$ret[$key][DBObject::stDBFieldToObjField($field)] = $value;
			}
		}
		return $ret;
	}

	function getResult() {

		return reset($this->getResults());
	}

	static function stGetResults($filters, $order = false, $page = 1, $limit = 10, $noArray = false) {

		$search = static::stVirtualConstructor(array("filters" => $filters,
							     "order" => $order,
							     "page" => $page,
							     "limit" => $limit,
		));

		if ($search->count == 0) {
			return array();
		}

		if ($noArray === false
		    && $search->count == 1) {
			return $search->getResult();
		}

		if ($search->count > 1
		    || $noArray !== false) {
			return $search->getResults();
		}
	}
}