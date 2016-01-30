<?php

/**
 * Clase objeto de imágenes
 */
class User extends DBObject {

	const ERROR_CODE_USER_NOT_EXISTS = "User::ERROR_CODE_USER_NOT_EXISTS";
	const ERROR_CODE_PASSWORD_NOT_MATCH = "User::ERROR_CODE_PASSWORD_NOT_MATCH";

	protected static function _stCreate($params) {

		// Ya han sido validados asi que debería de existir
		$params["password"] = sha1($params["password"]);

		return parent::_stCreate($params);
	}

	static function stLogin($userName, $password) {

		$password = sha1($password);

		$filters = array("userName" => array($userName));

		$user = UserSearch::stVirtualConstructor(array("filters" => $filters,
							       "limit" => 1))->getResult();


		if (!$user) {
			LogsErrors::stCreate(array("errorCode" => User::ERROR_CODE_USER_NOT_EXISTS,
						   "param" => "userName",
						   "value" => $userName));
			return false;
		}

		if ($user["password"] != $password) {
			LogsErrors::stCreate(array("errorCode" => User::ERROR_CODE_PASSWORD_NOT_MATCH,
						   "param" => "password"));
			return false;
		}

		$token = sha1($password . time());

		if (!HTTPSession::stCreate(array("userName" => $userName,
						 "password" => $password,
						 "token" => $token))) {
			return false;
		}

		return array("token" => $token);
	}
}
