<?php
	require_once(DOCTRINE_PATH."/Doctrine/Record.php");

	abstract class Aerial_Record extends Doctrine_Record
	{
		protected function _set($fieldName, $value, $load = true)
		{
			if(is_array($value))
				$value = new Doctrine_Collection("Topic");

			NetDebug::trace("Set $fieldName to $value");
			return parent::_set($fieldName, $value, $load);
		}
	}
?>