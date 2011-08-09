<?php
	include_once(AMFPHP_BASE."/shared/util/MethodTable.php");

	/**
	 * A built-in amfphp service that allows introspection into services and their methods.
	 * Remove from production servers
	 */
	class DiscoveryService
	{
		private $services_path;
		private $internal_services_path;

		public function __construct()
		{
			$this->internal_services_path = LIB_PATH;
			$this->services_path = ConfigXml::getInstance()->servicesPath;
		}

		/**
		 * Get the list of services
		 * @returns An array of array ready to be bound to a Tree
		 */
		function getServices()
		{
			$paths = array("Aerial Services" => $this->internal_services_path,
							"User-defined Services" => $this->services_path);
			$temp = array();
	
			// build deep array of services
			foreach($paths as $label => $path)
			{
				$temp[$label] = array();

                try
                {
                    $r = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
                }
                catch(Exception $e)
                {
                    continue;
                }
					
				foreach($r as $name => $file)
				{
					//echo $file.":".$path."\n";
					$folder = substr($name, strlen($path) + 1);
					if(strpos($folder, ".svn") !== false)
						continue;
						
					$subfolders = array();
	
					if(strpos($folder, DIRECTORY_SEPARATOR) !== false)
					{
						$subfolders = explode(DIRECTORY_SEPARATOR, $folder);
						if(count($subfolders) > 1)
							array_pop($subfolders);
					}
	
					$container =& $temp[$label];
					foreach($subfolders as $subfolder)
					{
						if(!is_array($container[$subfolder]))
							$container[$subfolder] = array();
	
						$container =& $container[$subfolder];
					}
						
					$folders = "";
					if(count($subfolders) > 0)
						$folders = join(DIRECTORY_SEPARATOR, $subfolders).DIRECTORY_SEPARATOR;
						
					$file = $path.DIRECTORY_SEPARATOR.$folders.$file->getFilename();
					array_push($container, substr($file, 0, strrpos($file, ".php")));
				}
			}

			// format array
			
			$services = array();
			foreach($temp as $key => $folder)
				array_push($services, array("label" => $key, "children" => $this->toTreeCompatible($folder, $key), "open" => true));
			
			return $services;
		}
		
		private function toTreeCompatible($arr, $folder)
		{
			$target = array();
			foreach($arr as $key => &$value)
			{
				if(is_array($value))
					array_push($target, array("label" => $key, "children" => $this->toTreeCompatible($value, $key), "open" => true));
				else
				{
					$absolute = substr($value, 0, strrpos($value, DIRECTORY_SEPARATOR) + 1);
					$file = substr($value, strlen($absolute));
					
					if(substr($absolute, 0, strlen($this->internal_services_path)) == $this->internal_services_path)
					{
						$sub = substr($absolute, 0, strlen($this->internal_services_path));
						$base = substr($absolute, strlen($sub) + 1);
					}
					else if(substr($absolute, 0, strlen($this->services_path)) == $this->services_path)
					{
						$sub = substr($absolute, 0, strlen($this->services_path));  
						$base = substr($absolute, strlen($sub) + 1);
					}
					
					if(!$base)
						$base = "";

					//echo "---------$base------------\n";
					
					//substr($value, 0, strrpos($value, "."))
					array_push($target, array("label" => $file, "data" => $base, "folder" => $absolute));
				}
			}
			
			return $target;
		}

		/**
		 * Describe a service and all its methods
		 * @param $data An object containing 'label' and 'data' keys
		 */
		function describeService($data)
		{
			$className = $data['label'];
			$folder = $data['folder'];
			
			$path = $folder.$className.".php";
//			if(realpath($this->internal_services_path."/".$folder.$className.".php"))
//				$path = realpath($this->internal_services_path."/".$folder.$className.".php");
//				
//			if(realpath($this->services_path."/".$folder.$className.".php"))
//				$path = realpath($this->services_path."/".$folder.$className.".php");

			//die($folder.$className);
			$methodTable = MethodTable::create($path, NULL, $classComment);
			return array($methodTable, $classComment);
		}

		function _listServices($dir = "", $suffix = "", $label = "")
		{
			if($dir == "")
			{
				$dir = $this->_path;
			}
			$services = array();
			
			if(in_array($suffix, $this->_omit))
			{
				return;
			}
			if ($handle = opendir($dir . $suffix))
			{
				while (false !== ($file = readdir($handle)))
				{
					chdir(dirname(__FILE__));
					if ($file != "." && $file != "..")
					{
						if(is_file($dir . $suffix . $file))
						{
							if(strpos($file, '.methodTable') !== FALSE)
							{
								continue;
							}
							$index = strrpos($file, '.');
							$before = substr($file, 0, $index);
							$after = substr($file, $index + 1);

							if($after == 'php')
							{
								$loc = "zzz_default";
								if($suffix != "")
								{
									$loc = str_replace(DIRECTORY_SEPARATOR,'.', substr($suffix, 0, -1));
								}

								if($services[$loc] == NULL)
								{
									$services[$loc] = array();
								}
								$services[$loc][] = array($before, $suffix);
								//array_push($this->_classes, $before);
							}

						}
						elseif(is_dir($dir . $suffix . $file))
						{
							$insideDir = $this->_listServices($dir, $suffix . $file . DIRECTORY_SEPARATOR, $label);
							if(is_array($insideDir))
							{
								$services = $services + $insideDir;
							}
						}
					}
				}
			}else
			{
				//echo("error");
			}
			closedir($handle);
			
			return $services;
		}

		function listTemplates()
		{
			$templates = array();
			if ($handle = opendir('templates'))
			{
				while (false !== ($file = readdir($handle)))
				{
					//chdir(dirname(__FILE__));
					if ($file != "." && $file != "..")
					{
						if(is_file('./templates/' . $file))
						{
							$index = strrpos($file, '.');
							$before = substr($file, 0, $index);
							$after = substr($file, $index + 1);

							if($after == 'php')
							{
								$templates[] = $before;
							}
						}
					}
				}
			}
			else
			{
				trigger_error("Could not open templates dir");
			}
			return $templates;
		}
	}