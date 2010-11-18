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

	public function save($object)
	{
		$object = ModelMapper::mapToModel($this->modelName, $object, true);

		$result = $object->trySave();
		return ($result === true)
		?   $object->getIdentifier()
		:   $object->save();
	}

	public function insert($object)
	{
		$object = ModelMapper::mapToModel($this->modelName, $object);

		// unset the primary key values if one is set to insert a new record
		foreach($object->table->getIdentifierColumnNames() as $primaryKey)
		unset($object->$primaryKey);

		$result = $object->trySave();
		return ($result === true)
		?   $object->getIdentifier()
		:   $object->save();
	}

	public function drop($object)
	{
		$object = ModelMapper::mapToModel($this->modelName, $object, true);
		return $object->delete();
	}

	public function find($criteria, $limit, $offset, $sort, $relations)
	{
		$q = Doctrine_Query::create()->from("$this->modelName r");

		//========================  Selects / Joins ==========================
		if($relations)
		{
			//Merge the relations into a single tree; validate all paths start with the root table.
			$mergedRelations = array();
			foreach($relations as $path)
			{
				$path = str_replace(' ','', $path); //Remove white space
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
		else
			$q->select("*");

		//============================   Order  ===============================
		if($sort){
			foreach($sort as $key=>$value)
			{
				$q->orderBy("$key $value");
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
}
?>