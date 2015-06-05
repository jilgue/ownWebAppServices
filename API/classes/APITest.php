<?php
/**
 * Dispatching de URLs
 */
class APITest extends DispatchPage {

	var $fieldConfig = array("algo" => "\d");

	function getOutput() {

		$ob = DBTest1::stVirtualConstructor(array("algo" => "31123"));
		var_dump($ob->getAlgo());
		var_dump($ob->setAlgo("hola"));
		var_dump($ob);die;
		echo $this->algo;
	}
}
