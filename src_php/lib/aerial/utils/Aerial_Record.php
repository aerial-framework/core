<?php
	require_once(DOCTRINE_PATH."/Doctrine/Record.php");

	abstract class Aerial_Record extends Doctrine_Record
	{
		protected function _set($fieldName, $value, $load = true)
		{
			if($this->isRelation($fieldName))
			{
				/*echo "$fieldName is a relation on ".$this->getTable()->getTableName()." > ";
				echo (gettype($value) == "object") ? get_class($value) : gettype($value)."\n";*/

				$value = $this->arrayToCollection($fieldName, $value);
			}

			return parent::_set($fieldName, $value, $load);
		}

		/**
		 * @param  $fieldName
		 * @param  $array
		 * @return Doctrine_Collection|array
		 *
		 * Returns a Doctrine_Collection instance if the field key is a relation alias, otherwise return the array
		 */
		protected function arrayToCollection($fieldName, $array)
		{
			$relationKey = $fieldName;
			$table = $this->getTable();
			
			$relation = $table->getRelation($relationKey);

			if($relation->isOneToOne())
			{
				if(!$array || is_undefined($array))
					return Doctrine_Record::initNullObject(new Doctrine_Null());

				return $array;
			}
			else
			{
				$coll = new Doctrine_Collection($relation->getTable());
				if(!$array || is_undefined($array))
					return $coll;

				foreach($array as $post)
					$coll->add($post);

				return $coll;
			}
		}

		protected function isRelation($alias)
		{
			return $this->getTable()->hasRelation($alias);
		}

		public function fromArray(array $array, $deep = true)
		{
			$refresh = false;
			foreach ($array as $key => $value) {
				if(is_undefined($value))
					continue;

				if ($key == '_identifier') {
					$refresh = true;
					$this->assignIdentifier($value);
					continue;
				}

				if ($deep && $this->getTable()->hasRelation($key)) {
					if ( ! $this->$key) {
						$this->refreshRelated($key);
					}

					if($value instanceof Aerial_Record)
					{
						$this->$key = $value;
						if(!is_undefined($value->id))
						{
							$refresh = true;
							$value->assignIdentifier($value->id);
						}
					}
					else if (is_array($value)) {
						if($value[0] instanceof Aerial_Record)
							$this->$key = $value;
						else if (isset($value[0]) && ! is_array($value[0])) {
							$this->unlink($key, array(), false);
							$this->link($key, $value, false);
						} else {
							$this->$key = $this->fromArray($value, $deep);
						}
					}
				} else if ($this->getTable()->hasField($key) || array_key_exists($key, $this->_values)) {
					$this->set($key, $value);
				} else {
					$method = 'set' . Doctrine_Inflector::classify($key);

					try {
						if (is_callable(array($this, $method))) {
							$this->$method($value);
						}
					} catch (Doctrine_Record_Exception $e) {}
				}
			}

			if ($refresh) {
				$this->refresh();
			}
		}
	}
?>