<?php

$config["fileuploadimage.json"] = array("class" => "FileUploadImageJSONPage",
					"method" => json_decode(file_get_contents("php://input")),
					);
