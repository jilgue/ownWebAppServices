<?php
/**
 * Dispatching de URLs
 */
abstract class DispatchJSONPage extends DispatchPage {


	abstract protected function _getOutput();


	function getOutput() {

		header('Content-Type: application/json');

		// Llamamos primero a _getOutput para recoger el resultado de la clase hija
		$ret = $this->_getOutput();

                return json_encode($ret);
	}
}
