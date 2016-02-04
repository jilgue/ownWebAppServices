<?php
/**
 * Dispatching de URLs
 */
abstract class APIRESTJSONPage extends APIJSONPage {

	protected function __construct($params = array()) {

		// Mergeamos los params que ya tenemos con los valores que vengan del post o del get
		// Ala ! Libres domingos y domingas
		$params = array_merge($params, $this->_getRequestParams());

		parent::__construct($params);
	}

	private function _getRequestParams() {

		$requestMethod = $_SERVER["REQUEST_METHOD"];

		$requestParams = eval('return $_' . $requestMethod . ';');

		return array("queryStringParams" => $requestParams);
	}

	protected function _getCallableMethod() {

		$class = LoadInit::stGetClassCaseInsensitive($this->object);

		$func = $this->function;

		$obj = $class::stVirtualConstructor();

		// Permitimos que no pasen la funcion tan cual
		if (!method_exists($obj, $func)) {
			$func = "st" . ucfirst($this->function);
		}

		if (!method_exists($obj, $func)) {

			return array();
		}

		return array($class, $func);
	}
}
