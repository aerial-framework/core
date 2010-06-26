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
		
		private function mergeDeep($object, $mergeProperties=true, $update=false)
		{
			if(is_null($object) || is_undefined($object))
				return null;
			
			$class = get_class($object);
			$new = new $class();
			if($mergeProperties)
				$new->merge($object, false);
			
			$relations = $object->getTable()->getRelations();
			foreach($relations as $key => $val)
			{
				if(is_undefined($object->$key) || is_null($object->$key))
					continue;
				
				if($val["type"] == Doctrine_Relation::MANY)
				{
					foreach($object->$key as $item)
					{
						$itemClass = get_class($item);
						$newItem = new $itemClass;
												
						$rel =& $new->$key;
						
						if(!is_nan($item->id))
						{
							$table = $val["table"];
							$test = $table->find($item->id);
							
							$newItem = $this->mergeDeep($item, false);
							
							(is_object($test))
							?	$newItem->assignIdentifier($item->id)
							:	trigger_error("Attempt to relate non-existent record in table '".$table->getTableName().
														"' with primary key of {$item->id}");
						}
						else
							$newItem = $this->mergeDeep($item);
						
						$rel[] = $newItem;
					}
				}
				else
					$new->$key = $this->mergeDeep($object->$key);
			}
			
			return $new;
		}
		
		public function getCompleteGraph($object)
		{
			$new = new stdClass();
			$new->_explicitType = $object->_explicitType;
			foreach($object as $key => $val)
				$new->$key = $val;
				
			$rels = $object->getTable()->getRelations();
			foreach($rels as $key => $val)
				$new->$key = $object->$key;
			
			return $new;
		}

		public function saveUser(User $user)
		{
			$new = $this->mergeDeep($user);

			// try save the object
			$result = $new->trySave();
			if($result === true)
			{
				return $this->getCompleteGraph($new);
			}
			else
				$new->save();			// this will throw an error if it cannot successfully execute the request
		}
		
		public function updateUser(User $user)
		{		
			$new = new User();
			$new->assignIdentifier($user->id);

			$t = $new->getTable();
			foreach($user as $key => $val)				// modify the regular fields
			{
				$definition = $t->getColumnDefinition($t->getColumnName($key));
				//$nullable = !((boolean) $definition["notnull"]);
				
				$changed = ($new->$key !== $user->$key);
				
				if($definition["primary"])				// don't screw with the primary key
					continue;
				
				if($changed)
				{
					$type = gettype($user->$key);
					switch($type)
					{
						// if datatype is string and the value is an empty string, ignore this since the default value in ActionScript is ""
						case "string":
							if($val != null)
								$new->$key = $val;
							break;
							
						// if datatype is null and the value is null, assume that the developer wants to null the value in the database
						case "null":
						case "NULL":
							$new->$key = null;
							break;
							
						// if datatype is double and the value is NAN, ignore this since the default value in ActionScript is NaN
						case "double":
							if(!is_nan($val))
								$new->$key = $val;
							break;
							
						default:
							$new->$key = $val;
							break;
					}
				}
			}
			
			//then the relations...
			$relations = $new->getTable()->getRelations();
			foreach($relations as $key => $val)
			{
				// if the related data is undefined, ignore it because the property was unset
				// if the related data is null, remove the relation
				// it the related data is an empty Doctrine_Collection or an empty array, set the value
				
				if(is_null($user->$key))
				{
					$new->$key = null;
				}
				else if(is_undefined($user->$key))
				{
					continue;
				}
				else if(gettype($user->$key) == "array" || get_class($user->$key) == "Doctrine_Collection")
				{
					if((gettype($user->$key) == "array" && count($user->$key) == 0) || 
								(get_class($user->$key) == "Doctrine_Collection" && $user->$key->count() == 0))
					{
						$coll = new Doctrine_Collection($val["class"]);
						
						foreach($new->$key as $item)
						{
							$foreign = $val["foreign"];
							$item->$foreign = null;
							
							$coll->add($item);
						}
						
						$new->$key = $coll;
					}
					
					if($val["type"] == Doctrine_Relation::MANY)
					{
						$coll = new Doctrine_Collection($val["class"]);
						
						foreach($new->$key as $item)
						{
							$foreign = $val["foreign"];
							$item->$foreign = null;
							
							$coll->add($item);
						}
						
						foreach($user->$key as $item)
						{
							$c = $val["class"];
							$typed = new $c;
							$typed->merge($item, false);
							if($item["id"] != 0 && !is_nan($item["id"]))
								$typed->assignIdentifier($item["id"]);
							else
							{
								$foreign = $val["foreign"];
								$typed->$foreign = $new->id;
							}
								
							$coll->add($typed);
						}
						
						$new->$key = $coll;
					}
				}
				else
				{
					$item = $user->$key;
					
					$c = $val["class"];
					$typed = new $c;
					$typed->merge($item, false);
					if($item["id"] != 0 && !is_nan($item["id"]))
						$typed->assignIdentifier($item["id"]);
						
					$new->$key = $typed;
				}
			}

			// try save the object
			$result = $new->trySave();
			if($result === true)
				return $new;
			else
				$new->save();			// this will throw an error if it cannot successfully execute the request
		}
		
		public function deleteUser($user)
		{
			$existing = $this->getUser($user->id);
			if(!$existing)
				return;
				
			return $existing->delete();
		}
		
		public function getUser($user_id, $deep=true)
		{
			$user = $this->table->find($user_id);
			if(!$deep)
				return $user;
			
			$toReturn = new stdClass();
			$toReturn->_explicitType = $user->_explicitType;

			foreach($user as $key => $val)
				$toReturn->$key = $val;
			
			$relations = $user->getTable()->getRelations();
			foreach($relations as $alias => $val)
				$toReturn->$alias = $user->$alias;
				
			return $toReturn;
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
			$relations = array("images" => array("type" => "many",
													"alias" => "images",
													"table" => "Image",
													"local_key" => "id",
													"foreign_key" => "user_id",
													"refTable" => ""),
								"comments" => array("type" => "many",
													"alias" => "comments",
													"table" => "Comment",
													"local_key" => "id",
													"foreign_key" => "user_id",
													"refTable" => ""));
			$complex = new stdClass();
			
			$record = $this->table->find($user_id);
			foreach($record as $key => $value)
				$complex->$key = $value;
				
			foreach($relations as $relation)
				$complex->$relation["alias"] = $this->getRelated($relation["alias"], $user_id, $paged, $limit, $offset);
				
			return $complex;
		}
		
		// get related data for field
		public function getRelated($field, $user_id, $paged=false, $limit=0, $offset=0)
		{
			//	available relations:
			//		Alias: images, Type: many
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