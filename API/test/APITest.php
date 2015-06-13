<?php
/**
 * Dispatching de URLs
 */
class APITest extends DispatchPage {

	static $objField = array("algo" => "\d");

	function getOutput() {

		$array = array("userImage" => "2",
			       "nameImage" => "holaquetal",
			       "md5Hash" => "asdfasdfsafsadfa");
		$imageId = Image::stCreate($array);
		var_dump($imageId);die;
		echo $this->algo;
	}
}
