<?php

/**
 *
 */
class TestPage extends DispatchPage {

	function getOutput() {

		$params = array("campo1" => 1,
				"campo2" => "que tal");
		//$test = Test::stCreate($params);
		//var_dump($test);die;
		$test = Test::stVirtualConstructor(1);
		var_dump($test);
		$test->setCampo1("hola5");
		$test->save();
		$test = Test::stVirtualConstructor(1);
		var_dump($test);die;
	}
}
