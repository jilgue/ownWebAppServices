<?php
/**
 * Dispatching de URLs
 */
class UserLoginAPIJSONPage extends APIJSONPage {

	static $objField = array("user" => ".*",
				 "password" => ".*",
				 );

	private function _login() {

		// Permitimos el id y el email
		if (User::stExists(array("idUser" => $this->user))) {
			$query = "SELECT password from " . User::$table . " where id_user=" . $this->user;
			$queryId = "SELECT id_user from " . User::$table . " where id_user=" . $this->user;
		} else if (User::stExists(array("email" => $this->user))) {
			$query = "SELECT password from " . User::$table . " where email='" . $this->user . "'";
			$queryId = "SELECT id_user from " . User::$table . " where email='" . $this->user . "'";
		} else {
			$this->_setError("USER_DONT_EXIST", "User $this->user dont exist");
			return "";
		}

		// TODO llevar password a datatype
		$res = DBMySQLConnection::stVirtualConstructor(User::$table)->select($query);

		if (is_null($res)) {
			$this->_setError("USER_DONT_EXIST", "User $this->user dont exist");
			return "";
		}

		if ($res["password"] == $this->password) {
			return array("idUser" => (int)DBMySQLConnection::stVirtualConstructor(User::$table)->select($queryId)["id_user"]);
		}

		$this->_setError("PASS_DONT_MATCH", "Password dont match");
		return "";
	}

	protected function _getResponse() {

		return $this->_login();
	}
}
