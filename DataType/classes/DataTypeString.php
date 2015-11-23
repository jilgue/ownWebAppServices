<?php
/**
 *
 */
class DataTypeString extends DataType {

	static $objField = array("type" => "is_string",
				 "DBType" => "VARCHAR",
				 "regex" => ".*",
				 "maxLength" => 250,
	);
}
