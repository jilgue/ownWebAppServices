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
	static function stGetConfigClass($class) {


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
}