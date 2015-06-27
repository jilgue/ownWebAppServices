<?php
/**
 * Dispatching de URLs
 */
abstract class APIJSONPage extends DispatchJSONPage {

	var $error = "";
	var $status = "OK";
	var $response = "";

	protected function _setError($error, $msg) {

		$this->status = "KO";

		$this->error = LoadConfig::stGetConfigVar($error);

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
