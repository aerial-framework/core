<?php
	
	//Aerial Config
	require_once(dirname(__FILE__)."../../../lib/aerial/config.php");

	//Set start time before loading framework
	list($usec, $sec) = explode(" ", microtime());
	$amfphp['startTime'] = ((float)$usec + (float)$sec);

	$basePath = AMFPHP_PATH;
	$servicesPath = INTERNAL_SERVICES_PATH;
	$voPath = realpath(BACKEND_MODELS_PATH . "/..");  //Needed to make this the "model" directory for AMFPHP.  Rob: 4-16-2010
	
	//As an example of what you might want to do here, consider:
	
	/*
	if(!PRODUCTION_SERVER)
	{
		define("DB_HOST", "localhost");
		define("DB_USER", "root");
		define("DB_PASS", "");
		define("DB_NAME", "amfphp");
	}
	*/
	
?>