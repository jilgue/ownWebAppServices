<?php
/**
 *
 */
class DataTypeBoolDT extends DataType {

	var $type = "is_bool";
	var $DBType = "BOOL";
	var $regex = "true|false";
	var $maxLength = false;

}
