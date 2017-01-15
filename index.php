<?php

// Cargamos primero nuestro autoload para que funcione en último lugar
use ownWebAppServices\Load\classes\LoadInit;

require_once __DIR__ . '/vendor/autoload.php';

LoadInit::stPackagesLoad();

use ownWebAppServices\Dispatch\classes\DispatchDispatcher;

DispatchDispatcher::stProcessRequest($_SERVER["REQUEST_URI"]);
