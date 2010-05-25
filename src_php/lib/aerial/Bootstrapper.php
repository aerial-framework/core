<?php
	require_once(DOCTRINE_PATH.'/Doctrine.php');
	require_once(AMFPHP_PATH.'/globals.php');

	class Bootstrapper
	{
		public $conn;
		public $manager;

		private static $_instance;

		public function __construct()
		{
			if(isset(self::$_instance))
				trigger_error("You must not call the constructor directly! This class is a Singleton");
		}

		private static function init()
		{
			spl_autoload_register(array('Doctrine', 'autoload'));
			spl_autoload_register(array('Doctrine_Core', 'modelsAutoload'));

			self::$_instance->manager = Doctrine_Manager::getInstance();

			self::$_instance->manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
			self::$_instance->manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
			self::$_instance->manager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);

			$connectionString = DB_ENGINE."://".
								DB_USER.":".
								DB_PASSWORD.
								"@".DB_HOST."/".
								DB_NAME;
			self::$_instance->conn = Doctrine_Manager::connection($connectionString, CONNECTION_NAME);
			Doctrine_Core::loadModels(BACKEND_MODELS_PATH);
			
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