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
			$this->_unauthorizedRequest("WRONG_TOKEN", $token);
		}
	}

	private function _unauthorizedRequest($error, $token = "") {

		$this->_setError($error, $error . " the token correct is " . $token);

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

		// Si ya tenemos la respuesta no seguimos NOTA esto no me gusta una mierda asin
		if ($this->response != "") {
			$ret["response"] = $this->response;
		} else {
			$_ret = $this->_getResponse();
			$ret["response"] =  $this->response == "" ? $_ret  : $this->response;
		}

		$ret = $this->error == "" ? $ret : array_merge($ret, array("error" => $this->error));

		$ret = array_merge($ret, array("status" => $this->status));

		return $ret;
	}
}
