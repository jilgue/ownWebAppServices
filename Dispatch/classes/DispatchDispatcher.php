<?php

/**
 * Dispatching de URLs
 */
class DispatchDispatcher {

	public $request;

	static function stProcessRequest($URL) {

		$_URL = $URL;
		// Limpiamos la url de los parametros get
		if (preg_match("/(.*)\?/", $URL, $match) === 1) {
			$_URL = $match[1];
		}

		$dispatchTable = LoadConfig::stGetDispatchTable();

		//DispatchDispatcher::$request = $URL;

		foreach ($dispatchTable as $urlMatch => $config) {

			if (preg_match("@" . $urlMatch . "@", $_URL, $match) === 1) {
				// TODO mandar a un output
				$obj = call_user_func_array(array($config["class"], "stVirtualConstructor"), array(DispatchDispatcher::stGetUrlArg($URL, $config)));
				return $obj->getOutput();
			}
		}

		echo DispatchDispatcher::stPageNotFound();
	}

	static function stGetUrlArg($URL, $config) {

		$ret = array();
		foreach ($_GET as $param => $value) {

			// No soportamos page.html?myarray[]=1
			if (is_array($value)) {
				continue;
			}

			// TODO soporte para optionalGETParams y obligatoryGETParams

			$ret[$param] = $value;
		}

		return $ret;
	}

	static function stPageNotFound() {

		$output = '<!doctype html>
<html>
        <head>
                <meta charset="utf-8">
                <title>404 Not Found</title>
        </head>
        <body>
                <p>404 Not Found</p>
                <p><a href="/">Home page</a></p>
        </body>
</html>';
		return $output;
	}
}
