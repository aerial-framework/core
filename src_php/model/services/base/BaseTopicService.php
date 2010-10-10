<?php
class BaseTopicService
{
	protected $connection;
	protected $table;
	protected $modelName = "Topic";

	public function __construct()
	{
		$this->connection = Doctrine_Manager::connection();
		$this->table = $this->connection->getTable($this->modelName);
	}

	public function findAll($criteria, $offset, $limit)
	{
		//$q = Doctrine_Query::create()->select("*")->from("Topic y");
		$q = Doctrine_Query::create()->from('Topic n');
		$selectTables = 'n.*'; //Parent nodes/depth will be of form p1, p2, p3.  Child nodes/depth will be of form c1, c2, c3
		
		//Set Table and Relations
		$q = $q->select($selectTables);

		//Set Criteria
		if($criteria <> null)
		{
			foreach($criteria as $key=>$value)
				$q = $q->where("n.$key =?", $value);
		}
		
		//Set Pagination
		if($limit) $q->limit($limit);
		if($offset) $q->offset($offset);

		
			
		//$q->setHydrationMode("amf_collection");
		return $q->execute();

	}

	public function insert($args)
	{
		//		$obj = new stdClass();
		//		$obj->_explicitType = "model.vo.Topic";
		//		$obj->bleh = "name";

		$args = (object)$args;
		return array( $args);
	}

	// -----------------------------------------------------------------------------
	public function findAllWithRelated($criteria = null)
	{
		$relations = array("Category" => array("type" => "one",
													"alias" => "Category",
													"table" => "Category",
													"local_key" => "categoryid",
													"foreign_key" => "id",
													"refTable" => ""),
								"User" => array("type" => "one",
													"alias" => "User",
													"table" => "User",
													"local_key" => "userid",
													"foreign_key" => "id",
													"refTable" => ""),
								"posts" => array("type" => "many",
													"alias" => "posts",
													"table" => "Post",
													"local_key" => "id",
													"foreign_key" => "topicId",
													"refTable" => ""));


		$q = Doctrine_Query::create()->from('Topic y');
		$selectTables = 'y.*';
			
		foreach($relations as $relation)
		{
			$i++;
			if($relation["type"] == "many" ||  $relation["type"] == "one")
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


	public function save($topic, $related=null)
	{
		if($related)
		foreach($related as $relation => $descriptor)
		{
			if($descriptor["type"] == "many")
			{
				for($x = 0; $x < count($descriptor["value"]); $x++)
				{
					$arr =& $topic->$relation;
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
				?	$topic->$descriptor["local_key"] = $descriptor["value"]["$foreign_key"]
				:	$topic->$relation = $descriptor["value"];
			}
		}

		$result = $topic->trySave();
		return ($result === true)
		?   $topic->id
		:   $topic->save();
	}

	public function update($topic_id, $fields)
	{
		$existing = $this->find($topic_id);
		if(!$existing)
		return;

		foreach($fields as $key => $val)
		{
			if($existing->$key != $val)
			{
				if($val != $existing->$key && $val == null)
				continue;

				$existing->$key = $val;
			}
		}

		$result = $existing->trySave();
		return ($result === true)
		?   $existing
		:   $existing->save();
	}

	public function drop($topic)
	{
		$existing = $this->find($topic->id);
		if(!$existing)
		return;

		$oldID = $existing->id;
		if($existing->delete())
		return $oldID;
	}

	public function find($topic_id)
	{
		return $this->table->find($topic_id);
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
	public function findWithRelated($topic_id)
	{
		$relations = array("Category" => array("type" => "one",
													"alias" => "Category",
													"table" => "Category",
													"local_key" => "categoryid",
													"foreign_key" => "id",
													"refTable" => ""),
								"User" => array("type" => "one",
													"alias" => "User",
													"table" => "User",
													"local_key" => "userid",
													"foreign_key" => "id",
													"refTable" => ""),
								"posts" => array("type" => "many",
													"alias" => "posts",
													"table" => "Post",
													"local_key" => "id",
													"foreign_key" => "topicId",
													"refTable" => ""));
		$complex = new stdClass();
			
		$record = $this->table->find($topic_id);
		foreach($record as $key => $value)
		$complex->$key = $value;

		foreach($relations as $relation)
		$complex->$relation["alias"] = $this->getRelated($relation["alias"], $topic_id, $paged, $limit, $offset);

		return $complex;
	}



	// get related data for field
	public function findRelated($field, $topic_id, $paged=false, $limit=0, $offset=0)
	{
		//	available relations:
		//		Alias: Category, Type: one
		//		Alias: User, Type: one
		//		Alias: posts, Type: many
			
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
				->where("y.{$rel->getLocalFieldName()} = $topic_id")
				->andWhere("x.id = y.{$rel->getForeignFieldName()}");

				return $q->execute();
			}
			else
			$q->where($rel->getForeignFieldName()." = $topic_id");
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
			->where("y.".$rel->getForeign()." = $topic_id");
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



	public function count()
	{
		return $this->table->count();
	}

	public function countRelated($field, $topic_id)
	{
		$related = $this->getRelated($field, $topic_id);
		return get_class($related) != "Doctrine_Collection" ? 1 : $related->count();
	}
}
?>