<?php

/**
 * Dispatching de URLs
 */
abstract class DispatchPage extends Object {

	static $objField = array("page" => "page");
	abstract function getOutput();

	function printOutput() {

		echo $this->getOutput();
	}
}
