<?php

/**
 *
 */
class HTTPSession {

	static function stCreate($params) {

		session_start();

		foreach ($params as $param => $value) {
			$_SESSION[$param] = $value;
		}
		// TODO control de errores
		return true;
	}
}