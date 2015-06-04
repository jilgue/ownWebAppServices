<?php

/**
 * Clase objecto
 */
class Object {

	// Campos publicos de la clase
	var $fieldConfig = array();

	protected function __construct($params = array()) {

		$this->_autoloadParams($params);
	}

	private function _autoloadParams($params) {

		foreach ($params as $param => $value) {

			$this->$param = $value;

			// Ademas guardamos si estan en la configuración del objeto
			if (isset($this->fieldConfig[$param])
			    && $this->_checkType($this->fieldConfig[$param], $value)) {
				$this->fieldConfig[$param] = $value;
			}
		}
	}

	private function _checkType($type, $value) {

		return preg_match("#" . $type . "#", $value, $match) === 1;
	}

	static function stVirtualConstructor($params = array()) {

		$class = get_called_class();
		$args = func_get_args();
		$numArgs = count($args);

		if ($numArgs == 1) {
			if (is_array($args[0])) {
				// Ok, nos han llamado de la forma "normal": un único parámetro de tipo array
				return new $class($args[0]);
			}
		} else if ($numArgs == 0) {
			return new $class(array());
		}

		// No tengo muy claro cuando puede pasar este caso:
		var_dump("mal, muy mal");die;
	}
}
