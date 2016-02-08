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

	private function _formatFields($fields, $class) {

		$obj = $class::stVirtualConstructor();

		$ret = array();
		foreach ($fields as $field => $params) {

			$DT = $obj->getFieldDTObj($field);
			$ret[] = array_merge(array("field" => $field), get_object_vars($DT));
		}
		return $ret;
	}

	protected function _getResponse() {

		$callableMethod = $this->_getCallableMethod();
		if ($callableMethod === array()) {
			return array();
		}

		list($class, $func) = $callableMethod;

		$fields = $this->_getFields($class, $func);

		return array("config" => $this->_formatFields($fields, $class));
	}
}
