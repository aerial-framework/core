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
		
		public function find($object_id)
		{
			return $this->table->find($object_id);
		}

		public function count()
		{
			return $this->table->count();
		}
	}
?>