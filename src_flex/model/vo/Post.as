package model.vo
{
	import model.vo.base.BasePost;
	
	[RemoteClass(alias="model.vo.Post")]	
	[Bindable]
	public class Post extends BasePost
	{		
		public function Post()
		{
		}
	}
}