<?php

/**
 *
 */

class LogsErrors extends ObjectConfigurable {

	static $stErrors = array();

	protected function __construct($params = array()) {

		parent::__construct($params);

		LogsErrors::$stErrors[] = $this->params;
	}


	static function stCreate($params) {

		return LogsErrors::stVirtualConstructor($params);
	}

	protected function _getMessage($params) {
		$class = $this->_getTraces(10)[9]["class"];

		$errorCode = LoadConfig::stGetConfigVarClass("errorCodeMessage", $class);

		if (isset($errorCode[$params["errorCode"]])) {
			return $errorCode[$params["errorCode"]];
		}

		return null;
	}

	protected function _getClass() {

		$traces = $this->_getTraces();

		return $traces[9]["class"];
	}

	protected function _getFunction(){

		$traces = $this->_getTraces();

		return $traces[9]["function"];
	}

	private function _getTraces($limit = 10) {

		return debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, $limit);
	}
}