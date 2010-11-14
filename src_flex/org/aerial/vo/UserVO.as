package org.aerial.vo
{
	import org.aerial.rpc.AbstractVO;
	import org.aerial.vo.*;
	import mx.collections.ArrayCollection;
	
	[Bindable]	
	public class UserVO extends AbstractVO
	{		
		public function UserVO()
		{
			super("org.aerial.vo.User", function(field:String):*{return this[field]});
		}
		
		private var id:*
		private var username:*
		private var password:*
		private var createDate:*
		private var modDate:*
		private var posts:*
		private var comments:*
		private var categories:*
		private var topics:*

		public function get id():int
		{
			return _id;
		}
		
		public function set id(value:int):void
		{
			_id = value;
		}

		public function get username():String
		{
			return _username;
		}
		
		public function set username(value:String):void
		{
			_username = value;
		}

		public function get password():String
		{
			return _password;
		}
		
		public function set password(value:String):void
		{
			_password = value;
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

		public function get posts():PostVO
		{
			return _posts;
		}
		
		public function set posts(value:PostVO):void
		{
			_posts = value;
		}

		public function get comments():CommentVO
		{
			return _comments;
		}
		
		public function set comments(value:CommentVO):void
		{
			_comments = value;
		}

		public function get categories():CategoryVO
		{
			return _categories;
		}
		
		public function set categories(value:CategoryVO):void
		{
			_categories = value;
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