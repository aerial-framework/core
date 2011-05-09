<?php
	// Load Aerial's entrypoint
	include(dirname(__FILE__) . "/start-aerial.php");

	// define AMFPHP base path for AMFPHP's usage
	define("AMFPHP_BASE", realpath(conf("paths/amfphp"))."/core/");

	// Load AMFPHP
	set_include_path(AMFPHP_BASE);
	include(AMFPHP_BASE."../gateway.php");
	include(AMFPHP_BASE."../globals.php");
?>