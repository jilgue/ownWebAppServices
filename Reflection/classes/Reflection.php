<?php

namespace ownWebAppServices\Reflection\classes;

/**
 * Class Reflection
 * @package ownWebAppServices\Reflection\classes
 */
class Reflection
{
    /**
     * @return mixed
     */
    public static function stGetPreviousCalledClass()
    {

        // 0 soy yo, 1 donde me llaman, 2 la que quiero saber xD
        return Reflection::stGetCalledClass(3);
    }

    /**
     * @param int $backTrace
     * @return mixed
     */
    public static function stGetCalledClass(int $backTrace)
    {
        $traces = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, $backTrace);
        return $traces[$backTrace - 1]["class"];
    }

    /**
     * @param int $backTrace
     * @return bool
     */
    public static function stGetCalledClassFromFile(int $backTrace)
    {
        $traces = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, $backTrace);
        $file = $traces[$backTrace - 1]["file"];

        if (preg_match("%/(.[^/]*)\.php%", $file, $match) === 1) {
            return $match[1];
        }

        return false;
    }
}
