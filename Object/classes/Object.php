<?php

/**
 * Clase objecto
 */
abstract class Object {

	// Se guarda toda la jerarquia de la clase
	var $hierarchy = array();

	public $objFields = array();

	var $params = array();

	var $ok = true;

	protected function __construct($params = array()) {

		$this->_loadHierarchy();

		$this->_autoloadParams($params);
	}

	private function _loadHierarchy() {

		$class = get_class($this);
		$this->hierarchy = Object::stGetHierarchy($class);
	}

	private function _autoloadParams($params) {

		if (is_array($params)) {
			foreach ($params as $param => $value) {

				$this->$param = $value;
			}
		}

		$this->params = $params;

		foreach ($this->objFields as $objField => $value) {
			if (!isset($this->$objField)) {
				$this->$objField = $value;
			}
		}
	}


	static function stVirtualConstructor($params = array()) {

		$class = get_called_class();
		$args = func_get_args();
		$numArgs = count($args);

		if ($numArgs == 1) {
			if (is_array($args[0])) {

				if ($args[0] == $params) {
					return new $class($params);
				}

				// Ok, nos han llamado de la forma "normal": un único parámetro de tipo array
				return new $class(array_merge($args[0], $params));
			}
		}

		if ($numArgs == 0) {
			return new $class($params);
		}

		// Si no es así pasamos todos los parametros y cada clase sabra que hacer con ellos, por el bien que le trae xD
		if (is_array($params)) {
			return new $class(array_merge($args, $params));
		}

		return new $class($args);
	}

	static function stGetObjFields() {

		$class = get_called_class();

		$objFields = array();
		foreach (Object::stGetHierarchy($class) as $class) {

			$reflex = new ReflectionClass($class);

			$properties = $reflex->getDefaultProperties();

			$objFields = array_merge($objFields, $properties["objFields"]);
		}
		return $objFields;
	}

	static function stGetHierarchy($class) {

		$ret = array($class);

		while (($class = get_parent_class($class)) !== false) {
			$ret[] = $class;
		}

		return $ret;
	}

	/**
	 * Lo usamos para definir getter y setters automáticamente
	 */
	public function __call($method, $arguments) {

		if (preg_match("#^(.+)_cached$#", $method, $matches)) {
			// Funcion que va ir cacheada
			return $this->_getCachedFuncion($matches[1], $arguments);
		}

		return $this->_callSetGet($method, $arguments);
	}

	private function _callSetGet($method, $arguments) {

		if (!preg_match("#^(get|set|_get|_set)(.+)$#", $method, $matches)) {
			// Método desconocido
			return null;
		}

		$op = $matches[1];
		$capturedField = lcfirst($matches[2]);

		if (!isset($this->$capturedField)) {
			return null;
		}

		switch ($op) {
		case "get":
		case "_get":
			if (count($arguments) != 0) {
				return null;
			}

			return $this->_getter($capturedField);
		break;

		case "set":
		case "_set":
			if (count($arguments) != 1) {
				return null;
			}

			return $this->_setter($capturedField, $arguments[0]);
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

		return false;
	}

	private function _setter($field, $value) {

		if (isset($this->$field)) {
			$this->$field = $value;
			return (bool) $this->$field === $value;
		}
		return false;
	}

	function multiSetter($params) {

		foreach ($params as $field => $value) {
			if (!$this->_setter($field, $value)) {
				return false;
			}
		}

		return true;
	}

	private function _getCachedFuncion($function, $args) {

		static $stCache = array();

		// TODO pasar en args el tiempo de cache y quitar de los args de la funcion
		$cacheId = md5(serialize($function) . serialize($args));

		// TODO cachear de verdad
		if (isset($stCache[$cacheId])) {
			return $stCache[$cacheId];
		}

		$ret = call_user_func_array(array($this, $function), $args);

		$stCache[$cacheId] = $ret;

		return $ret;
	}
}
