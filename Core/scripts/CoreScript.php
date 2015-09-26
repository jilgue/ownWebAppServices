<?php
/**
 *
 */
abstract class CoreScript extends Object {

	var $run = false;

	function __construct($params) {

		// Separamos argumentos: --variable valor
		$formatParams = $this->_getFormatParams($params);
		parent::__construct($formatParams);
	}

	private function _getFormatParams($params){

		$ret = array();
		foreach ($params as $param) {

			// Si llegamos a un params que es un array es cosa del constructor de Object, paramos
			if (is_array($param)) {
				break;
			}

			if (preg_match("/^--(.+)$/", $param, $match)) {
				$ret[$match[1]] = true;
			} else {

				// Comprobamos que lo primero que llega no es un valor
				if (count($ret) == 0) {
					Logs::stFatal("mal muy mal");
				}

				// Si es el primer valor lo guardamos normal
				if (is_bool($ret[end(array_keys($ret))])) {
					$ret[end(array_keys($ret))] = $param;
				} else if (is_array($ret[end(array_keys($ret))])) {
					// Si ya es un array juntamos
					$ret[end(array_keys($ret))] = array_merge($ret[end(array_keys($ret))], array($param));
				} else {
					// Si hay mas de uno lo guardamos como array
					$ret[end(array_keys($ret))] = array_merge(array($ret[end(array_keys($ret))]), array($param));
				}
			}
		}
		return $ret;
	}

	abstract function run();
}
