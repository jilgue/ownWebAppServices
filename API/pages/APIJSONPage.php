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
		$this->_securityRequest($params);
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
			$this->_unauthorizedRequest("WRONG_TOKEN");
		}
	}

	private function _unauthorizedRequest($error) {

		$this->_setError($error, $error);

		$this->printOutput();
		die;
	}

	protected function _setError($error, $msg) {

		$this->status = "KO";

		$this->error = LoadConfig::stGetConfigVar("errorCode")[$error];

		$this->response = $msg;
	}

	abstract protected function _getResponse();

	function _getOutput() {

		$ret = array();

		$ret["response"] = $this->response == "" ? $this->_getResponse() : $this->response;

		$ret = $this->error == "" ? $ret : array_merge($ret, array("error" => $this->error));

		$ret = array_merge($ret, array("status" => $this->status));

		return $ret;
	}
}
