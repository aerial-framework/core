<?php

class AerialStartupManager
{
	private static $displayedProductionMessage;

	private static $_request;

	private static $_showStartupMessages;

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
		return self::$_request != null;
	}

	public static function setStartupMessagesDisplayFlag($flag)
	{
		self::$_showStartupMessages = $flag;
	}

	public static function showStartupMessages()
	{
		return self::$_showStartupMessages;
	}

	/**
	 *  To determine if Aerial is being used for a request, compare the requested script filename with the current file
	 *	 ...if they match, it is most likely an AMF request (direct call to server.php, not including server.php in another file)
	 *
	 * @return string
	 */
	//		public static function isDirectCall()
	//		{
	//			return realpath($_SERVER["SCRIPT_FILENAME"]) === realpath(__FILE__);
	//		}

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
		if((ConfigXml::getInstance()->config->options->{"debug-mode"}) == "")        // no debug-mode node in config
		{
			$message = "You are missing the <strong>options/debug-mode</strong> node in your <i>config.xml</i> file";
			$type = E_USER_ERROR;
		}
		else if(!ConfigXml::getInstance()->debugMode)
		{
			if(!self::$displayedProductionMessage)
			{
				self::simpleLog("<p>Enable debug mode to view errors.</p>", E_USER_ERROR);
				self::$displayedProductionMessage = true;
			}
			return;
		}

		// if an AMF request has been received, let amfPHP handle the errors, only display startup problems
		if(!AerialStartupManager::showStartupMessages())
		{
			self::simpleLog($message, $type);
			return;
		}

		// only display success messages if no AMF request is present (i.e. direct call to server.php)
		if(AerialStartupManager::showStartupMessages())
		{
			switch($type)
			{
				case E_USER_ERROR:
					die("<div class='error'><h2>Fatal Error</h2><p>".$message."</p></h2></div>\n");
					break;
				case E_USER_WARNING:
					echo("<div class='warning'><h2>Warning</h2><p>".$message."</p></h2></div>\n");
					break;
				case "Success":
					echo("<div class='success'><h2>Info</h2><p>".$message."</p></h2></div>\n");
					break;
			}
		}
	}

	private static function simpleLog($message, $type)
	{
		if($type == E_USER_ERROR || $type == E_ERROR)
			die("--Aerial startup warning--\n".strip_tags($message));
	}
}
?>