<?php

$config["DBMySQLConnection"] = array("host" => "localhost",
				     "port" => "3306",
				     "user" => "bam_uploads",
				     "password" => "b4m",
				     "database" => "bam_uploads",
);

$config["DBMySQLConnection"]["objField"]["table"] = array("DT" => "DataTypeAllValueDT",
							  "DTParams" => array("identifier" => true));