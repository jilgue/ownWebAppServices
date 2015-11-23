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

	private function _createSchema() {

		$configClasses = $this->_getConfigClasses();

		foreach ($configClasses as $class => $config) {

			foreach ($config as $field => $fieldConfig) {

				$dt = $fieldConfig["DT"];
				$dbColumn = DataTypeString::stVirtualConstructor($fieldConfig["DTParams"])->getDBColumnType($field);
				var_dump($dbColumn);
			}
		}die;
	}

	function run() {

		return $this->_createSchema();
	}
}
