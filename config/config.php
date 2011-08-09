<?php

define('CONFIG_PATH', dirname(__FILE__));

if(!@include_once((realpath(CONFIG_PATH . "/../aerial/lib/php/include.php"))))
	die("Cannot find Aerial library");

