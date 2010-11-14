package org.aerial.vo
{
	import org.aerial.rpc.AbstractVO;
	import org.aerial.vo.*;
	import mx.collections.ArrayCollection;
	
	[Bindable]	
	public class CommentVO extends AbstractVO
	{		
		public function CommentVO()
		{
			super("org.aerial.vo.Comment", function(field:String):*{return this[field]});
		}
		
		private var id:*
		private var userId:*
		private var postId:*
		private var message:*
		private var createDate:*
		private var modDate:*
		private var User:*
		private var Post:*

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

		public function get postId():int
		{
			return _postId;
		}
		
		public function set postId(value:int):void
		{
			_postId = value;
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

		public function get Post():PostVO
		{
			return _Post;
		}
		
		public function set Post(value:PostVO):void
		{
			_Post = value;
		}		
	}
}