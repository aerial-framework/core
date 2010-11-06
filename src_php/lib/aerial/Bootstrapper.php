<?php
	require_once(conf("paths/doctrine").'Doctrine.php');
	require_once(conf("paths/libs")."config/Authentication.php");
	require_once(conf("paths/doctrine").'Aerial.php');
	require_once(conf("paths/aerial")."utils/ModelMapper.php");

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
			spl_autoload_register(array('Aerial', 'autoload'));

			require_once(conf("paths/doctrine").'Aerial/Connection/Aerial_Connection.php');
			require_once(conf("paths/aerial")."exceptions/Aerial_Exception.php");
			
			self::$_instance->manager = Doctrine_Manager::getInstance();
			
			self::$_instance->manager->registerHydrator(Aerial_Core::HYDRATE_AMF_COLLECTION, Aerial_Core::HYDRATE_AMF_COLLECTION);
			self::$_instance->manager->registerHydrator(Aerial_Core::HYDRATE_AMF_ARRAY, Aerial_Core::HYDRATE_AMF_ARRAY);
			
			self::$_instance->manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
			self::$_instance->manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
			self::$_instance->manager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);
			require_once(conf("paths/doctrine")."Aerial/Record/Aerial_Record.php");

			self::setCustomConnections();

			$connectionString = conf("database", false, false, "engine")."://".
							conf("database", false, false, "username").":".
							conf("database", false, false, "password").
							"@".conf("database", false, false, "host")."/".
							conf("database", false, false, "database");
								
			self::$_instance->conn = Doctrine_Manager::connection($connectionString, "doctrine");

			$php_path = conf("code-generation/php");
			$package = conf("code-generation/package", false);

			if($package)
				$php_path .= implode(DIRECTORY_SEPARATOR, explode(".", $package)).DIRECTORY_SEPARATOR;

			$models_path = $php_path.conf("code-generation/models-folder", true, false);

			if(!file_exists($models_path))					// if the folder does not exist, create it to avoid errors!
				mkdir($models_path, conf("options/directory-mode", false), true);

			Doctrine_Core::loadModels($models_path);
			
			Authentication::getInstance();

			require_once(dirname(__FILE__)."/../services/core/aerial/Configuration.php");
		}
		
		public static function setCredentials($username, $password)
		{
			$credentials = new stdClass();
			$credentials->username = $username;
			$credentials->password = $password;
			 
			session_start();
            $_SESSION["credentials"] = $credentials;
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

		/**
		 * Register custom Doctrine connections to catch connection exceptions
		 *
		 * @static
		 * @return void
		 */
		private static function setCustomConnections()
		{
			self::$_instance->manager->registerConnectionDriver('sqlite', 'Aerial_Connection');
			self::$_instance->manager->registerConnectionDriver('sqlite2', 'Aerial_Connection');
			self::$_instance->manager->registerConnectionDriver('sqlite3', 'Aerial_Connection');
			self::$_instance->manager->registerConnectionDriver('dblib', 'Aerial_Connection');
			self::$_instance->manager->registerConnectionDriver('mysql', 'Aerial_Connection');
			self::$_instance->manager->registerConnectionDriver('oci8', 'Aerial_Connection');
			self::$_instance->manager->registerConnectionDriver('oci', 'Aerial_Connection');
			self::$_instance->manager->registerConnectionDriver('pgsql', 'Aerial_Connection');
			self::$_instance->manager->registerConnectionDriver('odbc', 'Aerial_Connection');
			self::$_instance->manager->registerConnectionDriver('mock', 'Aerial_Connection');
			self::$_instance->manager->registerConnectionDriver('oracle', 'Aerial_Connection');
		}
	}
?>