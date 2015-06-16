<?php

$config["(?<action>.*)/(?<object>.*)\.json"] = array("class" => "APITest",
					       "optionalGETParams" => array("algo" => ".+")
					       );
