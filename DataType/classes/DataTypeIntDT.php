<?php
/**
 *
 */
class DataTypeIntDT extends DataType {

	var $type = "is_int";
	var $DBType = "INT";
	var $regex = "\d+";
	var $maxLength = 5;

	protected function _getDBColumnType($params) {

		return "$params[DBType] ($params[maxLength]) NOT NULL";
	}
}
