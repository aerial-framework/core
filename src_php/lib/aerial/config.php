<?php
	// path constants
	date_default_timezone_set('America/New_York');										// Required for PHP >= 5.3

	define("AERIAL_FILE_CHMOD", 0664);													// Default Aerial permission for generated files
	define("AERIAL_DIR_CHMOD", 0777);													// Default Aerial permission for generated directories
	
	define("AMFPHP_USE_ARRAYCOLLECTION", true);											// False will return Array (Flash based projects)

	define("AERIAL_BASE_PATH", realpath(dirname(__FILE__)));							// Aerial base path
	define("AERIAL_INTERNAL", realpath(AERIAL_BASE_PATH));								// Internal path for core functionality
	define("AERIAL_EXTERNAL", realpath(AERIAL_BASE_PATH."/.."));						// Path outside of aerial core
	define("DOCTRINE_PATH", realpath(AERIAL_BASE_PATH. "/../doctrine"));				// Path to Doctrine library
	define("AMFPHP_PATH", realpath(AERIAL_BASE_PATH."/../../public_html/amfphp"));		// Path to AMFPHP library
	
	define("BACKEND_PATH", realpath(AERIAL_EXTERNAL."/../model"));						// Path to project development backend

	define("FRONTEND_PATH", realpath(AERIAL_EXTERNAL."/../../src_flex"));				// Path to project development fronend

	define("UTILS", realpath(AERIAL_INTERNAL."/utils"));				                // Path to utils
	define("USE_AUTH", true);															// Use authentication
	
	// models & services
	
	define("INTERNAL_SERVICES_PATH", realpath(AERIAL_EXTERNAL."/services"));			// Path to internal services
	define("BACKEND_SERVICES_PATH", realpath(BACKEND_PATH."/services"));				// Path to generated backend services
	define("BACKEND_MODELS_PATH", realpath(BACKEND_PATH)."/vo");						// Path to generated backend vo's
	define("FRONTEND_MODELS_PACKAGE", "model.vo");										// ActionScript package for generated vo's
	define("FRONTEND_MODELS_PATH", realpath(FRONTEND_PATH."/model/vo"));				// Path to generated frontend vo
	define("FRONTEND_SERVICES_PACKAGE", "model.services");								// ActionScript package for generated services
	define("FRONTEND_SERVICES_PATH", realpath(FRONTEND_PATH."/model/services"));		// Path to generated frontend services
	define("FRONTEND_CONFIG_CLASS", "com.forum.config.Config");							// Fully qualified frontend configuration path.  Needs to implement IConfig

	define("VO_SUFFIX", "VO");
	
	// connection constants
	// PRODUCTION_SERVER defined in gateway.php
	
	if(PRODUCTION_SERVER)
	{
		define("DB_ENGINE", "mysql");													// Database engine type
		define("DB_NAME", "aerial_forum");												// Database name
		define("DB_HOST", "localhost");													// Database host
		define("DB_USER", "root");															// Database user
		define("DB_PASSWORD", "secret");														// Database password
		define("CONNECTION_NAME", "doctrine");
	
	}else{
		define("DB_ENGINE", "mysql");													// Database engine type
		define("DB_NAME", "aerial_forum");												// Database name
		define("DB_HOST", "localhost");													// Database host
		define("DB_USER", "root");														// Database user
		define("DB_PASSWORD", "secret");

        // Database password
		define("CONNECTION_NAME", "doctrine");
	}
	
	// Connection name (internal feature)
	
	require_once(AERIAL_BASE_PATH."/Bootstrapper.php");
	Bootstrapper::getInstance();
?>