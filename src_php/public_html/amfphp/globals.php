<?php
	
	//Aerial Config
	require_once(dirname(__FILE__)."../../../lib/aerial/config.php");

	//Set start time before loading framework
	list($usec, $sec) = explode(" ", microtime());
	$amfphp['startTime'] = ((float)$usec + (float)$sec);

	$basePath = AMFPHP_PATH;
	$servicesPath = INTERNAL_SERVICES_PATH;
	$voPath = BACKEND_MODELS_PATH . "/../../";
?>