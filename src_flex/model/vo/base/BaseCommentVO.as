package model.vo.base
{
	import model.vo.PostVO;
	import model.vo.UserVO;

	[Bindable]
	public class BaseCommentVO
	{
		public var id:int;
		public var userId:int;
		public var postId:int;
		public var message:String;
		public var createDate:String;
		public var modDate:String;
		
		public var User:UserVO;
		public var Post:PostVO;	
		
	}
}