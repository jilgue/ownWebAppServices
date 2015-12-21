<?php

/**
 *
 */
class TestPage extends DispatchPage {

	function getOutput() {

		$params = array("campo1" => "campo",
				"campo2" => "que tal");
		//$test = Test::stCreate($params);
		$test = Test::stVirtualConstructor(2);
		var_dump($test->setCampo2("subnormal"), $test->save(), $test->delete());die;
	}
}
