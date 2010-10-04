package model.vo.base
{
	import model.vo.UserVO;
	
	import mx.collections.ArrayCollection;

	[Bindable]
	public class BaseCategoryVO
	{
		public var id:int;
		public var userId:int;
		public var name:String;
		public var createDate:String;
		public var modDate:String;
		
		public var User:UserVO;
		public var topics:ArrayCollection;
		
	}
}