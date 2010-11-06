<?php
	//Set start time before loading framework
	list($usec, $sec) = explode(" ", microtime());
	$amfphp['startTime'] = ((float)$usec + (float)$sec);

	$basePath = AMFPHP_BASE;

	$servicesPath = conf("paths/internal-services");

	$php_path = conf("code-generation/php");
	$package = conf("code-generation/package", false);

	if($package)
		$php_path .= implode(DIRECTORY_SEPARATOR, explode(".", $package)).DIRECTORY_SEPARATOR;

	$models_path = $php_path.conf("code-generation/models-folder");

	$voPath = realpath($models_path."/..");  //Needed to make this the "model" directory for AMFPHP.  Rob: 4-16-2010	
?>