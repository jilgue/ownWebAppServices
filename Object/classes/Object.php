<?php

/**
 * Clase objecto
 */
class Object {

	// Campos publicos de la clase
	static $objField = array();
	static $hierarchy = array();

	var $params = array();

	protected function __construct($params = array()) {

		$this->_loadHierarchy();
		$this->_loadObjFieldConfig();
		$this->_mergeObjField();
		$this->_autoloadParams($params);
	}

	private function _loadHierarchy() {

		$class = get_class($this);
		$this::$hierarchy = array($class);
		while (($class = get_parent_class($class)) !== false) {
			$this::$hierarchy[] = $class;
		}
		return;
	}

	private function _loadObjFieldConfig() {

		$class = reset($this::$hierarchy);

		$config = LoadConfig::stGetConfigVarClass("objField", $class);

		if ($config) {
			$this::$objField = array_merge($config, $this::$objField);
		}
	}

	private function _mergeObjField() {

		foreach ($this::$hierarchy as $class) {
			$this::$objField = array_merge($class::$objField, $this::$objField);
		}
		return;
	}

	private function _autoloadParams($params) {

		// Si no existe $this->objField es que ninguna clase hija lo ha tocado, lo cargamos nosotros
		if (!isset($this->objField)) {
			$this->objField = $this::$objField;
		}

		if (!isset($this->hierarchy)) {
			$this->hierarchy = $this::$hierarchy;
		}

		foreach ($params as $param => $value) {

			$this->$param = $value;

			// Ademas guardamos si estan en la configuración del objeto
			if (isset($this->objField[$param])
			    && isset($this->objField[$param]["type"])
			    && $this->_checkType($this->objField[$param]["type"], $value)) {
				$this->objField[$param]["value"] = $value;
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
	 * Lo usamos para definir getter y setters automáticamente
	 */
	public function __call($method, $arguments) {

		if (preg_match("#^(.+)_cached$#", $method, $matches)) {
			// Funcion que va ir cacheada
			return $this->_getCachedFuncion($matches[1], $arguments);
		}

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

		return false;
	}

	private function _setter($field, $value) {

		if (isset($this->$field)) {
			return (bool) $this->$field = $value;
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
