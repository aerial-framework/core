package model.vo
{
	import org.aerial.rpc.AbstractVO;
	
	[Bindable]
	public class TopicVO extends AbstractVO
	{		
		public function TopicVO()
		{
			super("model.vo.Topic");
		}
		
		private var _id:*;
		private var _userId:*;
		private var _categoryId:*;
		private var _name:*;
		private var _description:*;
		private var _createDate:*;
		private var _modDate:*;
		
		// Relations:
		private var _Category:*;
		private var _User:*;
		private var _posts:*;
		
		
		public function get id():int
		{
			return _id;
		}
		
		public function set id(value:int):void
		{
			_id = value;
		}
		
		public function get userId():int
		{
			return _userId;
		}
		
		public function set userId(value:int):void
		{
			_userId = value;
		}
		
		public function get categoryId():int
		{
			return _categoryId;
		}
		
		public function set categoryId(value:int):void
		{
			_categoryId = value;
		}
		
		public function get name():String
		{
			return _name;
		}
		
		public function set name(value:String):void
		{
			_name = value;
		}
		
		public function get description():String
		{
			return _description;
		}
		
		public function set description(value:String):void
		{
			_description = value;
		}
		
		public function get createDate():String
		{
			return _createDate;
		}
		
		public function set createDate(value:String):void
		{
			_createDate = value;
		}
		
		public function get modDate():String
		{
			return _modDate;
		}
		
		public function set modDate(value:String):void
		{
			_modDate = value;
		}
		
		// Relations:
		
		public function get Category():CategoryVO
		{
			return _Category;
		}
		
		public function set Category(value:CategoryVO):void
		{
			_Category = value;
		}
		
		public function get User():UserVO
		{
			return _User;
		}
		
		public function set User(value:UserVO):void
		{
			_User = value;
		}
		
		public function get posts():Array
		{
			return _posts;
		}
		
		public function set posts(value:Array):void
		{
			_posts = value;
		}
		
	}
}