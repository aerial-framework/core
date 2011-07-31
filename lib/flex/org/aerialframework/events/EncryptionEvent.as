package org.aerialframework.events
{
	import flash.events.Event;

	public class EncryptionEvent extends Event
	{
		public static const ENCRYPTED_SESSION_STARTED:String = "encryptedSessionStarted";
		public static const ENCRYPTED_SESSION_FAILED:String = "encryptedSessionFailed";

		public function EncryptionEvent(type:String, bubbles:Boolean = false, cancelable:Boolean = false)
		{
			super(type, bubbles, cancelable);
		}
	}
}