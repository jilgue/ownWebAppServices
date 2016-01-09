<?php

/**
 *
 */
class TestPage extends DispatchPage {

	function getOutput() {

		$filters = array("campo1" => array("=", "hola"),
				 "campo2" => "que tal");
		$search = TestSearch::stVirtualConstructor(array("filters" => $filters,
		));

		var_dump($search->getResults());die;

	}
}
