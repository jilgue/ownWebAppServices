<?php
use ownWebAppServices\Load\classes\LoadInit;

require_once __DIR__ . '/../../vendor/autoload.php';

LoadInit::stPackagesLoad();

use ownWebAppServices\Core\classes\Core;
use ownWebAppServices\Core\classes\CoreClasses;
use ownWebAppServices\Logs\classes\Logs;

$rootPath = Core::stGetRootPath();

global $argv;

// No pasan ningun argumento sacamos la lista de scripts
if (count($argv) == 1) {

    Logs::stEcho("Lista de scripts:");
    $scripts = CoreClasses::stGetClassesList("scripts");
    foreach ($scripts as $script) {
        Logs::stEcho($script);
    }

    die(0);
}

$class = $argv[1];

// Pasamos al script el resto de argumentos
$script = $class::stVirtualConstructor(array_slice($argv, 2));

// RUN
$script->run();

Logs::stEcho("\n\nAdios buen día");
