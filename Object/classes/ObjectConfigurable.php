<?php

/**
 *
 */
class ObjectConfigurable extends Object {


	protected function __construct($params = array()) {

		$this->_loadObjFieldConfig();

		parent::__construct($params);
	}

	private function _loadObjFieldConfig() {

		$class = get_class($this);

		$ret = array();
		do {
			$objField = LoadConfig::stGetConfigVarClass("objField", $class);

			if ($objField !== false) {
				$this::$objField = array_merge($objField, $this::$objField);
			}

		} while (($class = get_parent_class($class)) != false);
	}

	static function stGetFieldsConfig() {

		static $stCache = array();

		$class = get_called_class();

		if (isset($stCache[$class])) {
			return $stCache[$class];
		}

		if ($class::$objField == array()) {
			$classObj = $class::stVirtualConstructor();
		}

		$ret = $class::$objField;

		$stCache[$class] = $ret;

		return $ret;
	}
}
