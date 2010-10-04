package model.vo.base
{
	import model.vo.CategoryVO;
	import model.vo.UserVO;
	
	import mx.collections.ArrayCollection;

	[Bindable]
	public class BaseTopicVO
	{
		public var id:int;
		public var userId:int;
		public var categoryId:int;
		public var name:String;
		public var description:String;
		public var createDate:String;
		public var modDate:String;
		//public var Category:CategoryVO;
		//public var User:UserVO;
		public var posts:Array;
		
	}
}