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
		$this->_securityRequest();
	}

	private function _securityRequest() {

		$apiKey = "547cd75345cb937f69c76d9461cdf8f1";

		if (!isset($this->token)) {

			// Fuera
			$this->_unauthorizedRequest("MISING_TOKEN");
		}

		$token = sha1($apiKey . LoadConfig::stGetConfigClass()["token"] . $this->object);

		if ($this->token != $token) {

			// Fuera
			$this->_unauthorizedRequest("WRONG_TOKEN");
		}

		return;
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

		$ret = array("response" => $this->_getResponse());

		$ret = $this->error == "" ? $ret : array_merge($ret, array("error" => $this->error));

		// Si tenemos el this reponse lo pisamos
		$ret = $this->response == "" ? $ret : array_merge($ret, array("response" => $this->response));

		$ret = array_merge($ret, array("status" => $this->status));

		return $ret;
	}
}
