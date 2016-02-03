<?php
/**
 *
 */
class UserPasswordDT extends DataTypeStringDT {

	var $type = "is_string";
	var $DBType = "VARCHAR";
	var $regex = ".*";
	var $maxLength = 250;
	var $inputType = "password";
}
