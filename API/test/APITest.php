<?php
/**
 * Dispatching de URLs
 */
class APITest extends DispatchPage {

	static $objField = array("action" => "\d",
				 "object" => "\d",
				 );

	private function _processSignatureBook($action) {

		$objField = SignatureBook::$objField;

		$params = array();
		foreach ($objField as $param => $conf) {

			if (isset($this->$param)) {
				$params[$param] = $this->$param;
			}
		}

		return SignatureBook::stCreate($params);
	}

	function getOutput() {

		$class = LoadInit::stGetClassCaseInsensitive($this->object);

		$func = "_process" . $class;
		$ret = $this->$func($this->action);

		echo json_encode($ret);
	}
}
