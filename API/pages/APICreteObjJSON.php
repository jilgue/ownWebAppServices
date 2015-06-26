<?php
/**
 * Dispatching de URLs
 */
class APICreteObjJSON extends DispatchPage {

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
				return $class::stUpdate($params);
			} else {
				// Si no existe avisamos
				return "Error";
			}
		}

		return $class::stCreate($params);
	}

	function getOutput() {

		$class = LoadInit::stGetClassCaseInsensitive($this->object);

		$ret = $this->_processCreate($class);

		echo json_encode($ret);
	}
}
