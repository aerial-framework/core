<?php
define('LIB_PATH', dirname(__FILE__));
define('WEB_PATH', realpath($_SERVER['DOCUMENT_ROOT']));
define('BASE_PATH', realpath(WEB_PATH . "/../")); 

function import($classPath) {
	$importFile = str_replace(".", DIRECTORY_SEPARATOR, $classPath) . ".php";
	require_once(LIB_PATH . DIRECTORY_SEPARATOR . $importFile);
}