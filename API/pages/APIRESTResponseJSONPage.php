<?php
/**
 * Dispatching de URLs
 */
class APIRESTResponseJSONPage extends APIJSONPage {

	// Errores
	const ERROR_CODE_INVALID_FUNCTION_PARAM = "APIRESTResponseJSONPage::ERROR_CODE_INVALID_FUNCTION_PARAM";

	public $objFields = array("object" => "\d",
				  "function" => "\d",
	);

	protected function __construct($params = array()) {

		// Mergeamos los params que ya tenemos con los valores que vengan del post o del get
		// Ala ! Libres domingos y domingas
		$params = array_merge($params, $this->_getRequestParams());

		parent::__construct($params);
	}

	private function _getRequestParams() {

		$requestMethod = $_SERVER["REQUEST_METHOD"];

		$requestParams = eval('return $_' . $requestMethod . ';');

		return array("functionsParams" => $requestParams);
	}

	private function _getFunctionParams($class, $func) {

		$refFunc = new ReflectionMethod($class, $func);

		$expectedParams = array();
		foreach($refFunc->getParameters() as $validFunctionParam){
			$expectedParams[] = $validFunctionParam->name;
		}

		// Nos aseguramos que si nos pasamos el mismo numero de parametros que los que esperamos esten bien
		if (count($this->functionsParams) == count($expectedParams)
		    && array_diff(array_keys($this->functionsParams), $expectedParams) !== array()) {
			    return false;
		}

		$goodParams = true;

		$funtionParams = array();
		foreach ($this->functionsParams as $param => $value) {

			if (in_array($param, $expectedParams)) {
				$funtionParams[] = $value;
				$goodParams = true;
				continue;
			} else {
				if ($goodParams) {

					$funtionParams[][$param] = $value;
					$goodParams = false;
					continue;
				} else {
					end($funtionParams);
					$funtionParams[key($funtionParams)][$param] = $value;
				}
			}
		}

		return $funtionParams;
	}

	protected function _getResponse() {

		$class = LoadInit::stGetClassCaseInsensitive($this->object);

		$func = "st" . ucfirst($this->function);

		$obj = $class::stVirtualConstructor();

		if (!method_exists($obj, $func)) {

			return;
		}

		$params = $this->_getFunctionParams($class, $func);
		if (!$params) {
			LogsErrors::stCreate(array("errorCode" => APIRESTResponseJSONPage::ERROR_CODE_INVALID_FUNCTION_PARAM,
						   "function" => "_getFunctionParams"));
			return array();
		}

		$res = call_user_func_array(array($class, $func), $params);

		return array("response" => $res,
			     "class" => $class,
			     "function" => $func);
	}
}
