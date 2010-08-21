<?php
	// path constants
	date_default_timezone_set('Africa/Johannesburg');										// Required for PHP >= 5.3
	
	define("AERIAL_FILE_CHMOD", 0664);													// Default Aerial permission for generated files
	define("AERIAL_DIR_CHMOD", 0777);													// Default Aerial permission for generated directories
	
	define("AMFPHP_USE_ARRAYCOLLECTION", true);											// False will return Array (Flash based projects)
	
	define("AERIAL_BASE_PATH", realpath(dirname(__FILE__)));							// Aerial base path
	define("AERIAL_INTERNAL", realpath(AERIAL_BASE_PATH));								// Internal path for core functionality
	define("AERIAL_EXTERNAL", realpath(AERIAL_BASE_PATH."/.."));						// Path outside of aerial core
	define("DOCTRINE_PATH", realpath(AERIAL_BASE_PATH. "/../doctrine"));				// Path to Doctrine library
	define("AMFPHP_PATH", realpath(AERIAL_BASE_PATH."/../../public_html/amfphp"));		// Path to AMFPHP library
	define("AMFPHP_GATEWAY_URL", "http://localhost./open-source/aerial/0.8-release/src_php/public_html/amfphp/gateway.php");				// Path to AMFPHP gateway.php file
	
	define("BACKEND_PATH", AERIAL_EXTERNAL."/../model");								// Path to project development backend
	
	define("FRONTEND_PATH", AERIAL_EXTERNAL."/../../src_flex");							// Path to project development fronend
    define("PLUGINS_PATH", AERIAL_BASE_PATH."/plugins");							    // Path to plugins
    
    define("USE_AUTH", true);
	
	// models & services
	
	define("INTERNAL_SERVICES_PATH", AERIAL_EXTERNAL."/services");			// Path to internal services
	define("BACKEND_SERVICES_PATH", BACKEND_PATH."/services");				// Path to generated backend services
	define("BACKEND_MODELS_PATH", BACKEND_PATH."/vo");						// Path to generated backend vo's
	define("FRONTEND_MODELS_PACKAGE", "model.vo");										// ActionScript package for generated vo's
	define("FRONTEND_MODELS_PATH", FRONTEND_PATH."/model/vo");				// Path to generated frontend vo
	define("FRONTEND_SERVICES_PACKAGE", "model.services");								// ActionScript package for generated services
	define("FRONTEND_SERVICES_PATH", FRONTEND_PATH."/model/services");		// Path to generated frontend services
	
	define("AMFPHP_MAP_PATH", BACKEND_PATH);										// Path to backend models

	// connection constants

	define("DB_ENGINE", "mysql");													// Database engine type
	define("DB_NAME", "aerial_release");															// Database name
	define("DB_HOST", "localhost.");													// Database host
	define("DB_USER", "root");															// Database user
	define("DB_PASSWORD", "mac150189``");														// Database password
	define("CONNECTION_NAME", "doctrine");											// Connection name (internal feature)
	
	require_once(AERIAL_BASE_PATH."/Bootstrapper.php");
	Bootstrapper::getInstance();
?>