<?php
/**
 * Dispatching de URLs
 */
class APITest extends DispatchPage {

	var $objField = array("algo" => "\d");

	function getOutput() {

		$ob = DBTest1::stVirtualConstructor(array("algo" => "31123"));
		var_dump($ob->getAlgo());
		var_dump($ob->setAlgo(array("algo" => "hola")));
		var_dump($ob);die;
		echo $this->algo;
	}
}
