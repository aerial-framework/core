package model.vo.base
{
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
		
		private var related:Object = {};
			
		[Transient]
		public function get Category():*
		{
			return related["Category"];
		}
			
		public function set Category(value:*):void
		{
			related["Category"] = {table:"Category", value:value, type:"one",
									local_key:"categoryid", foreign_key:"id", refTable:""};
		}
			
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
		public function get posts():*
		{
			return related["posts"];
		}
			
		public function set posts(value:*):void
		{
			related["posts"] = {table:"Post", value:value, type:"many",
									local_key:"id", foreign_key:"topicId", refTable:""};
		}
		
		public function getRelatedData():Object
		{
			return related;
		}
	}
}