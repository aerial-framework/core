<?php

define('CONFIG_PATH', dirname(__FILE__));

/*
 * Set the location of include.php
 * This can normally be found in Aerial's 'lib/php/include.php'
 */
$include = realpath(CONFIG_PATH . '/../../Aerial/lib/php/include.php');

if(!file_exists($include))
	die("Error: cannot find 'include.php' on line 9 of " . __FILE__);


include_once($include);