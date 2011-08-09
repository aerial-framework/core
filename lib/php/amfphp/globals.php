<?php
	//Set start time before loading framework
	list($usec, $sec) = explode(" ", microtime());
	$amfphp['startTime'] = ((float)$usec + (float)$sec);
	
	define('AMFPHP_BASE', realpath(LIB_PATH . "/amfphp/core") . DIRECTORY_SEPARATOR);
	$basePath = AMFPHP_BASE;

	//$servicesPath = conf("paths/lib")."php";
	$servicesPath = ConfigXml::getInstance()->modelsPath;
	
	//$models_path = conf("paths/php-models");
	//$voPath = realpath($models_path."/..");
	$voPath = ConfigXml::getInstance()->modelsPath;
?>