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
			$cmd = "find " . $GLOBALS["path"] . " -name " . $class . ".php";
		} else {
			$cmd = "find " . $GLOBALS["path"] . " -iname " . $class . ".php";
		}

		exec($cmd, $out);

		if (count($out) == 1) {
			return $out[0];
		}
		echo "$class dont exist";die;
	}

	static function stGetClassCaseInsensitive($class) {

		$path = static::stGetClassPath($class, true);

		preg_match("%/(.[^/]*)\.php%", $path, $match);
		return $match[1];
	}

	/**
	 * Autocarga la clase $class
	 */
	static function stAutoload($class) {

		$classPath = static::stGetClassPath($class);

		if ($classPath !== false) {
			require_once $classPath;
		}
	}

	static function stPackagesLoad() {

		static $stCache = array();

		if (isset($stCache["packageList"])) {
			return $stCache["packageList"];
		}

		// Trapi autoload del config de Load
		require_once( dirname( __FILE__ ) . '/../conf/config.inc');
		$cmd = "ls -d " . $config["path"] . "*/";

		exec($cmd, $out);

		$ret = array();
		foreach ($out as $path) {
			preg_match("#.*/([A-z]{1,}[^/])#", $path, $match);
			$ret[] = $match[1];
		}

		// Guardamos en "cache" la lista de paquetes y el path
		$GLOBALS["path"] = $config["path"];
		$stCache["packageList"] = $ret;

		return $ret;
	}
}

spl_autoload_register(array("LoadInit", "stAutoload"));

LoadInit::stPackagesLoad();