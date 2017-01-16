<?php

namespace ownWebAppServices\Core\classes;

use ownWebAppServices\File\classes\FileSystem;

class Core
{

    /**
     * @return null|string
     */
    public static function stGetRootPath()
    {
        if (!isset($_SERVER["SCRIPT_FILENAME"])) {
            // TODO fatal
            return null;
        }

        $scriptFileName = $_SERVER["SCRIPT_FILENAME"];

        if (!($rootPath = FileSystem::stGetAbsolutePath($scriptFileName))) {
            // TODO fatal
            return null;
        }

        return $rootPath;
    }
}
