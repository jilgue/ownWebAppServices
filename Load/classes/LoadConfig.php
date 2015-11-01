<?php

/**
 * Carga inicial
 */
class LoadConfig {

	/**
	 * Obtiene la tabla con todas las rutas disponibles
	 */
	static function stGetDispatchTable() {

		$cmd = "find " . $GLOBALS["config"]["path"] . " -name dispatch.inc";

		exec($cmd, $out);

		$ret = array();
		foreach ($out as $path) {

			require_once $path;

			$ret = array_merge($ret, $config);
		}

		return $ret;
	}

	/**
	 * Obtiene el config de una clase
	 */
	static function stGetConfigClass($class = "") {

		$class = $class != "" ? $class : LoadConfig::stGetPreviousCalledClass();

		if (($path = LoadConfig::_stGetConfigPath($class)) === false) {
			return false;
		}
		require_once $path;

		if (isset($config[$class])) {
			return $config[$class];
		}
		return false;
	}

	/**
	 * Obtiene el config de una clase
	 */
	static function stGetConfigVar($var, $class = "") {

		$class = $class != "" ? $class : LoadConfig::stGetPreviousCalledClass();

		if (($path = LoadConfig::_stGetConfigPath($class)) === false) {
			return false;
		}
		require_once $path;

		if (isset($config[$var])) {
			return $config[$var];
		}
		return false;
	}

	/**
	 * Obtiene el config de una clase
	 */
	static function stGetConfigVarClass($var, $class = "") {

		$class = $class != "" ? $class : LoadConfig::stGetPreviousCalledClass();

		// Quiza ya haya sido cargado
		if (isset($GLOBALS["config"][$class][$var])) {
			return $GLOBALS["config"][$class][$var];
		}

		if (($path = LoadConfig::_stGetConfigPath($class)) === false) {
			return false;
		}

		require_once $path;

		$GLOBALS["config"] = array_merge(isset($GLOBALS["config"]) ? $GLOBALS["config"] : array(), $config);

		if (isset($config[$class][$var])) {
			return $config[$class][$var];
		}

		return false;
	}

	private static function _stGetConfigPath($class) {

		$cmd = "find " . $GLOBALS["config"]["path"] . " -name $class.php";

		exec($cmd, $out);

		if (count($out) != 1) {
			var_dump("clase repetida, mal");die;
		}

		if (preg_match("@\./[[:alnum:]]{1,}@", reset($out), $match)) {

			return reset($match) . "/conf/config.inc";
		}

		return false;
	}

	static function stGetPreviousCalledClass() {

		$traces = debug_backtrace();

		// 0 soy yo, 1 donde me llaman, 2 la que quiero saber xD
		return $traces[2]["class"];
	}
}
