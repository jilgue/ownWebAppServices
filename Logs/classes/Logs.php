<?php

/**
 *
 */

class Logs {

	static function stEcho($msg) {

		echo date('Y/m/d H:i:s ') . "$msg\n";
		return;
	}

	static function stFatal($mgs) {

		Logs::stEcho($mgs);
		// TODO hacer fatal de verdad xD
		die;
	}
}
