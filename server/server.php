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
				<title>[Aerial Framework]</title>
			</head>

			<style>
				body
				{
					background: url("assets/images/logo.png") repeat-y #000000 right 0px;

					font-family: Droid Sans, Tahoma, sans-serif;
					color: #FFFFFF;
					font-size: 11px;
				}

				p
				{
					background-color: #ff2222;
					font-size: 14px;
					padding: 8px 8px 8px 8px;
					border: #CCCCCC solid 1px;
					margin-right: 100px;
				}

				p.warning
				{
					background-color: #ffbb00;
				}

				p.success
				{
					background-color: #44cc00;
				}
			</style>

			<body>';
	}

	set_error_handler("errorHandler");
	set_exception_handler("exceptionHandler");

	include(dirname(__FILE__) . "/start-aerial.php");
		
	// define AMFPHP base path for AMFPHP's usage
	define("AMFPHP_BASE", realpath(conf("paths/amfphp"))."/core/");

	// Load AMFPHP
	set_include_path(AMFPHP_BASE);
	include(AMFPHP_BASE."../gateway.php");
	include(AMFPHP_BASE."../globals.php");

	echo "<p class='success'><strong>Aerial</strong> is configured correctly</p>";

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
				StartupHelper::error($errstr);
				break;
			case E_USER_WARNING:
				StartupHelper::warn($errstr);
				break;
		}
	}

	class StartupHelper
	{
		public static function error($error)
		{
			self::log($error, E_USER_ERROR);
		}

		public static function warn($warning)
		{
			self::log($warning, E_USER_WARNING);
		}

		private static function log($message, $type)
		{
			global $request;

			switch($type)
			{
				case E_USER_ERROR:
					die((!$request ? "<p>" : "")."<strong>ERROR</strong>:<br/><br/>".$message.(!$request ? "</p>" : ""));
					break;
				case E_USER_WARNING:
					echo((!$request ? "<p class='warning'>" : "")."<i>WARNING</i>:<br/><br/>".$errstr.(!$request ? "</p>" : ""));
					break;
			}
		}
	}
?>