<?php

$config["create/(?<object>.*)\.json"] = array("class" => "APICreteObjJSONPage",
					      "method" => $_POST,
					      );

$config["search/(?<object>.*)\.json"] = array("class" => "APISearchJSONPage",
					      "method" => $_GET,
					      );
