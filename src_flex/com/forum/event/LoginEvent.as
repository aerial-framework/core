package com.forum.event{
	import flash.events.Event;
	
	public class LoginEvent extends Event{
		
		public static const LOGIN_ACTION:String = "login";
		public static const LOGOUT_ACTION:String = "logout";
		
		public var user:Object; //We're going to leave this as object so that it can be re-used in other projects.
		public var authenticated:Boolean = false;
		
		public function LoginEvent(type:String, bubbles:Boolean=true, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
		public override function clone():Event
		{
			return new LoginEvent(this.type,this.bubbles,this.cancelable);
		}
		
	}
}