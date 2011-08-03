<?php
	class AerialServer
	{
		public $_config;
		public $_config_alt;

		public $createDirectories = false;

		private $startTime;

		/*private $started = false;*/

		public function start()
		{
			$this->startTime = microtime(true);

			$request = @$GLOBALS["HTTP_RAW_POST_DATA"];
			if(!$request)
				$request = file_get_contents('php://input');

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
			$this->initializeAerial();

			// define AMFPHP base path for AMFPHP's usage
			define("AMFPHP_BASE", realpath(conf("paths/amfphp"))."/core/");

			// Load AMFPHP
			set_include_path(AMFPHP_BASE);
			include_once(AMFPHP_BASE."../gateway.php");
			include_once(AMFPHP_BASE."../globals.php");

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
			if(AerialStartupManager::hasAMFRequest() || !AerialStartupManager::isDirectCall())
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
			if(AerialStartupManager::hasAMFRequest() || !AerialStartupManager::isDirectCall())
				return;

			echo "\n\t</body>\n</html>";
		}

		private function initializeAerial()
		{
			$_configPath = $this->getConfigPath();
			$_basePath = $this->getBasePath();

			$this->_config = @simplexml_load_file($_configPath);
			if(file_exists(dirname($_configPath)."/config-alt.xml") && is_readable(dirname($_configPath)."/config-alt.xml"))
				$this->_config_alt = @simplexml_load_file(dirname($_configPath)."/config-alt.xml");

			$modelsPath = realpath(conf("paths/php-models", true, false));
			$servicesPath = realpath(conf("paths/php-services", true, false));

			if(!realpath(conf("paths/lib")))
			{
				if(realpath($_basePath."/".conf("paths/lib")))          // check against the base path
				{
					// reassign config path
					$this->_config->paths->lib = realpath($_basePath."/".conf("paths/lib"));
				}
				else
				{
					AerialStartupManager::error("The path to the <strong>Aerial library</strong> is invalid or cannot be accessed: ".
					                            "[<strong>".conf("paths/lib", true, false)."</strong>]");
				}
			}

			if(!$this->_config)
				AerialStartupManager::error("There is a syntax error in config.xml");

			if(!$modelsPath)             // models path is relative, so try resolve the absolute value
			{
				$isRelative = $this->isPathRelative(conf("paths/php-models"));

				if($isRelative)
					$modelsPath = $_basePath."/".conf("paths/php-models");
				else
					$modelsPath = conf("paths/php-models");

				if(!realpath($modelsPath))
				{
					// if no models path could be found at this point, try creating it
					if($this->createDirectories)
					{
						mkdir($modelsPath, 0766, true);
					}
					
					$modelsPath = realpath($modelsPath);

					if(!$modelsPath)
					{
						if(!$this->createDirectories)
						{
							$thisPage = $this->getCurrentPageURL();

							AerialStartupManager::warn("PHP models path could not be found [".conf("paths/php-models")."]".
										"<br/>Click <a href='$thisPage?createDirectories=true'>here</a> to create them.");
						}
						else
						{
							AerialStartupManager::error("PHP models path could not be found [".conf("paths/php-models")."]");
						}
					}
				}
			}

			if(!$servicesPath)             // services path is relative, so try resolve the absolute value
			{
				$isRelative = $this->isPathRelative(conf("paths/php-services"));

				if($isRelative)
					$servicesPath = $_basePath."/".conf("paths/php-services");
				else
					$servicesPath = conf("paths/php-services");

				if(!realpath($servicesPath))
				{
					// if no services path could be found at this point, try creating it
					if($this->createDirectories)
					{
						mkdir($servicesPath, 0766, true);
					}

					$servicesPath = realpath($servicesPath);

					if(!$servicesPath)
					{
						if(!$this->createDirectories)
						{
							$thisPage = $this->getCurrentPageURL();

							AerialStartupManager::warn("PHP services path could not be found [".conf("paths/php-services")."]".
										"<br/>Click <a href='$thisPage?createDirectories=true'>here</a> to create them.");
						}
						else
						{
							AerialStartupManager::error("PHP services path could not be found [".conf("paths/php-services")."]");
						}
					}
				}
			}

			$dynamicPaths = array(
				"base" => $_basePath,
				"config" => dirname($_configPath),
				"project" => dirname(dirname($_configPath)),        // convention: config must always be in root of project
				"aerialframework" => conf("paths/lib", true, false)."/php/aerialframework",
				"encryption" => conf("paths/lib", true, false)."/php/aerialframework/encryption",
				"internal-services" => conf("paths/lib", true, false)."/php",
				"doctrine" => conf("paths/lib", true, false)."/php/doctrine",
				"amfphp" => conf("paths/lib", true, false)."/php/amfphp",
				"php-models" => $modelsPath,
				"php-services" => $servicesPath
			);

			$this->assignDynamicPaths($dynamicPaths);

			if(!realpath($_basePath))
				AerialStartupManager::error("Project root directory is invalid [$_basePath]");

			include_once(dirname(__FILE__)."/server.php");
			date_default_timezone_set(conf("options/timezone", false, false));				// Required for PHP >= 5.3

			if(!is_readable(conf("paths/aerialframework")."core/Bootstrapper.php"))
			{
				AerialStartupManager::error("The <strong>Aerial core</strong> directory is unreadable - check your 'lib' path in <i>config.xml</i>
												<br/>Hint: Change the directory owner to <strong>".AerialStartupManager::getGroup()."</strong>
												and ensure that the path is correct.");
			}
			else if(!realpath(conf("paths/aerialframework")."core/Bootstrapper.php"))
				AerialStartupManager::error("The <strong>Aerial core</strong> could not be located - check your 'lib' path in <i>config.xml</i>");
			else
			{
				require_once(conf("paths/aerialframework")."core/Bootstrapper.php");

				Bootstrapper::getInstance();
			}
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

		/**
		 * Adds a number of configuration options at runtime so that the config.xml file can be smaller
		 *
		 * @param  $paths
		 * @return void
		 */
		private function assignDynamicPaths($paths)
		{
			foreach($paths as $key => $path)
				$this->_config->paths->{$key} = realpath($path);
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

			if(!$location)
			{
				// if no base can be found, try using the base path from this file
				$referencePoint = realpath(dirname(__FILE__));
				if($referencePoint)
				{
					$fromConfig = (string) $projectXML->location;
					$location = realpath($referencePoint."/".$fromConfig);
				}
			}

			if($filetype != "xml")
				$location = $location."/config.xml";

			if(!$location || !file_exists($location))
			{
				AerialStartupManager::error("Please ensure that the path used for the <strong>config.xml</strong>
						file in <strong>server/.project</strong> is <u>valid</u> and <u>readable</u>.");
			}

			return $location;
		}

		private function getBasePath()
		{
			$directory = dirname(__FILE__);
			if(!file_exists("$directory/.project"))
				AerialStartupManager::error("Please ensure that the path used for the <strong>server/.project</strong> file is valid.");

			$projectXML = @simplexml_load_file("$directory/.project");
			if(!$projectXML)
				AerialStartupManager::error("There is an XML syntax error in the <strong>server/.project</strong> file");

			$location = realpath((string) $projectXML->base);
			if(!$location)
			{
				AerialStartupManager::error("The base path for the project is invalid in the <strong>server/.project</strong> file. ".
				                                "This value should point to the root of the project.");
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

	$server = new AerialServer();
	$server->start();

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



	class AerialStartupManager
	{
		private static $displayedProductionMessage;

		private static $_request;

		public static function setAMFRequest($request)
		{
			self::$_request = $request;
		}

		public static function error($error)
		{
			self::log($error, E_USER_ERROR);
		}

		public static function warn($warning)
		{
			self::log($warning, E_USER_WARNING);
		}

		public static function info($message)
		{
			self::log($message, "Success");
		}

		public static function hasAMFRequest()
		{
			return self::$_request != null && self::isDirectCall();
		}

		/**
		 *  To determine if Aerial is being used for a request, compare the requested script filename with the current file
		 *	 ...if they match, it is most likely an AMF request (direct call to server.php, not including server.php in another file)
		 *
		 * @return string
		 */
		public static function isDirectCall()
		{
			return realpath($_SERVER["SCRIPT_FILENAME"]) === realpath(__FILE__);
		}

		public static function getAMFRequest()
		{
			return self::$_request;
		}

		public static function getGroup()
		{
			$pid = @posix_getgid();
			$group = @posix_getgrgid($pid);
			return $group["name"];
		}

		private static function log($message, $type)
		{
			global $server;

			if($server->_config)
			{
				if(((string) $server->_config->options->{"debug-mode"}) == "")        // no debug-mode node in config
				{
					$message = "You are missing the <strong>options/debug-mode</strong> node in your <i>config.xml</i> file";
					$type = E_USER_ERROR;
				}
				else if(!conf("options/debug-mode",false,false))
				{
					if(!self::$displayedProductionMessage)
					{
						self::simpleLog("<p>Enable debug mode to view errors.</p>", E_USER_ERROR);
						self::$displayedProductionMessage = true;
					}
					return;
				}
			}

			// if an AMF request has been received, let amfPHP handle the errors, only display startup problems
			if(AerialStartupManager::hasAMFRequest())
			{
				self::simpleLog($message, $type);
				return;
			}

			// only display success messages if no AMF request is present (i.e. direct call to server.php)
			if(AerialStartupManager::isDirectCall())
			{
				switch($type)
				{
					case E_USER_ERROR:
						die("<div class='error'><h2>Fatal Error</h2><p>".$message."</p></h2></div>");
						break;
					case E_USER_WARNING:
						echo("<div class='warning'><h2>Warning</h2><p>".$message."</p></h2></div>");
						break;
					case "Success":
						echo("<div class='success'><h2>Info</h2><p>".$message."</p></h2></div>");
						break;
				}
			}
		}

		private static function simpleLog($message, $type)
		{
			if($type == E_USER_ERROR || $type == E_ERROR)
				die("Aerial startup warning: ".strip_tags($message));
		}
	}
?>