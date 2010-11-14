package org.aerial.vo
{
	import org.aerial.rpc.AbstractVO;
	import org.aerial.vo.*;
	import mx.collections.ArrayCollection;
	import flash.net.registerClassAlias;
	
	[Bindable]
	[RemoteClass(alias="org.aerial.vo.Post")]	
	public class PostVO extends AbstractVO
	{
		public function PostVO()
		{
			super("org.aerial.vo.Post",
							function(field:String):*{return this[field]},
							function(field:String, value:*):void{this[field] = value});
		}
		
		private var _id:*
		private var _userId:*
		private var _topicId:*
		private var _title:*
		private var _message:*
		private var _createDate:*
		private var _modDate:*
		private var _User:*
		private var _Topic:*
		private var _comments:*
		private var _postTags:*

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

		public function get comments():ArrayCollection
		{
			return _comments;
		}
		
		public function set comments(value:ArrayCollection):void
		{
			_comments = value;
		}

		public function get postTags():ArrayCollection
		{
			return _postTags;
		}
		
		public function set postTags(value:ArrayCollection):void
		{
			_postTags = value;
		}		
	}
}