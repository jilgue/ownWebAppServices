<?php

/**
 * Carga inicial
 */
class LoadConfig {

	/**
	 * Autocarga la clase $class
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
}