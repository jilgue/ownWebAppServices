<?php
/**
 *
 */
class DataTypeIdDT extends DataTypeIntDT {

	var $optional = false;
	var $identifier = array(false => "",
				true => "UNSIGNED NOT NULL AUTO_INCREMENT");


	protected function _getDBColumnType($params) {

		return "$params[DBType] ($params[maxLength]) $params[identifier]";
	}
}
