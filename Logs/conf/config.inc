<?php

$config["LogsErrors"]["objField"]["errorCode"] = array("DT" => "DataTypeStringDT",
						       "DTParams" => array("obligatory" => true));

$config["LogsErrors"]["objField"]["degree"] = array("DT" => "DataTypeStringDT",
						    "DTParams" => array("validValues" => array("normal"),
									"default" => "normal"));

$config["LogsErrors"]["objField"]["message"] = array("DT" => "DataTypeStringDT",
						     "DTParams" => array("defaultEval" => 'return $this->_getMessage($params);'));

$config["LogsErrors"]["objField"]["class"] = array("DT" => "DataTypeStringDT",
						   "DTParams" => array("defaultEval" => 'return $this->_getClass();'));

$config["LogsErrors"]["objField"]["function"] = array("DT" => "DataTypeStringDT",
						      "DTParams" => array("defaultEval" => 'return $this->_getFunction();'));

$config["LogsErrors"]["objField"]["param"] = array("DT" => "DataTypeStringDT");

$config["LogsErrors"]["objField"]["value"] = array("DT" => "DataTypeStringDT");
