<?php
	class Authentication
	{
		private static $_instance;

		private $config;

		public function __construct()
		{
			if(isset(self::$_instance))
				trigger_error("You must not call the constructor directly! This class is a Singleton");
		}
		
		private static function init()
		{
			$file = dirname(__FILE__)."/authentication.xml";
			$f = fopen($file, "r+");
			$contents = fread($f, filesize($file));
			fclose($f);
			
			self::getInstance()->config = new SimpleXMLElement($contents);
		}
		
		public function isValid()
		{
			session_start();
			$credentials = $_SESSION["credentials"];
			
			$users = self::getInstance()->config->xpath("//user[@name='{$credentials->username}'".
															"and @password='{$credentials->password}']");
			
			die(empty($users));
		}

		public static function getInstance()
		{
			if(!isset(self::$_instance))
			{
				self::$_instance = new self();
				self::init();
			}

			return self::$_instance;
		}
	}
?>