package model.vo.base
{
	
	[Bindable]
	public class BaseUser
	{
		public var id:int;
		public var username:String;
		public var password:String;
		public var createDate:String;
		public var modDate:String;
		
		private var related:Object = {};
			
		[Transient]
		public function get posts():*
		{
			return related["posts"];
		}
			
		public function set posts(value:*):void
		{
			related["posts"] = {table:"Post", value:value, type:"many",
									local_key:"id", foreign_key:"userId", refTable:""};
		}
			
		[Transient]
		public function get comments():*
		{
			return related["comments"];
		}
			
		public function set comments(value:*):void
		{
			related["comments"] = {table:"Comment", value:value, type:"many",
									local_key:"id", foreign_key:"userId", refTable:""};
		}
			
		[Transient]
		public function get categories():*
		{
			return related["categories"];
		}
			
		public function set categories(value:*):void
		{
			related["categories"] = {table:"Category", value:value, type:"many",
									local_key:"id", foreign_key:"userId", refTable:""};
		}
		
		public function getRelatedData():Object
		{
			return related;
		}
	}
}