<?php	
	class BaseUserService
	{	
		protected $connection;
		protected $table;
		protected $modelName = "User";
		
		public function __construct()
		{
			$this->connection = Doctrine_Manager::connection();
			$this->table = $this->connection->getTable($this->modelName);
		}

		public function saveUser($user, $related=null)
		{
			if($related)
				foreach($related as $relation => $descriptor)
				{
					if($descriptor["type"] == "many")
					{
						for($x = 0; $x < count($descriptor["value"]); $x++)
						{
							$arr =& $user->$relation;
							$arr[$x] = $descriptor["value"][$x];
						}
					}
					else
					{
						// check for existence of related item
						$testTable = $this->connection->getTable($descriptor["table"]);
						$foreign_key = $descriptor["foreign_key"];
						$test = $testTable->find($descriptor["value"]["$foreign_key"]);
						
						(is_object($test))
						?	$user->$descriptor["local_key"] = $descriptor["value"]["$foreign_key"]
						:	$user->$relation = $descriptor["value"];
					}
				}
				
			return $user->save();
		}
		
		public function updateUser($user_id, $fields)
		{
			$existing = $this->getUser($user_id);
			if(!$existing)
				return;
			
			foreach($fields as $key => $val)
			{
				if($existing->$key != $val)
				{
					if($val === null && $existing->$key !== null)
						continue;
						
					$existing->$key = $val;
				}
			}
				
			return $existing->save();
		}
		
		public function deleteUser($user)
		{
			$existing = $this->getUser($user->id);
			if(!$existing)
				return;
				
			return $existing->delete();
		}
		
		public function getUser($user_id)
		{
			return $this->table->find($user_id);
		}
		
		public function getUserByField($field, $value, $paged=false, $limit=0, $offset=0)
		{
			$q = Doctrine_Query::create()
					->select("x.*")
					->where("x.$field = '$value'")
					->from($this->modelName." x");
			
			if($paged)
			{
				if($limit)
					$q->limit($limit);
				if($offset)
					$q->offset($offset);
			}
					
			return $q->execute();
		}
		
		public function getUserByFields($fields, $values, $paged=false, $limit=0, $offset=0)
		{
			$q = Doctrine_Query::create()
					->select("x.*");
					
			if($fields[0] && $values[0])
				$q->where("x.".$fields[0]." = '".$values[0]."'");
			
			for($x = 1; $x < count($fields); $x++)
				if($values[$x] !== null)
					$q->andWhere("x.".$fields[$x]." = '".$values[$x]."'");
				
			$q->from($this->modelName." x");
				
			if($paged)
			{
				if($limit)
					$q->limit($limit);
				if($offset)
					$q->offset($offset);
			}
					
			return $q->execute();
		}
		
		// get all relations and find related data
		public function getUserWithRelated($user_id)
		{
			$relations = array("posts" => array("type" => "many",
													"alias" => "posts",
													"table" => "Post",
													"local_key" => "id",
													"foreign_key" => "userId",
													"refTable" => ""),
								"comments" => array("type" => "many",
													"alias" => "comments",
													"table" => "Comment",
													"local_key" => "id",
													"foreign_key" => "userId",
													"refTable" => ""),
								"categories" => array("type" => "many",
													"alias" => "categories",
													"table" => "Category",
													"local_key" => "id",
													"foreign_key" => "userId",
													"refTable" => ""),
								"topics" => array("type" => "many",
													"alias" => "topics",
													"table" => "Topic",
													"local_key" => "id",
													"foreign_key" => "userId",
													"refTable" => ""));
			$complex = new stdClass();
			
			$record = $this->table->find($user_id);
			foreach($record as $key => $value)
				$complex->$key = $value;
				
			foreach($relations as $relation)
				$complex->$relation["alias"] = $this->getRelated($relation["alias"], $user_id, $paged, $limit, $offset);
				
			return $complex;
		}
		
		public function getAllUserWithRelated($criteria = null)
		{
			$relations = array("posts" => array("type" => "many",
													"alias" => "posts",
													"table" => "Post",
													"local_key" => "id",
													"foreign_key" => "userId",
													"refTable" => ""),
								"comments" => array("type" => "many",
													"alias" => "comments",
													"table" => "Comment",
													"local_key" => "id",
													"foreign_key" => "userId",
													"refTable" => ""),
								"categories" => array("type" => "many",
													"alias" => "categories",
													"table" => "Category",
													"local_key" => "id",
													"foreign_key" => "userId",
													"refTable" => ""),
								"topics" => array("type" => "many",
													"alias" => "topics",
													"table" => "Topic",
													"local_key" => "id",
													"foreign_key" => "userId",
													"refTable" => ""));
				
				
			$q = Doctrine_Query::create()->from('User y');
			$selectTables = 'y.*';
			
			foreach($relations as $relation)
			{
				$i++;
				if($relation["type"] == "many" )
				{
					$selectTables .= ',z' . $i . '.*';
					$q = $q->leftJoin('y.' . $relation["alias"] . ' z' . $i);
				}
			}
	
			$q = $q->select($selectTables);
			
			if($criteria <> null)
			{
				foreach($criteria as $key=>$value)
				{
					$q = $q->where("y.$key =?", $value);
				}
			}
	
			if($paged)
			{
				if($limit)
				$q->limit($limit);
				if($offset)
				$q->offset($offset);
			}
	
			return $q->execute();
			
		}
		
		// get related data for field
		public function getRelated($field, $user_id, $paged=false, $limit=0, $offset=0)
		{
			//	available relations:
			//		Alias: posts, Type: many
			//		Alias: comments, Type: many
			//		Alias: categories, Type: many
			//		Alias: topics, Type: many
			
			$rel = $this->table->getRelation($field);
			
			$q = Doctrine_Query::create()
					->select("x.*")
					->from($rel->getClass()." x");
					
			if($rel->getType() == Doctrine_Relation::MANY)
			{
				if(get_class($rel) == Doctrine_Relation_Association)				// if relation is many-to-many
				{
					$foreignTable = $rel->getClass();
					$joining = $rel->getAssociationTable()->getClassnameToReturn();
					
					$q = Doctrine_Query::create()
							->select("x.*")
							->from("$foreignTable x, $joining y")
							->where("y.{$rel->getLocalFieldName()} = $user_id")
							->andWhere("x.id = y.{$rel->getForeignFieldName()}");

					return $q->execute();
				}
				else	
					$q->where($rel->getForeignFieldName()." = $user_id");
			}
			else
			{
				$foreignRelations = $this->connection->getTable($rel->getClass())->getRelations();
				$foreignRelation = null;
				
				foreach($foreignRelations as $key => $value)
				{
					if($value->getClass() == $this->modelName)
					{
						$foreignRelation = $value->getAlias();
						break;
					}
				}

				$q->leftJoin("x.$foreignRelation y")
					->where("y.".$rel->getForeign()." = $user_id");
			}
					
			if($paged)
			{
				if($limit)
					$q->limit($limit);
				if($offset)
					$q->offset($offset);
			}
				
			$result = $q->execute();
			return ($rel->getType() == Doctrine_Relation::ONE)
					?	$result[0]
					:	$result;
		}
		
		public function getAllUsers($paged=false, $limit=0, $offset=0)
		{
			$q = Doctrine_Query::create()->select("*")->from("User");
					
			if($paged)
			{
				if($limit)
					$q->limit($limit);
				if($offset)
					$q->offset($offset);
			}
				
			return $q->execute();
		}
		
		public function countUsers()
		{
			return $this->table->count();
		}
		
		public function countRelated($field, $user_id)
		{
			$related = $this->getRelated($field, $user_id);
			return get_class($related) != "Doctrine_Collection" ? 1 : $related->count();
		}
	}
?>