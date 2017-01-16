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
