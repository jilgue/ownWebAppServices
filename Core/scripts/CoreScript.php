<?php
/**
 *
 */
abstract class CoreScript {

	var $params = array();

	function __construct($params, $paramsSpec = null) {

	}

	abstract function run();
}
