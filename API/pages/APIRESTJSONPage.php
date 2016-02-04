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


}
