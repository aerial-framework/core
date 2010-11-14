package org.aerial.vo
{
	import org.aerial.rpc.AbstractVO;
	import org.aerial.vo.*;
	import mx.collections.ArrayCollection;
	import flash.net.registerClassAlias;
	
	[Bindable]
	[RemoteClass(alias="org.aerial.vo.Topic")]	
	public class TopicVO extends AbstractVO
	{
		public function TopicVO()
		{
			super("org.aerial.vo.Topic",
							function(field:String):*{return this[field]},
							function(field:String, value:*):void{this[field] = value});
		}
		
		private var _id:*
		private var _userId:*
		private var _categoryId:*
		private var _name:*
		private var _description:*
		private var _createDate:*
		private var _modDate:*
		private var _Category:*
		private var _User:*
		private var _posts:*
		private var _topicTags:*

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

		public function get posts():ArrayCollection
		{
			return _posts;
		}
		
		public function set posts(value:ArrayCollection):void
		{
			_posts = value;
		}

		public function get topicTags():ArrayCollection
		{
			return _topicTags;
		}
		
		public function set topicTags(value:ArrayCollection):void
		{
			_topicTags = value;
		}		
	}
}