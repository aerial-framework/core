<?php
	class RelationUtil
	{
		public static function appendRelations($object)
		{
			$p = new stdClass();
			
			foreach($object as $key => $val)
				$p->$key = $val;
			
			$table = $object->getTable();
			foreach($table->getRelations() as $key => $value)	
				$p->$key = $object->$key;
				
			$p->_explicitType = $object->_explicitType;
			
			return $p;
		}
	}
?>