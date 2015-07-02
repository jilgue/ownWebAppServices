<?php
/**
 * Dispatching de URLs
 */
class UserLoginAPIJSONPage extends APIJSONPage {

	static $objField = array("idUser" => "\d",
				 "password" => ".*",
				 );
	private function _login() {

		// TODO llevar password a datatype
		$query = "SELECT password from " . User::$table . " where id_user=" . $this->idUser;

		$res = DBMySQLConnection::stVirtualConstructor(User::$table)->select($query);

		if (is_null($res)) {

			$this->_setError("USER_DONT_EXIST", "User $this->idUser dont exist");
			return "";
		}

		if ($res["password"] == $this->password) {
			return "login";
		}

		$this->_setError("PASS_DONT_MATCH", "Password dont match");
		return "";
	}

	protected function _getResponse() {

		return $this->_login();
	}
}
