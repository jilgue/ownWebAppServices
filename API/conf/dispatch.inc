<?php

$config["/apirest/(?<object>.*)/(?<function>.*)\.json"] = array("class" => "APIRESTResponseJSONPage",
);

$config["/apiconf/(?<object>.*)/(?<function>.*)\.json"] = array("class" => "APIRESTConfigJSONPage",
);
