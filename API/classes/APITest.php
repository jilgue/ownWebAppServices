<?php
/**
 * Dispatching de URLs
 */
class APITest extends DispatchPage {

	var $fieldConfig = array("algo" => "\d");

	function getOutput() {
		echo $this->algo;
	}
}
