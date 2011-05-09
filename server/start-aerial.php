<?php
	$_configPath = getConfigPath();

	$_config = simplexml_load_file($_configPath);
	$_config_alt = @simplexml_load_file(dirname($_configPath)."/config-alt.xml");

	if(!$_config)
		die("Could not locate configuration file at ".realpath($_configPath));

    // add config path to config XML dynamically
    $_config->options->{"config-path"} = $_configPath;

	// Get the base path
	$_base = conf("paths/project");

	if(!realpath($_base))
		throw new Exception("Project root directory is invalid [$_base]");

	$_base = realpath($_base);
	
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
			throw new Exception("Could not find configuration value [$path]");

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
					trigger_error("$subnode node is missing in the database configuration");
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
			die("Aerial could not find your configuration file. Please make sure that you have a file named
					\".project\" in your \"server\" directory.");
		}

		try
		{
			$projectXML = @simplexml_load_file("$directory/.project");
			if(!$projectXML)
				throw new Exception("Syntax error");

			$location = realpath((string) $projectXML->location);
			$filetype = substr($location, strrpos($location, ".") + 1);

			if(!$location || !file_exists($location) || $filetype != "xml")
				throw new Exception("Invalid location");

			return $location;
		}
		catch(Exception $e)
		{
			$message = "An unexpected error occurred when reading the \".project\" file.";

			switch($e->getMessage())
			{
				case "Syntax error":
					$message = "There is an XML syntax error in the \".project\" file";
					break;
				case "Invalid location":
					$message = "Please ensure that the location used in the \".project\" file is valid.";
					break;
			}

			die($message);
		}
	}

	require_once(conf("paths/aerial")."core/Bootstrapper.php");
	Bootstrapper::getInstance();
?>