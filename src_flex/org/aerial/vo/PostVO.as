package org.aerial.vo
{
	import org.aerial.rpc.AbstractVO;
	import org.aerial.vo.*;
	import mx.collections.ArrayCollection;
	
	[Bindable]	
	public class PostVO extends AbstractVO
	{		
		public function PostVO()
		{
			super("org.aerial.vo.Post", function(field:String):*{return this[field]});
		}
		
		private var id:*
		private var userId:*
		private var topicId:*
		private var title:*
		private var message:*
		private var createDate:*
		private var modDate:*
		private var User:*
		private var Topic:*
		private var comments:*
		private var postTags:*

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

		public function get comments():CommentVO
		{
			return _comments;
		}
		
		public function set comments(value:CommentVO):void
		{
			_comments = value;
		}

		public function get postTags():PostTagVO
		{
			return _postTags;
		}
		
		public function set postTags(value:PostTagVO):void
		{
			_postTags = value;
		}		
	}
}