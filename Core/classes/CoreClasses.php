<?php

namespace ownWebAppServices\Core\classes;

use ownWebAppServices\File\classes\FileSystem;

class CoreClasses
{
    public static function stGetClassesList(string $type): array
    {

        if (preg_match("#^scripts$|^classes$|^pages$#", $type) === 0) {
            return array();
        }

        $ret = array();
        foreach (CorePackages::stGetPackagesList() as $package) {

            $path = CorePackages::stGetPackagePath($package) . $type . "/";

            if (is_dir($path)) {
                $ret = array_merge($ret, FileSystem::stGetListFiles($path));
            }
        }

        // Cleaning
        foreach ($ret as & $item) {
            if (preg_match("#(\w+)\.php#", $item, $match) === 1) {
                if (isset($match[1])) {
                    $item = $match[1];
                }
            }
        }

        return $ret;
    }
}
