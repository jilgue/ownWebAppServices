<?php

/**
 *
 */
class Core {

	static function stGetClassesList($type) {

		$packageList = LoadInit::stPackagesLoad();
		var_dump($packageList);die;
	}
}
