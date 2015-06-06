<?php

/**
 * Clase objecto
 */
class Object {

	// Campos publicos de la clase
	var $objField = array();

	protected function __construct($params = array()) {

		$this->_autoloadParams($params);
	}

	private function _autoloadParams($params) {

		foreach ($params as $param => $value) {

			$this->$param = $value;

			// Ademas guardamos si estan en la configuración del objeto
			if (isset($this->objField[$param])
			    && $this->_checkType($this->objField[$param], $value)) {
				$this->objField[$param] = $value;
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

	/**
	 * Magic method __call: PHP llama automáticamente a este método para todo método que no esté definido explícitamente
	 * Lo usamos para definir getter y setters automáticamente
	 */
	public function __call($method, $arguments) {

		if (!preg_match("#^(get|set|_get|_set)(.+)$#", $method, $matches)) {
			// Método desconocido
			return null;
		}

		$op = $matches[1];
		$capturedField = $matches[2];

		switch ($op) {
		case "get":
		case "_get":
			if (count($arguments) != 0) {
				return null;
			}

		return $this->_getter(lcfirst($capturedField));
		break;

		case "set":
		case "_set":
			if (count($arguments) != 1) {
				return null;
			}

		return $this->_setter(lcfirst($capturedField), $arguments[0]);
		break;

		default:
			return null;
			break;
		}
	}

	private function _getter($field) {

		if (isset($this->$field)) {
			return $this->$field;
		}

		return null;
	}

	private function _setter($field, $value) {

		if (isset($this->$field)) {
			return (bool) $this->$field = $value;
		}
		return null;
	}
}
