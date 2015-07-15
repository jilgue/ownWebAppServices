<?php

/**
 * Clase objeto de imágenes
 */
class Image extends DBObject {

	// ODMPPHE
	static $table = "images";

	function getUploadedDefaultValue($params) {

		return "0";
	}
}
