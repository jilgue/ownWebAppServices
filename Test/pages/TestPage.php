<?php

/**
 *
 */
class TestPage extends DispatchPage {

	function getOutput() {

		$test = Test::stVirtualConstructor(1);
		var_dump($test);die;
	}
}
