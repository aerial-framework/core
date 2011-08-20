<?php

/*
 * Set the location of config.php.
 */
$config = realpath($_SERVER['DOCUMENT_ROOT'] . '/../config/config.php');

if(!file_exists($config))
	die("Error: cannot find 'config.php' on line 6 of " . __FILE__);

include_once($config);
import("aerialframework.core.AerialServer");

$server = new AerialServer();
$server->start();