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

        if (php_sapi_name() == "cli") {
            if (preg_match("#(/.+/)Core/scripts#", $rootPath, $match) === 1) {
                if (isset($match[1])) {
                    return $match[1];
                }
            }
        }

        return $rootPath;
    }
}
