<?php

$basePath = realpath($_SERVER['DOCUMENT_ROOT']);

if(!@include_once(realpath($basePath . '/../config/config.php')))
	die("Cannot find config.php");

import("aerialframework.core.AerialServer");

// we need to check to see if it's an application/x-amf  request.
$server = new AerialServer();
$server->start();

