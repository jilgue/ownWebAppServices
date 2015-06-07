<?php
/**
 * Dispatching de URLs
 */
class APITest extends DispatchPage {

	static $objField = array("algo" => "\d");

	function getOutput() {

		$image = Image::stVirtualConstructor("1");
		var_dump($image);
		$image->setUserImage("2");
		var_dump($image);
		var_dump($image->save());
		$image = Image::stVirtualConstructor("1");
		var_dump($image);die;
		echo $this->algo;
	}
}
