<?php
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
			return self::$_request != null;
		}

		public static function getAMFRequest()
		{
			return self::$_request;
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
			if(self::$_request)
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
				die("Aerial startup warning: ".strip_tags($message));
		}
	}
?>