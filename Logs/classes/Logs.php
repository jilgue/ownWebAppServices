<?php

/**
 *
 */

class Logs {

	static function stEchoDate($msg) {

		echo date('Y/m/d H:i:s ') . "$msg\n";
		return;
	}

	static function stEcho($msg) {

		echo "$msg\n";
		return;
	}

	static function stFatal($mgs) {

		Logs::stEcho($mgs);
		// TODO hacer fatal de verdad xD
		die;
	}
}
