<?php
/**
 * Dispatching de URLs
 */
class APITest extends DispatchPage {

	static $objField = array("algo" => "\d");

	function getOutput() {

		$image = Image::stVirtualConstructor("1");
		var_dump($image);die;
		echo $this->algo;
	}
}
