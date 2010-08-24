<?php
	require_once("GenerationController.php");

	class ActionScriptGenerator extends GenerationController
	{
		public static function getModelData()
		{
			$tables = Configuration::getTables();
			$data = array();
			$relations = Configuration::getRelations();
			
			foreach($tables as &$table)
			{
				$table['columns'] = Configuration::getColumns($table['modelName']);
	
				$modelsPackage = FRONTEND_MODELS_PACKAGE;
				$servicesPackage = FRONTEND_SERVICES_PACKAGE;
				
				$class = $table['modelName'];
				$properties = array();
	
				foreach($table['columns'] as $field)
				{
					$type = ActionScriptGenerator::getAS3Type($field['type'], $field['unsigned']);
					array_push($properties, array("field" => $field['name'], "type" => $type));
				}
					
				$data[$class] = array("class" => $class, "properties" => $properties, "relations" => $relations[$class]);
			}
			
			return $data;
		}

		public static function getASServiceData()
		{
			$relations = Configuration::getRelations();
			
			$data = array();
			foreach($relations as $model => $relationNames)
				$data[$model] = array("class" => $model, "relations" => $relationNames, "object" => strtolower($model));
			
			return $data;
		}
		
		public static function getASProperties($array)
		{
			$properties = array();
			foreach($array as $property)
				array_push($properties, "\t\tpublic var ".$property["field"].":".$property["type"].";");
			
			return implode("\n", $properties);
		}
		
		public static function generateASBaseService($modelPackage, $package, $class, $object, $relationsArr,
													$inflectSingle, $inflectPlural, $model, $modelService, $directory)
		{
			$availRelations = "\t\t\t";
			$relations = array();
			$inflectSingleUpper = strtoupper($inflectSingle);
			$inflectPluralUpper = strtoupper($inflectPlural);
			
			foreach($relationsArr as $relation)
			{
				$availRelations .= "//\t\tAlias: ".$relation["alias"].", Type: ".$relation["type"]."\n\t\t\t";
				$relationInfo = array();

				foreach($relation as $key => $value)
					array_push($relationInfo, "\"$key\" => \"$value\"");
				
				array_push($relations, '"'.$relation['alias'].'" => array('.implode(",\n\t\t\t\t\t\t\t\t\t\t\t\t\t", $relationInfo).')');
			}
			
			$relations = implode(",\n\t\t\t\t\t\t\t\t", $relations);
			$replacementTokens = array("modelPackage","package", "class", "object", "availRelations", "relations", "inflectSingle", "inflectSingleUpper",
											"inflectPlural", "inflectPluralUpper", "model", "modelService", "gatewayURL", "gatewayPackage");
			$contents = self::readTemplate("AS3.baseservice");
			
			
			$gateway = explode(",", AMFPHP_GATEWAY_URL);
			$gatewayPackage = $gateway[0];
			$gatewayURL = $gateway[1];
			
			foreach($replacementTokens as $token)
				$contents = str_replace("{{".$token."}}", $$token, $contents);
		
			self::writeFile("$directory/$class.as", $contents);
		}
		
		public static function generateASService($package, $class, $directory)
		{
			$replacementTokens = array("class", "package");
			$contents = self::readTemplate("AS3.service");
			
			foreach($replacementTokens as $token)
				$contents = str_replace("{{".$token."}}", $$token, $contents);
		
			self::writeFile("$directory/$class.as", $contents);
		}
		
		public static function generateAS3BaseModel($package, $class, $properties, $relations, $directory)
		{
			$replacementTokens = array("package", "class", "properties", "gettersAndSetters");
			$contents = self::readTemplate("AS3.basemodel");
			$properties = self::getASProperties($properties);
			
			$setterStub = self::getTemplatePart("as3SetterStub");
			$getterStub = self::getTemplatePart("as3GetterStub");
			
			$getters = "";
			$setters = "";
			$gettersAndSetters = "";
			
			foreach($relations as $relation)
			{
				$alias = $relation["alias"];
				$g_stub = "\n\t\t\t".$getterStub;
				$s_stub = "\n\t\t\t".$setterStub;
				
				$stub = "";
				
				foreach($relation as $key => $value)
				{
					$g_stub = str_replace("{{".$key."}}", $value, $g_stub);
					$s_stub = str_replace("{{".$key."}}", $value, $s_stub);
					
					$stub = $g_stub.$s_stub;
				}
				
				$gettersAndSetters .= $stub;
			}
			
			foreach($replacementTokens as $token)
				$contents = str_replace("{{".$token."}}", $$token, $contents);
		
			self::writeFile("$directory/$class.as", $contents);
		}
		
		public static function generateAS3Model($package, $class, $remoteClass, $directory)
		{
			$replacementTokens = array("package", "class", "remoteClass");
			$contents = self::readTemplate("AS3.model");
			
			foreach($replacementTokens as $token)
				$contents = str_replace("{{".$token."}}", $$token, $contents);
				
			self::writeFile("$directory/$class.as", $contents);
		}
		
		public static function getAS3Type($type, $unsigned)
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
                case 'object':
                	$as3type = "Object";
                	break;
                case 'enum':
                case 'gzip':
                case 'string':
                case 'blob':
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