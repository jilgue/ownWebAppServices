<?php

/**
 *
 */
class TestPage extends DispatchPage {

	function getOutput() {

		$DT = DataTypeStringDT::stVirtualConstructor(array("validValues" => array("hola"),
								   "maxLength" => false));

		var_dump($DT->isValidValue(19283));
		var_dump($DT->isValidValue(false));
		var_dump($DT->isValidValue(null));
		var_dump($DT->isValidValue("hola"));


	}
}
