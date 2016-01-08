<?php

/**
 * Clase objeto de imágenes
 */
class User extends DBObject {

	protected static function _stCreate($params) {

		// Ya han sido validados asi que debería de existir
		$params["password"] = sha1($params["password"]);

		return parent::_stCreate($params);
	}

	static function stLogin($nick, $password) {

		$password = sha1($password);

		$query = "select * from user where nick = '$nick' AND password = '$password'";

		return (bool) DBMySQLConnection::stVirtualConstructor()->query($query)->num_rows;
	}
}
