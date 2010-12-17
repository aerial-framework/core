<?php
	class ModelMapper
	{
		private static $lookup;

		public static function mapToModel($class, $data, $assignIdentifier=false)
		{
			$instance = new $class();
			$primaryKeys = $instance->table->getIdentifierColumnNames();
			
			if($assignIdentifier)
			{
				// assign identifiers first, then properties
				
				if(is_array($data) || is_object($data))
					foreach($data as $key => $value)
					{
						if(is_undefined($value) || !self::isPrimary($key, $primaryKeys))
							continue;
	
						unset($data[$key]);
						$instance->assignIdentifier($value);
					}				
			}

			$relations = $instance->table->getRelations();
			
			// now assign properties			
			if(is_array($data) || is_object($data))
				foreach($data as $key => $value)
				{
					if(is_undefined($value))
						continue;
						
					if($instance->table->hasColumn($key) && $value == $instance->table->getDefaultValueOf($key))
						continue;
	
					$found = false;
					foreach($relations as $relation)
						if($key == $relation->getAlias())
							$found = true;
	
					if(!$found)				// is a normal property, not a relation
					{
						$columnName = $instance->getTable()->getColumnName($key);
						$definition = $instance->getTable()->getColumnDefinition($columnName);
						
						// pre-process special types if needed
						switch($definition["type"])
						{
							case "blob":
								if($value instanceof ByteArray)
									$value = $value->data;
								break;
						}
						
						$instance->$key = $value;
					}					
				}

			if(is_array($relations) || is_object($relations))
				foreach($relations as $relation)
				{
					$alias = $relation->getAlias();	
					/*echo $alias.">>".$relation->getClass()." [$class]\n";
					
					if(!is_undefined($data[$alias]) && is_object($data[$alias]))
					{
						echo "\n-------------------------\n";
						var_dump($data[$alias]->toArray());
						echo "\n-------------------------\n";
					}*/
					
					if(!is_undefined($data[$alias]) && $data[$alias] != null)
					{
						if($relation->getType() == Doctrine_Relation::MANY)
						{
							$collection = new Doctrine_Collection($relation->getClass());
							
							$relClass = $relation->getClass();
							$parentRel = self::hasParentRelationOfType(new $relClass, $class);

							if($data[$alias] instanceof Aerial_ArrayCollection)
							{
								foreach($data[$alias]->source as $element)
								{									
									$childObj = self::mapToModel($relation->getClass(), $element, true);
									if($parentRel)
										$childObj->$parentRel = $instance;
										
									$collection->add($childObj);
								}
							}
							else
							{
								foreach($data[$alias] as $element)
								{									
									$childObj = self::mapToModel($relation->getClass(), $element, true);
									if($parentRel)
										$childObj->$parentRel = $instance;
									
									$collection->add($childObj);
								}
							}
	
							$instance[$alias] = $collection;
						}
						else
						{
							$instance[$alias] = self::mapToModel($relation->getClass(), $data[$alias], true);
						}
					}
				}

			return self::checkLookup($instance);
		}
		
		private static function hasParentRelationOfType($instance, $class)
		{
			$relations = $instance->table->getRelations();
			if(count($relations) == 0)
				return false;
			
			foreach($relations as $name => $relation)
			{
				if($relation->getClass() == $class)
					return $name;
			}
		}

		private static function checkLookup($object)
		{
			for($x = 0; $x < count(self::$lookup); $x++)
				if($object->getData() == self::$lookup[$x]->getData())
					return self::$lookup[$x];

			self::$lookup[] = $object;
			return self::$lookup[count(self::$lookup) - 1];
		}

		private static function isPrimary($key, $keys)
		{
			foreach($keys as $value)
				if($value == $key)
					return true;

			return false;
		}
	}
?>