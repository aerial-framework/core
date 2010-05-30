<?php
	require_once(AERIAL_INTERNAL.'/generators/ActionScriptGenerator.php');
	require_once(AERIAL_INTERNAL.'/generators/PHPGenerator.php');
	require_once(AERIAL_INTERNAL.'/generators/GenerationController.php');

	/**
	 * Modification of configuration values
	 *
	 * @author Danny Kopping
	 */
	class Configuration
	{
		private static $_instance;
	
		public function __construct()
		{
			if(isset(self::$_instance))
				trigger_error("You must not call the constructor directly! This class is a Singleton");	
		}
	
		public static function getInstance()
		{
			if(!isset(self::$_instance))
			{
				self::$_instance = new self();
				self::init();
			}
	
			return self::$_instance;
		}
	
		public static function getConfigurationOptions()
		{
			$constants = get_defined_constants(true);
			return $constants['user'];
		}
	
		public static function generate()
		{
			Doctrine_Core::dropDatabases();
			Doctrine_Core::createDatabases();
			Doctrine_Core::generateModelsFromYaml(AERIAL_BASE_PATH.'/schema.yml', BACKEND_MODELS_PATH);
			Doctrine_Core::createTablesFromModels();
			self::generateModelsAndServices();
		}
	
		public static function getTables()
		{
			$models = self::getModels();
			$tables = array();
	
			foreach($models as $model)
			{
				$instance = new $model();
				array_push($tables, array("tableName" => $instance->getTable()->getTableName(), "modelName" => $model));
			}
	
			return $tables;
		}
	
		public static function getColumns($table)
		{
			$connection = Doctrine_Manager::connection();
			$names = array();
	
			foreach($connection->getTable($table)->getColumns() as $name => $column)
			{
				$definition = $connection->getTable($table)->getColumnDefinition($name);
				$fieldName = $connection->getTable($table)->getFieldName($name);
				$unsigned = (bool) $definition['unsigned'];
	
				array_push($names, array("name" => $fieldName, "type" => $definition['type'], "unsigned" => $unsigned));
			}
	
			return $names;
		}
	
		public static function getRelations()
		{
			$connection = Doctrine_Manager::connection();
			$models = self::getModels();
			$relationships = array();
	
			foreach($models as $model)
			{
				$relations = $connection->getTable($model)->getRelations();
				$relationships[$model] = array();
				
				foreach($relations as $name => $relation)
				{
					$rel = $connection->getTable($model)->getRelation($name);
					$type = $rel->getType() == Doctrine_Relation::ONE ? "one" : "many";
					
					$refTable = null;
					if(get_class($rel) == Doctrine_Relation_Association)
						$refTable = $rel->getAssociationTable()->getClassnameToReturn();
					
					if($rel->getAlias() != $refTable)
					{
						array_push($relationships[$model], //$rel->toArray());
								array("type" => $type, "alias" => $rel->getAlias(), "table" => $rel->getClass(),
										"local_key" => $rel->getLocalColumnName(), "foreign_key" => $rel->getForeignFieldName(),
										"refTable" => $refTable));
					}
				}
			}
			
			return $relationships;
		}
		
		public static function generateModelsAndServices()
		{
			GenerationController::emptyFolder(FRONTEND_MODELS_PATH."/base");
			$modelData = ActionScriptGenerator::getModelData();
			
			// check if models have been generated previously
			$numExisting = GenerationController::getNumFiles(FRONTEND_MODELS_PATH);
			
			foreach($modelData as $model)
			{
				$class = $model["class"]."VO";
				$remoteClass = $model["class"];
				$properties = $model["properties"];
				$relations = $model["relations"];
				
				ActionScriptGenerator::generateAS3BaseModel(FRONTEND_MODELS_PACKAGE.".base", "Base$class",
												$properties, $relations, FRONTEND_MODELS_PATH."/base");
				
				if($numExisting != count($modelData))
					ActionScriptGenerator::generateAS3Model(FRONTEND_MODELS_PACKAGE, $class, $remoteClass, FRONTEND_MODELS_PATH);
			}

			GenerationController::emptyFolder(BACKEND_SERVICES_PATH."/base");
			$phpServicesData = PHPGenerator::getPHPServiceData();
			
			// check if models have been generated previously
			$numExisting = GenerationController::getNumFiles(BACKEND_SERVICES_PATH);
			
			foreach($phpServicesData as $service)
			{
				$class = "Base".$service["class"]."Service";
				$object = $service["object"];
				$relations = $service["relations"];
				$model = $service["class"];
				$inflectSingle = self::inflect("singular", $model);
				$inflectPlural = self::inflect("plural", $model);
				
				PHPGenerator::generatePHPBaseService($class, $object, $relations, $model, $inflectSingle, $inflectPlural, BACKEND_SERVICES_PATH."/base");
				
				if($numExisting != count($phpServicesData))
					PHPGenerator::generatePHPService("{$model}Service", BACKEND_SERVICES_PATH);
			}			
		
			GenerationController::emptyFolder(FRONTEND_SERVICES_PATH."/base");
			$asServicesData = ActionScriptGenerator::getASServiceData();
			
			$numExisting = GenerationController::getNumFiles(FRONTEND_SERVICES_PATH);
			
			foreach($asServicesData as $service)
			{
				$class = "Base".$service["class"]."Service";
				$model = $service["class"];
				$object = $service["object"];
				$relations = $service["relations"];
				$inflectSingle = self::inflect("singular", $model);
				$inflectPlural = self::inflect("plural", $model);
				
				ActionScriptGenerator::generateASBaseService(FRONTEND_MODELS_PACKAGE, FRONTEND_SERVICES_PACKAGE, $class, $object, $relations,
												$inflectSingle, $inflectPlural, $model."VO", $model, FRONTEND_SERVICES_PATH."/base");
				if($numExisting != count($asServicesData))
					ActionScriptGenerator::generateASService(FRONTEND_SERVICES_PACKAGE, "{$model}Service", FRONTEND_SERVICES_PATH);
			}
		}
			
		
		
		public static function generateModelsAndServicesFromYaml()
		{
			Doctrine_Core::generateModelsFromYaml(AERIAL_BASE_PATH.'/schema.yml', BACKEND_MODELS_PATH);
			self::generateModelsAndServices();
		}
		
		
		private static function getModels()
		{
			$models = Doctrine_Core::loadModels(BACKEND_MODELS_PATH);
			foreach($models as $key => &$model)
				if(substr($model, 0, 4) == "Base")
					unset($models[$key]);
	
			return $models;
		}
		
		/**
		 * Taken from CodeIgniter
		 * @link http://www.codeignitor.com/user_guide/helpers/inflector_helper.html
		 */
		private static function inflect($form, $str)
		{
			switch($form)
			{
				case "singular":
					$str = ucfirst(trim($str));
					$end = substr($str, -3);
				
					if ($end == 'ies')
						$str = substr($str, 0, strlen($str)-3).'y';
					elseif ($end == 'ses')
						$str = substr($str, 0, strlen($str)-2);
					else
					{
						$end = substr($str, -1);
					
						if ($end == 's')
							$str = substr($str, 0, strlen($str)-1);
					}
				
					return $str;
					break;
				case "plural":
					$str = ucfirst(trim($str));
					$end = substr($str, -1);
			
					if ($end == 'y')
					{
						// Y preceded by vowel => regular plural
						$vowels = array('a', 'e', 'i', 'o', 'u');
						$str = in_array(substr($str, -2, 1), $vowels) ? $str.'s' : substr($str, 0, -1).'ies';
					}
					elseif ($end == 's')
					{
						if ($force == TRUE)
							$str .= 'es';
					}
					else
						$str .= 's';
			
					return $str;
					break;
			}
		}
	}
?>