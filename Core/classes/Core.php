<?php

/**
 *
 */
class Core {

	static function stGetClassesList($type) {

		$packageList = LoadInit::stPackagesLoad();

		$raidPath = $GLOBALS["path"];

		$ret = array();
		foreach ($packageList as $package) {

			$path = $raidPath . $package . "/" . $type . "/";
			if (!file_exists($path)) {
				continue;
			}
			$cmd = "ls " . $path . $package . "*";

			// $out no se borra se mantiene en el entorno de la función
			exec($cmd, $out);
		}

		foreach ($out as $scriptPath) {
			preg_match("#.*/([A-z]{1,})#", $scriptPath, $match);
			$ret[] = $match[1];
		}

		return $ret;
	}
}
