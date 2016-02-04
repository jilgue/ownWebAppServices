<?php
/**
 * Dispatching de URLs
 */
class APIRESTConfigJSONPage extends APIRESTJSONPage {

	public $objFields = array("object" => "\d",
				  "function" => "\d",
	);

	private function _getFields($class, $func) {

		$refFunc = new ReflectionMethod($class, $func);

		$expectedParams = array();
		foreach($refFunc->getParameters() as $validFunctionParam){
			$expectedParams[] = $validFunctionParam->name;
		}

		$fields = $class::stGetFieldsConfig();

		return array_intersect_key($fields, array_flip($expectedParams));
	}

	protected function _getResponse() {

		$callableMethod = $this->_getCallableMethod();
		if ($callableMethod === array()) {
			return array();
		}

		list($class, $func) = $callableMethod;

		// Esto no deberia fallar, si no malo...
		return $this->_getFields($class, $func);
	}
}
