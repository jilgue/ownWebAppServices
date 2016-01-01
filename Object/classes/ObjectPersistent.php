<?php

/**
 *
 */
abstract class ObjectPersistent extends ObjectConfigurable {

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

		$storedParams = $this->_getStoredParams();

		if (!ObjectPersistent::stIsValidParamsValues(get_class($this), $storedParams)) {
			// TODO warning y crear error
			return false;
		}

		return $this->_save();
	}

	abstract protected function _delete();

	function delete() {

		return $this->_delete();
	}

	protected function _getObjectId() {

		$fieldId = static::stGetFieldConfigFiltered(array("identifier" => true));

		return $this->$fieldId;
	}

	function getObjectId() {

		return $this->_getObjectId();
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
