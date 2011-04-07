package org.aerial.errors
{
	public class AerialError extends Error
	{
		public static const ENCRYPTED_SESSION_NOT_STARTED_ERROR:String = "The secure encrypted session has not been started.\n" +
																	"Use Aerial.instance.startSession(key) and " +
																	"listen for an AerialEvent.ENCRYPTED_SESSION_STARTED " +
																	"event.";
		public static const NO_ENCRYPTION_KEY_ERROR:String = "An encryption key has not been set";
		public static const DECRYPTION_ERROR:String = "An error occurred while attempting to decrypt encrypted data";
		public static const INVALID_ENCRYPTION_KEY_ERROR:String = "The provided encryption key is invalid";
		public static const ENCRYPTION_ERROR:String = "An error occurred while attempting to encrypt data";

		public function AerialError(message:* = "",id:* = 0)
		{
			super(message, id);
		}
	}
}