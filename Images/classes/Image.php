<?php

/**
 * Clase objeto de imágenes
 */
class Image extends DBObject {

	static $objField = array("imageId" => array("key" => "id"));
	// ODMPPHE
	static $table = "images";

	static function stCreate() {

		$image = Image::stVirtualConstructor();
		var_dump($image);die;
	}
}
