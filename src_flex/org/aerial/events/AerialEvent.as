package org.aerial.events
{
	import flash.events.Event;

	public class AerialEvent extends Event
	{
		public static const ENCRYPTED_SESSION_STARTED:String = "encryptedSessionStarted";
		public static const ENCRYPTED_SESSION_FAILED:String = "encryptedSessionFailed";

		public function AerialEvent(type:String, bubbles:Boolean = false, cancelable:Boolean = false)
		{
			super(type, bubbles, cancelable);
		}
	}
}