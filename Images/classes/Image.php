<?php

/**
 * Clase objeto de imágenes
 */
class Image extends DBObject {

	// ODMPPHE
	static $table = "images";

	function getCreateDateDefaultValue($params) {

		return date("Y-m-d", time());
	}

	function getUploadedDefaultValue($params) {

		return "0";
	}

}
