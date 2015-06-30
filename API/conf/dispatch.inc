<?php

$config["create/(?<object>.*)\.json"] = array("class" => "APICreteObjJSONPage",
					      "method" => "POST",
					      );

$config["search/(?<object>.*)\.json"] = array("class" => "APISearchJSONPage",
					      "method" => "GET",
					      );
