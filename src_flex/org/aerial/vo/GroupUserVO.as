package org.aerial.vo
{
	import org.aerial.rpc.AbstractVO;
	import org.aerial.vo.*;
	import mx.collections.ArrayCollection;
	import flash.net.registerClassAlias;
	
	[Bindable]
	[RemoteClass(alias="org.aerial.vo.GroupUser")]	
	public class GroupUserVO extends AbstractVO
	{
		public function GroupUserVO()
		{
			super("org.aerial.vo.GroupUser",
							function(field:String):*{return this[field]},
							function(field:String, value:*):void{this[field] = value});
		}
		
		private var _id:*
		private var _group_id:*
		private var _user_id:*
		private var _Group:*
		private var _User:*

		public function get id():int
		{
			return _id;
		}
		
		public function set id(value:int):void
		{
			_id = value;
		}

		public function get group_id():int
		{
			return _group_id;
		}
		
		public function set group_id(value:int):void
		{
			_group_id = value;
		}

		public function get user_id():int
		{
			return _user_id;
		}
		
		public function set user_id(value:int):void
		{
			_user_id = value;
		}

		public function get Group():GroupVO
		{
			return _Group;
		}
		
		public function set Group(value:GroupVO):void
		{
			_Group = value;
		}

		public function get User():UserVO
		{
			return _User;
		}
		
		public function set User(value:UserVO):void
		{
			_User = value;
		}		
	}
}