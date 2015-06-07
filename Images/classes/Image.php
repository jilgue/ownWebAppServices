<?php

/**
 * Clase objeto de imágenes
 */
class Image extends DBObject {

	static $objField = array("imageId" => array("key" => "id"));
	// ODMPPHE
	static $table = "images";

	static function stCreate() {

		$objField = Image::stVirtualConstructor()->objField;
		var_dump($objField);die;
	}
}
