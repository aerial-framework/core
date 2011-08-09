<?php
//import('doctrine.Doctrine.Core');

class Aerial_Core extends Doctrine_Core
{
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

    /**
     * Intercept Doctrines
     *
     * @static
     * @param  $directory
     * @param array $databases
     * @param array $options
     * @return void
     */
    public static function generateModelsFromDb($directory, array $databases = array(), array $options = array())
    {
        $import = new Aerial_Import();
        return $import->importSchema($directory, $databases, $options);
    }

    public static function generateEmulatedModelsFromYaml($yamlPath, $directory, $options = array())
    {
        $import = new Aerial_Import_Schema();
        $import->setOptions($options);

        return $import->importEmulatedSchema($yamlPath, 'yml', $directory);
    }

    public static function generateModelsFromYaml($yamlPath, $directory, $options = array())
    {
        $import = new Aerial_Import_Schema();
        $import->setOptions($options);

        return $import->importSchema($yamlPath, 'yml', $directory);
    }

    /**
     * Recursively load all models from a directory or array of directories
     *
     * @param  string   $directory      Path to directory of models or array of directory paths
     * @param  integer  $modelLoading   Pass value of Doctrine_Core::ATTR_MODEL_LOADING to force a certain style of model loading
     *                                  Allowed Doctrine_Core::MODEL_LOADING_AGGRESSIVE(default) or Doctrine_Core::MODEL_LOADING_CONSERVATIVE
     * @param  string  $classPrefix     The class prefix of the models to load. This is useful if the class name and file name are not the same
     */
    public static function loadModels($directory, $modelLoading = null, $classPrefix = null)
    {
	    $permissions = substr(sprintf('%o', fileperms($directory)), -4);
	    if(!is_readable($directory))
	    {
		    AerialStartupManager::error("Cannot load <strong>Aerial models</strong> from an unreadable directory with
		                            permissions of <strong>$permissions</strong>. Please check your
		                            <strong>php-models</strong> directory in <i>config.xml</i>");
	    }

        $manager = Doctrine_Manager::getInstance();

        $modelLoading = $modelLoading === null ? $manager->getAttribute(Doctrine_Core::ATTR_MODEL_LOADING) : $modelLoading;
        $classPrefix = $classPrefix === null ? $manager->getAttribute(Doctrine_Core::ATTR_MODEL_CLASS_PREFIX) : $classPrefix;

        $loadedModels = array();

	    try
	    {
			if ($directory !== null) {
				foreach ((array) $directory as $dir) {
					$dir = rtrim($dir, '/');
					if ( ! is_dir($dir)) {
						throw new Doctrine_Exception('You must pass a valid path to a directory containing Doctrine models');
					}

					$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir),
															RecursiveIteratorIterator::LEAVES_ONLY);

					foreach ($it as $file) {
						$e = explode('.', $file->getFileName());

						if (end($e) === 'php' && strpos($file->getFileName(), '.inc') === false) {
							if ($modelLoading == Doctrine_Core::MODEL_LOADING_PEAR) {
								$className = str_replace($dir . DIRECTORY_SEPARATOR, null, $file->getPathName());
								$className = str_replace(DIRECTORY_SEPARATOR, '_', $className);
								$className = substr($className, 0, strpos($className, '.'));
							} else {
								$className = $e[0];
							}

							if ($classPrefix) {
								$className = $classPrefix . $className;
							}

							if ( ! class_exists($className, false)) {
								if ($modelLoading == Doctrine_Core::MODEL_LOADING_CONSERVATIVE || $modelLoading == Doctrine_Core::MODEL_LOADING_PEAR) {
									parent::loadModel($className, $file->getPathName());

									$loadedModels[$className] = $className;
								} else {
									$declaredBefore = get_declared_classes();
									require_once($file->getPathName());
									$declaredAfter = get_declared_classes();

									// Using array_slice because array_diff is broken is some PHP versions
									$foundClasses = array_slice($declaredAfter, count($declaredBefore));

									if ($foundClasses) {
										foreach ($foundClasses as $className) {
											if (parent::isValidModelClass($className)) {
												$loadedModels[$className] = $className;

												parent::loadModel($className, $file->getPathName());
											}
										}
									}

									$previouslyLoaded = array_keys(parent::getLoadedModelFiles(), $file->getPathName());

									if ( ! empty($previouslyLoaded)) {
										$previouslyLoaded = array_combine(array_values($previouslyLoaded), array_values($previouslyLoaded));
										$loadedModels = array_merge($loadedModels, $previouslyLoaded);
									}
								}
							} else if (parent::isValidModelClass($className)) {
								$loadedModels[$className] = $className;
							}
						}
					}
				}
			}
	    }
	    catch(Exception $e)
		{
			AerialStartupManager::error($e->getMessage()."<br/>In file: <strong>".$e->getFile()."</strong> on line ".$e->getLine());
		}

        asort($loadedModels);

        return $loadedModels;
    }
}