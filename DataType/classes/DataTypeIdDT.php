<?php
/**
 *
 */
class DataTypeIdDT extends DataTypeIntDT {

	var $optional = false;

	protected function _getDBColumnType($params) {

		return "$params[DBType] ($params[maxLength]) UNSIGNED NOT NULL AUTO_INCREMENT";
	}
}
