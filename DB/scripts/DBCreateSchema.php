<?php
/**
 *
 */
class DBCreateSchema extends CoreScript {

	private function _getConfigClasses() {

		$classes = Core::stGetClassesList("classes");
		$packages = LoadInit::stPackagesLoad();

		$mainClasses = array_intersect($classes, $packages);

		$ret = array();
		foreach ($mainClasses as $mainClass) {

			if (!method_exists($mainClass, "stGetFieldsConfig")) {
				continue;
			}

			$ret[$mainClass] = $mainClass::stGetFieldsConfig();
		}

		return $ret;
	}

	private function _getColumnsSchema($config) {

		$ret = array();
		foreach ($config as $field => $fieldConfig) {

			$dt = $fieldConfig["DT"];
			$ret[] = DataTypeString::stVirtualConstructor($fieldConfig["DTParams"])->getDBColumnType($field);

		}
		return $ret;
	}

	private function _getTableSchema($config, $class) {

		$table = "CREATE TABLE `" . strtolower($class) . "` ( \n";

		$DBColumns = $this->_getColumnsSchema($config);

		$table = $table . implode("\n", $DBColumns);

		// TODO falta PF
		$table = substr($table, 0 , -1);

		$table = $table . "\n ) ENGINE=InnoDB DEFAULT CHARSET=latin1";

		return $table;
	}

	private function _createSchema() {

		$configClasses = $this->_getConfigClasses();

		foreach ($configClasses as $class => $config) {

			$table = $this->_getTableSchema($config, $class);

			if (!DBMySQLConnection::stVirtualConstructor()->query($table)) {
				// TODO warning
				return false;
			}
		}
	}

	function run() {

		return $this->_createSchema();
	}
}
