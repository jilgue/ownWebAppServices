<?php
/**
 * Dispatching de URLs
 */
class APIRESTProcessJSONPage extends APIRESTJSONPage {

	static $objField = array("object" => "\d",
				 "function" => "\d",
	);

	private function _getFunctionParams($class, $func) {

		$refFunc = new ReflectionMethod($class, $func);

		$expectedParams = array();
		foreach($refFunc->getParameters() as $validFunctionParam){
			$expectedParams[] = $validFunctionParam->name;
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

		$res = call_user_func_array(array($class, $func), $this->_getFunctionParams($class, $func));
		var_dump($res);die;
	}
}
