<?php

/**
 * Carga inicial
 */
class LoadConfig {

	/**
	 * Obtiene la tabla con todas las rutas disponibles
	 */
	static function stGetDispatchTable() {

		$cmd = "find . -name dispatch.inc";

		exec($cmd, $out);

		$ret = array();
		foreach ($out as $path) {

			require $path;

			$ret = array_merge($ret, $config);
		}

		return $ret;
	}

	/**
	 * Obtiene el config de una clase
	 */
	static function stGetConfigClass($class = "") {

		$class = $class != "" ? $class : LoadConfig::stGetPreviousCalledClass();

		$cmd = "find . -name $class.php";

		exec($cmd, $out);

		if (count($out) != 1) {
			var_dump("clase repetida, mal");die;
		}

		if (preg_match("@\./[[:alnum:]]{1,}@", reset($out), $match)) {
			require reset($match) . "/conf/config.inc";
			if (isset($config[$class])) {
				return $config[$class];
			}
			return false;
		}

		return false;
	}

	/**
	 * Obtiene el config de una clase
	 */
	static function stGetConfigVar($var, $class = "") {

		$class = $class != "" ? $class : LoadConfig::stGetPreviousCalledClass();

		$cmd = "find . -name $class.php";

		exec($cmd, $out);

		if (count($out) != 1) {
			var_dump("clase repetida, mal");die;
		}

		if (preg_match("@\./[[:alnum:]]{1,}@", reset($out), $match)) {
			require reset($match) . "/conf/config.inc";
			if (isset($config[$var])) {
				return $config[$var];
			}
			return false;
		}

		return false;
	}

	static function stGetPreviousCalledClass() {

		$traces = debug_backtrace();

		// 0 soy yo, 1 donde me llaman, 2 la que quiero saber xD
		return $traces[2]["class"];
	}
}