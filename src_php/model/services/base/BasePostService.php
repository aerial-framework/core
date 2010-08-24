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

		public function savePost($post, $related=null)
		{
			if($related)
				foreach($related as $relation => $descriptor)
				{
					if($descriptor["type"] == "many")
					{
						for($x = 0; $x < count($descriptor["value"]); $x++)
						{
							$arr =& $post->$relation;
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
						?	$post->$descriptor["local_key"] = $descriptor["value"]["$foreign_key"]
						:	$post->$relation = $descriptor["value"];
					}
				}
				
			return $post->save();
		}
		
		public function updatePost($post_id, $fields)
		{
			$existing = $this->getPost($post_id);
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
		
		public function deletePost($post)
		{
			$existing = $this->getPost($post->id);
			if(!$existing)
				return;
				
			return $existing->delete();
		}
		
		public function getPost($post_id)
		{
			return $this->table->find($post_id);
		}
		
		public function getPostByField($field, $value, $paged=false, $limit=0, $offset=0)
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
		
		public function getPostByFields($fields, $values, $paged=false, $limit=0, $offset=0)
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
		public function getPostWithRelated($post_id)
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
													"refTable" => ""));
			$complex = new stdClass();
			
			$record = $this->table->find($post_id);
			foreach($record as $key => $value)
				$complex->$key = $value;
				
			foreach($relations as $relation)
				$complex->$relation["alias"] = $this->getRelated($relation["alias"], $post_id, $paged, $limit, $offset);
				
			return $complex;
		}
		
		public function getAllPostWithRelated($criteria = null)
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
		public function getRelated($field, $post_id, $paged=false, $limit=0, $offset=0)
		{
			//	available relations:
			//		Alias: User, Type: one
			//		Alias: Topic, Type: one
			//		Alias: comments, Type: many
			
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
		
		public function getAllPosts($paged=false, $limit=0, $offset=0)
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
		
		public function countPosts()
		{
			return $this->table->count();
		}
		
		public function countRelated($field, $post_id)
		{
			$related = $this->getRelated($field, $post_id);
			return get_class($related) != "Doctrine_Collection" ? 1 : $related->count();
		}
	}
?>