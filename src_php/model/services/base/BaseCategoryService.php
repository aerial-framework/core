<?php	
	class BaseCategoryService
	{	
		protected $connection;
		protected $table;
		protected $modelName = "Category";
		
		public function __construct()
		{
			$this->connection = Doctrine_Manager::connection();
			$this->table = $this->connection->getTable($this->modelName);
		}

		public function saveCategory($category, $related=null)
		{
			if($related)
				foreach($related as $relation => $descriptor)
				{
					if($descriptor["type"] == "many")
					{
						for($x = 0; $x < count($descriptor["value"]); $x++)
						{
							$arr =& $category->$relation;
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
						?	$category->$descriptor["local_key"] = $descriptor["value"]["$foreign_key"]
						:	$category->$relation = $descriptor["value"];
					}
				}
				
			return $category->save();
		}
		
		public function updateCategory($category_id, $fields)
		{
			$existing = $this->getCategory($category_id);
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
		
		public function deleteCategory($category)
		{
			$existing = $this->getCategory($category->id);
			if(!$existing)
				return;
				
			return $existing->delete();
		}
		
		public function getCategory($category_id)
		{
			return $this->table->find($category_id);
		}
		
		public function getCategoryByField($field, $value, $paged=false, $limit=0, $offset=0)
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
		
		public function getCategoryByFields($fields, $values, $paged=false, $limit=0, $offset=0)
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
		public function getCategoryWithRelated($category_id)
		{
			$relations = array("User" => array("type" => "one",
													"alias" => "User",
													"table" => "User",
													"local_key" => "userid",
													"foreign_key" => "id",
													"refTable" => ""),
								"posts" => array("type" => "many",
													"alias" => "posts",
													"table" => "Post",
													"local_key" => "id",
													"foreign_key" => "categoryId",
													"refTable" => ""));
			$complex = new stdClass();
			
			$record = $this->table->find($category_id);
			foreach($record as $key => $value)
				$complex->$key = $value;
				
			foreach($relations as $relation)
				$complex->$relation["alias"] = $this->getRelated($relation["alias"], $category_id, $paged, $limit, $offset);
				
			return $complex;
		}
		
		// get related data for field
		public function getRelated($field, $category_id, $paged=false, $limit=0, $offset=0)
		{
			//	available relations:
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
							->where("y.{$rel->getLocalFieldName()} = $category_id")
							->andWhere("x.id = y.{$rel->getForeignFieldName()}");

					return $q->execute();
				}
				else	
					$q->where($rel->getForeignFieldName()." = $category_id");
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
					->where("y.".$rel->getForeign()." = $category_id");
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
		
		public function getAllCategories($paged=false, $limit=0, $offset=0)
		{
			$q = Doctrine_Query::create()->select("*")->from("Category");
					
			if($paged)
			{
				if($limit)
					$q->limit($limit);
				if($offset)
					$q->offset($offset);
			}
				
			return $q->execute();
		}
		
		public function countCategories()
		{
			return $this->table->count();
		}
		
		public function countRelated($field, $category_id)
		{
			$related = $this->getRelated($field, $category_id);
			return get_class($related) != "Doctrine_Collection" ? 1 : $related->count();
		}
	}
?>