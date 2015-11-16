<?php

/**
 *
 */
class Cache {


	/**
	 * Lo usamos para definir getter y setters automáticamente
	 */
	public function __call($method, $arguments) {
		var_dump($method, $arguments);die;
	}
}
