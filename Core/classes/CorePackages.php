<?php

namespace ownWebAppServices\Core\classes;

use ownWebAppServices\File\classes\FileSystem;

/**
 * Class CorePackages
 * @package ownWebAppServices\Core\classes
 */
class CorePackages
{

    /**
     * @return array|mixed
     */
    public static function stGetPackagesList()
    {
        static $stCache = array();

        if (isset($stCache["packageList"])) {
            return $stCache["packageList"];
        }

        $paths = FileSystem::stGetListDir(Core::stGetRootPath());

        $ret = array();
        foreach ($paths as $path) {
            if (preg_match("#^[A-Z][A-z]{1,}$#", $path, $match) === 1) {
                $ret[] = $match[0];
            }
        }

        // Guardamos en "cache" la lista de paquetes
        $stCache["packageList"] = $ret;

        return $ret;
    }

    /**
     * @param $class
     * @return null
     */
    public static function stGetPackageClass($class)
    {
        if (preg_match("#ownWebAppServices\\\(\w+)\\\.+#", $class, $matches)) {
            if (isset($matches[1]) && CorePackages::stPackageExists($matches[1])) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * @param $package
     * @return string
     */
    public static function stGetPackagePath($package)
    {
        if (!CorePackages::stPackageExists($package)) {
            return "";
        }

        return Core::stGetRootPath() . $package . "/";
    }

    /**
     * @param $class
     * @return string
     */
    public static function stGetPackagePathForClass($class)
    {

        $package = CorePackages::stGetPackageClass($class);
        if (is_null($package)) {
            return "";
        }

        return CorePackages::stGetPackagePath($package);
    }

    /**
     * @param $package
     * @return bool
     */
    public static function stPackageExists($package): bool
    {
        return (bool) in_array($package, CorePackages::stGetPackagesList());
    }
}
