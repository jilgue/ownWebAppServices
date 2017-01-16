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
    public static function stGetListDir($path): array
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
    public static function stGetListFiles($path): array
    {
        $ret = array();
        foreach (scandir($path) as $item) {
            if (is_file($item)) {
                $ret[] = $item;
            }
        }

        return $ret;
    }

    /**
     * @param $path
     * @return bool
     */
    public static function stIsAbsolutePath($path): bool
    {
        return (bool) FileSystem::stGetAbsolutePath($path);
    }

    /**
     * @param $path
     * @return string
     */
    public static function stGetAbsolutePath($path)
    {
        $path = dirname($path);

        // Añadimos la barra del final que realpath la quita y no me gusta una mierda
        return realpath($path) . "/";
    }
}
