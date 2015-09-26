<?php

/**
 * Carga inicial
 */
class LoadInit {

	/**
	 * Autocarga la clase $class
	 */
	static function stGetClassPath($class, $caseInsensitive = false) {

		if (!$caseInsensitive) {
			$cmd = "find . -name " . $class . ".php";
		} else {
			$cmd = "find . -iname " . $class . ".php";
		}

		exec($cmd, $out);

		if (count($out) == 1) {
			return $out[0];
		}
		echo "$class dont exist";die;
	}

	static function stGetClassCaseInsensitive($class) {

		$path = LoadInit::stGetClassPath($class, true);

		preg_match("%/(.[^/]*)\.php%", $path, $match);
		return $match[1];
	}

	/**
	 * Autocarga la clase $class
	 */
	static function stAutoload($class) {

		$classPath = LoadInit::stGetClassPath($class);

		if ($classPath !== false) {
			require_once $classPath;
			// Si existe $class::$table es una clase de DBObject
			if (isset($class::$table)) {
				// Autocargamos los campos de $objField
				$class::$objField = DBObject::stGetObjField($class::$table);
			}
		}
	}

}

spl_autoload_register(array("LoadInit", "stAutoload"));

