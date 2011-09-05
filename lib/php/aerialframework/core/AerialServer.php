<?php

import('aerialframework.core.AerialStartupManager');
import('aerialframework.core.ConfigXml');
import('aerialframework.core.Bootstrapper');

class AerialServer
{
	public $createDirectories = false; //ToDo.

	private $startTime;

	public function __construct()
	{
		date_default_timezone_set(ConfigXml::getInstance()->timezone); // Required for PHP >= 5.3
	}

	public function start()
	{
		$this->startTime = microtime(true);

		$request = @$GLOBALS["HTTP_RAW_POST_DATA"];
		if(!$request)
		{
			$request = file_get_contents('php://input');
		}

		AerialStartupManager::setAMFRequest($request);
		AerialStartupManager::setStartupMessagesDisplayFlag($this->canDisplayStartupInfo());

		$this->startHTMLOutput();

		set_error_handler(array($this, "errorHandler"));
		set_exception_handler(array($this, "exceptionHandler"));

		$this->createDirectories = (@$_GET["createDirectories"] && $_GET["createDirectories"] == "true");

		$this->loadAerial();

		$this->endHTMLOutput();
	}

	private function loadAerial()
	{
		Bootstrapper::getInstance();

		// Load AMFPHP
		set_include_path(LIB_PATH . DIRECTORY_SEPARATOR . 'amfphp' . DIRECTORY_SEPARATOR . 'core');
		import('amfphp.gateway');
		import('amfphp.globals');

		$endTime = microtime(true);
		$totalSeconds = round($endTime - $this->startTime, 4);

		AerialStartupManager::info("<strong>Aerial</strong> is configured correctly (started up in $totalSeconds seconds)");

		restore_error_handler();
		restore_exception_handler();
	}

	private function startHTMLOutput()
	{
		if(AerialStartupManager::showStartupMessages())
			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
						"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
					<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">

					<head>
						<title>Aerial Framework</title>
					</head>

					<link rel="stylesheet" type="text/css" href="assets/styles/style.css" />

					<body>';
	}

	/**
	 * Determines whether or not the startup information can be displayed or not
	 *
	 * @return bool
	 */
	private function canDisplayStartupInfo()
	{
		// Check if the incoming mimetype is consistent with the AMF mimetype.
		// If the request is indeed an AMF request, don't allow startup messages (HTML) to be displayed.
		// Only display startup messages if server.php is called directly.
		if(@$_SERVER["CONTENT_TYPE"] == "application/x-amf")
			return false;
		elseif(pathinfo($_SERVER['SCRIPT_NAME'],PATHINFO_BASENAME) == 'server.php')
			return  true;
		else 
			return false;
	}

	private function endHTMLOutput()
	{
		if(AerialStartupManager::showStartupMessages())
			echo "\n\t</body>\n</html>";
	}


	public function errorHandler($errno, $errstr, $errfile, $errline)
	{
		switch($errno)
		{
			case E_USER_ERROR:
			case E_ERROR:
				AerialStartupManager::error($errstr);
				break;
			case E_USER_WARNING:
				AerialStartupManager::warn($errstr);
				break;
			case E_NOTICE:
			case E_WARNING:
			case E_USER_NOTICE:
				//					AerialStartupManager::warn($errstr."<br/>in <strong>$errfile</strong> at line $errline");
				break;
		}
	}

	public function exceptionHandler(Exception $e)
	{
		$this->errorHandler(E_USER_ERROR, $e->getMessage(), $e->getFile(), $e->getLine());
	}
}


