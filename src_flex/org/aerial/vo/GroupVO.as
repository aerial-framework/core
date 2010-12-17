package org.aerial.vo
{
	import org.aerial.rpc.AbstractVO;
	import org.aerial.vo.*;
	import mx.collections.ArrayCollection;
	import flash.net.registerClassAlias;
	
	[Bindable]
	[RemoteClass(alias="org.aerial.vo.Group")]	
	public class GroupVO extends AbstractVO
	{
		public function GroupVO()
		{
			super("org.aerial.vo.Group",
							function(field:String):*{return this[field]},
							function(field:String, value:*):void{this[field] = value});
		}
		
		private var _id:*
		private var _name:*
		private var _Users:*
		private var _GroupUsers:*

		public function get id():int
		{
			return _id;
		}
		
		public function set id(value:int):void
		{
			_id = value;
		}

		public function get name():String
		{
			return _name;
		}
		
		public function set name(value:String):void
		{
			_name = value;
		}

		public function get Users():ArrayCollection
		{
			return _Users;
		}
		
		public function set Users(value:ArrayCollection):void
		{
			_Users = value;
		}

		public function get GroupUsers():ArrayCollection
		{
			return _GroupUsers;
		}
		
		public function set GroupUsers(value:ArrayCollection):void
		{
			_GroupUsers = value;
		}		
	}
}