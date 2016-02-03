<?php
/**
 *
 */
class DataTypeStringDT extends DataType {

	var $type = "is_string";
	var $DBType = "VARCHAR";
	var $regex = ".*";
	var $maxLength = 250;
	var $inputType = "input";

	protected function _getDBColumnType($params) {

		return "$params[DBType] ($params[maxLength]) NOT NULL";
	}
}
