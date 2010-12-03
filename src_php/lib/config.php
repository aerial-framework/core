<?php
	// path constants
	date_default_timezone_set('America/New_York');						// Required for PHP >= 5.3

	$_configPath = realpath(dirname(__FILE__)."/../config");
	$_config = simplexml_load_file($_configPath."/config.xml");
	$_config_alt = @simplexml_load_file($_configPath."/config-alt.xml");

	// Get the base path
	$_base = conf("paths/project");

	if(!realpath($_base))
		throw new Exception("Project root directory is invalid [$_base]");

	$_base = realpath($_base);

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
			$value = (string) $value[0];
			return $value;
		}

		if($value == "true" || $value == "false")				// using (bool) doesn't work properly
			$value = $value == "true";

		return $value;
	}

	require_once(conf("paths/aerial")."Bootstrapper.php");
	Bootstrapper::getInstance();
?>