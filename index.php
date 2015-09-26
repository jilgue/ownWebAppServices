<?php

require_once( dirname( __FILE__ ) . '/Load/classes/LoadInit.php');

DispatchDispatcher::stProcessRequest($_SERVER["REQUEST_URI"]);
