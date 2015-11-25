<?php

/**
 *
 */
class TestPage extends DispatchPage {

	static $objField = array("page" => "test");

	function getOutput() {
		$dt = DataTypeIdDT::stVirtualConstructor();
		var_dump($dt);
		var_dump($dt->isValidValue_cached("222asdf"), $dt->isValidValue_cached("222asdf"), $dt->isValidValue(333), $dt->isValidValue(969696), $dt->isValidValue(true));die;
	}
}
