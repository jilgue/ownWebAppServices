<?php

/**
 * Dispatching de URLs
 */
abstract class DispatchPage extends Object {

	abstract function getOutput();

	function printOutput() {

		echo $this->getOutput();
	}
}
