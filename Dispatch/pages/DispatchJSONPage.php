<?php
/**
 * Dispatching de URLs
 */
abstract class DispatchJSONPage extends DispatchPage {

	private function _setHeaders() {

		header('Content-Type: application/json');
	}

	abstract protected function _getOutput();

	function getOutput() {

		$this->_setHeaders();

		// Llamamos primero a _getOutput para recoger el resultado de la clase hija
		$ret = $this->_getOutput();

                return json_encode($ret);
	}
}
