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
				$obj = call_user_func_array(array($config["class"], "stVirtualConstructor"), array(DispatchDispatcher::stGetUrlArg($URL, $urlMatch, $config)));
				return $obj->printOutput();
			}
		}

		echo DispatchDispatcher::stPageNotFound();
	}

	static function stGetUrlArg($URL, $urlMatch, $config) {

		$URL = DispatchDispatcher::stRelativizeUrl($URL);

		$class = $config["class"];

		$ret = array();
		if (preg_match_all("%<(.[^>]*)%", $urlMatch, $match)) {

			preg_match("%" . $urlMatch . "%", $URL, $match);

			foreach ($match as $param => $value) {

				if (isset($class::$objField[$param])) {
					$ret[$param] = $value;
				}
			}
		}

		if (isset($config["method"])) {
			$method = $config["method"] == "POST" ? $_POST : $_GET;
		} else {
			return $ret;
		}

		foreach ($method as $param => $value) {

			// No soportamos page.html?myarray[]=1
			if (is_array($value)) {
				continue;
			}

			// TODO soporte para optionalGETParams y obligatoryGETParams

			$ret[$param] = $value;
		}

		return $ret;
	}

	static function stRelativizeUrl($URL) {

		$urlBase = LoadConfig::stGetConfigClass()["urlBase"];

		return preg_replace("%$urlBase%", "", $URL);
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
