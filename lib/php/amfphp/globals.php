<?php
	//Set start time before loading framework
	list($usec, $sec) = explode(" ", microtime());
	$amfphp['startTime'] = ((float)$usec + (float)$sec);

	$basePath = AMFPHP_BASE;

	$servicesPath = conf("paths/lib")."php";
	$models_path = conf("paths/php-models");

	$voPath = realpath($models_path."/..");
?>