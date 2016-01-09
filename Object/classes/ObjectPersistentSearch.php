<?php

/**
 *
 */
abstract class ObjectPersistentSearch extends Object {

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

	protected function __construct($params = array()) {

		parent::__construct($params);

		// Nos han pasado el constructor vacio, no hay nada mas que hacer
		if ($params === array()) {
			return;
		}

		$this->_processFieldFilter();

		$this->_processSearch();
	}

	abstract protected function _processSearch();

	private function _processFieldFilter() {

		foreach ($this->filters as & $filter) {

			// Si todavia no es un array ponemos el operador por defecto
			if (!is_array($filter)) {
				$filter = array("=", $filter);
			}
		}
	}

	static function stGetResults($filters, $order = false, $page = 1, $limit = 10) {

		$search = static::stVirtualConstructor(array("filters" => $filters,
							     "order" => $order,
							     "page" => $page,
							     "limit" => $limit,
		));
		var_dump($search);die;
	}
}