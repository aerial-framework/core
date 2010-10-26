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
			foreach($data as $key => $value)
			{
				if(is_undefined($value))
					continue;

				$found = false;
				foreach($relations as $relation)
					if($key == $relation->getAlias())
						$found = true;

				if(!$found)
					$instance->$key = $value;
			}

			foreach($relations as $relation)
			{
				$alias = $relation->getAlias();				
				if(!is_undefined($data[$alias]) && $data[$alias] != null)
				{
					if($relation->getType() == Doctrine_Relation::MANY)
					{
						$collection = new Doctrine_Collection($relation->getClass());

						foreach($data[$alias] as $element)
							$collection->add(self::mapToModel($relation->getClass(), $element, true));

						$instance[$alias] = $collection;
					}
					else
						$instance[$alias] = self::mapToModel($relation->getClass(), $data[$alias], true);
				}
			}

			return self::checkLookup($instance);
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