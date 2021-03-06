<?php

namespace ownWebAppServices\Dispatch\classes;

use ownWebAppServices\Load\classes\LoadConfiguration;

/**
 * Class DispatchDispatcher
 * @package ownWebAppServices\Dispatch\classes
 */
class DispatchDispatcher
{

    /**
     * @param $URL
     * @return string
     */
    private static function stGetURLMatch($URL)
    {

        $_URL = $URL;

        // Limpiamos la url de los parametros get
        if (preg_match("/(.*)\?/", $URL, $match) === 1) {
            $_URL = $match[1];
        }

        // Si no termina en .algo añadimos una barra
        if (preg_match("#(?:.(?!/))+\.([a-z]+)#", $_URL, $match) === 1) {
            // TODO controlar las terminaciones que permitimos
        } else {
            $_URL = $_URL . "/";
        }

        return $_URL;
    }

    /**
     * @param $URL
     * @return mixed
     */
    public static function stProcessRequest($URL)
    {

        $_URL = DispatchDispatcher::stGetURLMatch($URL);

        $dispatchTable = DispatchTable::stGetDispatchTable();
        //var_dump($dispatchTable);
        //die;
        foreach ($dispatchTable as $urlMatch => $config) {

            // TODO puede hacer problemas con la @ ?
            if (preg_match("@" . $urlMatch . "@", $_URL, $match) === 1) {

                $obj = call_user_func_array(
                    array($config["class"], "stVirtualConstructor"),
                    array(DispatchDispatcher::stGetUrlArg($URL, $urlMatch, $config))
                );
                return $obj->printOutput();
            }
        }

        echo DispatchDispatcher::stPageNotFound();
    }

    /**
     * @param $URL
     * @param $urlMatch
     * @param $config
     * @return array
     */
    public static function stGetUrlArg($URL, $urlMatch, $config)
    {

        $URL = DispatchDispatcher::stRelativizeUrl($URL);

        $class = $config["class"];

        $ret = array();
        if (preg_match_all("%<(.[^>]*)%", $urlMatch, $match)) {

            preg_match("%" . $urlMatch . "%", $URL, $match);

            foreach ($match as $param => $value) {

                if (isset($class::stGetObjFields()[$param])) {
                    $ret[$param] = $value;
                }
            }
        }

        if (isset($config["method"])) {
            $method = $config["method"];
        } else {
            return $ret;
        }

        foreach ($method as $param => $value) {

            // No soportamos page.html?myarray[]=1
            if ($method != $_FILES
                && is_array($value)
            ) {
                continue;
            }

            // TODO soporte para optionalGETParams y obligatoryGETParams

            $ret[$param] = $value;
        }

        return $ret;
    }

    /**
     * @param $URL
     * @return mixed
     */
    public static function stRelativizeUrl($URL)
    {

        $urlBase = LoadConfiguration::stGetConfigClass()["urlBase"];

        return preg_replace("%$urlBase%", "", $URL);
    }

    /**
     * @return string
     */
    public static function stPageNotFound()
    {

        $output = '<!doctype html>
<html>
        <head>
                <meta charset="utf-8">
                <title>404 Not Found</title>
        </head>
        <body>
                <p>404 Not Found</p>
                <p><a href="/">Home page</a></p>
        </body>
</html>';
        return $output;
    }
}
