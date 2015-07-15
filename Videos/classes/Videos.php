<?php

/**
 * Clase objeto de imágenes
 */
class Videos extends DBObject {

	// ODMPPHE
	static $table = "videos";

	function getUploadedDefaultValue($params) {

		return "0";
	}
}
