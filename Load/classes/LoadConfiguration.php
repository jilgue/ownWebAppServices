<?php

namespace ownWebAppServices\Load\classes;

use ownWebAppServices\Load\classes\LoadInit;

/**
 * Class LoadConfiguration
 * @package ownWebAppServices\Load\classes
 */
class LoadConfiguration
{

    /**
     * Obtiene la tabla con todas las rutas disponibles
     */
    public static function stGetDispatchTable()
    {

        $ret = array();
        foreach (LoadInit::stPackagesLoad() as $package) {

            $path = LoadInit::$path . $package . "/conf/dispatch.inc";
            if (!is_file($path)) {
                continue;
            }

            $config = LoadConfiguration::stRequireConfig($path);

            $ret = array_merge($ret, $config);
        }

        return $ret;
    }

    /**
     * @param $path
     * @return mixed
     */
    public static function stRequireConfig($path)
    {

        static $stCache = array();

        if (isset($stCache[$path])) {
            return $stCache[$path];
        }

        require_once $path;

        if (!isset($config)) {
            return array();
        }

        $stCache[$path] = $config;

        return $config;
    }
}
