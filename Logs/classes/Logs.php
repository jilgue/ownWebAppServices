<?php

/**
 *
 */

class Logs {

	static function stEcho($msg) {

		echo date('Y/m/d H:i:s ') . "$msg\n";
		return;
	}
}
