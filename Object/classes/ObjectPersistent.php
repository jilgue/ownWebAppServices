<?php

/**
 *
 */
abstract class ObjectPersistent extends ObjectConfigurable {

	var $objectId = false;
	var $fieldId = false;

	protected function __construct($params = array()) {

		parent::__construct($params);

		$fieldId = static::stGetFieldConfigFiltered(array("identifier" => true));

		// Si no existe, ponemos como no ok y paramos
		if (!isset($this->$fieldId)
		    || static::stExists($this->$fieldId) === false) {
			$this->ok = false;
			return;
		}

		// añadimos el objectId
		$this->objectId = $this->$fieldId;
		$this->fieldId = $fieldId;
	}

	static function stGetValidCreateParams($class) {

		return ObjectConfigurable::_stExcludeConfigParams($class, array(array("identifier" => true)));
	}

	private static function stIsValidParamsValues($class, $params) {

		$fieldConfig = $class::stGetFieldsConfig();

		foreach ($params as $field => $value) {

			$DT = $fieldConfig[$field]["DT"];
			$DTParams = isset($fieldConfig[$field]["DTParams"]) ? $fieldConfig[$field]["DTParams"] : array();

			$DT = $DT::stVirtualConstructor($DTParams);

			if ($DT->isValidValue($value) === false) {
				// TODO warning y crear error
				return false;
			}
		}

		return true;
	}

	protected function _getStoredParams() {

		$storedParams = ObjectConfigurable::_stExcludeConfigParams(get_class($this), array(array("identifier" => true)));

		$ret = array();
		foreach ($storedParams as $storedParam) {
			$ret[$storedParam] = $this->$storedParam;
		}
		return $ret;
	}

	abstract protected static function _stExists($objId);

	static function stExists($objId) {

		// TODO validate params
		return static::_stExists($objId);
	}

	abstract protected static function _stCreate($params);

	static function stCreate($params) {

		// Para ello no se debe de llamar NUNCA a DBObject::stCreate si no con la clase del objeto a crear
		$class = get_called_class();

		$createParams = ObjectPersistent::stGetValidCreateParams($class);

		$invalidParams = array_diff(array_keys($params), $createParams);
		if (count($invalidParams) !== 0) {

			LogsErrors::stCreate(array("errorCode" => 0,
						   "param" => implode(",", $invalidParams)));
			return false;
		}

		if (!ObjectPersistent::stIsValidParamsValues($class, $params)) {
			// TODO warning y crear error
			return false;
		}

		return static::_stCreate($params);
	}

	static function stUpdate($objId, $params) {

		$obj = static::stVirtualConstructor($objId);

		if (!$obj->multiSetter($params)
		    || !$obj->save()) {
			return false;
		}

		return true;
	}

	abstract protected function _save();

	function save() {

		if(!$this->ok) {
			return false;
		}

		$storedParams = $this->_getStoredParams();

		if (!ObjectPersistent::stIsValidParamsValues(get_class($this), $storedParams)) {
			// TODO warning y crear error
			return false;
		}

		return $this->_save();
	}

	abstract protected function _delete();

	function delete() {

		if(!$this->ok) {
			return false;
		}

		return $this->_delete();
	}

	static function stGetObject($objId) {

		$obj = static::stVirtualConstructor($objId);

		$fields = array_keys(static::stGetFieldsConfig());

		$ret = array();
		foreach ($fields as $field) {
			if (isset($obj->$field)) {
				$ret[$field] = $obj->$field;
			}
		}

		return $ret;
	}
}
