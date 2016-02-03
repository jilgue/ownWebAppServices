<?php

/**
 *
 */
abstract class ObjectConfigurable extends Object {

	const ERROR_CODE_PARAM_IS_NOT_VALID = "ObjectConfigurable::ERROR_CODE_PARAM_IS_NOT_VALID";


	protected function __construct($params = array()) {

		$this->_loadObjFieldConfig();

		parent::__construct($params);

		$this->_validateParams();
	}

	private function _loadObjFieldConfig() {

		$objFields = static::stGetFieldsConfig();
		if ($objFields === array()) {
			return;
		}

		$this->objFields = array_merge($this->objFields, $objFields);
	}

	private function _validateParams() {

		// Comprobamos los parametros que nos han pasado son válidos
		foreach (array_intersect(array_keys($this->params), array_keys($this->objFields)) as $param) {

			if (!$this->getFieldDTObj($param)
			    || !$this->getFieldDTObj($param)->isValidValue($this->params[$param])) {

				LogsErrors::stCreate(array("errorCode" => ObjectConfigurable::ERROR_CODE_PARAM_IS_NOT_VALID,
							   "object" => $this,
							   "degree" => "fatal",
							   "param" => $param,
							   "value" => $this->params[$param]));
			}
		}
	}

	function getFieldDTObj($field) {

		if (!isset($this->objFields[$field]["DT"])) {
			return false;
		}

		$DT = $this->objFields[$field]["DT"];
		$DTParams = isset($this->objFields[$field]["DTParams"]) ? $this->objFields[$field]["DTParams"] : array();

		return $DT::stVirtualConstructor($DTParams);
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
