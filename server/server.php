<?php

require_once(realpath($_SERVER['DOCUMENT_ROOT'] . '/../config/config.php')); 
import("aerialframework.core.AerialServer");

// we need to check to see if it's an application/x-amf  request.
$server = new AerialServer();
$server->start();

