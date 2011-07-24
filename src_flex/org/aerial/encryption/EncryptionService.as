package org.aerial.encryption
{
    import com.hurlant.util.Hex;

    import mx.rpc.AsyncToken;
    import mx.rpc.remoting.Operation;
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
			var encrypted:EncryptedVO = new EncryptedVO();
			encrypted.data = Hex.toArray(Hex.fromString(data));
			encrypted.resetKey = true;

			var operation:Operation = this.getOperation("startSession") as Operation;
			return operation.send(encrypted);
		}
	}
}
