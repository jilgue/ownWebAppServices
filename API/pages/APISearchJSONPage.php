<?php
/**
 * Dispatching de URLs
 */
class APISearchJSONPage extends APIJSONPage {

	var $searchParams = array();
	var $page = 1;
	var $resultsPerPage = 10;

	static $objField = array("object" => "\d",
				 );


	function __construct($params) {

		parent::__construct($params);
		$params = $this->_loadSearchParams($params);


		var_dump($this);die;
	}

	static function stGetSearchOptionalsGETParams() {

		$paginationParams = array("page" => "\d+", "resultsPerPage" => "\d+");
		$additionalParams = array("order" => ".*");

		$searchGETParams = array();
		foreach (EDISubscriptionLog::stDBToObjFields() as $objField) {
			$searchGETParams[$objField] = ".*";
		}

		return array_merge($searchGETParams, $paginationParams, $additionalParams);
	}

	private function _loadSearchParams($params) {

		$class = LoadInit::stGetClassCaseInsensitive($this->object);
		var_dump($class, $params);die;
		foreach ($params as $param => $value) {

			if (isset(EDISubscriptionLog::stObjToDBFields()[$param])) {

				$this->searchParams["filters"][$param] = $value;

				// Quitamos el parametro para que no lo carge el autoload del padre
				unset($params[$param]);
			}

			if ($param == "order") {

				$this->searchParams["order"][$param] = $value;

				// Quitamos el parametro para que no lo carge el autoload del padre
				unset($params[$param]);
			}
		}

		return $params;
	}

	private function _getOpRegex() {

		$ret = "";
		foreach (EDISubscriptionLogSearch::$stOperatorAllowed as $operator) {
			$ret = $ret . "(" . $operator . ")|";
		}
		// Quitamos el último |
		return "/" . substr($ret, 0, -1) . "/";
	}

	private function _checkSearchValue($value, $param) {

		$cardType = EDISubscriptionLog::stGetFieldsConfigClass()[$param]["type"];

		$typeRegex = BBTCorePackage::stGetClassOrObjectConfigVar("EDISubscriptionLogSearch", "cardTypeToRegex");

		if (preg_match("/" . $typeRegex[$cardType] . "/", $value, $match) === 0) {
			$this->_setJSONError(5, "$param type incorrect");
			return false;
		}
		return true;
	}

	private function _checkSearchValues() {

		// Comprobamos que los valores que se usan para la búsqueda son correcto, y si no avisar
		$searchFilters = $this->_getSearch()->getFilters();

		foreach ($searchFilters as $param => $searchFilter) {

			if (!is_array($searchFilter)) {
				return $this->_checkSearchValue($searchFilter, $param);
			} else {
				foreach ($searchFilter as $value) {
					return $this->_checkSearchValue($value, $param);
				}
			}
		}

		return true;
	}

	private function _checkSearchParams() {

		if (!isset($this->searchParams["filters"])) {
			$this->_setJSONError(6, "Necesary filters params");
			return false;
		}

		foreach ($this->searchParams["filters"] as $param => $value) {

			if (preg_match($this->_getOpRegex(), $value, $match) === 1) {

				$value = array($match[0], trim(str_replace($match[0], "", $value)));

				if (count($value) != 2) {
					$this->_setJSONError(1, "Value" .  implode(" ", $value) . " wrong");
					return false;
				}
				$this->searchParams["filters"][$param] = $value;
			} else {

				if (count(explode(" ", $value)) > 1) {
					$this->_setJSONError(2, "Value $value wrong");
					return false;
				}
			}
		}

		if (isset($this->searchParams["order"])) {

			foreach ($this->searchParams["order"] as $param => $value) {

				if (preg_match("/(ASC)|(DESC)/", $value, $match) === 1) {
					$value = explode(" ", $value);

					if (count($value) != 2) {
						$this->_setJSONError(3, "Value" .  implode(" ", $value) . " wrong");
						return false;
					}
					$this->searchParams["order"] = array($value[0] => $value[1]);
				} else {

					// Si no viene ordenación por defecto ASC
					$this->searchParams["order"] = array($value => "ASC");
				}
			}
		}

		return true;
	}

	private function _getSearch() {

		return new EDISubscriptionLogSearch($this->searchParams, $this->page, $this->resultsPerPage, $this->resultsPerPage * ($this->page - 1));
	}

	private function _DBFieldToObjFieldResult($results) {

		$ret = array();
		foreach ($results as $key => $resul) {
			foreach ($resul as $field => $value) {

				$ret[$key][EDISubscriptionLog::stDBToObjFields()[$field]] = $value;

				// TRAPI
				if (EDISubscriptionLog::stDBToObjFields()[$field] == "trace") {
					$ret[$key][EDISubscriptionLog::stDBToObjFields()[$field]] = unserialize($value);
				}
			}
		}
		return $ret;
	}

	function _getResponse() {

		if (!$this->_checkSearchParams()) {
			return;
		}

		if (!$this->_checkSearchValues()) {
			return;
		}

		// TODO: mirar si se puede comprobar que la query ha salido bien
		return $this->_DBFieldToObjFieldResult($this->_getSearch()->getResults());
	}
}
