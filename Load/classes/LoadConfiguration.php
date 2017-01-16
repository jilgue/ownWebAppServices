<?php

namespace ownWebAppServices\Load\classes;

/**
 * Class LoadConfiguration
 * @package ownWebAppServices\Load\classes
 */
class LoadConfiguration
{

    /**
     * @param $path
     * @return array
     */
    public static function stRequireConfig($path): array
    {

        static $stCache = array();

        if (isset($stCache[$path])) {
            return $stCache[$path];
        }

        require_once $path;

        if (!isset($config) || !is_array($config)) {
            return array();
        }

        $stCache[$path] = $config;

        return $config;
    }
}
