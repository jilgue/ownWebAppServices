<?php

/**
 * Carga inicial
 */
class LoadConfig {

	/**
	 * Obtiene la tabla con todas las rutas disponibles
	 */
	static function stGetDispatchTable() {

		$cmd = "find " . $GLOBALS["path"] . " -name dispatch.inc";

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

		static $stCache = array();

		$class = $class != "" ? $class : LoadConfig::stGetPreviousCalledClass();

		// Quiza ya haya sido cargado
		if (isset($stCache[$class][$var])) {
			return $stCache[$class][$var];
		}

		$path = LoadConfig::_stGetConfigPath($class);

		if ($path === false) {
			return false;
		}

		$config = LoadConfig::_stRequireConfig($path);

		if (isset($config[$class][$var])) {
			$stCache[$class][$var] = $config[$class][$var];
			return $config[$class][$var];
		}

		return false;
	}

	private static function _stRequireConfig($path) {

		static $stCache = array();

		if (isset($stCache[$path])) {
			return $stCache[$path];
		}

		require_once $path;

		$stCache[$path] = $config;

		return $config;
	}

	private static function _stGetConfigPath($class) {

		foreach (LoadInit::stPackagesLoad() as $package) {

			if (preg_match("/" . $package . "[[:alnum:]]{0,}/", $class, $match)) {

				$path = $GLOBALS["path"] . $package . "/conf/config.inc";
				if (is_file($path)) {
					return $path;
				}
			}
		}

		return false;
	}

	static function stGetPreviousCalledClass() {

		$traces = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 3);

		// 0 soy yo, 1 donde me llaman, 2 la que quiero saber xD
		return $traces[2]["class"];
	}
}
