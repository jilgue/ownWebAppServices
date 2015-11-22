<?php

/**
 *
 */

global $argv;

require_once( dirname( __FILE__ ) . '/../../Load/classes/LoadInit.php');

// No pasan ningun argumento sacamos la lista de scripts
if (count($argv) == 1) {

	Logs::stEcho("Lista de scripts:");
	$scripts = Core::stGetClassesList("scripts");
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