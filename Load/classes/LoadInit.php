<?php

namespace ownWebAppServices\Load\classes;

use ownWebAppServices\Core\classes\CorePackages;

/**
 * Class LoadInit
 * @package ownWebAppServices\Load\classes
 */
class LoadInit
{

    /**
     * @return array|mixed
     */
    public static function stPackagesLoad()
    {
        return (bool) $GLOBALS["packages"] = CorePackages::stGetPackagesList();
    }

    public static function stAutoload($class)
    {
        //var_dump($class);
    }
}

spl_autoload_register(__NAMESPACE__ . "\LoadInit::stAutoload");
