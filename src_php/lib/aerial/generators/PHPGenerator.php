<?php
	require_once("GenerationController.php");

	class PHPGenerator extends GenerationController
	{
		public static function getPHPServiceData()
		{
			$relations = Configuration::getRelations();
			
			$data = array();
			foreach($relations as $model => $relationNames)
				$data[$model] = array("class" => $model, "relations" => $relationNames, "object" => strtolower($model));
			
			return $data;
		}
		
		public static function getPHPProperties($array)
		{
			$properties = array();
			foreach($array as $property)
				array_push($properties, "\t\tpublic $".$property["field"].";");//:".$property["type"].";");
			
			return implode("\n", $properties);
		}
		
		public static function generatePHPBaseService($class, $object, $relationsArr, $model, $inflectSingle, $inflectPlural, $directory)
		{
			$availRelations = "\t\t\t";
			$relations = array();
			
			foreach($relationsArr as $relation)
			{
				$availRelations .= "//\t\tAlias: ".$relation["alias"].", Type: ".$relation["type"]."\n\t\t\t";
				$relationInfo = array();

				foreach($relation as $key => $value)
					array_push($relationInfo, "\"$key\" => \"$value\"");
				
				array_push($relations, '"'.$relation['alias'].'" => array('.implode(",\n\t\t\t\t\t\t\t\t\t\t\t\t\t", $relationInfo).')');
			}
			
			$relations = implode(",\n\t\t\t\t\t\t\t\t", $relations);
			$replacementTokens = array("class", "object", "availRelations", "relations", "model", "inflectSingle", "inflectPlural");
			$contents = self::readTemplate("PHP.baseservice");
			
			foreach($replacementTokens as $token)
				$contents = str_replace("{{".$token."}}", $$token, $contents);
		
			self::writeFile("$directory/$class.php", $contents);
		}
		
		public static function generatePHPService($class, $directory)
		{
			$replacementTokens = array("class");
			$contents = self::readTemplate("PHP.service");
			
			foreach($replacementTokens as $token)
				$contents = str_replace("{{".$token."}}", $$token, $contents);
		
			self::writeFile("$directory/$class.php", $contents);
		}
	}
?>