<?php

namespace ownWebAppServices\Load\classes;

use ownWebAppServices\Reflection\classes\Reflection as ownReflection;

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

        $cmd = "find " . $GLOBALS["path"] . " -name dispatch.inc";

        exec($cmd, $out);

        $ret = array();
        foreach ($out as $path) {

            $config = LoadConfiguration::_stRequireConfig($path);

            $ret = array_merge($ret, $config);
        }

        return $ret;
    }

    /**
     * Obtiene el config de una clase
     */
    public static function stGetConfigClass($class = "")
    {

        $class = $class != "" ? $class : LoadConfiguration::stGetPreviousCalledClass();

        if (($path = LoadConfiguration::_stGetConfigPath($class)) === false) {
            return false;
        }

        $config = LoadConfiguration::_stRequireConfig($path);

        if (isset($config[$class])) {

            return $config[$class];
        }

        return false;
    }

    /**
     * Obtiene el config de una clase
     */
    public static function stGetConfigVar($var, $class = "")
    {

        $class = $class != "" ? $class : static::stGetPreviousCalledClass();

        if (($path = LoadConfiguration::_stGetConfigPath($class)) === false) {
            return false;
        }

        $config = LoadConfiguration::_stRequireConfig($path);

        if (isset($config[$var])) {
            return $config[$var];
        }
        return false;
    }

    /**
     * Obtiene el config de una clase
     */
    public static function stGetConfigVarClass($var, $class = "")
    {

        static $stCache = array();

        $class = $class != "" ? $class : static::stGetPreviousCalledClass();

        // Quiza ya haya sido cargado
        if (isset($stCache[$class][$var])) {
            return $stCache[$class][$var];
        }

        $path = LoadConfiguration::_stGetConfigPath($class);

        if ($path === false) {
            return false;
        }

        $config = LoadConfiguration::_stRequireConfig($path);

        if (isset($config[$class][$var])) {
            $stCache[$class][$var] = $config[$class][$var];
            return $config[$class][$var];
        }

        return false;
    }

    /**
     * @param $path
     * @return mixed
     */
    private static function _stRequireConfig($path)
    {

        static $stCache = array();

        if (isset($stCache[$path])) {
            return $stCache[$path];
        }

        require_once $path;

        $stCache[$path] = $config;

        return $config;
    }

    /**
     * @param $class
     * @return bool|string
     */
    private static function _stGetConfigPath($class)
    {

        foreach (LoadInit::stPackagesLoad() as $package) {

            if (preg_match("/" . $package . "[[:alnum:]]{0,}/", $class, $match)) {

                $path = $GLOBALS["path"] . $package . "/conf/config.inc";
                if (is_file($path)) {
                    return $path;
                }
            }
        }

        return false;
    }
}
