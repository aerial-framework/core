<?php	
	class BasePostService
	{	
		protected $connection;
		protected $table;
		protected $modelName = "Post";
		
		public function __construct()
		{
			$this->connection = Doctrine_Manager::connection();
			$this->table = $this->connection->getTable($this->modelName);
		}

		public function save($post)
		{
			$post = ModelMapper::mapToModel($this->modelName, $post, true);

			$result = $post->trySave();
			return ($result === true)
			?   $post->getIdentifier()
			:   $post->save();
		}

		public function insert($post)
		{
			$post = ModelMapper::mapToModel($this->modelName, $post);

			// unset the primary key values if one is set to insert a new record
			foreach($post->table->getIdentifierColumnNames() as $primaryKey)
				unset($post->$primaryKey);

			$result = $post->trySave();
			return ($result === true)
			?   $post->getIdentifier()
			:   $post->save();
		}

		public function drop($post)
		{
			$post = ModelMapper::mapToModel($this->modelName, $post, true);
			return $post->delete();
		}
		
		public function find($post_id)
		{
			return $this->table->find($post_id);
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
		public function findWithRelated($post_id)
		{
			$relations = array("User" => array("type" => "one",
													"alias" => "User",
													"table" => "User",
													"local_key" => "userid",
													"foreign_key" => "id",
													"refTable" => ""),
								"Topic" => array("type" => "one",
													"alias" => "Topic",
													"table" => "Topic",
													"local_key" => "topicid",
													"foreign_key" => "id",
													"refTable" => ""),
								"comments" => array("type" => "many",
													"alias" => "comments",
													"table" => "Comment",
													"local_key" => "id",
													"foreign_key" => "postId",
													"refTable" => ""),
								"postTags" => array("type" => "many",
													"alias" => "postTags",
													"table" => "PostTag",
													"local_key" => "id",
													"foreign_key" => "postId",
													"refTable" => ""));
			$complex = new stdClass();
			
			$record = $this->table->find($post_id);
			foreach($record as $key => $value)
				$complex->$key = $value;
				
			foreach($relations as $relation)
				$complex->$relation["alias"] = $this->findRelated($relation["alias"], $post_id, $paged, $limit, $offset);
				
			return $complex;
		}
		
		public function findAllWithRelated($criteria = null)
		{
			$relations = array("User" => array("type" => "one",
													"alias" => "User",
													"table" => "User",
													"local_key" => "userid",
													"foreign_key" => "id",
													"refTable" => ""),
								"Topic" => array("type" => "one",
													"alias" => "Topic",
													"table" => "Topic",
													"local_key" => "topicid",
													"foreign_key" => "id",
													"refTable" => ""),
								"comments" => array("type" => "many",
													"alias" => "comments",
													"table" => "Comment",
													"local_key" => "id",
													"foreign_key" => "postId",
													"refTable" => ""),
								"postTags" => array("type" => "many",
													"alias" => "postTags",
													"table" => "PostTag",
													"local_key" => "id",
													"foreign_key" => "postId",
													"refTable" => ""));
				
				
			$q = Doctrine_Query::create()->from('Post y');
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
		public function findRelated($field, $post_id, $paged=false, $limit=0, $offset=0)
		{
			//	available relations:
			//		Alias: User, Type: one
			//		Alias: Topic, Type: one
			//		Alias: comments, Type: many
			//		Alias: postTags, Type: many
			
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
							->where("y.{$rel->getLocalFieldName()} = $post_id")
							->andWhere("x.id = y.{$rel->getForeignFieldName()}");

					return $q->execute();
				}
				else	
					$q->where($rel->getForeignFieldName()." = $post_id");
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
					->where("y.".$rel->getForeign()." = $post_id");
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
			$q = Doctrine_Query::create()->select("*")->from("Post");
					
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
		
		public function countRelated($field, $post_id)
		{
			$related = $this->findRelated($field, $post_id);
			return get_class($related) != "Doctrine_Collection" ? 1 : $related->count();
		}
	}
?>