package org.aerial.encryption
{
	import com.hurlant.util.Hex;

	import flash.utils.ByteArray;

	import mx.rpc.AsyncToken;
	import mx.rpc.remoting.RemoteObject;

	import org.aerial.bootstrap.Aerial;

	public class EncryptionService extends RemoteObject
	{
		public function EncryptionService()
		{
			super("Aerial");

			this.source = "core.aerial.EncryptionService";
			this.endpoint = Aerial.SERVER_URL;
		}

		public function startSession(data:String):AsyncToken
		{
			var encrypted:Encrypted = new Encrypted();
			encrypted.data = Hex.toArray(Hex.fromString(data));

			return this.getOperation("startSession").send(encrypted);
		}
	}
}
