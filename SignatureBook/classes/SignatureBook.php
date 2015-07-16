<?php

/**
 * Clase objeto de imágenes
 */
class SignatureBook extends DBObject {

	// ODMPPHE
	static $table = "signature_book";

	static function stCreate($params) {

		// Comprobamos que el usuario no tiene ya una firma
		$query = "SELECT id_sign from signature_book where user_sign='" . $params["userSign"] . "'";
		$res = DBMySQLConnection::stVirtualConstructor(SignatureBook::$table)->select($query);

		if (is_null($res)) {

			// No existe, creamos de nuevas
			return parent::stCreate($params);
		}

		// Si hemos llegado aquí es un update
		$params["idSign"] = $res["id_sign"];

		if (SignatureBook::stUpdate($params)) {
			return array("idSign" => $params["idSign"]);
		} else {
			return "Fail to update";
		}
	}
}
