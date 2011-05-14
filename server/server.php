<?php
	if(!require_once(dirname(__FILE__)."/utils/AerialStartupManager.php"))
		die("Could not find AerialStartupManager.php");

	class AerialServer
	{
		public $_config;
		public $_base;
		public $_config_alt;

		/*private $started = false;*/

		public function start()
		{
			$request = @$GLOBALS["HTTP_RAW_POST_DATA"];
			if(!$request)
				$request = file_get_contents('php://input');

			AerialStartupManager::setAMFRequest($request);

			$this->startHTMLOutput();

			set_error_handler(array($this, "errorHandler"));
			set_exception_handler(array($this, "exceptionHandler"));

			/*if(!$this->started)
			{*/
				$this->loadAerial();
			/*	$this->started = true;
			}
			else
			{
				AerialStartupManager::info("All Aerial components loaded successfully in session.");
			}*/

			if(!$request)
				echo '</body></html>';
		}

		private function loadAerial()
		{
			$this->initializeAerial();

			// define AMFPHP base path for AMFPHP's usage
			define("AMFPHP_BASE", realpath(conf("paths/amfphp"))."/core/");

			// Load AMFPHP
			set_include_path(AMFPHP_BASE);
			include_once(AMFPHP_BASE."../gateway.php");
			include_once(AMFPHP_BASE."../globals.php");

			AerialStartupManager::info("<strong>Aerial</strong> is configured correctly");

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
			if(AerialStartupManager::hasAMFRequest())
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

		private function initializeAerial()
		{
			$_configPath = $this->getConfigPath();

			$this->_config = @simplexml_load_file($_configPath);
			if(file_exists(dirname($_configPath)."/config-alt.xml") && is_readable(dirname($_configPath)."/config-alt.xml"))
				$this->_config_alt = @simplexml_load_file(dirname($_configPath)."/config-alt.xml");

			if(!$this->_config)
				AerialStartupManager::error("There is a syntax error in config.xml");

			// Get the base path (one level above config folder)
			$this->_base = dirname($_configPath);

			$dynamicPaths = array(
				"config" => dirname($_configPath),
				"project" => dirname(dirname($_configPath)),        // convention: config must always be in root of project
				"aerial" => conf("paths/lib", true, false)."/php/aerial",
				"internal-services" => conf("paths/lib", true, false)."/php",
				"doctrine" => conf("paths/lib", true, false)."/php/doctrine",
				"amfphp" => conf("paths/lib", true, false)."/php/amfphp"
			);

			$this->assignDynamicPaths($dynamicPaths);

			if(!realpath($this->_base))
				AerialStartupManager::error("Project root directory is invalid [$this->_base]");

			include_once(dirname(__FILE__)."/server.php");
			date_default_timezone_set(conf("options/timezone", false, false));				// Required for PHP >= 5.3

			if(!is_readable(conf("paths/aerial")."core/Bootstrapper.php"))
			{
				AerialStartupManager::error("The <strong>Aerial core</strong> directory is unreadable - check your 'lib' path in <i>config.xml</i>
												<br/>Hint: Change the directory owner to <strong>".AerialStartupManager::getGroup()."</strong>
												and ensure that the path is correct.");
			}
			else if(!realpath(conf("paths/aerial")."core/Bootstrapper.php"))
				AerialStartupManager::error("The <strong>Aerial core</strong> could not be located - check your 'lib' path in <i>config.xml</i>");
			else
			{
				require_once(conf("paths/aerial")."core/Bootstrapper.php");

				Bootstrapper::getInstance();
			}
		}

		/**
		 * Adds a number of configuration options at runtime so that the config.xml file can be smaller
		 *
		 * @param  $paths
		 * @return void
		 */
		private function assignDynamicPaths($paths)
		{
			foreach($paths as $key => $path)
				$this->_config->paths->{$key} = $path;
		}

		private function getConfigPath()
		{
			$directory = dirname(__FILE__);
			$configDirectoryGuess = realpath($directory."/../config/config.xml");

			// check for existence of .project file if the config folder is not in its default location
			if(!file_exists("$directory/.project") && !file_exists($configDirectoryGuess))
			{
				throw new Exception("Aerial could not find your configuration file. Please make sure that you have a file named
						\".project\" in your \"server\" directory.");
			}

			if(!file_exists("$directory/.project"))
				AerialStartupManager::error("Please ensure that the path used for the <strong>server/.project</strong> file is valid.");

			$projectXML = @simplexml_load_file("$directory/.project");
			if(!$projectXML)
				AerialStartupManager::error("There is an XML syntax error in the <strong>server/.project</strong> file");

			$location = realpath((string) $projectXML->location);
			$filetype = substr($location, strrpos($location, ".") + 1);

			if($filetype != "xml")
				$location = $location."/config.xml";

			if(!$location || !file_exists($location))
			{
				AerialStartupManager::error("Please ensure that the path used for the <strong>config.xml</strong>
						file in <strong>server/.project</strong> is <u>valid</u> and <u>readable</u>.");
			}

			return $location;
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

/*	session_start();

	if(!@$_SESSION["server"] || (@$_GET["restart"] && @$_GET["restart"] == "true"))
	{*/
		$server = new AerialServer();
		$server->start();
/*
		$_SESSION["server"] = $server;
		session_write_close();
	}
	else
	{
		$server = $_SESSION["server"];
		$server->start();
	}*/

	if(!$server)
		AerialStartupManager::error("Could not start Aerial server. Please check that you have PHP sessions enabled");

	/**
	 * Obtains values from the configuration file
	 * [NOTE] This function is declared outside the main class so as to be accessible easily by other files
	 * ...in other words, it's a global function
	 *
	 * @throws Exception
	 * @param  $path
	 * @param  $isPath				Whether the value is a path
	 * @param bool $trailingSlash	Whether to return the value with a trailing slash to help with concatenation
	 * @return string				Return the value
	 */
	function conf($path, $isPath=true, $trailingSlash=true, $subnode=false)
	{
		global $server;

		$_config = $server->_config;
		$_base = $server->_base;
		$_config_alt = $server->_config_alt;

		if(!$_config)
		{
			AerialStartupManager::error("No configuration options were found.");
		}

		$node = $_config->xpath($path);
		if($_config_alt && $_config_alt->xpath($path))
			$node = $_config_alt->xpath($path);

		if(!$node)
		{
			AerialStartupManager::error("Could not find configuration value <strong>$path</strong>!<br/>".
								 "Please ensure that there is a <strong>$path</strong> node in <i>config.xml</i>");
		}

		$value = (string) $node[0];

		if($isPath)
		{
			// try create absolute path, otherwise return plain value
			if(realpath("$_base/$value"))
				$value = realpath("$_base/$value");

			return $value.($trailingSlash ? DIRECTORY_SEPARATOR : "");
		}

		if($subnode)				// only return the node that matches the "use" attribute's value (see database node)
		{
			$nodes = $node[0];
			$use = (string) $nodes["use"];
			$nodeToUse = $nodes->$use;

			if(!$nodeToUse)			// if no "use" attribute is found, use the first node by default
			{
				$nodeToUse = $nodes->children();
				$nodeToUse = $nodeToUse[0];
			}

			$value = $nodeToUse->xpath($subnode);

			// if value is empty and config-alt.xml exists, try find value in main config
			if(!$value)
			{
				if($_config_alt)
				{
					$nodes = $_config->xpath($path);
					$nodes = $nodes[0];

					$nodeToUse = $nodes->$use;

					if(!$nodeToUse)			// if no "use" attribute is found, use the first node by default
					{
						$nodeToUse = $nodes->children();
						$nodeToUse = $nodeToUse[0];
					}

					$value = $nodeToUse->xpath($subnode);
				}
				else
					AerialStartupManager::error("$subnode node is missing in the database configuration");
			}

			$value = (string) $value[0];
			return $value;
		}

		if($value == "true" || $value == "false")				// using (bool) doesn't work properly
			$value = $value == "true";

		return $value;
	}
?>