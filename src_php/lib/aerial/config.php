<?php
	// path constants
	date_default_timezone_set('America/New_York');										// Required for PHP >= 5.3

	$configPath = realpath(dirname(__FILE__)."/config");
	$config = simplexml_load_file($configPath."/config.xml");

	foreach($config as $key => $val)
	{
		if($val == "true" || $val == "false")
			$val = $val == "true";

		@define($key, $val);
	}

	require_once(AERIAL_BASE_PATH."/Bootstrapper.php");
	Bootstrapper::getInstance();
?>