package model.vo.base
{
	[Bindable]
	public class BaseCommentVO
	{
		public var id:int;
		public var userId:int;
		public var postId:int;
		public var message:String;
		public var createDate:String;
		public var modDate:String;
		
		private var related:Object = {};
			
		[Transient]
		public function get User():*
		{
			return related["User"];
		}
			
		public function set User(value:*):void
		{
			related["User"] = {table:"User", value:value, type:"one",
									local_key:"userid", foreign_key:"id", refTable:""};
		}
			
		[Transient]
		public function get Post():*
		{
			return related["Post"];
		}
			
		public function set Post(value:*):void
		{
			related["Post"] = {table:"Post", value:value, type:"one",
									local_key:"postid", foreign_key:"id", refTable:""};
		}
		
		public function getRelatedData():Object
		{
			return related;
		}
	}
}