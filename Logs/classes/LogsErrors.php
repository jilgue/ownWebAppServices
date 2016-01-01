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

		$errorCode = LoadConfig::stGetConfigVarClass("errorCode", "LogsErrors");

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

	private function _getTraces() {

		return debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 10);
	}
}