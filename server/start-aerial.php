<?php
	$_configPath = getConfigPath();

	$_config = @simplexml_load_file($_configPath);
	$_config_alt = @simplexml_load_file(dirname($_configPath)."/config-alt.xml");

	if(!$_config)
		StartupHelper::error("There is a syntax error in config.xml");

    // add config path to config XML dynamically
    $_config->options->{"config-path"} = $_configPath;

	// Get the base path
	$_base = dirname($_configPath);

	if(!realpath($_base))
		StartupHelper::error("Project root directory is invalid [$_base]");
	
	date_default_timezone_set(conf("options/timezone", false, false));						// Required for PHP >= 5.3

	/**
	 * @throws Exception
	 * @param  $path
	 * @param  $isPath				Whether the value is a path
	 * @param bool $trailingSlash	Whether to return the value with a trailing slash to help with concatenation
	 * @return string				Return the value
	 */
	function conf($path, $isPath=true, $trailingSlash=true, $subnode=false)
	{
		global $_config;
		global $_base;
		global $_config_alt;

		$node = $_config->xpath($path);
		if($_config_alt && $_config_alt->xpath($path))
			$node = $_config_alt->xpath($path);

		if(!$node)
		{
			StartupHelper::error("Could not find configuration value <strong>$path</strong>!<br/>".
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
					StartupHelper::error("$subnode node is missing in the database configuration");
			}
			
			$value = (string) $value[0];
			return $value;
		}

		if($value == "true" || $value == "false")				// using (bool) doesn't work properly
			$value = $value == "true";

		return $value;
	}

	function getConfigPath()
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
			StartupHelper::error("Please ensure that the path used for the <strong>.project</strong> file is valid.");

		$projectXML = @simplexml_load_file("$directory/.project");
		if(!$projectXML)
			StartupHelper::error("There is an XML syntax error in the <strong>.project</strong> file");

		$location = realpath((string) $projectXML->location);
		$filetype = substr($location, strrpos($location, ".") + 1);

		if($filetype != "xml")
			$location = $location."/config.xml";

		if(!$location || !file_exists($location))
			StartupHelper::error("Please ensure that the path used for the <strong>config.xml</strong> in <i>.project</i> is valid.");

		return $location;
	}

	if(!is_readable(conf("paths/aerial")."core/Bootstrapper.php"))
		StartupHelper::error("The <strong>Aerial core</strong> directory is unreadable - check your 'aerial' path in <i>config.xml</i>");
	else if(!realpath(conf("paths/aerial")."core/Bootstrapper.php"))
		StartupHelper::error("The <strong>Aerial core</strong> could not be located - check your 'aerial' path in <i>config.xml</i>");
	else
	{
		require_once(conf("paths/aerial")."core/Bootstrapper.php");

		Bootstrapper::getInstance();
	}

	class StartupHelper
	{
		static private $displayedProductionMessage;

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

		private static function log($message, $type)
		{
			global $request;
			global $_config;

			if(((string) $_config->options->{"debug-mode"}) == "")        // no debug-mode node in config
			{
				$message = "You are missing the <strong>options/debug-mode</strong> node in your <i>config.xml</i> file";
				$type = E_USER_ERROR;
			}
			else if(!conf("options/debug-mode",false,false))
			{
				if(!self::$displayedProductionMessage)
				{
					echo "<p>Server is in production mode.</p>";
					self::$displayedProductionMessage = true;
				}
				return;
			}

			// if an AMF request has been received, let amfPHP handle the errors, only display startup problems
			if($request)
			{
				self::simpleLog($message, $type);
				return;
			}

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

		private static function simpleLog($message, $type)
		{
			if($type == E_USER_ERROR || $type == E_ERROR)
				die("Aerial startup warning: ".$message);
		}
	}
?>