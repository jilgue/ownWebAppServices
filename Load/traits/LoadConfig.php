<?php

namespace ownWebAppServices\Load\traits;

use ownWebAppServices\Core\classes\CorePackages;
use ownWebAppServices\Load\classes\LoadConfiguration;

trait LoadConfig
{
    /**
     * @param string $class
     * @return array|null
     */
    public static function stGetPackageConfig($class = "")
    {
        $class = $class != "" ? $class : get_called_class();

        if (($path = CorePackages::stGetPackagePathForClass($class)) === false) {
            return null;
        }

        $config = $path . "conf/config.inc";

        if (!is_file($config)) {
            return null;
        }

        return LoadConfiguration::stRequireConfig($config);
    }
}
