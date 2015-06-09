<?php

/**
 * Clase objeto de imágenes
 */
class Image extends DBObject {

	// ODMPPHE
	static $table = "images";

	static function stCreate() {
		var_dump(Image::$objField);die;
		$image = Image::stVirtualConstructor("1");
		var_dump($image);die;
	}
}
