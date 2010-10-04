package model.vo.base
{
	import model.vo.TopicVO;
	import model.vo.UserVO;
	
	import mx.collections.ArrayCollection;

	[Bindable]
	public class BasePostVO
	{
		public var id:int;
		public var userId:int;
		public var topicId:int;
		public var title:String;
		public var message:String;
		public var createDate:String;
		public var modDate:String;
		
		public var User:UserVO;
		public var Topic:TopicVO;
		public var comments:ArrayCollection;
			
	}
}