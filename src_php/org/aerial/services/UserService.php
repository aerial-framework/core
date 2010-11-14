<?php
	require_once(conf("paths/aerial")."service/AbstractService.php");

	class UserService extends AbstractService
	{
		public $modelName = "User";
	}
?>