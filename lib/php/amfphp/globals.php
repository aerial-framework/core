<?php
	//Set start time before loading framework
	list($usec, $sec) = explode(" ", microtime());
	$amfphp['startTime'] = ((float)$usec + (float)$sec);

	$basePath = AMFPHP_BASE;

	$servicesPath = conf("paths/aerial")."core";
	$models_path = conf("paths/php-models");

	$voPath = realpath($models_path."/..");  //Needed to make this the "model" directory for AMFPHP.  Rob: 4-16-2010	
?>