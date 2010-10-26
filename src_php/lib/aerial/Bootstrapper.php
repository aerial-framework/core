<?php
	require_once(DOCTRINE_PATH.'/Doctrine.php');
	require_once(AMFPHP_PATH.'/globals.php');
	require_once("config/Authentication.php");
	require_once(DOCTRINE_PATH.'/Aerial.php');
	require_once(UTILS."/ModelMapper.php");

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
			xdebug_disable();
			set_exception_handler(array("Bootstrapper", "exceptionHandler"));

			spl_autoload_register(array('Doctrine', 'autoload'));
			spl_autoload_register(array('Doctrine_Core', 'modelsAutoload'));
			spl_autoload_register(array('Aerial', 'autoload'));

			require_once(DOCTRINE_PATH.'/Aerial/Connection/Aerial_Connection.php');
			require_once(EXCEPTIONS."/Aerial_Exception.php");
			
			self::$_instance->manager = Doctrine_Manager::getInstance();
			
			self::$_instance->manager->registerHydrator(Aerial_Core::HYDRATE_AMF_COLLECTION, Aerial_Core::HYDRATE_AMF_COLLECTION);
			self::$_instance->manager->registerHydrator(Aerial_Core::HYDRATE_AMF_ARRAY, Aerial_Core::HYDRATE_AMF_ARRAY);
			
			self::$_instance->manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
			self::$_instance->manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
			self::$_instance->manager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);
			require_once(DOCTRINE_PATH."/Aerial/Record/Aerial_Record.php");

			self::setCustomConnections();

			$connectionString = "aerial-".DB_ENGINE."://".
								DB_USER.":".
								DB_PASSWORD.
								"@".DB_HOST."/".
								DB_NAME;
			self::$_instance->conn = Doctrine_Manager::connection($connectionString, CONNECTION_NAME);
			
			if(!file_exists(BACKEND_MODELS_PATH))					// if the folder does not exist, create it to avoid errors!
				mkdir(BACKEND_MODELS_PATH, AERIAL_DIR_CHMOD);
			
			Doctrine_Core::loadModels(BACKEND_MODELS_PATH);
			
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
			self::$_instance->manager->registerConnectionDriver('aerial-sqlite', 'Aerial_Connection');
			self::$_instance->manager->registerConnectionDriver('aerial-sqlite2', 'Aerial_Connection');
			self::$_instance->manager->registerConnectionDriver('aerial-sqlite3', 'Aerial_Connection');
			self::$_instance->manager->registerConnectionDriver('aerial-dblib', 'Aerial_Connection');
			self::$_instance->manager->registerConnectionDriver('aerial-mysql', 'Aerial_Connection');
			self::$_instance->manager->registerConnectionDriver('aerial-oci8', 'Aerial_Connection');
			self::$_instance->manager->registerConnectionDriver('aerial-oci', 'Aerial_Connection');
			self::$_instance->manager->registerConnectionDriver('aerial-pgsql', 'Aerial_Connection');
			self::$_instance->manager->registerConnectionDriver('aerial-odbc', 'Aerial_Connection');
			self::$_instance->manager->registerConnectionDriver('aerial-mock', 'Aerial_Connection');
			self::$_instance->manager->registerConnectionDriver('aerial-oracle', 'Aerial_Connection');
		}

		public static function exceptionHandler(Exception $ex)
		{
			trigger_error($ex->getMessage());
			//die(get_class($ex));

		}
	}
?>