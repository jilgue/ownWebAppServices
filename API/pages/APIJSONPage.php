<?php
/**
 * Dispatching de URLs
 */
abstract class APIJSONPage extends DispatchJSONPage {

	var $error = "";
	var $status = "OK";
	var $response = "";

	protected function __construct($params = array()) {

		parent::__construct($params);

		// Securizamos las peticiones
		//$this->_securityRequest($params);
	}

	private function _securityRequest($params) {

		$apiKey = "547cd75345cb937f69c76d9461cdf8f1";

		if (!isset($params["token"])) {

			// Fuera
			$this->_unauthorizedRequest("MISING_TOKEN");
		}

		$token = sha1($apiKey . LoadConfig::stGetConfigClass()["token"] . $params["object"]);

		if ($params["token"] != $token) {

			// Fuera
			$this->_unauthorizedRequest("WRONG_TOKEN", $token);
		}
	}

	private function _unauthorizedRequest($error, $token = "") {

		$this->_setError($error, $error . " the token correct is " . $token);

		$this->printOutput();
		die;
	}

	protected function _getErrors() {

		if (count(LogsErrors::$stErrors) == 0) {
			return array();
		}

		$this->status = "KO";

		return array("errors" => LogsErrors::$stErrors);
	}

	abstract protected function _getResponse();

	function _getOutput() {

		$ret = $this->_getResponse();

		$ret = array_merge($ret, $this->_getErrors());

		$ret = array_merge($ret, array("status" => $this->status));

		return $ret;
	}
}
