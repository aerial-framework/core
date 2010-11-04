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

		public function save($user)
		{
			$user = ModelMapper::mapToModel($this->modelName, $user, true);

			$result = $user->trySave();
			return ($result === true)
			?   $user->getIdentifier()
			:   $user->save();
		}

		public function insert($user)
		{
			$user = ModelMapper::mapToModel($this->modelName, $user);

			// unset the primary key values if one is set to insert a new record
			foreach($user->table->getIdentifierColumnNames() as $primaryKey)
				unset($user->$primaryKey);

			$result = $user->trySave();
			return ($result === true)
			?   $user->getIdentifier()
			:   $user->save();
		}

		public function drop($user)
		{
			$user = ModelMapper::mapToModel($this->modelName, $user, true);
			return $user->delete();
		}
		
		public function find($user_id)
		{
			return $this->table->find($user_id);
		}
		
		public function findByField($field, $value, $paged=false, $limit=0, $offset=0)
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
		
		public function findByFields($fields, $values, $paged=false, $limit=0, $offset=0)
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
		public function findWithRelated($user_id)
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
				$complex->$relation["alias"] = $this->findRelated($relation["alias"], $user_id, $paged, $limit, $offset);
				
			return $complex;
		}
		
		public function findAllWithRelated($criteria = null)
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
	
			return $q->execute()->toAmf(true);
			
		}
		
		// get related data for field
		public function findRelated($field, $user_id, $paged=false, $limit=0, $offset=0)
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
		
		public function findAll($paged=false, $limit=0, $offset=0)
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
		
		public function count()
		{
			return $this->table->count();
		}
		
		public function countRelated($field, $user_id)
		{
			$related = $this->findRelated($field, $user_id);
			return get_class($related) != "Doctrine_Collection" ? 1 : $related->count();
		}
	}
?>