<?php

/**
 *
 */
class TestPage extends DispatchPage {

	function getOutput() {

		$params = array("campo1" => "campo",
				"campo2" => "que tal");
		$test = Test::stUpdate(1, $params);
		var_dump($test);
		$test = Test::stVirtualConstructor(1);
		var_dump($test);die;
		$test->setCampo1("hola5");
		$test->save();
		$test = Test::stVirtualConstructor(1);
		var_dump($test);die;
	}
}
