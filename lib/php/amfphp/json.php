<?php
	
	/**
	 * JSON gateway
	 */
	
	include_once("globals.php");
	
	include_once("core/json/app/Gateway.php");
	
	$gateway = new Gateway();
	
	$gateway->setBaseClassPath($servicesPath);
	
	$gateway->service();
?>