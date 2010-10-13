package model.vo
{
	import org.aerial.rpc.AbstractVO;
	import model.vo.*;
	import mx.collections.ArrayCollection;

	[Bindable]	
	public class UserVO extends AbstractVO
	{		
		public function UserVO()
		{
			super("model.vo.User");
		}
		
		private var _id:*
		private var _username:*
		private var _password:*
		private var _createDate:*
		private var _modDate:*

		//Relations
		private var _posts:*
		private var _comments:*
		private var _categories:*
		private var _topics:*

			
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

		//Relations
			
		public function get posts():ArrayCollection
		{
			return _posts;
		}
		
		public function set posts(value:ArrayCollection):void
		{
			_posts = value;
		}
			
		public function get comments():ArrayCollection
		{
			return _comments;
		}
		
		public function set comments(value:ArrayCollection):void
		{
			_comments = value;
		}
			
		public function get categories():ArrayCollection
		{
			return _categories;
		}
		
		public function set categories(value:ArrayCollection):void
		{
			_categories = value;
		}
			
		public function get topics():ArrayCollection
		{
			return _topics;
		}
		
		public function set topics(value:ArrayCollection):void
		{
			_topics = value;
		}
		
	}
}