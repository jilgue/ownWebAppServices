<?php

namespace ownWebAppServices\Dispatch\classes;

use ownWebAppServices\Load\classes\LoadConfiguration;
use ownWebAppServices\Core\classes\CorePackages;

/**
 * Class DispatchTable
 * @package ownWebAppServices\Dispatch\classes
 */
class DispatchTable
{

    /**
     * Obtiene la tabla con todas las rutas disponibles
     */
    public static function stGetDispatchTable()
    {

        $ret = array();
        foreach (CorePackages::stGetPackagesList() as $package) {

            $path = CorePackages::stGetPackagePath($package) . "/conf/dispatch.inc";
            if (!is_file($path)) {
                continue;
            }

            $config = LoadConfiguration::stRequireConfig($path);

            $ret = array_merge($ret, $config);
        }

        return $ret;
    }
}
