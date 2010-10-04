<?php
	require_once(DOCTRINE_PATH."/Doctrine/Record.php");

	abstract class Aerial_Record extends Doctrine_Record
	{
		protected function _set($fieldName, $value, $load = true)
		{
			if(is_array($value))
				$value = $this->arrayToCollection($fieldName, $value);

			return parent::_set($fieldName, $value, $load);
		}

		/**
		 * @param  $fieldName
		 * @param  $array
		 * @return Doctrine_Collection|array
		 *
		 * Returns a Doctrine_Collection instance if the field key is a relation alias, otherwise return the array
		 */
		private function arrayToCollection($fieldName, $array)
		{
			$relationKey = $fieldName;
			$table = $this->getTable();

			try
			{
				$relation = $table->getRelation($relationKey);
			}
			catch(Doctrine_Table_Exception $e)
			{
				return $array;
			}

			$coll = new Doctrine_Collection($relation->getTable());
			foreach($array as $post)
				$coll->add($post);

			return $coll;
		}
	}
?>