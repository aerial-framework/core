<?php
	class ModelMapper
	{
		private static $lookup;

		public static function mapToModel($class, $data, $assignIdentifier=false)
		{
			if(is_undefined($data))
				return null;
			
			$instance = new $class();
			
			// Since we're instantiating a new model and then mapping the values, we need to clear out default column values.
			// Otherwise, Doctrine will assign the default values to "_oldValues" before we finish mapping.
			foreach($instance->_data as $field=>$value)
			{
				if(! $value instanceof  Doctrine_Null)
					$instance->$field = new Doctrine_Null();
			}

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

			$mustDelete = false;

			// now assign properties			
			if(is_array($data) || is_object($data))
				foreach($data as $key => $value)
				{
					if(is_undefined($value))
						continue;
					else
						if($value == null)
						{
							$instance->$key = NULL;
							continue;
						}
						
					if($instance->table->hasColumn($key) && $value == $instance->table->getDefaultValueOf($key))
						continue;

					if($key == "deleted")
					{
						$mustDelete = true;
						continue;
					}

					if($key == "aerialGUID")           // don't add the uid property to the new Doctrine model
					{
						$instance->setGUID($value);
						continue;
					}
	
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

			if($mustDelete)
			{
				$instance->delete();
			}
			else
			{
				if(is_array($relations) || is_object($relations))
					foreach($relations as $relation)
					{
						$alias = $relation->getAlias();

						if(!is_undefined($data[$alias]) && $data[$alias] != null)
						{
							$relClass = $relation->getClass();
							$parentRel = self::hasParentRelationOfType(new $relClass, $class);

							if($relation->getType() == Doctrine_Relation::MANY)
							{
								//echo "Many: $alias\n";
								$collection = new Doctrine_Collection($relation->getClass());

								//print_r($data[$alias]);
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

								$instance->$alias = $collection;
							}
							else
							{
								$childObj = self::mapToModel($relation->getClass(), $data[$alias], true);

								if($parentRel)
									$childObj->$parentRel = $instance;

								$instance->$alias = $childObj;
							}
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
				if($relation->getClass() == $class && $relation->getType() != Doctrine_Relation::MANY)
					return $relation->getAlias();
			}
			
			return false;
		}

		private static function checkLookup($object)
		{
			for($x = 0; $x < count(self::$lookup); $x++)
			{
				if($object->getGUID() == self::$lookup[$x]->getGUID())
					return self::$lookup[$x];
			}

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