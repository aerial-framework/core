<?php
	// Load Aerial's entrypoint

	$request = @$GLOBALS["HTTP_RAW_POST_DATA"];
	if(!$request)
		$request = file_get_contents('php://input');

	if(!$request)
	{
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
				"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">

			<head>
				<title>Aerial Framework</title>
			</head>

			<link rel="stylesheet" type="text/css" href="assets/styles/style.css" />

			<body>';
	}

	set_error_handler("errorHandler");
	set_exception_handler("exceptionHandler");

	include_once(dirname(__FILE__) . "/start-aerial.php");
		
	// define AMFPHP base path for AMFPHP's usage
	define("AMFPHP_BASE", realpath(conf("paths/amfphp"))."/core/");

	// Load AMFPHP
	set_include_path(AMFPHP_BASE);
	include_once(AMFPHP_BASE."../gateway.php");
	include_once(AMFPHP_BASE."../globals.php");

	AerialStartupManager::info("<strong>Aerial</strong> is configured correctly");

	restore_error_handler();
 	restore_exception_handler();

	if(!$request)
		echo '</body></html>';

	function errorHandler($errno, $errstr, $errfile, $errline)
	{
		global $request;

		switch($errno)
		{
			case E_USER_ERROR:
			case E_ERROR:
				AerialStartupManager::error($errstr);
				break;
			case E_USER_WARNING:
				AerialStartupManager::warn($errstr);
				break;
		}
	}

	function exceptionHandler(Exception $e)
	{
		errorHandler(E_USER_ERROR, $e->getMessage(), $e->getFile(), $e->getLine());
	}
?>