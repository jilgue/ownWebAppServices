<?php

/**
 * Clase objecto
 */
class Object {

	// Campos publicos de la clase
	static $objField = array();
	var $params = array();

	protected function __construct($params = array()) {

		$this->_autoloadParams($params);
	}

	private function _autoloadParams($params) {

		foreach ($params as $param => $value) {

			$this->$param = $value;

			// Ademas guardamos si estan en la configuración del objeto
			if (isset($this->objField[$param])) {
				$this->objField[$param] = $value;
				// TODO comprobaciones del type
				//&& isset($this->objField[$param]["type"])
				//&& $this->_checkType($this->objField[$param][["type"]], $value)) {
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

		// Añadimos cosas útiles a params y se lo pasamos a la clase que ha sido llamada
		$params = array($params, "class" => $class);

		if ($numArgs == 1) {
			if (is_array($args[0])) {
				// Ok, nos han llamado de la forma "normal": un único parámetro de tipo array
				return new $class(array_merge($args[0], $params));
			}
		} else if ($numArgs == 0) {
			return new $class($params);
		}

		// Si solo nos han pasado un argumento suponemos que es el id del objeto si esta configurado en dicho objeto
		if (count($args) == 1
		    && isset(reset($class::$objField)["key"])
		    && reset($class::$objField)["key"] == "id") {

			$args = array(key($class::$objField) => reset($args));
			return new $class(array_merge($args, $params));
		}

		// Si no es así pasamos todos los parametros y cada clase sabra que hacer con ellos, por el bien que le trae xD
		return new $class(array_merge($args, $params));
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
