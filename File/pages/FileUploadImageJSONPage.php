<?php
/**
 * Dispatching de URLs
 */
class FileUploadImageJSONPage extends APIJSONPage {

	static $objField = array("file" => ".*",
				 "idUser" => ".*",
				 );

	protected function __construct($params = array()) {

		// MEGA TRAPI
		$params["token"] = "ce6cf1c81095f72fa961ece85db5f5eb0a1509fa";
		parent::__construct($params);
	}

	protected function _getResponse() {

		preg_match("/data:image\/([a-z]{1,});base64,/", $this->file->dataURL, $type);
		$type = $type[1];
		$data = base64_decode(str_replace('data:image/jpeg;base64,', '', $this->file->dataURL));
		$file = "/var/www/public/" . time() . "-" . $this->idUser . "-webapp." . $type;
		return file_put_contents($file, $data);
	}
}
