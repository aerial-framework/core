<?php
	if(!file_exists("../../project.xml"))
	{
		trigger_error("project.xml file not present.");
		die();
	}

	// Load Aerial's configuration data
	include("/../lib/config.php");

	// define AMFPHP base path for AMFPHP's usage
	define("AMFPHP_BASE", conf("paths/amfphp"));

	// Load AMFPHP
	set_include_path(AMFPHP_BASE);
	include(AMFPHP_BASE."../gateway.php");
	include(AMFPHP_BASE."../globals.php");
?>