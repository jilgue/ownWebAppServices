<?php

/**
 *
 */
abstract class ObjectPersistent extends ObjectConfigurable {

	abstract private static function _stExists($objId);

	static function stExists($objId) {

		return ObjectPersistent::_stExists($objId);
	}

	abstract private static function _stCreate($params);

	static function stCreate($params) {

		return ObjectPersistent::_stCreate($params);
	}

	abstract private static function _stUpdate($params);

	static function stUpdate($params) {

		return ObjectPersistent::_stUpdate($params);
	}
}