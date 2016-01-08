<?php

$config["User"]["objField"]["userId"] = array("DT" => "DataTypeIdDT",
					      "DTParams" => array("identifier" => true));

$config["User"]["objField"]["nick"] = array("DT" => "DataTypeStringDT",
					    "DTParams" => array("maxLength" => 25));

$config["User"]["objField"]["password"] = array("DT" => "DataTypeStringDT",
						"DTParams" => array("maxLength" => 40));
