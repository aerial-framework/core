package model.vo.base
{
	public class BaseCategory
	{
		public var id:int;
		public var userId:int;
		public var title:String;
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
		public function get posts():*
		{
			return related["posts"];
		}
			
		public function set posts(value:*):void
		{
			related["posts"] = {table:"Post", value:value, type:"many",
									local_key:"id", foreign_key:"categoryId", refTable:""};
		}
		
		public function getRelatedData():Object
		{
			return related;
		}
	}
}