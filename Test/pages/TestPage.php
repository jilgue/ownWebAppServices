<?php

/**
 *
 */
class TestPage extends DispatchPage {

	function getOutput() {

		$params = array("campo1" => 1,
				"campo2" => "que tal");
		$test = Test::stCreate($params);
		var_dump($test);die;
		die;
		$test = Test::stVirtualConstructor(1);
		var_dump($test);die;
	}
}
