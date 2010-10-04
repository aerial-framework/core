package model.vo.base
{
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
		public function get Topic():*
		{
			return related["Topic"];
		}
			
		public function set Topic(value:*):void
		{
			related["Topic"] = {table:"Topic", value:value, type:"one",
									local_key:"topicid", foreign_key:"id", refTable:""};
		}
			
		[Transient]
		public function get comments():*
		{
			return related["comments"];
		}
			
		public function set comments(value:*):void
		{
			related["comments"] = {table:"Comment", value:value, type:"many",
									local_key:"id", foreign_key:"postId", refTable:""};
		}
		
		public function getRelatedData():Object
		{
			return related;
		}
	}
}