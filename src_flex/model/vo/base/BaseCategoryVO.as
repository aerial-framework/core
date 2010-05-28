package model.vo.base
{
	[Bindable]
	public class BaseCategoryVO
	{
		public var id:int;
		public var userId:int;
		public var name:String;
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
		public function get topics():*
		{
			return related["topics"];
		}
			
		public function set topics(value:*):void
		{
			related["topics"] = {table:"Topic", value:value, type:"many",
									local_key:"id", foreign_key:"categoryId", refTable:""};
		}
		
		public function getRelatedData():Object
		{
			return related;
		}
	}
}