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
				
				// add relations
//				foreach($relations[$class] as $relation)
//				{
//					$alias = $relation["alias"];
//					
//					array_push($properties, array("field" => $alias, "type" => $relation["type"]));
//				}
					
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
			{
				$defaultValue = " = ".self::getAS3TypeDefault($property["type"]);
				if($property["field"] == "id")
					$defaultValue = "";
				
				array_push($properties, "\t\tpublic var ".$property["field"].":".$property["type"].$defaultValue.";");
			}
			
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
											"inflectPlural", "inflectPluralUpper", "model", "modelService", "gatewayURL");
			$contents = self::readTemplate("AS3.baseservice");
			
			$gatewayURL = AMFPHP_GATEWAY_URL;
			
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
		
		public static function generateAS3BaseModel($package, $class, $properties, $rels, $directory)
		{
			$replacementTokens = array("package", "class", "properties", "relations", "imports", "gettersAndSetters");
			$contents = self::readTemplate("AS3.basemodel");
			
			$getterStub = GenerationController::getTemplatePart("as3GetterStub");
			$setterStub = GenerationController::getTemplatePart("as3SetterStub");
			
			$gettersAndSetters = "";
			
			$properties = self::getASProperties($properties);
			
			$relations = "";
			$imports = "import mx.collections.ArrayCollection;\n\t";
			
			foreach($rels as $relation)
			{
				$alias = $relation["alias"];
				$type = $relation["table"]."VO";
				
				$getter = str_replace("{{alias}}", $alias, $getterStub);
				$getter = str_replace("{{type}}", $relation["type"] == "one" ? $type : "*", $getter);
				
				$setter = str_replace("{{alias}}", $alias, $setterStub);
				$setter = str_replace("{{type}}", "*", $setter);
				
				if($type == $alias)			// naming conflict
					continue;
				
				$relations .= "\t\tprivate var _".$relation["alias"].":*;\n";				
				$imports .= "import ".FRONTEND_MODELS_PACKAGE.".".$relation["table"]."VO".";\n\t";
				$gettersAndSetters .= $getter."\n".$setter."\n";
			}
			
			if($imports != null)
				$imports .= "\n\t";
			
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
                /*$as3type = $unsigned ? "uint" : "int";
                break;*/
                case 'integer':
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
                	$as3type = "String";
                	break;
                default:
                	$as3type = $type;
                	break;
			}
			
			return $as3type;
		}
		
		public static function getAS3TypeDefault($type)
		{
			$value = '""';
			switch ($type)
			{
            	case "Number":
                	$value = "NaN";
                	break;
            	case "Array":
                	$value = "null";
                	break;
            	case "Boolean":
                	$value = "false";
                	break;
            	case "Object":
                	$value = "null";
                	break;
            	case "String":
            		$value = '""';
            		break;
                default:
                	$value = "null";
                	break;
			}
			
			return $value;
		}
	}
?>