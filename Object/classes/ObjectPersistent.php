<?php

/**
 *
 */
abstract class ObjectPersistent extends ObjectConfigurable {

	static function stGetValidCreateParams($class) {

		$fieldConfig = $class::stGetFieldsConfig();

		$invalidCreateDTParams = array(array("identifier" => true));

		$ret = array();
		foreach ($fieldConfig as $field => $config) {

			foreach ($invalidCreateDTParams as $invalidCreateDTParam) {

				if (array_search($invalidCreateDTParam, $config)  !== false) {
					$ret[] = $field;
				}
			}
		}

		return array_diff(array_keys($fieldConfig), $ret);
	}

	abstract protected static function _stExists($objId);

	static function stExists($objId) {

		return ObjectPersistent::_stExists($objId);
	}

	abstract protected static function _stCreate($params);

	static function stCreate($params) {

		// Para ello no se debe de llamar NUNCA a DBObject::stCreate si no con la clase del objeto a crear
		$class = get_called_class();

		$createParams = ObjectPersistent::stGetValidCreateParams($class);

		$invalidParams = array_diff(array_keys($params), $createParams);
		if (count($invalidParams) !== 0) {
			// TODO warning y crear error
			return false;
		}

		return static::_stCreate($params);
	}

	abstract protected static function _stUpdate($params);

	static function stUpdate($params) {

		return ObjectPersistent::_stUpdate($params);
	}
}
