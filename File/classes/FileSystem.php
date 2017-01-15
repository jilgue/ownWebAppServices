<?php

namespace ownWebAppServices\File\classes;

/**
 * Class FileSystem
 * @package ownWebAppServices\File\classes
 */
class FileSystem
{
    /**
     * @param $path
     * @return array
     */
    public static function stGetListDir($path)
    {
        $ret = array();
        foreach (scandir($path) as $item) {
            if (is_dir($item)) {
                $ret[] = $item;
            }
        }

        return $ret;
    }

    /**
     * @param $path
     * @return array
     */
    public static function stGetListFiles($path)
    {
        $ret = array();
        foreach (scandir($path) as $item) {
            if (is_file($item)) {
                $ret[] = $item;
            }
        }

        return $ret;
    }
}
