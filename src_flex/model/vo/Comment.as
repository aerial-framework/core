package model.vo
{
	import models.base.BaseComment;
	
	[RemoteClass(alias="model.vo.Comment")]	
	[Bindable]
	public class Comment extends BaseComment
	{		
		public function Comment()
		{
		}
	}
}