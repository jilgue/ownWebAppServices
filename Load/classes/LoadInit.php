<?php

namespace ownWebAppServices\Load\classes;

use ownWebAppServices\File\classes\FileSystem;

/**
 * Class LoadInit
 * @package ownWebAppServices\Load\classes
 */
class LoadInit
{
    /**
     * @var string
     */
    public static $path = "/deploy/projects/ownWebAppServices/";

    /**
     * @return array|mixed
     */
    public static function stPackagesLoad()
    {

        static $stCache = array();

        if (isset($stCache["packageList"])) {
            return $stCache["packageList"];
        }

        $paths = FileSystem::stGetListDir(LoadInit::$path);

        $ret = array();
        foreach ($paths as $path) {
            if (preg_match("#^[A-Z][A-z]{1,}$#", $path, $match) === 1) {
                $ret[] = $match[0];
            }
        }

        // Guardamos en "cache" la lista de paquetes y el path
        $stCache["packageList"] = $ret;

        return $ret;
    }
}
