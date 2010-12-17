package org.aerial.vo
{
	import org.aerial.rpc.AbstractVO;
	import org.aerial.vo.*;
	import mx.collections.ArrayCollection;
	import flash.net.registerClassAlias;
	
	[Bindable]
	[RemoteClass(alias="org.aerial.vo.User")]	
	public class UserVO extends AbstractVO
	{
		public function UserVO()
		{
			super("org.aerial.vo.User",
							function(field:String):*{return this[field]},
							function(field:String, value:*):void{this[field] = value});
		}
		
		private var _id:*
		private var _username:*
		private var _password:*
		private var _Groups:*
		private var _GroupUsers:*
		private var _Contact:*
		private var _Phonenumbers:*

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

		public function get Groups():ArrayCollection
		{
			return _Groups;
		}
		
		public function set Groups(value:ArrayCollection):void
		{
			_Groups = value;
		}

		public function get GroupUsers():ArrayCollection
		{
			return _GroupUsers;
		}
		
		public function set GroupUsers(value:ArrayCollection):void
		{
			_GroupUsers = value;
		}

		public function get Contact():ContactVO
		{
			return _Contact;
		}
		
		public function set Contact(value:ContactVO):void
		{
			_Contact = value;
		}

		public function get Phonenumbers():ArrayCollection
		{
			return _Phonenumbers;
		}
		
		public function set Phonenumbers(value:ArrayCollection):void
		{
			_Phonenumbers = value;
		}		
	}
}