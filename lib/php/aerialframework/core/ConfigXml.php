<?php
class ConfigXml
{
	public $config;
	
	public $modelsPath;
	public $servicesPath;
	public $serverUrl;
	public $debugMode = false;
	public $useEncryption = false;
	public $useAuthentication = false;
	public $timezone = 'UTC';
	public $collectionClass = 'flex.messaging.io.ArrayCollection';
	
	public $dbEngine;
	public $dbHost;
	public $dbSchema;
	public $dbUsername;
	public $dbPassword;
	public $dbPort;
	
	private static $instance;
	
	private function __construct()
	{
		$this->init();
		$this->setPath('php-models');
		$this->setPath('php-services');
		$this->serverUrl = $this->config->options->{'server-url'};
		$this->debugMode = ($this->config->options->{'debug-mode'} == 'true');
		$this->useEncryption = ($this->config->options->{'use-encryption'} == 'true');
		$this->useAuthentication = ($this->config->options->{'use-authentication'} == 'true');
		$this->timezone = $this->config->options->timezone;
		$this->collectionClass = (string) $this->config->options->{'collection-class'};
		
		$this->dbEngine = $this->config->database->engine;
		$this->dbHost = $this->config->database->host;
		$this->dbSchema = $this->config->database->schema;
		$this->dbUsername = $this->config->database->username;
		$this->dbPassword = $this->config->database->password;
		$this->dbPort = $this->config->database->port;
		
		//Still need to do <authentication>.  
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

		//Fix any Window type paths.
		foreach ($this->config->paths->children() as $key => $value)
		{
			$this->config->paths->$key = str_replace('\\','/',$value);
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
			die("<strong>Aerial Error: </strong>The <i>'$path'</i> path in config.xml (or config-alt.xml) does not exist.");
		
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
	
	public function toString()
	{
		$dom = new DOMDocument('1.0');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($this->config->asXML());
		return $dom->saveXML();
	}


}