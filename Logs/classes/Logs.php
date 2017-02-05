<?php

namespace ownWebAppServices\Logs\classes;

class Logs
{

    public static function stEchoDate($msg)
    {
        echo date('Y/m/d H:i:s ') . "$msg\n";
        return;
    }

    public static function stEcho($msg)
    {
        echo "$msg\n";
        return;
    }

    public static function stFatal($mgs)
    {
        Logs::stEcho($mgs);
        // TODO hacer fatal de verdad xD
        die;
    }
}
