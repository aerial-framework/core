<?php
abstract class AbstractService
{
	protected $connection;
	protected $table;
	protected $modelName;

	public function __construct()
	{
		$this->connection = Doctrine_Manager::connection();

		// try connect when create so that any connection errors can be detected early
		if(!$this->connection->isConnected())
			$this->connection->connect();

		$this->table = $this->connection->getTable($this->modelName);
	}

	public function save($object, $returnCompleteObject = false)
	{
		$object = ModelMapper::mapToModel($this->modelName, $object, true);

		$result = $object->trySave();
		
		if($result === true)
		{
			return $returnCompleteObject ? $object : $object->getIdentifier();
		}
		else
		{
			$object->save();
		}
		
	}
	
	/**
	 * Alias of "save" function
	 * @param $object
	 */
	public function update($object)
	{
		return self::save($object);
	}

	public function insert($object, $returnCompleteObject = false)
	{
		$object = ModelMapper::mapToModel($this->modelName, $object);
		
		// unset the primary key values if one is set to insert a new record
		foreach($object->table->getIdentifierColumnNames() as $primaryKey)
			unset($object->$primaryKey);
			
		$result = $object->trySave();		
		
		if($result === true)
		{
			return $returnCompleteObject ? $object : $object->getIdentifier();
		}
		else
		{
			$object->save();
		}
		
	}

	public function drop($object)
	{
		$object = ModelMapper::mapToModel($this->modelName, $object, true);
		
		return $object->delete();
	}

	public function find($criteria, $limit, $offset, $sort, $relations, $preprocess)
	{
		$q = Doctrine_Query::create()->from("$this->modelName r");
		
		//============================  PreProcess =============================
		//Need to merge PreProcess branches as we can have two of the same processes effecting different columns in the same table. 
		//For now, this is a working proof of concept.
		foreach ($preprocess as $proc) 
		{
			list($processTable, $processColumn) = explode(".", $proc['field']);
			list($processClass, $processMethod) = explode(".", $proc['process']);
			$processClass = "Aerial_Hydrator_Preprocess_" . $processClass;
			$params = $proc['args'];
			
			$T = Doctrine_Core::getTable($processTable);
			$T->addRecordListener(new $processClass($processColumn, $processMethod, $params));
		}

		//========================  Selects / Joins ==========================
		if($relations)
		{
			//Merge the relations into a single tree; validate all paths start with the root table.
			$mergedRelations = array();
			foreach($relations as $path)
			{
				list($dirty_key) = explode(".", $path, 2);
				if(Aerial_Relationship::key($dirty_key) <> $this->modelName) $path = $this->modelName . "." . $path;
					$mergedRelations = Aerial_Relationship::merge($mergedRelations, $path);
			}

			//Build the DQL 'leftJoin' and 'Select' parts.
			$relationParts = Aerial_Relationship::relationParts($mergedRelations);
			foreach($relationParts["joins"] as $join)
				$q->leftJoin($join);

			$q->select($relationParts["selects"]);
		}

		//============================  Criteria =============================
		if($criteria)
		{
			foreach($criteria as $key=>$value)
				$q->addWhere("r.$key =?", $value);
		}

		if($relationParts && !$relationParts["criteria"])
			$relationParts["criteria"] = array();

		if($relationParts)
		{
			foreach($relationParts["criteria"] as $criteria)
				$q->addWhere($criteria);
		}
		
		
		//============================   Order  ===============================
		if($sort){
			foreach($sort as $key=>$value)
			{
				$q->addOrderBy("$key $value");
			}
		}

		//==========================  Pagination  ==============================
		if($limit) $q->limit($limit);
		if($offset) $q->offset($offset);

		$q->setHydrationMode(Aerial_Core::HYDRATE_AMF_COLLECTION);
		$results = $q->execute();
		
		return $results;

	}


	public function count()
	{
		return $this->table->count();
	}

    public function executeDQL($properties)
    {
        $q = Doctrine_Query::create();
        foreach($properties as $property)
        {
            $method = $property["key"];
            call_user_func_array(array($q, $method), $property["value"]);
        }

        return $q->execute();
    }
}
?>