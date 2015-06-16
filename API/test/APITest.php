<?php
/**
 * Dispatching de URLs
 */
class APITest extends DispatchPage {

	static $objField = array("action" => "\d",
				 "object" => "\d",
				 );

	function getOutput() {

		var_dump($this);die;
		$array = array("userImage" => "2",
			       "nameImage" => "holaquetal",
			       "md5Hash" => "asdfasdfsafsadfa");
		$imageId = Image::stCreate($array);
		var_dump($imageId);die;
		echo $this->algo;
	}
}
