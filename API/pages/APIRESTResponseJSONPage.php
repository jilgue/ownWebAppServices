<?php
/**
 * Dispatching de URLs
 */
class APIRESTResponseJSONPage extends APIRESTJSONPage {

	// Errores
	const ERROR_CODE_INVALID_FUNCTION_PARAM = "APIRESTResponseJSONPage::ERROR_CODE_INVALID_FUNCTION_PARAM";

	public $objFields = array("object" => "\d",
				  "function" => "\d",
	);

	private function _getFunctionParams($class, $func) {

		$refFunc = new ReflectionMethod($class, $func);

		$expectedParams = array();

		foreach($refFunc->getParameters() as $validFunctionParam){

			if ($validFunctionParam->isOptional()) {

				$expectedParams[$validFunctionParam->getName()] = array("default" => $validFunctionParam->getDefaultValue(),
											// TODO guardamos el tipo porque sólo se puede meter un array cuando se espera un array, pero no se si lo necesitaré
											"type" => gettype($validFunctionParam->getDefaultValue()));
				continue;
			}

			// Indicamos que es obligatorio
			$expectedParams[$validFunctionParam->getName()] = false;
		}

		// Nos aseguramos que si nos pasamos el mismo número de parametros que los que esperamos estén bien
		if (count($this->queryStringParams) == count($expectedParams)
		    && array_diff(array_keys($this->queryStringParams), $expectedParams) !== array()) {
			// TODO mirar porque esto siempre es así, espero un array de cosas pero solo me mandan una, está bien
			//return false;
		}

		$goodParams = true;

		$funtionParams = array();

		// TODO ordenar parametros de queryStringParams con $expectedParams

		// Contador de parámetros que ya hemos procesado
		$i = 0;
		foreach ($this->queryStringParams as $param => $value) {

			if (isset($expectedParams[$param])) {

				$funtionParams[$param] = $value;
				$goodParams = true;

				$i++;
				continue;

			} else {
				if ($goodParams) {

					$funtionParams[array_keys($expectedParams)[$i]][$param] = $value;
					$goodParams = false;

					$i++;
					continue;
				} else {
					end($funtionParams);
					$funtionParams[key($funtionParams)][$param] = $value;

					$i++;
				}
			}
		}

		$ret = array();
		foreach ($expectedParams as $param => $options) {

			if (isset($funtionParams[$param])) {
				$ret[$param] = $funtionParams[$param];
			} else {
				// No debería pasar que no tengamos un valor por defecto de algo que no tenemos xDD muy bien explicado César xD
				$ret[$param] = $options["default"];
			}
		}

		return $ret;
	}

	protected function _getResponse() {

		$callableMethod = $this->_getCallableMethod();
		if ($callableMethod === array()) {
			return array();
		}

		list($class, $func) = $callableMethod;
		$params = $this->_getFunctionParams($class, $func);

		if (!$params) {
			LogsErrors::stCreate(array("errorCode" => APIRESTResponseJSONPage::ERROR_CODE_INVALID_FUNCTION_PARAM,
						   "function" => "_getFunctionParams"));
			return array();
		}

		$res = call_user_func_array($callableMethod, array_values($params));

		return array("response" => $res,
			     "class" => $class,
			     "function" => $func);
	}
}
