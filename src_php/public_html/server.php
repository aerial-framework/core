<?php
	$gatewayPath =	realpath(dirname(__FILE__)."/amfphp/gateway.php");
	$aerialPath =	realpath(dirname(__FILE__)."/../lib/aerial");

	// Load Aerial's configuration data
	set_include_path($aerialPath);
	include($aerialPath."/config.php");

	// Load AMFPHP
	set_include_path($gatewayPath);
	include($gatewayPath);
?>