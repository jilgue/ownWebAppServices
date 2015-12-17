<?php

/**
 *
 */
class TestPage extends DispatchPage {

	function getOutput() {

		$params = array("campo1" => "hola",
				"campo2" => "que tal");
		$test = Test::stCreate($params);
		var_dump($test);die;
	}
}
