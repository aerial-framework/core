<?php

import('aerialframework.core.AerialStartupManager');
import('aerialframework.core.ConfigXml');
import('aerialframework.core.Bootstrapper');

class AerialServer
{
	public $_config;
	public $_config_alt;
	
	private $config;
	
	public $createDirectories = false;

	private $startTime;

	/*private $started = false;*/
	
	public function __construct()
	{
		$this->config = ConfigXml::getInstance()->config;
		date_default_timezone_set($this->config->options->timezone);				// Required for PHP >= 5.3
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

	public function __sleep()
	{
		$this->_config = $this->_config ? $this->_config->asXML() : null;
		$this->_config_alt = $this->_config_alt ? $this->_config_alt->asXML() : null;

		return array_keys(get_object_vars($this));
	}

	public function __wakeup()
	{
		$this->_config = $this->_config ? new SimpleXMLElement((string) $this->_config) : null;
		$this->_config_alt = $this->_config_alt ? new SimpleXMLElement((string) $this->_config_alt) : null;

		return $this;
	}

	private function startHTMLOutput()
	{
		if(AerialStartupManager::hasAMFRequest() )
		return;
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
						"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
					<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">

					<head>
						<title>Aerial Framework</title>
					</head>

					<link rel="stylesheet" type="text/css" href="assets/styles/style.css" />

					<body>';
	}

	private function endHTMLOutput()
	{
		if(AerialStartupManager::hasAMFRequest()) //|| !AerialStartupManager::isDirectCall()
		return;

		echo "\n\t</body>\n</html>";
	}



	private function isPathRelative($path)
	{
		$base = $this->getBasePath();

		// replace all slashes with a forward slash for consistency
		$base = preg_replace('%[/\\\\]+%', '/', $base);
		$path = preg_replace('%[/\\\\]+%', '/', $path);

		return strpos($path, $base) === false;
	}

	private function getCurrentPageURL()
	{
		$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on")
		{
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80")
		{
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		}
		else
		{
			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}

		$pageURL = explode("?", $pageURL);
		return $pageURL[0];
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


