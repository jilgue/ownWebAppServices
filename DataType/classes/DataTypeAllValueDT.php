<?php
/**
 *
 */
class DataTypeAllValueDT extends DataType {

	var $type = "";
	var $DBType = "";
	var $regex = "";
	var $maxLength = 250;

	function isValidValue($value) {
		return true;
	}
}
