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
	}
?>