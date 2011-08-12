<?php

$basePath = realpath($_SERVER['DOCUMENT_ROOT']);

if(!@include_once(realpath($basePath . '/../config/config.php')))
	die("Cannot find config.php");

import("aerialframework.core.AerialServer");

$server = new AerialServer();
$server->start();

