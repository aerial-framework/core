<?php
	require_once(conf("paths/aerial")."service/AbstractService.php");

	class CommentService extends AbstractService
	{
		public $modelName = "Comment";
	}
?>