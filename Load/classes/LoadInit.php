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
		}
	}

	static function stPackagesLoad() {

		if (isset($GLOBALS["packageList"])) {
			return $GLOBALS["packageList"];
		}

		// Trapi autoload del config de Load
		require_once( dirname( __FILE__ ) . '/../conf/config.inc');
		$cmd = "ls " . $config["path"];

		exec($cmd, $out);

		// Quitamos el último que será el index.php al ser el único archivo
		array_pop($out);

		// Guardamos en "cache" la lista de paquetes
		$GLOBALS["path"] = $config["path"];
		$GLOBALS["packageList"] = $out;
		return;
	}
}

spl_autoload_register(array("LoadInit", "stAutoload"));

LoadInit::stPackagesLoad();