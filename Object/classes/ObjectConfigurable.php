<?php

/**
 *
 */
class ObjectConfigurable extends Object {


	static function stGetFieldsConfig() {

		static $stCache = array();

		$class = $calledClass = get_called_class();

		if (isset($stCache[$calledClass])) {
			return $stCache[$calledClass];
		}

		$ret = array();
		do {
			$objField = LoadConfig::stGetConfigVarClass("objField", $class);

			if ($objField !== false) {
				$ret = array_merge($objField, $ret);
			}

		} while (($class = get_parent_class($class)) != false);

		$stCache[$calledClass] = $ret;

		return $ret;
	}
}
