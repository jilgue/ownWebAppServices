<?php

/**
 * DB Object
 */
abstract class DBObject extends Object {

	protected function __construct($params = array()) {

		parent::__construct($params);

		$this->_load();
	}

	private function _loadFieldConfig() {
	}

	abstract static function stCreate();
}
