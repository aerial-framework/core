package org.aerial.vo
{
	import org.aerial.rpc.AbstractVO;
	import org.aerial.vo.*;
	import mx.collections.ArrayCollection;
	
	[Bindable]	
	public class CategoryVO extends AbstractVO
	{		
		public function CategoryVO()
		{
			super("org.aerial.vo.Category", function(field:String):*{return this[field]});
		}
		
		private var id:*
		private var userId:*
		private var name:*
		private var createDate:*
		private var modDate:*
		private var User:*
		private var topics:*

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

		public function get name():String
		{
			return _name;
		}
		
		public function set name(value:String):void
		{
			_name = value;
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

		public function get User():UserVO
		{
			return _User;
		}
		
		public function set User(value:UserVO):void
		{
			_User = value;
		}

		public function get topics():TopicVO
		{
			return _topics;
		}
		
		public function set topics(value:TopicVO):void
		{
			_topics = value;
		}		
	}
}