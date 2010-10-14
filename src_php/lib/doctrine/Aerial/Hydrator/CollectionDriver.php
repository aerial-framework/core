<?php

class Aerial_Hydrator_CollectionDriver  extends Doctrine_Hydrator_Graph
{
	public function getElementCollection($component)
	{
		return new Aerial_ArrayCollection();
	}

	public function getElement(array $data, $component)
	{
		$component = $this->_getClassNameToReturn($data, $component);
		$this->_tables[$component]->setData($data);
		$record = $this->_tables[$component]->getRecord();
		$type = $record->get("_explicitType");
		
		$amfRecord = new Aerial_AmfRecord($type);
		
		foreach($data as $key=>$value)
			$amfRecord->$key = $value;

		return $amfRecord;
	}


	public function registerCollection($coll)
	{
	}

	public function initRelated(&$record, $name)
	{
		if ( ! isset($record[$name])) {
			$record[$name] = new Aerial_ArrayCollection();
		}
		
		return true;
	}

	public function getNullPointer()
	{
		return null;
	}

	public function getLastKey(&$coll)
	{
		$coll->getLast();
		return $coll->key();
	}

	public function setLastElement(&$prev, &$coll, $index, $dqlAlias, $oneToOne) //$coll is the return $result.
	{
		if ($coll === null) {
			unset($prev[$dqlAlias]); 
			return;
		}

		if ($index !== false) {
			$prev[$dqlAlias] = $coll[$index];
			return;
		}

		if (count($coll) > 0) {
			$prev[$dqlAlias] =  $coll->getLast(); //AmfRecord has getLast() implementation to account for 1-1 relationships. 
		}
	}
	
}
