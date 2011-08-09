<?php
class ConfigXml
{
	public $config;
	public $modelsPath;
	public $servicesPath;
	
	private static $instance;
	
	private function __construct()
	{
		$this->init();
		$this->setPath('php-models');
		$this->setPath('php-services');
	}
	
	public static function getInstance()
	{
		if (!isset(self::$instance)) 
		{
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
	}

	private function init()
	{
		if(!defined('CONFIG_PATH'))
		{
			trigger_error("CONFIG_PATH has not been defined");
		}
		
		$configPath = CONFIG_PATH . DIRECTORY_SEPARATOR . 'config.xml';
		$configAltPath = CONFIG_PATH . DIRECTORY_SEPARATOR . 'config-alt.xml';

		if (file_exists($configPath))
		{
			$this->config = new SimpleXMLElement($configPath,null,true);
		}

		if (file_exists($configAltPath))
		{
			$configAlt = new SimpleXMLElement($configAltPath,null,true);
			if($this->config)
			{
				$this->mergeXml($this->config, $configAlt);
			}
		}
		
	}
	
	//Helper
	private function setPath($path)
	{
		//Check if it's absolute.
		$absolutePath =  realpath($this->config->paths->{$path});
		
		//Check if it's relative to the config.xml.
		if(!$absolutePath)
			$absolutePath = realpath(CONFIG_PATH . DIRECTORY_SEPARATOR . $this->config->paths->{$path});
		
		//Check if it's relative to the base path (above the web root).
		if(!$absolutePath)
			$absolutePath = realpath(BASE_PATH . DIRECTORY_SEPARATOR . $this->config->paths->{$path});
			
		//Check if it's relative to the web root
		if(!$absolutePath)
			$absolutePath = realpath(WEB_PATH . DIRECTORY_SEPARATOR . $this->config->paths->{$path});
			
		//Give up
		if(!$absolutePath)
			trigger_error("Please check the <$path> value in config.xml. ");
		
		if($path == 'php-models')
		{
			$this->modelsPath = $absolutePath;
		}
		elseif ($path == 'php-services')
		{
			$this->servicesPath = $absolutePath;
		}
	}

	//mergeXml expects all element names to be unique.
	private function mergeXml(&$xml, $xmlAlt)
	{
		foreach ($xmlAlt->children() as $xmlAltChild)
		{
			$elementName = $xmlAltChild->getName();

			if(!$xml->{$elementName}) // Check if the element exists.
			{
				$element = $xml->addChild($elementName, $xmlAltChild);
				foreach ($xmlAltChild->attributes() as $key => $value)
				{
					$element->addAttribute($key, $value);
				}
				if(count($xmlAltChild) > 0) //Recurse through additinal leaves.
				{
					$this->mergeXml($element, $xmlAltChild);
				}
			}
			elseif(count($xmlAltChild) == 0) //Check if the configAlt element is a leaf.
			{
				$xml->{$elementName} = $xmlAltChild;
				foreach ($xmlAltChild->attributes() as $key => $value)
				{
					$xml->{$elementName}[$key] = $value;
				}
			}
			else
			{
				$this->mergeXml($xml->{$elementName}, $xmlAltChild);
			}
		}
	}
	
	public function createPaths()
	{
		//Not sure if we should be creating any directories
		//mkdir($modelsPath, 0766, true);
	}

	public function toString()
	{
		$dom = new DOMDocument('1.0');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($this->config->asXML());
		return $dom->saveXML();
	}


}