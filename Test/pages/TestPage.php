<?php

/**
 *
 */
class TestPage extends DispatchPage {

	function getOutput() {

		var_dump(DBObject::stDBToObjFields("test"));die;
		$test = Test::stVirtualConstructor(1);
		var_dump($test);die;
	}
}
