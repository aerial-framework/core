package model.vo.base
{
    import model.vo.*;

	[Bindable]
	public class BasePostVO
	{
		private var _id:*;
		private var _userId:*;
		private var _topicId:*;
		private var _title:*;
		private var _message:*;
		private var _createDate:*;
		private var _modDate:*;

		// Relations:
		private var _User:*;
		private var _Topic:*;
		private var _comments:*;
			
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
			
		public function get topicId():int
		{
			return _topicId;
		}
			
		public function set topicId(value:int):void
		{
			_topicId = value;
		}
			
		public function get title():String
		{
			return _title;
		}
			
		public function set title(value:String):void
		{
			_title = value;
		}
			
		public function get message():String
		{
			return _message;
		}
			
		public function set message(value:String):void
		{
			_message = value;
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
			
		public function get User():UserVO
		{
			return _User;
		}
			
		public function set User(value:UserVO):void
		{
			_User = value;
		}
			
		public function get Topic():TopicVO
		{
			return _Topic;
		}
			
		public function set Topic(value:TopicVO):void
		{
			_Topic = value;
		}
			
		public function get comments():Array
		{
			return _comments;
		}
			
		public function set comments(value:Array):void
		{
			_comments = value;
		}
	}
}