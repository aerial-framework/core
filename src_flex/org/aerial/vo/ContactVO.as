package org.aerial.vo
{
	import org.aerial.rpc.AbstractVO;
	import org.aerial.vo.*;
	import mx.collections.ArrayCollection;
	import flash.net.registerClassAlias;
	
	[Bindable]
	[RemoteClass(alias="org.aerial.vo.Contact")]	
	public class ContactVO extends AbstractVO
	{
		public function ContactVO()
		{
			super("org.aerial.vo.Contact",
							function(field:String):*{return this[field]},
							function(field:String, value:*):void{this[field] = value});
		}
		
		private var _id:*
		private var _name:*
		private var _user_id:*
		private var _User:*

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

		public function get user_id():int
		{
			return _user_id;
		}
		
		public function set user_id(value:int):void
		{
			_user_id = value;
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