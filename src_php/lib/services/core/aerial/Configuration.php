<?php
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
	
		public function getConfigurationOptions()
		{
			$constants = get_defined_constants(true);
			return $constants['user'];
		}
	
		public function generate()
		{
			Doctrine_Core::dropDatabases();
			Doctrine_Core::createDatabases();

			$options = array(
				"baseClassName" => "Aerial_Record",
				"baseClassesDirectory" => "base"
			);

			$php_path = conf("code-generation/php");
			$package = conf("options/package", false);
			$php_path .= implode("/", explode(".", $package))."/";
			$models_path = $php_path.conf("options/models-folder");

			Doctrine_Core::generateModelsFromYaml(conf("paths/aerial").'schema.yml', $models_path, $options);
			Doctrine_Core::createTablesFromModels();
			//self::generateModelsAndServices();
		}

		public function getModels()
		{
			$models = Doctrine_Core::getLoadedModels();
			return $models;
		}

		/**
		 * Get details about each models' properties, type, etc
		 *
		 * @param  $models
		 * @return void
		 */
		public function getModelDefinitions($models=null)
		{
			if(!$models)
				$models = $this->getModels();

			if(count($models) == 0)
				return;

			$details = array();
			foreach($models as $model)
			{
				//$details[$model] = array("HELLO!");
				//return $details;

				$instance = new $model;
				$table = $instance->table;

				$details[$model] = array();
				foreach($table->getColumnNames() as $column)
				{
					$definition = $table->getColumnDefinition($column);					
					$as3Type = $this->getAS3Type($definition["type"], (bool) $definition["unsigned"]);

					$details[$model][] = array("name" => $table->getFieldName($column),
												//"type" => $definition["type"],
												"type" => $as3Type,
												"length" => $definition["length"]);
				}
				
				$relations = $table->getRelations();
				foreach($relations as $relation)
				{
					$details[$model][] = array("relation" => true,
												"name" => $relation->getAlias(),
												"type" => $relation->getClass(),
												"many" => (bool) $relation->getType());
				}

				$instance = null;
			}

			return $details;
		}

		public function getAS3Type($type, $unsigned)
		{
			$as3type = "";
			switch ($type)
			{
                case 'integer':
                	$as3type = $unsigned ? "uint" : "int";
                	break;
                case 'decimal':
                case 'float':
                	$as3type = "Number";
                	break;
                case 'set':
                case 'array':
                	$as3type = "Array";
                	break;
                case 'boolean':
                	$as3type = "Boolean";
                	break;
                case 'blob':
	                $as3type = "ByteArray";
			        break;
                case 'object':
                	$as3type = "Object";
                	break;
                case 'enum':
                case 'gzip':
                case 'string':
                case 'clob':
                case 'time':
                case 'timestamp':
                case 'date':
                default:
                	$as3type = "String";
                	break;
			}

			return $as3type;
		}
	}
?>