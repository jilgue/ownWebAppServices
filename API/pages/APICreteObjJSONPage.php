<?php
/**
 * Dispatching de URLs
 */
class APICreteObjJSONPage extends APIJSONPage {

	static $objField = array("object" => "\d",
				 );

	private function _processCreate($class) {

		$objField = $class::$objField;

		$params = array();
		foreach ($objField as $param => $conf) {

			if (isset($this->$param)) {
				$params[$param] = $this->$param;
			}
		}

		$id = DBObject::stGetObjIdField($class);
		// Si nos pasan el id seguramente sea un update en lugar de un create
		if (isset($params[$id])) {

			if ($class::stExist(array($id => $params[$id]))) {
				return (string) $class::stUpdate($params);
			} else {
				// Si no existe avisamos
				$this->_setError("DONT_EXIST", "$id " . $params[$id] . " dont exist");
				return "";
			}
		}

		$ret = $class::stCreate($params);
		if (is_array($ret)) {
			return (string) $ret;
		}

		$this->_setError("MISING_PARAM", $ret);
		return "";
	}

	protected function _getResponse() {

		$class = LoadInit::stGetClassCaseInsensitive($this->object);

		return $this->_processCreate($class);
	}
}
