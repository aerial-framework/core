<?php
	$configPath =	realpath(dirname(__FILE__)."/../lib/");

	// Load Aerial's configuration data
	set_include_path($configPath);
	include($configPath."/config.php");

	// define AMFPHP base path for AMFPHP's usage
	define("AMFPHP_BASE", conf("paths/amfphp"));

	// Load AMFPHP
	set_include_path(AMFPHP_BASE);
	include(AMFPHP_BASE."../gateway.php");
	include(AMFPHP_BASE."../globals.php");
?>