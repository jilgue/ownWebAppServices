<?php

/**
 * Clase de test de DB
 */
class DBTest1 extends DBObject {

	var $fieldConfig = array("algo" => "\d");

	static function stCreate() {
		return;
	}
}
