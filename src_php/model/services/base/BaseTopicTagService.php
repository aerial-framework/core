<?php	
	class BaseTopicTagService
	{	
		protected $connection;
		protected $table;
		protected $modelName = "TopicTag";
		
		public function __construct()
		{
			$this->connection = Doctrine_Manager::connection();
			$this->table = $this->connection->getTable($this->modelName);
		}

		public function save($topictag)
		{
			$topictag = ModelMapper::mapToModel($this->modelName, $topictag, true);

			$result = $topictag->trySave();
			return ($result === true)
			?   $topictag->getIdentifier()
			:   $topictag->save();
		}

		public function insert($topictag)
		{
			$topictag = ModelMapper::mapToModel($this->modelName, $topictag);

			// unset the primary key values if one is set to insert a new record
			foreach($topictag->table->getIdentifierColumnNames() as $primaryKey)
				unset($topictag->$primaryKey);

			$result = $topictag->trySave();
			return ($result === true)
			?   $topictag->getIdentifier()
			:   $topictag->save();
		}

		public function drop($topictag)
		{
			$topictag = ModelMapper::mapToModel($this->modelName, $topictag, true);
			return $topictag->delete();
		}
		
		public function find($topictag_id)
		{
			return $this->table->find($topictag_id);
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
		public function findWithRelated($topictag_id)
		{
			$relations = array("Topic" => array("type" => "one",
													"alias" => "Topic",
													"table" => "Topic",
													"local_key" => "topicid",
													"foreign_key" => "id",
													"refTable" => ""),
								"Tag" => array("type" => "one",
													"alias" => "Tag",
													"table" => "Tag",
													"local_key" => "tagid",
													"foreign_key" => "id",
													"refTable" => ""));
			$complex = new stdClass();
			
			$record = $this->table->find($topictag_id);
			foreach($record as $key => $value)
				$complex->$key = $value;
				
			foreach($relations as $relation)
				$complex->$relation["alias"] = $this->findRelated($relation["alias"], $topictag_id, $paged, $limit, $offset);
				
			return $complex;
		}
		
		public function findAllWithRelated($criteria = null)
		{
			$relations = array("Topic" => array("type" => "one",
													"alias" => "Topic",
													"table" => "Topic",
													"local_key" => "topicid",
													"foreign_key" => "id",
													"refTable" => ""),
								"Tag" => array("type" => "one",
													"alias" => "Tag",
													"table" => "Tag",
													"local_key" => "tagid",
													"foreign_key" => "id",
													"refTable" => ""));
				
				
			$q = Doctrine_Query::create()->from('TopicTag y');
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
		public function findRelated($field, $topictag_id, $paged=false, $limit=0, $offset=0)
		{
			//	available relations:
			//		Alias: Topic, Type: one
			//		Alias: Tag, Type: one
			
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
							->where("y.{$rel->getLocalFieldName()} = $topictag_id")
							->andWhere("x.id = y.{$rel->getForeignFieldName()}");

					return $q->execute();
				}
				else	
					$q->where($rel->getForeignFieldName()." = $topictag_id");
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
					->where("y.".$rel->getForeign()." = $topictag_id");
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
			$q = Doctrine_Query::create()->select("*")->from("TopicTag");
					
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
		
		public function countRelated($field, $topictag_id)
		{
			$related = $this->findRelated($field, $topictag_id);
			return get_class($related) != "Doctrine_Collection" ? 1 : $related->count();
		}
	}
?>