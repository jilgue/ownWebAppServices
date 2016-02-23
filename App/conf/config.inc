<?php

$config["App"]["objField"]["appId"] = array("DT" => "DataTypeIdDT",
					    "DTParams" => array("identifier" => true));

$config["App"]["objField"]["appName"] = array("DT" => "DataTypeStringDT",
					      "DTParams" => array("maxLength" => 25));

$config["App"]["objField"]["appActive"] = array("DT" => "DataTypeBoolDT",
						"DTParams" => array("default" => true));
