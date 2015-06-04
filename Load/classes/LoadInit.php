<?php

/**
 * Carga inicial
 */
class LoadInit {

	/**
	 * Autocarga la clase $class
	 */
	static function stGetClassPath($class) {

		$cmd = "find . -name " . $class . ".php";

		exec($cmd, $out);

		if (count($out) == 1) {
			return $out[0];
		}
		echo "$class dont exist";die;
	}

	/**
	 * Autocarga la clase $class
	 */
	static function stAutoload($class) {

		$classPath = LoadInit::stGetClassPath($class);

		if ($classPath !== false) {
			require_once $classPath;
		}
	}

}

spl_autoload_register(array("LoadInit", "stAutoload"));

DispatchDispatcher::stProcessRequest($_SERVER["REQUEST_URI"]);
