<?php

class Aerial_Core{

	//Hydrators
	const HYDRATE_AMF_ARRAY = "Aerial_Hydrator_ArrayDriver";
	const HYDRATE_AMF_COLLECTION = "Aerial_Hydrator_CollectionDriver";

	private static $_path;

	public static function autoload($className)
	{
		if (0 !== stripos($className, 'Aerial_') || class_exists($className, false) || interface_exists($className, false)) {
			return false;
		}

		$class = self::getPath() . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

		if (file_exists($class)) {
			require $class;

			return true;
		}

		return false;
	}

	public static function getPath()
	{
		if ( ! self::$_path) {
			self::$_path = realpath(dirname(__FILE__) . '/..');
		}

		return self::$_path;
	}

}
