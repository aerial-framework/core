<?php
class Aerial_Relationship
{

	public static function columns($dirty_key)
	{
		$cols = preg_split('#\w+$|.+\(\s*|\s*,\s*|\s*\)\s*#', $dirty_key, -1, PREG_SPLIT_NO_EMPTY);

		return $cols;
	}

	public static function key($dirty_key)
	{
		preg_match('#\w+#', $dirty_key, $key);

		return $key[0];
	}

	public static function merge_dirty_keys($dirty_key1, $dirty_key2)
	{
		$merged_columns = array_unique(array_merge(self::columns($dirty_key1), self::columns($dirty_key2)));

		$key1 = self::key($dirty_key1);

		if(count($merged_columns) > 0) $key1 = $key1 . "(" . implode("," , $merged_columns) . ")";

		return  $key1;
	}

	public static function key_exists($dirty_key, $array)
	{
		foreach ($array as $dk=>$v)
		if(self::key($dk) == self::key($dirty_key)) return $dk;

		return false;
	}

	public static function to_array($string, $rootTable) //Not using this function anywhere rigth now.
	{
		$array = explode(".", $string);
		if($array[0] <> $rootTable) array_unshift($array, $rootTable);

		$tmp =& $p;
		foreach($array as $table)
		{
			$table = str_replace(' ','', $table); //White spaces need to be stripped out.
			$tmp[$table] = array(); //Could comment this out so it equals null but would have to change logic everywhere else.
			$tmp =& $tmp[$table];
		}

		return $p;
	}

	public static function merge( array &$array1,  $string )
	{
		$tree = $array1;
		@list($dirty_key, $value) = explode(".", $string, 2);

		$source_dirty_key = self::key_exists($dirty_key,$tree);

		if($source_dirty_key ) //$value && is_array($tree[$source_dirty_key])
		{
			$new_dirty_key = self::merge_dirty_keys($source_dirty_key, $dirty_key);
			if($new_dirty_key <> $source_dirty_key)
			{
				$tree[$new_dirty_key] = $tree[$source_dirty_key];
				unset($tree[$source_dirty_key]);
				$source_dirty_key = $new_dirty_key;
			}
			if($value)
			$tree [$source_dirty_key] = self::merge($tree[$source_dirty_key], $value);
		}
		else
		{
			$tree [$dirty_key] = array();
			if($value)
			$tree[$dirty_key] = self::merge($tree[$dirty_key], $value);
		}

		return $tree;
	}


	public static function relationParts($relations)
	{
		$pTable = self::key(key($relations));
		$dAlias = $relations;
		$joins = array();
		$selects = array();

		self::internal_relationParts($dAlias, $pTable, $joins, $selects);

		//Combine the SELECT array elements into a single string.
		$start = true;
		foreach($selects as $key=>$val)
		{
			//Iterate through the columns
			foreach($val as $col)
			{
				if($start){
					$selectStr = "$key.$col";
					$start = false;
				}else{
					$selectStr .= ", $key.$col";
				}
			}
		}

		return array("selects"=>$selectStr, "joins"=>$joins);
	}

	private static function internal_relationParts($dAlias, $pTable, &$leftJoins, &$selectedTables, $sAlias=null, &$i=0)
	{
		$isRoot = ($sAlias == null) ? true : false;
		
		$_table = Doctrine_Core::getTable($pTable);

		foreach($dAlias as $d => $dd)
		{
			if($isRoot){
				$sqlAlias = "r";
				$d_docTable = $_table;
			}else {
				$i++;
				$sqlAlias = "n" . $i;
				$rd = self::key($d);
				
				$d_name = $_table->getRelation($rd)->getClass(); //String
				$d_docTable = Doctrine_Core::getTable($d_name); //DoctrineTable
	
				//Build the leftJoin().
				$leftJoins[] = "$sAlias." . $rd . " $sqlAlias";
			}


			//Build the column selects.
			$columns = self::columns($d);
			$selectedTables[$sqlAlias] = array();

			if(count($columns) == 0)
			{
				$test  = $d_docTable->getIdentifier();
				$selectedTables[$sqlAlias][] = $d_docTable->getIdentifier(); //Primary Key
			}

			foreach($columns as $col)
			{
				$firstChar = substr($col, 0,1);
				$oper = ($firstChar == "-" ? "-" : "+");
				$colName = (($firstChar == "-") || ($firstChar ==  "+") ? substr($col, 1) : $col);

				if($oper == "+"){
					if($colName == "*"){
						$selectedTables[$sqlAlias] =  $d_docTable->getFieldNames();;
					}else{
						$selectedTables[$sqlAlias][] =  $colName;
					}
				}elseif($oper == "-"){
					if($colName == "*"){
						//May need to build some logic to handle user error here.
					}
					//Need to check if the column exists before removing.
					$found = array_search($colName, $selectedTables[$sqlAlias]);
					if($found) unset($selectedTables[$sqlAlias][$found]);
				}
			}

			//Recursion
			if(count($dd) > 0){
				if($isRoot){
					$d_name = $pTable;
				}
				self::internal_relationParts($dd, $d_name, $leftJoins, $selectedTables, $sqlAlias, $i);
			}

		}//Table Loop
	}


}
?>