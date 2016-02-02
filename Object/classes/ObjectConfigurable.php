<?php

/**
 *
 */
abstract class ObjectConfigurable extends Object {


	protected function __construct($params = array()) {

		$this->_loadObjFieldConfig();

		$this->_areValidValues($params);
		$params = $this->_getDefaultValues($params);

		parent::__construct($params);
	}

	private function _loadObjFieldConfig() {

		$objFields = static::stGetFieldsConfig();
		if ($objFields === array()) {
			return;
		}

		$this->objFields = array_merge($this->objFields, $objFields);
	}

	// TODO esto debería estar aquí ? es cosa de los DT no ?
	private function _areValidValues($params) {

		foreach ($params as $param => $value) {

			if (!isset($this->objFields[$param])) {
				// TODO crear error
				return;
			}

			$DT = $this->objFields[$param]["DT"];
			$DTParams = isset($this->objFields[$param]["DTParams"]) ? $this->objFields[$param]["DTParams"] : array();

			if (!$DT::stVirtualConstructor($DTParams)->isValidValue($value)) {
				// TODO crear error
				return;
			}
		}
	}

	// TODO esto debería estar aquí ? es cosa de los DT no ?
	private function _getDefaultValues($params) {

		$defaultParams = array_merge(static::stGetFieldsConfigObjFiltered("default"), static::stGetFieldsConfigObjFiltered("defaultEval"));
		foreach ($defaultParams as $field => $config) {

			// Si ya esta no tenemos que añadir su por defecto
			if (isset($params[$field])) {
				continue;
			}

			// Esto asi es como un poco feo
			if (isset($config["DTParams"]["default"])) {
				$params[$field] = $config["DTParams"]["default"];
			}

			if (isset($config["DTParams"]["defaultEval"])) {
				$params[$field] = eval($config["DTParams"]["defaultEval"]);
			}
		}

		return $params;
	}

	static function stGetFieldsConfig() {

		static $stCache = array();

		$class = get_called_class();

		if (isset($stCache[$class])) {
			return $stCache[$class];
		}

		$ret = array();
		do {
			$objField = LoadConfig::stGetConfigVarClass("objField", $class);

			if ($objField !== false) {
				$ret = array_merge($objField, $ret);
			}

		} while (($class = get_parent_class($class)) != false);

		$stCache[$class] = $ret;

		return $ret;
	}

	static function stGetFieldsConfigObjFiltered($filter) {

		$fieldConfig = static::stGetFieldsConfig();

		$ret = array();
		foreach ($fieldConfig as $field => $config) {

			if (array_column($config, $filter)) {
				$ret[$field] = $config;
			}
		}

		return $ret;
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
