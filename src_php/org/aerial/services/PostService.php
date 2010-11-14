<?php
	require_once(conf("paths/aerial")."service/AbstractService.php");

	class PostService extends AbstractService
	{
		public $modelName = "Post";
		
		public function find($id)
		{
			$x = parent::find($id)->toArray();
			$x["explicitType"] = "org.aerial.vo.Post";
			return $x;
		}
	}
?>