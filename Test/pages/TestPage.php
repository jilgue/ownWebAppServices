<?php

/**
 *
 */
class TestPage extends DispatchPage {

	static $objField = array("page" => "test");

	function getOutput() {

		$test = Test::stVirtualConstructor();
		var_dump($test);die;
		$dt = DataTypeString::stVirtualConstructor();
		var_dump($dt);die;
		var_dump($dt->isValidValue("222asdf"), $dt->isValidValue(333), $dt->isValidValue(true));die;
	}
}
