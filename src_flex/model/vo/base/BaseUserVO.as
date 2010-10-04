package model.vo.base
{
	import mx.collections.ArrayCollection;

	[Bindable]
	public class BaseUserVO
	{
		public var id:int;
		public var username:String;
		public var password:String;
		public var createDate:String;
		public var modDate:String;
		
		public var posts:ArrayCollection;
		public var comments:ArrayCollection;
		public var categories:ArrayCollection;
		public var topics:ArrayCollection;
		
	}
}