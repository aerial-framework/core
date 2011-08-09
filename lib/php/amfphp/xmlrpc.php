<?php

	/**
	 * XML-RPC server
	 */
	include_once("globals.php");

	include_once("core/xmlrpc/app/Gateway.php");
	
	$gateway = new Gateway();
	
	$gateway->setBaseClassPath($servicesPath);
	
	$gateway->service();
?>