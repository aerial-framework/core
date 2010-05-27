package model.vo
{
	import model.vo.base.BaseComment;
	
	[RemoteClass(alias="model.vo.Comment")]	
	[Bindable]
	public class Comment extends BaseComment
	{		
		public function Comment()
		{
		}
	}
}