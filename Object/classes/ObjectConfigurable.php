<?php

/**
 *
 */
abstract class ObjectConfigurable extends Object {


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

	static function stGetFieldFilteredConfig($filters) {

		$fieldConfig = static::stGetFieldsConfig();

		foreach ($fieldConfig as $field => $config) {
			if (array_search($filters, $config)) {
				return $field;
			}
		}

		return false;
	}

	static function stVirtualConstructor($params = array()) {

		$class = get_called_class();
		$args = func_get_args();
		$numArgs = count($args);

		if ($numArgs == 1
		    && !is_array($params)) {

			$fieldId = $class::stGetFieldFilteredConfig(array("identifier" => true));

			if ($fieldId !== false) {
				$params = array($fieldId => $params);
			}
		}

		return parent::stVirtualConstructor($params);
	}
}
