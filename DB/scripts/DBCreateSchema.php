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

			$ret[] = $dt::stVirtualConstructor($fieldConfig["DTParams"])->getDBColumnType($field);

		}
		return $ret;
	}

	private function _getPrimaryKey($config) {

		$PK = array();
		foreach ($config as $field => $fieldConfig) {

			if ($fieldConfig["DT"] == "DataTypeIdDT"
			    && $fieldConfig["DTParams"]["identifier"] == true) {
				$PK[] = $field;
			}
		}

		return "PRIMARY KEY (`" . implode(",", $PK) . "`)";
	}

	private function _getTableSchema($config, $class) {

		$table = "CREATE TABLE `" . DBObject::stGetTableName($class) . "` ( \n";

		$DBColumns = $this->_getColumnsSchema($config);

		// Añadimos la clave primaria
		$DBColumns[] = $this->_getPrimaryKey($config);

		$table = $table . implode(",\n", $DBColumns);

		$table = $table . "\n ) ENGINE=InnoDB DEFAULT CHARSET=latin1";

		return $table;
	}

	private function _createSchema() {

		$configClasses = $this->_getConfigClasses();

		foreach ($configClasses as $class => $config) {

			$table = $this->_getTableSchema($config, $class);

			echo $table . "\n";

			if (!DBMySQLConnection::stVirtualConstructor()->query($table)) {
				// TODO warning
				return false;
			}
		}
		return true;
	}

	function run() {

		return $this->_createSchema();
	}
}
