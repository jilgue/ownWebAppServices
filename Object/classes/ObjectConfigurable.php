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

		$objField = LoadConfig::stGetConfigVarClass("objField", $class);

		$stCache[$class] = $objField;

		return $objField;
	}

	static function stGetFieldConfigFiltered($filters) {

		$fieldConfig = static::stGetFieldsConfig();

		foreach ($fieldConfig as $field => $config) {
			if (array_search($filters, $config)) {
				return $field;
			}
		}

		return false;
	}

	protected static function _stExcludeConfigParams($class, $filters) {

		$fieldConfig = $class::stGetFieldsConfig();

		$excludeParams = array();
		foreach ($fieldConfig as $field => $config) {

			foreach ($filters as $filter) {

				if (array_search($filter, $config)  !== false) {
					$excludeParams[] = $field;
				}
			}
		}

		return array_diff(array_keys($fieldConfig), $excludeParams);
	}

	static function stVirtualConstructor($params = array()) {

		$class = get_called_class();
		$args = func_get_args();
		$numArgs = count($args);

		if ($numArgs == 1
		    && !is_array($params)) {

			$fieldId = $class::stGetFieldConfigFiltered(array("identifier" => true));

			if ($fieldId !== false) {
				$params = array($fieldId => $params);
			}
		}

		return parent::stVirtualConstructor($params);
	}
}
